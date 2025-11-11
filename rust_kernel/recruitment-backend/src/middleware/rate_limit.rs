use std::sync::{Arc, Mutex};
use std::time::{Duration, Instant};

use axum::body::Body;
use axum::extract::State;
use axum::http::{Request, StatusCode};
use axum::middleware::Next;
use axum::response::{IntoResponse, Response};

#[derive(Debug)]
struct WindowState {
    start: Instant,
    count: u32,
}

#[derive(Clone, Debug)]
pub struct RateLimiter {
    rps: u32,
    window: Arc<Mutex<WindowState>>,
}

impl RateLimiter {
    fn new(rps: u32) -> Self {
        Self {
            rps: rps.max(1),
            window: Arc::new(Mutex::new(WindowState {
                start: Instant::now(),
                count: 0,
            })),
        }
    }

    fn allow(&self) -> bool {
        let mut guard = self.window.lock().expect("rate limiter mutex poisoned");
        let now = Instant::now();
        if now.duration_since(guard.start) >= Duration::from_secs(1) {
            guard.start = now;
            guard.count = 0;
        }
        if guard.count < self.rps {
            guard.count += 1;
            true
        } else {
            false
        }
    }
}

pub async fn rps_middleware(
    State(state): State<RateLimiter>,
    req: Request<Body>,
    next: Next,
) -> Response {
    if !state.allow() {
        return (StatusCode::TOO_MANY_REQUESTS, "rate_limit_exceeded").into_response();
    }
    next.run(req).await
}

pub fn new_rps_state(rps: u32) -> RateLimiter {
    RateLimiter::new(rps)
}
