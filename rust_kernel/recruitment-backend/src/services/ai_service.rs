use crate::dto::integration_dto::{CreateQuestion, GenerateVacancyDescriptionPayload};
use crate::error::Result;
use crate::models::question::{
    CodeDetails, MultipleChoiceDetails, Question, QuestionDetails, QuestionType,
    ShortAnswerDetails, TestCase,
};
use crate::services::embed_service::EmbedService;
use crate::services::eval_service::EvalService;
use anyhow::{anyhow, Context as _};
use reqwest::Client;
use serde::{Deserialize, Serialize};
use serde_json::Value as JsonValue;
use std::time::Duration;
use tokio::task::JoinSet;

#[derive(Clone, Debug, Serialize, Deserialize)]
pub struct GenerationOutput {
    pub questions: Vec<Question>,
    pub logs: Vec<String>,
}

#[derive(Clone)]
pub struct AIService {
    client: Client,
    api_key: String,
}

#[derive(Deserialize)]
struct GenerationAndCritiqueResponse {
    question: Question,
    critique: String,
    score: f32,
}

#[derive(Deserialize, Clone)]
struct BlueprintItem {
    topic: String,
    subtopic: String,
    #[serde(rename = "type")]
    q_type: String,
}

impl AIService {
    pub fn new(api_key: String, client: Client) -> Self {
        Self { client, api_key }
    }

    pub async fn generate_test(
        &self,
        embed_service: &EmbedService,
        eval_service: &EvalService,
        profession: &str,
        skills: &[String],
        num_questions: usize,
    ) -> Result<GenerationOutput> {
        let mut logs: Vec<String> = vec![];
        let mut questions: Vec<Question> = vec![];

        let mut plan = self
            .generate_blueprint(profession, skills, num_questions)
            .await?;
        logs.push(format!("Blueprint generated with {} items.", plan.len()));
        if plan.len() < num_questions {
            let deficit = num_questions - plan.len();
            logs.push(format!("Blueprint short by {} items; padding.", deficit));
            let mut i = 0usize;
            for skill in skills.iter().cloned().cycle().take(deficit) {
                let sub = if i % 2 == 0 {
                    format!("Advanced concepts in {}", skill)
                } else {
                    format!("Practical application of {}", skill)
                };
                let q_type = if (plan.len() + i) % 3 == 0 {
                    "short_answer".to_string()
                } else {
                    "multiple_choice".to_string()
                };
                plan.push(BlueprintItem {
                    topic: skill,
                    subtopic: sub,
                    q_type,
                });
                i += 1;
            }
        }

        let blueprint_json = serde_json::json!({
            "plan": plan
                .iter()
                .map(|item| serde_json::json!({
                    "topic": item.topic,
                    "subtopic": item.subtopic,
                    "type": item.q_type,
                }))
                .collect::<Vec<_>>()
        });

        let mut set = JoinSet::new();
        for item in plan.clone() {
            let ai = self.clone();
            let prof = profession.to_string();
            set.spawn(async move {
                ai.try_generate_one_good_question(prof, item.topic, item.subtopic, item.q_type)
                    .await
            });
        }

        while let Some(res) = set.join_next().await {
            match res {
                Ok(Ok((q, log))) => {
                    questions.push(q);
                    logs.push(log);
                }
                Ok(Err(e)) => {
                    logs.push(e.to_string());
                }
                Err(e) => {
                    logs.push(format!("Tokio Join Error: {}", e));
                }
            }
        }

        logs.push(format!(
            "Successfully generated {} questions. Deduplicating...",
            questions.len()
        ));
        self.semantic_dedup(&mut questions, embed_service, 0.92)
            .await;
        logs.push(format!(
            "{} questions remaining after deduplication.",
            questions.len()
        ));

        if questions.len() < num_questions {
            let needed = num_questions - questions.len();
            logs.push(format!(
                "Top-up: need {} more questions. Using curated fallbacks.",
                needed
            ));
            let curated = self.generate_curated_mcqs(&skills.to_vec(), needed);
            questions.extend(curated);
            self.semantic_dedup(&mut questions, embed_service, 0.92)
                .await;
        }

        if questions.len() < num_questions {
            let remain = num_questions - questions.len();
            logs.push(format!("Top-up with dynamic questions: {} needed.", remain));
            let dyns = self.generate_dynamic_topups(&skills.to_vec(), remain);
            questions.extend(dyns);
            self.semantic_dedup(&mut questions, embed_service, 0.92)
                .await;
        }

        let mut assembly_blueprint = blueprint_json.clone();
        if questions.len() < num_questions {
            let generator_models = ["openai/gpt-oss-20b:free", "qwen/qwen-2.5-7b-instruct"];
            match self
                .critique_and_revise(
                    &questions,
                    &blueprint_json,
                    eval_service,
                    profession,
                    &generator_models,
                    embed_service,
                )
                .await
            {
                Ok((revised, critique_json, new_blueprint, mut critique_logs)) => {
                    logs.append(&mut critique_logs);
                    if let Some(c) = critique_json {
                        logs.push(format!(
                            "Critique summary: {}",
                            serde_json::to_string(&c).unwrap_or_default()
                        ));
                    }
                    if !revised.is_empty() {
                        logs.push(format!("Revision added {} questions.", revised.len()));
                        questions.extend(revised);
                        self.semantic_dedup(&mut questions, embed_service, 0.92)
                            .await;
                    }
                    if let Some(bp) = new_blueprint {
                        assembly_blueprint = bp;
                    }
                }
                Err(err) => {
                    logs.push(format!("Critique and revision failed: {}", err));
                }
            }
        }

        questions = self.assemble_test(questions, &assembly_blueprint);

        if questions.len() < num_questions {
            let remain = num_questions - questions.len();
            logs.push(format!("Final top-up adding {} dynamic questions.", remain));
            let dyns = self.generate_dynamic_topups(&skills.to_vec(), remain);
            questions.extend(dyns);
        }

        for (idx, q) in questions.iter_mut().enumerate() {
            q.id = (idx as i32) + 1;
        }
        if questions.len() > num_questions {
            questions.truncate(num_questions);
        }

        Ok(GenerationOutput { questions, logs })
    }

