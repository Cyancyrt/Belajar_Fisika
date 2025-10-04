<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Simulasi Mobil — Friction & Percepatan</title>
<style>
  html,body { margin:0; padding:0; height:100%; overflow:hidden; font-family: Inter, system-ui, Arial; background:#eaeaea; }
  canvas { position:absolute; top:0; left:0; width:100%; height:100%; z-index:0; display:block; }

  #controls {
    position: fixed; top: 10px; left: 10px;
    width: 220px; background: rgba(255,255,255,0.95);
    padding: 12px; border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    z-index: 80;
  }
  #controls .title { font-weight:700; margin-bottom:8px; }
  button { border:none; cursor:pointer; border-radius:8px; padding:8px 10px; font-size:13px; }
  .btn { background:#4a4e69; color:#fff; }
  .ghost { background:#eef2ff; color:#0b1220; border:1px solid #e6e7eb; }

  #inputSection {
    position: fixed; bottom: 12px; left: 50%; transform: translateX(-50%);
    z-index: 80; background: rgba(255,255,255,0.95); padding: 15px;
    border-radius: 10px; box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    display: flex; gap: 10px; align-items: center;
  }
  #answerInput { padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
  #checkAnswerBtn { padding: 8px 16px; background: #4a4e69; color: white; border: none; border-radius: 6px; cursor: pointer; }

  #readouts {
    position: fixed; right: 18px; bottom: 18px; z-index:80;
    background: rgba(255,255,255,0.95); padding:10px; border-radius:8px;
    box-shadow: 0 6px 18px rgba(0,0,0,.06); font-size:13px; min-width:220px;
  }

  #feedback {
    position: fixed; right: 18px; top: 18px; z-index:90;
    min-width:220px; padding:10px; border-radius:8px; display:none; font-weight:600;
  }
  #feedback.ok { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; display:block; }
  #feedback.bad { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; display:block; }
  #feedback .close-btn { position:absolute; top:5px; right:10px; background:none; border:none; color:#666; font-size:18px; cursor:pointer; }
  #feedback .close-btn:hover { color: #000; }

  #questionBtn { position: fixed; top: 16px; right: 18px; z-index: 85;
    width: 48px; height: 48px; border-radius: 50%; border:none;
    background:#0f172a; color:#fff; font-size:22px; cursor:pointer; transition:all 0.3s ease; }
  #questionBtn:hover { width: 140px; border-radius: 12px; font-size: 18px; }
  #questionBtn::after { content:"Question"; opacity:0; margin-left:8px; transition:opacity 0.3s ease; }
  #questionBtn:hover::after { opacity:1; }

  #questionModal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:95; align-items:center; justify-content:center; }
  #questionModal .card { width:420px; background:#fff; padding:18px; border-radius:10px; }

  @media (max-width: 768px) {
  #controls {
    width: 90%;
    left: 50%;
    transform: translateX(-50%);
    top: 10px;
    font-size: 14px;
  }

  #inputSection {
    flex-direction: column;
    width: 90%;
    bottom: 10px;
    padding: 12px;
  }

  #answerInput {
    width: 100%;
    font-size: 14px;
  }

  #checkAnswerBtn {
    width: 100%;
  }

  #readouts {
    position: fixed;
    right: 10px;
    bottom: 100px; /* biar tidak ketutup inputSection */
    font-size: 12px;
    min-width: auto;
  }

  #questionBtn {
    width: 40px;
    height: 40px;
    font-size: 18px;
  }

  #questionModal .card {
    width: 90%;
  }
}

</style>
</head>

