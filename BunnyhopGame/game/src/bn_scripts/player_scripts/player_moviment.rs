use fyrox::{
    core::{
        algebra::{ArrayStorage, Const, Matrix, UnitQuaternion, Vector3},
        impl_component_provider,
        log::Log,
        num_traits::Float,
        pool::Handle,
        reflect::prelude::*,
        uuid::{uuid, Uuid},
        visitor::prelude::*,
        TypeUuidProvider,
    },
    event::{ElementState, Event, WindowEvent},
    keyboard::{KeyCode, PhysicalKey},
    scene::{collider::Collider, node::Node, rigidbody::RigidBody, transform::Transform},
    script::{ScriptContext, ScriptTrait},
};

use crate::bn_scripts::objects_scripts::timer::Timer;

use super::{
    camera_moviment::CameraMoviment, foot_collider::FootCollider, frontal_collider::FrontalCollider,
};

#[derive(Visit, Reflect, Default, Debug, Clone)]
pub struct PlayerMoviment {
    camera_node: Handle<Node>,
    foot_collider_node: Handle<Node>,
    frontal_collider_node: Handle<Node>,
    timer_node: Handle<Node>,
    // Player Directions
    position_x: bool,
    position_x_negative: bool,
    position_z: bool,
    position_z_negative: bool,
    jump: bool,
    // Flags
    ticks_dessaceleration: i32,
    ticks_jump_cooldown: i32,
    ticks_reset_cooldown: i32,
    acceleration: f32,
    down_speed: f32,
    straffing: bool,
    old_camera_yaw: f32,
}
impl PlayerMoviment {
    /// Process all keyboard for player moviment
    pub fn process_input_event(&mut self, event: &Event<()>, context: &mut ScriptContext) {
        match event {
            Event::WindowEvent { event, .. } => {
                if let WindowEvent::KeyboardInput { event, .. } = event {
                    // Enabling the timer
                    if let Some(timer_node_script_ref) = context
                        .scene
                        .graph
                        .try_get_script_of_mut::<Timer>(self.timer_node)
                    {
                        let pressed = event.state == ElementState::Pressed;
                        if let PhysicalKey::Code(code) = event.physical_key {
                            match code {
                                KeyCode::KeyW => self.position_z = pressed,
                                KeyCode::KeyS => self.position_z_negative = pressed,
                                KeyCode::KeyA => self.position_x = pressed,
                                KeyCode::KeyD => self.position_x_negative = pressed,
                                KeyCode::Space => self.jump = pressed,
                                KeyCode::KeyR => timer_node_script_ref.reset_timer(),
                                _ => (),
                            }
                        }
                    } else {
                        Log::err("Timer Error: Cannot retrieve timer data, keyboard packet lost");
                    }
                }
            }
            _ => (),
        }
    }

