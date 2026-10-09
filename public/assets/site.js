(() => {
'use strict';
const scriptURL=document.currentScript.src;
const assets=new URL('./',scriptURL);
const reduced=window.matchMedia('(prefers-reduced-motion: reduce)');
const menu=document.querySelector('.menu-toggle'),nav=document.querySelector('#navigation');
menu?.addEventListener('click',()=>{const open=menu.getAttribute('aria-expanded')!=='true';menu.setAttribute('aria-expanded',String(open));nav.classList.toggle('open',open);});
nav?.addEventListener('keydown',e=>{if(e.key==='Escape'){nav.classList.remove('open');menu.setAttribute('aria-expanded','false');menu.focus();}});
const dialog=document.querySelector('#gallery-dialog');let previousFocus;
document.querySelectorAll('.gallery-open').forEach(button=>button.addEventListener('click',()=>{if(!dialog?.showModal)return;previousFocus=button;dialog.querySelector('img').src=button.dataset.src;dialog.querySelector('img').alt=button.dataset.alt;dialog.querySelector('p').textContent=button.dataset.alt;dialog.showModal();dialog.querySelector('button').focus();}));
dialog?.querySelector('button').addEventListener('click',()=>dialog.close());dialog?.addEventListener('close',()=>previousFocus?.focus());dialog?.addEventListener('click',e=>{if(e.target===dialog){const r=dialog.getBoundingClientRect();if(e.clientX<r.left||e.clientX>r.right||e.clientY<r.top||e.clientY>r.bottom)dialog.close();}});
const validPhone=n=>/^[1-9][0-9]{7,14}$/.test(n||'');
const openChat=(number,lines)=>{if(!validPhone(number))return;window.location.assign('https://wa.me/'+number+'?text='+encodeURIComponent(lines.join('\n')));};
document.querySelectorAll('.package-booking [name=name],.package-booking [name=departure_city]').forEach(input=>input.addEventListener('input',()=>input.setCustomValidity('')));
document.querySelectorAll('.package-booking').forEach(form=>form.addEventListener('submit',event=>{event.preventDefault();if(!form.reportValidity()||!validPhone(form.dataset.number))return;const data=new FormData(form);for(const key of ['name','departure_city']){const input=form.elements.namedItem(key);if(!String(data.get(key)||'').trim()){input.setCustomValidity('Please enter '+(key==='name'?'your name.':'your departure city.'));input.reportValidity();return;}}const lines=['Hello The Dream Holidays!','I would like to book this package.','','Package: '+form.dataset.name,'Duration: '+form.dataset.duration];[['name','Name'],['departure_city','Departure City'],['travel_date','Travel Date'],['adults','Adults'],['children','Children'],['message','Message']].forEach(([key,label])=>{const value=String(data.get(key)||'').trim();if(value!=='')lines.push(label+': '+value);});lines.push('Package Link: '+form.dataset.link,'','Please confirm availability and share the quotation.');openChat(form.dataset.number,lines);}));
document.querySelectorAll('.vehicle-booking').forEach(form=>form.addEventListener('submit',event=>{event.preventDefault();if(!form.reportValidity()||!validPhone(form.dataset.number))return;const data=new FormData(form);const lines=['Hello The Dream Holidays!','I would like to book a vehicle.','','Vehicle: '+form.dataset.name,'Category: '+form.dataset.category,'Seating: '+form.dataset.seating+' passengers','Air conditioning: '+form.dataset.ac];[['travel_date','Travel Date'],['pickup','Pickup Location'],['travellers','Travellers']].forEach(([key,label])=>{const value=String(data.get(key)||'').trim();if(value!=='')lines.push(label+': '+value);});lines.push('','Please confirm availability, rental terms and share the quotation.');openChat(form.dataset.number,lines);}));
document.querySelectorAll('.enquiry-form').forEach(form=>form.addEventListener('submit',()=>{if(!form.checkValidity())return;const button=form.querySelector('button[type=submit]');button.disabled=true;button.textContent='Saving enquiry…';}));
window.addEventListener('pageshow',()=>document.querySelectorAll('.enquiry-form button[type=submit]').forEach(b=>{b.disabled=false;b.textContent='Send Enquiry';}));
const loadScript=src=>new Promise((resolve,reject)=>{const s=document.createElement('script');s.src=new URL(src,assets).href;s.onload=resolve;s.onerror=reject;document.head.append(s);});
if(!reduced.matches){loadScript('vendor/gsap.min.js').then(()=>import(new URL('scroll-animations.js',assets).href)).then(module=>module.initScrollAnimations(window.gsap,reduced)).catch(()=>{});}
if(!reduced.matches&&matchMedia('(hover:hover) and (pointer:fine)').matches)document.querySelectorAll('.tilt').forEach(card=>{card.addEventListener('pointermove',event=>{if(reduced.matches)return;const r=card.getBoundingClientRect();card.style.transform=`perspective(900px) rotateX(${-(event.clientY-r.top-r.height/2)/r.height*3}deg) rotateY(${(event.clientX-r.left-r.width/2)/r.width*3}deg)`;});card.addEventListener('pointerleave',()=>card.style.transform='');});
const holder=document.querySelector('#globe');if(!holder||reduced.matches||navigator.connection?.saveData)return;
const lowPower=innerWidth<760||(navigator.hardwareConcurrency&&navigator.hardwareConcurrency<=4);
// No blocking load: the original static illustration remains until the first successful render.
Promise.all([import(new URL('vendor/three.module.js',assets).href),import(new URL('travel-models.js',assets).href)]).then(([THREE,models])=>{
 if(reduced.matches)return;
 let renderer;try{renderer=new THREE.WebGLRenderer({alpha:true,antialias:!lowPower,powerPreference:'low-power'});}catch{return;}
 renderer.setPixelRatio(Math.min(devicePixelRatio||1,lowPower?1:1.6));holder.append(renderer.domElement);
 const scene=new THREE.Scene(),camera=new THREE.PerspectiveCamera(40,1,.1,100);camera.position.set(0,0,7.2);scene.add(new THREE.AmbientLight(0xffffff,2.1));const sun=new THREE.DirectionalLight(0xffffff,3);sun.position.set(-3,4,5);scene.add(sun);
 const world=new THREE.Group();world.rotation.z=.16;scene.add(world);
 const globe=new THREE.Mesh(new THREE.SphereGeometry(1.85,lowPower?32:64,lowPower?24:48),new THREE.MeshStandardMaterial({color:0x229d9f,roughness:.82}));world.add(globe);
 const wire=new THREE.Mesh(new THREE.SphereGeometry(1.86,24,12),new THREE.MeshBasicMaterial({color:0xc1ece2,wireframe:true,transparent:true,opacity:.18}));world.add(wire);
 const latLng=(lat,lng,r=1.87)=>{const phi=(90-lat)*Math.PI/180,theta=(lng+180)*Math.PI/180;return new THREE.Vector3(-r*Math.sin(phi)*Math.cos(theta),r*Math.cos(phi),r*Math.sin(phi)*Math.sin(theta));};
 // Original stylized land silhouettes, not a geographic navigation map.
 const continents=[[[68,-160],[65,-120],[50,-65],[23,-80],[8,-90],[25,-115]],[[10,-78],[-8,-40],[-25,-45],[-55,-70],[-15,-82]],[[68,-10],[68,90],[50,140],[10,100],[25,70],[10,40],[35,35],[35,-10]],[[35,-17],[30,30],[5,43],[-35,18],[-10,-5]],[[-12,112],[-12,145],[-38,150],[-35,115]]];
 continents.forEach(points=>{const center=points.reduce((v,[lat,lng])=>v.add(latLng(lat,lng)),new THREE.Vector3()).normalize().multiplyScalar(1.87);const vertices=[];points.forEach((point,i)=>{const a=latLng(...point),b=latLng(...points[(i+1)%points.length]);const steps=lowPower?8:14;const at=(u,v)=>center.clone().multiplyScalar(1-u-v).addScaledVector(a,u).addScaledVector(b,v).normalize().multiplyScalar(1.873);for(let u=0;u<steps;u++)for(let v=0;v<steps-u;v++){vertices.push(...at(u/steps,v/steps).toArray(),...at((u+1)/steps,v/steps).toArray(),...at(u/steps,(v+1)/steps).toArray());if(u+v<steps-1)vertices.push(...at((u+1)/steps,v/steps).toArray(),...at((u+1)/steps,(v+1)/steps).toArray(),...at(u/steps,(v+1)/steps).toArray());}});const geometry=new THREE.BufferGeometry();geometry.setAttribute('position',new THREE.Float32BufferAttribute(vertices,3));geometry.computeVertexNormals();const land=new THREE.Mesh(geometry,new THREE.MeshStandardMaterial({color:0xc4e7bf,side:THREE.DoubleSide,roughness:1}));world.add(land);});
 const markers=[];let data=[];try{data=JSON.parse(document.querySelector('#globe-data')?.textContent||'[]');}catch{}
 data.forEach(item=>{const marker=new THREE.Mesh(new THREE.SphereGeometry(.047,12,8),new THREE.MeshBasicMaterial({color:0xffd14c}));marker.position.copy(latLng(item.lat,item.lng,1.92));marker.userData=item;world.add(marker);markers.push(marker);});
 const path=new THREE.CatmullRomCurve3([new THREE.Vector3(-2.3,-.6,0),new THREE.Vector3(-1,1.9,1.1),new THREE.Vector3(1.8,1.4,.4),new THREE.Vector3(2.3,-.7,-.4),new THREE.Vector3(-.8,-1.9,-.9)],true,'catmullrom',.45);
 scene.add(new THREE.Mesh(new THREE.TubeGeometry(path,90,.012,6,true),new THREE.MeshBasicMaterial({color:0xd9b236})));
 const plane=models.createFlight();scene.add(plane);
 const bus=models.createBus(),train=models.createTrain();scene.add(bus,train);
 bus.rotation.set(.18,-.45,-.06);train.rotation.set(.16,.4,.04);
 let arrival=0;
 holder.dataset.transport='flight bus train';
 if(!lowPower){for(let i=0;i<5;i++){const clouds=new THREE.Group();for(let j=0;j<3;j++){const puff=new THREE.Mesh(new THREE.SphereGeometry(.12+j*.025,12,8),new THREE.MeshStandardMaterial({color:0xffffff,transparent:true,opacity:.5}));puff.position.x=j*.14;clouds.add(puff);}clouds.position.copy(latLng(i*18-30,i*64-140,2.06));world.add(clouds);}}
 let visible=true,frame=0,last=0,angle=0,progress=0;const clock=new THREE.Clock();
 const resize=()=>{const rect=holder.getBoundingClientRect();if(!rect.width||!rect.height)return;renderer.setSize(rect.width,rect.height);camera.aspect=rect.width/rect.height;camera.updateProjectionMatrix();};resize();const resizeObserver=new ResizeObserver(resize);resizeObserver.observe(holder);
 const tick=time=>{frame=0;if(!visible||document.hidden||reduced.matches)return;frame=requestAnimationFrame(tick);if(time-last<(lowPower?50:30))return;last=time;const dt=Math.min(clock.getDelta(),.1);angle+=dt*.055;progress=(progress+dt*.035)%1;arrival+=dt;const enter=1-Math.pow(1-Math.min(arrival/1.3,1),3);world.rotation.y=angle;plane.position.copy(path.getPointAt(progress));plane.quaternion.setFromUnitVectors(new THREE.Vector3(1,0,0),path.getTangentAt(progress));bus.position.set(-1.2-Math.cos(arrival*.6)*.08,-1.35+Math.sin(arrival*1.1)*.055,2.15);train.position.set(1.08+Math.sin(arrival*.5)*.1,-1.18+Math.cos(arrival*.9)*.05,1.95);bus.scale.setScalar(.7*enter);train.scale.setScalar(.55*enter);renderer.render(scene,camera);holder.parentElement.classList.add('has-webgl');};
 const resume=()=>{if(!frame&&visible&&!document.hidden&&!reduced.matches){clock.getDelta();frame=requestAnimationFrame(tick);}};
 const observer=new IntersectionObserver(entries=>{visible=entries[0].isIntersecting;if(!visible&&frame){cancelAnimationFrame(frame);frame=0;}resume();});observer.observe(holder);
 document.addEventListener('visibilitychange',()=>{if(document.hidden&&frame){cancelAnimationFrame(frame);frame=0;}resume();});
 reduced.addEventListener('change',()=>{if(reduced.matches){if(frame)cancelAnimationFrame(frame);frame=0;holder.parentElement.classList.remove('has-webgl');}else resume();});
 renderer.domElement.addEventListener('webglcontextlost',event=>{event.preventDefault();visible=false;if(frame)cancelAnimationFrame(frame);frame=0;holder.parentElement.classList.remove('has-webgl');});
 const raycaster=new THREE.Raycaster();holder.addEventListener('click',event=>{const rect=holder.getBoundingClientRect();raycaster.setFromCamera(new THREE.Vector2((event.clientX-rect.left)/rect.width*2-1,-(event.clientY-rect.top)/rect.height*2+1),camera);const hits=raycaster.intersectObjects(markers);if(hits.length){const hit=hits[0];const occluded=raycaster.intersectObject(globe)[0];if(!occluded||hit.distance<occluded.distance)location.assign(hit.object.userData.url);}});
 resume();
}).catch(()=>{});
})();
