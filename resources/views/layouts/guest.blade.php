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

    {{-- Tailwind CDN (Menyesuaikan dengan app.blade.php) --}}
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

        /* Form Inputs matching app.blade.php */
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
<body class="h-full flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        {{-- Logo Header --}}
        <div class="flex flex-col items-center mb-8">
            <a href="/">
                <x-application-logo class="w-24 h-24 mb-4 object-cover rounded-2xl shadow-xl border border-[#2a3347]" />
            </a>
            <h2 class="text-[22px] font-800 text-white tracking-tight">Internt Service Provider</h2>
            <p class="text-[13px] text-slate-500 mt-1">Satu Solusi Tiga Layanan Andal</p>
        </div>

        {{-- Card Container --}}
        <div class="bg-[#1e2535] border border-[#2a3347] rounded-[16px] shadow-2xl p-8" style="box-shadow: 0 20px 40px -10px rgba(0,0,0,0.5);">
            {{ $slot }}
        </div>
        
        <div class="text-center mt-8 text-[12px] text-slate-600">
            &copy; {{ date('Y') }} PT. Tiga Putra Pandawa. All rights reserved.
        </div>
    </div>
</body>
</html>
