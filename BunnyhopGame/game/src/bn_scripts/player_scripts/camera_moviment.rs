use fyrox::{
    core::{
        algebra::{UnitQuaternion, Vector3},
        impl_component_provider,
        pool::Handle,
        reflect::prelude::*,
        uuid::{uuid, Uuid},
        visitor::prelude::*,
        TypeUuidProvider, log::Log,
    },
    engine::GraphicsContext,
    event::{DeviceEvent, ElementState, Event, WindowEvent},
    keyboard::{KeyCode, PhysicalKey},
    scene::node::Node,
    script::{ScriptContext, ScriptMessageContext, ScriptMessagePayload, ScriptTrait},
    window::CursorGrabMode,
};

use crate::bn_scripts::GAME_PAUSED;

#[derive(Visit, Reflect, Default, Debug, Clone)]
pub struct CameraMoviment {
    pub pitch: f32,
    pub yaw: f32,
    first_load: bool,
    player_node: Handle<Node>,
}
impl CameraMoviment {
    //Mouse Detect Function Pitch
    fn process_camera_moviment_pitch(&mut self, event: &Event<()>, camera_node: &mut Node) {
        match event {
            Event::DeviceEvent { event, .. } => {
                if let DeviceEvent::MouseMotion { delta } = event {
                    self.pitch = (self.pitch + delta.1 as f32).clamp(-90.0, 90.0);
                    camera_node.local_transform_mut().set_rotation(
                        UnitQuaternion::from_axis_angle(
                            &Vector3::x_axis(),
                            self.pitch.to_radians() / 3.,
                        ),
                    );
                }
            }
            _ => (),
        }
    }
    //Mouse Detect Function Yaw
    fn process_camera_moviment_yaw(&mut self, event: &Event<()>) {
        //Notes, the camera moviment is handled by the moviment, because for some reason
        //if this is handled by the camera script will cause camera lag
        match event {
            Event::DeviceEvent { event, .. } => {
                if let DeviceEvent::MouseMotion { delta } = event {
                    self.yaw -= delta.0 as f32;
                }
            }
            _ => (),
        }
    }
    // Reset the view to default of level
    fn reset_camera_moviment_with_script_message(&mut self, context: &mut ScriptMessageContext) {
        self.yaw = 540.;
        self.pitch = 0.;

        let camera_node = &mut context.scene.graph[context.handle];
        camera_node
            .local_transform_mut()
            .set_rotation(UnitQuaternion::from_axis_angle(
                &Vector3::x_axis(),
                self.pitch.to_radians(),
            ));
    }

    // Detect Pause button
    fn process_pause_button(&mut self, event: &Event<()>, context: &mut ScriptContext) {
        match event {
            Event::WindowEvent { event, .. } => {
                if let WindowEvent::KeyboardInput { event, .. } = event {
                    let pressed = event.state == ElementState::Pressed;
                    if let PhysicalKey::Code(code) = event.physical_key {
                        match code {
                            KeyCode::Escape => {
                                // Check if is pressed
                                if pressed {
                                    // Change game paused
                                    unsafe {
                                        GAME_PAUSED = !GAME_PAUSED;
                                    }
                                    // Check if graphics context is initialized
                                    if let GraphicsContext::Initialized(ref graphics_context) =
                                        context.graphics_context
                                    {
                                        // Block cursor when game is running
                                        if unsafe { !GAME_PAUSED } {
                                            let window = &graphics_context.window;
                                            // Disable cursor visibility
                                            window.set_cursor_visible(false);
                                            // Prevent the cursor to be moved outside of the window.
                                            let _ = window.set_cursor_grab(CursorGrabMode::Confined);
                                            Log::info("The game Resumed");
                                        }
                                        // Unblock cursor
                                        else {
                                            let window = &graphics_context.window;
                                            // Disable cursor visibility
                                            window.set_cursor_visible(true);
                                            // Prevent the cursor to be moved outside of the window.
                                            let _ = window.set_cursor_grab(CursorGrabMode::None);
                                            Log::info("The game is Paused");
                                        }
                                    }
                                }
                            }
                            _ => (),
                        }
                    }
                }
            }
            _ => (),
        }
    }
}

impl_component_provider!(CameraMoviment);

impl TypeUuidProvider for CameraMoviment {
    fn type_uuid() -> Uuid {
        uuid!("9a9be198-92d4-4693-bd4a-0070d73b95ac")
    }
}

impl ScriptTrait for CameraMoviment {
    fn on_start(&mut self, context: &mut ScriptContext) {
        // Subscribing messages
        context
            .message_dispatcher
            .subscribe_to::<&str>(context.handle);
    }

    fn on_os_event(&mut self, event: &Event<()>, context: &mut ScriptContext) {
        // game paused = only check the pause button
        if unsafe { GAME_PAUSED } {
            // Process Pause button
            self.process_pause_button(event, context);
            return;
        } else {
            // Process Pause button
            self.process_pause_button(event, context);
        }
        // Enable mouse detection
        // Process Vertical View
        self.process_camera_moviment_pitch(event, &mut context.scene.graph[context.handle]);
        // Process Horizontal View
        self.process_camera_moviment_yaw(event);
    }

    fn on_message(
        &mut self,
        message: &mut dyn ScriptMessagePayload,
        context: &mut ScriptMessageContext,
    ) {
        // React to message.
        if let Some(data) = message.downcast_ref::<&str>() {
            if data == &"reset_camera" {
                self.reset_camera_moviment_with_script_message(context);
            }
        }
    }
}