    pub async fn generate_vacancy_description(
        &self,
        payload: &GenerateVacancyDescriptionPayload,
    ) -> Result<String> {
        let language = payload.language.as_deref().unwrap_or("ru-RU");
        let defaults = serde_json::json!({
            "salary": "Договорная",
            "schedule": payload.schedule.as_deref().unwrap_or("6/1 (пн-сб)"),
            "company_name": "ГК «КОИНОТИ НАВ»",
            "company_motto": "ВЕРИМ! МОЖЕМ! СОЗДАЁМ!",
            "company_site": "https://koinotinav.tj/",
            "contact_email": "hr@koinotinav.tj",
            "contact_telegram": "@hr_kn_bot",
            "age": payload.age,
            "education": payload.education,
            "working_experience": payload.working_experience,
            "professional_skills": payload.professional_skills,
            "computer_knowledge": payload.computer_knowledge,
            "personal_qualities": payload.personal_qualities
        });
        let template = r#"1. Честность и вовлеченность.
2. Желание развиваться в большой команде.
3. Позитивное мышление.

🔹️ Мы предлагаем:
1. Стать частью команды крупного и успешного Холдинга!
2. Работу в Головном Офисе Компании в г. Душанбе.
3. Получить уникальный опыт работы в профессиональной команде.
4. Корпоративное обучение в тренинг центре «AtS».
5. График работы: 6/1 (пн-сб).
6. Корпоративное питание.
7. Достойную заработную плату.
8. Привлекательную систему мотивации и другие плюшки.

🤝 Хотите присоединиться к нам, тогда:
1. Вышлите своё Резюме нам на hr@koinotinav.tj или нашему Телеграм боту @hr_kn_bot с обязательным указанием названия вакансии в теме письма.
2. Успешно пройдите все этапы отбора, и ДОБРО ПОЖАЛОВАТЬ к нам в команду!

___________________

ГК «КОИНОТИ НАВ» – ВЕРИМ! МОЖЕМ! СОЗДАЁМ!
Подробнее о Компании: https://koinotinav.tj/"#;

        let request = serde_json::json!({
            "system": format!(
                "You are an HR copywriter crafting engaging vacancy descriptions strictly in {}. Always answer in Russian (Cyrillic script) regardless of user hints. Respond with JSON containing only the `description` string. Maintain emoji section headers, numbered lists, and an enthusiastic employer brand voice.",
                language
            ),
            "user": {
                "vacancy": {
                    "title": payload.title,
                    "company": payload.company,
                    "location": payload.location,
                    "language": language,
                    "age": payload.age,
                    "education": payload.education,
                    "working_experience": payload.working_experience,
                    "professional_skills": payload.professional_skills,
                    "computer_knowledge": payload.computer_knowledge,
                    "personal_qualities": payload.personal_qualities,
                    "schedule": payload.schedule
                },
                "defaults": defaults,
                "template": template,
                "instructions": [
                    "Write 4-6 sentences opening that highlight the role, company, location, and schedule if provided.",
                    "Create a numbered expectations list that clearly references working experience history, professional skills, education, and computer knowledge if provided.",
                    "Summarize personal qualities and age expectations where relevant in the tone of the role.",
                    "Keep the 'Мы предлагаем' benefits section with 6-8 bullets, updating location, schedule, and keeping defaults.",
                    "Add the call-to-action block with provided contacts and closing motto.",
                    "Ensure the tone is warm, inspiring, and aligned with the sample template.",
                    "Use natural Russian language with Cyrillic characters only (aside from proper nouns).",
                    "If any part of the draft appears in English, translate it into Russian before responding.",
                    "Return plain text only inside JSON field `description`, no markdown or additional fields."
                ],
                "schema": {
                    "type": "object",
                    "required": ["description"],
                    "properties": {
                        "description": {"type": "string"}
                    }
                }
            }
        });

        let models = ["openai/gpt-oss-20b:free", "qwen/qwen-2.5-7b-instruct"];
        if let Ok(Ok(resp)) = tokio::time::timeout(
            Duration::from_secs(25),
            self.chat_json_multi(&models, request),
        )
        .await
        {
            if let Some(desc) = resp.get("description").and_then(|v| v.as_str()) {
                let trimmed = desc.trim();
                if !trimmed.is_empty() && Self::contains_cyrillic(trimmed) {
                    return Ok(trimmed.to_string());
                }
            }
        }

        Ok(self.fallback_vacancy_description(payload))
    }

    fn fallback_vacancy_description(&self, payload: &GenerateVacancyDescriptionPayload) -> String {
        let salary = "Договорная";
        let schedule = payload.schedule.as_deref().unwrap_or("6/1 (пн-сб)");
        format!(
            "{} — {}\n\n🔹️ Наши ожидания:\n1. Возраст/этап развития карьеры: {}.\n2. Образование: {}.\n3. Опыт работы: {}.\n4. Профессиональные навыки: {}.\n5. Компьютерная грамотность: {}.\n6. Личные качества: {}.\n7. Готовность работать по графику {}.\n\n🔹️ Мы предлагаем:\n1. Стать частью команды крупного и успешного Холдинга!\n2. Работу в {}.\n3. Корпоративное обучение в тренинг центре «AtS».\n4. График работы: {}.\n5. Достойную заработную плату ({}) и систему мотивации.\n6. Корпоративное питание и поддержку наставников.\n\n🤝 Хотите присоединиться к нам, тогда:\n1. Вышлите своё резюме на hr@koinotinav.tj или нашему Телеграм-боту @hr_kn_bot с указанием названия вакансии.\n2. Успешно пройдите все этапы отбора — ДОБРО ПОЖАЛОВАТЬ в команду!\n\n___________________\n\nГК «КОИНОТИ НАВ» – ВЕРИМ! МОЖЕМ! СОЗДАЁМ!\nПодробнее: https://koinotinav.tj/",
            payload.title.trim(),
            payload.company.trim(),
            payload.age.clone().unwrap_or_else(|| "уточняется при собеседовании".to_string()),
            payload.education.clone().unwrap_or_else(|| "высшее или профильное".to_string()),
            payload.working_experience.clone().unwrap_or_else(|| "профильный опыт от 3 лет".to_string()),
            payload.professional_skills.clone().unwrap_or_else(|| "ключевые компетенции роли".to_string()),
            payload.computer_knowledge.clone().unwrap_or_else(|| "уверенное владение ПК и профильными системами".to_string()),
            payload.personal_qualities.clone().unwrap_or_else(|| "ответственность, командность, ориентация на результат".to_string()),
            schedule,
            payload.location.trim(),
            schedule,
            salary
        )
    }

    fn contains_cyrillic(text: &str) -> bool {
        text.chars().any(|c| matches!(c,
            '\u{0400}'..='\u{04FF}' |
            '\u{0500}'..='\u{052F}' |
            '\u{2DE0}'..='\u{2DFF}' |
            '\u{A640}'..='\u{A69F}'
        ))
    }

