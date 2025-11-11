use argon2::{
    password_hash::{PasswordHash, PasswordHasher, PasswordVerifier, SaltString},
    Argon2,
};
use rand::rngs::OsRng;

pub fn hash_password(plain: &str) -> Result<String, argon2::password_hash::Error> {
    let salt = SaltString::generate(&mut OsRng);
    let argon2 = Argon2::default();
    let password_hash = argon2.hash_password(plain.as_bytes(), &salt)?.to_string();
    Ok(password_hash)
}

pub fn verify_password(plain: &str, hashed: &str) -> Result<bool, argon2::password_hash::Error> {
    let parsed_hash = PasswordHash::new(hashed)?;
    let ok = Argon2::default()
        .verify_password(plain.as_bytes(), &parsed_hash)
        .is_ok();
    Ok(ok)
}
