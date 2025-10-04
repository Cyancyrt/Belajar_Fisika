<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Gaya Gesek</title>
<style>
  body, html { 
    margin: 0; 
    padding: 0; 
    overflow: hidden;
    font-family: 'Nunito', 'Comic Sans MS', 'Arial Rounded MT Bold', sans-serif;
  }
  canvas { 
    display: block; 
    background: #FFF8F2;
  }
  #controls {
    position: fixed;
    top: 20px; 
    left: 20px;
    background: #FFFFFF;
    padding: 20px;
    border-radius: 20px;
    box-shadow: 0 8px 16px rgba(123, 92, 245, 0.15);
    z-index: 10;
    user-select: none;
    max-width: 280px;
    border: 3px solid #7B5CF5;
  }
  
  #controls h2 {
    margin: 0 0 15px 0;
    color: #7B5CF5;
    font-size: 22px;
    font-weight: 800;
  }
  
  #controls label {
    display: block;
    margin-bottom: 15px;
    color: #3A2E2E;
    font-weight: 700;
    font-size: 14px;
  }
  
  #controls input[type="range"] {
    width: 100%;
    height: 8px;
    border-radius: 10px;
    background: #FFF8F2;
    outline: none;
    margin: 8px 0;
    cursor: pointer;
  }
  
  #controls input[type="range"]::-webkit-slider-thumb {
    appearance: none;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #7B5CF5;
    cursor: pointer;
    border: 3px solid #FFFFFF;
    box-shadow: 0 2px 8px rgba(123, 92, 245, 0.3);
  }
  
  #controls input[type="number"] {
    width: 100%;
    padding: 10px;
    border: 2px solid #7B5CF5;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    color: #3A2E2E;
    background: #FFF8F2;
    margin-top: 5px;
  }
  
  #controls span {
    display: inline-block;
    background: #7B5CF5;
    color: #FFFFFF;
    padding: 4px 12px;
    border-radius: 20px;
    font-weight: 800;
    margin-left: 8px;
    font-size: 14px;
  }
  
  .forceLabel {
    position: absolute;
    background: #7B5CF5;
    color: #FFFFFF;
    font-size: 13px;
    font-weight: 700;
    padding: 8px 14px;
    border-radius: 16px;
    pointer-events: none;
    user-select: none;
    white-space: nowrap;
    box-shadow: 0 4px 12px rgba(123, 92, 245, 0.3);
    border: 2px solid #FFFFFF;
  }
  
  #dropdownMenu {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 20;
    user-select: none;
  }

  #mainButton {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 3px solid #FFFFFF;
    background-color: #7B5CF5;
    color: white;
    font-size: 28px;
    cursor: pointer;
    box-shadow: 0 6px 16px rgba(123, 92, 245, 0.4);
    transition: all 0.2s;
    font-weight: 800;
  }

  #mainButton:hover {
    background-color: #4DB6FF;
    transform: scale(1.05);
  }

  #mainButton:active {
    transform: scale(0.95);
  }

  #submenu {
    position: absolute;
    top: 70px;
    right: 0;
    display: none;
    flex-direction: column;
    gap: 12px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s;
  }

  #submenu.show {
    display: flex;
    opacity: 1;
    pointer-events: auto;
  }

  .subButton {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 3px solid #FFFFFF;
    background-color: #4DB6FF;
    color: white;
    font-size: 20px;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(77, 182, 255, 0.3);
    transition: all 0.2s;
  }

  .subButton:hover {
    background-color: #7B5CF5;
    transform: scale(1.05);
  }
  
  .subButton:active {
    transform: scale(0.95);
  }
  
  #resetBtn {
    width: 100%;
    padding: 14px;
    background: #10B981;
    color: #FFFFFF;
    border: none;
    border-radius: 16px;
    font-size: 16px;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    transition: all 0.2s;
    margin-top: 10px;
  }
  
  #resetBtn:hover {
    background: #059669;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
  }
  
  #resetBtn:active {
    transform: translateY(0);
  }
