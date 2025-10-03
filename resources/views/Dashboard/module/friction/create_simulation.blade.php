<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Physics Editor — Sticky Objects & Slopes (Rapi)</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
  :root{
    --ui-height:270px;
    --bg:#f6f7fb;
    --card:#ffffff;
    --muted:#6b7280;
    --accent:#2563eb;
    --glass: rgba(255,255,255,0.98);
    --radius:10px;
    --gap:12px;
    --pad:12px;
    --shadow: 0 8px 20px rgba(0,0,0,0.06);
  }
  *{box-sizing:border-box}
  html,body{height:100%;margin:0;font-family:Inter,system-ui,Arial;color:#111;background:var(--bg)}

  /* canvas container = full screen */
  #container{
    position:fixed;
    inset:0;
    width:100vw;
    height:100vh;
    background:var(--bg);
    overflow:hidden;
    z-index:0; /* canvas always behind */
  }

  /* top UI bar overlay */
  .ui{
    position:fixed;
    top:0; left:0; right:0;
    height:var(--ui-height);
    background:var(--glass);
    border-radius:0 0 var(--radius) var(--radius);
    padding:var(--pad);
    box-shadow:var(--shadow);
    z-index:10;
    display:flex;
    gap:var(--gap);
    overflow-x:auto;
    overflow-y:hidden;
    align-items:flex-start;
  }

  h3{margin:0;font-size:15px}
.actions-panel {
  display: grid;
  grid-template-columns: 1fr 1fr; /* 2 kolom */
  gap: 10px;
  background: var(--card);
  padding: 10px;
  border-radius: 8px;
  box-shadow: 0 1px 0 rgba(16,24,40,0.02);
  min-width: 300px;
}
.actions-panel > div:first-child {
  grid-column: 1 / 3; /* judul span 2 kolom */
  font-weight: 600;
}
.actions-panel .compact {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}
  label{font-size:13px;color:#111;min-width:70px}
  .field{
    display:flex;
    gap:6px;
    align-items:center;
    grid-column:1/3;
  }
  input[type=number],select{
    flex:1;
    padding:6px 12px;
    border-radius:8px;
    border:1px solid #e6e7eb;
    font-size:13px;
    width: 80px;   /* atur lebar custom */
    flex: unset;   /* biar gak dipaksa full */
  }
   button {
    padding: 6px 10px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    background: var(--accent);
    color: #fff;
    font-size: 13px;
    width: 100%; /* biar rapih di kolom kanan */
  }
  button.ghost {
    background: #eef2ff;
    color: #0b1220;
    border: 1px solid #e6e7eb;
  }
  .compact{
    grid-column:1/3;
    display:flex;
    flex-wrap:wrap;
    gap:6px;
  }
  .muted{color:var(--muted);font-size:13px}
  #selectionInfo{font-size:13px;color:#374151;grid-column:1/3}

  /* footer hint */
  #hint{
    position:fixed;
    right:12px;bottom:12px;
    background:rgba(0,0,0,0.6);
    color:#fff;padding:8px 10px;
    border-radius:8px;
    font-size:13px;
    z-index:10;
  }
</style>
</head>
<body>
<div id="container"></div>

<div class="ui" id="ui">

  <!-- PANEL OBJECT -->
  <div class="panel">
    <div>Object — spawn</div>
    <div class="field"><label>Tipe</label><select id="typeSelect"><option value="box">Box</option><option value="circle">Circle</option></select></div>
    <div class="field"><label>Massa (kg)</label><input id="massInput" type="number" step="0.1" value="2" min="0.1"/></div>
    <div class="field" id="sizeBoxRow"><label>Ukuran (W×H)</label><input id="widthInput" type="number" value="60" min="8"/><input id="heightInput" type="number" value="60" min="8"/></div>
    <div class="field" id="radiusRow" style="display:none"><label>Radius</label><input id="radiusInput" type="number" value="30" min="4"/></div>
    <div class="field"><label>Friction</label><input id="frictionInput" type="number" step="0.05" value="0.3" min="0" max="1"/></div>
    <div class="field"><label>FrictionStatic</label><input id="fricStaticInput" type="number" step="0.05" value="0.5" min="0" max="1"/></div>
    <div class="field"><label>Restitution</label><input id="restitutionInput" type="number" step="0.01" value="0.0" min="0" max="1"/></div>
  </div>

  <!-- PANEL SLOPE -->
  <div class="panel">
    <div>Slope</div>
    <div class="field"><label>Lebar</label><input id="sWidth" type="number" value="300"/><label>Tinggi</label><input id="sHeight" type="number" value="20"/></div>
    <div class="field"><label>Angle (deg)</label><input id="sAngle" type="number" step="0.5" value="0.3"/></div>
    <div class="field"><label>Friction</label><input id="sFriction" type="number" step="0.05" value="0.8"/></div>
    
  </div>

  <!-- PANEL ACTIONS -->
