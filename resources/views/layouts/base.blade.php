<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'easyShop - Gestion Commerciale')</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{asset('vendor/fontawesome-free/css/all.min.css')}}">

    <style>
        /* ===== RESET & BASE ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* ===== CSS VARIABLES ===== */
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --secondary: #0ea5e9;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgba(12, 10, 10, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* ===== BODY ===== */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: var(--gray-800);
            min-height: 100vh;
        }

        .sidebar {
            background: linear-gradient(135deg, #7C3AED 0%, #06B6D4 100%);
            color: white;
            min-height: 100vh;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            padding: 20px;
            overflow-y: auto;
        }

        .sidebar-logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 15px;
        }

        .sidebar-menu a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.3);
            font-weight: bold;
        }

         .main-content {
            margin-left: 250px;
            padding: 20px;
        }

    
        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7C3AED 0%, #06B6D4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .user-dropdown {
            position: relative;
        }

        .user-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            min-width: 200px;
            z-index: 1000;
            display: none;
        }

        .user-dropdown-menu.active {
            display: block;
        }

        .user-dropdown-menu a,
        .user-dropdown-menu button {
            display: block;
            width: 100%;
            padding: 12px 15px;
            text-align: left;
            color: #333;
            text-decoration: none;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .user-dropdown-menu a:hover,
        .user-dropdown-menu button:hover {
            background: #f5f5f5;
        }

         .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }

        ===== NAVIGATION BAR =====
        .navbar {
            /* position: sticky;
            top: 0;
            z-index: 1000;
            background-color:var(--white);
            box-shadow: var(--shadow-md);
            backdrop-filter: blur(10px); */

             position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: #111827;
            color: #fff;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .navbar-container {
            /* max-width: 1400px;
            margin: 0 auto;
            padding: 0 1.5rem; */

             display: flex;
            flex-direction: column;
            height: 100%;
            padding: 1rem;
        }

        .navbar-top {
            /* display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px; */

            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin-bottom: 1rem;
        }

        .navbar-brand {
            /* display: flex;
            align-items: center;
            gap: 0.875rem;
            text-decoration: none; */

            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-logo {
            /* width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
            box-shadow: var(--shadow-md);
            transition: transform 0.2s; */

                width: 40px;
                height: 40px;
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                border-radius: 8px;
                 color: white;
                font-weight: 700;
                font-size: 1.25rem;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
        }

        .navbar-logo:hover {
            transform: scale(1.05);
        }

        .navbar-title {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;

            color: #fff;
            font-size: 18px;
            font-weight: bold;
        }

        .navbar-search {
            /* flex: 1;
            max-width: 400px;
            margin: 0 2rem;
            position: relative; */

             display: none;
        }

        .navbar-search svg {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--gray-400);
        }

        .navbar-search input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border: 1px solid var(--gray-200);
            border-radius: 10px;
            font-size: 0.875rem;
            transition: all 0.3s;
            background: var(--gray-50);
        }

        .navbar-search input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .navbar-actions {
            /* display: flex;
            align-items: center;
            gap: 0.5rem; */

             display: flex;
            gap: 10px;
        }

        .navbar-icon-btn {
            /* width: 42px;
            height: 42px;
            border: none;
            background: var(--gray-50);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: all 0.2s;
            position: relative; */


            width: 38px;
            height: 38px;
           background: var(--gray-50);
            border: none;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .navbar-icon-btn:hover {
            background: var(--gray-200);
            transform: translateY(-2px);
        }

        .navbar-icon-btn svg {
            /* width: 20px;
            height: 20px;
            color: var(--gray-600); */

            width: 18px;
            height: 18px;
           color: var(--gray-600);
        }

        .notification-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            background: var(--danger);
            border-radius: 50%;
            border: 2px solid var(--white);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .navbar-user {
            /* display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-left: 1rem;
            margin-left: 1rem;
            border-left: 1px solid var(--gray-200);
            cursor: pointer;
            transition: all 0.2s;
            border-radius: 10px;
            padding: 0.5rem 1rem; */


             display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .navbar-user:hover {
            background: var(--gray-50);
        }

        .navbar-user-info {
            /* text-align: right;
            display: none; */

            font-size: 12px;
        }

        .navbar-user-name {
            /* font-size: 0.875rem;
            font-weight: 600; */
            color: var(--gray-800);

             font-weight: bold;
        }

        .navbar-user-role {
            font-size: 0.75rem;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .navbar-user-avatar {
            /* width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            box-shadow: var(--shadow); */

            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
             color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        

        @media (min-width: 768px) {
            .navbar-user-info {
                display: block;
            }
        }

        /* ===== NAVIGATION TABS ===== */
        .navbar-tabs {
            /* display: flex;
            align-items: center;
            gap: 0.25rem;
            overflow-x: auto;
            padding: 0.5rem 0;
            scrollbar-width: none; */
                 flex: 1;
                overflow-y: auto;
                margin-top: 1rem;
        }

        .navbar-tabs::-webkit-scrollbar {
            display: none;
        }

        .navbar-tab {
            padding: 0.75rem 1.25rem;
            /* font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-600);
            border: none;
            background: transparent;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.2s;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none; */


             display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            margin-bottom: 5px;
            border-radius: 8px;
            color: var(--gray-600);
             text-decoration: none;
            transition: 0.2s;
             background: transparent;
        }

        .navbar-tab svg {
            width: 18px;
            height: 18px;
        }

        .navbar-tab:hover {
            background: var(--gray-100);
            color: var(--primary);
        }

        .navbar-tab.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            box-shadow: var(--shadow);
        }

        .navbar-tab.logout {
            margin-left: auto;
            color: var(--danger);
        }

        .navbar-tab.logout:hover {
            background: rgba(239, 68, 68, 0.1);
        }
        .notification-badge {
        width: 8px;
        height: 8px;
        background: red;
        border-radius: 50%;
        margin-left: auto;
        }

        
        /* ===== TABS (Alternative Style) ===== */
        .tabs {
            display: flex;
            gap: 0.5rem;
            border-bottom: 2px solid var(--gray-200);
            padding: 1rem 2rem;
            background: var(--white);
            overflow-x: auto;
            max-width: 1400px;
            margin: 0 auto;
        }

        .tab-link {
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            color: var(--gray-600);
            font-weight: 500;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .tab-link:hover,
        .tab-link.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        /* ===== MAIN CONTENT ===== */
        .container {
            /* max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1.5rem; */

            max-width: 1200px;
             margin: 0 auto;

        }

        /* ===== DASHBOARD HEADER ===== */
        .dashboard-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .dashboard-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .dashboard-header p {
            color: var(--gray-600);
            font-size: 1rem;
        }

        .dashboard-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: var(--white);
            border-radius: 10px;
            box-shadow: var(--shadow);
            color: var(--gray-700);
            font-weight: 500;
        }

        .dashboard-date svg {
            width: 20px;
            height: 20px;
            color: var(--primary);
        }

          /* Conteneur Principal */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        /* ===== KPI CARDS ===== */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .kpi-card {
           background: var(--white);
            border-radius: 16px;
            padding: 1.75rem;
            box-shadow: var(--shadow);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--gray-100);
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--card-color) 0%, var(--card-color-light) 100%);
        }

        .kpi-card:hover {
           transform: translateY(-5px);
            box-shadow:var(--shadow-xl);
        }

        .kpi-card.primary {
            --card-color: var(--primary);
            --card-color-light: var(--primary-light);
        }

        .kpi-card.secondary {
            --card-color: var(--secondary);
            --card-color-light: #38bdf8;
        }

        .kpi-card.warning {
            --card-color: var(--warning);
            --card-color-light: #fbbf24;
        }

        .kpi-card.success {
            --card-color: var(--success);
            --card-color-light: #34d399;
        }

        .kpi-card.revenue {
            background: linear-gradient(135deg, #14B8A6 0%, #2DD4BF 100%);
        }

        .kpi-card.expenses {
            background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%);
        }

        .kpi-card.profit {
            background: linear-gradient(135deg, #7C3AED 0%, #A78BFA 100%);
        }

        .kpi-card.margin {
            background: linear-gradient(135deg, #F59E0B 0%, #FBBF24 100%);
        }

        .kpi-content {
            flex: 1;
        }

        .kpi-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .kpi-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.75rem;
            line-height: 1;
        }

        .kpi-trend {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-600);
        }

        .kpi-trend.up {
            color: var(--success);
        }

        .kpi-trend.down {
            color: var(--danger);
        }

        .kpi-trend svg {
            width: 16px;
            height: 16px;
        }

        .kpi-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--card-color) 0%, var(--card-color-light) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-md);
            flex-shrink: 0;
        }

        .kpi-icon svg {
            width: 24px;
            height: 24px;
            color: var(--white);
        }

        /* ===== CONTENT GRID ===== */
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        /* ===== SECTIONS ===== */
        .activity-section,
        .tasks-section,
        .card {
            background: var(--white);
            border-radius: 16px;
            padding: 1.75rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-100);
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title svg {
            width: 24px;
            height: 24px;
            color: var(--primary);
        }

        /* ===== ACTIVITY FEED ===== */
        .activity-feed {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 12px;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .activity-item:hover {
            background: var(--white);
            border-color: var(--primary);
            box-shadow: var(--shadow);
        }

        .activity-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .activity-icon svg {
            width: 20px;
            height: 20px;
            color: var(--white);
        }

        .activity-icon.order {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        }

        .activity-icon.stock {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        }

        .activity-icon.payment {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .activity-icon.alert {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.25rem;
        }

        .activity-time {
            font-size: 0.875rem;
            color: var(--gray-500);
        }

        /* ===== TASKS SECTION ===== */
        .task-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .task-item {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 1rem 1.25rem;
            background: var(--gray-50);
            border-radius: 12px;
            transition: all 0.2s;
            border-left: 3px solid var(--task-color);
        }

        .task-item:hover {
            background: var(--white);
            box-shadow: var(--shadow);
            transform: translateX(5px);
        }

        .task-item.purple {
            --task-color: #8b5cf6;
        }

        .task-item.cyan {
            --task-color: #06b6d4;
        }

        .task-item.teal {
            --task-color: #14b8a6;
        }

        .task-item.amber {
            --task-color: #f59e0b;
        }

        .task-checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid var(--gray-300);
            border-radius: 6px;
            cursor: pointer;
            appearance: none;
            transition: all 0.2s;
            position: relative;
            flex-shrink: 0;
        }

        .task-checkbox:checked {
            background: var(--task-color);
            border-color: var(--task-color);
        }

        .task-checkbox:checked::after {
            content: '✓';
            color: white;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 14px;
            font-weight: bold;
        }

        .task-text {
            flex: 1;
            color: var(--gray-700);
            font-weight: 500;
        }

        .task-checkbox:checked ~ .task-text,
        .task-checkbox:checked + .task-text {
            text-decoration: line-through;
            color: var(--gray-400);
        }

        /* ===== BOTTOM GRID ===== */
        .bottom-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 16px;
            padding: 1.75rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-100);
        }

        .stat-card h3 {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stat-card h3 svg {
            width: 20px;
            height: 20px;
            color: var(--primary);
        }

        /* ===== CHART ===== */
        .chart-container {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 0.75rem;
            height: 180px;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 12px;
            margin-bottom: 1rem;
        }

        .chart-bar {
            flex: 1;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 6px 6px 0 0;
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
        }

        .chart-bar:hover {
            background: linear-gradient(180deg, var(--secondary) 0%, var(--primary) 100%);
            transform: scaleY(1.05);
        }

        .chart-labels {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            font-size: 0.75rem;
            color: var(--gray-600);
            font-weight: 600;
        }

        .chart-labels span {
            flex: 1;
            text-align: center;
        }

        /* ===== PRODUCT LIST ===== */
        .product-list {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .product-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .product-info {
            flex: 1;
        }

        .product-name {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .product-bar {
            width: 100%;
            height: 8px;
            background: var(--gray-200);
            border-radius: 4px;
            overflow: hidden;
        }

        .product-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 4px;
            transition: width 0.5s;
        }

        .product-value {
            font-weight: 700;
            color: var(--gray-900);
            font-size: 1rem;
            min-width: 80px;
            text-align: right;
        }

        /* ===== FORMS ===== */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--gray-800);
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--gray-300);
            border-radius: 0.5rem;
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-error {
            color: var(--danger);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* ===== BUTTONS ===== */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        .btn-secondary {
            background: var(--secondary);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-success {
            background: var(--primary-dark);
            color: white;
        }

        .btn-info {
            background: var(--success);
            color: white;
        }

        /* ===== TABLES ===== */
        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .table thead {
            background: var(--gray-100);
            border-bottom: 2px solid var(--gray-200);
        }

        .table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--gray-800);
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .table tbody tr:hover {
            background: var(--gray-50);
        }

        /* ===== BADGES ===== */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .badge-info {
            background: rgba(6, 182, 212, 0.1);
            color: var(--secondary);
        }

        /* ===== ALERTS ===== */
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
            border-left: 4px solid var(--warning);
        }

        .alert strong {
            font-weight: 700;
        }

        .alert ul {
            margin-top: 0.5rem;
            padding-left: 1.5rem;
        }

        /* ===== GRID UTILITIES ===== */
        .grid {
            display: grid;
            gap: 1.5rem;
        }

        .grid-2 {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        .grid-3 {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }

        .grid-4 {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .navbar-search {
                display: none;
            }

            .dashboard-header h1 {
                font-size: 1.5rem;
            }

            .kpi-value {
                font-size: 2rem;
            }

            .bottom-grid {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 1rem;
            }

            .grid-2,
            .grid-3,
            .grid-4 {
                grid-template-columns: 1fr;
            }

           
            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 640px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        
            .navbar-tab span:last-child {
                display: inline;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
      @include('layouts.navigation')
     <div class="main-content">
      
        
    
    </div>
</body>