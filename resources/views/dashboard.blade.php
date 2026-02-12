@extends('layouts.app')
@section('content')

<style>
    :root{
        /* Brighter, high-contrast palette */
    --bg: #0a0f1c;           
    --panel: #121a2f;        
    --panel-2: #16213e;      
    --border: rgba(148,163,184,.18);
    --text: #f1f5f9;
    --muted: rgba(226,232,240,.80);

    /* Brighter accents */
    --accent: #3b82f6;       /* vibrant blue */
    --good:   #10b981;       /* emerald */
    --warn:   #f59e0b;       /* strong amber */
    --bad:    #ef4444;       /* strong red */
    --violet: #8b5cf6;       /* vivid violet */

    --shadow: 0 12px 28px rgba(0,0,0,.35);
    --radius: 14px;
    --pad: 12px;
    }

    body{
        background: radial-gradient(1200px 600px at 20% 0%, rgba(106,167,255,.10), transparent 60%),
                    radial-gradient(900px 500px at 85% 10%, rgba(183,156,255,.10), transparent 55%),
                    var(--bg);
        color: var(--text);
        font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
        overflow: hidden; /* ✅ no page scroll */
    }

    /* Container that fits the viewport */
    .dash-shell{
        height: calc(100vh - 84px); /* adjust if your navbar is taller/shorter */
        padding: 10px 12px;
        overflow: hidden; /* ✅ no scroll */
    }

    .dash-topbar{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap: 10px;
        padding: 10px 12px;
        background: linear-gradient(135deg, rgba(106,167,255,.14), rgba(183,156,255,.10));
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
    }
    .dash-title{
        display:flex;
        flex-direction:column;
        gap:2px;
        line-height:1.1;
    }
    .dash-title h1{
        font-size: 14px;
        margin:0;
        letter-spacing: .2px;
        font-weight: 800;
        font-size: larger;
        color: black;
    }
    .dash-title p{
        margin:0;
        font-size: 12px;
        color: var(--muted);
    }

    .insight-pill{
        display:flex;
        gap:8px;
        align-items:center;
        font-size: 12px;
        color: var(--muted);
        padding: 8px 10px;
        border: 1px solid var(--border);
        border-radius: 999px;
        background: rgba(15,26,43,.65);
        white-space: nowrap;
    }
    .dot{
        width:8px; height:8px; border-radius:999px;
        background: var(--accent);
        box-shadow: 0 0 0 4px rgba(106,167,255,.12);
    }

    /* Layout grid: KPIs + charts + compact table (no scroll) */
    .dash-grid{
        margin-top: 10px;
        height: calc(100% - 62px);
        display:grid;
        grid-template-columns: 420px 1fr;
        grid-template-rows: auto 1fr;
        gap: 10px;
        overflow:hidden;
    }

    .kpi-grid{
        display:grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        align-content:start;
    }

    .card{
        background: rgba(15,26,43,.92);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow:hidden;
    }

    .kpi{
        padding: 10px 12px;
        display:flex;
        justify-content:space-between;
        gap:10px;
        min-height: 74px;
    }
    .kpi .meta{
        display:flex;
        flex-direction:column;
        gap:4px;
    }
    .kpi .label{
        font-size: 11px;
        color: var(--muted);
        letter-spacing:.08em;
        text-transform:uppercase;
        font-weight:700;
    }
    .kpi .value{
        font-size: 32px;
    font-weight: 900;
    letter-spacing:-.02em;
    color: #ffffff;
    text-shadow: 0 0 8px rgba(59,130,246,.25);
    }
    .kpi .sub{
        font-size: 12px;
        color: var(--muted);
    }
    /* .kpi .icon{
        width: 34px; height: 34px;
        border-radius: 10px;
        display:grid;
        place-items:center;
        background: rgba(106,167,255,.12);
        border: 1px solid rgba(106,167,255,.18);
        color: var(--accent);
        flex: 0 0 auto;
        font-size: 14px;
    }
    .icon.good{ background: rgba(63,191,154,.10); border-color: rgba(63,191,154,.18); color: var(--good);}
    .icon.warn{ background: rgba(240,179,107,.10); border-color: rgba(240,179,107,.18); color: var(--warn);}
    .icon.bad{  background: rgba(255,123,123,.10); border-color: rgba(255,123,123,.18); color: var(--bad);}
    .icon.violet{ background: rgba(183,156,255,.10); border-color: rgba(183,156,255,.18); color: var(--violet);} */

    .icon{
    width: 36px; 
    height: 36px;
    border-radius: 12px;
    display:grid;
    place-items:center;
    background: rgba(59,130,246,.15);
    border: 1px solid rgba(59,130,246,.35);
    color: var(--accent);
    font-size: 15px;
    box-shadow: 0 0 12px rgba(59,130,246,.25);
}
.icon.good{ 
    background: rgba(16,185,129,.15); 
    border-color: rgba(16,185,129,.35); 
    color: var(--good);
    box-shadow: 0 0 12px rgba(16,185,129,.25);
}
.icon.warn{ 
    background: rgba(245,158,11,.15); 
    border-color: rgba(245,158,11,.35); 
    color: var(--warn);
}
.icon.bad{  
    background: rgba(239,68,68,.15); 
    border-color: rgba(239,68,68,.35); 
    color: var(--bad);
}
.icon.violet{ 
    background: rgba(139,92,246,.15); 
    border-color: rgba(139,92,246,.35); 
    color: var(--violet);
}


    /* Charts zone */
    .charts{
        display:grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 10px;
        overflow:hidden;
    }

    .card-hd{
        padding: 10px 12px;
        background: rgba(13,23,40,.9);
        border-bottom: 1px solid var(--border);
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap: 10px;
    }
    .card-hd h6{
        margin:0;
        font-size: 12px;
        letter-spacing:.08em;
        text-transform:uppercase;
        color: rgba(226,232,240,.85);
        font-weight: 900;
        display:flex;
        gap:8px;
        align-items:center;
    }

    .card-bd{
        padding: 10px 12px;
        height: calc(100% - 44px);
        overflow:hidden;
    }

    .chart-wrap{
        height: 100%;
        width: 100%;
        position: relative;
    }

    /* Compact table (no internal scrolling) */
    .mini-table{
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .mini-table thead th{
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: rgba(226,232,240,.8);
        padding: 8px 10px;
        border-bottom: 1px solid var(--border);
    }
    .mini-table td{
        padding: 8px 10px;
        border-bottom: 1px solid rgba(148,163,184,.10);
        vertical-align: top;
        color: rgba(226,232,240,.86);
    }

    .badge-rank{
        display:inline-block;
        padding: 3px 8px;
        border-radius: 999px;
        border: 1px solid rgba(106,167,255,.18);
        background: rgba(106,167,255,.10);
        color: rgba(226,232,240,.92);
        font-weight: 800;
        font-size: 11px;
    }

    .afpos-chip{
        display:inline-block;
        padding: 3px 6px;
        margin: 0 6px 6px 0;
        border-radius: 8px;
        border: 1px solid rgba(148,163,184,.14);
        background: rgba(2,6,23,.25);
        color: rgba(226,232,240,.85);
        font-size: 11px;
        white-space: nowrap;
    }

    .muted{
        color: var(--muted);
    }

    /* Responsive */
    @media (max-width: 1200px){
        body{ overflow:auto; } /* fallback on small screens */
        .dash-shell{ height: auto; overflow: visible; }
        .dash-grid{ grid-template-columns: 1fr; grid-template-rows: auto auto; height:auto; }
        .charts{ grid-template-columns: 1fr; grid-template-rows: auto; }
        .card-bd{ height: 260px; }
    }
</style>

<div class="dash-shell container-fluid">
    <div class="dash-topbar">
        <div class="dash-title">
            <h1><i class="fas fa-chart-line"></i> Officer Analytics</h1>
            <!-- <p>Actionable overview — assignment gaps, rank distribution, AFPOS mix</p> -->
        </div>

        <!-- <div class="insight-pill">
            <span class="dot"></span>
            <span>
                Top Rank:
                <strong class="text-light">{{ $stats['top_rank'] ?? '—' }}</strong>
                <span class="muted">({{ number_format($stats['top_rank_total'] ?? 0) }})</span>
                &nbsp;•&nbsp;
                Top AFPOS:
                <strong class="text-light">{{ $stats['top_afpos'] ?? '—' }}</strong>
                <span class="muted">({{ number_format($stats['top_afpos_total'] ?? 0) }})</span>
            </span>
        </div> -->
    </div>

    <div class="dash-grid">
        {{-- Left: KPIs --}}
        <div class="kpi-grid">
            <div class="card kpi">
                <div class="meta">
                    <div class="label">Total Officers</div>
                    <div class="value">{{ number_format($stats['total_officers']) }}</div>
                    <div class="sub">Active personnel</div>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>

            <div class="card kpi">
                <div class="meta">
                    <div class="label">Retiring (12 months)</div>
                    <div class="value">{{ number_format($stats['retiring_12_months'] ?? 0) }}</div>
                    <div class="sub">From today</div>
                </div>
                <div class="icon warn"><i class="fas fa-hourglass-half"></i></div>
            </div>


            <!-- <div class="card kpi">
                <div class="meta">
                    <div class="label">AFPOS (Unique)</div>
                    <div class="value">{{ number_format($stats['total_afpos']) }}</div>
                    <div class="sub">Distinct positions</div>
                </div>
                <div class="icon violet"><i class="fas fa-briefcase"></i></div>
            </div> -->

            <div class="card kpi">
                <div class="meta">
                    <div class="label">Assigned</div>
                    <div class="value">{{ number_format($stats['assigned_officers']) }}</div>
                    <div class="sub">{{ $stats['assigned_rate'] }}% coverage</div>
                </div>
                <div class="icon good"><i class="fas fa-user-check"></i></div>
            </div>

            <div class="card kpi">
                <div class="meta">
                    <div class="label">Unassigned</div>
                    <div class="value">{{ number_format($stats['unassigned_officers']) }}</div>
                    <div class="sub">Needs review</div>
                </div>
                <div class="icon warn"><i class="fas fa-user-times"></i></div>
            </div>

            <div class="card kpi">
                <div class="meta">
                    <div class="label">Male</div>
                    <div class="value">{{ number_format($stats['male_officers']) }}</div>
                    <div class="sub">
                        {{ $stats['total_officers'] > 0 ? number_format(($stats['male_officers'] / $stats['total_officers']) * 100, 1) : 0 }}%
                    </div>
                </div>
                <div class="icon"><i class="fas fa-male"></i></div>
            </div>

            <div class="card kpi">
                <div class="meta">
                    <div class="label">Female</div>
                    <div class="value">{{ number_format($stats['female_officers']) }}</div>
                    <div class="sub">
                        {{ $stats['total_officers'] > 0 ? number_format(($stats['female_officers'] / $stats['total_officers']) * 100, 1) : 0 }}%
                    </div>
                </div>
                <div class="icon bad"><i class="fas fa-female"></i></div>
            </div>
        </div>

        {{-- Right: Charts + compact table --}}
        <div class="charts">
            <div class="card">
                <div class="card-hd">
                    <h6><i class="fas fa-filter" style="color: var(--accent)"></i> Rank Distribution</h6>
                </div>
                <div class="card-bd">
                    <div class="chart-wrap"><canvas id="rankChart"></canvas></div>
                </div>
            </div>

            <div class="card" style="height: 320px !important">
                <div class="card-hd">
                    <h6><i class="fas fa-layer-group" style="color: var(--violet);"></i> AFPOS Per rank</h6>
                </div>
                <div class="card-bd">
                    <div class="chart-wrap"><canvas id="afposChart"></canvas></div>
                </div>
            </div>

            <div class="card" style="height: 370px !important">
                <div class="card-hd">
                    <h6><i class="fas fa-table" style="color: var(--good)"></i> Tally of AFPOS</h6>
                </div>
                <div class="card-bd" style="padding:0;">
                    <table class="mini-table">
                        <thead>
                        <tr>
                            <th style="width: 80px;">Rank</th>
                            <th>AFPOS Breakdown</th>
                            <th style="width: 70px; text-align:right;">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($chartData['ranks'] as $index => $rank)
                            @php
                                $afposData = $chartData['afpos'][$rank] ?? collect([]);
                                $total = $chartData['total'][$index] ?? 0;
                            @endphp
                            <tr>
                                <td><span class="badge-rank">{{ $rank }}</span></td>
                                <td>
                                    @if($afposData->count() > 0)
                                        @foreach($afposData as $afpos)
                                            <span class="afpos-chip">{{ $afpos->AFPOS }}: {{ $afpos->count }}</span>
                                        @endforeach
                                    @else
                                        <span class="muted">No data</span>
                                    @endif
                                </td>
                                <td style="text-align:right; font-weight:900;">{{ $total }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-hd">
                    <h6><i class="fas fa-user-check" style="color: var(--good)"></i> Assigned vs Unassigned</h6>
                </div>
                <div class="card-bd">
                    <div class="chart-wrap"><canvas id="assignmentChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<script>
    Chart.register(ChartDataLabels);

    const chartData = @json($chartData);

    // Global styling (muted)
    Chart.defaults.font.family = "Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif";
    Chart.defaults.font.size = 10;
    Chart.defaults.color = "rgba(226,232,240,.78)";

    const gridColor = "rgba(148,163,184,.12)";
    const tipBg = "rgba(2,6,23,.92)";

    const colors = {
        male: "rgba(106,167,255,.85)",
        female: "rgba(255,123,123,.78)",
        assigned: "rgba(63,191,154,.78)",
        unassigned: "rgba(240,179,107,.78)",
        outline: "rgba(226,232,240,.10)"
    };

    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    usePointStyle: true,
                    pointStyle: "circle",
                    boxWidth: 8,
                    padding: 10,
                    font: { weight: "700", size: 10 }
                }
            },
            tooltip: {
                backgroundColor: tipBg,
                borderColor: "rgba(106,167,255,.25)",
                borderWidth: 1,
                titleColor: "rgba(255,255,255,.95)",
                bodyColor: "rgba(255,255,255,.90)",
                padding: 10,
                displayColors: true,
                boxPadding: 6,
            },
            datalabels: {
                display: false // default off; enable only where needed
            }
        },
        scales: {
            x: {
                grid: { color: gridColor },
                ticks: { color: "rgba(226,232,240,.70)" }
            },
            y: {
                grid: { color: gridColor },
                ticks: { color: "rgba(226,232,240,.70)" }
            }
        }
    };

    // 1) Rank Distribution (horizontal stacked bar: male + female)
    const totalOfficers = chartData.total.reduce((sum, v) => sum + v, 0);

    new Chart(document.getElementById("rankChart"), {
        type: "bar",
        data: {
            labels: chartData.ranks,
            datasets: [
                { label: "Male", data: chartData.male, backgroundColor: colors.male, borderWidth: 0 },
                { label: "Female", data: chartData.female, backgroundColor: colors.female, borderWidth: 0 },
            ]
        },
        options: {
            ...baseOptions,
            indexAxis: "y",
            scales: {
                x: { stacked: true, grid: { color: gridColor }, ticks: { color: "rgba(226,232,240,.70)" } },
                y: { stacked: true, grid: { display: false }, ticks: { color: "rgba(226,232,240,.82)", font: { weight: "800" } } }
            },
            plugins: {
                ...baseOptions.plugins,
                tooltip: {
                    ...baseOptions.plugins.tooltip,
                    callbacks: {
                        title: (ctx) => {
                            const i = ctx[0].dataIndex;
                            const total = chartData.total[i];
                            const pct = totalOfficers > 0 ? ((total / totalOfficers) * 100).toFixed(0) : 0;
                            return `${chartData.ranks[i]} • ${total} (${pct}%)`;
                        }
                    }
                },
                datalabels: {
                    display: true,
                    color: "rgba(255,255,255,.92)",
                    font: { weight: "900", size: 10 },
                    formatter: (value, ctx) => {
                        // only show total once (on Female dataset)
                        if (ctx.datasetIndex !== 1) return "";
                        const i = ctx.dataIndex;
                        return chartData.total[i] || "";
                    },
                    anchor: "end",
                    align: "right",
                    offset: 6
                }
            }
        }
    });

    // 2) Assigned vs Unassigned (vertical grouped bar)
    new Chart(document.getElementById("assignmentChart"), {
        type: "bar",
        data: {
            labels: chartData.ranks,
            datasets: [
                { label: "Assigned", data: chartData.assigned, backgroundColor: colors.assigned, borderWidth: 0, borderRadius: 6 },
                { label: "Unassigned", data: chartData.unassigned, backgroundColor: colors.unassigned, borderWidth: 0, borderRadius: 6 },
            ]
        },
        options: {
            ...baseOptions,
            scales: {
                x: { grid: { display: false }, ticks: { color: "rgba(226,232,240,.70)" } },
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: "rgba(226,232,240,.70)" } }
            }
        }
    });

    // 3) AFPOS Mix (stacked bar) - compact: only top AFPOS categories overall
    // Build top AFPOS list (by total across ranks)
    const totalsByAfpos = new Map();
    for (const rank of chartData.ranks) {
        const items = (chartData.afpos && chartData.afpos[rank]) ? chartData.afpos[rank] : [];
        for (const item of items) {
            const key = item.AFPOS;
            const current = totalsByAfpos.get(key) || 0;
            totalsByAfpos.set(key, current + Number(item.count || 0));
        }
    }

    const topAfpos = [...totalsByAfpos.entries()]
        .sort((a,b) => b[1] - a[1])
        .slice(0, 8)
        .map(([k]) => k);

    const palette = [
        "rgba(106,167,255,.75)",
        "rgba(183,156,255,.70)",
        "rgba(63,191,154,.70)",
        "rgba(240,179,107,.70)",
        "rgba(255,123,123,.65)",
        "rgba(94,234,212,.60)",
        "rgba(165,180,252,.65)",
        "rgba(251,191,36,.55)",
    ];

    const afposDatasets = topAfpos.map((afpos, idx) => ({
        label: afpos,
        data: chartData.ranks.map(rank => {
            const items = (chartData.afpos && chartData.afpos[rank]) ? chartData.afpos[rank] : [];
            const found = items.find(x => x.AFPOS === afpos);
            return found ? Number(found.count || 0) : 0;
        }),
        backgroundColor: palette[idx % palette.length],
        borderColor: "rgba(226,232,240,.08)",
        borderWidth: 1,
        borderRadius: 4
    }));

    new Chart(document.getElementById("afposChart"), {
        type: "bar",
        data: { labels: chartData.ranks, datasets: afposDatasets },
        options: {
            ...baseOptions,
            plugins: {
                ...baseOptions.plugins,
                legend: { display: true, position: "bottom" } // keeps top clean
            },
            scales: {
                x: { stacked: true, grid: { display: false }, ticks: { color: "rgba(226,232,240,.70)" } },
                y: { stacked: true, beginAtZero: true, grid: { color: gridColor }, ticks: { color: "rgba(226,232,240,.70)" } }
            }
        }
    });
</script>
@endsection
