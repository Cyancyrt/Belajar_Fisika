<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Simulasi Friction — Fixed (Walls + Visible Drop)</title>
<style>
  html,body { margin:0; padding:0; height:100%; overflow:hidden; font-family: Inter, system-ui, Arial; background:#eaeaea; }
  canvas {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    display: block;
  }

  #controls {
    position: fixed;
    top: 10px; left: 10px;
    width: 220px;
    background: rgba(255,255,255,0.95);
    padding: 12px;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    z-index: 80;
  }
  #controls .title { font-weight:700; margin-bottom:8px; }
  button, .btn {
    display:inline-block; border: none; cursor:pointer; border-radius:8px; padding:8px 10px;
    background:#4a4e69; color:#fff; font-size:13px;
  }
  button.ghost { background:#eef2ff; color:#0b1220; border:1px solid #e6e7eb; }

#answerZoneWrap {
  position: fixed;
  left: 12px;
  bottom: 12px;
  z-index: 80;
  display: flex;
  gap: 12px;
  align-items: center;
}
  #answerZone {
    width: 420px; min-height:72px; background: rgba(255,255,255,0.95);
    padding:10px; border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,0.08);
    display:flex; gap:10px; align-items:center; justify-content:flex-start;
    flex-wrap:wrap;
  }
  .answerTile {
    padding:8px 12px; border-radius:8px; background:#fff; border:1px solid #d1d5db;
    cursor:pointer; user-select:none; font-weight:600; font-size:14px;
  }
  .answerTile.dragging { opacity:0.6; transform:scale(0.98); }

 #dropZoneFloating {
    position: absolute;
    bottom: 80px;
    left: 50%;
    transform: translateX(-50%);
    width: 160px;
    height: 56px;
    border-radius: 8px;
    border: 2px dashed #c7d2fe;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0b1220;
    font-weight: 700;
    background: rgba(255,255,255,0.8);
    font-size: 15px;
    z-index: 90;
    pointer-events: none;
  }
  #dropZoneFloating.hasObject {
    border-color: #4a4e69;
    background: rgba(74,78,105,0.15);
  }

  #readouts {
    position: fixed; right: 18px; bottom: 18px; z-index:80;
    background: rgba(255,255,255,0.95); padding:10px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,.06);
    font-size:13px; min-width:220px;
  }

  #feedback { position: fixed; right: 18px; top: 18px; z-index:90; min-width:220px; padding:10px; border-radius:8px; display:none; font-weight:600; }
  #feedback.ok { background: #dcfce7; color:#166534; border:1px solid #bbf7d0; display:block; }
  #feedback.bad { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; display:block; }

#questionBtn {
    position: fixed;
    top: 16px;
    right: 18px;
    z-index: 85;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: none;
    background: #0f172a;
    color: #fff;
    font-size: 22px;
    cursor: pointer;
    transition: all 0.3s ease;
    overflow: hidden;
    white-space: nowrap;
  }

  /* Hover effect */
  #questionBtn:hover {
    width: 140px;          /* lebar lebih panjang */
    border-radius: 12px;   /* jadi rounded rectangle */
    font-size: 18px;       /* font dikecilin biar pas */
  }

  /* Atur teks agar berubah */
  #questionBtn::after {
    content: "Question";
    opacity: 0;
    margin-left: 8px;
    transition: opacity 0.3s ease;
  }

  #questionBtn:hover::after {
    opacity: 1;
  }
  #questionModal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:95; align-items:center; justify-content:center;transition: transform 0.3s ease, opacity 0.3s ease; }
  #questionModal .card { width:420px; background:#fff; padding:18px; border-radius:10px; }
  #questionModal.show{
    animation: fadeInScale 0.4s ease forwards;
  }
  /* Animasi tambahan untuk entrance yang lebih smooth */
      @keyframes fadeInScale {
          from {
              opacity: 0;
              transform: scale(0.8) translateY(-20px);
          }
          to {
              opacity: 1;
              transform: scale(1) translateY(0);
          }
      }
  @media (max-width:860px){
    #controls { width: 200px; }
    #answerZone { width: 92vw; }
  }
