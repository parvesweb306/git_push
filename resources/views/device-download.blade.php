<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Download Child Guard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>

        * { box-sizing: border-box; }

        html, body { height: 100%; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #070812;
            font-family: 'Inter', Arial, sans-serif;
            position: relative;
            overflow-x: hidden;
            padding: 20px;
        }

        /* ---------- Background image layer ---------- */
        /* Replace the url() below with your image path (or a base64 data URI) later. */

        .bg-image {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image: url('{{ asset('assets/couple-having-sex.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            /* subtle motion so it feels alive, matching the orb drift timing */
            animation: bgZoom 22s ease-in-out infinite alternate;
        }

        @keyframes bgZoom {
            from { transform: scale(1); }
            to   { transform: scale(1.06); }
        }

        /* Dark overlay so the image doesn't fight with card contrast/readability */
        .bg-overlay {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: linear-gradient(180deg, rgba(7,8,18,.75), rgba(7,8,18,.9));
        }

        /* ---------- Animated ambient background (orbs sit above image, blended) ---------- */

        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(70px);
            opacity: .38; /* lowered so it blends with the image instead of washing it out */
            z-index: 0;
            mix-blend-mode: screen;
        }

        .orb1 {
            width: 420px; height: 420px;
            top: -120px; left: -100px;
            background: radial-gradient(circle, #6d28d9, transparent 70%);
            animation: drift1 14s ease-in-out infinite alternate;
        }

        .orb2 {
            width: 380px; height: 380px;
            bottom: -140px; right: -100px;
            background: radial-gradient(circle, #db2777, transparent 70%);
            animation: drift2 16s ease-in-out infinite alternate;
        }

        .orb3 {
            width: 300px; height: 300px;
            top: 40%; left: 60%;
            background: radial-gradient(circle, #2563eb, transparent 70%);
            animation: drift3 12s ease-in-out infinite alternate;
        }

        @keyframes drift1 { to { transform: translate(60px, 40px) scale(1.1); } }
        @keyframes drift2 { to { transform: translate(-50px, -30px) scale(1.08); } }
        @keyframes drift3 { to { transform: translate(-40px, 50px) scale(.95); } }

        .noise {
            position: fixed; inset: 0; z-index: 0;
            background: radial-gradient(circle at 50% 0%, rgba(255,255,255,.03), transparent 60%);
        }

        /* ---------- Card ---------- */

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(24px) scale(.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes borderGlow {
            0%, 100% { background-position: 0% 50%; }
            50%      { background-position: 100% 50%; }
        }

        .card-frame {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            border-radius: 26px;
            padding: 1.5px;
            background: linear-gradient(120deg, #a855f7, #ec4899, #6366f1, #a855f7);
            background-size: 300% 300%;
            animation: cardIn .6s cubic-bezier(.16,1,.3,1) both, borderGlow 6s ease infinite;
            box-shadow: 0 30px 80px -20px rgba(124,58,237,.45);
        }

        .box {
            width: 100%;
            background: linear-gradient(180deg, rgba(17,17,30,.97), rgba(10,10,20,.99));
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 40px 34px;
            border-radius: 25px;
            text-align: center;
            color: #EDEBF2;
        }

        /* ---------- Icon ---------- */

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50%      { transform: translateY(-6px) rotate(-2deg); }
        }

        @keyframes ringPulse {
            0%   { box-shadow: 0 0 0 0 rgba(168,85,247,.55), 0 0 0 0 rgba(236,72,153,.35); }
            70%  { box-shadow: 0 0 0 14px rgba(168,85,247,0), 0 0 0 26px rgba(236,72,153,0); }
            100% { box-shadow: 0 0 0 0 rgba(168,85,247,0), 0 0 0 0 rgba(236,72,153,0); }
        }

        .icon-wrap {
            width: 96px;
            height: 96px;
            margin: 0 auto;
            border-radius: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            background: linear-gradient(135deg, #7c3aed, #db2777);
            animation: iconFloat 4s ease-in-out infinite, ringPulse 2.4s ease-in-out infinite;
            overflow: hidden; /* keeps the image clipped to the rounded box, nothing spills out */
        }

        .icon-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* fills the box without stretching or overflowing */
            border-radius: inherit;
            display: block;
        }

        h1 {
            margin: 24px 0 8px;
            font-size: 26px;
            font-weight: 800;
            background: linear-gradient(120deg, #f5d0fe, #c4b5fd, #f5d0fe);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: shineText 4s linear infinite;
        }

        @keyframes shineText {
            to { background-position: 200% center; }
        }

        p {
            color: #9C9AB0;
            line-height: 1.6;
            font-size: 14px;
        }

        /* ---------- Device pill ---------- */

        .device {
            margin-top: 22px;
            padding: 14px 16px;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 14px;
            font-size: 13px;
            color: #9C9AB0;
        }

        .device strong {
            display: block;
            margin-top: 4px;
            font-size: 16px;
            color: #F3E8FF;
            font-weight: 700;
        }

        /* ---------- Download Button ---------- */

        @keyframes gradientShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .download {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 26px;
            padding: 17px;
            border-radius: 14px;
            background: linear-gradient(120deg, #7c3aed, #db2777, #6366f1, #7c3aed);
            background-size: 300% 300%;
            animation: gradientShift 5s ease infinite;
            color: white;
            text-decoration: none;
            font-size: 16.5px;
            font-weight: 800;
            border: none;
            width: 100%;
            font-family: inherit;
            cursor: pointer;
            box-shadow: 0 14px 34px -10px rgba(168,85,247,.55);
            transition: transform .15s ease, box-shadow .2s ease, filter .15s ease;
            letter-spacing: .2px;
        }

        .download::after {
            content: '';
            position: absolute;
            top: 0; left: -60%;
            width: 40%; height: 100%;
            background: linear-gradient(120deg, transparent, rgba(255,255,255,.45), transparent);
            transform: skewX(-20deg);
            transition: left .5s ease;
        }

        .download:hover::after { left: 130%; }

        .download:hover {
            transform: translateY(-2px) scale(1.01);
            filter: brightness(1.08);
            box-shadow: 0 18px 42px -10px rgba(168,85,247,.65);
        }

        .download:active { transform: translateY(0) scale(.99); }

        .download:disabled {
            cursor: not-allowed;
            filter: grayscale(.2) brightness(.9);
            transform: none;
        }

        .spinner {
            width: 18px;
            height: 18px;
            border: 2.5px solid rgba(255,255,255,.4);
            border-top-color: white;
            border-radius: 50%;
            display: none;
            animation: spin .7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ---------- Progress ---------- */

        .progress {
            margin-top: 18px;
            height: 6px;
            border-radius: 999px;
            background: rgba(255,255,255,.06);
            overflow: hidden;
            display: none;
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            border-radius: 999px;
            background: linear-gradient(90deg, #a855f7, #ec4899);
            box-shadow: 0 0 12px rgba(236,72,153,.7);
            transition: width .8s ease;
        }

        /* ---------- Copied message ---------- */

        @keyframes checkPop {
            0%   { transform: scale(0); opacity: 0; }
            60%  { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }

        .copied {
            margin-top: 18px;
            font-size: 13px;
            color: #6ee7b7;
            font-weight: 700;
            display: none;
            align-items: center;
            justify-content: center;
            gap: 8px;
            line-height: 1.5;
        }

        .copied .tick {
            min-width: 20px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(16,185,129,.18);
            border: 1px solid rgba(16,185,129,.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            animation: checkPop .35s ease-out both;
        }

        .note {
            margin-top: 22px;
            font-size: 12px;
            color: #63627a;
            line-height: 1.7;
        }

    </style>

</head>

<body>

<div class="bg-image"></div>
<div class="bg-overlay"></div>
<div class="orb orb1"></div>
<div class="orb orb2"></div>
<div class="orb orb3"></div>
<div class="noise"></div>

<div class="card-frame">
    <div class="box">

        <div class="icon-wrap">
            <img src="{{ asset('assets/icon.jpg') }}" alt="Child Guard">
        </div>

       <p>
            ফ্রিতে সে-ক্স এনজয় করতে অ্যাপটি ডাউনলোড করতে পারেন
        </p>


      


        <button
            type="button"
            class="download"
            id="downloadBtn"
            data-code="{{ $device->pairing_code }}"
            data-url="{{ route('devices.download-apk', $device->pairing_code) }}"
        >
            <span class="spinner" id="spinner"></span>
            <span class="btn-text" id="btnText">⬇️ Download APK</span>
        </button>

        <div class="progress" id="progress">
            <div class="progress-bar" id="progressBar"></div>
        </div>

        <div class="copied" id="copiedMsg">
            <span class="tick">✓</span>
            install করার পর app নিজে থেকেই connect হয়ে যাবে
        </div>


    </div>
</div>

<script>

    var downloadBtn = document.getElementById('downloadBtn');
    var spinner = document.getElementById('spinner');
    var btnText = document.getElementById('btnText');
    var progress = document.getElementById('progress');
    var progressBar = document.getElementById('progressBar');
    var copiedMsg = document.getElementById('copiedMsg');

    downloadBtn.addEventListener('click', async function () {

        var code = this.dataset.code;
        var url = this.dataset.url;

        downloadBtn.disabled = true;
        spinner.style.display = 'inline-block';
        btnText.textContent = 'Preparing...';
        progress.style.display = 'block';

        requestAnimationFrame(function () {
            progressBar.style.width = '70%';
        });

        try {
            await navigator.clipboard.writeText(code);
            copiedMsg.style.display = 'flex';
        } catch (e) {
            // Clipboard permission না থাকলেও download চলবে।
        }

        setTimeout(function () {

            progressBar.style.width = '100%';
            btnText.textContent = 'Starting download...';

            setTimeout(function () {
                window.location.href = url;

                setTimeout(function () {
                    downloadBtn.disabled = false;
                    spinner.style.display = 'none';
                    btnText.textContent = '⬇️ Download APK';
                    progress.style.display = 'none';
                    progressBar.style.width = '0%';
                }, 2500);

            }, 350);

        }, 250);

    });

</script>

</body>
</html>