    async fn try_generate_one_good_question(
        &self,
        profession: String,
        topic: String,
        subtopic: String,
        q_type: String,
    ) -> Result<(Question, String)> {
        let mut attempt_logs = Vec::new();
        for attempt in 1..=3 {
            let future: std::pin::Pin<
                Box<dyn std::future::Future<Output = Result<GenerationAndCritiqueResponse>> + Send>,
            > = if q_type == "multiple_choice" {
                Box::pin(self.generate_and_critique_mcq(&profession, &topic, &subtopic))
            } else {
                Box::pin(self.generate_and_critique_open(&profession, &topic, &subtopic))
            };

            match tokio::time::timeout(Duration::from_secs(45), future).await {
                Ok(Ok(resp)) => {
                    if resp.score >= 0.5 {
                        let log = format!(
                            "[SUCCESS] Generated '{}/{}' (score: {:.2}). Critique: {}",
                            topic, subtopic, resp.score, resp.critique
                        );
                        return Ok((resp.question, log));
                    } else {
                        attempt_logs.push(format!(
                            "Attempt {}: Low score ({:.2}). Critique: {}",
                            attempt, resp.score, resp.critique
                        ));
                    }
                }
                Ok(Err(e)) => {
                    attempt_logs.push(format!(
                        "Attempt {}: Generation/Critique failed. Error: {}",
                        attempt, e
                    ));
                }
                Err(_) => {
                    attempt_logs.push(format!("Attempt {}: Timed out after 45 seconds.", attempt));
                }
            }
        }
        Err(anyhow!(
            "[FAILURE] Failed to generate a valid question for '{}/{}'. Reasons: [{}]",
            topic,
            subtopic,
            attempt_logs.join("; ")
        )
        .into())
    }

    async fn critique_and_revise(
        &self,
        current_questions: &[Question],
        blueprint: &JsonValue,
        eval: &EvalService,
        profession: &str,
        generator_models: &[&str],
        embed_service: &EmbedService,
    ) -> Result<(
        Vec<Question>,
        Option<JsonValue>,
        Option<JsonValue>,
        Vec<String>,
    )> {
        let mut logs: Vec<String> = vec![];
        let plan = blueprint
            .get("plan")
            .and_then(|p| p.as_array())
            .map(|p| p.clone())
            .unwrap_or_default();
        let want_total = plan.len();

        if current_questions.len() >= want_total {
            logs.push("Sufficient questions generated, skipping revision.".to_string());
            return Ok((vec![], None, None, logs));
        }
        logs.push(format!(
            "Need to generate {} more questions.",
            want_total - current_questions.len()
        ));

        let critique_payload = serde_json::json!({
            "system": "You are a quality inspector for an AI-generated technical assessment. Your task is to critique the provided set of questions against the original plan and create a *new, minimal blueprint* to generate only the missing, highest-priority questions. Be strict and specific.",
            "user": {
                "original_blueprint": blueprint,
                "current_questions": current_questions,
                "critique_instructions": [
                   "Identify which subtopics from the original plan are missing or poorly covered.",
                   "Diagnose any repetitiveness in question structure or type.",
                   "Create a new_blueprint containing ONLY the items needed to fix the gaps. Do not include items that have already been fulfilled.",
                ],
                "schema": {
                   "type":"object", "required":["critique", "new_blueprint"],
                   "properties": {
                       "critique": {"type":"object", "properties": {"diagnosis":{"type":"string"},"is_sufficient":{"type":"boolean"}}},
                       "new_blueprint": {"type":"object", "properties": {"plan": {"type": "array"}}}
                   }
                }
            }
        });

        let Ok(Ok(critique_json)) = tokio::time::timeout(
            Duration::from_secs(30),
            self.chat_json_multi(&["openai/gpt-oss-20b:free"], critique_payload),
        )
        .await
        else {
            logs.push("Critique generation failed or timed out.".to_string());
            return Ok((vec![], None, None, logs));
        };

        let new_blueprint = critique_json.get("new_blueprint").cloned();
        let critique = critique_json.get("critique").cloned();
        logs.push(format!(
            "Critique received: {}",
            serde_json::to_string(&critique).unwrap_or_default()
        ));

        if let Some(bp) = &new_blueprint {
            if bp
                .get("plan")
                .and_then(|p| p.as_array())
                .map_or(true, |p| p.is_empty())
            {
                logs.push("New blueprint is empty, no revision needed.".to_string());
                return Ok((vec![], critique, new_blueprint, logs));
            }
            logs.push(format!(
                "Executing new blueprint: {}",
                serde_json::to_string(bp).unwrap_or_default()
            ));
            let (mut new_questions, new_gen_logs) = self
                .generation_loop(bp, generator_models, eval, profession)
                .await;
            logs.extend(new_gen_logs);
            logs.push(format!(
                "Generated {} new questions from revision.",
                new_questions.len()
            ));
            self.semantic_dedup(&mut new_questions, embed_service, 0.92)
                .await;
            logs.push(format!(
                "{} questions remain after revision deduplication.",
                new_questions.len()
            ));
            return Ok((new_questions, critique, new_blueprint, logs));
        }

        Ok((vec![], critique, None, logs))
    }

    async fn generate_blueprint(
        &self,
        profession: &str,
        skills: &[String],
        num_questions: usize,
    ) -> Result<Vec<BlueprintItem>> {
        let models = &["openai/gpt-oss-20b:free", "google/gemma-3n-e2b-it:free"];
        let mc = ((num_questions as f32) * 0.6).round() as i32;
        let open = (num_questions as i32) - mc;
        let payload = serde_json::json!({
            "system": "You are a technical assessment designer. Create a detailed JSON blueprint for a test. Focus on a diverse set of specific, practical subtopics within the main skills.",
            "user": {
                "profession": profession, "skills": skills, "num_mcq": mc, "num_open": open,
                "instructions": [
                    "Generate a list of specific, fine-grained subtopics to test.",
                    "Ensure subtopics cover a range of concepts: core knowledge, practical application, debugging, design patterns, etc.",
                    "Do not create generic topics like just 'Rust' or 'AWS'. Be specific, e.g., 'Rust error handling with Result' or 'AWS S3 bucket policies'.",
                    "Assign either 'multiple_choice' or 'short_answer' to each subtopic.",
                ],
                "schema": {
                    "type": "object", "required": ["plan"],
                    "properties": { "plan": { "type": "array", "items": {
                        "type": "object", "required": ["topic", "subtopic", "type"],
                        "properties": {
                            "topic": {"type": "string"},
                            "subtopic": {"type": "string"},
                            "type": {"type": "string", "enum": ["multiple_choice", "short_answer"]}
                        }
                    }}}
                }
            }
        });

        #[derive(Deserialize)]
        struct BlueprintResponse {
            plan: Vec<BlueprintItem>,
        }

        let bp_result = tokio::time::timeout(
            Duration::from_secs(45),
            self.chat_json_multi(models, payload),
        )
        .await;

        match bp_result {
            Ok(Ok(bp_val)) => {
                match serde_json::from_value::<BlueprintResponse>(bp_val.clone()) {
                    Ok(bp) => {
                        tracing::info!("Successfully generated and parsed blueprint.");
                        return Ok(bp.plan);
                    }
                    Err(e) => {
                        tracing::warn!("Blueprint deserialization failed: {}. AI response was: {}. Falling back.", e, bp_val);
                    }
                }
            }
            Ok(Err(e)) => {
                tracing::warn!("Blueprint generation API call failed: {}. Falling back.", e);
            }
            Err(_) => {
                tracing::warn!("Blueprint generation timed out after 45 seconds. Falling back.");
            }
        }

        tracing::warn!("Using fallback blueprint generation.");
        Ok(skills
            .iter()
            .cycle()
            .take(num_questions)
            .enumerate()
            .map(|(i, skill)| BlueprintItem {
                topic: skill.clone(),
                subtopic: if i % 2 == 0 {
                    format!("Advanced concepts in {}", skill)
                } else {
                    format!("Practical application of {}", skill)
                },
                q_type: if i % 3 == 0 {
                    "short_answer".to_string()
                } else {
                    "multiple_choice".to_string()
                },
            })
            .collect())
    }

