use fyrox::{
    core::{
        pool::Handle,
        reflect::prelude::*,
        uuid::{uuid, Uuid},
        visitor::prelude::*,
        TypeUuidProvider,
        impl_component_provider,
    },
    scene::{collider::Collider, graph::Graph, node::Node},
    script::{ScriptContext, ScriptTrait},
};

#[derive(Visit, Reflect, Default, Debug, Clone)]
pub struct FrontalCollider {
    pub is_frontal_collide: bool,
}

impl_component_provider!(FrontalCollider);

impl TypeUuidProvider for FrontalCollider {
    fn type_uuid() -> Uuid {
        uuid!("e354a7a8-99df-411c-8efc-6c97566517e0")
    }
}
impl FrontalCollider {
    pub fn has_frontal_contact(&mut self, handle: Handle<Node>, graph: &Graph) -> bool {
        if let Some(collider) = graph.try_get(handle).and_then(|n| n.cast::<Collider>()) {
            for contact in collider.contacts(&graph.physics) {
                for manifold in contact.manifolds.iter() {
                    if manifold.local_n1.y.abs() > 0.7 || manifold.local_n2.y.abs() > 0.7 {
                        return true;
                    }
                }
            }
        }
        false
    }
}

impl ScriptTrait for FrontalCollider {
    fn on_init(&mut self, _context: &mut ScriptContext) {
        self.is_frontal_collide = false;
    }

    fn on_update(&mut self, context: &mut ScriptContext) {
        self.is_frontal_collide = self.has_frontal_contact(context.handle, &context.scene.graph);
    }
}