    ///Function to detect if the R keyboard is pressed to reset the scene
    pub fn reset_player(&mut self, event: &Event<()>, context: &mut ScriptContext) {
        match event {
            Event::WindowEvent { event, .. } => {
                if let WindowEvent::KeyboardInput { event, .. } = event {
                    if let PhysicalKey::Code(code) = event.physical_key {
                        match code {
                            KeyCode::KeyR => {
                                //Reset Player Observer
                                if self.ticks_reset_cooldown > 30 {
                                    // Borrow rigid body node.
                                    let body =
                                        context.scene.graph[context.handle].as_rigid_body_mut();
                                    self.ticks_reset_cooldown = 0;
                                    self.acceleration = 1.;
                                    // Reseting moviment
                                    body.set_lin_vel(Vector3::new(0.0, 0.0, 0.0));
                                    // Reseting player position
                                    let player: &mut Transform =
                                        context.scene.graph[context.handle].local_transform_mut();
                                    player.set_position(Vector3::new(-0.008, 3.204, 8.868));
                                    // Send a message to reset the pitch view and yaw variable
                                    context.message_sender.send_global("reset_camera");
                                    // Reseting the Yaw
                                    context.scene.graph[context.handle]
                                        .local_transform_mut()
                                        .set_rotation(UnitQuaternion::from_axis_angle(
                                            &Vector3::y_axis(),
                                            (540.).to_radians(),
                                        ));
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

    /// Calculate the player velocity based in acceleration, also
    /// will calculate the collisions to stop the velocity
    pub fn velocity(
        &mut self,
        velocity: Matrix<f32, Const<3>, Const<1>, ArrayStorage<f32, 3, 1>>,
        is_on_air: bool,
        is_on_slide: bool,
        is_frontal_collide: bool,
        is_pressing_s: bool,
        acceleration_mouse_yaw: f32,
        acceleration_mouse_pitch: f32,
        direction_mouse_yaw: &str,
    ) -> Matrix<f32, Const<3>, Const<1>, ArrayStorage<f32, 3, 1>> {
        let mut base_velocity = velocity;
        let calculate_acceleration: bool =
            (is_on_air || is_on_slide) && !is_pressing_s && !is_frontal_collide && self.position_z;
        // We use the for to
        for i in 0..3 {
            if i == 1 {
                continue;
            };
            //If is in the air and moving the camera
            if calculate_acceleration
                && (acceleration_mouse_yaw != 0. || acceleration_mouse_pitch != 0.)
            {
                let acceleration: f32;
                //Determines the maximum speed earned
                // Low speed needs to get a good acceleration
                if self.acceleration <= 8. {
                    // Check if the acceleration is more than should be
                    if acceleration_mouse_yaw >= 0.02 {
                        acceleration = 0.010;
                    } else {
                        // Add acceleration
                        acceleration = acceleration_mouse_yaw / 20.0;
                    }
                }
                // Medium speed needs get a bad velocity
                else if self.acceleration > 8. && self.acceleration < 16. {
                    // Check if the acceleration is more than should be
                    if acceleration_mouse_yaw >= 0.02 {
                        acceleration = 0.005;
                    } else {
                        // Add acceleration
                        acceleration = acceleration_mouse_yaw / 40.0;
                    }
                }
                // High speed needs to block ultra gain velocity
                else {
                    // Check if the acceleration is more than should be
                    if acceleration_mouse_yaw >= 0.02 {
                        acceleration = 0.002;
                    } else {
                        // Add acceleration
                        acceleration = acceleration_mouse_yaw / 100.0;
                    }
                }
                self.ticks_dessaceleration = 0;
                // Not pressing W situation
                if !self.position_z {
                    //Base Acceleration /    Reduced by 0.5%     /     Slider Acceleration
                    base_velocity[i] = (base_velocity[i] * 0.995) + acceleration_mouse_pitch;
                }
                // Normal situation
                else {
                    let acceleration_calculated: f32;
                    // Calculating the keyboard A/D and camera direction
                    if (direction_mouse_yaw == "left" && self.position_x)
                        || (direction_mouse_yaw == "right" && self.position_x_negative)
                    {
                        acceleration_calculated = acceleration * 1.5;
                    } else {
                        acceleration_calculated = acceleration;
                    }
                    //Base Acceleration / Air Acceleration / Slider Acceleration
                    self.acceleration += acceleration_calculated + acceleration_mouse_pitch;
                    base_velocity[i] *= self.acceleration;
                }
            }
            //If is only in the air
            else if calculate_acceleration {
                self.ticks_dessaceleration = 0;
                base_velocity[i] *= self.acceleration;
            }
            //Lowering the acceleartion conditions
            else {
                //If is on the ground
                if !is_on_air && !is_frontal_collide {
                    self.ticks_dessaceleration += 1;
                    if self.ticks_dessaceleration >= 20 {
                        self.ticks_dessaceleration = 20;
                        self.acceleration /= 1.03;
                        if self.acceleration <= 1. {
                            self.acceleration = 1.;
                            self.straffing = false;
                        }
                    }
                    base_velocity[i] *= self.acceleration;
                    //If is fronta colission
                }
                // If is frontal collide
                else if is_frontal_collide {
                    self.acceleration /= 3.;
                    if self.acceleration <= 1. {
                        self.acceleration = 1.;
                        self.straffing = false;
                    }
                    base_velocity[i] *= self.acceleration;
                    //If pressing S
                }
                // If pressed the S
                else if is_pressing_s {
                    self.acceleration /= 2.;
                    if self.acceleration <= 1. {
                        self.acceleration = 1.;
                        self.straffing = false;
                    }
                    base_velocity[i] *= self.acceleration;
                }
                // In others cases the velocity stays equals
                // this should not to be called anyways but is good to have
                // a treatment
                else {
                    base_velocity[i] *= self.acceleration;
                }
            }
        }
        return base_velocity;
    }

    /// Calculate the player acceleration and then call the function velocity
    /// to change the player velocity and then change the position of the player
    pub fn calculate_acceleration(&mut self, context: &mut ScriptContext) {
        // Start the timer
        if self.jump {
            // Enabling the timer
            if let Some(timer_node_script_ref) = context
                .scene
                .graph
                .try_get_script_of_mut::<Timer>(self.timer_node)
            {
                timer_node_script_ref.stop = false;
            } else {
                Log::err("Timer Error: cannot start the timer, reference not found");
            }
        }
        let is_on_air: bool;
        let is_on_slider: bool;
        let wall_slider_direction: String;
        let is_frontal_collide: bool;
        let camera_yaw: f32;
        let camera_pitch: f32;
        //Getting variables from others scripts
        {
            // Receiving the cameras
            if let Some(camera_node_script_ref) = context
                .scene
                .graph
                .try_get_script_of::<CameraMoviment>(self.camera_node)
            {
                camera_yaw = camera_node_script_ref.yaw;
                camera_pitch = camera_node_script_ref.pitch;
            } else {
                camera_yaw = 0.0;
                camera_pitch = 0.0;
            }

            // Receiving the foot collider
            if let Some(foot_collider_node_script_ref) = context
                .scene
                .graph
                .try_get_script_of::<FootCollider>(self.foot_collider_node)
            {
                is_on_air = foot_collider_node_script_ref.is_on_air;
                is_on_slider = foot_collider_node_script_ref.is_on_slider;
                wall_slider_direction = foot_collider_node_script_ref
                    .wall_slider_direction
                    .to_string();
            } else {
                is_on_air = false;
                is_on_slider = false;
                wall_slider_direction = String::from("none");
            }

            // Receiving the frontal collider
            if let Some(frontal_collider_node_script_ref) = context
                .scene
                .graph
                .try_get_script_of::<FrontalCollider>(self.frontal_collider_node)
            {
                is_frontal_collide = frontal_collider_node_script_ref.is_frontal_collide;
            } else {
                is_frontal_collide = false;
            }
        }
        // Handles the yaw view for the camera, more info in process_camera_moviment_yaw in camera script
        context.scene.graph[context.handle]
            .local_transform_mut()
            .set_rotation(UnitQuaternion::from_axis_angle(
                &Vector3::y_axis(),
                camera_yaw.to_radians() / 3.,
            ));
        let camera_yaw_radians: f32 = camera_yaw.to_radians();
        // Getting the mouse direction
        let mouse_direction_yaw: &str;
        if camera_yaw_radians > self.old_camera_yaw {
            mouse_direction_yaw = "left";
        } else if camera_yaw_radians < self.old_camera_yaw {
            mouse_direction_yaw = "right";
        } else {
            mouse_direction_yaw = "none";
        }
        //Movement Player Update
        // Borrow rigid body node.
        let body = context.scene.graph[context.handle].as_rigid_body_mut();
        // Keep only vertical velocity, and drop horizontal.
        let mut velocity = Vector3::new(0.0, body.lin_vel().y, 0.0);
        let mut dessacelerate: bool = false;
        let mut mouse_accelerate_yaw: f32 = 0.;
        let mut mouse_accelerate_pitch: f32 = 0.;
        // Change the velocity depending on the keys pressed.
        if self.position_z || self.straffing {
            // If we moving forward then add "look" vector of the body.
            self.straffing = true;
            velocity += body.look_vector() * 2.;
        }
        if self.position_z_negative {
            // If we moving backward then subtract "look" vector of the body.
            velocity -= body.look_vector() * 2.;
            dessacelerate = true;
        }
        if self.position_x {
            // If we moving left then add "side" vector of the body.
            if self.straffing && is_on_slider {
                velocity += body.side_vector() * 1.00005;
            }
            // Reduce moviment if is straffing
            else if self.straffing {
                velocity += body.side_vector() * 1.1;
            }
            // Normal moviment
            else {
                velocity += body.side_vector() * 2.0;
            }
        }
        if self.position_x_negative {
            // If we moving right then subtract "side" vector of the body.
            // Reduce moviment if is on slide and straffing
            if self.straffing && is_on_slider {
                velocity -= body.side_vector() * 1.00005;
            }
            // Reduce moviment if is straffing
            else if self.straffing {
                velocity -= body.side_vector() * 1.1;
            }
            // Normal moviment
            else {
                velocity -= body.side_vector() * 2.0;
            }
        }
        // Jump System
        if self.jump && !is_on_slider && !is_on_air && self.ticks_jump_cooldown <= 3 {
            //Check if is the first tick
            if self.ticks_jump_cooldown == -1 {
                self.ticks_jump_cooldown = 0;
            }
            // If we moving up add "up" vector of the body
            velocity += body.up_vector() * 2.;
        }
        //Cooldown the Jump Ticks
        if self.ticks_jump_cooldown >= 0 && self.ticks_jump_cooldown <= 20 {
            self.ticks_jump_cooldown += 1;
        } else if self.ticks_jump_cooldown > 20 {
            self.ticks_jump_cooldown = -1;
        }
        // Calculation the horizontal acceleration by mouse movement
        if self.old_camera_yaw != camera_yaw_radians {
            //Calculates the mouse velocity
            let mouse_position_yaw: f32;
            let old_mouse_position_yaw: f32;
            //Negative to Positive
            if camera_yaw_radians < 0. {
                mouse_position_yaw = camera_yaw_radians.abs();
                old_mouse_position_yaw = self.old_camera_yaw.abs();
            } else {
                mouse_position_yaw = camera_yaw_radians;
                old_mouse_position_yaw = self.old_camera_yaw
            }
            //Difference between
            if mouse_position_yaw != old_mouse_position_yaw {
                if mouse_position_yaw < old_mouse_position_yaw {
                    mouse_accelerate_yaw = old_mouse_position_yaw - mouse_position_yaw;
                } else {
                    mouse_accelerate_yaw = mouse_position_yaw - old_mouse_position_yaw;
                }
            }
            self.old_camera_yaw = camera_yaw_radians;
        }
        // Calculation the vertical acceleration by mouse moviment
        if is_on_slider {
            // Gaining Acceleration
            if camera_pitch >= 0.0 {
                if camera_pitch > 80. {
                    self.down_speed += 0.05;
                    mouse_accelerate_pitch = 0.15;
                } else if camera_pitch > 70.0 {
                    self.down_speed += 0.04;
                    mouse_accelerate_pitch = 0.10;
                } else if camera_pitch > 60.0 {
                    self.down_speed += 0.03;
                    mouse_accelerate_pitch = 0.05;
                } else if camera_pitch > 50.0 {
                    self.down_speed += 0.02;
                    mouse_accelerate_pitch = 0.04;
                } else if camera_pitch > 40.0 {
                    self.down_speed += 0.01;
                    mouse_accelerate_pitch = 0.03;
                } else if camera_pitch > 30.0 {
                    self.down_speed += 0.009;
                    mouse_accelerate_pitch = 0.02;
                } else if camera_pitch > 20.0 {
                    self.down_speed += 0.008;
                    mouse_accelerate_pitch = 0.015;
                } else if camera_pitch > 10.0 {
                    self.down_speed += 0.007;
                    mouse_accelerate_pitch = 0.010;
                } else if camera_pitch > 1.0 {
                    self.down_speed += 0.006;
                    mouse_accelerate_pitch = 0.005;
                } else {
                    mouse_accelerate_pitch = 0.0;
                }
                // Downing the player
                velocity -= body.up_vector() * self.down_speed;
            }
            // Loosing Acceleration
            else {
                // Reset down speed
                self.down_speed = 0.0;
                if camera_pitch < -80. {
                    mouse_accelerate_pitch = -0.15;
                } else if camera_pitch < -70. {
                    mouse_accelerate_pitch = -0.10;
                } else if camera_pitch < -60. {
                    mouse_accelerate_pitch = -0.05;
                } else if camera_pitch < -50. {
                    mouse_accelerate_pitch = -0.04;
                } else if camera_pitch < -40. {
                    mouse_accelerate_pitch = -0.03;
                } else if camera_pitch < -30. {
                    mouse_accelerate_pitch = -0.02;
                } else if camera_pitch < -20. {
                    mouse_accelerate_pitch = -0.015;
                } else if camera_pitch < -10. {
                    mouse_accelerate_pitch = -0.010;
                } else if camera_pitch < -1.0 {
                    mouse_accelerate_pitch = -0.005;
                } else {
                    mouse_accelerate_pitch = 0.0;
                }
            }
        } else {
            self.down_speed = 0.0;
        }
        // Wall Slider Jump
        {
            let direction: &str = &wall_slider_direction;
            match direction {
                "left" => velocity += body.side_vector() * 2.0,
                "right" => velocity -= body.side_vector() * 2.0,
                "front" => velocity += body.look_vector() * 2.0,
                "back" => velocity -= body.look_vector() * 2.0,
                "none" => {}
                _ => {}
            }
            if direction != "none" {
                velocity += body.up_vector() * 3.0;
            }
        }
        // Change the velocity of the player
        body.set_lin_vel(self.velocity(
            velocity,
            is_on_air,
            is_on_slider,
            is_frontal_collide,
            dessacelerate,
            mouse_accelerate_yaw,
            mouse_accelerate_pitch,
            mouse_direction_yaw,
        ));
    }

    /// Detects if player has reached in the end of the map
    /// the handle is te collider you want to detect, in this case the foot collider
    pub fn verify_finish(&mut self, handle: Handle<Node>, context: &mut ScriptContext) {
        let mut stage_finished = false;
        // Check if player is collided with finish object
        {
            let graph = &context.scene.graph;
            if let Some(collider) = graph.try_get(handle).and_then(|n| n.cast::<Collider>()) {
                for contact in collider.contacts(&graph.physics) {
                    for manifold in contact.manifolds.iter() {
                        // Checking the actual foot collision
                        if let Some(actual_collider) = graph
                            .try_get(manifold.rigid_body2)
                            .and_then(|n| n.cast::<RigidBody>())
                        {
                            // If is a slider then
                            if actual_collider.tag() == "Finish" {
                                if manifold.local_n1.y.abs() > 0.7
                                    || manifold.local_n2.y.abs() > 0.7
                                {
                                    stage_finished = true;
                                }
                            }
                        }
                    }
                }
            }
        }
        // Change the timer to finished
        {
            if stage_finished {
                if let Some(timer_node_script_ref) = context
                    .scene
                    .graph
                    .try_get_script_of_mut::<Timer>(self.timer_node)
                {
                    timer_node_script_ref.finished = true;
                }
            }
        }
    }
}

impl_component_provider!(PlayerMoviment);

impl TypeUuidProvider for PlayerMoviment {
    fn type_uuid() -> Uuid {
        uuid!("5e5f5d29-a9a9-447e-8010-9f413d9f6efb")
    }
}

impl ScriptTrait for PlayerMoviment {
    fn on_os_event(&mut self, event: &Event<()>, context: &mut ScriptContext) {
        //Keyboard Observer
        self.process_input_event(event, context);
        //Check if player asked for reset
        self.reset_player(event, context);
    }

    fn on_update(&mut self, context: &mut ScriptContext) {
        //Calculate the player accelaration and change the position
        self.calculate_acceleration(context);
        // Detects if the player has reached the end
        self.verify_finish(self.foot_collider_node, context);

        //Reset Tick Cooldown
        if self.ticks_reset_cooldown <= 30 {
            self.ticks_reset_cooldown += 1;
        }
    }
}