<body>
  <div id="simContainer">
    <canvas id="simCanvas"></canvas>

    <div id="controls">
      <div class="title">Simulasi: Mobil & Percepatan</div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <button id="startEngineBtn" class="btn">Start Engine</button>
        <button id="resetBtn" class="btn ghost">Reset</button>
      </div>
    </div>

  <div id="feedback" class="feedback">
    <span id="feedbackMsg"></span>
    <button id="closeFeedback" class="close-btn">&times;</button>
  </div>

    <div id="readouts">
      <div>Percepatan: <span id="roAcceleration">0</span> m/s²</div>
      <div>Gaya Gesek: <span id="roFriction">0</span> N</div>
      <div>Kecepatan: <span id="roVel">0</span> m/s</div>
    </div>

    <button id="questionBtn">?</button>
    <div id="questionModal">
      <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center">
          <div style="font-weight:700">Soal</div>
          <button id="closeQuestion" style="border:none;background:none;font-size:20px;cursor:pointer">&times;</button>
        </div>
        <p id="questionText" style="margin-top:12px;">
          {{ $question->question_text }}
        </p>
        <div style="margin-top:10px; display:flex; gap:8px; justify-content:flex-end;">
          <button id="closeQuestion2" class="btn ghost">Tutup</button>
        </div>
      </div>
    </div>

    <div id="inputSection">
    <input type="number" id="answerInput" placeholder="Masukkan percepatan (m/s²)" step="0.01">
    <button id="checkAnswerBtn" class="btn">Cek Jawaban</button>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/matter-js@0.19.0/build/matter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/matter-js/0.19.0/matter.min.js"></script>
{{-- LOGIC Simulation --}}
<script>
const { Engine, Render, Runner, Bodies, Body, Composite, Events } = Matter;

// === SETUP DASAR ===
const engine = Engine.create();
const world = engine.world;
world.gravity.y = 1;

let W = window.innerWidth, H = window.innerHeight;

// Renderer
const render = Render.create({
  element: document.body,
  engine: engine,
  options: {
    width: W,
    height: H,
    wireframes: false,
    background: 'transparent'
  }
});
Render.run(render);
Runner.run(Runner.create(), engine);

render.canvas.style.position = 'absolute';
render.canvas.style.zIndex = '10';

// === GROUND TAK TERBATAS ===
const groundHeight = 150;
const groundY = H - groundHeight / 2;
const segmentWidth = W;
const groundSegments = [];

for (let i = 0; i < 3; i++) {
  const seg = Bodies.rectangle(
    i * segmentWidth + segmentWidth / 2,
    groundY,
    segmentWidth,
    groundHeight,
    {
      isStatic: true,
      render: { fillStyle: '#7c5e10' },
      friction: 0
    }
  );
  Composite.add(world, seg);
  groundSegments.push(seg);
}

// updateGround: pindahkan segmen paling kiri ke depan
function updateGround(cameraX) {
  const threshold = segmentWidth / 2;
  groundSegments.forEach(seg => {
    if (cameraX - seg.position.x > threshold * 2) {
      Body.setPosition(seg, {
        x: seg.position.x + segmentWidth * groundSegments.length,
        y: groundY
      });
    }
  });
}

// === LATAR BELAKANG (PARALLAX) ===
const bgCanvas = document.createElement('canvas');
bgCanvas.width = W;
bgCanvas.height = H;
bgCanvas.style.position = 'absolute';
bgCanvas.style.top = '0';
bgCanvas.style.left = '0';
bgCanvas.style.zIndex = '5';
document.body.appendChild(bgCanvas);

const bgCtx = bgCanvas.getContext('2d');

function drawBackground(cameraX = 0) {
  bgCtx.clearRect(0, 0, W, H);

  // Langit
  const gradient = bgCtx.createLinearGradient(0, 0, 0, H);
  gradient.addColorStop(0, "#b3e5fc");
  gradient.addColorStop(1, "#e1f5fe");
  bgCtx.fillStyle = gradient;
  bgCtx.fillRect(0, 0, W, H);

  // Gunung
  bgCtx.fillStyle = "#7ba176";
  const mountainSpacing = 400;
  const mountainOffset = (cameraX * 0.3) % mountainSpacing;
  for (let i = -1; i < (W / mountainSpacing) + 2; i++) {
    const baseX = (i * mountainSpacing) - mountainOffset;
    bgCtx.beginPath();
    bgCtx.moveTo(baseX, H - 100);
    bgCtx.lineTo(baseX + 200, H - 350);
    bgCtx.lineTo(baseX + 400, H - 100);
    bgCtx.closePath();
    bgCtx.fill();
  }

  // Pohon
  const treeSpacing = 180;
  const treeOffset = (cameraX * 0.6) % treeSpacing;
  for (let i = -1; i < (W / treeSpacing) + 3; i++) {
    const x = (i * treeSpacing) - treeOffset;
    const y = H - 120;

    bgCtx.fillStyle = "#5b3714";
    bgCtx.fillRect(x + 10, y - 50, 12, 50);

    bgCtx.beginPath();
    bgCtx.fillStyle = "#2e7d32";
    bgCtx.arc(x + 16, y - 60, 25, 0, Math.PI * 2);
    bgCtx.fill();

    bgCtx.beginPath();
    bgCtx.arc(x + 5, y - 65, 20, 0, Math.PI * 2);
    bgCtx.fill();
  }
}

