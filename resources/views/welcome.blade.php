<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Your Name — Laravel Developer &amp; Designer</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,500;0,9..144,600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<style>
  :root{
    --space:#05060A;
    --space-soft:#0C0E16;
    --paper:#EDEBF2;
    --paper-dim:#9C9AB0;
    --coral:#FF5F45;
    --line:rgba(237,235,242,0.09);
  }
  *{box-sizing:border-box;}
  html{scroll-behavior:smooth;}
  body{margin:0;background:var(--space);color:var(--paper);font-family:'Inter',sans-serif;overflow-x:hidden;}
  @media (prefers-reduced-motion: reduce){*{animation-duration:0.01ms !important;}}

  .wrap{max-width:1080px;margin:0 auto;padding:0 32px;}

  /* ---------- 3D HERO ---------- */
  .hero{position:relative;height:100vh;height:100svh;min-height:560px;overflow:hidden;}
  #heroCanvas{position:absolute;inset:0;z-index:0;}
  .hero-overlay{
    position:relative;z-index:1;height:100%;display:flex;flex-direction:column;
    justify-content:center;pointer-events:none;
  }
  .hero-overlay .wrap{pointer-events:auto;}
  .tag{
    font-size:14px;color:var(--coral);font-weight:500;
    display:inline-flex;align-items:center;gap:8px;margin-bottom:26px;
  }
  .tag::before{content:'';width:6px;height:6px;border-radius:50%;background:var(--coral);}
  h1{
    font-family:'Fraunces',serif;font-weight:500;
    font-size:clamp(38px,6vw,72px);line-height:1.04;letter-spacing:-0.01em;
    margin:0 0 26px;max-width:16ch;
  }
  h1 span.line{display:block;overflow:hidden;}
  h1 span.line b{
    display:block;font-weight:500;transform:translateY(110%);
    animation:rise 0.9s cubic-bezier(.22,1,.36,1) forwards;
  }
  h1 span.line:nth-child(1) b{animation-delay:.4s;}
  h1 span.line:nth-child(2) b{animation-delay:.53s;}
  @keyframes rise{to{transform:translateY(0);}}
  .lede{font-size:18px;line-height:1.65;color:var(--paper-dim);max-width:50ch;margin:0 0 36px;
    opacity:0;animation:fade .8s ease forwards;animation-delay:.8s;}
  @keyframes fade{to{opacity:1;}}
  .cta-row{display:flex;gap:22px;flex-wrap:wrap;opacity:0;animation:fade .8s ease forwards;animation-delay:.95s;}
  .btn{background:var(--coral);color:#05060A;text-decoration:none;font-weight:600;font-size:15px;
    padding:14px 24px;border-radius:8px;display:inline-flex;transition:transform .2s ease;}
  .btn:hover{transform:translateY(-2px);}
  .btn-ghost{color:var(--paper);text-decoration:none;font-weight:500;font-size:15px;padding:14px 4px;
    border-bottom:1px solid var(--line);display:inline-flex;align-items:center;gap:8px;transition:border-color .2s;}
  .btn-ghost:hover{border-color:var(--paper);}
  .scroll-hint{
    position:absolute;bottom:32px;left:32px;font-size:12.5px;color:var(--paper-dim);
    display:flex;align-items:center;gap:8px;opacity:0;animation:fade .8s ease forwards;animation-delay:1.2s;
  }
  .scroll-hint .dot{width:6px;height:10px;border:1px solid var(--paper-dim);border-radius:3px;position:relative;}
  .scroll-hint .dot::after{content:'';position:absolute;top:2px;left:1.5px;width:2px;height:3px;background:var(--paper-dim);border-radius:1px;animation:scrolldot 1.6s ease infinite;}
  @keyframes scrolldot{0%,100%{transform:translateY(0);opacity:1;}60%{transform:translateY(4px);opacity:0;}}

  /* ---------- SECTIONS ---------- */
  section{padding:80px 0;border-bottom:1px solid var(--line);}
  .sec-head h2{font-family:'Fraunces',serif;font-weight:500;font-size:28px;margin:0 0 8px;}
  .sec-head p{color:var(--paper-dim);font-size:15px;margin:0 0 40px;max-width:44ch;}

  .stat-row{display:flex;gap:56px;flex-wrap:wrap;margin-bottom:36px;}
  .stat{display:flex;flex-direction:column;gap:6px;}
  .stat-num{font-family:'Fraunces',serif;font-size:36px;font-weight:500;color:var(--coral);}
  .stat-label{font-size:13.5px;color:var(--paper-dim);max-width:20ch;}
  .about-copy{font-size:17px;line-height:1.7;color:var(--paper-dim);max-width:62ch;margin:0;}

  /* tilt cards */
  .tilt-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:24px;perspective:1000px;}
  @media (max-width:700px){.tilt-grid{grid-template-columns:1fr;}}
  .tilt-card{
    background:var(--space-soft);border:1px solid var(--line);border-radius:14px;
    padding:26px;transform-style:preserve-3d;transition:transform .15s ease,border-color .2s ease;
    will-change:transform;
  }
  .tilt-card:hover{border-color:rgba(255,95,69,0.35);}
  .tilt-card .mark{width:8px;height:8px;border-radius:2px;background:var(--coral);margin-bottom:16px;}
  .tilt-card h3{font-size:16px;margin:0 0 8px;font-weight:600;}
  .tilt-card p{font-size:14.5px;line-height:1.6;color:var(--paper-dim);margin:0;}

  .demo-grid{display:grid;grid-template-columns:1.1fr 1fr;gap:28px;}
  @media (max-width:820px){
    .demo-grid{grid-template-columns:1fr;}
    section{padding:64px 0;}
  }
  @media (max-width:700px){
    .tilt-grid{grid-template-columns:1fr;}
    .stat-row{gap:36px;}
    .footer-top{grid-template-columns:1fr;gap:32px;}
  }
  .media-card{background:var(--space-soft);border:1px solid var(--line);border-radius:12px;overflow:hidden;position:relative;
    transform-style:preserve-3d;transition:transform .15s ease;will-change:transform;}
  .media-card .label{position:absolute;top:16px;left:16px;z-index:2;font-size:12px;background:rgba(5,6,10,.7);
    backdrop-filter:blur(6px);padding:5px 10px;border-radius:100px;border:1px solid var(--line);}
  .media-card img,.media-card video{display:block;width:100%;height:340px;object-fit:cover;}
  .play-btn{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(5,6,10,.25);
    z-index:1;cursor:pointer;border:none;padding:0;transition:background .2s;}
  .play-btn:hover{background:rgba(5,6,10,.1);}
  .play-btn .circle{width:60px;height:60px;border-radius:50%;background:var(--paper);display:flex;align-items:center;justify-content:center;transition:transform .2s;}
  .play-btn:hover .circle{transform:scale(1.08);}
  .play-btn.hidden{display:none;}

  section.contact{text-align:center;}
  section.contact h2{font-family:'Fraunces',serif;font-weight:500;font-size:clamp(26px,3.4vw,36px);margin:0 0 14px;}
  section.contact p{color:var(--paper-dim);font-size:16px;margin:0 0 32px;}

  footer{padding:64px 0 40px;}
  .footer-top{display:grid;grid-template-columns:1.3fr 1fr 1fr;gap:40px;padding-bottom:44px;}
  @media (max-width:700px){.footer-top{grid-template-columns:1fr;gap:32px;}}
  .footer-brand h3{font-family:'Fraunces',serif;font-weight:500;font-size:22px;margin:0 0 12px;}
  .footer-brand p{color:var(--paper-dim);font-size:14.5px;line-height:1.6;margin:0;max-width:34ch;}
  .footer-col h4{font-size:13px;color:var(--paper-dim);font-weight:500;margin:0 0 16px;}
  .footer-col ul{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:12px;}
  .footer-col a{color:var(--paper-dim);text-decoration:none;font-size:14.5px;transition:color .2s;}
  .footer-col a:hover{color:var(--coral);}
  .footer-bottom{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;
    padding-top:26px;border-top:1px solid var(--line);font-size:13px;color:var(--paper-dim);}
  .footer-bottom .social{display:flex;gap:16px;}
  .footer-bottom .social a{color:var(--paper-dim);transition:color .2s,transform .2s;}
  .footer-bottom .social a:hover{color:var(--coral);transform:translateY(-2px);}

  /* ---------- SMALL SCREENS ---------- */
  @media (max-width:600px){
    .wrap{padding:0 20px;}
    .tag{font-size:12.5px;margin-bottom:20px;}
    h1{font-size:clamp(30px,8vw,40px);max-width:100%;margin-bottom:18px;}
    .lede{font-size:15.5px;margin-bottom:28px;}
    .cta-row{gap:14px;}
    .btn,.btn-ghost{font-size:14px;padding:12px 20px;}
    .scroll-hint{left:20px;bottom:20px;}

    section{padding:52px 0;}
    .sec-head h2{font-size:22px;}
    .sec-head p{font-size:14px;margin-bottom:28px;}

    .stat-row{gap:28px 36px;margin-bottom:28px;}
    .stat-num{font-size:28px;}
    .stat-label{font-size:12.5px;}
    .about-copy{font-size:15px;}

    .tilt-grid{gap:16px;}
    .tilt-card{padding:20px;}

    .demo-grid{gap:18px;}
    .media-card img,.media-card video{height:220px;}
    .play-btn .circle{width:48px;height:48px;}

    section.contact h2{font-size:24px;}
    section.contact .cta-row{flex-direction:column;align-items:center;}

    .footer-top{gap:28px;padding-bottom:32px;}
    .footer-brand h3{font-size:19px;}
    .footer-bottom{flex-direction:column;align-items:flex-start;gap:16px;}
  }

  @media (max-width:380px){
    h1{font-size:clamp(26px,9vw,32px);}
    .media-card img,.media-card video{height:180px;}
  }
