use fyrox::{
    core::{
        impl_component_provider,
        log::Log,
        reflect::prelude::*,
        uuid::{uuid, Uuid},
        visitor::prelude::*,
        TypeUuidProvider,
    },
    gui::{message::MessageDirection, text::TextMessage},
    script::{ScriptContext, ScriptTrait},
};

use crate::Game;

#[derive(Visit, Reflect, Default, Debug, Clone)]
pub struct Timer {
    milliseconds: f32,
    seconds: i16,
    timer: String,
    pub finished: bool,
    pub stop: bool,
}

impl Timer {
    /// Resets the timer to 0 and make the timer stopped
    pub fn reset_timer(&mut self) {
        self.milliseconds = 0.0;
        self.seconds = 0;
        self.stop = true;
        self.finished = false;
        self.timer = "0:0".to_string();
    }
}

impl_component_provider!(Timer);

impl TypeUuidProvider for Timer {
    fn type_uuid() -> Uuid {
        uuid!("a68ae647-c5d9-46f1-912f-bb7f8b07f1a8")
    }
}

impl ScriptTrait for Timer {
    fn on_init(&mut self, _context: &mut ScriptContext) {
        self.milliseconds = 0.0;
        self.seconds = 0;
        self.stop = true;
        self.timer = "0:0".to_string();
    }

    fn on_update(&mut self, context: &mut ScriptContext) {
        // Verify if timer is stopped
        if !self.stop && !self.finished {
            // Incrementing the timer in milliseconds
            self.milliseconds += context.dt * 1000.0;
            // Rollover full seconds using math (robust to long frames)
            if self.milliseconds >= 1000.0 {
                let extra_seconds = (self.milliseconds / 1000.0).floor() as i16;
                self.seconds += extra_seconds;
                self.milliseconds -= (extra_seconds as f32) * 1000.0;
            }
            // Converts the timer into readable string
            self.timer = format!("{}:{}", self.seconds, self.milliseconds as i16).to_string();
            // Update the Text Widget
            context.user_interface.send_message(TextMessage::text(
                context.plugins.of_type_ref::<Game>().unwrap().timer_text,
                MessageDirection::ToWidget,
                self.timer.to_string(),
            ));
        }
    }
}
