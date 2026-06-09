<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CityTech — Installment System</title>
    
    <!-- Font Configuration -->
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Brand Tokens -->
    @include('partials.brand')
    <style>
        /* Font: Khmer OS Siemreap for both English and Khmer */
        * { 
            font-family: 'Khmer OS Siemreap', 'KhmerOSSiemreap', 'Khmer OS', sans-serif !important;
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
        }
        body { margin: 0; padding: 0; background: var(--bg); overflow-x: hidden; }
        html { margin: 0; padding: 0; }

        /* ── Sidebar ── */
        #sidebar {
            width: 280px; min-height: 100vh; background: var(--sidebar);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; bottom: 0; z-index: 50;
            box-shadow: 4px 0 24px rgba(15,23,42,0.16);
            transition: transform 0.3s ease;
            overflow-x: hidden;
        }
        #sidebar.collapsed {
            transform: translateX(-280px);
        }
        
        /* Custom scrollbar for sidebar */
        #sidebar::-webkit-scrollbar {
            width: 0px;
        }
        .sb-logo {
            padding: 24px 20px 20px;
            display: flex; align-items: center; gap: 14px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sb-logo-icon {
            width: 50px; height: 50px; border-radius: 12px;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 24px; flex-shrink: 0;
            box-shadow: var(--shadow-accent);
        }
        .sb-logo h1 { font-size: 16px; font-weight: 800; color: #fff; line-height: 1.2; letter-spacing: 0.3px; }
        .sb-logo p  { font-size: 12px; color: var(--accent-soft); margin-top: 2px; }

        .sb-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .sb-nav::-webkit-scrollbar {
            width: 6px;
        }
        .sb-nav::-webkit-scrollbar-track {
            background: transparent;
        }
        .sb-nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 3px;
        }
        .sb-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.2);
        }
        .sb-nav a {
            display: flex; align-items: center; gap: 14px;
            padding: 14px 16px; border-radius: 12px; margin-bottom: 4px;
            font-size: 14px; font-weight: 600; color: #94a3b8;
            text-decoration: none; transition: all 0.2s ease;
            position: relative;
        }
        .sb-nav a i { width: 20px; text-align: center; font-size: 16px; }
        .sb-nav a:hover {
            background: var(--sidebar-hover);
            color: var(--sidebar-text-hi);
        }
        .sb-nav a.active {
            background: var(--accent);
            color: #fff;
            box-shadow: var(--shadow-accent);
        }
        .sb-nav a.active i { color: #fff; }

        /* Dropdown Menu */
        .sb-dropdown {
            margin-bottom: 4px;
        }
        .sb-dropdown-toggle {
            display: flex; align-items: center; gap: 14px;
            padding: 14px 16px; border-radius: 12px;
            font-size: 14px; font-weight: 600; color: #94a3b8;
            cursor: pointer; transition: all 0.2s ease;
            position: relative;
        }
        .sb-dropdown-toggle:hover,
        .sb-dropdown-toggle.active {
            background: var(--sidebar-hover);
            color: var(--sidebar-text-hi);
            text-shadow: 0 0 10px rgba(255,255,255,0.35);
        }
        .sb-dropdown-toggle i:first-child { 
            width: 20px; text-align: center; font-size: 16px; 
        }
        .sb-dropdown-toggle .fa-chevron-down {
            margin-left: auto;
            font-size: 10px;
            transition: transform 0.3s ease;
        }
        .sb-dropdown.open .sb-dropdown-toggle .fa-chevron-down {
            transform: rotate(180deg);
        }
        .sb-dropdown-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding-left: 34px;
        }
        .sb-dropdown.open .sb-dropdown-menu {
            max-height: 500px;
        }
        .sb-dropdown-menu a {
            padding: 10px 16px;
            font-size: 13px;
            margin-bottom: 2px;
        }
        .sb-dropdown-menu a.active {
            background: rgba(59,130,246,0.18);
            color: #eff6ff;
            font-weight: 700;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.12);
        }

        .sb-promo {
            margin: 16px 14px;
            background: var(--brand);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px; padding: 20px 18px; text-align: center;
            box-shadow: var(--shadow-lg);
        }
        .sb-promo .promo-icon {
            width: 56px; height: 56px; margin: 0 auto 12px;
            background: rgba(255,255,255,0.12); border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; color: var(--accent-soft);
        }
        .sb-promo p { font-size: 12px; color: #CBD5E1; line-height: 1.6; margin-bottom: 14px; }
        .sb-promo button {
            width: 100%; padding: 10px; border-radius: 10px;
            background: var(--accent); color: #fff; font-size: 13px; font-weight: 700;
            border: none; cursor: pointer; transition: all 0.2s;
            box-shadow: var(--shadow-accent);
        }
        .sb-promo button:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
        }

        .sb-logout {
            padding: 14px 12px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        .sb-logout a {
            display: flex; align-items: center; gap: 14px;
            padding: 14px 16px; border-radius: 12px;
            font-size: 14px; font-weight: 600; color: #94a3b8;
            text-decoration: none; transition: all 0.2s;
        }
        .sb-logout a:hover { 
            background: rgba(239,68,68,0.1); 
            color: #fca5a5;
        }
        .sb-logout a i { width: 20px; text-align: center; font-size: 16px; }

        /* ── Main ── */
        .main-wrapper { 
            margin-left: 280px; 
            min-height: 100vh; 
            width: calc(100% - 280px);
            display: flex; 
            flex-direction: column; 
            transition: all 0.3s ease;
        }
        .main-wrapper.expanded { 
            margin-left: 0;
            width: 100%;
        }

        /* ── Topbar ── */
        .topbar {
            position: sticky; top: 0; z-index: 40;
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
            display: flex; align-items: center; gap: 12px;
        }
        .topbar-hamburger { 
            font-size: 20px; color: #64748b; cursor: pointer; margin-right: 4px;
            padding: 8px 12px; border-radius: 8px; transition: all 0.2s;
        }
        .topbar-hamburger:hover {
            background: #f1f5f9;
            color: var(--brand);
        }
        .topbar-title h2 { font-size: 17px; font-weight: 700; color: #0f172a; }
        .topbar-title p  { font-size: 12px; color: #64748b; margin-top: 1px; }
        .topbar-spacer { flex: 1; }
        .topbar-search {
            display: flex; align-items: center; gap: 8px;
            background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 10px;
            padding: 7px 14px; width: 220px;
        }
        .topbar-search input { background: none; border: none; outline: none; font-size: 13px; color: #0f172a; width: 100%; }
        .topbar-search i { font-size: 13px; color: #94a3b8; }
        .topbar-bell {
            position: relative; width: 38px; height: 38px; border-radius: 10px;
            background: #f1f5f9; border: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: #64748b; font-size: 16px;
            transition: all 0.2s;
        }
        .topbar-bell:hover {
            background: #e2e8f0;
            color: var(--brand);
        }
        .topbar-bell .badge {
            position: absolute; top: -4px; right: -4px;
            width: 17px; height: 17px; border-radius: 50%;
            background: #ef4444; color: #fff; font-size: 9px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #fff;
        }
        
        /* Notification Dropdown */
        .notification-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            width: 360px;
            max-height: 480px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
            z-index: 1000;
            overflow: hidden;
        }
        .topbar-bell.open .notification-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .notification-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notification-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }
        .notification-header .mark-read {
            font-size: 12px;
            color: var(--brand);
            cursor: pointer;
            font-weight: 600;
        }
        .notification-header .mark-read:hover {
            color: var(--brand-hover);
        }
        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }
        .notification-item {
            padding: 14px 20px;
            border-bottom: 1px solid #f8fafc;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }
        .notification-item:hover {
            background: #f8fafc;
        }
        .notification-item.unread {
            background: #eff6ff;
        }
        .notification-item.unread:hover {
            background: #dbeafe;
        }
        /* ── Notification Icon Colors (Brand System) ── */
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
        }
        .notification-icon.blue { background: #eff6ff; color: #2563eb; }    /* Blue - Information */
        .notification-icon.green { background: #ecfdf5; color: #059669; }   /* Green - Success/Paid */
        .notification-icon.orange { background: #fffbeb; color: #d97706; }  /* Yellow - Pending */
        .notification-icon.red { background: #fef2f2; color: #dc2626; }     /* Red - Overdue */
        .notification-icon.gray { background: #f8fafc; color: #64748b; }    /* Gray - Inactive */
        .notification-content {
            flex: 1;
        }
        .notification-title {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .notification-text {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 4px;
        }
        .notification-time {
            font-size: 11px;
            color: #94a3b8;
        }
        .notification-delete {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: #64748b;
            cursor: pointer;
            transition: color 0.2s ease, background 0.2s ease;
        }
        .notification-delete:hover {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
        }
        .notification-footer {
            padding: 12px 20px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
        }
        .notification-footer a {
            font-size: 13px;
            color: var(--brand);
            font-weight: 600;
            text-decoration: none;
        }
        .notification-footer a:hover {
            color: var(--brand-hover);
        }
        .notification-empty {
            padding: 40px 20px;
            text-align: center;
            color: #94a3b8;
        }
        .notification-empty i {
            font-size: 48px;
            margin-bottom: 12px;
            opacity: 0.3;
        }
        .topbar-user {
            display: flex; align-items: center; gap: 10px;
            padding: 5px 12px 5px 6px;
            background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 50px;
            cursor: pointer; position: relative;
        }
        .topbar-user:hover {
            background: #e2e8f0;
        }
        
        /* User Dropdown Menu */
        .user-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            min-width: 220px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
            z-index: 1000;
        }
        .topbar-user.open .user-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .user-dropdown-header {
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .user-dropdown-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--brand);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            flex-shrink: 0;
            overflow: hidden;
        }
        .user-dropdown-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .user-dropdown-info {
            flex: 1;
        }
        .user-dropdown-header .name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .user-dropdown-header .email {
            font-size: 12px;
            color: #64748b;
        }
        .user-dropdown-menu {
            padding: 8px;
        }
        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #334155;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }
        .user-dropdown-item:hover {
            background: #f8fafc;
            color: var(--brand);
        }
        .user-dropdown-item i {
            width: 18px;
            text-align: center;
            font-size: 14px;
        }
        .user-dropdown-divider {
            height: 1px;
            background: #f1f5f9;
            margin: 8px 0;
        }
        .user-dropdown-item.logout {
            color: #ef4444;
        }
        .user-dropdown-item.logout:hover {
            background: #fef2f2;
            color: #dc2626;
        }
        .topbar-avatar {
            width: 30px; height: 30px; border-radius: 50%;
            background: var(--brand); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; overflow: hidden; flex-shrink: 0;
        }
        .topbar-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .topbar-uname { font-size: 12px; font-weight: 700; color: #0f172a; }
        .topbar-urole { font-size: 10px; color: #64748b; }

        /* ── Content ── */
        .content { padding: 24px; flex: 1; width: 100%; max-width: 100%; }

        /* ── Stat Cards ── */
        .stat-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
            gap: 14px; 
            margin-bottom: 20px; 
        }
        
        @media (max-width: 1400px) {
            .stat-grid { 
                grid-template-columns: repeat(3, 1fr); 
            }
        }
        
        @media (max-width: 1024px) {
            .stat-grid { 
                grid-template-columns: repeat(2, 1fr); 
            }
        }
        
        @media (max-width: 640px) {
            .stat-grid { 
                grid-template-columns: 1fr; 
            }
        }
        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius); padding: 18px 16px 14px;
            color: #0f172a; position: relative; overflow: hidden;
            min-height: 120px; display: flex; flex-direction: column; justify-content: space-between;
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .stat-card .sc-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; margin-bottom: 10px;
        }
        .stat-card .sc-label { font-size: 12px; font-weight: 600; color: #64748b; }
        .stat-card .sc-value { font-size: 28px; font-weight: 800; line-height: 1; margin-top: 4px; color: #0f172a; }
        .stat-card .sc-trend { font-size: 11px; color: #64748b; margin-top: 6px; font-weight: 500; }
        .stat-card .sc-wave {
            position: absolute; bottom: 0; left: 0; right: 0; height: 36px;
            pointer-events: none;
        }

        /* ── Brand Color System for Stat Cards ── */
        /* Blue - Information/General */
        .sc-blue .sc-icon { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
        .sc-blue .sc-wave polyline { stroke: #2563eb !important; stroke-opacity: 0.15; }

        /* Green - Success/Paid */
        .sc-green .sc-icon { background: rgba(5, 150, 105, 0.1); color: #059669; }
        .sc-green .sc-wave polyline { stroke: #059669 !important; stroke-opacity: 0.15; }

        /* Yellow/Orange - Pending/Warning */
        .sc-amber .sc-icon { background: rgba(217, 119, 6, 0.1); color: #d97706; }
        .sc-amber .sc-wave polyline { stroke: #d97706 !important; stroke-opacity: 0.15; }

        /* Red - Overdue/Error */
        .sc-red .sc-icon { background: rgba(220, 38, 38, 0.1); color: #dc2626; }
        .sc-red .sc-wave polyline { stroke: #dc2626 !important; stroke-opacity: 0.15; }

        /* Gray - Inactive/Disabled */
        .sc-gray .sc-icon { background: rgba(100, 116, 139, 0.1); color: #64748b; }
        .sc-gray .sc-wave polyline { stroke: #64748b !important; stroke-opacity: 0.15; }

        /* ── Cards ── */
        .card {
            background: var(--surface); border-radius: var(--radius);
            border: 1px solid var(--border); padding: 18px 20px;
            box-shadow: var(--shadow);
        }
        .card-title {
            font-size: 14px; font-weight: 700; color: var(--text);
            margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between;
        }
        .card-title span { color: var(--brand); }
        .year-select {
            font-size: 12px; font-weight: 500; color: #64748b;
            border: 1px solid #e2e8f0; border-radius: 8px;
            padding: 4px 10px; outline: none; cursor: pointer; background: #f8fafc;
        }

        /* ── Table ── */
        .tbl { width: 100%; border-collapse: collapse; }
        .tbl thead th {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.04em; color: #94a3b8;
            padding: 0 10px 10px; text-align: left;
            border-bottom: 1px solid #f1f5f9;
        }
        .tbl tbody tr { border-bottom: 1px solid #f8fafc; }
        .tbl tbody tr:last-child { border-bottom: none; }
        .tbl tbody tr:hover { background: #f8fafc; }
        .tbl tbody td { font-size: 12px; color: #334155; padding: 9px 10px; }
        .tbl tbody td.mono { font-family: monospace; font-size: 11px; }

        /* ── Status Pills (Brand Color System) ── */
        .pill {
            display: inline-block; padding: 3px 10px; border-radius: 999px;
            font-size: 10px; font-weight: 700; border: 1.5px solid;
        }
        /* Payment Status */
        .pill-ongoing { color: #d97706; border-color: #fde68a; background: #fffbeb; }  /* Yellow - Pending */
        .pill-paid    { color: #059669; border-color: #a7f3d0; background: #ecfdf5; }  /* Green - Success/Paid */
        .pill-overdue { color: #dc2626; border-color: #fecaca; background: #fef2f2; }  /* Red - Overdue */
        .pill-pending { color: #d97706; border-color: #fde68a; background: #fffbeb; }  /* Yellow - Pending */
        
        /* Payment Methods */
        .pill-qr      { color: #2563eb; border-color: #bfdbfe; background: #eff6ff; }  /* Blue - Info */
        .pill-aba     { color: #2563eb; border-color: #bfdbfe; background: #eff6ff; }  /* Blue - Info */
        .pill-cc      { color: #2563eb; border-color: #bfdbfe; background: #eff6ff; }  /* Blue - Info */
        .pill-wing    { color: #059669; border-color: #a7f3d0; background: #ecfdf5; }  /* Green - Success */
        .pill-other   { color: #64748b; border-color: #e2e8f0; background: #f8fafc; }  /* Gray - Inactive */

        /* ── Quick Shortcuts ── */
        .shortcut-btn {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 14px; border-radius: 12px;
            border: 1px solid #e2e8f0; margin-bottom: 8px;
            text-decoration: none; font-size: 12.5px; font-weight: 600;
            transition: all 0.15s; cursor: pointer; background: #fff;
        }
        .shortcut-btn:last-child { margin-bottom: 0; }
        .shortcut-btn:hover { border-color: var(--border-strong); background: var(--surface-muted); }
        /* ── Shortcut Button Icons (Brand Colors) ── */
        .shortcut-icon {
            width: 34px; height: 34px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; flex-shrink: 0;
        }
        .si-blue   { background: #eff6ff; color: #2563eb; }     /* Blue - Information */
        .si-green  { background: #ecfdf5; color: #059669; }     /* Green - Success */
        .si-amber  { background: #fffbeb; color: #d97706; }     /* Yellow - Pending */
        .si-red    { background: #fef2f2; color: #dc2626; }     /* Red - Overdue */
        .si-gray   { background: #f8fafc; color: #64748b; }     /* Gray - Inactive */

        /* ── Donut ── */
        .donut-wrap { display: flex; align-items: center; gap: 16px; margin-top: 10px; }
        .donut-legend { flex: 1; }
        .donut-row { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
        .donut-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .donut-label { font-size: 12px; color: #334155; flex: 1; }
        .donut-val { font-size: 12px; font-weight: 600; color: #0f172a; }

        /* ── System Info ── */
        .sysrow { display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .sysrow:last-child { border-bottom: none; }
        .sysrow .sk { font-size: 12px; color: #64748b; }
        .sysrow .sv { font-size: 12px; font-weight: 600; color: #0f172a; }

        /* ── Alert Messages (Brand Color System) ── */
        .alert { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 500; margin-bottom: 18px; }
        .alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; }  /* Green - Success */
        .alert-warning { background: #fffbeb; border: 1px solid #fde68a; color: #d97706; }  /* Yellow - Warning */
        .alert-error   { background: #fef2f2; border: 1px solid: #fecaca; color: #dc2626; }  /* Red - Error */
        .alert-info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb; }  /* Blue - Info */

        /* View All btn */
        .btn-viewall {
            font-size: 11px; font-weight: 700; color: #fff;
            background: var(--brand); border: none; padding: 5px 12px;
            border-radius: 8px; cursor: pointer; text-decoration: none;
        }
        .btn-viewall:hover { background: var(--brand-hover); }

        /* footer */
        .footer { text-align: center; padding: 16px; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; background: #fff; }

        /* ── Responsive Design ── */
        @media (max-width: 1400px) {
            .charts-row {
                grid-template-columns: 1fr 1fr !important;
            }
            .charts-row > div:last-child {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 1024px) {
            .charts-row,
            .tables-row {
                grid-template-columns: 1fr !important;
            }
            .topbar-search {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .content {
                padding: 16px;
            }
            .topbar {
                padding: 10px 16px;
            }
            .topbar-title h2 {
                font-size: 16px;
            }
            .topbar-title p {
                display: none;
            }
            .stat-card .sc-value {
                font-size: 22px;
            }
            .card {
                padding: 14px 16px;
            }
        }

        @media (max-width: 640px) {
            .topbar-user > div {
                display: none;
            }
            .topbar-bell {
                width: 36px;
                height: 36px;
            }
        }
    </style>
</head>
<body>

<div style="display:flex;width:100%;min-height:100vh;margin:0;padding:0;">

    {{-- ── SIDEBAR ── --}}
    <aside id="sidebar">
        <div class="sb-logo">
            <div class="sb-logo-icon"><i class="fas fa-desktop"></i></div>
            <div>
                <h1>COMPUTER SHOP</h1>
                <p>Installment System</p>
            </div>
        </div>

        <nav class="sb-nav">

            {{-- ── General ── --}}
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> {{ __('app.dashboard') }}
            </a>

            <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <i class="fas fa-user-friends"></i> {{ __('app.customers') }}
            </a>

            <a href="{{ route('installments.index') }}" class="{{ request()->routeIs('installments.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i> {{ __('app.installment_plans') }}
            </a>

            <a href="{{ route('invoices.index') }}" class="{{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> {{ __('app.invoices') }}
            </a>

            {{-- Payment Dropdown --}}
            <div class="sb-dropdown {{ request()->routeIs('payments.*') || request()->routeIs('late-payments.*') ? 'open' : '' }}">
                <div class="sb-dropdown-toggle {{ request()->routeIs('payments.*') || request()->routeIs('late-payments.*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                    <i class="fas fa-money-check-alt"></i>
                    <span>{{ __('app.payments') }}</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="sb-dropdown-menu">
                    <a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.index') ? 'active' : '' }}">
                        <i class="fas fa-list"></i> {{ __('app.all_payments') }}
                    </a>
                    <a href="{{ route('payments.create') }}" class="{{ request()->routeIs('payments.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i> {{ __('app.new_payment') }}
                    </a>
                    <a href="{{ route('late-payments.index') }}" class="{{ request()->routeIs('late-payments.*') ? 'active' : '' }}">
                        <i class="fas fa-exclamation-circle"></i> {{ __('app.late_payments') }}
                    </a>
                </div>
            </div>

            {{-- Product Management (visible to admin & staff) --}}
            <div class="sb-dropdown {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.suppliers.*') || request()->routeIs('admin.purchases.*') || request()->routeIs('admin.sales.*') || request()->routeIs('admin.stock-movements.*') ? 'open' : '' }}">
                <div class="sb-dropdown-toggle {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.suppliers.*') || request()->routeIs('admin.purchases.*') || request()->routeIs('admin.sales.*') || request()->routeIs('admin.stock-movements.*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                    <i class="fas fa-box-open"></i>
                    <span>{{ __('app.products') }}</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="sb-dropdown-menu">
                    <a href="{{ route('admin.products.index') }}" class="{{ (request()->routeIs('admin.products.index') || (request()->routeIs('admin.products.show') && request('from') !== 'stock') || (request()->routeIs('admin.products.edit') && request('from') !== 'stock') || (request()->routeIs('admin.products.create') && request('from') !== 'stock')) ? 'active' : '' }}">
                        <i class="fas fa-list"></i> {{ __('app.product_list') }}
                    </a>
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.products.stock') }}" class="{{ (request()->routeIs('admin.products.stock') || (request()->routeIs('admin.products.show') && request('from') === 'stock') || (request()->routeIs('admin.products.edit') && request('from') === 'stock') || (request()->routeIs('admin.products.create') && request('from') === 'stock')) ? 'active' : '' }}">
                        <i class="fas fa-boxes"></i> {{ __('app.manage_stock') }}
                    </a>
                    <a href="{{ route('admin.purchases.create') }}" class="{{ request()->routeIs('admin.purchases.create') ? 'active' : '' }}">
                        <i class="fas fa-truck-loading"></i> {{ __('app.stock_in') }}
                    </a>
                    <a href="{{ route('admin.sales.create') }}" class="{{ request()->routeIs('admin.sales.create') ? 'active' : '' }}">
                        <i class="fas fa-sign-out-alt"></i> {{ __('app.stock_out') }}
                    </a>
                    <a href="{{ route('admin.purchases.index') }}" class="{{ request()->routeIs('admin.purchases.index') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i> {{ __('app.purchase_history') }}
                    </a>
                    <a href="{{ route('admin.stock-movements.index') }}" class="{{ request()->routeIs('admin.stock-movements.*') ? 'active' : '' }}">
                        <i class="fas fa-exchange-alt"></i> {{ __('app.stock_movements') }}
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i> {{ __('app.categories') }}
                    </a>
                    <a href="{{ route('admin.suppliers.index') }}" class="{{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                        <i class="fas fa-truck"></i> {{ __('app.suppliers') }}
                    </a>
                    @endif
                </div>
            </div>

            {{-- ── Admin Only ── --}}
            @if(auth()->user()->role === 'admin')

            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i> {{ __('app.user_management') }}
            </a>

            {{-- Reports Dropdown --}}
            <div class="sb-dropdown {{ request()->routeIs('admin.reports.*') ? 'open' : '' }}">
                <div class="sb-dropdown-toggle {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                    <i class="fas fa-chart-line"></i>
                    <span>{{ __('app.reports') }}</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="sb-dropdown-menu">
                    <a href="{{ route('admin.reports.daily') }}" class="{{ request()->routeIs('admin.reports.daily') ? 'active' : '' }}">
                        <i class="fas fa-calendar-day"></i> {{ __('app.daily_report') }}
                    </a>
                    <a href="{{ route('admin.reports.monthly') }}" class="{{ request()->routeIs('admin.reports.monthly') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i> {{ __('app.monthly_report') }}
                    </a>
                    <a href="{{ route('admin.reports.customer') }}" class="{{ request()->routeIs('admin.reports.customer') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> {{ __('app.customer_report') }}
                    </a>
                    <a href="{{ route('admin.reports.income') }}" class="{{ request()->routeIs('admin.reports.income') ? 'active' : '' }}">
                        <i class="fas fa-dollar-sign"></i> {{ __('app.income_report') }}
                    </a>
                </div>
            </div>

            <a href="{{ route('telegram-logs.index') }}" class="{{ request()->routeIs('telegram-logs.*') ? 'active' : '' }}">
                <i class="fab fa-telegram-plane"></i> {{ __('app.telegram_center') }}
            </a>

            <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i> {{ __('app.settings') }}
            </a>

            <a href="{{ route('admin.backups.index') }}" class="{{ request()->routeIs('admin.backups.*') ? 'active' : '' }}">
                <i class="fas fa-database"></i> {{ __('app.backup_restore') }}
            </a>

            @endif

        </nav>

        <div class="sb-logout">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left text-gray-300 hover:text-white flex items-center gap-3 px-4 py-3 rounded" style="background: transparent; border: none; cursor: pointer;">
                    <i class="fas fa-sign-out-alt"></i> {{ __('app.logout') }}
                </button>
            </form>
        </div>
    </aside>

    {{-- ── MAIN WRAPPER ── --}}
    <div class="main-wrapper">

        {{-- Topbar --}}
        <header class="topbar">
            <div class="topbar-hamburger" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </div>
            <div class="topbar-title">
                <h2>Dashboard</h2>
                <p>{{ __('app.welcome_back') }}, {{ auth()->user()->name }}! 👋</p>
            </div>
            <div class="topbar-spacer"></div>

            {{-- Language Switcher --}}
            <div style="position:relative;" id="lang-switcher">
                <button onclick="toggleLangMenu()" style="
                    display:flex; align-items:center; gap:6px;
                    padding:6px 12px; border-radius:10px;
                    border:1px solid #e2e8f0; background:#f8fafc;
                    cursor:pointer; font-size:13px; font-weight:600;
                    color:#334155; transition:all 0.2s;
                " onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f8fafc'">
                    @if(app()->getLocale() === 'km')
                        <span style="font-size:16px;">🇰🇭</span>
                        <span>ខ្មែរ</span>
                    @else
                        <span style="font-size:16px;">🇺🇸</span>
                        <span>English</span>
                    @endif
                    <svg style="width:10px;height:10px;color:#94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="lang-menu" style="
                    display:none; position:absolute; top:calc(100% + 8px); right:0;
                    background:white; border:1px solid #e2e8f0; border-radius:12px;
                    box-shadow:0 8px 24px rgba(0,0,0,0.12); min-width:150px;
                    z-index:1000; overflow:hidden;
                ">
                    <a href="{{ route('lang.switch', 'en') }}" style="
                        display:flex; align-items:center; gap:10px;
                        padding:10px 16px; font-size:13px; font-weight:600;
                        color:{{ app()->getLocale() === 'en' ? '#2563eb' : '#334155' }};
                        background:{{ app()->getLocale() === 'en' ? '#eff6ff' : 'transparent' }};
                        text-decoration:none; transition:background 0.15s;
                    " onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='{{ app()->getLocale() === 'en' ? '#eff6ff' : 'transparent' }}'">
                        <span style="font-size:18px;">🇺🇸</span>
                        <span>English</span>
                        @if(app()->getLocale() === 'en')
                            <svg style="width:14px;height:14px;margin-left:auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        @endif
                    </a>
                    <div style="height:1px;background:#f1f5f9;margin:2px 0;"></div>
                    <a href="{{ route('lang.switch', 'km') }}" style="
                        display:flex; align-items:center; gap:10px;
                        padding:10px 16px; font-size:13px; font-weight:600;
                        color:{{ app()->getLocale() === 'km' ? '#2563eb' : '#334155' }};
                        background:{{ app()->getLocale() === 'km' ? '#eff6ff' : 'transparent' }};
                        text-decoration:none; transition:background 0.15s;
                    " onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='{{ app()->getLocale() === 'km' ? '#eff6ff' : 'transparent' }}'">
                        <span style="font-size:18px;">🇰🇭</span>
                        <span>ភាសាខ្មែរ</span>
                        @if(app()->getLocale() === 'km')
                            <svg style="width:14px;height:14px;margin-left:auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        @endif
                    </a>
                </div>
            </div>

            <div class="topbar-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="{{ __('app.search_here') }}">
            </div>
            <div class="topbar-bell" onclick="toggleNotificationDropdown(event)">
                <i class="fas fa-bell"></i>
                <span class="badge" id="notification-badge">0</span>
                
                {{-- Notification Dropdown --}}
                <div class="notification-dropdown">
                    <div class="notification-header">
                        <h3>{{ __('app.notifications') }}</h3>
                        <span class="mark-read" onclick="markAllAsRead(event)">{{ __('app.mark_all_read') }}</span>
                    </div>
                    <div class="notification-list" id="notification-list">
                        {{-- Notifications will be loaded here dynamically --}}
                        <div class="notification-empty">
                            <i class="fas fa-bell-slash"></i>
                            <p>{{ __('app.no_notifications') }}</p>
                        </div>
                    </div>
                    <div class="notification-footer">
                        <a href="{{ route('payments.index') }}">{{ __('app.view_all_notifications') }}</a>
                    </div>
                </div>
            </div>
            <div class="topbar-user" onclick="toggleUserDropdown(event)">
                <div class="topbar-avatar">
                    @if(auth()->user()->profile_image && \Illuminate\Support\Facades\Storage::disk('public')->exists(auth()->user()->profile_image))
                        <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="avatar" onerror="this.style.display='none'">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <div class="topbar-uname">{{ auth()->user()->name }}</div>
                    <div class="topbar-urole">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
                <i class="fas fa-chevron-down" style="font-size:10px;color:#94a3b8;margin-left:4px;"></i>
                
                {{-- User Dropdown Menu --}}
                <div class="user-dropdown">
                    <div class="user-dropdown-header">
                        <div class="user-dropdown-avatar">
                            @if(auth()->user()->profile_image && \Illuminate\Support\Facades\Storage::disk('public')->exists(auth()->user()->profile_image))
                                <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="avatar" onerror="this.style.display='none'">
                            @else
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="user-dropdown-info">
                            <div class="name">{{ auth()->user()->name }}</div>
                            <div class="email">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <div class="user-dropdown-menu">
                        <a href="{{ route('profile.edit') }}" class="user-dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>{{ __('app.my_profile') }}</span>
                        </a>
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.users.index') }}" class="user-dropdown-item">
                            <i class="fas fa-users-cog"></i>
                            <span>{{ __('app.manage_users') }}</span>
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="user-dropdown-item">
                            <i class="fas fa-cog"></i>
                            <span>{{ __('app.settings') }}</span>
                        </a>
                        <a href="{{ route('admin.backups.index') }}" class="user-dropdown-item">
                            <i class="fas fa-database"></i>
                            <span>{{ __('app.backup_restore') }}</span>
                        </a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="user-dropdown-item">
                            <i class="fas fa-th-large"></i>
                            <span>{{ __('app.dashboard') }}</span>
                        </a>
                        <div class="user-dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="user-dropdown-item logout" style="width:100%;border:none;background:none;text-align:left;">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>{{ __('app.logout') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="content">

            @yield('content')

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Language Menu Toggle
function toggleLangMenu() {
    const menu = document.getElementById('lang-menu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    const switcher = document.getElementById('lang-switcher');
    if (switcher && !switcher.contains(e.target)) {
        const menu = document.getElementById('lang-menu');
        if (menu) menu.style.display = 'none';
    }
});

// Toggle Sidebar Function
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainWrapper = document.querySelector('.main-wrapper');
    
    sidebar.classList.toggle('collapsed');
    mainWrapper.classList.toggle('expanded');
    
    // Save state to localStorage
    const isCollapsed = sidebar.classList.contains('collapsed');
    localStorage.setItem('sidebarCollapsed', isCollapsed);
}

// Toggle Dropdown Function
function toggleDropdown(element) {
    const dropdown = element.closest('.sb-dropdown');
    dropdown.classList.toggle('open');
}

// Toggle User Dropdown
function toggleUserDropdown(event) {
    event.stopPropagation();
    const userDropdown = event.currentTarget;
    const notificationBell = document.querySelector('.topbar-bell');
    
    // Close notification dropdown
    if (notificationBell) {
        notificationBell.classList.remove('open');
    }
    
    userDropdown.classList.toggle('open');
}

// Toggle Notification Dropdown
function toggleNotificationDropdown(event) {
    event.stopPropagation();
    const notificationBell = event.currentTarget;
    const userDropdown = document.querySelector('.topbar-user');
    
    // Close user dropdown
    if (userDropdown) {
        userDropdown.classList.remove('open');
    }
    
    notificationBell.classList.toggle('open');
    
    // Load notifications when opening
    if (notificationBell.classList.contains('open')) {
        loadNotifications();
    }
}

function escapeHtml(text) {
    return String(text || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// Load Notifications
function loadNotifications() {
    fetch('{{ route("notifications.index") }}')
        .then(response => response.json())
        .then(notifications => {
            const notificationList = document.getElementById('notification-list');
            
            if (notifications.length === 0) {
                notificationList.innerHTML = `
                    <div class="notification-empty">
                        <i class="fas fa-bell-slash"></i>
                        <p>No notifications</p>
                    </div>
                `;
                return;
            }
            
            notificationList.innerHTML = notifications.map(notif => {
                const link = escapeHtml(notif.link || '#');
                const title = escapeHtml(notif.title);
                const message = escapeHtml(notif.message);
                const icon = escapeHtml(notif.icon || 'bell');
                const iconColor = escapeHtml(notif.icon_color || 'blue');
                const unreadClass = !notif.is_read ? 'unread' : '';

                return `
                    <div class="notification-item ${unreadClass}">
                        <a href="${link}" class="notification-link" onclick="markNotificationAsRead(event, ${notif.id}, '${link}')">
                            <div class="notification-icon ${iconColor}">
                                <i class="fas fa-${icon}"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">${title}</div>
                                <div class="notification-text">${message}</div>
                                <div class="notification-time">${formatTime(notif.created_at)}</div>
                            </div>
                        </a>
                        <button type="button" class="notification-delete" onclick="deleteNotification(event, ${notif.id})" title="Delete notification">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            }).join('');
        });
}

// Load Unread Count
function loadUnreadCount() {
    fetch('{{ route("notifications.unread-count") }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notification-badge');
            badge.textContent = data.count;
            badge.style.display = data.count > 0 ? 'flex' : 'none';
        });
}

// Mark Notification as Read
function markNotificationAsRead(event, notificationId, link) {
    event.preventDefault();
    
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadUnreadCount();
            loadNotifications();
            
            // Redirect to link if exists
            if (link) {
                window.location.href = link;
            }
        }
    });
}

// Mark All as Read
function markAllAsRead(event) {
    event.stopPropagation();
    
    fetch('{{ route("notifications.mark-all-read") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadUnreadCount();
            loadNotifications();
        }
    });
}

function deleteNotification(event, notificationId) {
    event.stopPropagation();
    event.preventDefault();

    if (!confirm('Delete this notification?')) {
        return;
    }

    fetch(`/notifications/${notificationId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadUnreadCount();
            loadNotifications();
        }
    });
}

// Format Time
function formatTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000); // seconds
    
    if (diff < 60) return 'Just now';
    if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
    if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
    if (diff < 604800) return Math.floor(diff / 86400) + ' days ago';
    return date.toLocaleDateString();
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const userDropdown = document.querySelector('.topbar-user');
    const notificationBell = document.querySelector('.topbar-bell');
    
    // Close user dropdown if clicking outside
    if (userDropdown && !userDropdown.contains(event.target)) {
        userDropdown.classList.remove('open');
    }
    
    // Close notification dropdown if clicking outside
    if (notificationBell && !notificationBell.contains(event.target)) {
        notificationBell.classList.remove('open');
    }
});

// Prevent dropdown from closing when clicking inside
document.addEventListener('DOMContentLoaded', function() {
    const notificationDropdown = document.querySelector('.notification-dropdown');
    const userDropdownMenu = document.querySelector('.user-dropdown');
    
    if (notificationDropdown) {
        notificationDropdown.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
    
    if (userDropdownMenu) {
        userDropdownMenu.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
    
    // Load initial notification count
    loadUnreadCount();
    
    // Refresh notification count every 30 seconds
    setInterval(loadUnreadCount, 30000);
});

// Restore sidebar state on page load
document.addEventListener('DOMContentLoaded', function() {
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        document.getElementById('sidebar').classList.add('collapsed');
        document.querySelector('.main-wrapper').classList.add('expanded');
    }
    
    // Restore sidebar scroll position
    const sidebar = document.querySelector('.sb-nav');
    const savedScrollPos = sessionStorage.getItem('sidebarScrollPos');
    if (savedScrollPos && sidebar) {
        sidebar.scrollTop = parseInt(savedScrollPos);
    }
});

// Save sidebar scroll position before navigating
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sb-nav');
    if (sidebar) {
        // Save scroll position when clicking sidebar links
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                sessionStorage.setItem('sidebarScrollPos', sidebar.scrollTop);
            });
        });
        
        // Also save on dropdown toggle
        const dropdownToggles = document.querySelectorAll('.sb-dropdown-toggle');
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                sessionStorage.setItem('sidebarScrollPos', sidebar.scrollTop);
            });
        });
    }
});
</script>
</body>
</html>
