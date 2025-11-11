use validator::Validate;

pub fn validate<T: Validate>(val: &T) -> Result<(), validator::ValidationErrors> {
    val.validate()
}