    async fn generate_and_critique_mcq(
        &self,
        profession: &str,
        topic: &str,
        subtopic: &str,
    ) -> Result<GenerationAndCritiqueResponse> {
        let models = &["openai/gpt-oss-20b:free", "qwen/qwen-2.5-7b-instruct"];
        let payload = serde_json::json!({
            "system": "You are a Senior Engineer creating a single, high-quality multiple-choice question AND a critique of your own question. Your output MUST be a single, valid JSON object.",
            "user": {
                "profession": profession, "topic": topic, "subtopic": subtopic,
                "instructions": {
                    "task": "First, create one challenging multiple-choice question with 4 plausible, distinct options. Second, provide a brief, honest critique of your question's quality, relevance, and clarity. Third, provide a quality score from 0.0 (terrible) to 1.0 (perfect).",
                    "quality_criteria": [
                        "The question must require genuine understanding, not just rote memorization.",
                        "All 4 options must be plausible, distinct, and of similar length and format.",
                        "Provide a brief but insightful explanation for the correct answer."
                    ],
                    "negative_constraints": [
                        "DO NOT use trivial phrases like 'Which of the following...' or 'What is...'.",
                        "DO NOT create questions with generic options like 'All of the above' or 'None of the above'."
                    ],
                     "example_good_question": {
                        "question": {
                            "type": "multiple_choice",
                            "question": "A Rust microservice using asynchronous processing receives a sudden spike in requests, causing it to slow down. Which of the following is the MOST likely bottleneck to investigate first?",
                            "options": [
                                "The number of available threads in the async runtime's thread pool.",
                                "The capacity of the underlying database connection pool.",
                                "The CPU's clock speed and core count.",
                                "The speed of the network interface card (NIC)."
                            ],
                            "correct_answer": 0,
                            "explanation": "In an async environment, a limited number of worker threads can become saturated with I/O-bound tasks, preventing new tasks from being processed. While other factors can be bottlenecks, the runtime's thread pool is the most immediate concern for an async service under load."
                        },
                        "critique": "This is a strong question as it presents a realistic scenario and requires the candidate to reason about performance in an async context, rather than just recall a fact. The options are all plausible bottlenecks, forcing a deeper level of analysis.",
                        "score": 0.9
                    }
                },
                "schema": {
                    "type": "object", "required": ["question", "critique", "score"],
                    "properties": {
                        "question": {
                            "type": "object", "required": ["type", "question", "options", "correct_answer", "explanation"],
                            "properties": {
                                "type": {"type":"string", "const": "multiple_choice"},
                                "question": {"type": "string"},
                                "options": {"type": "array", "minItems": 4, "maxItems": 4, "items": {"type": "string"}},
                                "correct_answer": {"type": "integer", "minimum": 0, "maximum": 3},
                                "explanation": {"type": "string"}
                            }
                        },
                        "critique": { "type": "string" },
                        "score": { "type": "number", "minimum": 0.0, "maximum": 1.0 }
                    }
                }
            }
        });
        let resp_val = self
            .chat_json_multi(models, payload)
            .await
            .context("generate_and_critique_mcq call failed")?;
        Ok(serde_json::from_value(resp_val)?)
    }

    async fn generate_and_critique_open(
        &self,
        profession: &str,
        topic: &str,
        subtopic: &str,
    ) -> Result<GenerationAndCritiqueResponse> {
        let models = &["openai/gpt-oss-20b:free", "qwen/qwen-2.5-7b-instruct"];
        let payload = serde_json::json!({
            "system": "You are an Architect creating a single, high-quality, open-ended question AND a critique of your own question. Your output MUST be a single, valid JSON object.",
            "user": {
                 "profession": profession, "topic": topic, "subtopic": subtopic,
                "instructions": {
                    "task": "First, create one high-quality, open-ended short-answer question based on a realistic scenario. Second, provide a brief, honest critique of your question's quality and relevance. Third, provide a quality score from 0.0 (terrible) to 1.0 (perfect).",
                    "quality_criteria": [
                        "The question should require critical thinking and a detailed explanation, not a simple one-word answer.",
                        "Provide a list of expected keywords or concepts that a good answer should contain.",
                        "Set a minimum word count of at least 50."
                    ],
                    "negative_constraints": [
                        "DO NOT use the template 'Explain a real-world scenario where X is critical...'. Be more creative and specific.",
                        "DO NOT ask for simple definitions."
                    ],
                    "example_good_question": {
                        "question": {
                            "type": "short_answer",
                            "question": "You are designing a distributed system where message ordering is critical for financial transactions. Describe how you would configure a Kafka topic and its producers/consumers to guarantee strict 'first-in, first-out' (FIFO) ordering for all messages related to a single customer account. What are the performance trade-offs of this approach?",
                            "min_words": 60,
                            "expected_keywords": ["partition key", "single partition", "consumer group", "producer idempotence", "throughput vs. ordering"]
                        },
                        "critique": "This is a solid system design question that tests a fundamental concept in Kafka. It's specific, scenario-based, and requires understanding trade-offs, which is key for a senior role.",
                        "score": 0.9
                    }
                },
                "schema": {
                    "type": "object", "required": ["question", "critique", "score"],
                    "properties": {
                        "question": {
                             "type": "object", "required": ["type", "question", "min_words", "expected_keywords"],
                            "properties": {
                                "type": {"type":"string", "const": "short_answer"},
                                "question": {"type": "string"},
                                "min_words": {"type": "integer", "minimum": 50},
                                "expected_keywords": {"type": "array", "minItems": 3, "items": {"type": "string"}}
                            }
                        },
                        "critique": { "type": "string" },
                        "score": { "type": "number", "minimum": 0.0, "maximum": 1.0 }
                    }
                }
            }
        });
        let resp_val = self
            .chat_json_multi(models, payload)
            .await
            .context("generate_and_critique_open call failed")?;
        Ok(serde_json::from_value(resp_val)?)
    }

