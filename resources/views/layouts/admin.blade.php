<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') | SiswaMart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        :root {
            --color-primary:       #FF7A00;
            --color-primary-dark:  #E66A00;
            --bg-body:             #FFF8E8;
            --text-main:           #241A14;
            --bg-surface:          #FFFCF5;
            --border-color:        #241A14;
            --text-muted:          #64748B;
            --bg-hover:            #FFF8E8;
            --bg-active:           #FF7A00;
            --text-active:         #241A14;
            --input-border:        #241A14;
            
            /* Status colors */
            --color-success: #5B8C51;
            --color-warning: #FFD84D;
            --color-danger: #DC2626;
        }

        * { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; box-sizing: border-box; }
        .font-display { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; }
        body { background: var(--bg-body); color: var(--text-main); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #bdc1c6; border-radius: 3px; }

        /* ── Sidebar ── */
        .app-sidebar {
            background: var(--bg-surface);
            border-right: 1px solid var(--border-color);
            width: 256px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
        }
        .sidebar-section-title {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 16px 16px 6px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 9999px;
            margin: 4px 12px;
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-muted);
            transition: all 0.15s;
            text-decoration: none;
            border: 2px solid transparent;
        }
        .sidebar-link svg { flex-shrink: 0; }
        .sidebar-link:hover { 
            background: var(--color-primary-light); 
            color: var(--text-main); 
            border-color: var(--border-color);
            transform: translate(-2px, -2px);
            box-shadow: 4px 4px 0px 0px var(--border-color);
        }
        .sidebar-link.active { 
            background: var(--bg-active); 
            color: var(--bg-surface); 
            font-weight: 800; 
            border: 2px solid var(--border-color);
            box-shadow: 2px 2px 0px 0px var(--border-color);
        }
        .sidebar-link.active svg { color: var(--bg-surface); }

        /* ── Topbar ── */
        .app-topbar {
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-color);
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 30;
        }

        /* ── Cards & Tables ── */
        .admin-card {
            background: var(--bg-surface);
            border: 2px solid var(--border-color);
            border-radius: 24px;
            box-shadow: 4px 4px 0px 0px var(--border-color);
            overflow: hidden;
        }
        .admin-card-header {
            padding: 16px 20px;
            border-bottom: 2px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .stat-card {
            background: var(--bg-surface);
            border: 2px solid var(--border-color);
            border-radius: 24px;
            box-shadow: 4px 4px 0px 0px var(--border-color);
            padding: 20px;
        }

        /* Forms */
        .form-input {
            width: 100%;
            background: var(--bg-body);
            border: 2px solid var(--input-border);
            color: var(--text-main);
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 0.875rem;
            box-shadow: 2px 2px 0px 0px var(--border-color);
            transition: all 0.2s;
        }
        .form-input:focus { outline: none; background: var(--bg-surface); transform: translate(-2px, -2px); box-shadow: 4px 4px 0px 0px var(--border-color); }

        /* Buttons */
        .btn-primary {
            background: var(--color-primary);
            color: var(--border-color);
            font-weight: 800;
            font-size: 0.875rem;
            padding: 8px 16px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            border: 2px solid var(--border-color);
            box-shadow: 4px 4px 0px 0px var(--border-color);
        }
        .btn-primary:hover { 
            background: var(--color-primary-dark); 
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0px 0px var(--border-color);
        }
        
        .btn-outline {
            background: var(--bg-surface);
            color: var(--text-main);
            border: 2px solid var(--border-color);
            font-weight: 800;
            font-size: 0.875rem;
            padding: 8px 16px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 4px 4px 0px 0px var(--border-color);
        }
        .btn-outline:hover { 
            background: var(--color-primary-light);
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0px 0px var(--border-color);
        }

        /* Flash */
        .admin-flash {
            position: fixed;
            bottom: 24px;
            left: 24px;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            box-shadow: 4px 4px 0 var(--ink);
            padding: 12px 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-main);
            z-index: 9999;
            animation: slideUp 0.3s ease-out forwards;
        }
        @keyframes slideUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        /* Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-aktif { background: #e6f4ea; color: #137333; }
        .badge-pending { background: #fef7e0; color: #b06000; }
        .badge-ditolak { background: #fce8e6; color: #c5221f; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/siswamart-theme.css') }}">
</head>
<body class="admin-dashboard flex min-h-screen">

    {{-- ── Sidebar ── --}}
    <aside class="app-sidebar hidden lg:flex">
        <div class="h-16 flex items-center px-6 border-b border-[var(--border-color)]">
            <a href="/" class="flex items-center group transition-transform hover:scale-105 text-xl font-black tracking-tight drop-shadow-sm">
                <span class="text-[var(--text-main)]">Siswa</span><span class="text-[#ea580c]">Mart</span>
            </a>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-4">
            <div class="sidebar-section-title">Menu Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            
            <div class="sidebar-section-title mt-4">Manajemen</div>
            <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Kategori
            </a>
            <a href="{{ route('admin.penjual.index') }}" class="sidebar-link {{ request()->routeIs('admin.penjual.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Penjual
            </a>
            <a href="{{ route('admin.produk.index') }}" class="sidebar-link {{ request()->routeIs('admin.produk.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Moderasi Produk
            </a>
            <a href="{{ route('admin.komentar.index') }}" class="sidebar-link {{ request()->routeIs('admin.komentar.*') || request()->routeIs('admin.ulasan.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Kelola Komentar
            </a>
            <a href="{{ route('admin.laporan.index') }}" class="sidebar-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                Rekap Admin
            </a>
        </nav>

        {{-- User info --}}
        <div class="p-4 border-t-2 border-[var(--border-color)] flex-shrink-0">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-[var(--color-primary)] flex items-center justify-center text-[var(--border-color)] border-2 border-[var(--border-color)] font-bold text-sm shadow-[2px_2px_0px_0px_var(--border-color)]">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-[var(--ink)] truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[var(--ink-soft)] font-semibold text-xs capitalize">Admin SiswaMart</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-xs font-bold text-[#d93025] hover:bg-[#fce8e6] border-2 border-[#d93025] py-1.5 rounded-full shadow-[2px_2px_0px_0px_#d93025] transition-all hover:translate-x-[-1px] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_0px_#d93025]">Keluar</button>
            </form>
        </div>
    </aside>

    {{-- ── Main Area ── --}}
    <div class="flex-1 min-w-0 flex flex-col min-h-screen">
        {{-- Topbar --}}
        <header class="app-topbar">
            <div class="flex items-center gap-3 w-full mx-auto">
                <button class="p-2 rounded-full hover:bg-[var(--bg-hover)] text-[var(--text-muted)] lg:hidden flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex-1 min-w-0">
                    <h1 class="font-display font-black tracking-tight text-xl text-[var(--text-main)] truncate">@yield('page-title', 'Admin Panel')</h1>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @php
                        $adminNotifications = Auth::user()->notifications()->latest()->limit(8)->get();
                        $unreadNotificationCount = Auth::user()->unreadNotifications()->count();
                    @endphp
                    <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                        <button type="button" @click="open = !open" class="relative w-10 h-10 rounded-full bg-[var(--bg-surface)] text-[var(--text-main)] border-2 border-[var(--border-color)] flex items-center justify-center shadow-[2px_2px_0px_0px_var(--border-color)] hover:-translate-y-0.5 transition-all" aria-label="Notifikasi" :aria-expanded="open.toString()">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span id="adminNotificationCount" class="absolute -top-1 -right-1 min-w-5 h-5 px-1 rounded-full bg-[#ea4335] text-white text-[10px] font-black flex items-center justify-center border-2 border-[var(--bg-surface)] {{ $unreadNotificationCount > 0 ? '' : 'hidden' }}">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
                        </button>

                        <div x-show="open" @click.outside="open = false" x-cloak x-transition class="absolute right-0 top-12 w-[min(380px,calc(100vw-2rem))] bg-[var(--bg-surface)] border-2 border-[var(--border-color)] rounded-2xl shadow-[5px_5px_0px_0px_var(--border-color)] overflow-hidden z-50">
                            <div class="flex items-center justify-between gap-3 px-4 py-3 border-b-2 border-[var(--border-color)]">
                                <h2 class="text-sm font-black text-[var(--text-main)]">Notifikasi</h2>
                                @if($unreadNotificationCount > 0)
                                    <form method="POST" action="{{ route('admin.notifications.read_all') }}">
                                        @csrf
                                        <button type="submit" class="text-[11px] font-bold text-[#1a73e8] hover:underline">Tandai semua sudah dibaca</button>
                                    </form>
                                @endif
                            </div>
                            <div id="adminNotificationList" class="max-h-96 overflow-y-auto">
                                @forelse($adminNotifications as $notification)
                                    @php($notificationData = $notification->data)
                                    <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}" class="border-b border-[var(--border-color)]/20">
                                        @csrf
                                        <button type="submit" class="block w-full px-4 py-3 text-left hover:bg-[var(--bg-hover)] transition-colors {{ $notification->read_at ? '' : 'bg-[#FFF3E0]' }}">
                                        <div class="flex items-start gap-3">
                                            <span class="mt-1 w-2 h-2 rounded-full flex-shrink-0 {{ $notification->read_at ? 'bg-transparent' : 'bg-[#ea4335]' }}"></span>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-black text-[var(--text-main)]">{{ $notificationData['title'] ?? 'Aktivitas baru' }}</p>
                                                <p class="text-xs text-[var(--text-muted)] mt-1 leading-relaxed">{{ $notificationData['description'] ?? '' }}</p>
                                                <p class="text-[10px] text-[var(--text-muted)] mt-1">{{ $notification->created_at?->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        </button>
                                    </form>
                                @empty
                                    <p class="px-4 py-8 text-center text-xs font-bold text-[var(--text-muted)]">Belum ada notifikasi.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-[var(--color-primary-light)] text-[var(--text-main)] border-2 border-[var(--border-color)] font-bold text-sm flex items-center justify-center shadow-[2px_2px_0px_0px_var(--border-color)]">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div id="adminFlash" class="admin-flash">
            <svg class="w-4 h-4 text-[#34a853] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div id="adminFlash" class="admin-flash">
            <svg class="w-4 h-4 text-[#ea4335] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
        @endif

        <main class="flex-1 p-6 overflow-x-hidden">
            <div class="max-w-6xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // Auto-hide flash
        setTimeout(() => {
            const el = document.getElementById('adminFlash');
            if (el) { el.style.transition = 'opacity 0.4s'; el.style.opacity = '0'; setTimeout(() => el.remove(), 400); }
        }, 3500);

        const notificationList = document.getElementById('adminNotificationList');
        const notificationCount = document.getElementById('adminNotificationCount');
        const notificationUrl = @json(route('admin.notifications.poll'));
        const notificationReadUrl = @json(route('admin.notifications.read', ['notification' => '__ID__']));
        const notificationCsrfToken = @json(csrf_token());
        const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[char]));
        const refreshAdminNotifications = async () => {
            try {
                const response = await fetch(notificationUrl, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) return;
                const data = await response.json();
                notificationCount.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                notificationCount.classList.toggle('hidden', data.unread_count < 1);
                notificationList.innerHTML = data.notifications.length
                    ? data.notifications.map((item) => `<form method="POST" action="${notificationReadUrl.replace('__ID__', encodeURIComponent(item.id))}" class="border-b border-[var(--border-color)]/20"><input type="hidden" name="_token" value="${escapeHtml(notificationCsrfToken)}"><button type="submit" class="block w-full px-4 py-3 text-left hover:bg-[var(--bg-hover)] ${item.read_at ? '' : 'bg-[#FFF3E0]'}"><div class="flex items-start gap-3"><span class="mt-1 w-2 h-2 rounded-full flex-shrink-0 ${item.read_at ? 'bg-transparent' : 'bg-[#ea4335]'}"></span><div class="min-w-0 flex-1"><p class="text-xs font-black text-[var(--text-main)]">${escapeHtml(item.data.title || 'Aktivitas baru')}</p><p class="text-xs text-[var(--text-muted)] mt-1 leading-relaxed">${escapeHtml(item.data.description || '')}</p><p class="text-[10px] text-[var(--text-muted)] mt-1">${escapeHtml(item.created_at || '')}</p></div></div></button></form>`).join('')
                    : '<p class="px-4 py-8 text-center text-xs font-bold text-[var(--text-muted)]">Belum ada notifikasi.</p>';
            } catch (error) {
                // Polling is best-effort; the persisted notification menu remains usable.
            }
        };
        setInterval(refreshAdminNotifications, 10000);
    </script>
@unless(request()->routeIs('admin.dashboard'))
    <x-back-floating :fallback="route('admin.dashboard')" />
@endunless
</body>
</html>


