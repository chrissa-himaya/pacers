@extends('layouts.app')
@section('content')

<style>
    /* Compact 2x2 Grid Dashboard */
    .compact-card {
        margin-bottom: 0.5rem;
    }
    .compact-card .card-body {
        padding: 0.6rem;
    }
    .compact-card h6 {
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
    }
    .compact-card h4 {
        font-size: 1.2rem;
        margin-bottom: 0;
        font-weight: 600;
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
    }
    .card-body-compact {
        padding: 0.6rem;
    }
</style>

<div class="container-fluid px-3">
    <!-- Statistics Cards Row -->
    <div class="row mb-2">
        <div class="col-md-2 col-sm-4 col-6 mb-1">
            <div class="card text-white bg-primary compact-card">
                <div class="card-body text-center">
                    <h6 class="card-title">Total Officers</h6>
                    <h4 class="mb-0">{{ number_format($stats['total_officers']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6 mb-1">
            <div class="card text-white bg-info compact-card">
                <div class="card-body text-center">
                    <h6 class="card-title">Male</h6>
                    <h4 class="mb-0">{{ number_format($stats['male_officers']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6 mb-1">
            <div class="card text-white bg-danger compact-card">
                <div class="card-body text-center">
                    <h6 class="card-title">Female</h6>
                    <h4 class="mb-0">{{ number_format($stats['female_officers']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-6 mb-1">
            <div class="card text-white bg-success compact-card">
                <div class="card-body text-center">
                    <h6 class="card-title">Gender Ratio (M:F)</h6>
                    <h4 class="mb-0">
                        {{ $stats['female_officers'] > 0 ? number_format($stats['male_officers'] / $stats['female_officers'], 2) : 'N/A' }}:1
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-6 mb-1">
            <div class="card text-white bg-warning compact-card">
                <div class="card-body text-center">
                    <h6 class="card-title">AFPOS</h6>
                    <h4 class="mb-0">{{ number_format($stats['total_afpos']) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 1: Christmas Tree + Gender Chart -->
    <div class="row mb-2">
        <!-- Christmas Tree Chart -->
        <div class="col-md-6 mb-2">
            <div class="card h-100">
                <div class="card-header bg-primary text-white card-header-compact">
                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Officers Distribution by Rank</h6>
                </div>
                <div class="card-body card-body-compact">
                    <div class="chart-container">
                        <canvas id="pyramidChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gender Distribution Chart -->
        <div class="col-md-6 mb-2">
            <div class="card h-100">
                <div class="card-header bg-info text-white card-header-compact">
                    <h6 class="mb-0"><i class="fas fa-venus-mars"></i> Male vs Female Officers by Rank</h6>
                </div>
                <div class="card-body card-body-compact">
                    <div class="chart-container">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: AFPOS Table + AFPOS Stacked Chart -->
    <div class="row">
        <!-- AFPOS Table -->
        <div class="col-md-6 mb-2">
            <div class="card h-100">
                <div class="card-header bg-success text-white card-header-compact">
                    <h6 class="mb-0"><i class="fas fa-list"></i> Officers per AFPOS by Rank</h6>
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
                                                    <span class="badge badge-primary compact-badge mr-1 mb-1">
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

        <!-- AFPOS Stacked Chart -->
        <div class="col-md-6 mb-2">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark card-header-compact">
                    <h6 class="mb-0"><i class="fas fa-chart-pie"></i> AFPOS Distribution by Rank</h6>
                </div>
                <div class="card-body card-body-compact">
                    <div class="chart-container">
                        <canvas id="afposChart"></canvas>
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
    Chart.defaults.font.family = 'Arial, sans-serif';
    Chart.defaults.font.size = 11;

    // Data from Laravel
    const chartData = @json($chartData);

    // 1. Christmas Tree Chart (Population Pyramid)
    const pyramidCtx = document.getElementById('pyramidChart').getContext('2d');
    const pyramidChart = new Chart(pyramidCtx, {
        type: 'bar',
        data: {
            labels: chartData.ranks,
            datasets: [{
                label: 'Total Officers',
                data: chartData.total,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 99, 132, 1)',
                ],
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
                        font: { size: 10 }
                    },
                    ticks: {
                        stepSize: 1,
                        font: { size: 10 }
                    }
                },
                y: {
                    title: {
                        display: false
                    },
                    ticks: {
                        font: { size: 11 }
                    }
                }
            }
        }
    });

    // 2. Gender Distribution Chart
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    const genderChart = new Chart(genderCtx, {
        type: 'bar',
        data: {
            labels: chartData.ranks,
            datasets: [
                {
                    label: 'Male',
                    data: chartData.male,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Female',
                    data: chartData.female,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: 'rgba(255, 99, 132, 1)',
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
                        font: { size: 11 },
                        padding: 8,
                        boxWidth: 15
                    }
                },
                title: {
                    display: false
                },
                tooltip: {
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
                        font: { size: 10 }
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Officers',
                        font: { size: 10 }
                    },
                    ticks: {
                        stepSize: 1,
                        font: { size: 10 }
                    }
                }
            }
        }
    });

    // 3. AFPOS Distribution Chart (Stacked Bar)
    const afposCtx = document.getElementById('afposChart').getContext('2d');
    
    // Collect all unique AFPOS across all ranks
    const allAfpos = new Set();
    Object.values(chartData.afpos).forEach(rankData => {
        rankData.forEach(item => {
            allAfpos.add(item.AFPOS);
        });
    });

    // Generate colors for each AFPOS
    const afposColors = {};
    const colorPalette = [
        'rgba(255, 99, 132, 0.7)',
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 206, 86, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(153, 102, 255, 0.7)',
        'rgba(255, 159, 64, 0.7)',
        'rgba(199, 199, 199, 0.7)',
        'rgba(83, 102, 255, 0.7)',
        'rgba(255, 99, 255, 0.7)',
        'rgba(99, 255, 132, 0.7)',
    ];
    
    let colorIndex = 0;
    allAfpos.forEach(afpos => {
        afposColors[afpos] = colorPalette[colorIndex % colorPalette.length];
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
            borderColor: afposColors[afpos].replace('0.7', '1'),
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
                        font: { size: 10 },
                        padding: 6,
                        boxWidth: 12
                    }
                },
                title: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                x: {
                    stacked: true,
                    title: {
                        display: true,
                        text: 'Rank',
                        font: { size: 10 }
                    },
                    ticks: {
                        font: { size: 10 }
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Officers',
                        font: { size: 10 }
                    },
                    ticks: {
                        stepSize: 1,
                        font: { size: 10 }
                    }
                }
            }
        }
    });
</script>
@endsection
