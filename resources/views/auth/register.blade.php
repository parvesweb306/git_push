<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Register') }} — {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;}
        body{
            margin:0;
            min-height:100vh;
            background:#05060A;
            color:#EDEBF2;
            font-family:'Inter',sans-serif;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:48px 20px;
            position:relative;
            overflow-x:hidden;
        }
        body::before{
            content:'';
            position:fixed; inset:0; z-index:0;
            background:
                radial-gradient(circle at 20% 15%, rgba(255,95,69,0.10), transparent 40%),
                radial-gradient(circle at 80% 85%, rgba(156,154,176,0.08), transparent 45%);
        }
        .wrap{position:relative; z-index:1; width:100%; max-width:380px;}

        .brand{text-align:center; margin-bottom:32px;}
        .brand .tag{
            display:inline-flex; align-items:center; gap:8px;
            font-size:12.5px; font-weight:500; color:#FF5F45;
        }
        .brand .tag::before{content:''; width:6px; height:6px; border-radius:50%; background:#FF5F45;}
        .brand h1{
            font-family:'Fraunces',serif; font-weight:500; font-size:30px;
            margin:16px 0 8px;
        }
        .brand p{font-size:14px; color:#9C9AB0; margin:0;}

        .card{
            border-radius:16px; border:1px solid rgba(255,255,255,0.10);
            background:#0C0E16; padding:28px;
            box-shadow:0 20px 60px -20px rgba(0,0,0,0.6);
        }
        .field{margin-bottom:20px;}
        .field:last-of-type{margin-bottom:0;}
        label{
            display:block; font-size:14px; font-weight:500;
            color:#EDEBF2; margin-bottom:7px;
        }

        input[type="text"], input[type="email"], input[type="password"]{
            display:block; width:100%;
            background:#05060A; color:#EDEBF2;
            border:1px solid rgba(255,255,255,0.10); border-radius:10px;
            padding:11px 14px; font-size:14px; font-family:inherit;
            transition:border-color .2s, box-shadow .2s;
        }
        input::placeholder{color:#5D5C6E;}
        input:focus{
            outline:none; border-color:rgba(255,95,69,0.5);
            box-shadow:0 0 0 3px rgba(255,95,69,0.18);
        }
        .err{margin:7px 0 0; font-size:12.5px; color:#FF8A75;}

        .actions{
            display:flex; align-items:center; justify-content:space-between;
            margin-top:26px; gap:16px;
        }
        .login-link{font-size:13px; color:#9C9AB0; text-decoration:none;}
        .login-link:hover{color:#FF5F45;}

        .submit{
            background:#FF5F45; color:#05060A;
            border:none; border-radius:10px; padding:11px 22px;
            font-size:14.5px; font-weight:600; font-family:inherit;
            cursor:pointer; transition:filter .15s; white-space:nowrap;
        }
        .submit:hover{filter:brightness(1.1);}
        .submit:active{filter:brightness(0.95);}
        .submit:focus-visible{outline:2px solid #FF5F45; outline-offset:3px;}
    </style>
</head>
<body>
    <div class="wrap">

        <div class="brand">
            <span class="tag">{{ config('app.name', 'Laravel') }}</span>
            <h1>Create an account</h1>
            <p>Start by filling in your details below.</p>
        </div>

        <div class="card">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="field">
                    <label for="name">{{ __('Name') }}</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                           required autofocus autocomplete="name" placeholder="Your name">
                    @error('name') <p class="err">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label for="email">{{ __('Email') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           required autocomplete="username" placeholder="you@example.com">
                    @error('email') <p class="err">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password"
                           required autocomplete="new-password" placeholder="••••••••">
                    @error('password') <p class="err">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           required autocomplete="new-password" placeholder="••••••••">
                    @error('password_confirmation') <p class="err">{{ $message }}</p> @enderror
                </div>

                <div class="actions">
                    <a href="{{ route('login') }}" class="login-link">{{ __('Already registered?') }}</a>
                    <button type="submit" class="submit">{{ __('Register') }}</button>
                </div>
            </form>
        </div>

    </div>
</body>
</html>