    async fn generation_loop(
        &self,
        blueprint: &JsonValue,
        models: &[&str],
        eval: &EvalService,
        profession: &str,
    ) -> (Vec<Question>, Vec<String>) {
        let mut logs: Vec<String> = vec![];
        let plan = match blueprint.get("plan").and_then(|p| p.as_array()) {
            Some(p) => p.clone(),
            None => {
                logs.push("Blueprint has no plan.".to_string());
                return (vec![], logs);
            }
        };

        let total_wanted = plan.len();
        if total_wanted == 0 {
            logs.push("Blueprint has no items to generate.".to_string());
            return (vec![], logs);
        }

        let mut set: JoinSet<(Option<JsonValue>, String, String, String)> = JoinSet::new();
        let mut generated_items: Vec<Question> = Vec::new();

        for item in plan.iter().cycle().take(total_wanted * 2) {
            // Oversample to 2x
            let topic = item
                .get("topic")
                .and_then(|v| v.as_str())
                .unwrap_or("")
                .to_string();
            let subtopic = item
                .get("subtopic")
                .and_then(|v| v.as_str())
                .unwrap_or("")
                .to_string();
            let q_type = item
                .get("type")
                .and_then(|v| v.as_str())
                .unwrap_or("multiple_choice")
                .to_string();

            let ai = self.clone();
            let prof = profession.to_string();
            let eval_clone = eval.clone();
            let models_clone = models.iter().map(|s| s.to_string()).collect::<Vec<_>>();
            set.spawn(async move {
                let models_ref: Vec<&str> = models_clone.iter().map(|s| s.as_str()).collect();
                let gen_future: std::pin::Pin<
                    Box<dyn std::future::Future<Output = Option<JsonValue>> + Send>,
                > = if q_type == "multiple_choice" {
                    Box::pin(ai.generate_one_mcq(&models_ref, &prof, &topic, &subtopic))
                } else {
                    Box::pin(ai.generate_one_open(&models_ref, &prof, &topic, &subtopic))
                };

                if let Some(generated_q) = tokio::time::timeout(Duration::from_secs(25), gen_future)
                    .await
                    .ok()
                    .flatten()
                {
                    if let Ok((score, critique)) = eval_clone.critique_question(&generated_q).await
                    {
                        if score >= 0.5 {
                            // Lowered threshold to 0.5
                            return (
                                Some(generated_q),
                                topic,
                                subtopic,
                                format!("ACCEPT: score={:.2} | {}", score, critique),
                            );
                        } else {
                            return (
                                None,
                                topic,
                                subtopic,
                                format!("REJECT: score={:.2} | {}", score, critique),
                            );
                        }
                    }
                }
                (
                    None,
                    topic,
                    subtopic,
                    "FAIL: Generation or critique timed out".to_string(),
                )
            });
        }

        while let Some(res) = set.join_next().await {
            if let Ok((item_json_opt, topic, subtopic, log_msg)) = res {
                logs.push(format!("[{}/{}] {}", topic, subtopic, log_msg));
                if let Some(item_json) = item_json_opt {
                    if let Ok(q) = serde_json::from_value(item_json) {
                        generated_items.push(q);
                    }
                }
            }
        }
        (generated_items, logs)
    }

    fn assemble_test(&self, questions: Vec<Question>, blueprint: &JsonValue) -> Vec<Question> {
        let plan = blueprint
            .get("plan")
            .and_then(|p| p.as_array())
            .map(|p| p.clone())
            .unwrap_or_default();
        let want_mcq = plan
            .iter()
            .filter(|i| i["type"] == "multiple_choice")
            .count();
        let want_open = plan.iter().filter(|i| i["type"] == "short_answer").count();
        let num_questions = want_mcq + want_open;

        let (mut mcqs, mut opens): (Vec<_>, Vec<_>) = questions
            .into_iter()
            .partition(|q| matches!(q.question_type, QuestionType::MultipleChoice));

        mcqs.truncate(want_mcq);
        opens.truncate(want_open);

        let mut assembled = mcqs;
        assembled.append(&mut opens);

        assembled.retain(|q| !self.looks_trivial(&q.question));

        let mut final_questions = Vec::new();
        for mut q in assembled.into_iter() {
            let mut is_valid = true;
            if let QuestionDetails::ShortAnswer(sa) = &mut q.details {
                q.points = 0;
                if sa.min_words.unwrap_or(0) < 50 {
                    sa.min_words = Some(50);
                }
            } else if let QuestionDetails::MultipleChoice(mc) = &mut q.details {
                let mut seen = std::collections::HashSet::new();
                mc.options.retain(|opt| seen.insert(opt.clone()));
                if mc.options.len() < 4 {
                    is_valid = false; // Silently drop invalid MCQs
                }
            }
            if is_valid {
                final_questions.push(q);
            }
        }

        for (idx, q) in final_questions.iter_mut().enumerate() {
            q.id = (idx as i32) + 1;
        }

        final_questions.truncate(num_questions);
        final_questions
    }

    async fn generate_one_mcq(
        &self,
        models: &[&str],
        profession: &str,
        topic: &str,
        subtopic: &str,
    ) -> Option<JsonValue> {
        let payload = serde_json::json!({
            "system": "You are a Senior Engineer creating a single, high-quality multiple-choice question for a technical assessment. Your output MUST be a single, valid JSON object and nothing else. Do not include prose.",
            "user": {
                "profession": profession, "topic": topic, "subtopic": subtopic,
                "instructions": {
                    "task": "Create one challenging multiple-choice question.",
                    "quality_criteria": [
                        "The question must require genuine understanding, not just rote memorization.",
                        "All 4 options must be plausible, distinct, and of similar length and format.",
                        "Provide a brief but insightful explanation for the correct answer."
                    ],
                    "negative_constraints": [
                        "DO NOT use trivial phrases like 'Which of the following...' or 'What is...'.",
                        "DO NOT use templates. Be creative.",
                        "DO NOT create questions with generic options like 'All of the above' or 'None of the above'."
                    ]
                },
                "schema": {
                    "type": "object", "required": ["type", "question", "options", "correct_answer", "explanation"],
                    "properties": {
                        "type": {"type":"string", "const": "multiple_choice"},
                        "question": {"type": "string"},
                        "options": {"type": "array", "minItems": 4, "maxItems": 4, "items": {"type": "string"}},
                        "correct_answer": {"type": "integer", "minimum": 0, "maximum": 3},
                        "explanation": {"type": "string"}
                    }
                }
            }
        });
        self.chat_json_multi(models, payload).await.ok()
    }