</style>
</head>
<body>
  <div id="simContainer">

  <canvas id="simCanvas"></canvas>
  <div id="dropZoneFloating">Drop answer here</div>

  <div id="controls">
    <div class="title">Simulasi: Gaya & Gesekan</div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <button id="applyForceBtn" class="btn">Apply Force</button>
      <button id="resetBtn" class="btn ghost">Reset</button>
    </div>
  </div>

  <div id="answerZoneWrap">
    <div id="answerZone"></div>
    <div id="dropZone"></div>
    <div style="display:flex;flex-direction:column;gap:8px;margin-left:8px">
      <button id="submitAnswer" class="btn">Submit</button>
      <button id="clearDrop" class="btn ghost">Clear</button>
    </div>
  </div>

  <div id="feedback"></div>
  <div id="readouts">
    <div>Fnet: <span id="roFnet">0</span> N</div>
    <div>f (gesek): <span id="roFriction">0</span> N</div>
    <div>Kecepatan: <span id="roVel">0</span> m/s</div>
    <canvas id="graphCanvas" width="600" height="150" style="border:1px solid #ccc;"></canvas>
  </div>

  <button id="questionBtn">?</button>
  <div id="questionModal">
    <div class="card">
      <div style="display:flex;justify-content:space-between;align-items:center">
        <div style="font-weight:700">Soal</div>
        <button id="closeQuestion" style="border:none;background:none;font-size:20px;cursor:pointer">&times;</button>
      </div>
      <p id="questionText" style="margin-top:12px; white-space:pre-line;">
        {{ $question->question_text }}
      </p>
      <div style="margin-top:10px; display:flex; gap:8px; justify-content:flex-end;">
        <button id="closeQuestion2" class="btn ghost">Tutup</button>
      </div>
    </div>
  </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/matter-js@0.19.0/build/matter.min.js"></script>
<script>
const { Engine, Render, Runner, Bodies, Composite, Body, Events, Mouse, MouseConstraint } = Matter;

const engine = Engine.create();
const world = engine.world;
world.gravity.y = 0;

let W = window.innerWidth;
let H = window.innerHeight;

const render = Render.create({
  element: document.body,
  engine,
  options: {
    width: W, height: H,
    wireframes: false,
    background: '#e6eef9'
  }
});
Render.run(render);
const runner = Runner.create();
Runner.run(runner, engine);

render.canvas.style.position = 'absolute';
render.canvas.style.left = '0px';
render.canvas.style.top = '0px';
render.canvas.style.zIndex = '10';

const ground = Bodies.rectangle(W/2, H - 40, W, 80, { isStatic:true, render:{ fillStyle:'#2e2b44' }, friction: 0.8 });
Composite.add(world, ground);

const SLOPE_LENGTH = 600;
const slopeCenterX = W/2 + 150;
const slopeCenterY = H - 180;
let slopeAngleDeg = 0;
const slope = Bodies.rectangle(slopeCenterX, slopeCenterY, SLOPE_LENGTH, 20, { isStatic:true, angle: 0, render:{ fillStyle:'#4a4e69' }, friction: 0.6 });
Composite.add(world, slope);

const wallThickness = 80;
const leftWall = Bodies.rectangle(-wallThickness/2, H/2, wallThickness, H*2, { isStatic:true, render:{ fillStyle:'#0b1220' }});
const rightWall = Bodies.rectangle(W + wallThickness/2, H/2, wallThickness, H*2, { isStatic:true, render:{ fillStyle:'#0b1220' }});
Composite.add(world, [leftWall, rightWall]);

const mouse = Mouse.create(render.canvas);
const mouseConstraint = MouseConstraint.create(engine, {
  mouse,
  constraint: { stiffness: 0.2, render: { visible: false } }
});
Composite.add(world, mouseConstraint);
render.mouse = mouse;

const REAL_G = 9.8;
const FORCE_SCALE = 0.005;

 const defaults = {
    m: parseFloat(@json($question->parameters['m'] ?? 5)),
    mu_s: parseFloat(@json($question->parameters['mu_s'] ?? 0.6)),
    mu_k: parseFloat(@json($question->parameters['mu_k'] ?? 0.4)),
    appliedF: parseFloat(@json($question->parameters['appliedF'] ?? 20)),
    slopeDeg: parseFloat(@json($question->parameters['slopeDeg'] ?? 0))
  };
let appliedF = 0; // gaya luar saat ini

function degToRad(d){ return d * Math.PI / 180; }

function slopeEndpoints() {
  const rad = slope.angle;
  const half = SLOPE_LENGTH / 2;
  const x1 = slope.position.x - Math.cos(rad) * half;
  const y1 = slope.position.y - Math.sin(rad) * half;
  const x2 = slope.position.x + Math.cos(rad) * half;
  const y2 = slope.position.y + Math.sin(rad) * half;
  return { x1, y1, x2, y2, rad };
}

