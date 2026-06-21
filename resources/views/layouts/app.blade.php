<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Remindly — @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @livewireStyles
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{
            --blue:#6d28d9;--blue-light:#ede9fe;--blue-mid:#7c3aed;
            --green:#059669;--green-light:#d1fae5;
            --red:#ea4335;--red-light:#fce8e6;
            --yellow:#fbbc04;--yellow-light:#fef7e0;
            --gray:#5f6368;--gray-light:#f8f9fa;--gray-border:#dadce0;
            --surface:#fff;--text:#202124;--text2:#5f6368;
            --font:'Inter',sans-serif;
            --r:8px;--rl:12px;--rxl:20px;
        }
        body,html{font-family:var(--font);color:var(--text);background:var(--gray-light);height:100%}
        .app{display:flex;height:100vh;min-height:700px}

        /* SIDEBAR */
        .sidebar{width:220px;flex-shrink:0;background:var(--surface);border-right:1px solid var(--gray-border);display:flex;flex-direction:column}
        .brand{display:flex;align-items:center;gap:10px;padding:16px 20px;border-bottom:1px solid var(--gray-border)}
        .brand-icon{width:32px;height:32px;border-radius:8px;background:var(--blue);display:flex;align-items:center;justify-content:center}
        .brand-icon i{color:#fff;font-size:18px}
        .brand-name{font-size:15px;font-weight:700;color:var(--text)}
        .brand-tag{font-size:10px;font-weight:600;color:var(--blue);background:var(--blue-light);padding:2px 7px;border-radius:20px;margin-left:2px}
        .nav-section{padding:14px 16px 4px;font-size:11px;font-weight:600;color:var(--text2);letter-spacing:.6px;text-transform:uppercase}
        .nav-item{display:flex;align-items:center;gap:10px;padding:9px 14px;border-radius:var(--rxl);margin:2px 8px;font-size:13.5px;font-weight:500;color:var(--text2);text-decoration:none;transition:background .15s,color .15s}
        .nav-item i{font-size:17px}
        .nav-item:hover{background:var(--gray-light);color:var(--text)}
        .nav-item.active{background:var(--blue-light);color:var(--blue)}
        .nav-badge{margin-left:auto;background:var(--red);color:#fff;font-size:11px;font-weight:600;padding:2px 7px;border-radius:20px;min-width:18px;text-align:center}
        .sidebar-footer{margin-top:auto;padding:14px;border-top:1px solid var(--gray-border)}
        .user-row{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:var(--rxl)}
        .avatar{width:32px;height:32px;border-radius:50%;background:var(--blue);color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0}
        .user-name{font-size:13px;font-weight:500;color:var(--text)}
        .user-plan{font-size:11px;color:var(--text2);margin-top:1px}

        /* MAIN */
        .main{flex:1;display:flex;flex-direction:column;overflow:hidden}

        /* TABS */
        .screen-tabs{display:flex;gap:2px;padding:0 24px;background:var(--surface);border-bottom:1px solid var(--gray-border)}
        .s-tab{padding:12px 18px;font-size:14px;font-weight:500;color:var(--text2);text-decoration:none;border-bottom:3px solid transparent;transition:all .15s;white-space:nowrap}
        .s-tab:hover{color:var(--text)}
        .s-tab.active{color:var(--blue);border-bottom-color:var(--blue)}

        /* TOPBAR */
        .topbar{display:flex;align-items:center;gap:16px;padding:13px 24px;background:var(--surface);border-bottom:1px solid var(--gray-border)}
        .page-title{font-size:18px;font-weight:500;color:var(--text)}
        .topbar-right{margin-left:auto;display:flex;align-items:center;gap:10px}

        /* BUTTONS */
        .btn{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:var(--rxl);font-family:var(--font);font-size:13.5px;font-weight:500;cursor:pointer;border:none;text-decoration:none;transition:all .15s}
        .btn i{font-size:16px}
        .btn-primary{background:var(--blue);color:#fff}
        .btn-primary:hover{background:var(--blue-mid);color:#fff}
        .btn-outline{background:transparent;color:var(--blue);border:1px solid var(--gray-border)}
        .btn-outline:hover{background:var(--blue-light)}

        .content{flex:1;overflow-y:auto;padding:24px}

        /* STAT CARDS */
        .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
        .stat-card{background:var(--surface);border:1px solid var(--gray-border);border-radius:var(--rl);padding:18px}
        .stat-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:10px}
        .stat-icon{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0}
        .stat-icon i{font-size:19px}
        .si-blue{background:var(--blue-light);color:var(--blue)}
        .si-green{background:var(--green-light);color:var(--green)}
        .si-red{background:var(--red-light);color:var(--red)}
        .si-yellow{background:var(--yellow-light);color:#b06000}
        .stat-label{font-size:11.5px;font-weight:500;color:var(--text2);letter-spacing:.2px;margin-bottom:6px}
        .stat-value{font-size:24px;font-weight:400;color:var(--text);letter-spacing:-1px;margin-bottom:4px}
        .stat-sub{font-size:12px;display:flex;align-items:center;gap:4px}
        .stat-sub i{font-size:12px}
        .sub-red{color:var(--red)}
        .sub-green{color:var(--green)}
        .sub-gray{color:var(--text2)}

        /* DASHBOARD GRID */
        .dash-grid{display:grid;grid-template-columns:1fr 360px;gap:14px}

        /* CARDS */
        .card{background:var(--surface);border:1px solid var(--gray-border);border-radius:var(--rl);overflow:hidden}
        .card-head{display:flex;align-items:center;gap:12px;padding:13px 18px;border-bottom:1px solid var(--gray-border)}
        .card-title{font-size:14px;font-weight:500;color:var(--text)}

        /* SEARCH + CHIPS */
        .search-bar{display:flex;align-items:center;gap:8px;background:var(--gray-light);border:1px solid var(--gray-border);border-radius:var(--rxl);padding:6px 14px;flex:1;max-width:300px}
        .search-bar input{border:none;background:transparent;font-family:var(--font);font-size:13.5px;color:var(--text);outline:none;width:100%}
        .search-bar i{color:var(--text2);font-size:15px}
        .filter-chips{display:flex;gap:6px;margin-left:auto}
        .chip{padding:4px 13px;border-radius:20px;font-size:12.5px;font-weight:500;cursor:pointer;border:1px solid var(--gray-border);background:var(--surface);color:var(--text2);transition:all .15s}
        .chip.on{background:var(--blue-light);color:var(--blue);border-color:var(--blue)}

        /* TABLE */
        table{width:100%;border-collapse:collapse}
        thead th{text-align:left;padding:10px 18px;font-size:11.5px;font-weight:500;color:var(--text2);letter-spacing:.3px;border-bottom:1px solid var(--gray-border);background:var(--gray-light)}
        tbody tr{border-bottom:1px solid var(--gray-border);transition:background .1s;cursor:pointer}
        tbody tr:last-child{border-bottom:none}
        tbody tr:hover{background:#f8f9fa}
        tbody td{padding:13px 18px;font-size:13.5px;color:var(--text)}

        /* CLIENT AVATARS */
        .client-cell{display:flex;align-items:center;gap:9px}
        .av{width:28px;height:28px;border-radius:50%;font-size:11px;font-weight:700;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0}
        .av-r{background:#ea4335}.av-b{background:#7c3aed}.av-g{background:#059669}.av-o{background:#f97316}.av-p{background:#9c27b0}

        /* STATUS PILLS */
        .pill{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:500}
        .pill-dot{width:6px;height:6px;border-radius:50%;background:currentColor;flex-shrink:0}
        .pill-paid{background:#d1fae5;color:#059669}
        .pill-overdue{background:#fce8e6;color:#ea4335}
        .pill-pending{background:#fef7e0;color:#b06000}
        .pill-sent{background:#ede9fe;color:#6d28d9}

        /* RISK BAR */
        .risk-bar{display:flex;align-items:center;gap:7px}
        .risk-track{width:48px;height:4px;border-radius:2px;background:#e0e0e0;overflow:hidden}
        .risk-fill{height:100%;border-radius:2px}
        .rf-low{background:var(--green)}.rf-mid{background:var(--yellow)}.rf-high{background:var(--red)}

        /* AI PANEL */
        .ai-badge{background:linear-gradient(135deg,#7c3aed,#059669);color:#fff;font-size:11px;font-weight:600;padding:2px 9px;border-radius:20px}
        .insight{display:flex;gap:10px;padding:11px 0;border-bottom:1px solid var(--gray-border)}
        .insight:last-child{border-bottom:none;padding-bottom:0}
        .i-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0;margin-top:5px}
        .dot-red{background:var(--red)}.dot-yellow{background:var(--yellow)}.dot-green{background:var(--green)}.dot-blue{background:var(--blue)}
        .i-text{font-size:12.5px;color:var(--text);line-height:1.55}
        .i-action{font-size:12px;color:var(--blue);cursor:pointer;margin-top:3px}

        /* REMINDER FLOW */
        .flow-card{background:var(--surface);border:1px solid var(--gray-border);border-radius:var(--rl);overflow:hidden;margin-top:12px}
        .r-item{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border-bottom:1px solid var(--gray-border)}
        .r-item:last-child{border-bottom:none}
        .r-step{width:26px;height:26px;border-radius:50%;background:var(--blue-light);color:var(--blue);font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px}
        .r-title{font-size:13px;color:var(--text);font-weight:500;margin-bottom:1px}
        .r-sub{font-size:11.5px;color:var(--text2)}
        .r-ai{font-size:11px;color:var(--blue);background:var(--blue-light);padding:2px 8px;border-radius:10px;margin-top:4px;display:inline-block}

        /* EMPTY */
        .empty{padding:40px;text-align:center;color:var(--text2);font-size:13.5px}
        .empty a{color:var(--blue);font-weight:500;text-decoration:none}
    </style>
</head>
<body>
<div class="app">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon"><i class="ti ti-bell-ringing"></i></div>
            <span class="brand-name">Remindly</span>
            <span class="brand-tag">AI</span>
        </div>

        <nav style="flex:1;overflow-y:auto;padding-top:6px">
            <div class="nav-section">Main</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard"></i> Dashboard
            </a>
            <a href="{{ route('invoices.index') }}" class="nav-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <i class="ti ti-file-invoice"></i> Invoices
                @php $overdueCount = \App\Models\Invoice::where('user_id', auth()->id())->where('status','overdue')->count(); @endphp
                @if($overdueCount)<span class="nav-badge">{{ $overdueCount }}</span>@endif
            </a>
            <a href="{{ route('recurring.index') }}" class="nav-item {{ request()->routeIs('recurring.*') ? 'active' : '' }}">
                <i class="ti ti-refresh"></i> Recurring
            </a>
            <a href="{{ route('clients.index') }}" class="nav-item {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i class="ti ti-users"></i> Clients
            </a>

            <div class="nav-section" style="margin-top:6px">AI & Analytics</div>
            <a href="{{ route('ai.index') }}" class="nav-item {{ request()->routeIs('ai.*') ? 'active' : '' }}">
                <i class="ti ti-sparkles"></i> AI Engine
            </a>
            <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="ti ti-chart-bar"></i> Analytics
            </a>

            <div class="nav-section" style="margin-top:6px">Settings</div>
            <a href="{{ route('reminders.index') }}" class="nav-item {{ request()->routeIs('reminders.*') ? 'active' : '' }}">
                <i class="ti ti-bell"></i> Reminders
            </a>
            <a href="{{ route('payments.index') }}" class="nav-item {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <i class="ti ti-credit-card"></i> Payments
            </a>
            <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="ti ti-settings"></i> Settings
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-row">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-plan">Pro Plan</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="main">

        {{-- TABS --}}
        <div class="screen-tabs">
            <a href="{{ route('dashboard') }}" class="s-tab {{ request()->routeIs('dashboard') ? 'active' : '' }}">Overview</a>
            <a href="{{ route('invoices.index') }}" class="s-tab {{ request()->routeIs('invoices.*') ? 'active' : '' }}">Invoices</a>
            <a href="{{ route('ai.index') }}" class="s-tab {{ request()->routeIs('ai.*') ? 'active' : '' }}">AI Engine</a>
            <a href="{{ route('clients.index') }}" class="s-tab {{ request()->routeIs('clients.*') ? 'active' : '' }}">Clients</a>
            <a href="{{ route('reports.index') }}" class="s-tab {{ request()->routeIs('reports.*') ? 'active' : '' }}">Analytics</a>
        </div>

        {{-- TOPBAR --}}
        <div class="topbar">
            <span class="page-title">@yield('page-title','Dashboard')</span>
            <div class="topbar-right">
                @yield('topbar-actions')
            </div>
        </div>

        {{-- CONTENT --}}
        <div class="content">
            @yield('content')
        </div>

    </div>
</div>
@livewireScripts
@stack('scripts')
</body>
</html>