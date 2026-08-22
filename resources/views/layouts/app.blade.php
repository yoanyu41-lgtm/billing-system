<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CityTech — Installment System</title>
    
    <!-- Font Configuration -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Battambang:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Brand Tokens -->
    @include('partials.brand')
    <style>
        /* Dark Mode Styles */
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }
        
        .dark {
            --bg-primary: #1e293b;
            --bg-secondary: #0f172a;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --border-color: #334155;
        }
        
        .dark body {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }
        
        body {
            background: #ffffff !important;
        }
        
        .dark .sidebar {
            background: #0f172a;
            border-right: 1px solid #334155;
        }
        
        .dark .topbar {
            background: #1e293b;
            border-bottom: 1px solid #334155;
        }
        
        .dark .card {
            background: #1e293b;
            border-color: #334155;
            color: var(--text-primary);
        }
        
        .dark input, .dark select, .dark textarea {
            background: #0f172a;
            border-color: #334155;
            color: var(--text-primary);
        }
        
        .dark .sidebar a {
            color: #94a3b8;
        }
        
        .dark .sidebar a:hover,
        .dark .sidebar a.active {
            background: #334155;
            color: #ffffff;
        }
        
        /* Dark mode for search results dropdown */
        .dark #search-results {
            background: #1e293b;
            border-color: #334155;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
        }
        
        .dark #search-results h4 {
            color: #94a3b8;
        }
        
        .dark #search-results a {
            background: #0f172a !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        
        .dark #search-results a:hover {
            background: #334155 !important;
        }
        
        .dark #search-results a div {
            color: inherit;
        }
        
        .dark #search-results a div:last-child {
            color: #94a3b8 !important;
        }

        /* Custom Dropdown Scrollbar */
        .product-dropdown-menu::-webkit-scrollbar,
        .customer-dropdown-menu::-webkit-scrollbar {
            width: 6px;
        }
        .product-dropdown-menu::-webkit-scrollbar-thumb,
        .customer-dropdown-menu::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }
        .product-dropdown-menu::-webkit-scrollbar-thumb:hover,
        .customer-dropdown-menu::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }

        /* Font: Poppins for English, Battambang for Khmer */
        * { 
            font-family: 'Poppins', 'Battambang', 'Khmer OS Battambang', 'Khmer-System', sans-serif !important;
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
        }
        
        /* Exclude Font Awesome icons from generic font overrides */
        .fa, .fas, .far, .fal, .fad, .fab, .fa-solid, .fa-regular, [class*="fa-"] {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands", "Font Awesome 5 Free", "Font Awesome 5 Brands", "FontAwesome" !important;
            line-height: 1 !important;
            display: inline-block;
        }
        body { margin: 0; padding: 0; background: var(--bg); overflow-x: hidden; }
        html { margin: 0; padding: 0; }

        /* ── Sidebar ── */
        #sidebar {
            width: 280px; min-height: 100vh; background: var(--sidebar);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; bottom: 0; z-index: 50;
            box-shadow: 4px 0 24px rgba(15,23,42,0.16);
            transition: width 0.3s ease, transform 0.3s ease;
            overflow-x: hidden;
        }
        #sidebar.collapsed {
            width: 80px;
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
            background: #ffffff;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 24px; flex-shrink: 0;
            padding: 8px; box-sizing: border-box;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
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
            max-height: 1000px;
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
            margin-left: 80px;
            width: calc(100% - 80px);
        }

        /* ── Collapsed Sidebar styles for desktop ── */
        @media (min-width: 769px) {
            #sidebar.collapsed {
                width: 80px;
            }
            #sidebar.collapsed .sb-logo {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
                gap: 0;
            }
            #sidebar.collapsed .sb-logo > div:not(.sb-logo-icon) {
                display: none !important;
            }
            #sidebar.collapsed .sb-nav a,
            #sidebar.collapsed .sb-dropdown-toggle {
                font-size: 0 !important;
                justify-content: center;
                padding-left: 0 !important;
                padding-right: 0 !important;
                gap: 0 !important;
            }
            #sidebar.collapsed .sb-nav a i,
            #sidebar.collapsed .sb-dropdown-toggle i {
                font-size: 18px !important;
                margin-right: 0 !important;
                margin-left: 0 !important;
                width: 100% !important;
                text-align: center;
            }
            #sidebar.collapsed .sb-dropdown-toggle .fa-chevron-down,
            #sidebar.collapsed .sb-dropdown-toggle span {
                display: none !important;
            }
            #sidebar.collapsed .sb-dropdown-menu {
                padding-left: 0 !important;
            }
            #sidebar.collapsed .sb-dropdown-menu a {
                justify-content: center;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            #sidebar.collapsed .sb-promo {
                display: none !important;
            }
            #sidebar.collapsed .sb-logout {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            #sidebar.collapsed .sb-logout button {
                font-size: 0 !important;
                justify-content: center;
                padding-left: 0 !important;
                padding-right: 0 !important;
                gap: 0 !important;
            }
            #sidebar.collapsed .sb-logout button i {
                font-size: 18px !important;
                margin-right: 0 !important;
                margin-left: 0 !important;
                width: 100% !important;
                text-align: center;
            }
        }

        /* ── Mobile Responsive Sidebar (Floating Drawer) ── */
        @media (max-width: 768px) {
            #sidebar {
                width: 280px !important;
                transform: translateX(0);
                transition: transform 0.3s ease;
            }
            #sidebar.collapsed {
                width: 280px !important;
                transform: translateX(-280px) !important;
            }
            .main-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }
            .main-wrapper.expanded {
                margin-left: 0 !important;
                width: 100% !important;
            }
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
            gap: 16px; 
            margin-bottom: 24px; 
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

        /* ── Premium Stat Card Design ── */
        .stat-card {
            position: relative; overflow: hidden;
            min-height: 130px; display: flex; flex-direction: column; justify-content: space-between;
            border-radius: 16px;
            padding: 20px 22px 14px;
            background: #fff;
            border: 1px solid #f1f5f9;
            box-shadow: 0 2px 8px rgba(15,23,42,0.06), 0 1px 2px rgba(15,23,42,0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        /* Colored top-accent line */
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            border-radius: 16px 16px 0 0;
        }
        /* Faint decorative circle top-right */
        .stat-card::after {
            content: '';
            position: absolute;
            top: -24px; right: -24px;
            width: 90px; height: 90px;
            border-radius: 50%;
            opacity: 0.06;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(15,23,42,0.10), 0 2px 6px rgba(15,23,42,0.06);
        }
        .stat-card .sc-icon {
            width: 46px; height: 46px; border-radius: 13px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; margin-bottom: 14px;
            flex-shrink: 0;
        }
        .stat-card .sc-label { 
            font-size: 11.5px; font-weight: 600; 
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 2px;
        }
        .stat-card .sc-value { 
            font-size: 28px; font-weight: 800; line-height: 1.1; margin-top: 2px; 
            color: #0f172a;
            letter-spacing: -0.02em;
        }
        .stat-card .sc-trend { 
            font-size: 11px; margin-top: 8px; font-weight: 500;
            color: #94a3b8;
        }
        .stat-card .sc-wave {
            position: absolute; bottom: 0; left: 0; right: 0; height: 38px;
            pointer-events: none; opacity: 0.07;
        }

        /* ── Clean Color Accent System for Stat Cards ── */
        /* Blue */
        .sc-blue::before { background: linear-gradient(90deg, #3b82f6, #6366f1); }
        .sc-blue::after  { background: #3b82f6; }
        .sc-blue .sc-icon { background: #eff6ff; color: #2563eb; }
        .sc-blue .sc-value { color: #1e3a5f; }
        .sc-blue .sc-wave polyline { stroke: #3b82f6 !important; }

        /* Green */
        .sc-green::before { background: linear-gradient(90deg, #10b981, #06b6d4); }
        .sc-green::after  { background: #10b981; }
        .sc-green .sc-icon { background: #ecfdf5; color: #059669; }
        .sc-green .sc-value { color: #064e3b; }
        .sc-green .sc-wave polyline { stroke: #10b981 !important; }

        /* Amber */
        .sc-amber::before { background: linear-gradient(90deg, #f59e0b, #fb923c); }
        .sc-amber::after  { background: #f59e0b; }
        .sc-amber .sc-icon { background: #fffbeb; color: #d97706; }
        .sc-amber .sc-value { color: #451a03; }
        .sc-amber .sc-wave polyline { stroke: #f59e0b !important; }

        /* Red */
        .sc-red::before { background: linear-gradient(90deg, #ef4444, #f43f5e); }
        .sc-red::after  { background: #ef4444; }
        .sc-red .sc-icon { background: #fef2f2; color: #dc2626; }
        .sc-red .sc-value { color: #450a0a; }
        .sc-red .sc-wave polyline { stroke: #ef4444 !important; }

        /* Gray */
        .sc-gray::before { background: linear-gradient(90deg, #64748b, #94a3b8); }
        .sc-gray::after  { background: #64748b; }
        .sc-gray .sc-icon { background: #f8fafc; color: #64748b; }
        .sc-gray .sc-value { color: #1e293b; }
        .sc-gray .sc-wave polyline { stroke: #64748b !important; }

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
                font-size: 24px;
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

        /* Smooth Dropdown Animation */
        .animate-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            z-index: 1000;
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .animate-dropdown.open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* Print styling for clean browser printing */
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
            #sidebar, .topbar, header, nav, form, button, .no-print, .sb-nav, .sb-dropdown, .topbar-hamburger, .topbar-title, .topbar-right, .shortcut-btn, .btn, a.bg-emerald-600, a.bg-blue-600, .bg-blue-600, .bg-emerald-600 {
                display: none !important;
            }
            body, .main-wrapper, .content, main {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
                left: 0 !important;
                margin-left: 0 !important;
                background: #ffffff !important;
                color: #000000 !important;
                box-shadow: none !important;
            }
            #receipt {
                border: 2px solid #1d4ed8 !important;
                box-shadow: none !important;
                margin: 0 auto !important;
                padding: 24px !important;
                width: 100% !important;
                max-width: 100% !important;
                background: #ffffff !important;
                page-break-inside: avoid !important;
            }
            .grid { display: grid !important; }
            .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
            .md\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
        }
    </style>
</head>
<body>

<div style="display:flex;width:100%;min-height:100vh;margin:0;padding:0;">

    {{-- ── SIDEBAR ── --}}
    <aside id="sidebar">
        <div class="sb-logo">
            <div class="sb-logo-icon"><img src="{{ $companyLogo }}" alt="CT" style="width:34px;height:34px;object-fit:contain;"></div>
            <div>
                <h1>{{ $companyName ?? 'COMPUTER SHOP' }}</h1>
                <p>{{ $companySubtitle ?? 'Installment System' }}</p>
            </div>
        </div>

        <nav class="sb-nav">

            {{-- ── Dashboard ── --}}
            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('dashboard.view'))
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> {{ __('app.dashboard') }}
            </a>
            @endif

            {{-- Customers Dropdown --}}
            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('customers.view'))
            @php
                $routeCustomer = request()->route('customer');
                if ($routeCustomer instanceof \App\Models\Customer) {
                    $custType = $routeCustomer->type;
                } else {
                    $custType = request('type', 'installment');
                }
            @endphp
            <div class="sb-dropdown {{ request()->routeIs('customers.*') ? 'open' : '' }}">
                <div class="sb-dropdown-toggle {{ request()->routeIs('customers.*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                    <i class="fas fa-user-friends"></i>
                    <span>{{ __('app.customer_management') }}</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="sb-dropdown-menu">
                    <a href="{{ route('customers.index', ['type' => 'installment']) }}" class="{{ request()->routeIs('customers.*') && !request()->routeIs('customers.trash') && $custType !== 'direct' ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar"></i> {{ __('app.installment_customers') }}
                    </a>
                    <a href="{{ route('customers.index', ['type' => 'direct']) }}" class="{{ request()->routeIs('customers.*') && !request()->routeIs('customers.trash') && $custType === 'direct' ? 'active' : '' }}">
                        <i class="fas fa-cash-register"></i> {{ __('app.direct_customers') }}
                    </a>
                </div>
            </div>
            @endif

            {{-- Installments Dropdown --}}
            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('installments.view'))
            <div class="sb-dropdown {{ request()->routeIs('installments.*') ? 'open' : '' }}">
                <div class="sb-dropdown-toggle {{ request()->routeIs('installments.*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>{{ __('app.installment_plans') }}</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="sb-dropdown-menu">
                    <a href="{{ route('installments.index') }}" class="{{ request()->routeIs('installments.index') || request()->routeIs('installments.show') || request()->routeIs('installments.edit') ? 'active' : '' }}">
                        <i class="fas fa-list"></i> {{ __('app.all_plans') }}
                    </a>
                    <a href="{{ route('installments.schedule-index') }}" class="{{ request()->routeIs('installments.schedule-index') || request()->routeIs('installments.schedule') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i> {{ __('app.payment_schedule') }}
                    </a>
                    <a href="{{ route('installments.contract-index') }}" class="{{ request()->routeIs('installments.contract-index') || request()->routeIs('installments.contract') ? 'active' : '' }}">
                        <i class="fas fa-file-signature"></i> {{ __('app.contracts') ?? 'បោះពុម្ពកិច្ចសន្យា' }}
                    </a>
                    <a href="{{ route('installments.pay-off-index') }}" class="{{ request()->routeIs('installments.pay-off-index') ? 'active' : '' }}">
                        <i class="fas fa-hand-holding-usd"></i> {{ __('app.pay_off') }}
                    </a>
                    <a href="{{ route('installments.clearance-index') }}" class="{{ request()->routeIs('installments.clearance-index') || request()->routeIs('installments.clearance') ? 'active' : '' }}">
                        <i class="fas fa-certificate"></i> {{ __('app.clearance_certificates') }}
                    </a>
                </div>
            </div>
            @endif

            {{-- Direct Sale (ទិញដាច់) --}}
            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('sales.view') || auth()->user()->can('sales.create'))
            @php
                $isDirectSaleOpen = request()->routeIs('admin.sales.*') && request('from') !== 'invoice';
                $isDirectSaleIndexActive = (request()->routeIs('admin.sales.index') || request()->routeIs('admin.sales.show')) && request('from') !== 'invoice';
            @endphp
            <div class="sb-dropdown {{ $isDirectSaleOpen ? 'open' : '' }}">
                <div class="sb-dropdown-toggle {{ $isDirectSaleOpen ? 'active' : '' }}" onclick="toggleDropdown(this)">
                    <i class="fas fa-cash-register"></i>
                    <span>{{ __('app.direct_sale') }}</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="sb-dropdown-menu">
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('sales.create'))
                    <a href="{{ route('admin.sales.create') }}" class="{{ request()->routeIs('admin.sales.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i> {{ __('app.new_direct_sale') }}
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('sales.view'))
                    <a href="{{ route('admin.sales.index', ['from' => 'sale']) }}" class="{{ $isDirectSaleIndexActive ? 'active' : '' }}">
                        <i class="fas fa-list"></i> {{ __('app.sales_list') }}
                    </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- Invoices Dropdown --}}
            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('invoices.view'))
            @php
                $showInvoice = request()->route('invoice');
                if (is_numeric($showInvoice)) {
                    $showInvoice = \App\Models\Invoice::find($showInvoice);
                }
                $isPayoffShow = $showInvoice && $showInvoice->payment?->is_settlement;
                
                $isCompletedInvoiceShow = false;
                if ($showInvoice && $showInvoice->payment) {
                    $inst = $showInvoice->payment->installment;
                    if ($inst && $inst->status === 'completed' && !$showInvoice->payment->is_settlement) {
                        $latestApproved = $inst->payments()
                            ->where('status', 'approved')
                            ->orderBy('id', 'desc')
                            ->first();
                        if ($latestApproved && $latestApproved->id === $showInvoice->payment->id) {
                            $isCompletedInvoiceShow = true;
                        }
                    }
                }
                
                $isCompletedInvoice = (request()->routeIs('invoices.index') && request('type') === 'completed') || (request()->routeIs('invoices.show') && (request('type') === 'completed' || $isCompletedInvoiceShow));
                $isInvoiceOpen = request()->routeIs('invoices.*') || (request()->routeIs('admin.sales.*') && request('from') === 'invoice');
                
                $isAllInvoicesActive = request()->routeIs('invoices.index') && !request()->has('type');
                $isInstallmentInvoice = (request()->routeIs('invoices.index') && request('type') === 'installment') || (request()->routeIs('invoices.show') && request('type') !== 'payoff' && request('type') !== 'direct' && request('type') !== 'completed' && !$isPayoffShow && !$isCompletedInvoiceShow);
                $isPayoffInvoice = (request()->routeIs('invoices.index') && request('type') === 'payoff') || (request()->routeIs('invoices.show') && (request('type') === 'payoff' || $isPayoffShow));
                $isDirectSaleInvoice = (request()->routeIs('invoices.index') && request('type') === 'direct') || (request()->routeIs('invoices.show') && request('type') === 'direct');
            @endphp
            <div class="sb-dropdown {{ $isInvoiceOpen ? 'open' : '' }}">
                <div class="sb-dropdown-toggle {{ $isInvoiceOpen ? 'active' : '' }}" onclick="toggleDropdown(this)">
                    <i class="fas fa-file-alt"></i>
                    <span>{{ __('app.invoices') }}</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="sb-dropdown-menu">
                    <a href="{{ route('invoices.index') }}" class="{{ $isAllInvoicesActive ? 'active' : '' }}">
                        <i class="fas fa-list"></i> {{ __('app.all_invoices') }}
                    </a>
                    <a href="{{ route('invoices.index', ['type' => 'installment']) }}" class="{{ $isInstallmentInvoice ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i> {{ __('app.installment_invoices') }}
                    </a>
                    <a href="{{ route('invoices.index', ['type' => 'payoff']) }}" class="{{ $isPayoffInvoice ? 'active' : '' }}">
                        <i class="fas fa-file-signature"></i> {{ __('app.payoff_invoices') }}
                    </a>
                    <a href="{{ route('invoices.index', ['type' => 'direct']) }}" class="{{ $isDirectSaleInvoice ? 'active' : '' }}">
                        <i class="fas fa-cash-register"></i> {{ __('app.direct_sale_invoices') }}
                    </a>
                    <a href="{{ route('invoices.index', ['type' => 'completed']) }}" class="{{ $isCompletedInvoice ? 'active' : '' }}">
                        <i class="fas fa-check-circle"></i> {{ __('app.completed_invoices') }}
                    </a>
                </div>
            </div>
            @endif

            {{-- Payment Dropdown --}}
            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('payments.view'))
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
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('payments.create'))
                    <a href="{{ route('payments.create') }}" class="{{ request()->routeIs('payments.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i> {{ __('app.new_payment') }}
                    </a>
                    @endif
                    <a href="{{ route('late-payments.index') }}" class="{{ request()->routeIs('late-payments.*') ? 'active' : '' }}">
                        <i class="fas fa-exclamation-circle"></i> {{ __('app.late_payments') }}
                    </a>
                </div>
            </div>
            @endif

            {{-- Product Management --}}
            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('products.view'))
            <div class="sb-dropdown {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.suppliers.*') || request()->routeIs('admin.purchases.*') || request()->routeIs('admin.stock-movements.*') ? 'open' : '' }}">
                <div class="sb-dropdown-toggle {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.suppliers.*') || request()->routeIs('admin.purchases.*') || request()->routeIs('admin.stock-movements.*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                    <i class="fas fa-box-open"></i>
                    <span>{{ __('app.products') }}</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="sb-dropdown-menu">
                    <a href="{{ route('admin.products.index') }}" class="{{ (request()->routeIs('admin.products.index') || (request()->routeIs('admin.products.show') && request('from') !== 'stock') || (request()->routeIs('admin.products.edit') && request('from') !== 'stock') || (request()->routeIs('admin.products.create') && request('from') !== 'stock')) ? 'active' : '' }}">
                        <i class="fas fa-list"></i> {{ __('app.product_list') }}
                    </a>
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('stock.manage'))
                    <a href="{{ route('admin.products.stock') }}" class="{{ (request()->routeIs('admin.products.stock') || (request()->routeIs('admin.products.show') && request('from') === 'stock') || (request()->routeIs('admin.products.edit') && request('from') === 'stock') || (request()->routeIs('admin.products.create') && request('from') === 'stock')) ? 'active' : '' }}">
                        <i class="fas fa-boxes"></i> {{ __('app.manage_stock') }}
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('purchases.create'))
                    <a href="{{ route('admin.purchases.create') }}" class="{{ request()->routeIs('admin.purchases.create') ? 'active' : '' }}">
                        <i class="fas fa-truck-loading"></i> {{ __('app.stock_in') }}
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('purchases.view'))
                    <a href="{{ route('admin.purchases.index') }}" class="{{ request()->routeIs('admin.purchases.index') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i> {{ __('app.purchase_history') }}
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('stock.manage'))
                    <a href="{{ route('admin.stock-movements.index') }}" class="{{ request()->routeIs('admin.stock-movements.*') ? 'active' : '' }}">
                        <i class="fas fa-exchange-alt"></i> {{ __('app.stock_movements') }}
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('suppliers.manage'))
                    <a href="{{ route('admin.suppliers.index') }}" class="{{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                        <i class="fas fa-truck"></i> {{ __('app.suppliers') }}
                    </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- Reports Dropdown --}}
            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('reports.view'))
            <div class="sb-dropdown {{ request()->routeIs('admin.reports.*') ? 'open' : '' }}">
                <div class="sb-dropdown-toggle {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                    <i class="fas fa-chart-pie"></i>
                    <span>{{ __('app.reports') }}</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="sb-dropdown-menu">
                    <a href="{{ route('admin.reports.sales') }}" class="{{ request()->routeIs('admin.reports.sales') || request()->routeIs('admin.reports.daily') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i> {{ app()->getLocale() === 'km' ? 'របាយការណ៍ការលក់' : 'Sales Report' }}
                    </a>
                    <a href="{{ route('admin.reports.payment') }}" class="{{ request()->routeIs('admin.reports.payment') ? 'active' : '' }}">
                        <i class="fas fa-wallet"></i> {{ app()->getLocale() === 'km' ? 'របាយការណ៍ការទូទាត់' : 'Payment Report' }}
                    </a>
                    <a href="{{ route('admin.reports.installment') }}" class="{{ request()->routeIs('admin.reports.installment') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar"></i> {{ app()->getLocale() === 'km' ? 'របាយការណ៍បង់រំលស់' : 'Installment Report' }}
                    </a>
                    <a href="{{ route('admin.reports.customer') }}" class="{{ request()->routeIs('admin.reports.customer') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> {{ app()->getLocale() === 'km' ? 'របាយការណ៍អតិថិជន' : 'Customer Report' }}
                    </a>
                    <a href="{{ route('admin.reports.product') }}" class="{{ request()->routeIs('admin.reports.product') ? 'active' : '' }}">
                        <i class="fas fa-boxes-stacked"></i> {{ app()->getLocale() === 'km' ? 'របាយការណ៍ផលិតផល' : 'Product Report' }}
                    </a>
                    <a href="{{ route('admin.reports.expense') }}" class="{{ request()->routeIs('admin.reports.expense') ? 'active' : '' }}">
                        <i class="fas fa-hand-holding-dollar"></i> {{ app()->getLocale() === 'km' ? 'របាយការណ៍ចំណាយ' : 'Expense Report' }}
                    </a>
                    <a href="{{ route('admin.reports.profit') }}" class="{{ request()->routeIs('admin.reports.profit') || request()->routeIs('admin.reports.income') || request()->routeIs('admin.reports.monthly') || request()->routeIs('admin.reports.yearly') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i> {{ app()->getLocale() === 'km' ? 'របាយការណ៍ប្រាក់ចំណេញ' : 'Profit Report' }}
                    </a>
                </div>
            </div>
            @endif

            {{-- Telegram Management --}}
            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('settings.manage'))
            <a href="{{ route('telegram-logs.index') }}" class="{{ request()->routeIs('telegram-logs.*') || request()->routeIs('admin.broadcast.*') ? 'active' : '' }}">
                <i class="fab fa-telegram-plane"></i>
                <span>{{ app()->getLocale() === 'km' ? 'គ្រប់គ្រង Telegram' : 'Telegram Management' }}</span>
            </a>
            @endif

            {{-- Settings Dropdown --}}
            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('settings.manage') || auth()->user()->can('users.view') || auth()->user()->can('roles.manage'))
            <div class="sb-dropdown {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.contract-terms.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.backups.*') || request()->routeIs('customers.trash') ? 'open' : '' }}">
                <div class="sb-dropdown-toggle {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.contract-terms.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.backups.*') || request()->routeIs('customers.trash') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                    <i class="fas fa-cog"></i>
                    <span>{{ __('app.settings') }}</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="sb-dropdown-menu">
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('settings.manage'))
                    <a href="{{ route('admin.contract-terms.index') }}" class="{{ request()->routeIs('admin.contract-terms.*') ? 'active' : '' }}">
                        <i class="fas fa-file-signature"></i> {{ __('app.contract_terms') }}
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('users.view'))
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog"></i> {{ __('app.user_management') }}
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('roles.manage'))
                    <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                        <i class="fas fa-user-shield"></i> {{ app()->getLocale() === 'km' ? 'កំណត់សិទ្ធិ (Roles & Permissions)' : 'Roles & Permissions' }}
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('backup.manage'))
                    <a href="{{ route('admin.backups.index') }}" class="{{ request()->routeIs('admin.backups.*') ? 'active' : '' }}">
                        <i class="fas fa-database"></i> {{ __('app.backup_restore') }}
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('settings.manage'))
                    <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="fas fa-sliders-h"></i> {{ __('app.general_settings') }}
                    </a>
                    <a href="{{ route('customers.trash') }}" class="{{ request()->routeIs('customers.trash') ? 'active' : '' }}">
                        <i class="fas fa-trash-alt"></i> {{ __('app.trash') }}
                    </a>
                    @endif
                </div>
            </div>
            @endif

        </nav>

        <div class="sb-logout">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="button" onclick="confirmLogout(this, event)" class="w-full text-left text-gray-300 hover:text-white flex items-center gap-3 px-4 py-3 rounded" style="background: transparent; border: none; cursor: pointer;">
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
                <h2>
                    @if(request()->routeIs('dashboard'))
                        {{ __('app.dashboard') }}
                    @elseif(request()->routeIs('customers.*'))
                        {{ __('app.customers') }}
                    @elseif(request()->routeIs('installments.contract-index') || request()->routeIs('contracts'))
                        {{ __('app.contracts') }}
                    @elseif(request()->routeIs('installments.pay-off-index') || request()->routeIs('pay-offs'))
                        {{ __('app.pay_off') }}
                    @elseif(request()->routeIs('installments.schedule-index') || request()->routeIs('payment-schedules'))
                        {{ __('app.payment_schedule') }}
                    @elseif(request()->routeIs('installments.*'))
                        {{ __('app.installment_plans') }}
                    @elseif(request()->routeIs('invoices.index'))
                        @if(request('type') === 'installment')
                            {{ __('app.installment_invoices') }}
                        @elseif(request('type') === 'completed')
                            {{ __('app.completed_invoices') }}
                        @elseif(request('type') === 'payoff')
                            {{ __('app.payoff_invoices') }}
                        @elseif(request('type') === 'direct')
                            {{ __('app.direct_sale_invoices') }}
                        @else
                            {{ __('app.invoices') }}
                        @endif
                    @elseif(request()->routeIs('invoices.show'))
                        @php
                            $showInvoice = request()->route('invoice');
                            if (is_numeric($showInvoice)) {
                                $showInvoice = \App\Models\Invoice::find($showInvoice);
                            }
                            $isPayoffShow = $showInvoice && $showInvoice->payment?->is_settlement;
                            $isCompletedInvoiceShow = false;
                            if ($showInvoice && $showInvoice->payment) {
                                $inst = $showInvoice->payment->installment;
                                if ($inst && $inst->status === 'completed' && !$showInvoice->payment->is_settlement) {
                                    $latestApproved = $inst->payments()
                                        ->where('status', 'approved')
                                        ->orderBy('id', 'desc')
                                        ->first();
                                    if ($latestApproved && $latestApproved->id === $showInvoice->payment->id) {
                                        $isCompletedInvoiceShow = true;
                                    }
                                }
                            }
                        @endphp
                        @if(request('type') === 'direct')
                            {{ __('app.direct_sale_invoices') }}
                        @elseif($isPayoffShow)
                            {{ __('app.payoff_invoices') }}
                        @elseif(request('type') === 'completed' || $isCompletedInvoiceShow)
                            {{ __('app.completed_invoices') }}
                        @else
                            {{ __('app.installment_invoices') }}
                        @endif
                    @elseif(request()->routeIs('invoices.*'))
                        {{ __('app.invoices') }}
                    @elseif(request()->routeIs('payments.*'))
                        {{ __('app.payments') }}
                    @elseif(request()->routeIs('admin.sales.*') || request()->routeIs('sales.*'))
                        {{ __('app.direct_sale') }}
                    @elseif(request()->routeIs('admin.purchases.*') || request()->routeIs('purchases.*'))
                        {{ __('app.stock_in') }}
                    @elseif(request()->routeIs('admin.stock-movements.*') || request()->routeIs('stock-movements.*'))
                        {{ __('app.stock_movements') }}
                    @elseif(request()->routeIs('admin.products.*') || request()->routeIs('products.*'))
                        {{ __('app.products') }}
                    @elseif(request()->routeIs('admin.categories.*') || request()->routeIs('categories.*'))
                        {{ __('app.categories') }}
                    @elseif(request()->routeIs('admin.suppliers.*') || request()->routeIs('suppliers.*'))
                        {{ __('app.suppliers') }}
                    @elseif(request()->routeIs('admin.users.*') || request()->routeIs('users.*'))
                        {{ __('app.user_management') }}
                    @elseif(request()->routeIs('admin.reports.*') || request()->routeIs('reports.*') || request()->routeIs('admin.reports.monthly') || request()->routeIs('admin.reports.daily'))
                        {{ __('app.reports') }}
                    @elseif(request()->routeIs('admin.settings.*') || request()->routeIs('settings.*') || request()->routeIs('admin.payment-methods.*'))
                        {{ __('app.settings') }}
                    @elseif(request()->routeIs('late-payments.*') || request()->routeIs('late-payments.index'))
                        {{ __('app.late_payments') }}
                    @elseif(request()->routeIs('notifications.*') || request()->routeIs('notifications.index'))
                        {{ __('app.notifications') }}
                    @else
                        {{ __('app.dashboard') }}
                    @endif
                </h2>
                <p>{{ __('app.welcome_back') }}, {{ auth()->user()->name }}! 👋</p>
            </div>
            <div class="topbar-spacer"></div>

            {{-- Currency Switcher & Exchange Rate --}}
            <div class="flex items-center gap-3 mr-2" style="font-family: inherit;">
                <!-- Currency Toggle Buttons -->
                <div class="flex items-center bg-gray-100 rounded-lg p-1 text-xs border border-gray-200">
                    <span class="text-gray-500 font-semibold px-2 hidden md:inline">{{ __('app.currency') }}:</span>
                    <a href="{{ route('currency.switch', 'USD') }}" class="px-2.5 py-1 rounded-md font-bold transition {{ session('display_currency', 'USD') === 'USD' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:text-blue-600' }}" style="text-decoration: none;">
                        USD $
                    </a>
                    <span class="text-gray-400 px-0.5"><i class="fas fa-arrows-alt-h"></i></span>
                    <a href="{{ route('currency.switch', 'KHR') }}" class="px-2.5 py-1 rounded-md font-bold transition {{ session('display_currency', 'USD') === 'KHR' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 hover:text-emerald-600' }}" style="text-decoration: none;">
                        KHR ៛
                    </a>
                </div>

                <!-- Exchange Rate Display -->
                @php
                    $rateValue = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
                @endphp
                <div class="hidden lg:flex items-center gap-1.5 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-700">
                    <span class="font-medium text-gray-500">{{ __('app.exchange_rate') }}:</span>
                    <span class="font-bold text-gray-800">1 USD = {{ number_format($rateValue) }} KHR</span>
                    <a href="{{ route('admin.settings.index') }}" class="text-gray-400 hover:text-blue-600 transition" title="Change Exchange Rate">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </div>

            {{-- Theme Switcher --}}
            <div style="position:relative; display:none !important;" id="theme-switcher">
                <button onclick="toggleThemeMenu()" style="
                    display:flex; align-items:center; justify-content:center;
                    width:36px; height:36px; border-radius:8px;
                    border:1px solid #e2e8f0; background:#f8fafc;
                    cursor:pointer; transition:all 0.2s;
                    color:#334155;
                " onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f8fafc'">
                    <span id="theme-icon" style="font-size:16px;">☀️</span>
                </button>

                <div id="theme-menu" class="animate-dropdown" style="min-width: 140px;">
                    <button onclick="setTheme('light')" class="theme-option" data-theme="light" style="
                        display:flex; align-items:center; gap:10px; width:100%;
                        padding:10px 14px; font-size:13px; font-weight:600;
                        color:#334155; background:transparent;
                        border:none; cursor:pointer; transition:background 0.15s;
                        text-align:left;
                    " onmouseover="this.style.background='#f8fafc'" onmouseout="this.classList.contains('active') ? this.style.background='#eff6ff' : this.style.background='transparent'">
                        <span style="font-size:16px;">☀️</span>
                        <span>ពន្លឺ</span>
                        <svg class="theme-check" style="width:14px;height:14px;margin-left:auto;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                    <div style="height:1px;background:#f1f5f9;margin:2px 0;"></div>
                    <button onclick="setTheme('dark')" class="theme-option" data-theme="dark" style="
                        display:flex; align-items:center; gap:10px; width:100%;
                        padding:10px 14px; font-size:13px; font-weight:600;
                        color:#334155; background:transparent;
                        border:none; cursor:pointer; transition:background 0.15s;
                        text-align:left;
                    " onmouseover="this.style.background='#f8fafc'" onmouseout="this.classList.contains('active') ? this.style.background='#eff6ff' : this.style.background='transparent'">
                        <span style="font-size:16px;">🌙</span>
                        <span>ខ្មៅ</span>
                        <svg class="theme-check" style="width:14px;height:14px;margin-left:auto;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                    <div style="height:1px;background:#f1f5f9;margin:2px 0;"></div>
                    <button onclick="setTheme('auto')" class="theme-option" data-theme="auto" style="
                        display:flex; align-items:center; gap:10px; width:100%;
                        padding:10px 14px; font-size:13px; font-weight:600;
                        color:#334155; background:transparent;
                        border:none; cursor:pointer; transition:background 0.15s;
                        text-align:left;
                    " onmouseover="this.style.background='#f8fafc'" onmouseout="this.classList.contains('active') ? this.style.background='#eff6ff' : this.style.background='transparent'">
                        <span style="font-size:16px;">💻</span>
                        <span>ស្វ័យប្រវត្តិ</span>
                        <svg class="theme-check" style="width:14px;height:14px;margin-left:auto;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Language Switcher --}}
            <div style="position:relative;" id="lang-switcher">
                <button onclick="toggleLangMenu()" style="
                    display:flex; align-items:center; gap:6px;
                    padding:6px 10px; border-radius:8px;
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

                <div id="lang-menu" class="animate-dropdown" style="min-width: 150px;">
                    <a href="{{ route('lang.switch', 'en') }}" style="
                        display:flex; align-items:center; gap:10px;
                        padding:10px 14px; font-size:13px; font-weight:600;
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
                        padding:10px 14px; font-size:13px; font-weight:600;
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

            <div class="topbar-search" style="display:none !important;">
                <i class="fas fa-search"></i>
                <input type="text" id="global-search" placeholder="{{ __('app.search_here') }}" onkeyup="handleGlobalSearch(event)">
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
                    <div class="topbar-urole">{{ auth()->user()->roles->first()?->name ?? ucfirst(auth()->user()->role) }}</div>
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
                            <button type="button" onclick="confirmLogout(this, event)" class="user-dropdown-item logout" style="width:100%;border:none;background:none;text-align:left;">
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
    menu.classList.toggle('open');
    document.getElementById('theme-menu').classList.remove('open');
    document.querySelector('.topbar-user').classList.remove('open');
    document.querySelector('.topbar-bell').classList.remove('open');
}

