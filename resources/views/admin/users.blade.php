<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Users - Child Guard</title>

    <style>

        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --indigo: #4f46e5;
            --indigo-dark: #4338ca;
            --indigo-light: #eef2ff;
            --red: #dc2626;
            --green: #16a34a;
            --green-light: #dcfce7;
            --ink: #172033;
            --muted: #687086;
            --border: #e6e9ef;
            --bg: #f5f7fb;
            --card: #ffffff;
            --shadow: 0 1px 2px rgba(16,24,40,.04), 0 1px 3px rgba(16,24,40,.06);
        }

        body {
            background: var(--bg);
            font-family: "Segoe UI", "Noto Sans Bengali", -apple-system, Arial, sans-serif;
            color: var(--ink);
        }

        .topbar {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
        }

        .brand { font-weight: 700; display: flex; gap: 10px; align-items: center; font-size: 17px; }
        .brand .dot {
            width: 34px; height: 34px; background: var(--indigo); border-radius: 10px;
            display: flex; align-items: center; justify-content: center; font-size: 17px;
        }

        .topbar-right { display: flex; align-items: center; gap: 18px; }

        .nav-link {
            color: var(--muted); text-decoration: none; font-size: 14px; font-weight: 600;
        }
        .nav-link:hover { color: var(--indigo); }

        .container { max-width: 880px; margin: 0 auto; padding: 28px 20px 60px; }

        .card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 14px; padding: 24px; margin-bottom: 20px; box-shadow: var(--shadow);
        }

        .card h2 { font-size: 16px; margin-bottom: 4px; }
        .card p.sub { color: var(--muted); font-size: 13px; margin-bottom: 18px; }

        label { display: block; font-size: 13px; font-weight: 600; margin: 14px 0 6px; }

        input {
            width: 100%; padding: 10px 14px; border: 1px solid var(--border);
            border-radius: 9px; font-size: 14px; outline: none; transition: border-color .15s;
        }
        input:focus { border-color: var(--indigo); }

        .btn-primary {
            margin-top: 18px; background: var(--indigo); color: white; border: none;
            padding: 11px 20px; border-radius: 9px; font-weight: 600; font-size: 14px; cursor: pointer;
        }
        .btn-primary:hover { background: var(--indigo-dark); }

        .btn-danger {
            background: var(--red); color: white; border: none; padding: 7px 14px;
            border-radius: 7px; font-size: 12.5px; font-weight: 600; cursor: pointer;
        }

        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left; padding: 10px 8px; font-size: 12px; color: var(--muted);
            border-bottom: 1px solid var(--border); text-transform: uppercase; letter-spacing: .04em;
        }
        td { text-align: left; padding: 12px 8px; border-bottom: 1px solid var(--border); font-size: 14px; }

        .device-count-pill {
            background: var(--indigo-light); color: var(--indigo-dark);
            font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 999px;
        }

        .muted { color: var(--muted); font-size: 13px; }

        .alert {
            padding: 13px 16px; border-radius: 10px; margin-bottom: 18px; font-size: 14px; font-weight: 500;
        }
        .alert-success { background: var(--green-light); color: #166534; }
        .alert-error { background: #fee2e2; color: #991b1b; }

        .empty { text-align: center; padding: 30px 10px; color: var(--muted); }

    </style>
</head>
<body>

<div class="topbar">
    <div class="brand">
        <div class="dot">🛡️</div>
        Admin Create — User ম্যানেজমেন্ট
    </div>

    <div class="topbar-right">
        <a href="{{ route('profile.edit') }}" class="nav-link">⚙️ Profile</a>
        <a href="{{ route('dashboard') }}" class="nav-link">← Dashboard এ ফিরুন</a>
    </div>
</div>

<div class="container">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif

    <div class="card">
        <h2>➕ নতুন User তৈরি করুন</h2>
        <p class="sub">এই user তার নিজের device pairing এবং lock/unlock করতে পারবে, আর আপনি (Main Admin) সব সময় সবকিছু দেখতে পারবেন।</p>

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <label>নাম</label>
            <input type="text" name="name" value="{{ old('name') }}" required>

            <label>ইমেইল</label>
            <input type="email" name="email" value="{{ old('email') }}" required>

            <label>পাসওয়ার্ড</label>
            <input type="password" name="password" required>

            <label>পাসওয়ার্ড নিশ্চিত করুন</label>
            <input type="password" name="password_confirmation" required>

            <button type="submit" class="btn-primary">➕ User তৈরি করুন</button>
        </form>
    </div>

    <div class="card">
        <h2>👥 সব Sub-User ({{ $users->count() }})</h2>

        @if($users->count() === 0)
            <div class="empty">এখনো কোনো sub-user তৈরি করা হয়নি।</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>নাম</th>
                        <th>ইমেইল</th>
                        <th>Device</th>
                        <th>তৈরি হয়েছে</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td><span class="device-count-pill">{{ $u->devices_count }}</span></td>
                            <td class="muted">{{ $u->created_at->diffForHumans() }}</td>
                            <td>
                                <form method="POST"
                                      action="{{ route('admin.users.destroy', $u) }}"
                                      onsubmit="return confirm('এই user এবং তার সব device মুছে যাবে। নিশ্চিত?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger">মুছুন</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>

</body>
</html>