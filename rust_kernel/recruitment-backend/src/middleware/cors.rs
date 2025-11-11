use tower_http::cors::{Any, CorsLayer};

pub fn permissive_cors() -> CorsLayer {
    CorsLayer::new()
        .allow_methods(Any)
        .allow_headers(Any)
        .allow_origin(Any)
}