// Theme Menu Toggle
function toggleThemeMenu() {
    const menu = document.getElementById('theme-menu');
    menu.classList.toggle('open');
    document.getElementById('lang-menu').classList.remove('open');
    document.querySelector('.topbar-user').classList.remove('open');
    document.querySelector('.topbar-bell').classList.remove('open');
}

// Theme Management
function setTheme(theme) {
    localStorage.setItem('theme', theme);
    applyTheme(theme);
    updateThemeUI(theme);
    document.getElementById('theme-menu').classList.remove('open');
}

function applyTheme(theme) {
    const root = document.documentElement;
    
    // Force light mode only - always remove dark class
    // បង្ខំប្រើតែពណ៌សរ - យក dark class ចេញជានិច្ច
    root.classList.remove('dark');
    document.getElementById('theme-icon').textContent = '☀️';
}

function updateThemeUI(selectedTheme) {
    document.querySelectorAll('.theme-option').forEach(option => {
        const optionTheme = option.getAttribute('data-theme');
        const check = option.querySelector('.theme-check');
        
        if (optionTheme === selectedTheme) {
            option.classList.add('active');
            option.style.background = '#eff6ff';
            option.style.color = '#2563eb';
            check.style.display = 'block';
        } else {
            option.classList.remove('active');
            option.style.background = 'transparent';
            option.style.color = '#334155';
            check.style.display = 'none';
        }
    });
}

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', function() {
    // Force light mode only - បង្ខំប្រើតែពណ៌សរ
    localStorage.setItem('theme', 'light');
    const savedTheme = 'light';
    applyTheme(savedTheme);
    updateThemeUI(savedTheme);
    
    // Listen for system theme changes when in auto mode
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        const currentTheme = localStorage.getItem('theme');
        if (currentTheme === 'auto') {
            applyTheme('auto');
        }
    });
    
    // Global search keyboard shortcut (Ctrl+K)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            document.getElementById('global-search').focus();
        }
        // ESC to clear search
        if (e.key === 'Escape') {
            const searchInput = document.getElementById('global-search');
            if (searchInput === document.activeElement) {
                searchInput.value = '';
                searchInput.blur();
                hideSearchResults();
            }
        }
    });
});