    async fn generate_one_open(
        &self,
        models: &[&str],
        profession: &str,
        topic: &str,
        subtopic: &str,
    ) -> Option<JsonValue> {
        let payload = serde_json::json!({
            "system": "You are an Architect creating a single, high-quality, open-ended question for a technical assessment. Your output MUST be a single, valid JSON object and nothing else. Do not include prose.",
            "user": {
                "profession": profession, "topic": topic, "subtopic": subtopic,
                "instructions": {
                    "task": "Create one high-quality, open-ended short-answer question.",
                    "quality_criteria": [
                        "The question should require critical thinking and a detailed explanation, not a simple one-word answer.",
                        "It should be based on a realistic scenario that a candidate would encounter.",
                        "Provide a list of expected keywords or concepts that a good answer should contain.",
                        "Set a minimum word count of at least 50."
                    ],
                    "negative_constraints": [
                        "DO NOT use the template 'Explain a real-world scenario where X is critical...'. Be more creative and specific.",
                        "DO NOT ask for simple definitions."
                    ]
                },
                "schema": {
                    "type": "object", "required": ["type", "question", "min_words", "expected_keywords"],
                    "properties": {
                        "type": {"type":"string", "const": "short_answer"},
                        "question": {"type": "string"},
                        "min_words": {"type": "integer", "minimum": 50},
                        "expected_keywords": {"type": "array", "minItems": 3, "items": {"type": "string"}}
                    }
                }
            }
        });
        self.chat_json_multi(models, payload).await.ok()
    }

    async fn semantic_dedup(
        &self,
        questions: &mut Vec<Question>,
        embed_service: &EmbedService,
        threshold: f32,
    ) {
        if questions.len() < 2 {
            return;
        }
        let stems: Vec<String> = questions
            .iter()
            .map(|q| q.question.trim().to_string())
            .collect();
        if let Ok(embs) = embed_service.embed_texts(&stems).await {
            if embs.len() != questions.len() {
                return;
            }
            let mut keep: Vec<bool> = vec![true; questions.len()];
            for i in 0..embs.len() {
                if !keep[i] {
                    continue;
                }
                for j in (i + 1)..embs.len() {
                    if !keep[j] {
                        continue;
                    }
                    if EmbedService::cosine_sim(&embs[i], &embs[j]) >= threshold {
                        keep[j] = false;
                    }
                }
            }
            let mut filtered: Vec<Question> = Vec::new();
            for (q, k) in questions.drain(..).zip(keep.into_iter()) {
                if k {
                    filtered.push(q);
                }
            }
            *questions = filtered;
        }
    }

    async fn chat_json_multi(&self, models: &[&str], payload: JsonValue) -> Result<JsonValue> {
        let mut set: JoinSet<anyhow::Result<JsonValue>> = JoinSet::new();
        for m in models {
            let ai = self.clone();
            let p = payload.clone();
            let model = (*m).to_string();
            set.spawn(async move {
                ai.chat_json(&model, p)
                    .await
                    .map_err(|e| anyhow::anyhow!(e))
            });
        }
        let mut last_err: Option<anyhow::Error> = None;
        while let Some(res) = set.join_next().await {
            match res {
                Ok(Ok(v)) => return Ok(v),
                Ok(Err(e)) => {
                    last_err = Some(e);
                }
                Err(e) => {
                    last_err = Some(anyhow::anyhow!(e));
                }
            }
        }
        Err(anyhow::anyhow!("All models failed: {:?}", last_err).into())
    }

