use std::path::PathBuf;

use tokio::process::Command;
use tracing::{error, info, instrument, warn};
use validator::Validate;

use crate::error::{Error, Result};
use crate::AppState;

#[derive(Debug, Clone, serde::Deserialize, Validate)]
pub struct ExternalVacancyPayload {
    #[validate(email)]
    pub email: String,
    #[validate(length(min = 1))]
    pub password: String,
    #[validate(length(min = 1))]
    pub title: String,
    #[validate(length(min = 1))]
    pub content: String,
    pub city: Option<String>,
    pub direction: Option<String>,
    pub company: Option<String>,
    #[serde(default)]
    pub hot: bool,
    pub driver_binary: Option<String>,
}

#[derive(Debug, Clone, serde::Deserialize, Validate)]
pub struct ExternalVacancyDeletePayload {
    #[validate(email)]
    pub email: String,
    #[validate(length(min = 1))]
    pub password: String,
    pub vacancy_id: String,
    pub driver_binary: Option<String>,
}

#[derive(Debug, Clone, serde::Serialize)]
pub struct ExternalVacancyResult {
    pub success: bool,
    pub message: String,
    #[serde(skip_serializing_if = "Option::is_none")]
    pub vacancy_id: Option<String>,
    #[serde(skip_serializing_if = "Option::is_none")]
    pub stdout: Option<String>,
    #[serde(skip_serializing_if = "Option::is_none")]
    pub stderr: Option<String>,
}

pub struct ExternalVacancyService;

impl ExternalVacancyService {
    const DEFAULT_PYTHON: &'static str = "python3";
    const SCRIPT_PATH: &'static str = "../vacancy_creation.py";

    pub fn new(_state: AppState) -> Self {
        Self
    }

    fn build_base_command() -> Result<(Command, PathBuf)> {
        let python_bin = std::env::var("VACANCY_PYTHON_BIN")
            .unwrap_or_else(|_| Self::DEFAULT_PYTHON.to_string());

        let script_path = std::env::var("VACANCY_SCRIPT_PATH")
            .map(PathBuf::from)
            .unwrap_or_else(|_| PathBuf::from(env!("CARGO_MANIFEST_DIR")).join(Self::SCRIPT_PATH));

        if !script_path.exists() {
            return Err(Error::Config(format!(
                "Vacancy automation script not found at {}",
                script_path.display()
            )));
        }

        let command = Command::new(python_bin);
        Ok((command, script_path))
    }

    #[instrument(skip(self, payload))]
    pub async fn create_vacancy(
        &self,
        payload: ExternalVacancyPayload,
    ) -> Result<ExternalVacancyResult> {
        payload.validate()?;

        info!("Launching Selenium vacancy creation job");

        let (mut command, script_path) = Self::build_base_command()?;
        command.arg(&script_path);
        command.arg("--email").arg(&payload.email);
        command.arg("--password").arg(&payload.password);
        command.arg("--title").arg(&payload.title);
        command.arg("--content").arg(&payload.content);

        if let Some(city) = &payload.city {
            command.arg("--city").arg(city);
        }
        if let Some(direction) = &payload.direction {
            command.arg("--direction").arg(direction);
        }
        if let Some(company) = &payload.company {
            command.arg("--company").arg(company);
        }
        if payload.hot {
            command.arg("--hot");
        }
        if let Some(binary) = &payload.driver_binary {
            command.arg("--chrome-binary").arg(binary);
        }

        command.arg("--headless");

        if let Some(parent) = script_path.parent() {
            command.current_dir(parent);
        }

        let output = command.output().await;

        match output {
            Ok(result) => {
                let stdout = String::from_utf8_lossy(&result.stdout).to_string();
                let stderr = String::from_utf8_lossy(&result.stderr).to_string();

                if result.status.success() {
                    info!("Vacancy creation workflow completed successfully");
                    let vacancy_id = stdout
                        .lines()
                        .find_map(|line| {
                            line.strip_prefix("Created vacancy ID:")
                                .map(|value| value.trim().to_string())
                        });

                    Ok(ExternalVacancyResult {
                        success: true,
                        message: "Vacancy created successfully".to_string(),
                        vacancy_id,
                        stdout: Some(stdout),
                        stderr: (!stderr.is_empty()).then_some(stderr),
                    })
                } else {
                    warn!(?stdout, ?stderr, "Vacancy creation workflow failed");
                    Ok(ExternalVacancyResult {
                        success: false,
                        message: format!(
                            "Vacancy creation failed with status {:?}",
                            result.status.code()
                        ),
                        vacancy_id: None,
                        stdout: Some(stdout),
                        stderr: (!stderr.is_empty()).then_some(stderr),
                    })
                }
            }
            Err(err) => {
                error!(error = ?err, "Failed to spawn vacancy creation command");
                Ok(ExternalVacancyResult {
                    success: false,
                    message: format!("Failed to spawn vacancy creation command: {err}"),
                    vacancy_id: None,
                    stdout: None,
                    stderr: None,
                })
            }
        }
    }
}

impl ExternalVacancyService {
    #[instrument(skip(self, payload))]
    pub async fn delete_vacancy(
        &self,
        payload: ExternalVacancyDeletePayload,
    ) -> Result<ExternalVacancyResult> {
        payload.validate()?;

        info!(vacancy_id = %payload.vacancy_id, "Launching Selenium vacancy deletion job");

        let (mut command, script_path) = Self::build_base_command()?;
        command.arg(&script_path);
        command.arg("--email").arg(&payload.email);
        command.arg("--password").arg(&payload.password);
        command
            .arg("--vacancy-id")
            .arg(payload.vacancy_id.to_string());
        command.arg("--delete");
        command.arg("--headless");

        if let Some(binary) = &payload.driver_binary {
            command.arg("--chrome-binary").arg(binary);
        }

        if let Some(parent) = script_path.parent() {
            command.current_dir(parent);
        }

        let output = command.output().await;

        match output {
            Ok(result) => {
                let stdout = String::from_utf8_lossy(&result.stdout).to_string();
                let stderr = String::from_utf8_lossy(&result.stderr).to_string();

                if result.status.success() {
                    info!(vacancy_id = %payload.vacancy_id, "Vacancy deletion workflow completed successfully");
                    Ok(ExternalVacancyResult {
                        success: true,
                        message: "Vacancy deleted successfully".to_string(),
                        vacancy_id: Some(payload.vacancy_id.clone()),
                        stdout: Some(stdout),
                        stderr: (!stderr.is_empty()).then_some(stderr),
                    })
                } else {
                    warn!(
                        vacancy_id = %payload.vacancy_id,
                        ?stdout,
                        ?stderr,
                        "Vacancy deletion workflow failed"
                    );
                    Ok(ExternalVacancyResult {
                        success: false,
                        message: format!(
                            "Vacancy deletion failed with status {:?}",
                            result.status.code()
                        ),
                        vacancy_id: Some(payload.vacancy_id.clone()),
                        stdout: Some(stdout),
                        stderr: (!stderr.is_empty()).then_some(stderr),
                    })
                }
            }
            Err(err) => {
                error!(error = ?err, vacancy_id = %payload.vacancy_id, "Failed to spawn vacancy deletion command");
                Ok(ExternalVacancyResult {
                    success: false,
                    message: format!("Failed to spawn vacancy deletion command: {err}"),
                    vacancy_id: Some(payload.vacancy_id),
                    stdout: None,
                    stderr: None,
                })
            }
        }
    }
}
