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

  #questionBtn { position: fixed; top: 16px; right: 18px; z-index: 85;
    width: 48px; height: 48px; border-radius: 50%; border:none;
    background:#0f172a; color:#fff; font-size:22px; cursor:pointer; transition:all 0.3s ease; }
  #questionBtn:hover { width: 140px; border-radius: 12px; font-size: 18px; }
  #questionBtn::after { content:"Question"; opacity:0; margin-left:8px; transition:opacity 0.3s ease; }
  #questionBtn:hover::after { opacity:1; }

  #questionModal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:95; align-items:center; justify-content:center; }
  #questionModal .card { width:420px; background:#fff; padding:18px; border-radius:10px; }
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

    <div id="feedback"></div>

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
          Sebuah mobil bermassa 5 kg diberi gaya dorong 10 N pada permukaan datar dengan koefisien gesekan kinetik 0.2. Berapakah percepatan mobil tersebut?
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
<script>
const { Engine, Render, Runner, Bodies, Body, Composite, Events } = Matter;

// Engine & World
const engine = Engine.create();
const world = engine.world;
world.gravity.y = 1;

// Canvas size
let W = window.innerWidth, H = window.innerHeight;

const render = Render.create({
    element: document.body,
    engine: engine,
    options: {
        width: W,
        height: H,
        wireframes: false,
        background: '#87CEEB'
    }
});
Render.run(render);
Runner.run(Runner.create(), engine);
render.canvas.style.position = 'absolute';
render.canvas.style.zIndex = '10';

// Ground
const groundHeight = 150;
const groundY = H - groundHeight/2;
const ground = Bodies.rectangle(W/2, groundY, W*2, groundHeight, {
    isStatic: true,
    render: { fillStyle: '#808080' },
    friction : 0
});
Composite.add(world, ground);

// Physics parameters (sesuai parameter)
const MASS = 1200;        // kg
const APPLIED_F = 30000;   // N
const friction_force = 6000; // N

const expectedAcceleration = (APPLIED_F - friction_force) / MASS; // m/s²

// Car
let car =null, engineStarted = false, isAnswerCorrect = false;

function createCar(){
    const groundTop = groundY - groundHeight/2;
    const carBodyHeight = 30;
    const wheelRadius = 12;

    const bodyY = groundTop - wheelRadius - carBodyHeight/2;
    const wheelY = bodyY + carBodyHeight/2 + wheelRadius;

    const body = Bodies.rectangle(W/2, bodyY, 80, carBodyHeight, { render:{fillStyle:'blue'}, density:0.001 });
    const wheel1 = Bodies.circle(W/2-25, wheelY, wheelRadius, { render:{fillStyle:'#333'} });
    const wheel2 = Bodies.circle(W/2+25, wheelY, wheelRadius, { render:{fillStyle:'#333'} });

    car = Body.create({ parts:[body, wheel1, wheel2] });
    Body.setMass(car, MASS);
    car.isBroken = false;
    car.isMoving = false;
    Composite.add(world, car);
}
let brokenParts = [];
function breakCar(){
    if (!car) return;

    // Posisi terakhir sebelum dibongkar
    const pos = car.position;

    // Hapus mobil gabungan
    Composite.remove(world, car);

    // Bikin ulang parts sebagai body terpisah di posisi yang sama
    const brokenBody = Bodies.rectangle(pos.x, pos.y-20, 80, 30, { render:{fillStyle:'red'}, density:0.001 });
    const brokenWheel1 = Bodies.circle(pos.x-35, pos.y+10, 12, { render:{fillStyle:'#555'} });
    const brokenWheel2 = Bodies.circle(pos.x+27, pos.y+10, 12, { render:{fillStyle:'#555'} });
    car.isBroken = true; // 🚩 tandai sudah pecah
    brokenParts = [brokenBody, brokenWheel1, brokenWheel2];
    
    Composite.add(world, brokenParts);
}

createCar();

