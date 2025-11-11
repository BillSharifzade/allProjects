use crate::error::Result;
use anyhow::Context as _;
use reqwest::Client;

#[derive(Clone)]
pub struct EmbedService {
    client: Client,
    api_key: String,
}

impl EmbedService {
    pub fn new(api_key: String, client: Client) -> Self {
        Self { client, api_key }
    }

    pub async fn embed_texts(&self, texts: &[String]) -> Result<Vec<Vec<f32>>> {
        #[derive(serde::Serialize)]
        struct EmbReq<'a> {
            model: &'a str,
            input: &'a [String],
        }
        #[derive(serde::Deserialize)]
        struct EmbData {
            embedding: Vec<f32>,
        }
        #[derive(serde::Deserialize)]
        struct EmbResp {
            data: Vec<EmbData>,
        }

        if texts.is_empty() {
            return Ok(Vec::new());
        }
        let body = EmbReq {
            model: "nomic-ai/nomic-embed-text-v1.5",
            input: texts,
        };
        let resp = self
            .client
            .post("https://openrouter.ai/api/v1/embeddings")
            .bearer_auth(&self.api_key)
            .header("Accept", "application/json")
            .header("Content-Type", "application/json")
            .json(&body)
            .send()
            .await
            .context("embeddings request failed")?;

        let status = resp.status();
        let txt = resp.text().await.unwrap_or_default();
        if !status.is_success() {
            return Err(anyhow::anyhow!("embeddings status {}: {}", status.as_u16(), txt).into());
        }
        let parsed: EmbResp = serde_json::from_str(&txt).context("embeddings parse failed")?;
        Ok(parsed.data.into_iter().map(|d| d.embedding).collect())
    }

    pub fn cosine_sim(a: &[f32], b: &[f32]) -> f32 {
        let mut dot = 0f32;
        let mut na = 0f32;
        let mut nb = 0f32;
        for (x, y) in a.iter().zip(b.iter()) {
            dot += x * y;
            na += x * x;
            nb += y * y;
        }
        if na == 0.0 || nb == 0.0 {
            0.0
        } else {
            dot / (na.sqrt() * nb.sqrt())
        }
    }
}
