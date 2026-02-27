@extends('layouts.app')
@section('content')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap');

        :root {
            --bg: #f0f7f2;
            --bg-2: #e8f4ec;
            --panel: #ffffff;
            --panel-2: #f7fcf8;
            --border: rgba(34, 109, 62, .13);
            --border-2: rgba(34, 109, 62, .08);
            --text: #1a2e22;
            --text-2: #2d4a38;
            --muted: #6b8f76;
            --muted-2: #9ab5a3;
            --accent: #1e8c4a;
            --accent-light: #e4f5eb;
            --accent-mid: #34a85a;
            --teal: #0d9488;
            --teal-light: #e0f2f1;
            --lime: #84cc16;
            --good: #16a34a;
            --good-light: #dcfce7;
            --warn: #d97706;
            --warn-light: #fef3c7;
            --bad: #dc2626;
            --bad-light: #fee2e2;
            --violet: #7c3aed;
            --violet-light: #ede9fe;
            --blue: #2563eb;
            --blue-light: #dbeafe;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, .06), 0 1px 2px rgba(0, 0, 0, .04);
            --shadow: 0 4px 16px rgba(34, 109, 62, .08), 0 1px 4px rgba(0, 0, 0, .05);
            --shadow-md: 0 8px 24px rgba(34, 109, 62, .10), 0 2px 8px rgba(0, 0, 0, .06);
            --radius: 14px;
            --radius-sm: 8px;
            --radius-xs: 6px;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box
        }

        body {
            background: radial-gradient(ellipse 900px 500px at 0% 0%, rgba(30, 140, 74, .07), transparent 60%), radial-gradient(ellipse 700px 400px at 100% 100%, rgba(13, 148, 136, .06), transparent 55%), var(--bg);
            color: var(--text);
            font-family: 'DM Sans', system-ui, -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased
        }

        .dash-shell {
            height: calc(100vh - 84px);
            padding: 12px 14px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 10px
        }

        .dash-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 18px;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            flex-shrink: 0
        }

        .dash-topbar::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, var(--accent), var(--teal));
            border-radius: 4px 0 0 4px
        }

        .dash-title {
            display: flex;
            flex-direction: column;
            gap: 2px;
            line-height: 1.2;
            padding-left: 8px
        }

        .dash-title h1 {
            font-size: 16px;
            margin: 0;
            font-weight: 800;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -.2px
        }

        .dash-title h1 .title-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--accent-light);
            border: 1px solid rgba(30, 140, 74, .2);
            display: grid;
            place-items: center;
            color: var(--accent);
            font-size: 12px
        }

        .dash-title p {
            margin: 0;
            font-size: 11.5px;
            color: var(--muted);
            font-weight: 400
        }

        .topbar-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            background: var(--accent-light);
            border: 1px solid rgba(30, 140, 74, .18);
            font-size: 11px;
            font-weight: 600;
            color: var(--accent);
            flex-shrink: 0
        }

        .topbar-badge .pulse {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--accent);
            animation: pulse 2s infinite
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(30, 140, 74, .4)
            }

            50% {
                box-shadow: 0 0 0 5px rgba(30, 140, 74, 0)
            }
        }

        .tab-bar {
            display: flex;
            gap: 4px;
            padding: 4px;
            background: var(--panel);
            border: 1px solid var(--border-2);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            flex-shrink: 0;
            overflow-x: auto;
            scrollbar-width: thin
        }

        .tab-btn {
            padding: 8px 14px;
            border: none;
            background: transparent;
            border-radius: 10px;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--muted);
            cursor: pointer;
            transition: all .2s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 6px;
            font-family: inherit
        }

        .tab-btn:hover {
            background: var(--accent-light);
            color: var(--text-2)
        }

        .tab-btn.active {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 2px 8px rgba(30, 140, 74, .25)
        }

        .tab-btn .tab-icon {
            width: 18px;
            height: 18px;
            border-radius: 5px;
            display: grid;
            place-items: center;
            font-size: 9px;
            background: rgba(255, 255, 255, .15)
        }

        .tab-btn:not(.active) .tab-icon {
            background: var(--panel-2);
            border: 1px solid var(--border-2)
        }

        .tab-content {
            flex: 1;
            overflow: hidden;
            min-height: 0
        }

        .tab-pane {
            display: none;
            height: 100%;
            overflow-y: auto;
            scrollbar-width: thin
        }

        .tab-pane::-webkit-scrollbar {
            width: 5px
        }

        .tab-pane::-webkit-scrollbar-thumb {
            background: rgba(30, 140, 74, .2);
            border-radius: 4px
        }

        .tab-pane.active {
            display: block
        }

        .dash-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 10px;
            height: 100%;
            min-height: 0
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            align-content: start;
            overflow: hidden
        }

        .card {
            background: var(--panel);
            border: 1px solid var(--border-2);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: box-shadow .2s ease, transform .2s ease
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-1px)
        }

        .kpi {
            padding: 12px 14px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            min-height: 78px;
            position: relative;
            overflow: hidden
        }

        .kpi::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-light), transparent);
            opacity: .6
        }

        .kpi .meta {
            display: flex;
            flex-direction: column;
            gap: 3px;
            flex: 1;
            min-width: 0
        }

        .kpi .label {
            font-size: 10.5px;
            color: var(--muted);
            letter-spacing: .06em;
            text-transform: uppercase;
            font-weight: 700
        }

        .kpi .value {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -.03em;
            color: var(--text);
            line-height: 1.1;
            font-variant-numeric: tabular-nums
        }

        .kpi .sub {
            font-size: 11px;
            color: var(--muted);
            font-weight: 500
        }

        .kpi-clickable {
            cursor: pointer
        }

        .kpi-clickable:hover {
            border-color: var(--warn);
            box-shadow: 0 4px 16px rgba(217, 119, 6, .15)
        }

        .icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background: var(--accent-light);
            border: 1px solid rgba(30, 140, 74, .2);
            color: var(--accent);
            flex: 0 0 auto;
            font-size: 13px;
            box-shadow: 0 2px 6px rgba(30, 140, 74, .12)
        }

        .icon.good {
            background: var(--good-light);
            border-color: rgba(22, 163, 74, .22);
            color: var(--good)
        }

        .icon.warn {
            background: var(--warn-light);
            border-color: rgba(217, 119, 6, .22);
            color: var(--warn)
        }

        .icon.bad {
            background: var(--bad-light);
            border-color: rgba(220, 38, 38, .22);
            color: var(--bad)
        }

        .icon.violet {
            background: var(--violet-light);
            border-color: rgba(124, 58, 237, .22);
            color: var(--violet)
        }

        .charts {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 8px;
            overflow: hidden
        }

        .card-hd {
            padding: 10px 14px;
            background: linear-gradient(135deg, #fafffe, var(--panel-2));
            border-bottom: 1px solid var(--border-2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px
        }

        .card-hd h6 {
            margin: 0;
            font-size: 11px;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: var(--text-2);
            font-weight: 800;
            display: flex;
            gap: 7px;
            align-items: center
        }

        .hd-icon {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            display: grid;
            place-items: center;
            font-size: 10px
        }

        .hd-icon.green {
            background: var(--accent-light);
            color: var(--accent)
        }

        .hd-icon.teal {
            background: var(--teal-light);
            color: var(--teal)
        }

        .hd-icon.violet {
            background: var(--violet-light);
            color: var(--violet)
        }

        .hd-icon.warn {
            background: var(--warn-light);
            color: var(--warn)
        }

        .hd-icon.blue {
            background: var(--blue-light);
            color: var(--blue)
        }

        .card-bd {
            padding: 10px 12px;
            height: calc(100% - 42px);
            overflow: hidden
        }

        .chart-wrap {
            height: 100%;
            width: 100%;
            position: relative
        }

        /* Scrollable chart containers */
        .chart-scroll-outer {
            height: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            scrollbar-width: thin;
            scrollbar-color: rgba(30, 140, 74, .25) transparent
        }

        .chart-scroll-outer::-webkit-scrollbar {
            height: 5px
        }

        .chart-scroll-outer::-webkit-scrollbar-thumb {
            background: rgba(30, 140, 74, .25);
            border-radius: 4px
        }

        .chart-scroll-inner {
            height: 100%;
            min-width: 100%
        }

        .table-scroll {
            height: 100%;
            overflow-y: auto
        }

        .table-scroll::-webkit-scrollbar {
            width: 4px
        }

        .table-scroll::-webkit-scrollbar-thumb {
            background: rgba(30, 140, 74, .2);
            border-radius: 4px
        }

        .mini-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px
        }

        .mini-table thead th {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted);
            padding: 8px 12px;
            border-bottom: 1px solid var(--border);
            background: var(--panel-2);
            font-weight: 700;
            position: sticky;
            top: 0;
            z-index: 1
        }

        .mini-table tbody tr {
            transition: background .15s
        }

        .mini-table tbody tr:hover {
            background: var(--accent-light)
        }

        .mini-table td {
            padding: 7px 12px;
            border-bottom: 1px solid rgba(34, 109, 62, .06);
            vertical-align: middle;
            color: var(--text-2)
        }

        .mini-table tfoot td {
            padding: 8px 12px;
            font-weight: 800;
            color: var(--accent);
            border-top: 2px solid var(--border);
            background: var(--panel-2);
            font-family: 'DM Mono', monospace
        }

        .badge-rank {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            background: var(--accent-light);
            border: 1px solid rgba(30, 140, 74, .2);
            color: var(--accent);
            font-weight: 800;
            font-size: 10px;
            letter-spacing: .04em;
            white-space: nowrap;
            font-family: 'DM Mono', monospace
        }

        .afpos-chip {
            display: inline-block;
            padding: 2px 7px;
            margin: 2px 3px 2px 0;
            border-radius: var(--radius-xs);
            background: var(--panel-2);
            border: 1px solid var(--border);
            color: var(--text-2);
            font-size: 10.5px;
            white-space: nowrap;
            font-weight: 500
        }

        .afpos-chip strong {
            font-weight: 700;
            color: var(--accent)
        }

        .muted {
            color: var(--muted)
        }

        .val-green {
            color: var(--good) !important
        }

        .val-warn {
            color: var(--warn) !important
        }

        .val-blue {
            color: var(--blue) !important
        }

        .val-teal {
            color: var(--teal) !important
        }

        .chart-grid-full {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            height: 100%;
            padding: 8px
        }

        .chart-card-tall .card-bd {
            height: calc(100% - 80px);
            min-height: 350px
        }

        .filter-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            flex-wrap: wrap
        }

        .filter-row label {
            font-size: 10.5px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em
        }

        .filter-row select {
            padding: 5px 10px;
            border: 1px solid var(--border);
            border-radius: var(--radius-xs);
            font-size: 11px;
            font-family: inherit;
            font-weight: 600;
            color: var(--text-2);
            background: var(--panel);
            cursor: pointer
        }

        /* Drill-down modal */
        .drill-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .4);
            z-index: 9998;
            display: none;
            align-items: center;
            justify-content: center
        }

        .drill-overlay.show {
            display: flex
        }

        .drill-modal {
            background: var(--panel);
            border-radius: var(--radius);
            box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
            width: 90%;
            max-width: 860px;
            max-height: 82vh;
            display: flex;
            flex-direction: column;
            overflow: hidden
        }

        .drill-hd {
            padding: 14px 20px;
            background: linear-gradient(135deg, var(--accent-light), var(--panel-2));
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px
        }

        .drill-hd h5 {
            margin: 0;
            font-size: 14px;
            font-weight: 800;
            color: var(--text);
            flex: 1
        }

        .drill-hd-actions {
            display: flex;
            gap: 8px;
            align-items: center
        }

        .drill-close {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: var(--muted);
            padding: 4px 8px;
            border-radius: 6px
        }

        .drill-close:hover {
            background: var(--bad-light);
            color: var(--bad)
        }

        .btn-print {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--panel);
            font-size: 11px;
            font-weight: 700;
            color: var(--text-2);
            cursor: pointer;
            font-family: inherit;
            transition: all .2s
        }

        .btn-print:hover {
            background: var(--accent-light);
            border-color: rgba(30, 140, 74, .3);
            color: var(--accent)
        }

        .btn-csv {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid rgba(13, 148, 136, .35);
            background: var(--teal-light);
            font-size: 11px;
            font-weight: 700;
            color: var(--teal);
            cursor: pointer;
            font-family: inherit;
            transition: all .2s
        }

        .btn-csv:hover {
            background: var(--teal);
            color: #fff;
            border-color: var(--teal)
        }

        .btn-excel {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid rgba(22, 163, 74, .35);
            background: var(--good-light);
            font-size: 11px;
            font-weight: 700;
            color: var(--good);
            cursor: pointer;
            font-family: inherit;
            transition: all .2s
        }

        .btn-excel:hover {
            background: var(--good);
            color: #fff;
            border-color: var(--good)
        }

        .drill-bd {
            flex: 1;
            overflow-y: auto;
            padding: 0
        }

        .drill-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px
        }

        .drill-table thead th {
            padding: 10px 14px;
            background: var(--panel-2);
            border-bottom: 1px solid var(--border);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted);
            font-weight: 700;
            position: sticky;
            top: 0;
            z-index: 1
        }

        .drill-table tbody td {
            padding: 8px 14px;
            border-bottom: 1px solid rgba(34, 109, 62, .06);
            color: var(--text-2)
        }

        .drill-table tbody tr:hover {
            background: var(--accent-light)
        }

        .drill-loading {
            padding: 40px;
            text-align: center;
            color: var(--muted);
            font-size: 13px
        }

        .drill-empty {
            padding: 40px;
            text-align: center;
            color: var(--muted-2);
            font-size: 13px
        }

        /* Print styles */
        @media print {
            body * {
                visibility: hidden !important;
            }

            #printSection,
            #printSection * {
                visibility: visible !important;
            }

            #printSection {
                position: fixed;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
            }

            .btn-print,
            .drill-close,
            .drill-hd-actions {
                display: none !important;
            }
        }

        @media(max-width:1200px) {
            body {
                overflow: auto
            }

            .dash-shell {
                height: auto;
                overflow: visible
            }

            .dash-grid {
                grid-template-columns: 1fr;
                height: auto
            }

            .charts {
                grid-template-columns: 1fr;
                grid-template-rows: auto
            }

            .card-bd {
                height: 260px
            }

            .tab-bar {
                flex-wrap: nowrap
            }
        }
    </style>

    {{-- Drill-down Modal --}}
    <div class="drill-overlay" id="drillOverlay" onclick="if(event.target===this)closeDrill()">
        <div class="drill-modal">
            <div class="drill-hd">
                <h5 id="drillTitle">Officers</h5>
                <div class="drill-hd-actions">
                    <button class="btn-csv" onclick="downloadDrill('csv')" title="Download as CSV"><i
                            class="fas fa-file-csv"></i> CSV</button>
                    <button class="btn-excel" onclick="downloadDrill('excel')" title="Download as Excel"><i
                            class="fas fa-file-excel"></i> Excel</button>
                    <button class="btn-print" onclick="printDrillList()" title="Print list"><i class="fas fa-print"></i>
                        Print</button>
                    <button class="drill-close" onclick="closeDrill()">&times;</button>
                </div>
            </div>
            <div class="drill-bd" id="drillBody">
                <div class="drill-loading">Loading...</div>
            </div>
        </div>
    </div>

    {{-- Hidden print section --}}
    <div id="printSection" style="display:none"></div>

    <div class="dash-shell container-fluid">
        <div class="dash-topbar">
            <div class="dash-title">
                <h1><span class="title-icon"><i class="fas fa-chart-line"></i></span> Officers Dashboard</h1>
                <p>Personnel overview — rank distribution, assignment coverage &amp; AFPOS</p>
            </div>
            <div class="topbar-badge"><span class="pulse"></span> Live Overview</div>
        </div>

        <div class="tab-bar" id="dashTabBar">
            <button class="tab-btn active" data-tab="overview"><span class="tab-icon"><i
                        class="fas fa-th-large"></i></span>Overview</button>
            <button class="tab-btn" data-tab="afpos-rank"><span class="tab-icon"><i
                        class="fas fa-layer-group"></i></span>AFPOS per Rank</button>
            <button class="tab-btn" data-tab="population"><span class="tab-icon"><i
                        class="fas fa-sort-amount-down"></i></span>Population</button>
            <button class="tab-btn" data-tab="tenure"><span class="tab-icon"><i class="fas fa-clock"></i></span>Tenure in
                Grade</button>
            <button class="tab-btn" data-tab="age"><span class="tab-icon"><i class="fas fa-birthday-cake"></i></span>Age per
                Rank</button>
            <button class="tab-btn" data-tab="company-cdr"><span class="tab-icon"><i
                        class="fas fa-user-shield"></i></span>Company Commanders</button>
            <button class="tab-btn" data-tab="cgsc"><span class="tab-icon"><i class="fas fa-graduation-cap"></i></span>CGSC
                Graduates</button>
        </div>

        <div class="tab-content">

            {{-- TAB 1: OVERVIEW --}}
            <div class="tab-pane active" id="tab-overview">
                <div class="dash-grid">
                    <div class="kpi-grid" style="width:300px">
                        <div class="card kpi">
                            <div class="meta">
                                <div class="label">Total Officers</div>
                                <div class="value">{{ number_format($stats['total_officers']) }}</div>
                                <div class="sub">Active personnel</div>
                            </div>
                            <div class="icon"><i class="fas fa-users"></i></div>
                        </div>
                        <div class="card kpi kpi-clickable" onclick="fetchRetiringOfficers()"
                            title="Click to view list — RA 11939 Sec 6(a)">
                            <div class="meta">
                                <div class="label">Retiring (12 mo.)</div>
                                <div class="value val-warn">{{ number_format($stats['retiring_12_months'] ?? 0) }}</div>
                                <div class="sub">RA 11939 · Age 57</div>
                            </div>
                            <div class="icon warn"><i class="fas fa-hourglass-half"></i></div>
                        </div>
                        <div class="card kpi kpi-clickable" onclick="fetchAssignedOfficers('assigned')"
                            title="Click to view assigned officers" style="--kpi-hover-color:rgba(22,163,74,.15)">
                            <div class="meta">
                                <div class="label">Assigned</div>
                                <div class="value val-green">{{ number_format($stats['assigned_officers']) }}</div>
                                <div class="sub">{{ $stats['assigned_rate'] }}% coverage</div>
                            </div>
                            <div class="icon good"><i class="fas fa-user-check"></i></div>
                        </div>
                        <div class="card kpi kpi-clickable" onclick="fetchAssignedOfficers('unassigned')"
                            title="Click to view unassigned officers">
                            <div class="meta">
                                <div class="label">Unassigned</div>
                                <div class="value val-warn">{{ number_format($stats['unassigned_officers']) }}</div>
                                <div class="sub">Needs review</div>
                            </div>
                            <div class="icon warn"><i class="fas fa-user-times"></i></div>
                        </div>
                        <div class="card kpi kpi-clickable" onclick="fetchGenderOfficers('male')"
                            title="Click to view male officers">
                            <div class="meta">
                                <div class="label">Male</div>
                                <div class="value val-blue">{{ number_format($stats['male_officers']) }}</div>
                                <div class="sub">
                                    {{ $stats['total_officers'] > 0 ? number_format(($stats['male_officers'] / $stats['total_officers']) * 100, 1) : 0 }}%
                                </div>
                            </div>
                            <div class="icon"><i class="fas fa-male"></i></div>
                        </div>
                        <div class="card kpi kpi-clickable" onclick="fetchGenderOfficers('female')"
                            title="Click to view female officers">
                            <div class="meta">
                                <div class="label">Female</div>
                                <div class="value val-teal">{{ number_format($stats['female_officers']) }}</div>
                                <div class="sub">
                                    {{ $stats['total_officers'] > 0 ? number_format(($stats['female_officers'] / $stats['total_officers']) * 100, 1) : 0 }}%
                                </div>
                            </div>
                            <div class="icon violet"><i class="fas fa-female"></i></div>
                        </div>
                    </div>
                    <div class="charts">
                        <div class="card">
                            <div class="card-hd">
                                <h6><span class="hd-icon green"><i class="fas fa-filter"></i></span>Rank Distribution</h6>
                            </div>
                            <div class="card-bd">
                                <div class="chart-wrap"><canvas id="rankChart"></canvas></div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-hd">
                                <h6><span class="hd-icon violet"><i class="fas fa-building"></i></span>PAMU by Rank</h6>
                            </div>
                            <div class="card-bd" style="padding:0;height:calc(100% - 42px)">
                                <div class="table-scroll">
                                    <table class="mini-table">
                                        <thead>
                                            <tr>
                                                <th>PAMU</th>@foreach($pamuRanks as $pr)<th style="text-align:center">
                                                {{ $pr }}</th>@endforeach<th style="text-align:right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pamuRecap as $row)
                                                <tr>
                                                    <td title="{{ $row['name'] ?? '' }}"><span class="badge-rank"
                                                            style="font-family:var(--font-sans);background:var(--blue-light);color:var(--blue);border-color:rgba(37,99,235,.2)">{{ $row['code'] }}</span>
                                                    </td>
                                                    @foreach($pamuRanks as $pr)
                                                        <td
                                                            style="text-align:center;font-family:var(--font-sans);font-size:11px;{{ $row[$pr] > 0 ? 'font-weight:700;color:var(--text)' : 'color:var(--muted-2)' }}">
                                                            {{ $row[$pr] ?: '—' }}</td>
                                                    @endforeach
                                                    <td
                                                        style="text-align:right;font-weight:800;color:var(--accent);font-family:var(--font-sans);">
                                                        {{ $row['total'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td style="font-weight:800">TOTAL</td>
                                                @foreach($pamuRanks as $pr)<td
                                                    style="font-family:var(--font-sans); text-align:center">
                                                {{ $pamuTotals[$pr] ?? 0 }}</td>@endforeach
                                                <td style="font-family:var(--font-sans); text-align:right">
                                                    {{ $pamuTotals['total'] ?? 0 }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-hd">
                                <h6><span class="hd-icon teal"><i class="fas fa-table"></i></span>Tally of AFPOS</h6>
                            </div>
                            <div class="card-bd" style="padding:0;height:calc(100% - 42px)">
                                <div class="table-scroll">
                                    <table class="mini-table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>AFPOS Breakdown</th>
                                                <th style="text-align:right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($chartData['ranks'] as $index => $rank)
                                                @php $afposD = $chartData['afpos'][$rank] ?? collect([]);
                                                $total = $chartData['total'][$index] ?? 0; @endphp
                                                <tr>
                                                    <td><span class="badge-rank">{{ $rank }}</span></td>
                                                    <td>@if($afposD->count() > 0)@foreach($afposD as $afpos)<span
                                                        class="afpos-chip">{{ $afpos->AFPOS }}:
                                                    <strong>{{ $afpos->count }}</strong></span>@endforeach @else<span
                                                            class="muted">No data</span>@endif</td>
                                                    <td
                                                        style="text-align:right;font-weight:800;color:var(--accent);font-family:'DM Mono',monospace">
                                                        {{ $total }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-hd">
                                <h6><span class="hd-icon green"><i class="fas fa-user-check"></i></span>Assigned vs
                                    Unassigned</h6>
                            </div>
                            <div class="card-bd">
                                <div class="chart-wrap"><canvas id="assignmentChart"></canvas></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 2: AFPOS PER RANK — scrollable, clickable, labels always shown --}}
            <div class="tab-pane" id="tab-afpos-rank">
                <div class="chart-grid-full" style="height:100%">
                    <div class="card chart-card-tall">
                        <div class="card-hd">
                            <h6><span class="hd-icon violet"><i class="fas fa-layer-group"></i></span>AFPOS Distribution per
                                Rank (2LT – COL)</h6>
                        </div>
                        <div class="filter-row">
                            <label>Filter AFPOS:</label>
                            <select id="afposRankFilter" onchange="updateAfposRankChart()">
                                <option value="all">All AFPOS</option>
                            </select>
                            <span style="font-size:10px;color:var(--muted);margin-left:8px"><strong>Click bars to see
                                    officers</strong></span>
                        </div>
                        <div class="card-bd" style="min-height:450px;padding:0">
                            <div class="chart-scroll-outer">
                                <div class="chart-scroll-inner" id="afposRankScrollInner">
                                    <canvas id="afposRankFullChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 3: POPULATION PYRAMID — clickable --}}
            <div class="tab-pane" id="tab-population">
                <div class="chart-grid-full" style="height:100%">
                    <div class="card chart-card-tall">
                        <div class="card-hd">
                            <h6><span class="hd-icon green"><i class="fas fa-sort-amount-down"></i></span>Officer Population
                                Pyramid (2LT – COL)</h6>
                        </div>
                        <div class="filter-row">
                            <span style="font-size:10px;color:var(--muted)"><strong>Click any bar to view the list of
                                    officers for that rank</strong></span>
                        </div>
                        <div class="card-bd" style="min-height:450px">
                            <div class="chart-wrap"><canvas id="populationPyramid"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 4: TENURE — scrollable, labels always shown --}}
            <div class="tab-pane" id="tab-tenure">
                <div class="chart-grid-full" style="height:100%">
                    <div class="card chart-card-tall">
                        <div class="card-hd">
                            <h6><span class="hd-icon teal"><i class="fas fa-clock"></i></span>Tenure-In-Grade per RA 11939
                                (CPT – COL)</h6>
                        </div>
                        <div class="filter-row">
                            <label>Filter:</label>
                            <select id="tenureViewMode" onchange="updateTenureChart()">
                                <option value="exceeded">Exceeded Max Tenure</option>
                                <option value="not_exceeded">Not Exceeded Max Tenure</option>
                                <option value="average">Average Tenure (years)</option>
                            </select>
                            <span style="font-size:10px;color:var(--muted);margin-left:8px">CPT 6yr · MAJ 6yr · LTC 7yr ·
                                COL 10yr — <strong>Click bars to see officers</strong></span>
                        </div>
                        <div class="card-bd" style="min-height:450px;padding:0">
                            <div class="chart-scroll-outer">
                                <div class="chart-scroll-inner" id="tenureScrollInner">
                                    <canvas id="tenureChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 5: AGE — scrollable, labels always shown --}}
            <div class="tab-pane" id="tab-age">
                <div class="chart-grid-full" style="height:100%">
                    <div class="card chart-card-tall">
                        <div class="card-hd">
                            <h6><span class="hd-icon warn"><i class="fas fa-birthday-cake"></i></span>Average Age per Rank
                                (LTC – COL)</h6>
                        </div>
                        <div class="filter-row">
                            <label>Filter AFPOS:</label>
                            <select id="ageAfposFilter" onchange="updateAgeChart()">
                                <option value="all">All AFPOS (Top 12)</option>
                            </select>
                            <span style="font-size:10px;color:var(--muted);margin-left:8px"><strong>Click bars to see
                                    officers</strong></span>
                        </div>
                        <div class="card-bd" style="min-height:450px;padding:0">
                            <div class="chart-scroll-outer">
                                <div class="chart-scroll-inner" id="ageScrollInner">
                                    <canvas id="ageChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 6: CC — scrollable, clickable, labels always shown --}}
            <div class="tab-pane" id="tab-company-cdr">
                <div class="chart-grid-full" style="height:100%">
                    <div class="card chart-card-tall">
                        <div class="card-hd">
                            <h6><span class="hd-icon blue"><i class="fas fa-user-shield"></i></span>Current Company
                                Commanders — With vs Without OAC (2LT – CPT)</h6>
                        </div>
                        <div class="filter-row">
                            <label>View by:</label>
                            <select id="ccViewMode" onchange="updateCCChart()">
                                <option value="rank">Group by Rank</option>
                                <option value="afpos">Group by AFPOS</option>
                            </select>
                            <span style="font-size:10px;color:var(--muted);margin-left:8px"><strong>Click bars to see
                                    officers</strong></span>
                        </div>
                        <div class="card-bd" style="min-height:450px;padding:0">
                            <div class="chart-scroll-outer">
                                <div class="chart-scroll-inner" id="ccScrollInner">
                                    <canvas id="ccChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 7: CGSC — scrollable, clickable, labels always shown --}}
            <div class="tab-pane" id="tab-cgsc">
                <div class="chart-grid-full" style="height:100%">
                    <div class="card chart-card-tall">
                        <div class="card-hd">
                            <h6><span class="hd-icon violet"><i class="fas fa-graduation-cap"></i></span>CGSC Graduates —
                                Current Bn Cdr vs Not Yet Designated (LTC – COL)</h6>
                        </div>
                        <div class="filter-row">
                            <label>View by:</label>
                            <select id="cgscViewMode" onchange="updateCGSCChart()">
                                <option value="rank">Group by Rank</option>
                                <option value="afpos">Group by AFPOS</option>
                            </select>
                            <span style="font-size:10px;color:var(--muted);margin-left:8px"><strong>Click bars to see
                                    officers</strong></span>
                        </div>
                        <div class="card-bd" style="min-height:450px;padding:0">
                            <div class="chart-scroll-outer">
                                <div class="chart-scroll-inner" id="cgscScrollInner">
                                    <canvas id="cgscChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script>
        Chart.register(ChartDataLabels);

        const chartData = @json($chartData);
        const tab2Data = @json($tab2Data);
        const tab3Data = @json($tab3Data);
        const tab4Data = @json($tab4Data);
        const tab5Data = @json($tab5Data);
        const tab6Data = @json($tab6Data);
        const tab7Data = @json($tab7Data);

        Chart.defaults.font.family = "'DM Sans',system-ui,sans-serif";
        Chart.defaults.font.size = 10;
        Chart.defaults.color = "#6b8f76";

        const gridColor = "rgba(34,109,62,.08)";
        const tipBg = "#1a2e22";

        const colors = {
            male: "rgba(37,99,235,.75)", female: "rgba(237, 189, 58, 0.7)",
            assigned: "rgba(22,163,74,.75)", unassigned: "rgba(217,119,6,.70)"
        };
        const palette = [
            "rgba(22,163,74,.75)", "rgba(13,148,136,.70)", "rgba(37,99,235,.65)",
            "rgba(124,58,237,.65)", "rgba(217,119,6,.65)", "rgba(132,204,22,.65)",
            "rgba(220,38,38,.60)", "rgba(5,150,105,.65)", "rgba(236,72,153,.65)",
            "rgba(99,102,241,.65)", "rgba(245,158,11,.65)", "rgba(6,182,212,.65)"
        ];

        const baseOptions = {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { labels: { usePointStyle: true, pointStyle: "circle", boxWidth: 7, padding: 12, font: { weight: "700", size: 10 }, color: "#2d4a38" } },
                tooltip: { backgroundColor: tipBg, borderColor: "rgba(30,140,74,.35)", borderWidth: 1, titleColor: "rgba(255,255,255,.95)", bodyColor: "rgba(255,255,255,.85)", padding: 10, displayColors: true, boxPadding: 6, cornerRadius: 8 },
                datalabels: { display: false }
            },
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: "#6b8f76" } },
                y: { grid: { color: gridColor }, ticks: { color: "#6b8f76" } }
            }
        };

        // ─── Minimum bar width per group for scrollable charts ────────────────────────
        // We set a min px width per label so labels are always readable and bars don't crush.
        const MIN_BAR_WIDTH = 55; // px per label group

        function setScrollableCanvasWidth(wrapperId, labelCount) {
            const outer = document.querySelector(`#${wrapperId}`).closest('.chart-scroll-outer');
            if (!outer) return;
            const outerW = outer.clientWidth || 600;
            const minW = labelCount * MIN_BAR_WIDTH;
            const finalW = Math.max(outerW, minW);
            const inner = document.getElementById(wrapperId);
            inner.style.width = finalW + 'px';
            // height inherits from card-bd via CSS
        }

        // ─── Drill-down Modal helpers ─────────────────────────────────────────────────
        let _currentDrillData = { title: '', headers: [], rows: [] };

        function openDrill(title, html) {
            document.getElementById('drillTitle').textContent = title;
            document.getElementById('drillBody').innerHTML = html;
            // Reset data until a fetch populates it
            _currentDrillData = { title, headers: [], rows: [] };
            document.getElementById('drillOverlay').classList.add('show');
        }
        function setDrillData(headers, rows) {
            _currentDrillData.headers = headers;
            _currentDrillData.rows = rows;
        }
        function closeDrill() {
            document.getElementById('drillOverlay').classList.remove('show');
        }

        const loadingHtml = '<div class="drill-loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
        const emptyHtml = m => `<div class="drill-empty">${m}</div>`;

        // ─── Download CSV / Excel ──────────────────────────────────────────────────────
        function sanitizeFilename(name) {
            return name.replace(/[^a-z0-9_\-\s]/gi, '_').replace(/\s+/g, '_').substring(0, 80);
        }

        function downloadDrill(format) {
            const { title, headers, rows } = _currentDrillData;
            if (!headers.length || !rows.length) {
                alert('No data available to download yet. Please wait for the list to load.');
                return;
            }
            const filename = sanitizeFilename(title) || 'officers_list';

            if (format === 'csv') {
                const escape = v => {
                    const s = String(v === null || v === undefined ? '' : v);
                    return s.includes(',') || s.includes('"') || s.includes('\n')
                        ? '"' + s.replace(/"/g, '""') + '"'
                        : s;
                };
                const lines = [
                    headers.map(escape).join(','),
                    ...rows.map(r => r.map(escape).join(','))
                ];
                const blob = new Blob(['\uFEFF' + lines.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url; a.download = filename + '.csv';
                document.body.appendChild(a); a.click();
                document.body.removeChild(a); URL.revokeObjectURL(url);

            } else if (format === 'excel') {
                const wsData = [headers, ...rows];
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.aoa_to_sheet(wsData);
                // Auto column widths
                const colWidths = headers.map((h, ci) => {
                    const max = Math.max(h.length, ...rows.map(r => String(r[ci] ?? '').length));
                    return { wch: Math.min(Math.max(max + 2, 10), 40) };
                });
                ws['!cols'] = colWidths;
                XLSX.utils.book_append_sheet(wb, ws, 'Officers');
                XLSX.writeFile(wb, filename + '.xlsx');
            }
        }

        function printDrillList() {
            const title = document.getElementById('drillTitle').textContent;
            const section = document.getElementById('printSection');
            section.innerHTML = `<h2 style="margin-bottom:12px;font-family:sans-serif">${title}</h2>` + document.getElementById('drillBody').innerHTML;
            section.style.display = 'block';
            window.print();
            section.style.display = 'none';
        }

        // ─── Date formatter: dd-Mmm-YYYY ──────────────────────────────────────────────
        // The server now sends dates already formatted as 'd-M-Y' (e.g., 15-Jan-2025)
        // so we just display them directly. Legacy raw ISO dates are also handled here.
        function fmtDob(raw) {
            if (!raw) return '';
            // If already formatted (contains a letter month), return as-is
            if (/[a-zA-Z]/.test(raw)) return raw;
            // Otherwise parse ISO and reformat
            try {
                const d = new Date(raw);
                if (isNaN(d)) return raw;
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const dd = String(d.getDate()).padStart(2, '0');
                return `${dd}-${months[d.getMonth()]}-${d.getFullYear()}`;
            } catch (e) { return raw; }
        }

        // ─── Tab system ───────────────────────────────────────────────────────────────
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabPanes = document.querySelectorAll('.tab-pane');
        const chartInstances = {};
        const chartsInitialized = { overview: true };

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const t = btn.dataset.tab;
                tabBtns.forEach(b => b.classList.remove('active'));
                tabPanes.forEach(p => p.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('tab-' + t).classList.add('active');
                if (!chartsInitialized[t]) {
                    chartsInitialized[t] = true;
                    setTimeout(() => initTabCharts(t), 50);
                }
            });
        });

        initOverviewCharts();

        function initTabCharts(t) {
            switch (t) {
                case 'afpos-rank': initAfposRankChart(); break;
                case 'population': initPopulationPyramid(); break;
                case 'tenure': initTenureChart(); break;
                case 'age': initAgeChart(); break;
                case 'company-cdr': initCCChart(); break;
                case 'cgsc': initCGSCChart(); break;
            }
        }

        // ═══════════════════════════════════════════════════════════════
        //  TAB 1: OVERVIEW
        // ═══════════════════════════════════════════════════════════════
        function initOverviewCharts() {
            const tot = chartData.total.reduce((s, v) => s + v, 0);
            new Chart(document.getElementById("rankChart"), {
                type: "bar",
                data: {
                    labels: chartData.ranks, datasets: [
                        { label: "Male", data: chartData.male, backgroundColor: colors.male, borderWidth: 0 },
                        { label: "Female", data: chartData.female, backgroundColor: colors.female, borderWidth: 0, borderRadius: { topLeft: 0, topRight: 4, bottomLeft: 0, bottomRight: 4 } }
                    ]
                },
                options: {
                    ...baseOptions, indexAxis: "y",
                    scales: {
                        x: { stacked: true, grid: { color: gridColor } },
                        y: { stacked: true, grid: { display: false }, ticks: { color: "#0a0a0a", font: { weight: "700" } } }
                    },
                    plugins: {
                        ...baseOptions.plugins,
                        tooltip: {
                            ...baseOptions.plugins.tooltip, callbacks: {
                                title: ctx => { const i = ctx[0].dataIndex, t2 = chartData.total[i]; return `${chartData.ranks[i]} • ${t2} (${tot > 0 ? ((t2 / tot) * 100).toFixed(0) : 0}%)`; }
                            }
                        },
                        datalabels: {
                            display: ctx => ctx.dataset.data[ctx.dataIndex] > 0,
                            color: '#272323', font: { weight: '800', size: 9 },
                            formatter: (v, ctx) => v > 0 ? v : '',
                            anchor: 'center', align: 'center', clamp: true
                        }
                    }
                }
            });
            new Chart(document.getElementById("assignmentChart"), {
                type: "bar",
                data: {
                    labels: chartData.ranks, datasets: [
                        { label: "Assigned", data: chartData.assigned, backgroundColor: colors.assigned, borderWidth: 0, borderRadius: 5 },
                        { label: "Unassigned", data: chartData.unassigned, backgroundColor: colors.unassigned, borderWidth: 0, borderRadius: 5 }
                    ]
                },
                options: {
                    ...baseOptions,
                    scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: gridColor } } },
                    plugins: {
                        ...baseOptions.plugins,
                        datalabels: {
                            display: ctx => ctx.dataset.data[ctx.dataIndex] > 0,
                            color: '#1b1919', font: { weight: '800', size: 9 },
                            formatter: v => v > 0 ? v : '',
                            anchor: 'center', align: 'center', clamp: true
                        }
                    }
                }
            });
        }

        // ═══════════════════════════════════════════════════════════════
        //  TAB 2: AFPOS PER RANK — scrollable, always-on labels, clickable
        // ═══════════════════════════════════════════════════════════════
        function initAfposRankChart() {
            const s = document.getElementById('afposRankFilter');
            tab2Data.afposList.forEach(a => {
                const o = document.createElement('option'); o.value = a; o.textContent = a; s.appendChild(o);
            });
            updateAfposRankChart();
        }

        function updateAfposRankChart() {
            if (chartInstances['afpos-rank']) chartInstances['afpos-rank'].destroy();

            const f = document.getElementById('afposRankFilter').value;
            const list = f === 'all' ? tab2Data.afposList.slice(0, 12) : [f];

            const labelCount = tab2Data.ranks.length;
            setScrollableCanvasWidth('afposRankScrollInner', labelCount);

            const ds = list.map((a, i) => ({
                label: a,
                data: tab2Data.ranks.map(r => (tab2Data.data[r] && tab2Data.data[r][a]) || 0),
                backgroundColor: palette[i % palette.length],
                borderColor: "rgba(255,255,255,.15)", borderWidth: 1, borderRadius: 4, _afpos: a
            }));

            chartInstances['afpos-rank'] = new Chart(document.getElementById('afposRankFullChart'), {
                type: 'bar',
                data: { labels: tab2Data.ranks, datasets: ds },
                options: {
                    ...baseOptions,
                    onClick: (evt, els) => {
                        if (!els.length) return;
                        const rank = tab2Data.ranks[els[0].index];
                        const afpos = ds[els[0].datasetIndex]._afpos;
                        fetchAfposRankOfficers(rank, afpos);
                    },
                    plugins: {
                        ...baseOptions.plugins,
                        legend: { ...baseOptions.plugins.legend, display: true, position: "bottom" },
                        datalabels: {
                            display: ctx => ctx.dataset.data[ctx.dataIndex] > 0,
                            color: '#101010', font: { weight: '700', size: 9 },
                            formatter: v => v > 0 ? v : '',
                            anchor: 'center', align: 'center', clamp: true
                        }
                    },
                    scales: {
                        x: { stacked: true, grid: { display: false }, ticks: { color: "#2d4a38", font: { weight: "700" } } },
                        y: { stacked: true, beginAtZero: true, grid: { color: gridColor } }
                    }
                }
            });
        }

        // ═══════════════════════════════════════════════════════════════
        //  TAB 3: POPULATION PYRAMID — clickable
        // ═══════════════════════════════════════════════════════════════
        function initPopulationPyramid() {
            const cols = tab3Data.ranks.map((_, i) => `hsla(${142 + i * 8},72%,${38 + i * 5}%,.80)`);
            chartInstances['population'] = new Chart(document.getElementById('populationPyramid'), {
                type: 'bar',
                data: {
                    labels: tab3Data.ranks, datasets: [{
                        label: 'Officers', data: tab3Data.totals, backgroundColor: cols,
                        borderColor: "rgba(255,255,255,.3)", borderWidth: 1, borderRadius: 6,
                        barPercentage: .92, categoryPercentage: .85
                    }]
                },
                options: {
                    ...baseOptions, indexAxis: 'y',
                    onClick: (evt, els) => {
                        if (!els.length) return;
                        const rank = tab3Data.ranks[els[0].index];
                        fetchPopulationOfficers(rank);
                    },
                    plugins: {
                        ...baseOptions.plugins,
                        legend: { display: false },
                        datalabels: {
                            display: true, color: '#1a2e22', font: { weight: '800', size: 12 },
                            anchor: 'end', align: 'right', offset: 8,
                            formatter: v => v.toLocaleString()
                        },
                        tooltip: {
                            ...baseOptions.plugins.tooltip, callbacks: {
                                label: ctx => {
                                    const t = tab3Data.totals.reduce((s, v) => s + v, 0);
                                    return ` ${ctx.raw.toLocaleString()} officers (${t > 0 ? ((ctx.raw / t) * 100).toFixed(1) : 0}%) — click to view list`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { beginAtZero: true, grid: { color: gridColor } },
                        y: { grid: { display: false }, ticks: { color: "#1a2e22", font: { weight: "800", size: 13 } } }
                    }
                }
            });
        }

        // ═══════════════════════════════════════════════════════════════
        //  TAB 4: TENURE — scrollable, always-on labels, clickable
        // ═══════════════════════════════════════════════════════════════
        function initTenureChart() { updateTenureChart(); }

        function updateTenureChart() {
            if (chartInstances['tenure']) chartInstances['tenure'].destroy();

            const mode = document.getElementById('tenureViewMode').value;
            const isExc = mode === 'exceeded';
            const isNot = mode === 'not_exceeded';
            const isAvg = mode === 'average';

            const labelCount = tab4Data.ranks.length;
            setScrollableCanvasWidth('tenureScrollInner', labelCount);

            const ds = tab4Data.afposList.map((a, i) => ({
                label: a,
                data: tab4Data.ranks.map(r => {
                    if (isExc) return (tab4Data.tenured[r] && tab4Data.tenured[r][a]) || 0;
                    if (isNot) return (tab4Data.notTenured[r] && tab4Data.notTenured[r][a]) || 0;
                    return (tab4Data.averages[r] && tab4Data.averages[r][a]) || 0;
                }),
                backgroundColor: palette[i % palette.length],
                borderColor: "rgba(255,255,255,.15)", borderWidth: 1, borderRadius: 4, _afpos: a
            }));

            chartInstances['tenure'] = new Chart(document.getElementById('tenureChart'), {
                type: 'bar',
                data: { labels: tab4Data.ranks, datasets: ds },
                options: {
                    ...baseOptions,
                    onClick: (evt, els) => {
                        if (!els.length || isAvg) return;
                        const rank = tab4Data.ranks[els[0].index];
                        const afpos = ds[els[0].datasetIndex]._afpos;
                        fetchTenuredOfficers(rank, afpos, mode);
                    },
                    plugins: {
                        ...baseOptions.plugins,
                        legend: { ...baseOptions.plugins.legend, display: true, position: "bottom" },
                        datalabels: {
                            display: ctx => ctx.dataset.data[ctx.dataIndex] > 0,
                            color: '#0b0b0b', font: { weight: '700', size: 9 },
                            formatter: v => v > 0 ? (isAvg ? v.toFixed(1) : v) : '',
                            anchor: 'center', align: 'center', clamp: true
                        },
                        tooltip: {
                            ...baseOptions.plugins.tooltip, callbacks: {
                                label: ctx => {
                                    const r = tab4Data.ranks[ctx.dataIndex];
                                    const mx = tab4Data.maxTenure[r] || '?';
                                    if (isExc) return ` ${ctx.dataset.label}: ${ctx.raw} exceeded (max ${mx}yr)`;
                                    if (isNot) return ` ${ctx.dataset.label}: ${ctx.raw} within limit (max ${mx}yr)`;
                                    return ` ${ctx.dataset.label}: ${ctx.raw.toFixed(1)} yr avg (max ${mx}yr)`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: "#2d4a38", font: { weight: "700" } } },
                        y: {
                            beginAtZero: true, grid: { color: gridColor },
                            ticks: { color: "#6b8f76", callback: v => isAvg ? v + ' yr' : v },
                            title: { display: true, text: isExc ? 'Officers Exceeded' : isNot ? 'Officers Within Limit' : 'Average Tenure (Years)', color: '#6b8f76', font: { size: 11, weight: '600' } }
                        }
                    }
                }
            });
        }

        // ═══════════════════════════════════════════════════════════════
        //  TAB 5: AGE PER RANK — scrollable, always-on labels, clickable
        // ═══════════════════════════════════════════════════════════════
        function initAgeChart() {
            const s = document.getElementById('ageAfposFilter');
            (tab5Data.allAfposList || tab5Data.afposList).forEach(a => {
                const o = document.createElement('option'); o.value = a; o.textContent = a; s.appendChild(o);
            });
            updateAgeChart();
        }

        function updateAgeChart() {
            if (chartInstances['age']) chartInstances['age'].destroy();

            const f = document.getElementById('ageAfposFilter').value;
            const list = f === 'all' ? tab5Data.afposList.slice(0, 12) : [f];

            const labelCount = tab5Data.ranks.length;
            setScrollableCanvasWidth('ageScrollInner', labelCount);

            const ds = list.map((a, i) => ({
                label: a,
                data: tab5Data.ranks.map(r => {
                    const v = (tab5Data.averages[r] && tab5Data.averages[r][a]) || 0;
                    return v > 0 ? Math.floor(v) : 0;
                }),
                backgroundColor: palette[i % palette.length],
                borderColor: "rgba(255,255,255,.15)", borderWidth: 1, borderRadius: 4, _afpos: a
            }));

            chartInstances['age'] = new Chart(document.getElementById('ageChart'), {
                type: 'bar',
                data: { labels: tab5Data.ranks, datasets: ds },
                options: {
                    ...baseOptions,
                    onClick: (evt, els) => {
                        if (!els.length) return;
                        fetchAgeOfficers(tab5Data.ranks[els[0].index], ds[els[0].datasetIndex]._afpos);
                    },
                    plugins: {
                        ...baseOptions.plugins,
                        legend: { ...baseOptions.plugins.legend, display: true, position: "bottom" },
                        datalabels: {
                            display: ctx => ctx.dataset.data[ctx.dataIndex] > 0,
                            color: '#0b0b0b', font: { weight: '700', size: 9 },
                            formatter: v => v > 0 ? v : '',
                            anchor: 'center', align: 'center', clamp: true
                        },
                        tooltip: {
                            ...baseOptions.plugins.tooltip, callbacks: {
                                label: ctx => {
                                    const cnt = (tab5Data.counts[tab5Data.ranks[ctx.dataIndex]] && tab5Data.counts[tab5Data.ranks[ctx.dataIndex]][ctx.dataset._afpos]) || 0;
                                    return ` ${ctx.dataset.label}: avg ${ctx.raw} yrs (${cnt} officers)`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: "#2d4a38", font: { weight: "700" } } },
                        y: {
                            beginAtZero: false, grid: { color: gridColor },
                            ticks: { color: "#6b8f76", stepSize: 1, callback: v => Number.isInteger(v) ? v + ' yrs' : '' },
                            title: { display: true, text: 'Average Age (Years)', color: '#6b8f76', font: { size: 11, weight: '600' } }
                        }
                    }
                }
            });
        }

        // ═══════════════════════════════════════════════════════════════
        //  TAB 6: COMPANY COMMANDERS — scrollable, always-on labels, clickable
        // ═══════════════════════════════════════════════════════════════
        function initCCChart() { updateCCChart(); }

        function updateCCChart() {
            if (chartInstances['company-cdr']) chartInstances['company-cdr'].destroy();

            const mode = document.getElementById('ccViewMode').value;
            let labels, withD, withoutD;

            if (mode === 'rank') {
                labels = tab6Data.ranks;
                withD = labels.map(r => { let s = 0; Object.values(tab6Data.data[r] || {}).forEach(v => s += v.with || 0); return s; });
                withoutD = labels.map(r => { let s = 0; Object.values(tab6Data.data[r] || {}).forEach(v => s += v.without || 0); return s; });
            } else {
                labels = tab6Data.afposList;
                withD = labels.map(a => { let s = 0; tab6Data.ranks.forEach(r => { s += (tab6Data.data[r]?.[a]?.with || 0); }); return s; });
                withoutD = labels.map(a => { let s = 0; tab6Data.ranks.forEach(r => { s += (tab6Data.data[r]?.[a]?.without || 0); }); return s; });
            }

            setScrollableCanvasWidth('ccScrollInner', labels.length);

            const datasets = [
                { label: 'With OAC', data: withD, backgroundColor: 'rgba(22,163,74,.75)', borderRadius: 4, borderWidth: 0 },
                { label: 'Without OAC', data: withoutD, backgroundColor: 'rgba(217,119,6,.70)', borderRadius: 4, borderWidth: 0 }
            ];

            chartInstances['company-cdr'] = new Chart(document.getElementById('ccChart'), {
                type: 'bar',
                data: { labels, datasets },
                options: {
                    ...baseOptions,
                    onClick: (evt, els) => {
                        if (!els.length) return;
                        const idx = els[0].index;
                        const dsIdx = els[0].datasetIndex;
                        const lbl = labels[idx];
                        const type = dsIdx === 0 ? 'with' : 'without';
                        const rank = mode === 'rank' ? lbl : '';
                        const afpos = mode === 'afpos' ? lbl : '';
                        fetchCCOfficers(`${lbl} — ${type === 'with' ? 'With OAC' : 'Without OAC'}`, rank, afpos, type);
                    },
                    plugins: {
                        ...baseOptions.plugins,
                        legend: { ...baseOptions.plugins.legend, display: true, position: "bottom" },
                        datalabels: {
                            display: ctx => ctx.dataset.data[ctx.dataIndex] > 0,
                            color: '#090909', font: { weight: '700', size: 10 },
                            formatter: v => v > 0 ? v : '',
                            anchor: 'center', align: 'center', clamp: true
                        }
                    },
                    scales: {
                        x: { stacked: true, grid: { display: false }, ticks: { color: "#2d4a38", font: { weight: "700", size: mode === 'afpos' ? 9 : 11 } } },
                        y: { stacked: true, beginAtZero: true, grid: { color: gridColor } }
                    }
                }
            });
        }

        // ═══════════════════════════════════════════════════════════════
        //  TAB 7: CGSC — scrollable, always-on labels, clickable
        // ═══════════════════════════════════════════════════════════════
        function initCGSCChart() { updateCGSCChart(); }

        function updateCGSCChart() {
            if (chartInstances['cgsc']) chartInstances['cgsc'].destroy();

            const mode = document.getElementById('cgscViewMode').value;
            let labels, curD, notD;

            if (mode === 'rank') {
                labels = tab7Data.ranks;
                curD = labels.map(r => { let s = 0; Object.values(tab7Data.data[r] || {}).forEach(v => s += v.current || 0); return s; });
                notD = labels.map(r => { let s = 0; Object.values(tab7Data.data[r] || {}).forEach(v => s += v.not_designated || 0); return s; });
            } else {
                labels = tab7Data.afposList;
                curD = labels.map(a => { let s = 0; tab7Data.ranks.forEach(r => { s += (tab7Data.data[r]?.[a]?.current || 0); }); return s; });
                notD = labels.map(a => { let s = 0; tab7Data.ranks.forEach(r => { s += (tab7Data.data[r]?.[a]?.not_designated || 0); }); return s; });
            }

            setScrollableCanvasWidth('cgscScrollInner', labels.length);

            const datasets = [
                { label: 'Current Bn Commander', data: curD, backgroundColor: 'rgba(37,99,235,.75)', borderRadius: 4, borderWidth: 0 },
                { label: 'Not Yet Designated', data: notD, backgroundColor: 'rgba(124,58,237,.65)', borderRadius: 4, borderWidth: 0 }
            ];

            chartInstances['cgsc'] = new Chart(document.getElementById('cgscChart'), {
                type: 'bar',
                data: { labels, datasets },
                options: {
                    ...baseOptions,
                    onClick: (evt, els) => {
                        if (!els.length) return;
                        const idx = els[0].index;
                        const dsIdx = els[0].datasetIndex;
                        const lbl = labels[idx];
                        const type = dsIdx === 0 ? 'current' : 'not_designated';
                        const rank = mode === 'rank' ? lbl : '';
                        const afpos = mode === 'afpos' ? lbl : '';
                        fetchCGSCOfficers(`${lbl} — ${dsIdx === 0 ? 'Current Bn Cdr' : 'Not Yet Designated'}`, rank, afpos, type);
                    },
                    plugins: {
                        ...baseOptions.plugins,
                        legend: { ...baseOptions.plugins.legend, display: true, position: "bottom" },
                        datalabels: {
                            display: ctx => ctx.dataset.data[ctx.dataIndex] > 0,
                            color: '#090909', font: { weight: '700', size: 10 },
                            formatter: v => v > 0 ? v : '',
                            anchor: 'center', align: 'center', clamp: true
                        }
                    },
                    scales: {
                        x: { stacked: true, grid: { display: false }, ticks: { color: "#2d4a38", font: { weight: "700", size: mode === 'afpos' ? 9 : 11 } } },
                        y: { stacked: true, beginAtZero: true, grid: { color: gridColor } }
                    }
                }
            });
        }

        // ═══════════════════════════════════════════════════════════════
        //  AJAX fetch functions
        // ═══════════════════════════════════════════════════════════════

        // 1) Retiring officers
        function fetchRetiringOfficers() {
            openDrill('Compulsory Retirement — RA 11939 Sec 6 — Retiring Within 12 Months', loadingHtml);
            fetch(`{{ route("dashboard.retiring-officers") }}`)
                .then(r => r.json()).then(d => {
                    if (!d.officers || !d.officers.length) { document.getElementById('drillBody').innerHTML = emptyHtml('No officers retiring within the next 12 months.'); return; }
                    const headers = ['#', 'PM Code', 'Name', 'Rank', 'AFPOS', 'DOB', 'Age', 'Retirement Date (RET)'];
                    const rows = [];
                    let h = `<div style="padding:10px 14px;font-size:11px;color:var(--muted);border-bottom:1px solid var(--border-2)"><strong>RA 11939 Sec 6(a):</strong> Officers O-1 to O-9 shall be compulsorily retired upon reaching age 57 or 30 years active duty, whichever comes later.</div>`;
                    h += `<table class="drill-table"><thead><tr><th>#</th><th>PM Code</th><th>Name</th><th>Rank</th><th>AFPOS</th><th>DOB</th><th>Age</th><th>Retirement Date (RET)</th></tr></thead><tbody>`;
                    d.officers.forEach((o, i) => {
                        h += `<tr><td>${i + 1}</td><td>${o.pm_code}</td><td>${o.name || ''}</td><td><span class="badge-rank">${o.rank}</span></td><td>${o.afpos || ''}</td><td>${fmtDob(o.dob)}</td><td style="font-weight:700">${o.age}</td><td style="font-weight:800;color:var(--bad)">${fmtDob(o.retirement_date || '')}</td></tr>`;
                        rows.push([i + 1, o.pm_code, o.name || '', o.rank, o.afpos || '', fmtDob(o.dob), o.age, fmtDob(o.retirement_date || '')]);
                    });
                    h += `</tbody></table>`;
                    document.getElementById('drillBody').innerHTML = h;
                    setDrillData(headers, rows);
                }).catch(() => { document.getElementById('drillBody').innerHTML = emptyHtml('Error loading data.'); });
        }

        // 2) Assigned / Unassigned officers
        function fetchAssignedOfficers(type) {
            const label = type === 'assigned' ? 'Assigned Officers' : 'Unassigned Officers';
            openDrill(label, loadingHtml);
            fetch(`{{ route("dashboard.assigned-officers") }}?type=${type}`)
                .then(r => r.json()).then(d => {
                    if (!d.officers || !d.officers.length) { document.getElementById('drillBody').innerHTML = emptyHtml('No officers found.'); return; }
                    const headers = ['#', 'PM Code', 'Name', 'Rank', 'AFPOS', 'DOB', 'Age'];
                    const rows = [];
                    let h = `<table class="drill-table"><thead><tr><th>#</th><th>PM Code</th><th>Name</th><th>Rank</th><th>AFPOS</th><th>DOB</th><th>Age</th></tr></thead><tbody>`;
                    d.officers.forEach((o, i) => {
                        h += `<tr><td>${i + 1}</td><td>${o.pm_code}</td><td>${o.name || ''}</td><td><span class="badge-rank">${o.rank}</span></td><td>${o.afpos || ''}</td><td>${fmtDob(o.dob)}</td><td style="font-weight:800">${o.age}</td></tr>`;
                        rows.push([i + 1, o.pm_code, o.name || '', o.rank, o.afpos || '', fmtDob(o.dob), o.age]);
                    });
                    h += `</tbody></table>`;
                    document.getElementById('drillBody').innerHTML = h;
                    setDrillData(headers, rows);
                }).catch(() => { document.getElementById('drillBody').innerHTML = emptyHtml('Error loading data.'); });
        }

        // 3) Population pyramid officers
        function fetchPopulationOfficers(rank) {
            openDrill(`Officers — Rank: ${rank}`, loadingHtml);
            fetch(`{{ route("dashboard.population-officers") }}?rank=${encodeURIComponent(rank)}`)
                .then(r => r.json()).then(d => {
                    if (!d.officers || !d.officers.length) { document.getElementById('drillBody').innerHTML = emptyHtml('No officers found.'); return; }
                    const headers = ['#', 'PM Code', 'Name', 'Rank', 'AFPOS', 'DOB', 'Age'];
                    const rows = [];
                    let h = `<table class="drill-table"><thead><tr><th>#</th><th>PM Code</th><th>Name</th><th>Rank</th><th>AFPOS</th><th>DOB</th><th>Age</th></tr></thead><tbody>`;
                    d.officers.forEach((o, i) => {
                        h += `<tr><td>${i + 1}</td><td>${o.pm_code}</td><td>${o.name || ''}</td><td><span class="badge-rank">${o.rank}</span></td><td>${o.afpos || ''}</td><td>${fmtDob(o.dob)}</td><td style="font-weight:800">${o.age}</td></tr>`;
                        rows.push([i + 1, o.pm_code, o.name || '', o.rank, o.afpos || '', fmtDob(o.dob), o.age]);
                    });
                    h += `</tbody></table>`;
                    document.getElementById('drillBody').innerHTML = h;
                    setDrillData(headers, rows);
                }).catch(() => { document.getElementById('drillBody').innerHTML = emptyHtml('Error loading data.'); });
        }

        // 4) AFPOS per rank officers
        function fetchAfposRankOfficers(rank, afpos) {
            openDrill(`Officers — Rank: ${rank}${afpos ? ' / AFPOS: ' + afpos : ''}`, loadingHtml);
            fetch(`{{ route("dashboard.afpos-rank-officers") }}?rank=${encodeURIComponent(rank)}&afpos=${encodeURIComponent(afpos || '')}`)
                .then(r => r.json()).then(d => {
                    if (!d.officers || !d.officers.length) { document.getElementById('drillBody').innerHTML = emptyHtml('No officers found.'); return; }
                    const headers = ['#', 'PM Code', 'Name', 'Rank', 'AFPOS', 'DOB', 'Age'];
                    const rows = [];
                    let h = `<table class="drill-table"><thead><tr><th>#</th><th>PM Code</th><th>Name</th><th>Rank</th><th>AFPOS</th><th>DOB</th><th>Age</th></tr></thead><tbody>`;
                    d.officers.forEach((o, i) => {
                        h += `<tr><td>${i + 1}</td><td>${o.pm_code}</td><td>${o.name || ''}</td><td><span class="badge-rank">${o.rank}</span></td><td>${o.afpos || ''}</td><td>${fmtDob(o.dob)}</td><td style="font-weight:800">${o.age}</td></tr>`;
                        rows.push([i + 1, o.pm_code, o.name || '', o.rank, o.afpos || '', fmtDob(o.dob), o.age]);
                    });
                    h += `</tbody></table>`;
                    document.getElementById('drillBody').innerHTML = h;
                    setDrillData(headers, rows);
                }).catch(() => { document.getElementById('drillBody').innerHTML = emptyHtml('Error loading data.'); });
        }

        // 5) Tenured officers
        function fetchTenuredOfficers(rank, afpos, mode) {
            const modeLabel = mode === 'exceeded' ? 'Exceeded' : 'Not Exceeded';
            openDrill(`${modeLabel} Max Tenure — ${rank}${afpos ? ' / ' + afpos : ''}`, loadingHtml);
            fetch(`{{ route("dashboard.tenured-officers") }}?rank=${encodeURIComponent(rank)}&afpos=${encodeURIComponent(afpos || '')}&mode=${mode}`)
                .then(r => r.json()).then(d => {
                    if (!d.officers || !d.officers.length) { document.getElementById('drillBody').innerHTML = emptyHtml('No officers found.'); return; }
                    const headers = ['#', 'PM Code', 'Name', 'Rank', 'AFPOS', 'DOR', 'Tenure (yr)', 'Max (yr)'];
                    const rows = [];
                    let h = `<table class="drill-table"><thead><tr><th>#</th><th>PM Code</th><th>Name</th><th>Rank</th><th>AFPOS</th><th>DOR</th><th>Tenure (yr)</th><th>Max (yr)</th></tr></thead><tbody>`;
                    d.officers.forEach((o, i) => {
                        const color = d.mode === 'exceeded' ? 'var(--bad)' : 'var(--good)';
                        h += `<tr><td>${i + 1}</td><td>${o.pm_code}</td><td>${o.name || ''}</td><td><span class="badge-rank">${o.rank}</span></td><td>${o.afpos}</td><td>${o.dor || ''}</td><td style="font-weight:800;color:${color}">${o.tenure_years}</td><td>${d.maxTenure}</td></tr>`;
                        rows.push([i + 1, o.pm_code, o.name || '', o.rank, o.afpos, o.dor || '', o.tenure_years, d.maxTenure]);
                    });
                    h += '</tbody></table>';
                    document.getElementById('drillBody').innerHTML = h;
                    setDrillData(headers, rows);
                }).catch(() => { document.getElementById('drillBody').innerHTML = emptyHtml('Error loading data.'); });
        }

        // 6) Age officers
        function fetchAgeOfficers(rank, afpos) {
            openDrill(`Officers by Age — ${rank}${afpos ? ' / ' + afpos : ''}`, loadingHtml);
            fetch(`{{ route("dashboard.age-officers") }}?rank=${encodeURIComponent(rank)}&afpos=${encodeURIComponent(afpos || '')}`)
                .then(r => r.json()).then(d => {
                    if (!d.officers || !d.officers.length) { document.getElementById('drillBody').innerHTML = emptyHtml('No officers found.'); return; }
                    const headers = ['#', 'PM Code', 'Name', 'Rank', 'AFPOS', 'DOB', 'Age'];
                    const rows = [];
                    let h = `<table class="drill-table"><thead><tr><th>#</th><th>PM Code</th><th>Name</th><th>Rank</th><th>AFPOS</th><th>DOB</th><th>Age</th></tr></thead><tbody>`;
                    d.officers.forEach((o, i) => {
                        h += `<tr><td>${i + 1}</td><td>${o.pm_code}</td><td>${o.name || ''}</td><td><span class="badge-rank">${o.rank}</span></td><td>${o.afpos}</td><td>${fmtDob(o.dob)}</td><td style="font-weight:800">${o.age}</td></tr>`;
                        rows.push([i + 1, o.pm_code, o.name || '', o.rank, o.afpos, fmtDob(o.dob), o.age]);
                    });
                    h += '</tbody></table>';
                    document.getElementById('drillBody').innerHTML = h;
                    setDrillData(headers, rows);
                }).catch(() => { document.getElementById('drillBody').innerHTML = emptyHtml('Error loading data.'); });
        }

        // 7) CC officers
        function fetchCCOfficers(label, rank, afpos, type) {
            openDrill(`Company Commanders — ${label}`, loadingHtml);
            let url = `{{ route("dashboard.cc-officers") }}?type=${type}`;
            if (rank) url += `&rank=${encodeURIComponent(rank)}`;
            if (afpos) url += `&afpos=${encodeURIComponent(afpos)}`;
            fetch(url).then(r => r.json()).then(d => {
                if (!d.officers || !d.officers.length) { document.getElementById('drillBody').innerHTML = emptyHtml('No officers found.'); return; }
                const headers = ['#', 'PM Code', 'Name', 'Rank', 'AFPOS', 'OAC Status'];
                const rows = [];
                let h = `<table class="drill-table"><thead><tr><th>#</th><th>PM Code</th><th>Name</th><th>Rank</th><th>AFPOS</th><th>OAC Status</th></tr></thead><tbody>`;
                d.officers.forEach((o, i) => {
                    const oacLabel = o.has_oac ? 'With OAC' : 'Without OAC';
                    h += `<tr><td>${i + 1}</td><td>${o.pm_code}</td><td>${o.name || ''}</td><td><span class="badge-rank">${o.rank}</span></td><td>${o.afpos || ''}</td><td style="font-weight:700;color:${o.has_oac ? 'var(--good)' : 'var(--warn)'}">${oacLabel}</td></tr>`;
                    rows.push([i + 1, o.pm_code, o.name || '', o.rank, o.afpos || '', oacLabel]);
                });
                h += '</tbody></table>';
                document.getElementById('drillBody').innerHTML = h;
                setDrillData(headers, rows);
            }).catch(() => { document.getElementById('drillBody').innerHTML = emptyHtml('Error loading data.'); });
        }

        // 8) CGSC officers
        function fetchCGSCOfficers(label, rank, afpos, type) {
            openDrill(`CGSC Graduates — ${label}`, loadingHtml);
            let url = `{{ route("dashboard.cgsc-officers") }}?type=${type}`;
            if (rank) url += `&rank=${encodeURIComponent(rank)}`;
            if (afpos) url += `&afpos=${encodeURIComponent(afpos)}`;
            fetch(url).then(r => r.json()).then(d => {
                if (!d.officers || !d.officers.length) { document.getElementById('drillBody').innerHTML = emptyHtml('No officers found.'); return; }
                const headers = ['#', 'PM Code', 'Name', 'Rank', 'AFPOS', 'Status'];
                const rows = [];
                let h = `<table class="drill-table"><thead><tr><th>#</th><th>PM Code</th><th>Name</th><th>Rank</th><th>AFPOS</th><th>Status</th></tr></thead><tbody>`;
                d.officers.forEach((o, i) => {
                    const statusLabel = o.is_current ? 'Current Bn Cdr' : 'Not Yet Designated';
                    h += `<tr><td>${i + 1}</td><td>${o.pm_code}</td><td>${o.name || ''}</td><td><span class="badge-rank">${o.rank}</span></td><td>${o.afpos || ''}</td><td style="font-weight:700;color:${o.is_current ? 'var(--blue)' : 'var(--violet)'}">${statusLabel}</td></tr>`;
                    rows.push([i + 1, o.pm_code, o.name || '', o.rank, o.afpos || '', statusLabel]);
                });
                h += '</tbody></table>';
                document.getElementById('drillBody').innerHTML = h;
                setDrillData(headers, rows);
            }).catch(() => { document.getElementById('drillBody').innerHTML = emptyHtml('Error loading data.'); });
        }

        // 9) Gender officers
        function fetchGenderOfficers(sex) {
            const label = sex === 'male' ? 'Male Officers' : 'Female Officers';
            openDrill(label, loadingHtml);
            fetch(`{{ route("dashboard.gender-officers") }}?sex=${sex}`)
                .then(r => r.json()).then(d => {
                    if (!d.officers || !d.officers.length) { document.getElementById('drillBody').innerHTML = emptyHtml('No officers found.'); return; }
                    const headers = ['#', 'PM Code', 'Name', 'Rank', 'AFPOS', 'DOB', 'Age'];
                    const rows = [];
                    let h = `<table class="drill-table"><thead><tr><th>#</th><th>PM Code</th><th>Name</th><th>Rank</th><th>AFPOS</th><th>DOB</th><th>Age</th></tr></thead><tbody>`;
                    d.officers.forEach((o, i) => {
                        h += `<tr><td>${i + 1}</td><td>${o.pm_code}</td><td>${o.name || ''}</td><td><span class="badge-rank">${o.rank}</span></td><td>${o.afpos || ''}</td><td>${fmtDob(o.dob)}</td><td style="font-weight:800">${o.age}</td></tr>`;
                        rows.push([i + 1, o.pm_code, o.name || '', o.rank, o.afpos || '', fmtDob(o.dob), o.age]);
                    });
                    h += `</tbody></table>`;
                    document.getElementById('drillBody').innerHTML = h;
                    setDrillData(headers, rows);
                }).catch(() => { document.getElementById('drillBody').innerHTML = emptyHtml('Error loading data.'); });
        }
    </script>
@endsection