</style>
</head>
<body>

<section class="hero">
  <canvas id="heroCanvas"></canvas>
  <div class="hero-overlay">
    <div class="wrap">
      <div class="tag">Now building with Laravel</div>
      <h1>
        <span class="line"><b>Hi, I'm Your Name.</b></span>
        <span class="line"><b>I build in three dimensions of code, design, and delivery.</b></span>
      </h1>
      <p class="lede">Laravel applications designed and built end to end — this hero is real, live 3D, rendered right in your browser.</p>
      <div class="cta-row">
        <a href="#demo" class="btn">See the demo</a>
        <a href="https://www.linkedin.com/in/your-profile" target="_blank" rel="noopener" class="btn-ghost">Connect on LinkedIn →</a>
      </div>
    </div>
    <div class="scroll-hint"><span class="dot"></span> Scroll</div>
  </div>
</section>

<div class="wrap">

  <section class="about" id="about">
    <div class="stat-row">
      <div class="stat"><span class="stat-num">4+</span><span class="stat-label">Years building with Laravel</span></div>
      <div class="stat"><span class="stat-num">30+</span><span class="stat-label">Applications shipped</span></div>
      <div class="stat"><span class="stat-num">100%</span><span class="stat-label">Designed and coded myself</span></div>
    </div>
    <p class="about-copy">Most developers hand off design, and most designers hand off the build. I do both, so the interface and the code are never fighting each other — what you see in the demo below is exactly what ships to production.</p>
  </section>

  <section class="skills" id="skills">
    <div class="sec-head"><h2>What I work with</h2><p>The stack I reach for by default, and why.</p></div>
    <div class="tilt-grid">
      <div class="tilt-card"><div class="mark"></div><h3>Laravel &amp; PHP</h3><p>Clean, testable backends — queues, auth, APIs, and everything in between.</p></div>
      <div class="tilt-card"><div class="mark"></div><h3>Interface design</h3><p>Layouts and type systems built for the content, not a template.</p></div>
      <div class="tilt-card"><div class="mark"></div><h3>Front-end integration</h3><p>Blade, Livewire, or Vue — wired to the backend with no rough edges.</p></div>
      <div class="tilt-card"><div class="mark"></div><h3>Deployment</h3><p>Servers, CI, and databases configured so the app stays fast under load.</p></div>
    </div>
  </section>

  <section class="demo" id="demo">
    <div class="sec-head"><h2>A look at the product</h2><p>One screenshot of the interface, and a short walkthrough of it in use.</p></div>
    <div class="demo-grid">
      <div class="media-card">
        <span class="label">Dashboard, live data</span>
        <!-- STAND-IN — replace with your real screenshot -->
        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=900&q=80&auto=format&fit=crop" alt="Application dashboard screenshot">
      </div>
      <div class="media-card">
        <span class="label">60s walkthrough</span>
        <!-- STAND-IN — replace with your real recording -->
        <video id="demoVideo" playsinline preload="metadata" poster="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=900&q=80&auto=format&fit=crop">
          <source src="https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4" type="video/mp4">
        </video>
        <button class="play-btn" id="playBtn" aria-label="Play video">
          <span class="circle"><svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M6 4L16 10L6 16V4Z" fill="#05060A"/></svg></span>
        </button>
      </div>
    </div>
  </section>

  <section class="contact" id="contact">
    <h2>Have a Laravel project in mind?</h2>
    <p>I take on a small number of projects at a time — send a message and I'll reply within a day.</p>
    <div class="cta-row" style="justify-content:center;">
      <a href="mailto:you@example.com" class="btn">Email me</a>
      <a href="https://www.linkedin.com/in/your-profile" target="_blank" rel="noopener" class="btn-ghost">Message on LinkedIn →</a>
    </div>
  </section>

  <footer>
    <div class="footer-top">
      <div class="footer-brand">
        <h3>Your Name</h3>
        <p>Laravel developer &amp; designer. I build applications where the backend, the interface, and the interaction all come from the same hand.</p>
      </div>
      <div class="footer-col">
        <h4>Explore</h4>
        <ul>
          <li><a href="#demo">Product demo</a></li>
          <li><a href="https://your-portfolio-site.com" target="_blank" rel="noopener">Full portfolio</a></li>
          <li><a href="https://github.com/your-username" target="_blank" rel="noopener">GitHub</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Get in touch</h4>
        <ul>
          <li><a href="mailto:you@example.com">you@example.com</a></li>
          <li><a href="https://www.linkedin.com/in/your-profile" target="_blank" rel="noopener">LinkedIn</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© 2026 Your . Built with .</span>
      <div class="social">
        <a href="https://www.linkedin.com/in/your-profile" target="_blank" rel="noopener" aria-label="LinkedIn"><svg width="19" height="19" viewBox="0 0 24 24" fill="currentColor"><path d="M20.45 20.45h-3.55v-5.57c0-1.33-.02-3.03-1.85-3.03-1.85 0-2.14 1.45-2.14 2.94v5.66H9.36V9h3.41v1.56h.05c.47-.9 1.63-1.85 3.36-1.85 3.6 0 4.27 2.37 4.27 5.45v6.29zM5.34 7.43a2.06 2.06 0 1 1 0-4.12 2.06 2.06 0 0 1 0 4.12zM7.12 20.45H3.56V9h3.56v11.45z"/></svg></a>
        <a href="https://github.com/your-username" target="_blank" rel="noopener" aria-label="GitHub"><svg width="19" height="19" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.58 2 12.25c0 4.53 2.87 8.37 6.84 9.73.5.1.68-.22.68-.49 0-.24-.01-1.04-.01-1.89-2.78.62-3.37-1.21-3.37-1.21-.46-1.2-1.11-1.52-1.11-1.52-.91-.64.07-.63.07-.63 1 .07 1.53 1.05 1.53 1.05.89 1.56 2.34 1.11 2.91.85.09-.66.35-1.11.63-1.37-2.22-.26-4.56-1.14-4.56-5.06 0-1.12.39-2.03 1.03-2.75-.1-.26-.45-1.31.1-2.72 0 0 .84-.28 2.75 1.05a9.3 9.3 0 0 1 5 0c1.91-1.33 2.75-1.05 2.75-1.05.55 1.41.2 2.46.1 2.72.64.72 1.03 1.63 1.03 2.75 0 3.93-2.34 4.79-4.57 5.05.36.32.68.94.68 1.9 0 1.37-.01 2.47-.01 2.81 0 .27.18.6.69.49A10.26 10.26 0 0 0 22 12.25C22 6.58 17.52 2 12 2z"/></svg></a>
        <a href="mailto:you@example.com" aria-label="Email"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><path d="M4 5h16v14H4V5zm0 0l8 7 8-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
      </div>
    </div>
  </footer>

