use chrono::{DateTime, Utc};

pub fn now() -> DateTime<Utc> {
    Utc::now()
}

pub fn to_rfc3339(dt: DateTime<Utc>) -> String {
    dt.to_rfc3339()
}

pub fn from_rfc3339(s: &str) -> anyhow::Result<DateTime<Utc>> {
    Ok(DateTime::parse_from_rfc3339(s)?.with_timezone(&Utc))
}
