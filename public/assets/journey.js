import * as THREE from './vendor/three.module.js';
import {createBus,createFlight} from './travel-models.js';
const holder=document.querySelector('#journey-canvas');
const reduced=matchMedia('(prefers-reduced-motion: reduce)');
if(holder&&!reduced.matches&&!navigator.connection?.saveData) boot();
function boot(){
 let renderer;try{renderer=new THREE.WebGLRenderer({alpha:true,antialias:true,powerPreference:'low-power'});}catch{return;}
 renderer.setPixelRatio(Math.min(devicePixelRatio,1.5));holder.append(renderer.domElement);
 const scene=new THREE.Scene(),camera=new THREE.PerspectiveCamera(36,1,.1,80);camera.position.set(10,9,13);camera.lookAt(0,0,0);
 scene.add(new THREE.HemisphereLight(0xffffff,0x537462,2.8));const light=new THREE.DirectionalLight(0xffefd2,3.5);light.position.set(-3,9,4);scene.add(light);
 const world=new THREE.Group();scene.add(world);
 const mat=color=>new THREE.MeshStandardMaterial({color,roughness:.9,flatShading:true});
 const green=mat(0x558b67),pale=mat(0x89b594),dark=mat(0x2d634b),cream=mat(0xf3ead1),roadmat=mat(0x36574b);
 function mesh(geo,material,x,y,z){const m=new THREE.Mesh(geo,material);m.position.set(x,y,z);world.add(m);return m;}
 const theme=holder.dataset.theme||'road';
 const floats=[],rings=[];
 let roadPath,roadBus;
 if(theme==='road'){
 mesh(new THREE.CylinderGeometry(6.8,6, .7,64),pale,0,-.65,0);
 for(const [x,z,h,r] of [[-3,-2,3.6,2],[-.5,-3.3,4.5,2.1],[2.4,-2.8,3.2,1.8],[4,-.5,2.2,1.4],[-4,1,1.6,1.3]]){
  mesh(new THREE.ConeGeometry(r,h,5),green,x,h/2-.25,z);
  mesh(new THREE.ConeGeometry(r*.27,h*.27,5),cream,x,h-.25-h*.135,z);
 }
 const points=[[-1,0,5.6],[2,0,4],[2.8,0,2.7],[-2.5,0,1.4],[-2.7,0,.2],[1.6,0,-.5],[1.5,.05,-1.8],[.1,.15,-2.8]].map(p=>new THREE.Vector3(...p));
 const path=new THREE.CatmullRomCurve3(points);
 roadPath=path;
 // Flat ribbons keep the road connected to the island, including the bends.
 function ribbon(width,height,material){const vertices=[],indices=[];for(let i=0;i<=160;i++){const p=path.getPointAt(i/160),t=path.getTangentAt(i/160),n=new THREE.Vector3(-t.z,0,t.x).normalize().multiplyScalar(width/2);vertices.push(p.x+n.x,p.y+height,p.z+n.z,p.x-n.x,p.y+height,p.z-n.z);if(i<160){const a=i*2;indices.push(a,a+2,a+1,a+1,a+2,a+3);}}const g=new THREE.BufferGeometry();g.setAttribute('position',new THREE.Float32BufferAttribute(vertices,3));g.setIndex(indices);g.computeVertexNormals();material.side=THREE.DoubleSide;world.add(new THREE.Mesh(g,material));}
 ribbon(.91,.015,cream);ribbon(.72,.025,roadmat);
 for(let i=0;i<65;i++){const t=i/65,p=path.getPointAt(t),tangent=path.getTangentAt(t);const dash=mesh(new THREE.BoxGeometry(.035,.012,.12),cream,p.x,p.y+.04,p.z);dash.rotation.y=Math.atan2(tangent.x,tangent.z);}
 const treePositions=[[-4,2],[-3.7,2.5],[-4,-.8],[3.8,1.6],[4.4,1.1],[3.2,-1.3],[-1.5,-1.4],[.4,3.2],[.9,3.6],[-3.2,-2.8]];
 treePositions.forEach(([x,z],i)=>{const h=.55+i%3*.12;mesh(new THREE.CylinderGeometry(.045,.06,.45,6),mat(0x84694b),x,.03,z);mesh(new THREE.ConeGeometry(.28,h,6),dark,x,.3+h/2,z);});
 const bus=createBus();bus.scale.setScalar(.6);world.add(bus);
 roadBus=bus;
 }
 if(theme==='islands'){
  mesh(new THREE.CylinderGeometry(6.8,6.8,.35,64),mat(0x50b9c7),0,-.6,0);
  for(const [x,z,r] of [[-3,0,1.7],[.6,-2,2],[3,2,1.4]]){
   mesh(new THREE.CylinderGeometry(r,r*.85,.35,32),mat(0xf3dfae),x,-.25,z);
   mesh(new THREE.ConeGeometry(r*.65,1.2,7),green,x,.45,z-.2);
   mesh(new THREE.CylinderGeometry(.07,.11,1.6,8),mat(0x927048),x+.6,.65,z+.5);
   for(let i=0;i<5;i++){const leaf=mesh(new THREE.SphereGeometry(.5,10,6),dark,x+.6+Math.cos(i*1.26)*.4,1.5,z+.5+Math.sin(i*1.26)*.4);leaf.scale.set(1,.15,.4);leaf.rotation.y=-i*1.26;}
  }
  for(let i=0;i<5;i++){const ring=mesh(new THREE.TorusGeometry(1.9+i*.12,.015,4,60),cream,-3,-.05,0);ring.rotation.x=Math.PI/2;rings.push(ring);}
  const boat=new THREE.Group();const hull=new THREE.Mesh(new THREE.BoxGeometry(1.1,.25,.4),cream);boat.add(hull);const sail=new THREE.Mesh(new THREE.ConeGeometry(.5,1.2,3),mat(0xf2a36f));sail.position.y=.7;boat.add(sail);world.add(boat);floats.push(boat);
 }
 if(theme==='camp'){
  mesh(new THREE.CylinderGeometry(6.4,5.5,.65,48),dark,0,-.65,0);
  for(const [x,z,h] of [[-3,-2,4],[0,-3,5],[3,-2,3.4]]){mesh(new THREE.ConeGeometry(2,h,5),mat(0x657b98),x,h/2-.3,z);mesh(new THREE.ConeGeometry(.5,h*.25,5),cream,x,h*.875-.3,z);}
  const tent=mesh(new THREE.CylinderGeometry(0,1.5,1.9,3),mat(0xe8aa6a),0,.65,1);tent.rotation.y=Math.PI/6;
  mesh(new THREE.ConeGeometry(.5,1.1,3),mat(0x283b50),0,.27,2);
  for(let i=0;i<8;i++){const x=-4+i*1.1,z=-.3-Math.sin(i)*.5;mesh(new THREE.ConeGeometry(.4,1.6,6),green,x,.5,z);}
  const fire=mesh(new THREE.ConeGeometry(.24,.7,7),mat(0xffbd65),2,.2,2);floats.push(fire);
  for(let i=0;i<22;i++)mesh(new THREE.SphereGeometry(.035,6,4),cream,Math.sin(i*7)*6,3+(i%5)*.55,Math.cos(i*3)*4);
 }
 if(theme==='flight'){
  mesh(new THREE.CylinderGeometry(3.3,3.3,.35,64),mat(0x709bbc),0,-.6,0);
  const plane=createFlight();plane.scale.setScalar(5);plane.position.set(0,2,0);plane.rotation.y=-.4;world.add(plane);floats.push(plane);
  for(let i=0;i<3;i++){const arc=mesh(new THREE.TorusGeometry(3.6+i*.65,.025,6,80,Math.PI*1.6),i===1?cream:mat(0x85b7ce),0,1,0);arc.rotation.set(.6+i*.25,.3,i);rings.push(arc);}
 }
 if(theme==='gallery'){
  const colours=[0xe9b486,0x7cb8bf,0xa3b58b,0xb7a1c5];
  for(let i=0;i<4;i++){const frame=new THREE.Group();const border=new THREE.Mesh(new THREE.BoxGeometry(2.6,3,.14),cream);frame.add(border);const photo=new THREE.Mesh(new THREE.PlaneGeometry(2.25,2.2),mat(colours[i]));photo.position.set(0,.16,.08);frame.add(photo);const peak=new THREE.Mesh(new THREE.ConeGeometry(.9,1.5,3),mat(i%2?0x396e66:0x697da0));peak.position.set(0,.1,.3);peak.scale.z=.1;frame.add(peak);const sun=new THREE.Mesh(new THREE.SphereGeometry(.25,16,10),mat(0xffda93));sun.position.set(.65,.85,.25);frame.add(sun);frame.position.set((i-1.5)*2.7,1+(i%2)*.8,(i%2)*1.2);frame.rotation.set(-.1,(i-1.5)*-.16,(i-1.5)*.09);world.add(frame);floats.push(frame);}
 }
 const cloudmat=mat(0xffffff);for(let i=0;i<4;i++){const cloud=new THREE.Group();for(let j=0;j<3;j++){const puff=new THREE.Mesh(new THREE.SphereGeometry(.27+j*.06,12,8),cloudmat);puff.position.x=j*.32;cloud.add(puff);}cloud.position.set(-5+i*3,4.7+i%2*.65,-2-i%2);scene.add(cloud);}
 let visible=false,frame=0,last=0,time=0,pointer=0;
 const resize=()=>{const {width,height}=holder.getBoundingClientRect();renderer.setSize(width,height);camera.aspect=width/height;camera.position.set(width<600?13:10,width<600?12:9,width<600?18:13);camera.lookAt(0,.7,0);camera.updateProjectionMatrix();};new ResizeObserver(resize).observe(holder);resize();
 const render=now=>{frame=0;if(!visible||document.hidden||reduced.matches)return;frame=requestAnimationFrame(render);if(now-last<33)return;time+=Math.min((now-last)/1000,.08);last=now;
 if(theme==='road'){const path=roadPath,t=(time*.035)%1,p=path.getPointAt(t),tangent=path.getTangentAt(t);roadBus.position.copy(p);roadBus.position.y+=.23;roadBus.rotation.y=Math.atan2(-tangent.z,tangent.x);}
 floats.forEach((object,i)=>{if(!object.userData.base)object.userData.base=object.position.clone();const base=object.userData.base;object.position.y=base.y+Math.sin(time*(theme==='camp'?4:.7)+i)*.15;if(theme==='islands'){object.position.x=Math.sin(time*.3)*3;object.position.z=3+Math.cos(time*.3);object.rotation.y=-time*.3;}if(theme==='gallery')object.rotation.z=Math.sin(time*.5+i)*.08;if(theme==='flight')object.rotation.z=Math.sin(time*.7)*.07;});
 rings.forEach((r,i)=>{if(theme==='flight')r.rotation.z=time*.12*(i%2?1:-1);else r.scale.setScalar(1+Math.sin(time+i)*.04);});
 world.rotation.y+=(pointer*.18-world.rotation.y)*.035;renderer.render(scene,camera);holder.parentElement.classList.add('is-rendered');};
 const resume=()=>{if(visible&&!document.hidden&&!reduced.matches&&!frame){last=performance.now();frame=requestAnimationFrame(render);}};
 new IntersectionObserver(entries=>{visible=entries[0].isIntersecting;resume();}).observe(holder);
 document.addEventListener('visibilitychange',resume);reduced.addEventListener('change',()=>{holder.parentElement.classList.toggle('is-rendered',!reduced.matches);resume();});holder.parentElement.addEventListener('pointermove',e=>{const r=holder.getBoundingClientRect();pointer=(e.clientX-r.left)/r.width-.5;});holder.parentElement.addEventListener('pointerleave',()=>pointer=0);
 renderer.domElement.addEventListener('webglcontextlost',e=>{e.preventDefault();visible=false;cancelAnimationFrame(frame);frame=0;holder.parentElement.classList.remove('is-rendered');});
}