let slopeEndWall = null;

function computeForces({ m=defaults.m, mu_s=defaults.mu_s, mu_k=defaults.mu_k, appliedF=defaults.appliedF, slopeDeg=defaults.slopeDeg } = {}) {
  const theta = degToRad(slopeDeg);
  const N = m * REAL_G * Math.cos(theta);
  const gravityAlong = m * REAL_G * Math.sin(theta);
  const netDriving = appliedF + gravityAlong;
  const fs_max = mu_s * N;
  const fk = mu_k * N;
  const moves = Math.abs(netDriving) > fs_max;
  const f = moves ? fk : Math.abs(netDriving);
  return { N, gravityAlong, netDriving, fs_max, fk, f, moves };
}


const answerZone = document.getElementById('answerZone');
const dropZone = document.getElementById('dropZone');
const dropZoneFloating = document.getElementById('dropZoneFloating');
let currentDroppedTile = null;
let currentDroppedObject = null;
let spawnedBodies = [];

function spawnAnswerOptions() {
  answerZone.innerHTML = '';
  currentDroppedTile = null;
  const vals = computeForces({});

  const baseAns = vals.f;

  // langsung buat pasangan F dan f
  let pairs = [
    {F: 20, f: baseAns},
    {F: 40, f: baseAns * 1.5},
    {F: 10, f: baseAns * 0.5},
    {F: 30, f: baseAns * 0.75},
  ];

  // bulatkan & hilangkan duplikat berdasarkan string "F,f"
  pairs = Array.from(new Map(pairs.map(p => {
    const F = Number(p.F.toFixed(2));
    const f = Number(p.f.toFixed(2));
    return [`${F},${f}`, {F,f}];
  })).values());

  // acak urutan
  for (let i = pairs.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [pairs[i], pairs[j]] = [pairs[j], pairs[i]];
  }

  // buat tile
  pairs.forEach(p => {
    const d = document.createElement('div');
    d.className = 'answerTile';
    d.setAttribute('draggable','true');
    d.dataset.F = p.F;
    d.dataset.f = p.f;
    d.textContent = `f=${p.f} N`;

    d.addEventListener('dragstart', (ev) => {
      ev.dataTransfer.setData('text/plain', JSON.stringify(p));
      setTimeout(()=> d.classList.add('dragging'), 0);
    });
    d.addEventListener('dragend', () => d.classList.remove('dragging'));

    d.addEventListener('click', () => {
      spawnBoxFromAnswer(p.F, p.f, d.textContent);
    });

    answerZone.appendChild(d);
  });
}

dropZone.addEventListener('dragover', (ev)=> { ev.preventDefault(); dropZone.style.background = 'rgba(99,102,241,0.12)'; });
dropZone.addEventListener('dragleave', ()=> { dropZone.style.background = ''; });
dropZone.addEventListener('drop', (ev)=>{
  ev.preventDefault();
  dropZone.style.background = '';
  const data = ev.dataTransfer.getData('text/plain');
  if(!data) return;
  if(currentDroppedTile){
    answerZone.appendChild(currentDroppedTile);
    currentDroppedTile = null;
  }
  const tiles = Array.from(document.querySelectorAll('.answerTile'));
  const tile = tiles.find(t => t.dataset.value === data);
  if(tile){
    dropZone.innerHTML = '';
    dropZone.appendChild(tile);
    currentDroppedTile = tile;
  }
});

document.getElementById('clearDrop').addEventListener('click', ()=>{
  if(currentDroppedObject){
    Composite.remove(world, currentDroppedObject);
    spawnedBodies = spawnedBodies.filter(b => b !== currentDroppedObject);
    currentDroppedObject = null;
    dropZoneFloating.textContent = 'Drop answer here';
    dropZoneFloating.classList.remove('hasObject');
  }
});

document.getElementById('submitAnswer').addEventListener('click', ()=>{
  if(!currentDroppedObject){
    showFeedback('Letakkan object ke drop zone terlebih dahulu.', false);
    return;
  }
  const picked = currentDroppedObject.slideValueN;
  const expected = Number(computeForces({}).f.toFixed(2));
  if(Math.abs(picked - expected) < 0.05){
    showFeedback(`Benar! Jawaban = ${picked} N (expected ${expected} N).`, true);
  } else {
    showFeedback(`Salah — kamu memilih ${picked} N, sedangkan seharusnya ${expected} N.`, false);
  }
});