// === MOBIL ===
const MASS = 1200;
const APPLIED_F = 30000;
const friction_force = 6000;
const expectedAcceleration = (APPLIED_F - friction_force) / MASS;

let car = null;
let engineStarted = false;
let isAnswerCorrect = false;
let brokenParts = [];

function createCar() {
  const groundTop = groundY - groundHeight / 2;
  const wheelR = 12;
  const bodyY = groundTop - wheelR - 15;
  const wheelY = bodyY + 15 + wheelR;

  const body = Bodies.rectangle(W / 2, bodyY, 80, 30, { render: { fillStyle: 'blue' } });
  const w1 = Bodies.circle(W / 2 - 25, wheelY, wheelR, { render: { fillStyle: '#333' } });
  const w2 = Bodies.circle(W / 2 + 25, wheelY, wheelR, { render: { fillStyle: '#333' } });

  car = Body.create({ parts: [body, w1, w2] });
  Body.setMass(car, MASS);
  car.isBroken = false;
  car.isMoving = false;

  Composite.add(world, car);
}

function breakCar() {
  if (!car) return;
  const pos = car.position;
  Composite.remove(world, car);

  const brokenBody = Bodies.rectangle(pos.x, pos.y - 20, 80, 30, { render: { fillStyle: 'red' } });
  const w1 = Bodies.circle(pos.x - 35, pos.y + 10, 12, { render: { fillStyle: '#555' } });
  const w2 = Bodies.circle(pos.x + 27, pos.y + 10, 12, { render: { fillStyle: '#555' } });

  brokenParts = [brokenBody, w1, w2];
  Composite.add(world, brokenParts);
}

// === CAMERA FOLLOW + GROUND UPDATE ===
let lastCameraX = 0;
const UPDATE_THRESHOLD = 5;

Events.on(render, 'afterRender', function() {
  if (!car) return;
  const cameraX = car.position.x - W / 2;

  if (Math.abs(cameraX - lastCameraX) > UPDATE_THRESHOLD) {
    drawBackground(cameraX);
    updateGround(cameraX);
    lastCameraX = cameraX;
  }

  Render.lookAt(render, {
    min: { x: cameraX, y: 0 },
    max: { x: cameraX + W, y: H }
  });
});

// === INTERAKSI ===
let accelerationForce = 0;
let applyingForce = false;
let userAcceleration = 0;

function showFeedback(msg, success) {
  const fb = document.getElementById('feedback');
  const fbMsg = document.getElementById('feedbackMsg');
  fbMsg.textContent = msg;
  fb.className = '';
  fb.style.display = 'block';
  fb.style.opacity = '1';
  fb.classList.add(success ? 'ok' : 'bad');

  setTimeout(() => {
    fb.style.opacity = '0';
    setTimeout(() => fb.style.display = 'none', 400);
  }, success ? 3000 : 5000);
}

document.getElementById('closeFeedback').addEventListener('click', () => {
  const fb = document.getElementById('feedback');
  fb.style.opacity = '0';
  setTimeout(() => fb.style.display = 'none', 400);
});

document.getElementById('startEngineBtn').addEventListener('click', () => {
  const input = parseFloat(document.getElementById('answerInput').value);
  if (isNaN(input)) return showFeedback('Masukkan angka dulu sebelum start engine!', false);

  engineStarted = true;
  car.isMoving = true;
  userAcceleration = input;
  accelerationForce = input * 0.0005;
  applyingForce = true;

  car.friction = 0;
  car.frictionAir = 0.01;

  document.getElementById('checkAnswerBtn').disabled = false;
  showFeedback(`Mesin dinyalakan, mobil dipercepat ${input} m/s²!`, true);
});