    async fn chat_json(&self, model: &str, payload: JsonValue) -> Result<JsonValue> {
        #[derive(Serialize)]
        struct Msg<'a> {
            role: &'a str,
            content: String,
        }
        #[derive(Serialize)]
        struct ResponseFormat<'a> {
            #[serde(rename = "type")]
            r#type: &'a str,
        }
        #[derive(Serialize)]
        struct Req<'a> {
            model: &'a str,
            temperature: f32,
            response_format: ResponseFormat<'a>,
            messages: Vec<Msg<'a>>,
        }
        #[derive(Deserialize)]
        struct RespChoiceMsg {
            content: String,
        }
        #[derive(Deserialize)]
        struct RespChoice {
            message: RespChoiceMsg,
        }
        #[derive(Deserialize)]
        struct Resp {
            choices: Vec<RespChoice>,
        }

        let system_content = payload
            .get("system")
            .map(|v| {
                if v.is_string() {
                    v.as_str().unwrap().to_string()
                } else {
                    serde_json::to_string(v).unwrap()
                }
            })
            .unwrap_or_else(|| "".to_string());
        let user_json = payload
            .get("user")
            .cloned()
            .unwrap_or(serde_json::json!({}));
        let user_content = format!(
            "You will receive instructions as JSON below. Use them to produce STRICT JSON output only. Do not include prose.\n\nJSON:\n{}",
            serde_json::to_string(&user_json)?
        );

        let req = Req {
            model,
            temperature: 0.3,
            response_format: ResponseFormat {
                r#type: "json_object",
            },
            messages: vec![
                Msg {
                    role: "system",
                    content: system_content,
                },
                Msg {
                    role: "user",
                    content: user_content,
                },
            ],
        };

        let res = self
            .client
            .post("https://openrouter.ai/api/v1/chat/completions")
            .bearer_auth(&self.api_key)
            .header("Accept", "application/json")
            .header("Content-Type", "application/json")
            .header("HTTP-Referer", "http://localhost")
            .header("X-Title", "Recruitment Backend")
            .json(&req)
            .send()
            .await?;

        if !res.status().is_success() {
            return Err(anyhow::anyhow!("AI request failed: status {}", res.status()).into());
        }

        let body: Resp = res.json().await?;
        if let Some(first) = body.choices.into_iter().next() {
            let s = first.message.content;
            if let Ok(val) = serde_json::from_str::<JsonValue>(&s) {
                return Ok(val);
            }
        }

        Err(anyhow::anyhow!("AI request failed: no valid JSON choice").into())
    }

    pub fn sanitize_questions(
        &self,
        raw: &JsonValue,
        num_questions: usize,
        fallback_skills: &[String],
    ) -> Vec<Question> {
        let arr_val = match raw {
            JsonValue::Array(a) => a.clone(),
            JsonValue::Object(map) => {
                if let Some(qs) = map.get("questions").and_then(|v| v.as_array()) {
                    qs.clone()
                } else {
                    vec![JsonValue::Object(map.clone())]
                }
            }
            _ => Vec::new(),
        };

        let mut items: Vec<Question> = serde_json::from_value(JsonValue::Array(arr_val.clone()))
            .unwrap_or_else(|_| {
                arr_val
                    .into_iter()
                    .filter_map(|v| self.coerce_minimal_question(v).ok())
                    .collect()
            });

        for (idx, q) in items.iter_mut().enumerate() {
            q.id = (idx as i32) + 1;
            match &q.details {
                QuestionDetails::ShortAnswer(_) => {
                    q.points = 0;
                }
                _ => {
                    if q.points < 1 {
                        q.points = 1;
                    }
                    if q.points > 5 {
                        q.points = 5;
                    }
                }
            }
            if q.question.len() > 300 {
                q.question.truncate(300);
            }
            match &mut q.details {
                QuestionDetails::MultipleChoice(mc) => {
                    if mc.options.len() < 3 {
                        while mc.options.len() < 3 {
                            mc.options.push("Option".to_string());
                        }
                    }
                    if mc.options.len() > 6 {
                        mc.options.truncate(6);
                    }
                    if mc.correct_answer < 0 || (mc.correct_answer as usize) >= mc.options.len() {
                        mc.correct_answer = 0;
                    }
                    let mut seen = std::collections::HashSet::new();
                    mc.options.retain(|o| seen.insert(o.trim().to_lowercase()));
                    while mc.options.len() < 4 {
                        mc.options.push("Other".to_string());
                    }
                }
                QuestionDetails::Code(cd) => {
                    if cd.language.to_lowercase() != "rust" {
                        cd.language = "rust".to_string();
                    }
                    if cd.test_cases.len() < 2 {
                        let missing = 2 - cd.test_cases.len();
                        for _ in 0..missing {
                            cd.test_cases.push(TestCase {
                                input: String::new(),
                                expected: String::new(),
                            });
                        }
                    }
                }
                QuestionDetails::ShortAnswer(sa) => {
                    if sa.min_words.unwrap_or(0) < 40 {
                        sa.min_words = Some(40);
                    }
                    if sa.ai_grading == false {
                        sa.ai_grading = true;
                    }
                }
            }
        }

        items.retain(|q| !self.looks_trivial(&q.question));

        if items.len() < num_questions {
            let topups = self.generate_dynamic_topups(fallback_skills, num_questions - items.len());
            for q in topups {
                items.push(q);
            }
        }
        {
            let mut seen = std::collections::HashSet::new();
            items.retain(|q| seen.insert(q.question.trim().to_lowercase()));
        }
        if items.len() > num_questions {
            items.truncate(num_questions);
        }
        for (i, q) in items.iter_mut().enumerate() {
            q.id = (i as i32) + 1;
        }
        items
    }

    fn coerce_minimal_question(&self, v: JsonValue) -> Result<Question> {
        let obj = v.as_object().cloned().unwrap_or_default();
        let t = obj
            .get("type")
            .and_then(|x| x.as_str())
            .unwrap_or("multiple_choice");
        let qtext = obj
            .get("question")
            .and_then(|x| x.as_str())
            .unwrap_or("Write a question about Rust.")
            .to_string();
        let points = obj.get("points").and_then(|x| x.as_i64()).unwrap_or(1) as i32;
        let details = match t {
            "multiple_choice" => {
                let opts: Vec<String> = obj
                    .get("details")
                    .and_then(|d| d.get("options"))
                    .and_then(|o| o.as_array())
                    .map(|a| {
                        a.iter()
                            .filter_map(|e| e.as_str().map(|s| s.to_string()))
                            .collect()
                    })
                    .unwrap_or_else(|| vec!["A".into(), "B".into(), "C".into()]);
                let idx = obj
                    .get("details")
                    .and_then(|d| d.get("correct_answer"))
                    .and_then(|c| c.as_i64())
                    .unwrap_or(0) as i32;
                QuestionDetails::MultipleChoice(MultipleChoiceDetails {
                    options: opts,
                    correct_answer: idx,
                    explanation: None,
                })
            }
            "code" => QuestionDetails::Code(CodeDetails {
                language: "rust".into(),
                starter_code: None,
                test_cases: vec![
                    TestCase {
                        input: String::new(),
                        expected: String::new(),
                    },
                    TestCase {
                        input: String::new(),
                        expected: String::new(),
                    },
                ],
            }),
            _ => QuestionDetails::ShortAnswer(ShortAnswerDetails {
                expected_keywords: None,
                min_words: Some(20),
                ai_grading: true,
            }),
        };
        Ok(Question {
            id: 0,
            question_type: match t {
                "multiple_choice" => QuestionType::MultipleChoice,
                "code" => QuestionType::Code,
                _ => QuestionType::ShortAnswer,
            },
            question: qtext,
            points,
            details,
        })
    }

    pub fn to_create_questions(&self, questions: &[Question]) -> Vec<CreateQuestion> {
        questions
            .iter()
            .map(|q| CreateQuestion {
                question_type: q.question_type.clone(),
                question: q.question.clone(),
                points: q.points,
                details: q.details.clone(),
            })
            .collect()
    }

    fn generate_curated_mcqs(&self, skills: &[String], needed: usize) -> Vec<Question> {
        let skills = if skills.is_empty() {
            vec!["Rust".to_string()]
        } else {
            skills.to_vec()
        };
        let mut bank: Vec<Question> = Vec::new();

        for s in &skills {
            match s.to_lowercase().as_str() {
                "seo" | "ppc" | "content marketing" | "analytics" | "email marketing" => {
                    bank.extend(self.curated_marketing_mcqs(&skills))
                }
                "recruiting"
                | "labor law"
                | "performance management"
                | "compensation"
                | "employee relations" => bank.extend(self.curated_hr_mcqs(&skills)),
                _ => bank.extend(self.curated_generic_mcqs(s)),
            }
        }
        let mut seen = std::collections::HashSet::new();
        bank.retain(|q| seen.insert(q.question.clone()));

        if bank.len() < needed {
            let mut generic_more = self.curated_generic_mcqs("Software Engineering");
            bank.append(&mut generic_more);
        }

        bank.into_iter()
            .take(needed)
            .enumerate()
            .map(|(i, mut q)| {
                q.id = (i as i32) + 1;
                q
            })
            .collect()
    }

    fn curated_marketing_mcqs(&self, skills: &[String]) -> Vec<Question> {
        let mut qs: Vec<Question> = Vec::new();
        let has = |k: &str| skills.iter().any(|s| s.eq_ignore_ascii_case(k));

        if has("SEO") {
            qs.extend(vec![
            self.mcq(
                "Which on-page factor most directly improves click-through rate from SERPs?",
                vec!["Compelling title and meta description".into(), "robots.txt configuration".into(), "Increasing server RAM".into(), "Canonicalizing all pages".into()],
                0,
                2,
                Some("Meta title/description influence CTR; other options are not direct CTR levers."),
            ),
            self.mcq(
                "Which KPI best indicates SEO content effectiveness over time?",
                vec!["Organic sessions growth".into(), "Bounce rate alone".into(), "CPU usage".into(), "Email open rate".into()],
                0,
                2,
                Some("Sustained organic traffic growth is a primary signal."),
            ),
        ]);
        }

        if has("PPC") {
            qs.extend(vec![
                self.mcq(
                    "What primarily improves Google Ads Quality Score?",
                    vec![
                        "Ad relevance and expected CTR".into(),
                        "Daily budget amount".into(),
                        "Account age".into(),
                        "Number of ad extensions".into(),
                    ],
                    0,
                    2,
                    Some("QS is driven by relevance/CTR and landing page experience."),
                ),
                self.mcq(
                    "With limited conversion data, which bidding strategy is safest to start with?",
                    vec![
                        "Maximize Clicks".into(),
                        "Target CPA".into(),
                        "Target ROAS".into(),
                        "Enhanced CPC with tROAS".into(),
                    ],
                    0,
                    2,
                    Some(
                        "Automated conversion strategies need data; Maximize Clicks can bootstrap.",
                    ),
                ),
            ]);
        }

        if has("Content Marketing") {
            qs.extend(vec![self.mcq(
                "Which KPI best measures content lead-generation effectiveness?",
                vec![
                    "MQLs from content offers".into(),
                    "Page likes".into(),
                    "Average time to hire".into(),
                    "Server uptime".into(),
                ],
                0,
                2,
                Some("Marketing Qualified Leads tie content to pipeline."),
            )]);
        }

        if has("Analytics") {
            qs.extend(vec![self.mcq(
                "Which attribution model gives full credit to the first touchpoint?",
                vec![
                    "First-click".into(),
                    "Last-click".into(),
                    "Linear".into(),
                    "Time decay".into(),
                ],
                0,
                2,
                Some("First-click assigns 100% credit to the first interaction."),
            )]);
        }

        if has("Email Marketing") {
            qs.extend(vec![self.mcq(
                "Which tactic most improves email deliverability?",
                vec![
                    "List hygiene and double opt-in".into(),
                    "Embedding video attachments".into(),
                    "ALL CAPS subject lines".into(),
                    "Using noreply@ by default".into(),
                ],
                0,
                2,
                Some("Permission-based lists and hygiene reduce spam complaints."),
            )]);
        }

        qs
    }

    fn curated_hr_mcqs(&self, skills: &[String]) -> Vec<Question> {
        let mut qs: Vec<Question> = Vec::new();
        let has = |k: &str| skills.iter().any(|s| s.eq_ignore_ascii_case(k));

        if has("Recruiting") {
            qs.extend(vec![self.mcq(
                "Which metric best evaluates sourcing channel performance?",
                vec![
                    "Qualified hires per source".into(),
                    "Total resumes received".into(),
                    "Career page views".into(),
                    "Office attendance".into(),
                ],
                0,
                2,
                Some("Quality of hires is a stronger signal than raw volume."),
            )]);
        }

        if has("Labor Law") {
            qs.extend(vec![self.mcq(
                "Which scenario typically requires overtime pay under FLSA equivalents?",
                vec![
                    "Non-exempt employee works >40 hours/week".into(),
                    "Exempt salaried employee on-call".into(),
                    "Contractor invoices monthly".into(),
                    "Intern attends training".into(),
                ],
                0,
                2,
                Some("Non-exempt status triggers overtime; exemptions vary by jurisdiction."),
            )]);
        }

        if has("Performance Management") {
            qs.extend(vec![self.mcq(
                "Which practice best reduces recency bias in performance reviews?",
                vec![
                    "Quarterly documented check-ins".into(),
                    "Single annual recall-based review".into(),
                    "Anonymous peer rumor logs".into(),
                    "Manager memory only".into(),
                ],
                0,
                2,
                Some("Frequent, documented feedback creates a fuller record."),
            )]);
        }

        if has("Compensation") {
            qs.extend(vec![self.mcq(
                "What is the primary purpose of salary bands?",
                vec![
                    "Ensure internal equity and consistency".into(),
                    "Hide pay disparities".into(),
                    "Eliminate negotiation".into(),
                    "Set arbitrary caps".into(),
                ],
                0,
                2,
                Some("Bands align pay to levels and maintain equity."),
            )]);
        }

        if has("Employee Relations") {
            qs.extend(vec![self.mcq(
                "What is the first priority when receiving a harassment complaint?",
                vec![
                    "Ensure safety and begin impartial fact-finding".into(),
                    "Wait for annual review".into(),
                    "Announce to the whole team".into(),
                    "Delete the report for privacy".into(),
                ],
                0,
                2,
                Some("Prompt, safe handling and impartial investigation are critical."),
            )]);
        }

        qs
    }

    fn curated_generic_mcqs(&self, topic: &str) -> Vec<Question> {
        vec![self.mcq(
            &format!("Which KPI best indicates success in {} initiatives?", topic),
            vec![
                "A measurable, leading indicator tied to business outcome".into(),
                "Unrelated vanity metric".into(),
                "Server CPU utilization".into(),
                "Team lunch satisfaction".into(),
            ],
            0,
            2,
            None,
        )]
    }

    fn mcq(
        &self,
        question: &str,
        options: Vec<String>,
        correct_index: i32,
        points: i32,
        explanation: Option<&str>,
    ) -> Question {
        Question {
            id: 0,
            question_type: QuestionType::MultipleChoice,
            question: question.to_string(),
            points: points.clamp(1, 5),
            details: QuestionDetails::MultipleChoice(MultipleChoiceDetails {
                options,
                correct_answer: correct_index,
                explanation: explanation.map(|s| s.to_string()),
            }),
        }
    }

    fn generate_dynamic_topups(&self, skills: &[String], needed: usize) -> Vec<Question> {
        let mut out: Vec<Question> = Vec::with_capacity(needed);
        let skills = if skills.is_empty() {
            vec!["General".to_string()]
        } else {
            skills.to_vec()
        };
        let mut i = 0usize;
        while out.len() < needed {
            let skill = skills[i % skills.len()].clone();
            if out.len() % 3 == 2 {
                out.push(Question {
                    id: 0,
                    question_type: QuestionType::ShortAnswer,
                    question: format!("Explain a real-world scenario where {} is critical. List constraints, risks, and trade-offs.", skill),
                    points: 0,
                    details: QuestionDetails::ShortAnswer(ShortAnswerDetails {
                        expected_keywords: Some(vec![skill.clone(), "trade-offs".to_string(), "constraints".to_string()]),
                        min_words: Some(60),
                        ai_grading: true,
                    }),
                });
            } else {
                let variants = vec![
                    (
                        format!("{}", skill),
                        "Performance".to_string(),
                        "Reliability".to_string(),
                        "Security".to_string(),
                    ),
                    (
                        format!("{} usage", skill),
                        "Syntax".to_string(),
                        "UI".to_string(),
                        "Graphics".to_string(),
                    ),
                    (
                        format!("{} concept", skill),
                        "Networking".to_string(),
                        "Filesystem".to_string(),
                        "Audio".to_string(),
                    ),
                ];
                let (a, b, c, d) = &variants[i % variants.len()];
                out.push(self.mcq(
                    &format!("Which option is most directly associated with {}?", skill),
                    vec![a.clone(), b.clone(), c.clone(), d.clone()],
                    0,
                    2,
                    None,
                ));
            }
            i += 1;
        }
        out
    }

    fn looks_trivial(&self, q: &str) -> bool {
        let q = q.to_lowercase();
        q.contains("which of the following")
            || q.contains("what is the")
            || q.contains("most directly associated")
            || q.contains("most closely related")
            || q.contains("which statement best describes")
            || q.starts_with("write a question")
    }
}
