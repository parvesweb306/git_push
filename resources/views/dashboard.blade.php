<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Child Guard Dashboard</title>

    <style>

        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy: #0f172a;
            --navy-light: #1e2946;
            --slate: #334155;
            --gold: #c9a24b;
            --gold-light: #f2e4c0;
            --sapphire: #1d4ed8;
            --sapphire-light: #eef3ff;
            --ruby: #9f1239;
            --ruby-soft: #fde8ee;
            --emerald: #0f766e;
            --emerald-soft: #e5f7f2;
            --ink: #1a2035;
            --muted: #6b7280;
            --border: #e7e9ef;
            --bg: #f4f5f9;
            --card: #ffffff;
            --shadow: 0 2px 10px rgba(15, 23, 42, .06), 0 1px 2px rgba(15, 23, 42, .04);
            --shadow-lg: 0 14px 34px rgba(15, 23, 42, .12);
        }

        body {
            background: var(--bg);
            font-family: "Segoe UI", "Noto Sans Bengali", -apple-system, Arial, sans-serif;
            color: var(--ink);
            line-height: 1.5;
            min-height: 100vh;
        }

        /* ---------- Topbar ---------- */

        .topbar {
            background: linear-gradient(120deg, #0b1224 0%, #16213e 55%, #1e2946 100%);
            padding: 16px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 4px 24px rgba(9, 12, 24, .35);
            border-bottom: 1px solid rgba(201, 162, 75, .25);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 18px;
            color: #f4ead0;
            letter-spacing: .3px;
        }

        .brand .dot {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #c9a24b, #8a6d1f);
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 3px 10px rgba(201, 162, 75, .35);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-link {
            color: rgba(230, 232, 240, .8);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 9px;
            transition: background .15s, color .15s;
        }

        .nav-link:hover { background: rgba(201, 162, 75, .14); color: var(--gold-light); }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-left: 18px;
            border-left: 1px solid rgba(255,255,255,.14);
        }

        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #c9a24b, #8a6d1f);
            color: #1a1508;
            font-weight: 800;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 0 2px rgba(255,255,255,.15);
        }

        .user-name { font-size: 13.5px; font-weight: 700; color: #f1f3f9; }
        .user-role {
            font-size: 10.5px;
            color: var(--gold-light);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .logout-btn {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.18);
            color: #e5e7eb;
            cursor: pointer;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 8px;
            transition: background .15s;
        }
        .logout-btn:hover { background: rgba(255,255,255,.14); }

        /* ---------- Layout ---------- */

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px 20px 60px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 26px;
        }

        .stat {
            border-radius: 16px;
            padding: 22px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border);
            background: var(--card);
        }

        .stat::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 5px;
        }

        .stat.total::before { background: linear-gradient(180deg, var(--navy), var(--slate)); }
        .stat.locked::before { background: linear-gradient(180deg, var(--ruby), #e11d48); }
        .stat.unlocked::before { background: linear-gradient(180deg, var(--emerald), #10b981); }

        .stat .num { font-size: 30px; font-weight: 800; color: var(--ink); }
        .stat.total .num { color: var(--navy); }
        .stat.locked .num { color: var(--ruby); }
        .stat.unlocked .num { color: var(--emerald); }

        .stat .label {
            font-size: 12.5px;
            color: var(--muted);
            margin-top: 4px;
            font-weight: 600;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 26px;
            margin-bottom: 22px;
            box-shadow: var(--shadow);
        }

        .card h2 {
            font-size: 16.5px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--ink);
        }

        .card p.sub {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 18px;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
            border-left: 4px solid transparent;
        }

        .alert-success { background: var(--emerald-soft); color: #065f56; border-color: var(--emerald); }
        .alert-error { background: var(--ruby-soft); color: #881337; border-color: var(--ruby); }

        .form-row { display: flex; gap: 12px; }

        input[type="text"] {
            flex: 1;
            padding: 13px 16px;
            border: 1.5px solid var(--border);
            border-radius: 11px;
            font-size: 14px;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            background: #fafbfd;
        }

        input[type="text"]:focus {
            border-color: var(--navy-light);
            box-shadow: 0 0 0 4px rgba(30,41,70,.08);
            background: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--navy-light), var(--navy));
            color: #f4ead0;
            border: 1px solid rgba(201,162,75,.4);
            padding: 13px 24px;
            border-radius: 11px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            white-space: nowrap;
            box-shadow: 0 6px 16px rgba(15,23,42,.25);
            transition: transform .12s, box-shadow .12s;
        }

        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 22px rgba(15,23,42,.32); }

        .btn-success {
            background: linear-gradient(135deg, var(--emerald), #10b981);
            color: white;
            border: none;
            padding: 11px 16px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 12.5px;
            cursor: pointer;
            width: 100%;
            box-shadow: 0 4px 12px rgba(15,118,110,.25);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--ruby), #e11d48);
            color: white;
            border: none;
            padding: 11px 16px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 12.5px;
            cursor: pointer;
            width: 100%;
            box-shadow: 0 4px 12px rgba(159,18,57,.22);
        }

        .btn-danger.solid { background: linear-gradient(135deg, #7f1d1d, #9f1239); }

        .btn-danger:disabled {
            background: #f0cdd6;
            box-shadow: none;
            cursor: not-allowed;
        }

        .code-box {
            background: linear-gradient(135deg, #fbf7ec, var(--gold-light));
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 14px;
            border: 1px solid #ecd9a3;
        }

        .unlock-code {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 5px;
            color: #8a6d1f;
            margin: 8px 0;
        }

        .link-row {
            display: flex;
            align-items: stretch;
            gap: 8px;
            border: 1.5px dashed #d7dbe6;
            border-radius: 11px;
            padding: 10px 12px;
            background: #fafbfd;
        }

        .link-row .link-text {
            flex: 1;
            font-size: 12px;
            color: var(--sapphire);
            font-weight: 600;
            word-break: break-all;
            display: flex;
            align-items: center;
        }

        .copy-btn {
            background: linear-gradient(135deg, var(--navy-light), var(--sapphire));
            color: white;
            border: none;
            padding: 9px 15px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: transform .12s;
        }

        .copy-btn:hover { transform: translateY(-1px); }
        .copy-btn.copied { background: linear-gradient(135deg, var(--emerald), #10b981); }

        .empty {
            text-align: center;
            padding: 44px 20px;
            color: var(--muted);
        }

        .empty .big { font-size: 42px; margin-bottom: 12px; }

        .device-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
            gap: 18px;
        }

        .device {
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 13px;
            background: #fdfdfe;
            transition: box-shadow .15s, transform .15s;
        }

        .device:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); }

        .device-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }

        .device-name { font-weight: 800; font-size: 16px; color: var(--ink); }
        .device-sub { font-size: 12px; color: var(--muted); margin-top: 3px; font-weight: 500; }
        .device-sub.owner {
            color: var(--navy);
            font-weight: 700;
            background: #eef1f7;
            display: inline-block;
            padding: 2px 9px;
            border-radius: 999px;
            margin-top: 5px;
            border: 1px solid #dde2ee;
        }

        .badge {
            font-size: 10.5px;
            font-weight: 800;
            padding: 5px 11px;
            border-radius: 999px;
            white-space: nowrap;
            letter-spacing: .03em;
        }

        .badge-not-paired { background: #eef1f7; color: var(--slate); }
        .badge-locked { background: var(--ruby-soft); color: var(--ruby); }
        .badge-unlocked { background: var(--emerald-soft); color: var(--emerald); }
        .badge-offline { background: var(--gold-light); color: #8a6d1f; }

        .detail-list {
            display: flex;
            flex-direction: column;
            gap: 7px;
            font-size: 12.5px;
            background: #f8f9fb;
            border-radius: 10px;
            padding: 12px 14px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        .detail-row .k { color: var(--muted); font-weight: 500; }
        .detail-row .v { font-weight: 700; text-align: right; color: var(--ink); }
        .detail-row .mono { font-family: monospace; letter-spacing: 1px; color: var(--sapphire); }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .actions form { flex: 1; min-width: 100px; }

        .open-link {
            font-size: 12px;
            color: var(--sapphire);
            text-decoration: none;
            font-weight: 700;
        }
        .open-link:hover { text-decoration: underline; }

        @media (max-width: 640px) {
            .stats-row { grid-template-columns: 1fr; }
            .form-row { flex-direction: column; }
            .topbar { padding: 14px 16px; flex-wrap: wrap; gap: 10px; }
            .topbar-right { gap: 12px; flex-wrap: wrap; }
        }

    </style>

</head>
<body>

<div style="max-width:700px;margin:20px auto;padding:18px;border:1px solid #ddd;border-radius:12px">
    <h2>multiple install link</h2>
    <p>এই link নতুন multiple phone-এ খুলে Play Store থেকে app install করুন:</p>
    <input
        type="text"
        readonly
        value="{{ $familyLink }}"
        onclick="this.select()"
        style="width:100%;padding:12px;margin-top:8px"
    >
    <p style="margin-top:8px">Link-টি copy করে নতুন phone-এ পাঠাতে পারবেন।</p>
</div>
<div class="topbar">

    <div class="brand">
        <div class="dot">🛡️</div>
        Admin Dashboard
    </div>

    <div class="topbar-right">

        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.users.index') }}" class="nav-link">
                👤 Users ম্যানেজ করুন
            </a>
        @endif

        <a href="{{ route('profile.edit') }}" class="nav-link">
            ⚙️ Profile
        </a>

        <div class="user-chip">

            <div class="avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>

            <div>
                <div class="user-name">{{ auth()->user()->name ?? '' }}</div>
                <div class="user-role">
                    {{ auth()->user()->isAdmin() ? 'Main Admin' : 'User' }}
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>

        </div>

    </div>

</div>

<div class="container">

    <div class="stats-row">

        <div class="stat total">
            <div class="num">{{ $devices->count() }}</div>
            <div class="label">মোট Device</div>
        </div>

        <div class="stat locked">
            <div class="num">{{ $devices->where('is_locked', true)->count() }}</div>
            <div class="label">Locked</div>
        </div>

        <div class="stat unlocked">
            <div class="num">{{ $devices->where('is_locked', false)->count() }}</div>
            <div class="label">Unlocked</div>
        </div>

    </div>


    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif


    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif


    @if(session('unlock_code'))

        <div class="card">
            <h2>🔑 Unlock Code</h2>
            <p class="sub">এই code ফোনের locked screen-এ দিতে হবে। 30 মিনিট valid।</p>

            <div class="code-box">
                <div style="font-size:12px;color:var(--muted); font-weight:600;">6 Digit Unlock Code</div>
                <div class="unlock-code">{{ session('unlock_code') }}</div>
            </div>
        </div>

    @endif


    <div class="card">
        <h2>➕ নতুন Child Device যোগ করুন</h2>
        <p class="sub">Device তৈরি করলে একটি pairing code এবং APK download link তৈরি হবে।</p>

        <form method="POST" action="{{ route('devices.create-pairing') }}">
            @csrf

            <div class="form-row">
                <input
                    type="text"
                    name="device_name"
                    placeholder="Device name, যেমন: Son's Phone"
                    maxlength="100"
                    required
                >
                <button type="submit" class="btn-primary">Create Device</button>
            </div>
        </form>
    </div>


    @if(session('device_created'))

        @php
            $createdDevice = \App\Models\Device::find(session('device_created'));
            $createdLink = $createdDevice
                ? route('devices.download-page', $createdDevice->pairing_code)
                : null;
        @endphp

        @if($createdDevice)

            <div class="card">
                <h2>✅ Device তৈরি হয়েছে</h2>
                <p class="sub">নিচের Pairing Code এবং Download Link child-এর phone-এ ব্যবহার করুন।</p>

                <div class="code-box">
                    <strong style="font-size:12px;color:var(--muted); font-weight:600;">Pairing Code</strong>
                    <div class="unlock-code">{{ $createdDevice->pairing_code }}</div>
                </div>

                <div class="link-row">
                    <div class="link-text" id="link-created">{{ $createdLink }}</div>
                    <button type="button" class="copy-btn" onclick="copyLink('link-created', this)">
                        📋 Copy
                    </button>
                </div>
            </div>

        @endif

    @endif


    <div class="card">
        <h2>📱 আমার সব Device</h2>

        @if($devices->count() === 0)

            <div class="empty">
                <div class="big">📭</div>
                এখনো কোনো device তৈরি করা হয়নি।<br>
                উপরের ফর্ম থেকে প্রথম device যোগ করুন।
            </div>

        @else

            <div class="device-grid">

                @foreach($devices as $device)

                  @php
    $isPaired = !empty($device->device_token);
    $isOnline = $device->last_seen && $device->last_seen->gt(now()->subMinutes(2));

    $deviceLink = $device->pairing_code
        ? route('devices.download-page', $device->pairing_code)
        : null;

    $linkId = 'link-' . $device->id;
@endphp

                    <div class="device" id="device-{{ $device->id }}" data-paired="{{ $isPaired ? '1' : '0' }}">

                        <div class="device-header">

                            <div>
                                <div class="device-name">{{ $device->device_name }}</div>
                               @if($device->pairing_code)
    <div class="device-sub">Code: {{ $device->pairing_code }}</div>
@else
    <div class="device-sub">Family link দিয়ে যুক্ত হয়েছে</div>
@endif

                                @if(auth()->user()->isAdmin() && $device->user)
                                    <div class="device-sub owner">👤 {{ $device->user->name }}</div>
                                @endif
                            </div>

                            @if(!$isPaired)
                                <span class="badge badge-not-paired">NOT PAIRED</span>
                            @elseif($device->is_locked)
                                <span class="badge badge-locked">LOCKED</span>
                            @elseif($isOnline)
                                <span class="badge badge-unlocked">UNLOCKED</span>
                            @else
                                <span class="badge badge-offline">OFFLINE</span>
                            @endif

                        </div>


                        <div class="detail-list">

                            @if($device->pairing_code)
    <div class="detail-row">
        <span class="k">Pairing Code</span>
        <span class="v mono">{{ $device->pairing_code }}</span>
    </div>
@else
    <div class="detail-row">
        <span class="k">Connection</span>
        <span class="v">Family link</span>
    </div>
@endif

                            <div class="detail-row">
                                <span class="k">Paired</span>
                                <span class="v">{{ $isPaired ? 'হ্যাঁ' : 'না, এখনো connect করেনি' }}</span>
                            </div>

                            <div class="detail-row">
                                <span class="k">Lock Status</span>
                                <span class="v">{{ $device->is_locked ? 'Locked' : 'Unlocked' }}</span>
                            </div>

                            <div class="detail-row">
                                <span class="k">Last Seen</span>
                                <span class="v">
                                    {{ $device->last_seen ? $device->last_seen->diffForHumans() : 'কখনো না' }}
                                </span>
                            </div>

                            <div class="detail-row">
                                <span class="k">যোগ করা হয়েছে</span>
                                <span class="v">{{ $device->created_at->diffForHumans() }}</span>
                            </div>
                            
{{-- Device Information --}}
<div class="detail-row">
    <span class="k">Manufacturer</span>
    <span class="v">
        {{ $device->manufacturer ?: 'তথ্য পাওয়া যায়নি' }}
    </span>
</div>

<div class="detail-row">
    <span class="k">Model</span>
    <span class="v">
        {{ $device->model ?: 'তথ্য পাওয়া যায়নি' }}
    </span>
</div>

<div class="detail-row">
    <span class="k">Android Version</span>
    <span class="v">
        {{ $device->android_version ?: 'তথ্য পাওয়া যায়নি' }}
    </span>
</div>

<div class="detail-row">
    <span class="k">Battery</span>
    <span class="v">
        {{ is_numeric($device->battery_level) ? $device->battery_level . '%' : 'তথ্য পাওয়া যায়নি' }}
    </span>
</div>

<div class="detail-row">
    <span class="k">IP Address</span>
    <span class="v">
        {{ $device->ip_address ?: 'তথ্য পাওয়া যায়নি' }}
    </span>
</div>

<div class="detail-row">
    <span class="k">Country</span>
    <span class="v">
        {{ $device->country ?: 'তথ্য পাওয়া যায়নি' }}
    </span>
</div>

<div class="detail-row">
    <span class="k">Region</span>
    <span class="v">
        {{ $device->region ?: 'তথ্য পাওয়া যায়নি' }}
    </span>
</div>

<div class="detail-row">
    <span class="k">City</span>
    <span class="v">
        {{ $device->city ?: 'তথ্য পাওয়া যায়নি' }}
    </span>
</div>

<div class="detail-row">
    <span class="k">Postal Code</span>
    <span class="v">
        {{ $device->postal_code ?: 'তথ্য পাওয়া যায়নি' }}
    </span>
</div>

<div class="detail-row">
    <span class="k">Timezone</span>
    <span class="v">
        {{ $device->timezone ?: 'তথ্য পাওয়া যায়নি' }}
    </span>
</div>


                        </div>


                       @if($deviceLink)
    <div class="link-row">
        <div class="link-text" id="{{ $linkId }}">{{ $deviceLink }}</div>
        <button type="button" class="copy-btn" onclick="copyLink('{{ $linkId }}', this)">
            📋 Copy
        </button>
    </div>

    <a href="{{ $deviceLink }}" target="_blank" class="open-link">
        ↗️ Download / Pairing পেজ খুলুন
    </a>
@endif

                        <a href="{{ $deviceLink }}" target="_blank" class="open-link">
                            ↗️ Download / Pairing পেজ খুলুন
                        </a>


                        <div class="actions">

                            @if(!$device->is_locked)

                                <form method="POST" action="{{ route('devices.lock', $device) }}">
                                    @csrf
                                    <button type="submit" class="btn-danger" {{ !$isPaired ? 'disabled' : '' }}>
                                        🔒 Lock
                                    </button>
                                </form>

                            @else

                                <form method="POST" action="{{ route('devices.unlock', $device) }}">
                                    @csrf
                                    <button type="submit" class="btn-success">🔓 Unlock</button>
                                </form>

                                <form method="POST" action="{{ route('devices.generate-unlock-code', $device) }}">
                                    @csrf
                                    <button type="submit" class="btn-primary" style="width:100%">🔑 Code দিন</button>
                                </form>

                            @endif

                            <form
                                method="POST"
                                action="{{ route('devices.destroy', $device) }}"
                                onsubmit="return confirm('এই device মুছে ফেলা হবে। নিশ্চিত?');"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger solid">
                                    🗑️ Delete
                                </button>
                            </form>

                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </div>

</div>


<script>

    function copyLink(elementId, btn) {

        const text = document.getElementById(elementId).innerText.trim();

        navigator.clipboard.writeText(text).then(function () {

            const original = btn.innerHTML;
            btn.innerHTML = '✅ Copied';
            btn.classList.add('copied');

            setTimeout(function () {
                btn.innerHTML = original;
                btn.classList.remove('copied');
            }, 1800);

        }).catch(function () {

            const range = document.createRange();
            const node = document.getElementById(elementId);
            range.selectNode(node);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            document.execCommand('copy');
            window.getSelection().removeAllRanges();

            btn.innerHTML = '✅ Copied';
            setTimeout(function () {
                btn.innerHTML = '📋 Copy';
            }, 1800);

        });
    }

    setInterval(function () {

        fetch("{{ route('dashboard.status') }}", {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {

            if (!data.success) return;

            data.devices.forEach(function (device) {

                const card = document.getElementById('device-' + device.id);
                if (!card) return;

                const badge = card.querySelector('.badge');
                if (!badge) return;

                if (!device.paired) {
                    badge.textContent = 'NOT PAIRED';
                    badge.className = 'badge badge-not-paired';
                } else if (device.is_locked) {
                    badge.textContent = 'LOCKED';
                    badge.className = 'badge badge-locked';
                } else {
                    badge.textContent = 'UNLOCKED';
                    badge.className = 'badge badge-unlocked';
                }

            });

        })
        .catch(function () {
        });

    }, 10000);

</script>

</body>
</html>