<div class="panel actions-panel">
  <div>Actions</div>

  <div class="compact">
    <button id="toggleSticky" class="ghost">Toggle Sticky (selected)</button>
  </div>

  <div class="compact">
    <button id="deleteSelected" class="ghost">Delete</button>
    <button id="snapGrid" class="ghost">Snap Grid</button>
  </div>

  <div class="compact">
    <button id="exportBtn" class="ghost">Export JSON</button>
    <button id="resetBtn" class="ghost">Reset Semua</button>
  </div>
</div>
<div class="panel actions-panel">
  <div>Spawn Object Button</div>
 <div class="compact">
    <button id="spawnCenterBtn">Spawn Tengah</button>
    <button id="spawnClickBtn" class="ghost">Spawn di Klik: OFF</button>
  </div>
</div>
<div class="panel actions-panel">
  <div>Spawn Slope Button</div>
  <div class="compact">
    <button id="spawnSlopeLicin">Slope Licin</button>
    <button id="spawnSlopeDatar">Slope Datar</button>
    <button id="spawnSlopeBergelombang">Slope Bergelombang</button>
  </div>

  <div class="compact">
    <button id="spawnSlopeCenter">Spawn Slope Tengah</button>
    <button id="spawnSlopeClick" class="ghost">Spawn Slope Klik: OFF</button>
  </div>
  <div id="selectionInfo" style="grid-column:1/3">Selected: —</div>
</div>

</div>

<div id="hint">Klik canvas untuk interaksi (toggle spawn mode di UI)</div>