function showFeedback(text, ok){
  const fb = document.getElementById('feedback');
  fb.className = ok ? 'ok' : 'bad';
  fb.textContent = text;
  fb.style.display = 'block';
  setTimeout(()=> fb.style.display = 'none', ok ? 3500 : 6000);
}

function spawnBoxFromAnswer(Fvalue, fvalue, labelText) {
  const { x1, y1, rad, x2, y2 } = slopeEndpoints();
  const along = 40;
  const spawnX = x1 + Math.cos(rad) * along;
  const spawnY = y1 + Math.sin(rad) * along - 24;

  const cosT = Math.cos(rad) || 1;
  const mass = defaults.m

  const w = 48; const h = 48;
  const body = Bodies.rectangle(spawnX, spawnY, w, h, {
    friction: 0,
    frictionStatic: 0,
    restitution: 0,
    render: { fillStyle: '#f97316' }
  });
  Body.setMass(body, mass);
  body.isSlideObject = true;
  body.slideValueN = Number(fvalue.toFixed(2));
  body.slideLabel = labelText || `${fvalue.toFixed(2)} N`;
  body.slideSpeed = 0;

  body.appliedF = Number(Fvalue.toFixed(2));

  body.inertia = Infinity;
  body.inverseInertia = 0;

  Composite.add(world, body);
  spawnedBodies.push(body);
}

Events.on(render, 'afterRender', () => {
  const ctx = render.context;
  ctx.font = "14px Inter, Arial";
  ctx.textAlign = "center";
  ctx.textBaseline = "middle";
  ctx.fillStyle = "#fff";
  spawnedBodies.forEach(b => {
    if (b && b.slideLabel) {
      ctx.fillStyle = "rgba(0,0,0,0.45)";
      ctx.beginPath();
      ctx.roundRect
        ? ctx.roundRect(b.position.x - 28, b.position.y - 22, 56, 20, 8)
        : (ctx.fillRect(b.position.x - 28, b.position.y - 22, 56, 20));
      ctx.fillStyle = "#fff";
      ctx.fillText(b.slideLabel, b.position.x, b.position.y - 12);
    }
  });
});

Events.on(engine, 'beforeUpdate', (event) => {
  const dt = (event.delta || 16.6667) / 1000;
  const { x1, y1, x2, y2, rad } = slopeEndpoints();
  const leftX = Math.min(x1, x2) + 8;
  const rightX = Math.max(x1, x2) - 8;
  const slopeTan = Math.tan(rad);
  const nx = -Math.sin(rad), ny = Math.cos(rad);

  if (slopeEndWall) {
    const offset = 24 + 10;
    const cx = x2 + nx * offset;
    const cy = y2 + ny * offset;
    Body.setPosition(slopeEndWall, { x: cx, y: cy });
    Body.setAngle(slopeEndWall, rad + Math.PI/2);
  }

  const dzRect = dropZoneFloating.getBoundingClientRect();
  const dzCenterX = dzRect.left + dzRect.width/2;
  const dzCenterY = dzRect.top + dzRect.height/2;

  
  spawnedBodies.forEach(b => {
    if (!b.isSlideObject) return;

    if (mouseConstraint && mouseConstraint.body === b) {
      const x = Math.max(leftX, Math.min(rightX, b.position.x));
      const yOn = y1 + (x - x1) * slopeTan - 24;
      Body.setPosition(b, { x: x, y: yOn });
      b.slideSpeed = 0;
      return;
    }

    const canvasRect = render.canvas.getBoundingClientRect();
    const objScreenX = canvasRect.left + b.position.x;
    const objScreenY = canvasRect.top + b.position.y;
    
    const distX = Math.abs(objScreenX - dzCenterX);
    const distY = Math.abs(objScreenY - dzCenterY);
    
    if (distX < dzRect.width/2 && distY < dzRect.height/2 && !currentDroppedObject) {
      currentDroppedObject = b;
      b.isInDropZone = true;
      b.slideSpeed = 0;
      dropZoneFloating.textContent = b.slideLabel;
      dropZoneFloating.classList.add('hasObject');
      return;
    }

    if (b.isInDropZone) {
      return;
    }

    const dx = Math.cos(rad) * (b.slideSpeed * dt);
    let newX = b.position.x + dx;

    if (newX < leftX) newX = leftX;
    if (newX > rightX) newX = rightX;

    const newY = y1 + (newX - x1) * slopeTan - 24;
    Body.setPosition(b, { x: newX, y: newY });

    if (newX <= leftX + 0.5 || newX >= rightX - 0.5) {
      b.slideSpeed = 0;
    }
  });

  spawnedBodies.forEach(b => {
    if (!b.isSlideObject || b.isInDropZone) return;
    if (b.position.x < 0 + 20) {
      Body.setPosition(b, { x: 20, y: b.position.y });
      b.slideSpeed = 0;
    } else if (b.position.x > W - 20) {
      Body.setPosition(b, { x: W - 20, y: b.position.y });
      b.slideSpeed = 0;
    }
  });
});
let time = 0;
document.getElementById('applyForceBtn').addEventListener('click', () => {
  const dt = 0.016; // kira-kira 60fps

  spawnedBodies.forEach(b => {
    if (!b.isSlideObject) return;

    const mass = b.mass || defaults.m;
    const theta = degToRad(defaults.slopeDeg);

    // Normal force
    const N = mass * REAL_G * Math.cos(theta);

    // Gesek
    const fs_max = defaults.mu_s * N;
    const fk = defaults.mu_k * N;
    const userInputF = b.appliedF;
    let Fnet;

    if (userInputF <= fs_max) {
      // Belum cukup untuk bergerak → diam
      Fnet = 0;
      b.slideSpeed = 0;
    } else {
      // Bergerak → gesek kinetik
      Fnet = userInputF - fk; // jangan redeclare const
      const a = Fnet / mass;
      b.slideSpeed += a * dt *50; // kalikan dt
    }

    // update readouts
    document.getElementById('roFnet').textContent = Number(Fnet).toFixed(2);
    document.getElementById('roVel').textContent = b.slideSpeed.toFixed(2);
    document.getElementById('roFriction').textContent = (userInputF > fs_max ? fk : userInputF).toFixed(2);
  });

  // update graph satu kali (kalau mau terus bergerak, pakai requestAnimationFrame)
});