// Global Search Function
let searchTimeout;
function handleGlobalSearch(event) {
    clearTimeout(searchTimeout);
    const query = event.target.value.trim();
    
    if (query.length < 2) {
        hideSearchResults();
        return;
    }
    
    searchTimeout = setTimeout(() => {
        performSearch(query);
    }, 300); // Debounce 300ms
}

function performSearch(query) {
    const loadingText = '{{ __("app.loading") }}';
    const noResultsText = '{{ __("app.no_data") }}';
    
    // Show loading state
    showSearchResults(`<p style="color:#64748b;font-size:13px;text-align:center;padding:20px;">${loadingText}</p>`);
    
    // Make AJAX request to search endpoint
    fetch(`/api/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data);
        })
        .catch(error => {
            console.error('Search error:', error);
            showSearchResults(`<p style="color:#64748b;font-size:13px;text-align:center;padding:20px;">${noResultsText}</p>`);
        });
}

function displaySearchResults(data) {
    const translations = {
        noResults: '{{ __("app.no_data") }}',
        customers: '{{ __("app.customers") }}',
        products: '{{ __("app.products") }}'
    };
    
    let html = '<div style="padding:12px;max-height:400px;overflow-y:auto;">';
    
    if (!data || (!data.customers && !data.products)) {
        html += `<p style="color:#64748b;font-size:13px;text-align:center;padding:20px;">${translations.noResults}</p>`;
    } else {
        // Customers
        if (data.customers && data.customers.length > 0) {
            html += `<div style="margin-bottom:16px;"><h4 style="font-size:12px;font-weight:700;color:#64748b;margin-bottom:8px;text-transform:uppercase;">${translations.customers}</h4>`;
            data.customers.forEach(customer => {
                html += `<a href="/customers/${customer.id}" style="display:block;padding:8px 12px;border-radius:8px;text-decoration:none;color:#334155;margin-bottom:4px;background:#f8fafc;border:1px solid #e2e8f0;" onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='#f8fafc'">
                    <div style="font-weight:600;font-size:13px;">${customer.name}</div>
                    <div style="font-size:11px;color:#64748b;">${customer.phone || ''}</div>
                </a>`;
            });
            html += '</div>';
        }
        
        // Products
        if (data.products && data.products.length > 0) {
            html += `<div style="margin-bottom:16px;"><h4 style="font-size:12px;font-weight:700;color:#64748b;margin-bottom:8px;text-transform:uppercase;">${translations.products}</h4>`;
            data.products.forEach(product => {
                html += `<a href="/admin/products/${product.id}" style="display:block;padding:8px 12px;border-radius:8px;text-decoration:none;color:#334155;margin-bottom:4px;background:#f8fafc;border:1px solid #e2e8f0;" onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='#f8fafc'">
                    <div style="font-weight:600;font-size:13px;">${product.name}</div>
                    <div style="font-size:11px;color:#64748b;">Stock: ${product.stock || 0}</div>
                </a>`;
            });
            html += '</div>';
        }
    }
    
    html += '</div>';
    showSearchResults(html);
}

function showSearchResults(content) {
    let resultsDiv = document.getElementById('search-results');
    
    if (!resultsDiv) {
        resultsDiv = document.createElement('div');
        resultsDiv.id = 'search-results';
        resultsDiv.style.cssText = `
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            z-index: 1000;
            min-width: 300px;
        `;
        document.querySelector('.topbar-search').style.position = 'relative';
        document.querySelector('.topbar-search').appendChild(resultsDiv);
    }
    
    resultsDiv.innerHTML = content;
    resultsDiv.style.display = 'block';
}

function hideSearchResults() {
    const resultsDiv = document.getElementById('search-results');
    if (resultsDiv) {
        resultsDiv.style.display = 'none';
    }
}

document.addEventListener('click', function(e) {
    const langSwitcher = document.getElementById('lang-switcher');
    const themeSwitcher = document.getElementById('theme-switcher');
    const searchBox = document.querySelector('.topbar-search');
    
    if (langSwitcher && !langSwitcher.contains(e.target)) {
        const langMenu = document.getElementById('lang-menu');
        if (langMenu) langMenu.classList.remove('open');
    }
    
    if (themeSwitcher && !themeSwitcher.contains(e.target)) {
        const themeMenu = document.getElementById('theme-menu');
        if (themeMenu) themeMenu.classList.remove('open');
    }
    
    // Close search results when clicking outside
    if (searchBox && !searchBox.contains(e.target)) {
        hideSearchResults();
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
    
    // Close other dropdowns
    if (notificationBell) notificationBell.classList.remove('open');
    document.getElementById('lang-menu').classList.remove('open');
    document.getElementById('theme-menu').classList.remove('open');
    
    userDropdown.classList.toggle('open');
}

// Toggle Notification Dropdown
function toggleNotificationDropdown(event) {
    event.stopPropagation();
    const notificationBell = event.currentTarget;
    const userDropdown = document.querySelector('.topbar-user');
    
    // Close other dropdowns
    if (userDropdown) userDropdown.classList.remove('open');
    document.getElementById('lang-menu').classList.remove('open');
    document.getElementById('theme-menu').classList.remove('open');
    
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

<!-- Beautiful Custom Logout Confirmation Modal -->
<div id="logout-confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center" role="dialog" aria-modal="true">
    <!-- Backdrop overlay -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 ease-out opacity-0" id="logout-modal-backdrop" onclick="closeLogoutModal()"></div>
    
    <!-- Modal content wrapper -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none">
        <div class="relative w-full max-w-sm transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 p-6 text-left align-middle shadow-2xl transition-all duration-300 ease-out scale-95 opacity-0 border border-slate-100 dark:border-slate-700 pointer-events-auto" id="logout-modal-content">
            
            <!-- Close button (x) -->
            <button type="button" onclick="closeLogoutModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors duration-150 cursor-pointer">
                <i class="fas fa-times text-lg"></i>
            </button>
            
            <!-- Warning / Info Icon (gorgeous custom style) -->
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-50 dark:bg-rose-950/30 text-rose-500 mb-4 animate-pulse">
                <i class="fas fa-sign-out-alt text-2xl"></i>
            </div>
            
            <!-- Message text -->
            <div class="text-center">
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 leading-6" id="logout-modal-title">
                    {{ __('app.logout') }}
                </h3>
                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                    {{ __('app.logout_confirm') }}
                </p>
            </div>
            
            <!-- Buttons -->
            <div class="mt-6 flex flex-row justify-center gap-3">
                <button type="button" onclick="closeLogoutModal()" class="w-1/2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors duration-200 cursor-pointer text-center">
                    {{ __('app.cancel') }}
                </button>
                <button type="button" onclick="executeLogout()" class="w-1/2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-sm font-semibold text-white shadow-lg shadow-rose-500/20 dark:shadow-none hover:shadow-rose-600/30 transition-all duration-200 cursor-pointer text-center">
                    {{ __('app.logout') }}
                </button>
            </div>
            
        </div>
    </div>
</div>

<script>
let logoutFormToSubmit = null;

function confirmLogout(button, event) {
    if (event) event.preventDefault();
    logoutFormToSubmit = button.closest('form');
    
    const modal = document.getElementById('logout-confirm-modal');
    const backdrop = document.getElementById('logout-modal-backdrop');
    const content = document.getElementById('logout-modal-content');
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Trigger transition animation
    setTimeout(() => {
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');
        content.classList.remove('opacity-0', 'scale-95');
        content.classList.add('opacity-100', 'scale-100');
    }, 10);
}

function closeLogoutModal() {
    const backdrop = document.getElementById('logout-modal-backdrop');
    const content = document.getElementById('logout-modal-content');
    const modal = document.getElementById('logout-confirm-modal');
    
    backdrop.classList.remove('opacity-100');
    backdrop.classList.add('opacity-0');
    content.classList.remove('opacity-100', 'scale-100');
    content.classList.add('opacity-0', 'scale-95');
    
    // Hide modal after animation completes
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        logoutFormToSubmit = null;
    }, 300);
}

function executeLogout() {
    if (logoutFormToSubmit) {
        logoutFormToSubmit.submit();
        return;
    }
    
    // Fallback: dynamically create and submit logout form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('logout') }}";
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = "{{ csrf_token() }}";
    
    form.appendChild(csrfInput);
    document.body.appendChild(form);
    form.submit();
}

function printReportDirect(url) {
    let existingFrame = document.getElementById('report-print-iframe');
    if (existingFrame) {
        existingFrame.remove();
    }
    
    const iframe = document.createElement('iframe');
    iframe.id = 'report-print-iframe';
    iframe.style.position = 'fixed';
    iframe.style.right = '0';
    iframe.style.bottom = '0';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = '0';
    iframe.src = url;
    
    document.body.appendChild(iframe);
}
</script>
</body>
</html>
