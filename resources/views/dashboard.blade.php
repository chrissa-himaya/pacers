@extends('layouts.app')
@section('content')

<style>
    /* Professional Military Dashboard - Bright & Confident */
    .compact-card {
        margin-bottom: 0.5rem;
    }
    .compact-card .card-body {
        padding: 0.6rem;
    }
    .compact-card h6 {
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .compact-card h4 {
        font-size: 1.2rem;
        margin-bottom: 0;
        font-weight: 700;
    }
    .chart-container {
        position: relative;
        height: 280px;
    }
    .table-container {
        height: 280px;
        overflow-y: auto;
    }
    .compact-table {
        font-size: 0.8rem;
        margin-bottom: 0;
    }
    .compact-table td, .compact-table th {
        padding: 0.35rem;
    }
    .compact-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
    .card-header-compact {
        padding: 0.5rem 0.75rem;
        font-weight: 600;
    }
    .card-body-compact {
        padding: 0.6rem;
    }

    /* Bright Military Color Scheme - Professional but Confident */
    .bg-army-green {
        background-color: #4a7c59 !important; /* Army Green (Bright) */
    }
    .bg-air-force-blue {
        background-color: #5d8aa8 !important; /* Air Force Blue */
    }
    .bg-marine-red {
        background-color: #9b4f4f !important; /* Marine Corps Red */
    }
    .bg-navy-gold {
        background-color: #d4a373 !important; /* Navy Gold/Khaki */
    }
    .bg-military-khaki {
        background-color: #c3b091 !important; /* Military Khaki */
    }
    .bg-forest-green {
        background-color: #3d7c47 !important; /* Forest Green */
    }
    .bg-steel-blue {
        background-color: #4682b4 !important; /* Steel Blue */
    }
    .bg-bronze-brown {
        background-color: #8b6f47 !important; /* Bronze/Brown */
    }
    .bg-ranger-green {
        background-color: #5f7a61 !important; /* Ranger Green */
    }

    .badge-military {
        background-color: #5d8aa8 !important;
        color: #fff !important;
    }

    .table-dark {
        background-color: #3d7c47 !important;
        color: #fff !important;
    }
</style>

<div class="container-fluid px-3">
    <!-- Statistics Cards Row - Bright Military Colors -->
    <div class="row mb-2">
        <div class="col-md-2 col-sm-4 col-6 mb-1">
            <div class="card text-white bg-army-green compact-card">
                <div class="card-body text-center">
                    <h6 class="card-title">Total Officers</h6>
                    <h4 class="mb-0">{{ number_format($stats['total_officers']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6 mb-1">
            <div class="card text-white bg-air-force-blue compact-card">
                <div class="card-body text-center">
                    <h6 class="card-title">Male</h6>
                    <h4 class="mb-0">{{ number_format($stats['male_officers']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6 mb-1">
            <div class="card text-white bg-marine-red compact-card">
                <div class="card-body text-center">
                    <h6 class="card-title">Female</h6>
                    <h4 class="mb-0">{{ number_format($stats['female_officers']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-6 mb-1">
            <div class="card text-white bg-bronze-brown compact-card">
                <div class="card-body text-center">
                    <h6 class="card-title">Gender Ratio (M:F)</h6>
                    <h4 class="mb-0">
                        {{ $stats['female_officers'] > 0 ? number_format($stats['male_officers'] / $stats['female_officers'], 2) : 'N/A' }}:1
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-6 mb-1">
            <div class="card text-dark bg-military-khaki compact-card">
                <div class="card-body text-center">
                    <h6 class="card-title">AFPOS</h6>
                    <h4 class="mb-0">{{ number_format($stats['total_afpos']) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 1: Christmas Tree + AFPOS Stacked Chart -->
    <div class="row mb-2">
        <!-- Christmas Tree Chart -->
        <div class="col-md-6 mb-2">
            <div class="card h-100">
                <div class="card-header bg-forest-green text-white card-header-compact">
                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> OFFICERS DISTRIBUTION BY RANK</h6>
                </div>
                <div class="card-body card-body-compact">
                    <div class="chart-container">
                        <canvas id="pyramidChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- AFPOS Stacked Chart -->
        <div class="col-md-6 mb-2">
            <div class="card h-100">
                <div class="card-header bg-bronze-brown text-white card-header-compact">
                    <h6 class="mb-0"><i class="fas fa-chart-pie"></i> AFPOS DISTRIBUTION BY RANK</h6>
                </div>
                <div class="card-body card-body-compact">
                    <div class="chart-container">
                        <canvas id="afposChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: AFPOS Table + Gender Chart -->
    <div class="row">
        <!-- AFPOS Table -->
        <div class="col-md-6 mb-2">
            <div class="card h-100">
                <div class="card-header bg-ranger-green text-white card-header-compact">
                    <h6 class="mb-0"><i class="fas fa-list"></i> OFFICERS PER AFPOS BY RANK</h6>
                </div>
                <div class="card-body card-body-compact">
                    <div class="table-container">
                        <table class="table table-sm table-striped table-bordered table-hover compact-table">
                            <thead class="table-dark" style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th style="width: 70px;">Rank</th>
                                    <th>AFPOS Breakdown</th>
                                    <th style="width: 60px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($chartData['ranks'] as $index => $rank)
                                    @php
                                        $afposData = $chartData['afpos'][$rank] ?? collect([]);
                                        $total = isset($chartData['total'][$index]) ? $chartData['total'][$index] : 0;
                                    @endphp
                                    <tr>
                                        <td class="font-weight-bold text-center">{{ $rank }}</td>
                                        <td>
                                            @if($afposData->count() > 0)
                                                @foreach($afposData as $afpos)
                                                    <span class="badge badge-military compact-badge mr-1 mb-1">
                                                        {{ $afpos->AFPOS }}: {{ $afpos->count }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted small">No data</span>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold text-center">{{ $total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gender Chart -->
        <div class="col-md-6 mb-2">
            <div class="card h-100">
                <div class="card-header bg-steel-blue text-white card-header-compact">
                    <h6 class="mb-0"><i class="fas fa-venus-mars"></i> MALE VS FEMALE OFFICERS BY RANK</h6>
                </div>
                <div class="card-body card-body-compact">
                    <div class="chart-container">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Chart.js defaults
    Chart.defaults.font.family = "'Segoe UI', 'Roboto', 'Arial', sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#2d3748';

    // Data from Laravel
    const chartData = @json($chartData);

    // Bright Military Color Palettes - Confident & Professional
    const militaryPyramidColors = [
        'rgba(61, 124, 71, 0.85)',    // Forest Green (Army dress)
        'rgba(93, 138, 168, 0.85)',   // Air Force Blue
        'rgba(139, 111, 71, 0.85)',   // Bronze/Khaki
        'rgba(95, 122, 97, 0.85)',    // Ranger Green
        'rgba(70, 130, 180, 0.85)',   // Steel Blue
        'rgba(155, 79, 79, 0.85)',    // Marine Red
        'rgba(107, 142, 35, 0.85)',   // Olive Green
        'rgba(112, 128, 144, 0.85)',  // Slate Gray
    ];

    const militaryPyramidBorders = [
        'rgba(61, 124, 71, 1)',
        'rgba(93, 138, 168, 1)',
        'rgba(139, 111, 71, 1)',
        'rgba(95, 122, 97, 1)',
        'rgba(70, 130, 180, 1)',
        'rgba(155, 79, 79, 1)',
        'rgba(107, 142, 35, 1)',
        'rgba(112, 128, 144, 1)',
    ];

    // 1. Christmas Tree Chart (Population Pyramid) - BRIGHT MILITARY
    const pyramidCtx = document.getElementById('pyramidChart').getContext('2d');
    const pyramidChart = new Chart(pyramidCtx, {
        type: 'bar',
        data: {
            labels: chartData.ranks,
            datasets: [{
                label: 'Total Officers',
                data: chartData.total,
                backgroundColor: militaryPyramidColors,
                borderColor: militaryPyramidBorders,
                borderWidth: 2
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(45, 55, 72, 0.95)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#5d8aa8',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.x + ' officers';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Officers',
                        font: { size: 10, weight: 'bold' },
                        color: '#2d3748'
                    },
                    ticks: {
                        stepSize: 1,
                        font: { size: 10 },
                        color: '#4a5568'
                    },
                    grid: {
                        color: 'rgba(160, 174, 192, 0.3)'
                    }
                },
                y: {
                    title: {
                        display: false
                    },
                    ticks: {
                        font: { size: 11, weight: 'bold' },
                        color: '#2d3748'
                    },
                    grid: {
                        color: 'rgba(160, 174, 192, 0.2)'
                    }
                }
            }
        }
    });

    // 2. Gender Distribution Chart - AIR FORCE BLUE & MARINE RED
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    const genderChart = new Chart(genderCtx, {
        type: 'bar',
        data: {
            labels: chartData.ranks,
            datasets: [
                {
                    label: 'Male',
                    data: chartData.male,
                    backgroundColor: 'rgba(70, 130, 180, 0.8)',  // Steel Blue (confident, not dark)
                    borderColor: 'rgba(70, 130, 180, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Female',
                    data: chartData.female,
                    backgroundColor: 'rgba(155, 79, 79, 0.8)',  // Marine Red (strong, professional)
                    borderColor: 'rgba(155, 79, 79, 1)',
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: { size: 11, weight: 'bold' },
                        padding: 8,
                        boxWidth: 15,
                        color: '#2d3748'
                    }
                },
                title: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(45, 55, 72, 0.95)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#5d8aa8',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            const label = context.dataset.label || '';
                            const value = context.parsed.y;
                            const total = chartData.male[context.dataIndex] + chartData.female[context.dataIndex];
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: false
                    },
                    ticks: {
                        font: { size: 10 },
                        color: '#4a5568'
                    },
                    grid: {
                        color: 'rgba(160, 174, 192, 0.3)'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Officers',
                        font: { size: 10, weight: 'bold' },
                        color: '#2d3748'
                    },
                    ticks: {
                        stepSize: 1,
                        font: { size: 10 },
                        color: '#4a5568'
                    },
                    grid: {
                        color: 'rgba(160, 174, 192, 0.3)'
                    }
                }
            }
        }
    });

    // 3. AFPOS Distribution Chart - BRIGHT MILITARY PALETTE
    const afposCtx = document.getElementById('afposChart').getContext('2d');
    
    // Collect all unique AFPOS across all ranks
    const allAfpos = new Set();
    Object.values(chartData.afpos).forEach(rankData => {
        rankData.forEach(item => {
            allAfpos.add(item.AFPOS);
        });
    });

    // Bright Military Color Palette - Confident & Professional
    const afposColors = {};
    const militaryColorPalette = [
        'rgba(61, 124, 71, 0.85)',    // Forest Green
        'rgba(93, 138, 168, 0.85)',   // Air Force Blue
        'rgba(139, 111, 71, 0.85)',   // Bronze/Khaki
        'rgba(70, 130, 180, 0.85)',   // Steel Blue
        'rgba(155, 79, 79, 0.85)',    // Marine Red
        'rgba(107, 142, 35, 0.85)',   // Olive Green
        'rgba(95, 122, 97, 0.85)',    // Ranger Green
        'rgba(184, 134, 11, 0.85)',   // Dark Goldenrod
        'rgba(112, 128, 144, 0.85)',  // Slate Gray
        'rgba(188, 143, 143, 0.85)',  // Rosy Brown
    ];
    
    let colorIndex = 0;
    allAfpos.forEach(afpos => {
        afposColors[afpos] = militaryColorPalette[colorIndex % militaryColorPalette.length];
        colorIndex++;
    });

    // Build datasets for each AFPOS
    const afposDatasets = [];
    allAfpos.forEach(afpos => {
        const data = chartData.ranks.map(rank => {
            const afposData = chartData.afpos[rank];
            const found = afposData.find(item => item.AFPOS === afpos);
            return found ? found.count : 0;
        });
        
        afposDatasets.push({
            label: afpos,
            data: data,
            backgroundColor: afposColors[afpos],
            borderColor: afposColors[afpos].replace('0.85', '1'),
            borderWidth: 1
        });
    });

    const afposChart = new Chart(afposCtx, {
        type: 'bar',
        data: {
            labels: chartData.ranks,
            datasets: afposDatasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: { size: 10, weight: 'bold' },
                        padding: 6,
                        boxWidth: 12,
                        color: '#2d3748'
                    }
                },
                title: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(45, 55, 72, 0.95)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#5d8aa8',
                    borderWidth: 1
                }
            },
            scales: {
                x: {
                    stacked: true,
                    title: {
                        display: true,
                        text: 'Rank',
                        font: { size: 10, weight: 'bold' },
                        color: '#2d3748'
                    },
                    ticks: {
                        font: { size: 10 },
                        color: '#4a5568'
                    },
                    grid: {
                        color: 'rgba(160, 174, 192, 0.3)'
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Officers',
                        font: { size: 10, weight: 'bold' },
                        color: '#2d3748'
                    },
                    ticks: {
                        stepSize: 1,
                        font: { size: 10 },
                        color: '#4a5568'
                    },
                    grid: {
                        color: 'rgba(160, 174, 192, 0.3)'
                    }
                }
            }
        }
    });
</script>
@endsection