</style>
</head>
<body>
  <div id="controls">
  <div id="dropdownMenu">
    <button id="mainButton" title="Menu">&#9776;</button>
    <div id="submenu">
      <button class="subButton" title="Submenu 1">1</button>
      <button class="subButton" title="Submenu 2">2</button>
      <button class="subButton" title="Submenu 3">3</button>
    </div>
  </div>
  <label>
    Kemiringan (derajat):
    <input type="range" id="slopeAngle" min="-45" max="0" step="1" value="-23" />
    <span id="slopeAngleValue">-23</span>
  </label>
  <label>
    Massa benda (kg):
    <input type="number" id="boxMass" min="0.1" step="0.1" value="1" />
  </label>
  <label>
    Jumlah benda:
    <input type="number" id="boxCount" min="1" max="10" step="1" value="1" />
  </label>
  <button id="resetBtn">Reset Simulasi</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/matter-js@0.19.0/build/matter.min.js"></script>
<script>
  const { Engine, Render, Runner, Bodies, Composite, Body, Events, Mouse, MouseConstraint } = Matter;

  const engine = Engine.create();
  engine.world.gravity.y = 1;

  let width = window.innerWidth;
  let height = window.innerHeight;

  const render = Render.create({
    element: document.body,
    engine: engine,
    options: {
      width,
      height,
      wireframes: false,
      background: '#eaeaea'
    }
  });

  // Ground datar
  const ground = Bodies.rectangle(width/2, height - 40, width, 80, {
    isStatic: true,
    friction: 0.2,
    render: { fillStyle: '#2e2b44' }
  });

  // Slope miring (posisi dan ukuran tetap, angle akan diubah)
  const slope = Bodies.rectangle(600, height - 150, 500, 20, {
    isStatic: true,
    angle: -0.4, // default -23 derajat
    friction: 0.3,
    render: { fillStyle: '#4a4e69' }
  });

  Composite.add(engine.world, [ground, slope]);

  Render.run(render);
  const runner = Runner.create();
  Runner.run(runner, engine);

  // Mouse drag
  const mouse = Mouse.create(render.canvas);
  const mouseConstraint = MouseConstraint.create(engine, {
    mouse: mouse,
    constraint: { stiffness: 0.2, render: { visible: false } }
  });
  Composite.add(engine.world, mouseConstraint);
  render.mouse = mouse;

  // Kontrol input
  const slopeAngleInput = document.getElementById('slopeAngle');
  const slopeAngleValue = document.getElementById('slopeAngleValue');
  const boxMassInput = document.getElementById('boxMass');
  const boxCountInput = document.getElementById('boxCount');
  const resetBtn = document.getElementById('resetBtn');

  // Variabel untuk kotak dan label gaya gesek
  let boxes = [];
  let forceLabels = [];

  // Koefisien gesek
  const rollingFrictionCoeff = 0.05;
  const speedThreshold = 0.000000001;
  const angularThreshold = 0.02;

  // Fungsi buat kotak baru sesuai jumlah dan massa
  function createBoxes(count, mass) {
    // Hapus kotak lama dan labelnya
    boxes.forEach(b => Composite.remove(engine.world, b));
    boxes = [];
    forceLabels.forEach(label => label.remove());
    forceLabels = [];

    for (let i = 0; i < count; i++) {
      const b = Bodies.rectangle(150 + i * 70, 100, 60, 60, {
        friction: 0.2,
        restitution: 0.1,
        mass: mass,
        render: { fillStyle: '#c4b5fd' }
      });
      boxes.push(b);

      // Buat label gaya gesek untuk tiap kotak
      const label = document.createElement('div');
      label.className = 'forceLabel';
      label.style.display = 'none';
      document.body.appendChild(label);
      forceLabels.push(label);
    }
    Composite.add(engine.world, boxes);
  }

  // Fungsi update sudut slope
  function updateSlopeAngle(deg) {
    const rad = deg * Math.PI / 180;
    Body.setAngle(slope, rad);
  }

  // Fungsi dapatkan friction coefficient bidang kontak untuk tiap box
  function getContactFrictionForBox(box) {
    const pairs = engine.pairs.list;
    for (const pair of pairs) {
      if ((pair.bodyA === box && (pair.bodyB === ground || pair.bodyB === slope)) ||
          (pair.bodyB === box && (pair.bodyA === ground || pair.bodyA === slope))) {
        return (pair.bodyA === box) ? pair.bodyB.friction : pair.bodyA.friction;
      }
    }
    return 0;
  }

  // Reset simulasi sesuai input
  function resetSimulation() {
    const angleDeg = parseFloat(slopeAngleInput.value);
    const mass = parseFloat(boxMassInput.value);
    const count = parseInt(boxCountInput.value);

    updateSlopeAngle(angleDeg);
    createBoxes(count, mass);
  }

  // Event input
  slopeAngleInput.addEventListener('input', () => {
    slopeAngleValue.textContent = slopeAngleInput.value;
  });

  resetBtn.addEventListener('click', resetSimulation);

  // Inisialisasi awal
  resetSimulation();

  // Update gaya gesek dan label tiap frame
  Events.on(engine, 'afterUpdate', () => {
    const g = engine.world.gravity.y * (engine.gravity.scale || 1);
    const angle = slope.angle;

    boxes.forEach((box, i) => {
      const frictionCoeff = getContactFrictionForBox(box);
      if (frictionCoeff === 0) {
        forceLabels[i].style.display = 'none';
        return;
      }

        const v = box.velocity;
        const angle = slope.angle;
        const dirX = Math.cos(angle);
        const dirY = Math.sin(angle);
        const speedAlongSlope = Math.abs(v.x * dirX + v.y * dirY);

      const angularSpeed = Math.abs(box.angularVelocity);

      const mass = box.mass;
      // Normal force = m * g * cos(theta)
      const normalForce = mass * g * Math.cos(angle);

      let gayaGesek = 0;
      let kondisi = '';
        if (speedAlongSlope <= speedThreshold) {
        gayaGesek = 0;
        kondisi = 'Diam';
        } else if (angularSpeed > angularThreshold) {
        gayaGesek = rollingFrictionCoeff * normalForce;
        kondisi = 'Menggelinding';
        } else {
        gayaGesek = frictionCoeff * normalForce;
        kondisi = 'Meluncur';
        }


      forceLabels[i].style.display = 'block';
      forceLabels[i].textContent = `Gaya gesek (${kondisi}): ${gayaGesek.toFixed(5)} N`;

      const pos = box.position;
      const canvasRect = render.canvas.getBoundingClientRect();
      const scaleX = canvasRect.width / render.options.width;
      const scaleY = canvasRect.height / render.options.height;
      const x = canvasRect.left + pos.x * scaleX + 40;
      const y = canvasRect.top + pos.y * scaleY - 20;
      forceLabels[i].style.left = x + 'px';
      forceLabels[i].style.top = y + 'px';
    });
  });

  // Resize handler agar canvas dan ground menyesuaikan ukuran jendela
  window.addEventListener('resize', () => {
    width = window.innerWidth;
    height = window.innerHeight;
    render.canvas.width = width;
    render.canvas.height = height;
    Render.lookAt(render, { min: {x:0,y:0}, max: {x:width,y:height} });
    Body.setPosition(ground, { x: width/2, y: height - 40 });
    Body.setVertices(ground, [
      { x: 0, y: height - 80 },
      { x: width, y: height - 80 },
      { x: width, y: height },
      { x: 0, y: height }
    ]);
  });

  const mainButton = document.getElementById('mainButton');
  const submenu = document.getElementById('submenu');

  mainButton.addEventListener('click', () => {
    console.log('Main button clicked');
    submenu.classList.toggle('show');
  });

  // Optional: klik di luar dropdown untuk menutup submenu
  window.addEventListener('click', (e) => {
    if (!document.getElementById('dropdownMenu').contains(e.target)) {
      submenu.classList.remove('show');
    }
  });

</script>

</body>
</html>