document.getElementById('resetBtn').addEventListener('click', () => {
  // hapus semua benda di world
  spawnedBodies.forEach(b => Composite.remove(world, b));
  spawnedBodies = [];

  // reset objek drop
  currentDroppedObject = null;
  dropZoneFloating.textContent = 'Drop answer here';
  dropZoneFloating.classList.remove('hasObject');

  // spawn ulang pilihan jawaban
  spawnAnswerOptions();

  // reset tile jika ada yang sedang di-drop
  if (currentDroppedTile) {
    document.getElementById('answerZone').appendChild(currentDroppedTile);
    currentDroppedTile = null;
    dropZone.innerHTML = 'Drop answer here';
  }

  // reset hasil output ke default
  document.getElementById('roFnet').textContent = "0.00";
  document.getElementById('roVel').textContent = "0.00";
  document.getElementById('roFriction').textContent = "0.00";
});


spawnAnswerOptions();

window.addEventListener('resize', ()=> {
  W = window.innerWidth; H = window.innerHeight;
  render.canvas.width = W; render.canvas.height = H;
  Body.setPosition(ground, { x: W/2, y: H-40 });
  Body.setPosition(slope, { x: W/2 + 150, y: H - 180 });
  Body.setPosition(rightWall, { x: W + wallThickness/2, y: H/2 });
  Body.setPosition(leftWall, { x: -wallThickness/2, y: H/2 });
});

const questionBtn = document.getElementById('questionBtn');
const questionModal = document.getElementById('questionModal');
const closeQuestion = document.getElementById('closeQuestion');
const closeQuestion2 = document.getElementById('closeQuestion2');
questionBtn.addEventListener('click', ()=> questionModal.style.display = 'flex');
closeQuestion.addEventListener('click', ()=> questionModal.style.display = 'none');
closeQuestion2.addEventListener('click', ()=> questionModal.style.display = 'none');

document.addEventListener('dragstart', (e) => {
  if (e.target.classList && e.target.classList.contains('answerTile')) {
    e.dataTransfer.effectAllowed = 'move';
  }
});

(function addRoundRectPolyfill() {
  const ctx = render.context;
  if (!ctx.roundRect) {
    ctx.roundRect = function (x, y, w, h, r) {
      const minSize = Math.min(w, h);
      if (r > minSize / 2) r = minSize / 2;
      this.beginPath();
      this.moveTo(x + r, y);
      this.arcTo(x + w, y, x + w, y + h, r);
      this.arcTo(x + w, y + h, x, y + h, r);
      this.arcTo(x, y + h, x, y, r);
      this.arcTo(x, y, x + w, y, r);
      this.closePath();
      this.fill();
    };
  }
})();
</script>
</body>
</html>