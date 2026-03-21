<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Sedang Maintenance – IwakQu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green-dark:  #064e3b;
            --green-mid:   #065f46;
            --green-light: #10b981;
            --yellow:      #fbbf24;
            --white:       #ffffff;
        }

        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #064e3b 0%, #065f46 40%, #047857 100%);
            color: var(--white);
            overflow: hidden;
            padding: 24px;
        }

        /* ── Bubbles background ── */
        .bubbles {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .bubble {
            position: absolute;
            bottom: -120px;
            border-radius: 50%;
            background: rgba(255,255,255,.06);
            animation: rise linear infinite;
        }
        @keyframes rise {
            to { transform: translateY(-110vh); opacity: 0; }
        }

        /* ── Card ── */
        .card {
            position: relative;
            z-index: 1;
            background: rgba(255,255,255,.08);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 32px;
            padding: 56px 48px;
            max-width: 580px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 80px rgba(0,0,0,.3);
        }

        /* ── Logo ── */
        .logo-wrap {
            margin-bottom: 28px;
        }
        .logo-bg {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border-radius: 24px;
            padding: 16px 20px;
            box-shadow: 0 8px 32px rgba(251,191,36,.30), 0 2px 8px rgba(0,0,0,.12);
            animation: logoPulse 3s ease-in-out infinite;
        }
        .logo-bg img {
            height: 72px;
            width: auto;
            display: block;
        }
        @keyframes logoPulse {
            0%, 100% { box-shadow: 0 8px 32px rgba(251,191,36,.30), 0 2px 8px rgba(0,0,0,.12); }
            50%       { box-shadow: 0 12px 48px rgba(251,191,36,.55), 0 4px 16px rgba(0,0,0,.15); }
        }
        .bubbles-fish {
            margin-top: 12px;
            font-size: 18px;
            letter-spacing: 6px;
            animation: fadeBubble 2.4s ease-in-out infinite;
        }
        @keyframes fadeBubble {
            0%, 100% { opacity: .25; }
            50%       { opacity: 1; }
        }

        /* ── Badge ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(251,191,36,.15);
            border: 1px solid rgba(251,191,36,.35);
            border-radius: 999px;
            padding: 6px 18px;
            font-size: 13px;
            font-weight: 600;
            color: var(--yellow);
            margin-bottom: 20px;
        }
        .badge::before { content: '●'; animation: blink 1.2s step-end infinite; }
        @keyframes blink { 50% { opacity: 0; } }

        /* ── Typography ── */
        h1 {
            font-size: clamp(28px, 5vw, 42px);
            font-weight: 900;
            line-height: 1.15;
            margin-bottom: 16px;
        }
        h1 span { color: var(--yellow); }

        p.sub {
            font-size: 16px;
            color: rgba(255,255,255,.75);
            line-height: 1.7;
            max-width: 420px;
            margin: 0 auto 36px;
        }

        /* ── Divider ── */
        .divider {
            height: 1px;
            background: rgba(255,255,255,.12);
            margin: 0 0 36px;
        }

        /* ── Gear animation ── */
        .gears {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-bottom: 32px;
            font-size: 28px;
        }
        .gear-a { animation: spin 4s linear infinite; }
        .gear-b { animation: spin 4s linear infinite reverse; }
        .gear-c { animation: spin 3s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Progress bar ── */
        .prog-wrap {
            background: rgba(255,255,255,.12);
            border-radius: 999px;
            height: 8px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        .prog-bar {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--yellow), #f59e0b);
            width: 0;
            animation: load 3.5s ease-out forwards;
            box-shadow: 0 0 14px rgba(251,191,36,.6);
        }
        @keyframes load { to { width: 70%; } }
        .prog-label {
            font-size: 12px;
            color: rgba(255,255,255,.5);
            margin-bottom: 36px;
        }

        /* ── WA button ── */
        .btn-wa {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #25D366;
            color: #fff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            padding: 14px 28px;
            border-radius: 16px;
            text-decoration: none;
            transition: background .2s, transform .2s, box-shadow .2s;
            box-shadow: 0 6px 24px rgba(37,211,102,.35);
        }
        .btn-wa:hover {
            background: #128C7E;
            transform: translateY(-2px);
            box-shadow: 0 10px 32px rgba(37,211,102,.45);
        }
        .btn-wa svg { width: 22px; height: 22px; flex-shrink: 0; }

        /* ── Footer ── */
        .footer {
            position: relative;
            z-index: 1;
            margin-top: 28px;
            font-size: 13px;
            color: rgba(255,255,255,.35);
        }
        .footer a { color: var(--yellow); text-decoration: none; }

        /* Responsive */
        @media (max-width: 480px) {
            .card { padding: 40px 24px; border-radius: 24px; }
        }
    </style>
</head>
<body>

<!-- Bubbles BG -->
<div class="bubbles" id="bubblesContainer"></div>

<!-- Main Card -->
<div class="card">
    <div class="logo-wrap">
        <div class="logo-bg">
            <img src="{{ asset('images/logo.png') }}" alt="IwakQu">
        </div>
        <div class="bubbles-fish">○ ○ ○</div>
    </div>

    <div class="badge">Sedang Maintenance</div>

    <h1>Kami Sedang<br>Menyempurnakan <span>IwakQu</span></h1>

    <p class="sub">
        Website sedang dalam perbaikan untuk memberikan pengalaman belanja ikan marinasi yang lebih segar dan menyenangkan.
        Kami segera kembali! 🙏
    </p>

    <div class="divider"></div>

    <div class="gears">
        <span class="gear-a">⚙️</span>
        <span class="gear-b">⚙️</span>
        <span class="gear-c">⚙️</span>
    </div>

    <div class="prog-wrap">
        <div class="prog-bar"></div>
    </div>
    <p class="prog-label">Proses pembaruan sedang berlangsung…</p>

    <a href="https://api.whatsapp.com/send?phone=628980625805&text=hai,%20saya%20mau%20pesan%20"
       target="_blank" rel="noopener" class="btn-wa">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
            <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
        </svg>
        Hubungi via WhatsApp
    </a>
</div>

<div class="footer">
    &copy; {{ date('Y') }} <a href="/">IwakQu</a> — Ikan Marinasi Premium
</div>

<script>
// Generate random floating bubbles
(function () {
    const container = document.getElementById('bubblesContainer');
    const total = 18;
    for (let i = 0; i < total; i++) {
        const el = document.createElement('div');
        el.className = 'bubble';
        const size = Math.random() * 60 + 20;
        el.style.cssText = `
            width: ${size}px;
            height: ${size}px;
            left: ${Math.random() * 100}%;
            animation-duration: ${Math.random() * 12 + 8}s;
            animation-delay: ${Math.random() * 10}s;
            opacity: ${Math.random() * 0.12 + 0.03};
        `;
        container.appendChild(el);
    }
})();
</script>
</body>
</html>
