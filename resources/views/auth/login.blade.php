<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', '3PP Internet') }} — Authentication</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #0d1117; color: #e2e8f0; }

        .form-input {
            width: 100%;
            background: #0f1117;
            border: 1px solid #2a3347;
            border-radius: 8px;
            padding: 10px 14px;
            color: #e2e8f0;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
        }
        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 6px;
        }
        .btn-primary {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            background: #2563eb;
            color: #fff;
            padding: 11px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 md:p-8">
    <div class="w-full max-w-4xl bg-[#1e2535] border border-[#2a3347] rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row">
        
        {{-- Left Column: Logo & Title --}}
        <div class="md:w-1/2 bg-[#0f1117] p-10 flex flex-col items-center justify-center text-center border-b md:border-b-0 md:border-r border-[#2a3347]">
            <img src="{{ asset('images/logo-3pp.jpeg') }}" alt="Logo 3PP" class="w-32 h-32 mb-6 object-cover rounded-2xl shadow-xl border border-[#2a3347]" />
            <h2 class="text-2xl font-800 text-white tracking-tight mb-2">PT. Tiga Putra Pandawa</h2>
            <p class="text-[15px] text-slate-400">Internet Service Provider</p>
            <div class="mt-8 text-[12px] text-slate-600">
                &copy; {{ date('Y') }} PT. Tiga Putra Pandawa.<br>All rights reserved.
            </div>
        </div>

        {{-- Right Column: Login Form --}}
        <div class="md:w-1/2 p-10 flex flex-col justify-center">
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="mb-8">
                <h3 class="text-xl font-700 text-white leading-tight mb-2">Selamat Datang 👋</h3>
                <p class="text-[14px] text-slate-400">Silakan masuk menggunakan kredensial Anda.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <input id="email" type="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@3pp.co.id" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs" />
                </div>

                <!-- Password -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label for="password" class="form-label" style="margin-bottom:0;">{{ __('Password') }}</label>
                        @if (Route::has('password.request'))
                            <a class="text-xs text-blue-400 hover:text-blue-300 font-semibold transition-colors" href="{{ route('password.request') }}">
                                {{ __('Lupa password?') }}
                            </a>
                        @endif
                    </div>
                    <input id="password" type="password" name="password" class="form-input" required autocomplete="current-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center pt-2">
                    <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-[#2a3347] bg-[#0f1117] text-blue-600 focus:ring-blue-500 focus:ring-offset-[#1e2535]">
                    <label for="remember_me" class="ml-2 text-[13px] text-slate-400 select-none cursor-pointer">
                        {{ __('Ingat Saya') }}
                    </label>
                </div>

                <div class="pt-4">
                    <button type="submit" class="btn-primary">
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>
        </div>

    </div>
</body>
</html>
