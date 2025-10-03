<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Simulasi Jungkat-Jungkit Massa dengan Sandaran</title>
<style>
  body, html { margin:0; padding:0; overflow:hidden; background:#f0f0f0; font-family:sans-serif; }
  #container { position: relative; width: 100vw; height: 100vh; }
  canvas { display: block; background: #fff; margin: 0 auto; }

  #sidebar {
    position: fixed;
    top: 10px; left: 10px;
    width: 220px;
    background: rgba(255,255,255,0.95);
    border-radius: 8px;
    padding: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    user-select: none;
    z-index: 10;
  }
  #sidebar h2 {
    margin-top: 0;
    font-size: 18px;
    text-align: center;
  }
  .massItem {
    background: #6c757d;
    color: white;
    padding: 8px 12px;
    margin: 6px 0;
    border-radius: 6px;
    cursor: grab;
    text-align: center;
    user-select: none;
  }
  .massItem:active {
    cursor: grabbing;
  }
  #mysteryMass {
    margin-top: 20px;
    font-weight: bold;
    font-size: 16px;
    text-align: center;
  }
  #info {
    margin-top: 20px;
    font-size: 14px;
    color: #333;
  }
  #resetBtn {
    margin-top: 15px;
    width: 100%;
    padding: 8px;
    border: none;
    background: #4a4e69;
    color: white;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
  }
  #resetBtn:hover {
    background: #6c70a1;
  }
</style>
</head>
<body>

<div id="sidebar">
  <h2>Objek Massa</h2>
  <div class="massItem" data-mass="1">Benda 1 kg</div>
  <div class="massItem" data-mass="2">Benda 2 kg</div>
  <div class="massItem" data-mass="3">Benda 3 kg</div>

  <div id="mysteryMass">Massa Misterius: <span id="mysteryValue">?</span> kg</div>

  <div id="info">Tarik benda ke jungkat-jungkit.<br>Seimbangkan jungkat-jungkit dengan massa misterius.</div>

  <button id="resetBtn">Reset Simulasi</button>
</div>

<div id="container"></div>

