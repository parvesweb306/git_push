<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Family links - {{ config('app.name', 'Child Guard') }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #f4f5f9;
            color: #1a2035;
            font-family: "Segoe UI", "Noto Sans Bengali", -apple-system, Arial, sans-serif;
            line-height: 1.5;
        }
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px max(20px, calc((100% - 1080px) / 2));
            color: #f4ead0;
            background: linear-gradient(120deg, #0b1224, #1e2946);
            box-shadow: 0 4px 20px rgba(9, 12, 24, .2);
        }
        .brand { display: flex; align-items: center; gap: 10px; font-size: 16px; font-weight: 800; }
        .brand-icon {
            display: grid;
            width: 36px;
            height: 36px;
            place-items: center;
            border-radius: 10px;
            background: linear-gradient(135deg, #c9a24b, #8a6d1f);
        }
        .nav { display: flex; align-items: center; gap: 16px; }
        .nav a { color: #e5e7eb; font-size: 13px; font-weight: 600; text-decoration: none; }
        .nav a:hover { color: #f2e4c0; }
        main { width: min(940px, calc(100% - 32px)); margin: 34px auto 60px; }
        .heading { margin-bottom: 22px; }
        h1 { margin: 0; color: #0f172a; font-size: 26px; }
        .heading p, .sub { margin: 6px 0 0; color: #6b7280; font-size: 13px; }
        .card {
            margin-bottom: 18px;
            padding: 24px;
            border: 1px solid #e7e9ef;
            border-radius: 16px;
            background: white;
            box-shadow: 0 2px 10px rgba(15, 23, 42, .06);
        }
        h2 { margin: 0; font-size: 17px; }
        .create-form { display: flex; gap: 10px; margin-top: 18px; }
        input[type="text"] {
            flex: 1;
            min-width: 0;
            padding: 12px 14px;
            border: 1px solid #dfe3eb;
            border-radius: 10px;
            background: #fafbfd;
            font: inherit;
        }
        input:focus { outline: 3px solid rgba(29, 78, 216, .14); border-color: #1d4ed8; }
        button {
            border: 0;
            border-radius: 9px;
            padding: 10px 15px;
            color: white;
            background: #17233f;
            font: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
        }
        button:hover { filter: brightness(1.12); }
        .danger { background: #9f1239; }
        .alert { margin-bottom: 16px; padding: 13px 16px; border-radius: 10px; font-size: 14px; }
        .success { color: #065f46; background: #e5f7f2; }
        .error { color: #881337; background: #fde8ee; }
        .link-list { display: grid; gap: 12px; margin-top: 18px; }
        .link-item {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 14px;
            padding: 16px;
            border: 1px solid #e7e9ef;
            border-radius: 12px;
            background: #fcfcfd;
        }
        .link-name { margin-bottom: 8px; font-size: 14px; font-weight: 800; }
        .link-url {
            overflow-wrap: anywhere;
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 600;
        }
        .link-actions { display: flex; align-items: center; gap: 8px; }
        .copy-button { color: #17233f; background: #eef1f7; }
        .empty { padding: 24px 8px; color: #6b7280; text-align: center; font-size: 14px; }
        .label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 700; }
        .help { margin-top: 10px; color: #6b7280; font-size: 12px; }
        @media (max-width: 640px) {
            .topbar { align-items: flex-start; flex-direction: column; }
            .nav { flex-wrap: wrap; gap: 12px; }
            .card { padding: 18px; }
            .create-form { flex-direction: column; }
            .link-item { grid-template-columns: 1fr; }
            .link-actions { justify-content: flex-start; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="brand"><span class="brand-icon">🔗</span> Family link পরিচালনা</div>
        <nav class="nav" aria-label="Main navigation">
            <a href="{{ route('dashboard') }}">← Dashboard</a>
            <a href="{{ route('profile.edit') }}">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </nav>
    </header>

    <main>
        <div class="heading">
            <h1>Family link</h1>
            <p>নতুন ফোনে app install করার জন্য একাধিক link তৈরি করুন। কোনো link মুছে দিলে সেটি সঙ্গে সঙ্গেই কাজ করা বন্ধ করবে; আগে যুক্ত device মুছবে না।</p>
        </div>

        @if (session('success'))
            <div class="alert success" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert error" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="card">
            <h2>নতুন link তৈরি করুন</h2>
            <p class="sub">লিংকের নাম দিলে পরে চিনতে সুবিধা হবে।</p>
            <form method="POST" action="{{ route('family-links.store') }}" class="create-form">
                @csrf
                <div style="flex:1">
                    <label class="label" for="label">Link-এর নাম</label>
                    <input id="label" type="text" name="label" value="{{ old('label') }}" maxlength="100" placeholder="যেমন: বাসার ফোন">
                </div>
                <button type="submit">＋ Link তৈরি</button>
            </form>
        </section>

        <section class="card">
            <h2>সক্রিয় link ({{ $links->count() + $legacyUsers->count() }})</h2>
            @if ($legacyUsers->isNotEmpty() || $links->isNotEmpty())
                <div class="link-list">
                    @foreach ($legacyUsers as $legacyUser)
                        <article class="link-item">
                            <div>
                                <div class="link-name">পুরোনো Family link</div>
                                @if (auth()->user()->isAdmin())
                                    <div class="sub">{{ $legacyUser->name }}</div>
                                @endif
                                <div class="link-url" id="legacy-link-{{ $legacyUser->id }}">{{ route('family.install', $legacyUser->family_invite_code) }}</div>
                            </div>
                            <div class="link-actions">
                                <button type="button" class="copy-button" onclick="copyLink('legacy-link-{{ $legacyUser->id }}', this)">📋 Copy</button>
                                <form method="POST" action="{{ route('family-links.legacy.destroy', $legacyUser) }}" onsubmit="return confirm('এই পুরোনো link বাতিল করবেন? link-টি আর কাজ করবে না।')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="danger">বাতিল</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                    @foreach ($links as $link)
                        <article class="link-item">
                            <div>
                                <div class="link-name">{{ $link->label }}</div>
                                @if (auth()->user()->isAdmin())
                                    <div class="sub">{{ $link->user->name }}</div>
                                @endif
                                <div class="link-url" id="family-link-{{ $link->id }}">{{ route('family.install', $link->code) }}</div>
                            </div>
                            <div class="link-actions">
                                <button type="button" class="copy-button" onclick="copyLink('family-link-{{ $link->id }}', this)">📋 Copy</button>
                                <form method="POST" action="{{ route('family-links.destroy', $link) }}" onsubmit="return confirm('এই family link বাতিল করবেন? link-টি আর কাজ করবে না।')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="danger">বাতিল</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="empty">এখনো কোনো family link নেই। উপরের ফর্ম থেকে নতুন link তৈরি করুন।</div>
            @endif
            <p class="help">Link বাতিল হলে ওই link দিয়ে নতুন device যুক্ত করা যাবে না। আগে যুক্ত device ড্যাশবোর্ড থেকে আলাদাভাবে মুছতে হবে।</p>
        </section>
    </main>

    <script>
        function copyLink(elementId, button) {
            const text = document.getElementById(elementId).textContent.trim();
            navigator.clipboard.writeText(text).then(() => {
                const original = button.textContent;
                button.textContent = '✅ Copied';
                setTimeout(() => button.textContent = original, 1800);
            }).catch(() => {
                const range = document.createRange();
                range.selectNodeContents(document.getElementById(elementId));
                const selection = window.getSelection();
                selection.removeAllRanges();
                selection.addRange(range);
                document.execCommand('copy');
                selection.removeAllRanges();
                button.textContent = '✅ Copied';
                setTimeout(() => button.textContent = '📋 Copy', 1800);
            });
        }
    </script>
</body>
</html>
