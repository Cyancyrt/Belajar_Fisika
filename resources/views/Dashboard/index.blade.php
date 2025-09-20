<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Simulasi Box Menggelinding vs Meluncur</title>
<style>
  body, html { margin:0; padding:0; overflow:hidden; }
  canvas { display:block; background:#eaeaea; }
  #controls {
    position: fixed;
    top: 10px; left: 10px;
    background: rgba(255,255,255,0.9);
    padding: 10px;
    border-radius: 8px;
    font-family: sans-serif;
    z-index: 10;
    user-select: none;
  }
  #controls label {
    display: block;
    margin-bottom: 6px;
  }
  .forceLabel {
    position: absolute;
    background: rgba(0,0,0,0.7);
    color: #f9f9f9;
    font-size: 12px;
    padding: 2px 6px;
    border-radius: 4px;
    pointer-events: none;
    user-select: none;
    font-family: monospace;
    white-space: nowrap;
  }
  #dropdownMenu {
    position: fixed;
    top: 10px;
    right: 10px;
    z-index: 20;
    user-select: none;
  }

  #mainButton {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: none;
    background-color: #4a4e69;
    color: white;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    transition: background-color 0.3s;
  }

  #mainButton:hover {
    background-color: #6c70a1;
  }

  #submenu {
    position: absolute;
    bottom: 60px;
    right: 0;
    top: 60px;   /* turun dari main button */
    display: none; /* awalnya tidak tampil */
    flex-direction: column;
    gap: 10px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s;
  }

  #submenu.show {
    display: flex;          /* tampil */
    opacity: 1;
    pointer-events: auto;
  }

  .subButton {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background-color: #9a8c98;
    color: white;
    font-size: 18px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    transition: background-color 0.3s;
  }

  .subButton:hover {
    background-color: #c9ada7;
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