</div>

<script>
  // ---------- 3D HERO (Three.js) ----------
  const canvas = document.getElementById('heroCanvas');
  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(50, window.innerWidth/window.innerHeight, 0.1, 100);
  camera.position.z = 9;
  const renderer = new THREE.WebGLRenderer({canvas, antialias:true, alpha:true});
  renderer.setSize(window.innerWidth, window.innerHeight);
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

  const group = new THREE.Group();
  scene.add(group);

  const coral = new THREE.Color(0xFF5F45);
  const dim = new THREE.Color(0x3A3A55);

  // central wireframe icosahedron
  const coreGeo = new THREE.IcosahedronGeometry(2.4, 1);
  const coreMat = new THREE.MeshBasicMaterial({color: coral, wireframe:true, transparent:true, opacity:0.85});
  const core = new THREE.Mesh(coreGeo, coreMat);
  group.add(core);

  // orbiting smaller shapes
  const orbiters = [];
  const shapeGeos = [
    new THREE.TorusGeometry(0.5, 0.15, 8, 24),
    new THREE.OctahedronGeometry(0.55),
    new THREE.TetrahedronGeometry(0.6)
  ];
  for(let i=0;i<3;i++){
    const mat = new THREE.MeshBasicMaterial({color: dim, wireframe:true, transparent:true, opacity:0.6});
    const mesh = new THREE.Mesh(shapeGeos[i], mat);
    const radius = 4 + i*0.6;
    mesh.userData = {radius, speed: 0.15 + i*0.08, angle: (i/3)*Math.PI*2, yOff: i*0.6};
    group.add(mesh);
    orbiters.push(mesh);
  }

  // particle field
  const particleCount = 220;
  const positions = new Float32Array(particleCount*3);
  for(let i=0;i<particleCount;i++){
    positions[i*3] = (Math.random()-0.5)*20;
    positions[i*3+1] = (Math.random()-0.5)*14;
    positions[i*3+2] = (Math.random()-0.5)*10 - 3;
  }
  const pGeo = new THREE.BufferGeometry();
  pGeo.setAttribute('position', new THREE.BufferAttribute(positions,3));
  const pMat = new THREE.PointsMaterial({color:0x9C9AB0, size:0.035, transparent:true, opacity:0.5});
  const particles = new THREE.Points(pGeo, pMat);
  scene.add(particles);

  let mouseX = 0, mouseY = 0;
  window.addEventListener('mousemove', (e)=>{
    mouseX = (e.clientX/window.innerWidth - 0.5);
    mouseY = (e.clientY/window.innerHeight - 0.5);
  });

  const clock = new THREE.Clock();
  function animate(){
    const t = clock.getElapsedTime();
    core.rotation.x = t*0.15;
    core.rotation.y = t*0.2;

    orbiters.forEach(m=>{
      const {radius, speed, angle, yOff} = m.userData;
      const a = angle + t*speed;
      m.position.x = Math.cos(a)*radius;
      m.position.z = Math.sin(a)*radius*0.4;
      m.position.y = Math.sin(t*0.4+yOff)*1.2;
      m.rotation.x += 0.01;
      m.rotation.y += 0.008;
    });

    particles.rotation.y = t*0.02;

    group.rotation.y += (mouseX*0.6 - group.rotation.y)*0.03;
    group.rotation.x += (mouseY*0.3 - group.rotation.x)*0.03;

    renderer.render(scene, camera);
    requestAnimationFrame(animate);
  }
  animate();

  window.addEventListener('resize', ()=>{
    camera.aspect = window.innerWidth/window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
  });

  // ---------- video play/pause ----------
  const video = document.getElementById('demoVideo');
  const playBtn = document.getElementById('playBtn');
  playBtn.addEventListener('click', ()=>{ video.play(); playBtn.classList.add('hidden'); });
  video.addEventListener('pause', ()=>playBtn.classList.remove('hidden'));
  video.addEventListener('ended', ()=>playBtn.classList.remove('hidden'));

  // ---------- 3D tilt on cards ----------
  document.querySelectorAll('.tilt-card, .media-card').forEach(card=>{
    card.addEventListener('mousemove', (e)=>{
      const r = card.getBoundingClientRect();
      const x = (e.clientX - r.left)/r.width - 0.5;
      const y = (e.clientY - r.top)/r.height - 0.5;
      card.style.transform = `rotateY(${x*8}deg) rotateX(${-y*8}deg) translateZ(4px)`;
    });
    card.addEventListener('mouseleave', ()=>{ card.style.transform = 'rotateY(0) rotateX(0)'; });
  });
</script>

</body>
</html>