<script src="https://cdn.jsdelivr.net/npm/matter-js@0.19.0/build/matter.min.js"></script>
<script>
  const { Engine, Render, Runner, Bodies, Composite, Body, Events, Mouse, MouseConstraint, Constraint } = Matter;

  const container = document.getElementById('container');
  const mysteryValueSpan = document.getElementById('mysteryValue');
  const resetBtn = document.getElementById('resetBtn');

  const engine = Engine.create();
  const world = engine.world;
  world.gravity.y = 1;

  const width = window.innerWidth;
  const height = window.innerHeight;

  const render = Render.create({
    element: container,
    engine: engine,
    options: {
      width,
      height,
      wireframes: false,
      background: '#fff'
    }
  });

  Render.run(render);
  const runner = Runner.create();
  Runner.run(runner, engine);

  // Jungkat-jungkit
  const plankLength = 600;
  const plankHeight = 20;
  const pivotRadius = 15;
  const pivotX = width / 2;
  const pivotY = height / 2 + 100;

  const plankBody = Bodies.rectangle(pivotX, pivotY, plankLength, plankHeight, {
    chamfer: { radius: 10 },
    friction: 0.8,
    frictionStatic: 1,
    density: 0.004,
    render: { fillStyle: '#4a4e69' }
  });

  const pivotBody = Bodies.circle(pivotX, pivotY + plankHeight / 2 + pivotRadius, pivotRadius, {
    isStatic: true,
    render: { fillStyle: '#222' }
  });
   const bumperWidth = 10;
  const bumperHeight = 60;
  const bumperOffsetX = -plankLength / 2 + bumperWidth / 2;
  const bumperOffsetY = -plankHeight / 2 - bumperHeight / 2;

  const bumperLeft = Bodies.rectangle(
    pivotX + bumperOffsetX,
    pivotY + bumperOffsetY,
    bumperWidth,
    bumperHeight,
    { render: { fillStyle: '#222' } }
  );

  const bumperRight = Bodies.rectangle(
    pivotX - bumperOffsetX,
    pivotY + bumperOffsetY,
    bumperWidth,
    bumperHeight,
    { render: { fillStyle: '#222' } }
  );

  const bumperMiddleLeft = Bodies.rectangle(
    pivotX + bumperOffsetX + 110,
    pivotY + bumperOffsetY,
    bumperWidth,
    bumperHeight,
    { render: { fillStyle: '#222' } }
  );

  const bumperMiddleRight = Bodies.rectangle(
    pivotX - bumperOffsetX - 80,
    pivotY + bumperOffsetY,
    bumperWidth,
    bumperHeight,
    { render: { fillStyle: '#222' } }
  );
  const plank = Body.create({
    parts: [plankBody, pivotBody, bumperLeft, bumperRight, bumperMiddleLeft, bumperMiddleRight],
    friction: 0.8,
    frictionStatic: 1,
    density: 0.004,
    restitution: 0
  });
 


  const pivotConstraint = Constraint.create({
    pointA: { x: pivotX, y: pivotY + plankHeight / 2 + pivotRadius },
    bodyB: plank,
    pointB: { x: 0, y: plankHeight / 2 + pivotRadius },
    length: 0,
    stiffness: 1
  });

  Composite.add(world, [plank, pivotConstraint]);

  // Mystery object
  const mysteryMass = 5;
  const mysterySize = 60;
  const mysteryX = pivotX + plankLength / 2 - mysterySize / 2 - 5;
  const mysteryY = pivotY - plankHeight / 2 - mysterySize / 2 - 20;

  const mysteryObj = Bodies.rectangle(mysteryX, mysteryY, mysterySize, mysterySize, {
    render: { fillStyle: '#d90429' }
  });
  mysteryObj.massValue = mysteryMass;
  Composite.add(world, mysteryObj);

  let placedObjects = [];

  function createMassObject(mass, x, y) {
    const size = 40;
    const body = Bodies.rectangle(x, y, size, size, {
      mass: mass,
      friction: 1,
      frictionStatic: 1,
      restitution: 0,
      density: 0.01,
      chamfer: { radius: 5 },
      render: { fillStyle: '#4361ee' },
      frictionAir: 0.05,
      angularDamping: 0.9
    });
    body.massValue = mass;
    body.labelText = mass + "kg";
    return body;
  }

  Events.on(render, 'afterRender', () => {
    const ctx = render.context;
    placedObjects.forEach(obj => {
      if (obj.labelText) {
        ctx.fillStyle = '#fff';
        ctx.font = "14px Arial";
        ctx.textAlign = "center";
        ctx.fillText(obj.labelText, obj.position.x, obj.position.y + 5);
      }
    });
  });

  function snapToPlank(x, plank) {
    const slotSize = 50;
    const relativeX = x - plank.position.x;
    const snappedX = Math.round(relativeX / slotSize) * slotSize;
    return {
      x: plank.position.x + snappedX,
      y: plank.position.y - 40 // spawn langsung di atas plank
    };
  }

  // Klik tombol spawn
  const massItems = document.querySelectorAll('.massItem');
  massItems.forEach(item => {
    item.addEventListener('click', e => {
      e.preventDefault();
      const mass = parseFloat(item.dataset.mass);
      const snapped = snapToPlank(pivotX, plank);
      const newObj = createMassObject(mass, snapped.x, snapped.y);
      Composite.add(world, newObj);
      placedObjects.push(newObj);
    });
  });

  // Mouse drag & drop (slider style)
  const mouse = Mouse.create(render.canvas);
  const mouseConstraint = MouseConstraint.create(engine, {
    mouse,
    constraint: {
      stiffness: 0.2,
      render: { visible: false }
    }
  });
  Composite.add(world, mouseConstraint);
  render.mouse = mouse;

  // Kalkulasi momen
  function calculateMoment() {
    let leftMoment = 0;
    let rightMoment = 0;

    placedObjects.forEach(obj => {
      const dx = obj.position.x - pivotX;
      const dy = obj.position.y - pivotY;
      const mass = obj.massValue || 0;
      const distance = Math.sqrt(dx * dx + dy * dy);
      if (dx < 0) leftMoment += mass * distance;
      else rightMoment += mass * distance;
    });

    const dxMystery = mysteryObj.position.x - pivotX;
    const dyMystery = mysteryObj.position.y - pivotY;
    const distMystery = Math.sqrt(dxMystery * dxMystery + dyMystery * dyMystery);
    rightMoment += mysteryMass * distMystery;

    return { leftMoment, rightMoment };
  }

  let balanceTimer = null;
  Events.on(engine, 'afterUpdate', () => {
    const { leftMoment, rightMoment } = calculateMoment();
    const maxAngle = 0.3;
    const tolerance = 10;
    const momentDiff = rightMoment - leftMoment;

    let targetAngle = Math.max(-maxAngle, Math.min(maxAngle, momentDiff * 0.0005));
    Body.setAngle(plank, targetAngle);
    plank.angularVelocity *= 0.8;
    plank.angularDamping = 0.9;

    placedObjects.forEach(obj => {
      obj.angularVelocity *= 0.7;
      obj.angularDamping = 0.9;
    });

    const totalUserMass = placedObjects.reduce((sum, obj) => sum + (obj.massValue || 0), 0);
    const info = document.getElementById('info');

    if (Math.abs(momentDiff) <= tolerance) {
      info.innerHTML = '<b>Jungkat-jungkit seimbang!</b><br>Pertahankan selama 10 detik...';

      if (!balanceTimer) {
        balanceTimer = setTimeout(() => {
          if (Math.abs(momentDiff) <= tolerance) {
            if (totalUserMass === mysteryMass) {
              info.innerHTML = '<b>Benar!</b><br>Massa sama dengan misterius 🎉';
              mysteryValueSpan.textContent = mysteryMass;
            } else {
              info.innerHTML = '<b>Seimbang tapi salah.</b><br>Massa belum sama dengan misterius.';
            }
          }
          balanceTimer = null;
        }, 10000);
      }
    } else {
      if (balanceTimer) {
        clearTimeout(balanceTimer);
        balanceTimer = null;
      }
      info.innerHTML = momentDiff > 0
        ? 'Jungkat-jungkit miring ke kanan.<br>Tambahkan massa di kiri atau kurangi di kanan.'
        : 'Jungkat-jungkit miring ke kiri.<br>Tambahkan massa di kanan atau kurangi di kiri.';
    }
  });

  function resetSimulation() {
    placedObjects.forEach(obj => Composite.remove(world, obj));
    placedObjects = [];
    Body.setAngle(plank, 0);
    plank.angularVelocity = 0;
    plank.position.x = pivotX;
    plank.position.y = pivotY;
    Body.setPosition(mysteryObj, { x: mysteryX, y: mysteryY });
    Body.setVelocity(mysteryObj, { x: 0, y: 0 });
    Body.setAngularVelocity(mysteryObj, 0);
  }

  resetBtn.addEventListener('click', resetSimulation);

  window.addEventListener('resize', () => {
    const w = window.innerWidth;
    const h = window.innerHeight;
    render.canvas.width = w;
    render.canvas.height = h;
    Render.lookAt(render, { min: { x: 0, y: 0 }, max: { x: w, y: h } });
  });
</script>


</body>
</html>