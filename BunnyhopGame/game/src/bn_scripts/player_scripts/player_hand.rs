use fyrox::{
    core::{
        impl_component_provider,
        reflect::prelude::*,
        uuid::{uuid, Uuid},
        visitor::prelude::*,
        TypeUuidProvider,
    },
    event::Event,
    script::{ScriptContext, ScriptDeinitContext, ScriptTrait},
};

#[derive(Visit, Reflect, Default, Debug, Clone)]
pub struct PlayerHand {
    // Add fields here.
}

impl_component_provider!(PlayerHand);

impl TypeUuidProvider for PlayerHand {
    fn type_uuid() -> Uuid {
        uuid!("0595454f-6d2f-44b9-8bb4-4f4809a63e87")
    }
}

impl ScriptTrait for PlayerHand {
    fn on_init(&mut self, _context: &mut ScriptContext) {
        // Put initialization logic here.
    }

    fn on_start(&mut self, _context: &mut ScriptContext) {
        // There should be a logic that depends on other scripts in scene.
        // It is called right after **all** scripts were initialized.
    }

    fn on_deinit(&mut self, _context: &mut ScriptDeinitContext) {
        // Put de-initialization logic here.
    }

    fn on_os_event(&mut self, _event: &Event<()>, _context: &mut ScriptContext) {
        // Respond to OS events here.
    }

    fn on_update(&mut self, _context: &mut ScriptContext) {
        // Put object logic here.
    }
}
