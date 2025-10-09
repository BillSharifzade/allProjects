# Commands

### New project
- cargo install fyrox-template
- > First time only
- fyrox-template init --name bunny_to_rust --style 3d
### Running the game
- cargo run --package executor --release
### Running the scene editor
- cargo run --package editor --release
### Create script
- fyrox-template script --name new_script
### Update template
- fyrox-template upgrade --version=nightly
### Update engine
- cargo update


# Executors Utils
### Change update framerate
- executor/src/main.rs
### Add
```executor.set_desired_update_rate(120.0);```