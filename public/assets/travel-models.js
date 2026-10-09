import * as THREE from './vendor/three.module.js';

// Original lightweight models; no downloaded models or textures.
const materials = {
  yellow: new THREE.MeshStandardMaterial({ color: 0xffcf48, roughness: .45 }),
  navy: new THREE.MeshStandardMaterial({ color: 0x102f43, roughness: .6 }),
  glass: new THREE.MeshStandardMaterial({ color: 0x157f92, metalness: .2, roughness: .25 }),
  white: new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: .5 }),
  teal: new THREE.MeshStandardMaterial({ color: 0x11aba1, roughness: .4 }),
};
function box(group, size, position, material) {
  const mesh = new THREE.Mesh(new THREE.BoxGeometry(...size), materials[material]);
  mesh.position.set(...position); group.add(mesh); return mesh;
}
function wheels(group, axles, width) {
  for (const x of axles) for (const z of [-width, width]) {
    const tyre = new THREE.Mesh(new THREE.CylinderGeometry(.095, .095, .055, 12), materials.navy);
    tyre.rotation.x = Math.PI / 2; tyre.position.set(x, -.23, z); group.add(tyre);
    const hub = new THREE.Mesh(new THREE.CylinderGeometry(.045, .045, .058, 10), materials.white);
    hub.rotation.x = Math.PI / 2; hub.position.copy(tyre.position); group.add(hub);
  }
}
export function createBus() {
  const bus = new THREE.Group(); bus.name = 'travel-bus';
  box(bus, [1.05, .43, .4], [0, 0, 0], 'yellow');
  box(bus, [.98, .045, .41], [0, .235, 0], 'white');
  box(bus, [.03, .22, .32], [.535, .06, 0], 'glass');
  for (const z of [-.205, .205]) for (const x of [-.35, -.12, .11, .34])
    box(bus, [.18, .19, .014], [x, .07, z], 'glass');
  box(bus, [1.03, .045, .413], [0, -.12, 0], 'teal');
  for (const z of [-.13, .13]) box(bus, [.025, .055, .075], [.54, -.12, z], 'white');
  wheels(bus, [-.32, .32], .215); return bus;
}
export function createTrain() {
  const train = new THREE.Group(); train.name = 'travel-train';
  for (const x of [-.42, .42]) {
    box(train, [.77, .37, .35], [x, 0, 0], 'white');
    box(train, [.77, .07, .36], [x, -.12, 0], 'teal');
    box(train, [.72, .055, .31], [x, .21, 0], 'navy');
    for (const z of [-.18, .18]) for (const offset of [-.23, 0, .23])
      box(train, [.16, .15, .014], [x + offset, .035, z], 'glass');
  }
  box(train, [.055, .19, .26], [.825, .025, 0], 'glass');
  box(train, [.06, .055, .27], [.825, -.105, 0], 'yellow');
  box(train, [.18, .07, .1], [0, -.1, 0], 'navy');
  wheels(train, [-.67, -.19, .19, .67], .19); return train;
}
export function createFlight() {
  const plane = new THREE.Group(); plane.name = 'travel-flight';
  const body = new THREE.Mesh(new THREE.CapsuleGeometry(.06, .36, 4, 10), materials.white);
  body.rotation.z = -Math.PI / 2; plane.add(body);
  box(plane, [.15, .025, .48], [0, 0, 0], 'white');
  box(plane, [.09, .025, .21], [-.19, .015, 0], 'teal');
  box(plane, [.075, .14, .02], [-.19, .08, 0], 'teal');
  box(plane, [.09, .015, .075], [.12, .055, 0], 'glass');
  plane.scale.setScalar(1.55); return plane;
}