// Feedback
function showFeedback(msg, success){
  const fb = document.getElementById('feedback');
  fb.textContent = msg;
  fb.className = ''; // reset class dulu
  if(success){
    fb.classList.add('ok');
  } else {
    fb.classList.add('bad');
  }
}

// Check Answer
    document.getElementById('checkAnswerBtn').addEventListener('click', () => {
        if (!engineStarted){
            showFeedback('Harus start engine dulu sebelum submit jawaban!', false);
            return;
        }

        const input = parseFloat(document.getElementById('answerInput').value);
        if (isNaN(input)){
            showFeedback('Masukkan angka yang valid!', false);
            return;
        }

        const expected = expectedAcceleration.toFixed(2);
        if (Math.abs(input - expectedAcceleration) < 0.01) {
            // Jawaban benar
            showFeedback(`Benar! Percepatan = ${expected} m/s². Mobil berjalan normal.`, true);
            isAnswerCorrect = true;
            car.isBroken = false;
            car.render.fillStyle = 'blue';
        } else {
            // Jawaban salah → mobil rusak
            showFeedback(`Salah! Jawaban benar = ${expected} m/s². Mobil rusak.`, false);
            isAnswerCorrect = false;
            car.isBroken = true;
            car.isMoving = false;
            applyingForce = false;

            // stop mobil
            Body.setVelocity(car, { x: 0, y: 0 });
            breakCar();
        }
        updateReadouts();
    });

let accelerationForce = 0; // besar gaya per frame dari input
let applyingForce = false; // state apakah force sedang diterapkan
let userAcceleration = 0;

// Start Engine
document.getElementById('startEngineBtn').addEventListener('click', () => {
  const input = parseFloat(document.getElementById('answerInput').value);
  if (isNaN(input)) {
    showFeedback('Masukkan angka dulu sebelum start engine!', false);
    return;
  }

  engineStarted = true;
  car.isMoving = true;
  userAcceleration=input;
  // Hitung force konstan berdasarkan input percepatan
  // semakin besar input, semakin besar dorongan tiap frame
  accelerationForce = input * 0.0005;  
  applyingForce = true; // aktifkan gaya

  // Atur gesekan rendah biar efeknya kelihatan
  car.friction = 0;
  car.frictionAir = 0.01;

  // Aktifkan tombol cek jawaban
  document.getElementById('checkAnswerBtn').disabled = false;

  showFeedback(`Mesin dinyalakan, mobil dipercepat ${input} m/s²!`, true);
});

// Reset
document.getElementById('resetBtn').addEventListener('click', () => {
  Composite.remove(world, car);
  isAnswerCorrect = false;
  car.isBroken = false;
  car.isMoving = false;
  engineStarted = false;
  applyingForce = false;
  accelerationForce = 0;

  document.getElementById('answerInput').value = '';
    if (brokenParts.length > 0) {
        brokenParts.forEach(part => Composite.remove(world, part));
        brokenParts = [];
    }

    // Hapus mobil utuh kalau masih ada
    if (car) {
        Composite.remove(world, car);
        car = null;
    }
  createCar();
  updateReadouts();
  showFeedback('Reset dilakukan. Mulai lagi dari awal.', true);
});

// Update readouts
function updateReadouts(){
    const vel = car.speed;
    document.getElementById('roAcceleration').textContent = 
        (engineStarted ? userAcceleration : 0).toFixed(2);
    document.getElementById('roFriction').textContent = friction_force.toFixed(2);
    document.getElementById('roVel').textContent = vel.toFixed(2);
}

// Loop force
Events.on(engine, 'beforeUpdate', () => {
  if (applyingForce && car.isMoving) {
    Body.applyForce(car, car.position, { x: accelerationForce, y: 0 });
  }
    updateReadouts();
});

// Modal controls (opsional)
document.getElementById('questionBtn').addEventListener('click',()=>document.getElementById('questionModal').style.display='flex');
document.getElementById('closeQuestion').addEventListener('click',()=>document.getElementById('questionModal').style.display='none');
document.getElementById('closeQuestion2').addEventListener('click',()=>document.getElementById('questionModal').style.display='none');
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