<!-- Matter.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/matter-js/0.20.0/matter.min.js"></script>
<script>
(function(){
  // Destructure Matter modules for convenience
  const { Engine, Render, Runner, Bodies, Composite, Body, Events, Mouse, MouseConstraint, World, Query } = Matter;

  // ---------- setup ----------
  const container = document.getElementById('container');
  const w = () => window.innerWidth;
  const h = () => window.innerHeight;

  const engine = Engine.create();
  const world = engine.world;
  world.gravity.y = 1;

  const render = Render.create({ element: container, engine: engine, options: { width: w(), height: h(), wireframes: false, background: '#f6f7fb' } });
  Render.run(render);
  const runner = Runner.create(); Runner.run(runner, engine);

  // invisible bounds
  const wallThickness = 300;
  const ground = Bodies.rectangle(w()/2, h()-10, w(), 40, {
    isStatic: true,
    render: { visible: false }
  });
  const leftWall = Bodies.rectangle(-wallThickness/2, h()/2, wallThickness, h(), { isStatic:true, render:{ visible:false }});
  const rightWall = Bodies.rectangle(w()+wallThickness/2, h()/2, wallThickness, h(), { isStatic:true, render:{ visible:false }});
  Composite.add(world, [ground,leftWall,rightWall]);

  // ---------- state ----------
  let userBodies = [];
  let slopeBodies = [];
  let selected = null;
  let spawnObjectOnClick = false;
  let spawnSlopeOnClick = false;

  // ---------- UI refs ----------
  const typeSelect = document.getElementById('typeSelect');
  const massInput = document.getElementById('massInput');
  const widthInput = document.getElementById('widthInput');
  const heightInput = document.getElementById('heightInput');
  const radiusInput = document.getElementById('radiusInput');
  const radiusRow = document.getElementById('radiusRow');
  const sizeBoxRow = document.getElementById('sizeBoxRow');
  const frictionInput = document.getElementById('frictionInput');
  const fricStaticInput = document.getElementById('fricStaticInput');
  const restitutionInput = document.getElementById('restitutionInput');
  const spawnCenterBtn = document.getElementById('spawnCenterBtn');
  const spawnClickBtn = document.getElementById('spawnClickBtn');

  const sWidth = document.getElementById('sWidth');
  const sHeight = document.getElementById('sHeight');
  const sAngle = document.getElementById('sAngle');
  const sFriction = document.getElementById('sFriction');
  const spawnSlopeCenter = document.getElementById('spawnSlopeCenter');
  const spawnSlopeClick = document.getElementById('spawnSlopeClick');

  const toggleStickyBtn = document.getElementById('toggleSticky');
  const deleteSelectedBtn = document.getElementById('deleteSelected');
  const snapGridBtn = document.getElementById('snapGrid');
  const exportBtn = document.getElementById('exportBtn');
  const resetBtn = document.getElementById('resetBtn');
  const selectionInfo = document.getElementById('selectionInfo');

  // update size UI
  function updateSizeUI(){
    if(typeSelect.value === 'circle'){
      radiusRow.style.display = '';
      sizeBoxRow.style.display = 'none';
    } else {
      radiusRow.style.display = 'none';
      sizeBoxRow.style.display = '';
    }
  }
  typeSelect.addEventListener('change', updateSizeUI);
  updateSizeUI();

  // ---------- factories ----------
  function createUserBody(params){
    let body;
    if(params.type === 'circle'){
      body = Bodies.circle(params.x, params.y, params.radius, {
        friction: params.friction,
        frictionStatic: params.frictionStatic,
        restitution: params.restitution,
        density: Math.max(0.0001, params.mass / (Math.PI * params.radius * params.radius))
      });
    } else {
      body = Bodies.rectangle(params.x, params.y, params.width, params.height, {
        friction: params.friction,
        frictionStatic: params.frictionStatic,
        restitution: params.restitution,
        density: Math.max(0.0001, params.mass / Math.max(1, params.width * params.height))
      });
    }

    body.isUserObject = true;
    body.massValue = params.mass;
    body.isSticky = false;
    body.labelText = params.mass + 'kg';
    return body;
  }

  function createHalfCircle(x, y, radius, options = {}, protrusionFraction = 1.0) {
    const segments = 30; const vertices = [];
    protrusionFraction = Math.max(0.01, Math.min(1.0, protrusionFraction));
    const fullHeight = radius; const embedDepth = fullHeight * (1 - protrusionFraction);

    for (let i = 0; i <= segments; i++){
      const angle = (Math.PI * i / segments);
      const vx = radius * Math.cos(angle);
      const vy = -radius * Math.sin(angle);
      vertices.push({ x: vx, y: vy });
    }
    vertices.push({ x: -radius, y: 0 }); vertices.push({ x: radius, y: 0 });
    for (let i = 0; i < vertices.length; i++) vertices[i].y += embedDepth;
    for (let i = 0; i < vertices.length; i++) { vertices[i].x += x; vertices[i].y += y; }
    const adjustedCenterY = y + (embedDepth / 2);
    return Bodies.fromVertices(x, adjustedCenterY, [vertices], { isStatic:false, friction:0.6, restitution:0.1, ...options }, true);
  }

  function createSlope(params){
    const body = Bodies.rectangle(params.x, params.y, params.width, params.height, { isStatic:true, friction:params.friction, render:{ fillStyle: params.color || '#444' } });
    Body.rotate(body, params.angle);
    body.isSlope = true; body.isStatic = true; body.slopeProps = { angle: params.angle, width: params.width, height: params.height, surfaceType: params.surfaceType || 'datar' };
    return body;
  }

  // Spawn slope (compound: main + optional bumps)
  function spawnSlopeAt(x, y, surfaceType = 'datar'){
    const widthVal = parseFloat(sWidth.value) || 300; const heightVal = parseFloat(sHeight.value) || 20; const angleDeg = parseFloat(sAngle.value) || 0; const angleRad = angleDeg * Math.PI / 180;

    let friction = 0.8; let restitution = 0; let color = '#607d8b';
    if(surfaceType === 'licin'){ friction = 0.05; restitution = 0.2; color = '#03a9f4'; }
    if(surfaceType === 'bergelombang'){ friction = 0.6; restitution = 0.1; color = '#8B4513'; }

    // main part
    const slopeMain = Bodies.rectangle(0, 0, widthVal, heightVal, { friction, restitution, render:{ fillStyle: color } });
    const parts = [slopeMain];

    if(surfaceType === 'bergelombang'){
      const numBumps = 10; const bumpRadius = 17;
      for(let i=0;i<numBumps;i++){
        const offsetX = (i - (numBumps-1)/2) * (widthVal/numBumps);
        const offsetY = -heightVal/2 - bumpRadius/2;
        const protrusionFraction = 0.165;
        const bump = createHalfCircle(offsetX, offsetY, bumpRadius, { friction, restitution, render:{ fillStyle:'#FFD700' } }, protrusionFraction);
        parts.push(bump);
      }
    }

    const compound = Body.create({ parts, isStatic:false, friction, restitution });
    Body.setPosition(compound, { x, y }); Body.setAngle(compound, angleRad);
    compound.isSlope = true; compound.isUserObject = true; compound.slopeProps = { width: widthVal, height: heightVal, angle: angleDeg, surfaceType };
    Composite.add(world, compound);
    slopeBodies.push(compound);
    return compound;
  }

  // ---------- mouse & drag ----------
  const mouse = Mouse.create(render.canvas);
  const mouseConstraint = MouseConstraint.create(engine, { mouse, constraint:{ stiffness:0.18, render:{ visible:false } } });
  Composite.add(world, mouseConstraint); render.mouse = mouse;

  Events.on(mouseConstraint, 'startdrag', (ev)=>{ const b = ev.body; if(!b) return; selectBody(b); if(b.isUserObject){ if(b.isSticky && b.isStatic){ b._wasStickyTemp = true; Body.setStatic(b,false); } } });
  Events.on(mouseConstraint, 'enddrag', (ev)=>{ const b = ev.body; if(!b) return; if(b.isUserObject && b._wasStickyTemp){ b._wasStickyTemp = false; b.isSticky = true; Body.setStatic(b,true); Body.setVelocity(b,{x:0,y:0}); } });

  render.canvas.addEventListener('pointerdown',(ev)=>{
    if(ev.target && ev.target.closest && ev.target.closest('.ui')) return;
    const rect = render.canvas.getBoundingClientRect(); const x = ev.clientX-rect.left; const y = ev.clientY-rect.top;
    if(spawnSlopeOnClick){ spawnSlopeAt(x,y); return; }
    if(spawnObjectOnClick){ spawnObjectAt(x,y); return; }
    const found = Query.point(world.bodies, { x, y });
    if(found.length > 0) selectBody(found[found.length-1]); else selectBody(null);
  });

  // ---------- spawn object helper ----------
  function spawnObjectAt(x,y){ const type = typeSelect.value; const mass = parseFloat(massInput.value) || 1; const friction = parseFloat(frictionInput.value)||0; const frictionStatic = parseFloat(fricStaticInput.value)||0; const restitution = parseFloat(restitutionInput.value)||0;
    let body;
    if(type === 'circle'){ const radius = parseFloat(radiusInput.value)||20; body = createUserBody({ type:'circle', mass, x, y, radius, friction, frictionStatic, restitution }); }
    else { const widthVal = parseFloat(widthInput.value)||40; const heightVal = parseFloat(heightInput.value)||40; body = createUserBody({ type:'box', mass, x, y, width:widthVal, height:heightVal, friction, frictionStatic, restitution }); }
    Composite.add(world, body); userBodies.push(body);
    return body;
  }

  // ---------- selection / actions ----------
  function selectBody(b){ selected = b; if(!b) selectionInfo.textContent = 'Selected: —'; else { const t = b.isSlope ? 'Slope' : (b.circleRadius ? 'Circle' : 'Box'); const massInfo = b.isUserObject ? `, mass=${b.massValue}kg` : ''; const stickyInfo = b.isUserObject ? (b.isSticky ? ', STICKY' : '') : ''; selectionInfo.textContent = `Selected: ${t}${massInfo}${stickyInfo}`; } }

  document.getElementById('toggleSticky').addEventListener('click', ()=>{ if(!selected) return alert('Pilih objek dulu'); selected.isSticky = !selected.isSticky; Body.setStatic(selected, selected.isSticky); });
  deleteSelectedBtn.addEventListener('click', ()=>{ if(!selected) return; Composite.remove(world, selected); userBodies = userBodies.filter(b=>b!==selected); slopeBodies = slopeBodies.filter(s=>s!==selected); selectBody(null); });
  snapGridBtn.addEventListener('click', ()=>{ if(!selected) return; const x = Math.round(selected.position.x/10)*10; const y = Math.round(selected.position.y/10)*10; Body.setPosition(selected,{x,y}); });
  resetBtn.addEventListener('click', ()=>{ userBodies.forEach(b=>{ try{ Composite.remove(world,b);}catch(e){} }); slopeBodies.forEach(s=>{ try{ Composite.remove(world,s);}catch(e){} }); userBodies=[]; slopeBodies=[]; selected=null; selectionInfo.textContent='Selected: —'; });

  // rotate slope / small UX: mousedown rotates selected slightly
  Events.on(mouseConstraint, 'mousedown', (event)=>{
    const m = event.mouse; let clickedBodies = Query.point(slopeBodies, m.position);
    if(clickedBodies.length === 0) clickedBodies = Query.point(userBodies, m.position);
    if(clickedBodies.length > 0){ const body = clickedBodies[0]; const newAngle = body.angle - (Math.PI/36); Body.setAngle(body, newAngle); if(body.isSlope) body.slopeProps.angle = newAngle; else if(body.isUserObject){ body.userProps = body.userProps||{}; body.userProps.angle = newAngle; } }
  });

  // ---------- spawn buttons & toggles ----------
  document.getElementById('spawnCenterBtn').addEventListener('click', ()=>spawnObjectAt(w()/2, h()/2 - 120));
  document.getElementById('spawnSlopeCenter').addEventListener('click', ()=>spawnSlopeAt(w()/2+200, h()/2+100));
  document.getElementById('spawnClickBtn').addEventListener('click', ()=>{ spawnObjectOnClick = !spawnObjectOnClick; spawnClickBtn.textContent = `Spawn di Klik: ${spawnObjectOnClick? 'ON':'OFF'}`; spawnClickBtn.classList.toggle('ghost', !spawnObjectOnClick); if(spawnObjectOnClick){ spawnSlopeOnClick = false; spawnSlopeClick.textContent = 'Spawn Slope Klik: OFF'; spawnSlopeClick.classList.add('ghost'); } });
  document.getElementById('spawnSlopeClick').addEventListener('click', ()=>{ spawnSlopeOnClick = !spawnSlopeOnClick; spawnSlopeClick.textContent = `Spawn Slope Klik: ${spawnSlopeOnClick? 'ON':'OFF'}`; spawnSlopeClick.classList.toggle('ghost', !spawnSlopeOnClick); if(spawnSlopeOnClick){ spawnObjectOnClick = false; spawnClickBtn.textContent = 'Spawn di Klik: OFF'; spawnClickBtn.classList.add('ghost'); } });

  document.getElementById('spawnSlopeBergelombang').addEventListener('click', ()=>{ spawnSlopeAt(w()/2 - 200, h()/2 + 150, 'bergelombang'); });
  document.getElementById('spawnSlopeLicin').addEventListener('click', ()=>{ spawnSlopeAt(w()/2, h()/2 + 150, 'licin'); });
  document.getElementById('spawnSlopeDatar').addEventListener('click', ()=>{ spawnSlopeAt(w()/2 + 200, h()/2 + 150, 'datar'); });

  // export
  exportBtn.addEventListener('click', ()=>{
    const out = { objects:[], slopes:[] };
    userBodies.forEach(b=>{ if(b.circleRadius){ out.objects.push({ type:'circle', mass:b.massValue, radius:b.circleRadius, pos:b.position }); } else { out.objects.push({ type:'box', mass:b.massValue, w:b.bounds.max.x-b.bounds.min.x, h:b.bounds.max.y-b.bounds.min.y, pos:b.position }); } });
    slopeBodies.forEach(s=>{ out.slopes.push({ pos:s.position, w:s.slopeProps.width, h:s.slopeProps.height, angle:s.slopeProps.angle, surfaceType: s.slopeProps.surfaceType }); });
    const str = JSON.stringify(out, null, 2); const win = window.open('', '_blank'); win.document.body.innerHTML = '<pre>'+str+'</pre>';
  });

  // render overlays (labels + selection highlight)
  Events.on(render, 'afterRender', ()=>{
    const ctx = render.context;
    userBodies.forEach(b=>{ ctx.save(); ctx.fillStyle = '#fff'; ctx.font = '13px Inter, Arial'; ctx.textAlign = 'center'; ctx.fillText(b.labelText, b.position.x, b.position.y+4); ctx.restore(); });
    if(selected){ ctx.save(); ctx.strokeStyle = 'orange'; ctx.lineWidth = 2; ctx.beginPath(); if(selected.circleRadius){ ctx.arc(selected.position.x, selected.position.y, selected.circleRadius+4, 0, 2*Math.PI); } else { const v = selected.vertices; ctx.moveTo(v[0].x, v[0].y); for(let i=1;i<v.length;i++) ctx.lineTo(v[i].x,v[i].y); ctx.closePath(); } ctx.stroke(); ctx.restore(); }
  });

  // handle resize
  window.addEventListener('resize', ()=>{
    render.canvas.width = w(); render.canvas.height = h(); Body.setPosition(ground, {x:w()/2,y:h()+wallThickness/2}); Body.setPosition(leftWall, {x:-wallThickness/2,y:h()/2}); Body.setPosition(rightWall, {x:w()+wallThickness/2,y:h()/2});
  });
})();
</script>
</body>
</html>
