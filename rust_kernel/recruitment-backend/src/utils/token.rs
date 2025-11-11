use rand::{distributions::Alphanumeric, thread_rng, Rng};

pub fn generate_access_token(length: usize) -> String {
    thread_rng()
        .sample_iter(&Alphanumeric)
        .take(length)
        .map(char::from)
        .collect()
}
