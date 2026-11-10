<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK Evaluasi Kinerja Aslab — LABCOM WICIDA</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-labcom.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Poppins"', 'ui-sans-serif', 'sans-serif'] },
                    colors: {
                        brand: {
                            50:  '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                            950: '#2e1065',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background: #f8f7ff;
        }
        h1,h2,h3,h4,h5,h6 { font-weight: 700; letter-spacing: -0.02em; }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 10px;
            font-size: 13px; font-weight: 500; color: #c4b5fd;
            transition: all .18s; cursor: pointer; text-decoration: none;
        }
        .sidebar-link:hover { background: rgba(139,92,246,.18); color: #fff; }
        .sidebar-link.active { background: rgba(139,92,246,.3); color: #fff; font-weight: 600; }
        .sidebar-link .icon { width: 16px; height: 16px; opacity: .7; flex-shrink: 0; }
        .sidebar-link.active .icon { opacity: 1; }

        /* Scrollbar tipis */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #ddd6fe; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #a78bfa; }

        /* Form inputs */
        .form-input {
            width: 100%; padding: 10px 16px;
            border: 1.5px solid #e2e8f0; border-radius: 10px;
            font-size: 13.5px; font-family: 'Poppins', sans-serif;
            outline: none; transition: border .18s, box-shadow .18s;
            background: #fff;
        }
        .form-input:focus { border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,.12); }
        .form-label {
            font-size: 11px; font-weight: 600; color: #64748b;
            text-transform: uppercase; letter-spacing: .06em; display: block; margin-bottom: 4px;
        }

        /* Btn primary */
        .btn-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            background: #6d28d9; color: #fff; font-weight: 700; font-size: 13.5px;
            padding: 11px 20px; border-radius: 10px; border: none; cursor: pointer;
            transition: background .18s, box-shadow .18s; font-family: 'Poppins', sans-serif;
            box-shadow: 0 4px 14px rgba(109,40,217,.25);
        }
        .btn-primary:hover { background: #5b21b6; }

        /* Card */
        .card { background: #fff; border-radius: 16px; border: 1.5px solid #ede9fe; box-shadow: 0 1px 4px rgba(109,40,217,.06); }

        /* Badge */
        .badge-purple { background: #ede9fe; color: #6d28d9; font-weight: 700; font-size: 11px; padding: 2px 10px; border-radius: 99px; }
        .badge-green  { background: #dcfce7; color: #15803d; font-weight: 700; font-size: 11px; padding: 2px 10px; border-radius: 99px; }
        .badge-red    { background: #fee2e2; color: #b91c1c; font-weight: 700; font-size: 11px; padding: 2px 10px; border-radius: 99px; }
        .badge-amber  { background: #fef3c7; color: #92400e; font-weight: 700; font-size: 11px; padding: 2px 10px; border-radius: 99px; }

        /* Alert */
        .alert-success { display:flex; align-items:center; gap:10px; background:#f0fdf4; border:1.5px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:12px; font-size:13px; }
        .alert-error   { display:flex; align-items:center; gap:10px; background:#fef2f2; border:1.5px solid #fecaca; color:#991b1b; padding:12px 16px; border-radius:12px; font-size:13px; }
        .alert-info    { display:flex; align-items:center; gap:10px; background:#eff6ff; border:1.5px solid #bfdbfe; color:#1e40af; padding:12px 16px; border-radius:12px; font-size:13px; }
        .alert-warning { display:flex; align-items:center; gap:10px; background:#fffbeb; border:1.5px solid #fde68a; color:#92400e; padding:12px 16px; border-radius:12px; font-size:13px; }

        /* Table */
        .data-table { width:100%; border-collapse:collapse; font-size:13px; }
        .data-table thead tr { background:#f8f7ff; border-bottom:1.5px solid #ede9fe; }
        .data-table thead th { padding:12px 16px; color:#7c3aed; font-weight:700; font-size:11px; text-transform:uppercase; letter-spacing:.05em; }
        .data-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background .12s; }
        .data-table tbody tr:hover { background:#faf5ff; }
        .data-table tbody td { padding:13px 16px; color:#334155; vertical-align:middle; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    {{-- =========== SIDEBAR =========== --}}
    <aside style="width:240px; background:linear-gradient(175deg,#3b0764 0%,#4c1d95 60%,#2e1065 100%); display:flex; flex-direction:column; flex-shrink:0; z-index:30; box-shadow:4px 0 24px rgba(109,40,217,.18);">

        {{-- Brand --}}
        <div style="padding:20px 16px 16px; border-bottom:1px solid rgba(196,181,253,.15);">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:42px;height:42px;border-radius:12px;background:#fff;padding:4px;flex-shrink:0;border:1.5px solid rgba(196,181,253,.3);display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.12);">
                    <img src="{{ asset('images/logo-labcom.png') }}" alt="Logo" style="width:100%;height:100%;object-fit:contain;">
                </div>
                <div>
                    <div style="font-size:13px;font-weight:800;color:#ede9fe;letter-spacing:.04em;line-height:1.2;">LABCOM WICIDA</div>
                    <div style="font-size:10px;color:#a78bfa;font-weight:500;margin-top:1px;">SPK Evaluasi Aslab</div>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <nav style="padding:12px 10px; flex:1; overflow-y:auto;">
            @if(Auth::check())
            @php $user = Auth::user(); @endphp

            @if($user->role === 'admin')
            <div style="font-size:9px;font-weight:700;color:#7c3aed;letter-spacing:.1em;text-transform:uppercase;padding:4px 12px 6px;">Menu Admin</div>

            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="icon"></i> Dashboard
            </a>
            <a href="{{ route('admin.penilaian.hasil') }}"
               class="sidebar-link {{ request()->routeIs('admin.penilaian.*') ? 'active' : '' }}">
                <i data-lucide="award" class="icon"></i> Hasil & Peringkat
            </a>

            <div style="font-size:9px;font-weight:700;color:#7c3aed;letter-spacing:.1em;text-transform:uppercase;padding:12px 12px 6px;">Master Data</div>

            <a href="{{ route('admin.asisten.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.asisten.*') ? 'active' : '' }}">
                <i data-lucide="users" class="icon"></i> Data Asisten
            </a>
            <a href="{{ route('admin.kriteria.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.kriteria.*') ? 'active' : '' }}">
                <i data-lucide="list-todo" class="icon"></i> Kriteria & Rubrik
            </a>
            <a href="{{ route('admin.semester.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.semester.*') ? 'active' : '' }}">
                <i data-lucide="calendar-days" class="icon"></i> Kelola Semester
            </a>

            @elseif($user->role === 'kalab')
            <div style="font-size:9px;font-weight:700;color:#7c3aed;letter-spacing:.1em;text-transform:uppercase;padding:4px 12px 6px;">Menu Kalab</div>

            <a href="{{ route('kalab.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('kalab.dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="icon"></i> Dashboard
            </a>
            <a href="{{ route('kalab.penilaian.index') }}"
               class="sidebar-link {{ request()->routeIs('kalab.penilaian.index','kalab.penilaian.create') ? 'active' : '' }}">
                <i data-lucide="clipboard-pen" class="icon"></i> Input Penilaian
            </a>
            <a href="{{ route('kalab.penilaian.hasil') }}"
               class="sidebar-link {{ request()->routeIs('kalab.penilaian.hasil','kalab.penilaian.detail') ? 'active' : '' }}">
                <i data-lucide="bar-chart-3" class="icon"></i> Hasil Rangking
            </a>
            @endif
            @endif
        </nav>

        {{-- User Info --}}
        @if(Auth::check())
        <div style="padding:12px 10px; border-top:1px solid rgba(196,181,253,.15);">
            <div style="display:flex;align-items:center;justify-content:space-between;background:rgba(139,92,246,.15);border-radius:12px;padding:10px 12px;">
                <div style="display:flex;align-items:center;gap:8px;min-width:0;">
                    <div style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#8b5cf6,#6d28d9);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:12px;color:#fff;flex-shrink:0;border:1.5px solid rgba(196,181,253,.3);">
                        {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                    </div>
                    <div style="min-width:0;overflow:hidden;">
                        <div style="font-size:12px;font-weight:700;color:#ede9fe;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Auth::user()->name }}</div>
                        <div style="font-size:9.5px;color:#a78bfa;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">
                            {{ Auth::user()->role === 'admin' ? 'Administrator' : 'Kepala Lab' }}
                        </div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Keluar" style="color:#c4b5fd;background:none;border:none;cursor:pointer;padding:4px;border-radius:6px;display:flex;align-items:center;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#c4b5fd'">
                        <i data-lucide="log-out" style="width:15px;height:15px;"></i>
                    </button>
                </form>
            </div>
        </div>
        @endif
    </aside>

    {{-- =========== MAIN CONTENT =========== --}}
    <div style="flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0;">

        {{-- Top Bar --}}
        <header style="background:#fff;border-bottom:1.5px solid #ede9fe;padding:0 28px;height:60px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;z-index:20;box-shadow:0 1px 0 #f3f0ff;">
            <div>
                <div style="font-size:15px;font-weight:700;color:#2e1065;letter-spacing:-.02em;">
                    @if(Auth::check() && Auth::user()->role === 'admin')
                        Sistem Pendukung Keputusan &mdash; Administrator
                    @else
                        Sistem Pendukung Keputusan &mdash; Kepala Laboratorium
                    @endif
                </div>
                <div style="font-size:11px;color:#94a3b8;margin-top:1px;display:flex;align-items:center;gap:6px;">
                    <span>STMIK Widya Cipta Dharma</span>
                    <span style="color:#ddd6fe;">·</span>
                    <span>Profile Matching</span>
                    @if(isset($currentSemester) && $currentSemester)
                        <span style="color:#ddd6fe;">·</span>
                        <span style="color:#7c3aed;font-weight:600;">{{ $currentSemester->nama_semester }}</span>
                    @endif
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:12px;">
                @if(isset($isArchive) && $isArchive)
                <span style="font-size:11px;font-weight:700;padding:4px 12px;border-radius:99px;background:#fef3c7;color:#92400e;border:1.5px solid #fde68a;">
                    <i data-lucide="archive" style="width:11px;height:11px;display:inline;vertical-align:middle;margin-right:4px;"></i>
                    Arsip · Hanya Baca
                </span>
                @elseif(isset($currentSemester) && $currentSemester)
                <span style="font-size:11px;font-weight:700;padding:4px 12px;border-radius:99px;background:#dcfce7;color:#166534;border:1.5px solid #bbf7d0;">
                    <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#22c55e;margin-right:5px;vertical-align:middle;"></span>
                    Semester Aktif
                </span>
                @endif

                @if(isset($allSemesters) && $allSemesters->count())
                <form method="GET" action="{{ url()->current() }}" style="display:flex;align-items:center;gap:6px;">
                    <i data-lucide="calendar" style="width:14px;height:14px;color:#7c3aed;"></i>
                    <select name="semester_id" onchange="this.form.submit()"
                        style="font-family:'Poppins',sans-serif;font-size:12px;font-weight:600;color:#4c1d95;border:1.5px solid #ddd6fe;border-radius:8px;padding:6px 10px;cursor:pointer;background:#f8f7ff;outline:none;">
                        @foreach($allSemesters as $s)
                            <option value="{{ $s->id }}" @selected(isset($currentSemester) && $currentSemester?->id === $s->id)>
                                {{ $s->nama_semester }}{{ $s->status === 'aktif' ? ' ✓' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
                @endif
            </div>
        </header>

        {{-- Page Content --}}
        <main style="flex:1;overflow-y:auto;padding:28px 28px 40px;">
            @yield('content')
        </main>
    </div>

    <script>
        lucide.createIcons();
        if (window.Chart) Chart.defaults.font.family = "'Poppins', sans-serif";
    </script>
</body>
</html>
