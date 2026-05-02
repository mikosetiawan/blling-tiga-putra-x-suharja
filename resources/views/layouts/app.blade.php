<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — 3PP Internet Management</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        },
                        sidebar: '#0f1117',
                        panel: '#161b27',
                        card: '#1e2535',
                        border: '#2a3347',
                    }
                }
            }
        }
    </script>

    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        :root {
            --sidebar-w: 260px;
        }

        body { background: #0d1117; color: #e2e8f0; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #2a3347; border-radius: 4px; }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-w);
            background: #0f1117;
            border-right: 1px solid #1e2535;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            border-radius: 10px;
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            margin: 1px 0;
            cursor: pointer;
            text-decoration: none;
        }
        .nav-item:hover { background: #1e2535; color: #94a3b8; }
        .nav-item.active { background: #1e3a5f; color: #60a5fa; }
        .nav-item.active .nav-icon { color: #3b82f6; }
        .nav-icon { width: 18px; height: 18px; flex-shrink: 0; }

        /* Cards */
        .card {
            background: #1e2535;
            border: 1px solid #2a3347;
            border-radius: 14px;
        }
        .card-header {
            padding: 18px 20px;
            border-bottom: 1px solid #2a3347;
        }

        /* Badges */
        .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-aktif { background: #052e16; color: #4ade80; border: 1px solid #166534; }
        .badge-nonaktif { background: #1c1917; color: #94a3b8; border: 1px solid #44403c; }
        .badge-suspend { background: #2d1515; color: #f87171; border: 1px solid #7f1d1d; }
        .badge-lunas { background: #052e16; color: #4ade80; border: 1px solid #166534; }
        .badge-belum { background: #2d1515; color: #f87171; border: 1px solid #7f1d1d; }
        .badge-sebagian { background: #1c1407; color: #fbbf24; border: 1px solid #78350f; }
        .badge-open { background: #1e1b4b; color: #818cf8; border: 1px solid #3730a3; }
        .badge-progress { background: #1c1407; color: #fbbf24; border: 1px solid #78350f; }
        .badge-resolved { background: #052e16; color: #4ade80; border: 1px solid #166534; }
        .badge-closed { background: #1c1917; color: #94a3b8; border: 1px solid #44403c; }

        /* Inputs */
        .form-input, .form-select, .form-textarea {
            width: 100%;
            background: #0f1117;
            border: 1px solid #2a3347;
            border-radius: 8px;
            padding: 9px 12px;
            color: #e2e8f0;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
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

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; transition: all 0.2s; cursor: pointer; border: none; text-decoration: none; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary { background: #1e2535; color: #94a3b8; border: 1px solid #2a3347; }
        .btn-secondary:hover { background: #2a3347; color: #e2e8f0; }
        .btn-danger { background: #7f1d1d; color: #fca5a5; }
        .btn-danger:hover { background: #991b1b; }
        .btn-success { background: #052e16; color: #4ade80; border: 1px solid #166534; }
        .btn-success:hover { background: #065f46; }
        .btn-sm { padding: 5px 10px; font-size: 12px; border-radius: 6px; }

        /* Table */
        .tbl { width: 100%; border-collapse: separate; border-spacing: 0; }
        .tbl th { padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #2a3347; background: #161b27; }
        .tbl td { padding: 14px 16px; font-size: 14px; color: #cbd5e1; border-bottom: 1px solid #1e2535; }
        .tbl tr:hover td { background: rgba(30,37,53,0.5); }
        .tbl tr:last-child td { border-bottom: none; }

        /* Section header */
        .section-title { font-size: 18px; font-weight: 700; color: #f1f5f9; }
        .section-sub { font-size: 13px; color: #64748b; margin-top: 2px; }

        /* Stat card */
        .stat-card {
            background: linear-gradient(135deg, #1e2535, #1a2030);
            border: 1px solid #2a3347;
            border-radius: 14px;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 80px; height: 80px;
            border-radius: 50%;
            opacity: 0.08;
            transform: translate(20px, -20px);
        }

        /* Alert */
        .alert { padding: 12px 16px; border-radius: 10px; font-size: 14px; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #052e16; border: 1px solid #166534; color: #4ade80; }
        .alert-error { background: #2d1515; border: 1px solid #7f1d1d; color: #f87171; }

        /* Priority colors */
        .prio-rendah { color: #4ade80; }
        .prio-sedang { color: #fbbf24; }
        .prio-tinggi { color: #fb923c; }
        .prio-kritis { color: #f87171; }

        /* Scrollable table wrapper */
        .table-wrapper { overflow-x: auto; }

        /* Nav group label */
        .nav-group-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #374151;
            padding: 16px 16px 6px;
        }

        @media print {
            .sidebar, .topbar, .btn, .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; }
            body { background: white; color: black; }
            .card { background: white; border: 1px solid #ddd; }
            .tbl td, .tbl th { color: black; }
        }

        /* Chatbot Widget */
        .chatbot-btn { position: fixed; bottom: 20px; right: 20px; width: 56px; height: 56px; background: #2563eb; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; cursor: pointer; box-shadow: 0 4px 12px rgba(37,99,235,0.4); z-index: 1000; transition: transform 0.2s; border: none; }
        .chatbot-btn:hover { transform: scale(1.05); background: #1d4ed8; }
        .chat-window { position: fixed; bottom: 85px; right: 20px; width: 340px; height: 480px; background: #1e2535; border: 1px solid #2a3347; border-radius: 14px; display: none; flex-direction: column; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.5); z-index: 1000; }
        .chat-window.active { display: flex; }
        .chat-header { background: #2563eb; color: #fff; padding: 12px 16px; font-weight: 600; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1d4ed8; }
        .chat-close { cursor: pointer; background: transparent; border: none; color: #fff; font-size: 24px; line-height: 1; padding: 0; outline: none;}
        .chat-body { flex: 1; padding: 16px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; background: #0f1117;}
        .chat-msg { max-width: 85%; padding: 10px 14px; border-radius: 12px; font-size: 13px; line-height: 1.4; word-wrap: break-word; position: relative;}
        .chat-msg.bot { background: #1e2535; color: #e2e8f0; align-self: flex-start; border-bottom-left-radius: 2px; border: 1px solid #2a3347; }
        .chat-msg.user { background: #2563eb; color: #fff; align-self: flex-end; border-bottom-right-radius: 2px; }
        .chat-msg .time { font-size: 10px; opacity: 0.6; margin-top: 4px; text-align: right; display: block; }
        .chat-input-area { padding: 12px; background: #1e2535; border-top: 1px solid #2a3347; display: flex; gap: 8px; }
        .chat-input-area input { flex: 1; background: #0f1117; border: 1px solid #2a3347; color: #fff; padding: 8px 12px; border-radius: 20px; font-size: 13px; outline: none; }
        .chat-input-area input:focus { border-color: #3b82f6; }
        .chat-send-btn { background: #2563eb; border: none; width: 36px; height: 36px; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s; outline: none;}
        .chat-send-btn:hover { background: #1d4ed8; }
        @media (max-width: 400px) { .chat-window { width: calc(100% - 40px); right: 20px; bottom: 85px; } }
    </style>

    @stack('styles')
</head>
<body class="h-full flex">

    {{-- SIDEBAR --}}
    <aside class="sidebar fixed inset-y-0 left-0 flex flex-col z-50 overflow-y-auto">
        {{-- Logo --}}
        <div class="px-5 py-5 border-b border-[#1e2535]">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-3pp.jpeg') }}" alt="Logo 3PP" class="w-10 h-10 object-contain rounded-xl shadow-sm" />
                <div>
                    <div class="text-[13px] font-bold text-white leading-tight">Internet Service</div>
                    <div class="text-[10px] text-slate-500">Provider</div>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 p-3">
            <div class="nav-group-label">Utama</div>

            <a href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Dashboard
            </a>

            <div class="nav-group-label">Pelanggan & Billing</div>

            @if(auth()->user()->canViewPelanggan())
                <a href="{{ route('pelanggan.index') }}"
                   class="nav-item {{ request()->routeIs('pelanggan.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Data Pelanggan
                </a>
            @endif

            <a href="{{ route('billing.index') }}"
               class="nav-item {{ request()->routeIs('billing.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
                @if(auth()->user()->isPelanggan())
                    Tagihan Saya
                @else
                    Tagihan & Invoice
                @endif
            </a>

            @if(auth()->user()->canViewReports())
                <div class="nav-group-label">Laporan</div>

                <a href="{{ route('laporan.index') }}"
                   class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Laporan & Ekspor
                </a>
            @endif

            @if(auth()->user()->canManageUsers())
                <div class="nav-group-label">Pengaturan</div>

                <a href="{{ route('users.index') }}"
                   class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Manajemen User / Akses
                </a>
            @endif
        </nav>

        {{-- User info bottom --}}
        <div class="p-3 border-t border-[#1e2535]">
            <div class="flex items-center gap-3 p-2">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-[13px] font-semibold text-slate-300 truncate">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email ?? '' }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-500 hover:text-red-400 transition-colors" title="Logout">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col" style="margin-left: var(--sidebar-w);">

        {{-- Topbar --}}
        <header class="topbar sticky top-0 z-40 flex items-center justify-between px-6 py-4 border-b border-[#1e2535]" style="background: #0d1117;">
            <div>
                <h1 class="text-[16px] font-700 text-white">{{ $title ?? 'Dashboard' }}</h1>
                <div class="text-[12px] text-slate-500">PT. Tiga Putra Pandawa — {{ now()->isoFormat('dddd, D MMMM Y') }}</div>
            </div>
            <div class="flex items-center gap-3">
                @yield('topbar-actions')
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 p-6 overflow-y-auto">
            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Chatbot UI -->
    <button id="chatbot-toggle" class="chatbot-btn no-print">
        <img src="{{ asset('images/logo-3pp.jpeg') }}" alt="CS" class="w-full h-full rounded-full object-cover border-2 border-white" />
    </button>

    <div id="chat-window" class="chat-window no-print">
        <div class="chat-header">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-3pp.jpeg') }}" alt="CS" class="w-8 h-8 rounded-full object-cover border border-white" />
                <div>
                   <div style="font-size: 14px; line-height: 1;">Customer Service</div>
                   <div style="font-size: 10px; font-weight: normal; opacity: 0.8; margin-top: 2px;">Selalu Aktif</div>
                </div>
            </div>
            <button id="chat-close" class="chat-close">&times;</button>
        </div>
        <div id="chat-body" class="chat-body">
            <div class="chat-msg bot">
                Halo! Selamat datang di Layanan Bantuan PT. Tiga Putra Pandawa. Silakan pilih metode bantuan yang Anda inginkan:
                <div class="flex flex-col gap-2 mt-3">
                    <button onclick="handleChatOption('wa')" class="text-left px-3 py-2 text-xs bg-[#0f1117] border border-green-700 text-green-400 rounded-lg hover:bg-[#161b27] transition-colors">
                        <div class="flex items-center gap-2">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm.029 18.88c-1.161 0-2.305-.292-3.318-.844l-3.677.964.984-3.595c-.607-1.052-.927-2.246-.926-3.468.001-3.825 3.113-6.937 6.937-6.937 3.825 0 6.938 3.112 6.938 6.937 0 3.825-3.113 6.938-6.938 6.938z"/></svg>
                            Konsultasi via WhatsApp
                        </div>
                    </button>
                    <button onclick="handleChatOption('bot')" class="text-left px-3 py-2 text-xs bg-[#0f1117] border border-[#2a3347] text-blue-400 rounded-lg hover:bg-[#161b27] transition-colors">
                        <div class="flex items-center gap-2">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            Tanya Chatbot
                        </div>
                    </button>
                </div>
                <span class="time"></span>
            </div>
        </div>
        <div class="chat-input-area">
            <input type="text" id="chat-input" placeholder="Ketik pesan Anda..." autocomplete="off">
            <button id="chat-send" class="chat-send-btn">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('chatbot-toggle');
            const closeBtn = document.getElementById('chat-close');
            const chatWindow = document.getElementById('chat-window');
            const chatBody = document.getElementById('chat-body');
            const chatInput = document.getElementById('chat-input');
            const chatSend = document.getElementById('chat-send');

            window.handleChatOption = function(type) {
                if(type === 'wa') {
                    addMessage('Konsultasi via WhatsApp', true);
                    setTimeout(() => {
                        const msgDiv = document.createElement('div');
                        msgDiv.className = 'chat-msg bot';
                        msgDiv.innerHTML = `
                            Silakan pilih kategori keluhan untuk diteruskan ke WhatsApp:
                            <div class="flex flex-col gap-2 mt-3">
                                <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20ingin%20bertanya%20tentang%20Tagihan" target="_blank" class="text-center px-3 py-2 text-xs bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors no-underline">Informasi Tagihan</a>
                                <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20mengalami%20Gangguan%20Koneksi" target="_blank" class="text-center px-3 py-2 text-xs bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors no-underline">Gangguan Koneksi</a>
                                <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20ingin%20Pasang%20Baru" target="_blank" class="text-center px-3 py-2 text-xs bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors no-underline">Pasang Baru / Upgrade</a>
                            </div>
                            <span class="time">${getTime()}</span>
                        `;
                        chatBody.appendChild(msgDiv);
                        chatBody.scrollTop = chatBody.scrollHeight;
                    }, 500);
                } else if(type === 'bot') {
                    addMessage('Tanya Chatbot', true);
                    setTimeout(() => {
                        const msgDiv = document.createElement('div');
                        msgDiv.className = 'chat-msg bot';
                        msgDiv.innerHTML = `
                            Baik, silakan pilih topik pertanyaan atau ketikkan langsung di bawah:
                            <div class="flex flex-col gap-2 mt-3">
                                <button onclick="handleBotCategory('tagihan')" class="text-left px-3 py-2 text-xs bg-[#0f1117] border border-[#2a3347] text-blue-400 rounded-lg hover:bg-[#161b27] transition-colors">Informasi Tagihan</button>
                                <button onclick="handleBotCategory('koneksi mati')" class="text-left px-3 py-2 text-xs bg-[#0f1117] border border-[#2a3347] text-blue-400 rounded-lg hover:bg-[#161b27] transition-colors">Gangguan Koneksi</button>
                                <button onclick="handleBotCategory('ganti password')" class="text-left px-3 py-2 text-xs bg-[#0f1117] border border-[#2a3347] text-blue-400 rounded-lg hover:bg-[#161b27] transition-colors">Ganti Password WiFi</button>
                            </div>
                            <span class="time">${getTime()}</span>
                        `;
                        chatBody.appendChild(msgDiv);
                        chatBody.scrollTop = chatBody.scrollHeight;
                    }, 500);
                }
            };

            window.handleBotCategory = function(text) {
                chatInput.value = text;
                handleSend();
            };

            toggleBtn.addEventListener('click', () => {
                const isActive = chatWindow.classList.toggle('active');
                if(isActive) chatInput.focus();
            });

            closeBtn.addEventListener('click', () => {
                chatWindow.classList.remove('active');
            });

            function getTime() {
                const now = new Date();
                let h = now.getHours().toString().padStart(2, '0');
                let m = now.getMinutes().toString().padStart(2, '0');
                return `${h}:${m}`;
            }
            
            // Set init time for the welcome message
            const initMsgTime = document.querySelector('.chat-msg.bot .time');
            if(initMsgTime) initMsgTime.textContent = getTime();

            function addMessage(text, isUser = false) {
                const msgDiv = document.createElement('div');
                msgDiv.className = `chat-msg ${isUser ? 'user' : 'bot'}`;
                
                const spanStr = document.createElement('span');
                spanStr.textContent = text;
                msgDiv.appendChild(spanStr);

                const timeSpan = document.createElement('span');
                timeSpan.className = 'time';
                timeSpan.textContent = getTime();
                msgDiv.appendChild(timeSpan);

                chatBody.appendChild(msgDiv);
                chatBody.scrollTop = chatBody.scrollHeight;
            }

            function getBotResponse(input) {
                const lowerInput = input.toLowerCase();

                const responses = [
                    { keywords: ["halo", "hi", "hai", "ping", "pagi", "siang", "sore", "malam"], text: "Halo, ada yang bisa saya bantu terkait layanan internet Anda?" },
                    { keywords: ["tagihan", "bayar", "pembayaran", "rekening", "biaya", "harga"], text: "Untuk pembayaran tagihan, Anda dapat mentransfer ke nomor rekening yang tertera di halaman Invoice / Tagihan Anda di dalam aplikasi ini. Harap konfirmasi jika tagihan belum terupdate setelah Anda bayar." },
                    { keywords: ["mati", "putus", "los", "red", "merah", "tidak bisa internet", "terputus"], text: "Mohon maaf atas gangguan ini. Coba restart (cabut dan pasang listrik) router Anda dan biarkan sekitar 3 menit. Jika lampu indikator LOS (merah) masih menyala, mungkin ada kendala di kabel FO. Mohon konfirmasi ID pelanggan agar segera kami tindaklanjuti." },
                    { keywords: ["lambat", "lemot", "lelet", "lag", "buffering", "ping"], text: "Mohon maaf jika internet saat ini kurang optimal. Kami sarankan Anda restart perangkat router Anda sesaat. Pastikan juga ping tidak terbelah ke orang tak dikenal menggunakan WiFi Anda. Hubungi kami jika masih berlanjut." },
                    { keywords: ["paket", "upgrade", "tambah", "speed", "pasang", "baru"], text: "Untuk melihat opsi layanan lanjutan (upgrade bandwidth) atau jika Anda butuh info pemasangan titik baru, langsung drop pesan ke WhatsApp admin sales kami." },
                    { keywords: ["lupa", "password", "sandi", "ganti", "wifi"], text: "Demi keamanan, jika Anda butuh mengganti nama WiFi atau Password, infokan kepada sistem admin (via WhatsApp CS) dan sertakan ID Pelanggan. Perubahan dari jarak jauh akan kami pandu." },
                    { keywords: ["terima kasih", "makasih", "thanks", "ok", "oke", "sip", "baik", "mantap", "paham"], text: "Sama-sama! Kami selalu berusaha memberikan pelayanan terbaik. Semoga harimu menyenangkan dan koneksi inet selalu ngebut!" }
                ];

                for (let r of responses) {
                    for (let kw of r.keywords) {
                        if (lowerInput.includes(kw)) {
                            return r.text;
                        }
                    }
                }

                return "Mohon maaf, pesan Anda tidak saya pahami. Bisakah gunakan kata kunci utama seperti 'tagihan', 'internet mati', 'lambat', atau 'ganti password'?";
            }

            function handleSend() {
                const text = chatInput.value.trim();
                if(!text) return;

                addMessage(text, true);
                chatInput.value = '';

                // Simulate bot thinking delay
                setTimeout(() => {
                    const reply = getBotResponse(text);
                    addMessage(reply, false);
                }, 700);
            }

            chatSend.addEventListener('click', handleSend);
            chatInput.addEventListener('keypress', (e) => {
                if(e.key === 'Enter') handleSend();
            });
        });
    </script>
    @stack('scripts')
</body>
</html>