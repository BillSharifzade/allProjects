use crate::error::Result;
use reqwest::Client;
use serde_json::Value as JsonValue;

#[derive(Clone)]
pub struct EvalService {
    client: Client,
    api_key: String,
}

impl EvalService {
    pub fn new(api_key: String, client: Client) -> Self {
        Self { client, api_key }
    }

    pub async fn critique_question(&self, question: &JsonValue) -> Result<(f32, String)> {
        let models = &[
            "google/gemma-3n-e2b-it:free",
            "deepseek/deepseek-chat-v3.1:free",
        ];
        let payload = serde_json::json!({
            "system": "You are a strict quality judge for assessment questions. Output a JSON object with 'score' (0.0-1.0) and 'critique' string.",
            "user": {
                "question_to_evaluate": question,
                "rubric": {
                    "relevance": "Is the question relevant to the profession and topic?",
                    "clarity": "Is the question unambiguous and easy to understand?",
                    "difficulty": "Is it non-trivial? Avoids overly simple definitions.",
                    "mcq_quality": "If MCQ, are options distinct, plausible, and non-repetitive?",
                    "bias_free": "Is the question free of cultural or demographic bias?",
                    "single_truth": "Is there one clear correct answer?",
                    "no_trivial_patterns": "Avoids 'all of the above' or 'most closely related'."
                },
                "schema": {"type":"object","required":["score","critique"],"properties":{
                    "score":{"type":"number"}, "critique":{"type":"string"}
                }}
            }
        });

        #[derive(serde::Serialize)]
        struct Msg<'a> {
            role: &'a str,
            content: String,
        }
        #[derive(serde::Serialize)]
        struct ResponseFormat<'a> {
            #[serde(rename = "type")]
            r#type: &'a str,
        }
        #[derive(serde::Serialize)]
        struct Req<'a> {
            model: &'a str,
            temperature: f32,
            response_format: ResponseFormat<'a>,
            messages: Vec<Msg<'a>>,
        }
        #[derive(serde::Deserialize)]
        struct RespChoiceMsg {
            content: String,
        }
        #[derive(serde::Deserialize)]
        struct RespChoice {
            message: RespChoiceMsg,
        }
        #[derive(serde::Deserialize)]
        struct Resp {
            choices: Vec<RespChoice>,
        }

        let system_content = payload["system"].as_str().unwrap().to_string();
        let user_content = serde_json::to_string(&payload["user"])?;

        for model in models {
            let req = Req {
                model,
                temperature: 0.1,
                response_format: ResponseFormat {
                    r#type: "json_object",
                },
                messages: vec![
                    Msg {
                        role: "system",
                        content: system_content.clone(),
                    },
                    Msg {
                        role: "user",
                        content: user_content.clone(),
                    },
                ],
            };
            if let Ok(resp) = self
                .client
                .post("https://openrouter.ai/api/v1/chat/completions")
                .bearer_auth(&self.api_key)
                .json(&req)
                .send()
                .await
            {
                if let Ok(body) = resp.json::<Resp>().await {
                    if let Some(first) = body.choices.into_iter().next() {
                        if let Ok(val) = serde_json::from_str::<JsonValue>(&first.message.content) {
                            let score =
                                val.get("score").and_then(|v| v.as_f64()).unwrap_or(0.0) as f32;
                            let critique = val
                                .get("critique")
                                .and_then(|v| v.as_str())
                                .unwrap_or("No critique.")
                                .to_string();
                            return Ok((score, critique));
                        }
                    }
                }
            }
        }
        Ok((0.0, "Judge model failed".to_string()))
    }
}