document.getElementById('checkAnswerBtn').addEventListener('click', () => {
  if (!engineStarted) return showFeedback('Harus start engine dulu sebelum submit jawaban!', false);
  const input = parseFloat(document.getElementById('answerInput').value);
  if (isNaN(input)) return showFeedback('Masukkan angka yang valid!', false);

  const expected = expectedAcceleration.toFixed(2);
  if (Math.abs(input - expectedAcceleration) < 0.01) {
    showFeedback(`Benar! Percepatan = ${expected} m/s². Mobil berjalan normal.`, true);
    isAnswerCorrect = true;
    car.render.fillStyle = 'blue';
  } else {
    showFeedback(`Salah! Jawaban benar = ${expected} m/s². Mobil rusak.`, false);
    isAnswerCorrect = false;
    car.isMoving = false;
    applyingForce = false;
    Body.setVelocity(car, { x: 0, y: 0 });
    breakCar();
  }
  updateReadouts();
});
Events.on(engine, 'afterUpdate', function once() {
  Body.applyForce(car, car.position, { x: 0.05, y: 0 });
  Events.off(engine, 'afterUpdate', once); // hanya sekali
});
document.getElementById('resetBtn').addEventListener('click', () => {
  // Hapus semua objek
  Composite.clear(world, false);
  brokenParts = [];
  
  // Buat ulang ground
  groundSegments.length = 0;
  for (let i = 0; i < 3; i++) {
    const seg = Bodies.rectangle(
      i * segmentWidth + segmentWidth / 2,
      groundY,
      segmentWidth,
      groundHeight,
      {
        isStatic: true,
        render: { fillStyle: '#7c5e10' },
        friction: 0
      }
    );
    Composite.add(world, seg);
    groundSegments.push(seg);
  }

  // Reset status mobil
  isAnswerCorrect = false;
  engineStarted = false;
  applyingForce = false;
  accelerationForce = 0;
  document.getElementById('answerInput').value = '';

  createCar();
  drawBackground(0);
  updateGround(0);
  updateReadouts();

  showFeedback('Reset dilakukan. Mulai lagi dari awal.', true);
});


function updateReadouts() {
  const vel = car ? car.speed : 0;
  document.getElementById('roAcceleration').textContent = (engineStarted ? userAcceleration : 0).toFixed(2);
  document.getElementById('roFriction').textContent = friction_force.toFixed(2);
  document.getElementById('roVel').textContent = vel.toFixed(2);
}

Events.on(engine, 'beforeUpdate', () => {
  if (applyingForce && car && car.isMoving) {
    Body.applyForce(car, car.position, { x: accelerationForce, y: 0 });
  }
  updateReadouts();
});

// Modal controls
document.getElementById('questionBtn').addEventListener('click', () => {
  document.getElementById('questionModal').style.display = 'flex';
});
['closeQuestion', 'closeQuestion2'].forEach(id => {
  document.getElementById(id).addEventListener('click', () => {
    document.getElementById('questionModal').style.display = 'none';
  });
});

// INIT
createCar();
drawBackground(0);
updateGround(0);
</script>

{{-- SUBMIT ANSWER --}}
<script>
// ambil CSRF token dulu
await fetch('/sanctum/csrf-cookie', {
  method: 'GET',
  credentials: 'include' // penting, supaya cookie tersimpan
});

const response = await fetch(`/api/simulation/questions/${questionId}/submit`, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  credentials: 'include', // cookie sanctum dikirim otomatis
  body: JSON.stringify({
    answer: "jawaban user"
  }),
});

if (!response.ok) throw new Error("Error: " + response.status);
const data = await response.json();
console.log(data);
</script>
{{-- GET Question --}}
<script>
const token = "TOKEN_SANCTUM_KAMU";

const response = await fetch(`/api/simulation/topics/${topicSlug}/question`, {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json',
  }
});

if (!response.ok) throw new Error("Error: " + response.status);
const data = await response.json();
console.log(data);
</script>
</body>
</html>
