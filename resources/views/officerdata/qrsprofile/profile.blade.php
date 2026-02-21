@extends('layouts.app')
@section('content')

<style>
    :root{
    --muted:#666;
    --green:#dfefe0;
    --accent:#9dc791;
    --bg:#f6f7f7;
    --ok:#2e7d32;
    --warn:#ef6c00;
    --bad:#c62828;
    }

    /* ===== BASE ===== */
    body{
    font-family:Arial, Helvetica, sans-serif;
    margin:12px;
    background:var(--bg);
    color:#111;
    }

    .page{
    display:flex;
    flex-direction:column;
    gap:12px;
    }

    .panel{
    background:#fff;
    border:1px solid #d0d0d0;
    border-radius:4px;
    padding:10px;
    box-shadow:0 1px 2px rgba(0,0,0,.04);
    overflow-x:auto;
    }

    .sub-panel{
    display:grid;
    grid-template-columns:60% 40%;
    gap:12px;
    }

    /* ===== HEADER ===== */
    .photo{
    width:120px;
    height:120px;
    border:2px solid #4f8f5a;
    border-radius:4px;
    overflow:hidden;
    }
    .photo img{
    width:100%;
    height:100%;
    object-fit:cover;
    }

    h1{font-size:15px;margin:0}
    h2{font-size:20px;margin:0 0 4px 0}
    .meta h3{
    font-size:13px;
    font-weight:normal;
    margin:1px 0;
    }
    .meta h3 strong{font-weight:600}

    .backbtn{float:left;margin-bottom:6px}
    .printbtn{
    float:right;
    margin-right:6px;
    background:#2e7d32;
    color:#fff;
    border:none;
    padding:6px 12px;
    font-size:14px;
    cursor:pointer;
    border-radius:3px;
    }
    .printbtn:hover{background:#1b5e20}

    /* ===== TABLES ===== */
    table{
    width:100%;
    border-collapse:collapse;
    font-size:12px;
    table-layout:fixed;
    }

    thead th{
    background:var(--green);
    padding:6px;
    border:1px solid #d8d8d8;
    font-weight:700;
    position:sticky;
    top:0;
    z-index:2;
    }

    th,td{
    border:1px solid #eaeaea;
    padding:6px;
    overflow:hidden;
    white-space:nowrap;
    text-overflow:ellipsis;
    font-variant-numeric:tabular-nums;
    }

    tbody tr:nth-child(even){background:#fafafa}
    tbody tr:hover{background:#eef6ee}

    .left-col{text-align:left}
    .small{font-size:11px;color:var(--muted)}

    .section-title{
    font-weight:700;
    background:#f1f1f1;
    padding:4px;
    margin:8px 0 2px 0;
    border-radius:3px;
    }

    /* Career table sizing */
    .career-table tr,
    .qrs-table tr{height:28px}

    .career-table td:nth-child(1){width:12%}
    .career-table td:nth-child(2){width:38%}
    .career-table td:nth-child(n+3){width:8%}

    /* ===== TABS ===== */
    .qrs-wrap{display:flex;flex-direction:column}
    .tabs{
    display:flex;
    gap:6px;
    }
    .tab{
    flex:1;
    padding:8px;
    border-radius:4px;
    background:#f3f3f3;
    border:1px solid #d0d0d0;
    text-align:center;
    cursor:pointer;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.5px;
    }
    .tab.active{
    background:#4f8f5a;
    color:#fff;
    border-color:#4f8f5a;
    }
    .tab-body{flex:1;overflow:auto}

    /* QRS tables */
    .qrs-table th{
    background:#f6f8f6;
    border:1px solid #d6d6d6;
    }
    .qrs-table td{text-align:center}

    .qrs-table-extra{
    width:100%;
    text-align:center;
    border-collapse:collapse;
    }
    .qrs-table-extra th,
    .qrs-table-extra td{
    border:1px solid #e0e0e0;
    padding:6px;
    }

    /* ===== ANALYSIS SECTION ===== */
    .analysis-wrap{
    display:grid;
    grid-template-columns:55% 45%;
    gap:10px;
    }

    .analysis-panel{
    border:1px solid #d0d0d0;
    background:#fff;
    padding:6px;
    }

    /* Status colors */
    .ok{color:var(--ok);font-weight:700}
    .warn{color:var(--warn);font-weight:700}
    .bad{color:var(--bad);font-weight:700}

    /* ===== HORIZONTAL BAR CHART (STACKED VERTICAL) ===== */

    .bar-chart{
    display:flex;
    flex-direction:column;
    gap:10px;
    }

    .row{
    display:grid;
    grid-template-columns:160px 1fr;
    align-items:center;
    gap:10px;
    }

    .bars.stacked{
    display:flex;
    flex-direction:column;
    gap:2px;
    }

    /* ACTUAL (TOP BAR) */
    .bar.actual{
    position:relative;
    height:20px;
    background:#4caf50;
    width:calc(var(--val) * 20px);
    border-radius:2px;
    }

    /* MAX (BOTTOM BAR) */
    .bar.max{
    position:relative;
    height:20px;
    background:#cfcfcf;
    width:calc(var(--val) * 20px);
    border-radius:2px;
    color:#333;
    font-weight:700;
    font-size:12px;
    }

    /* VALUE LABEL (used by both bars if needed) */
    .bar-value{
    position:absolute;
    right:6px;
    top:50%;
    transform:translateY(-50%);
    font-size:12px;
    font-weight:700;
    color:#fff;
    white-space:nowrap;
    pointer-events:none;
    }

    /* darker text for max bar */
    .bar.max .bar-value{
    color:#333;
    }

    /* ===== RESPONSIVE ===== */
    @media(max-width:1000px){
    .sub-panel{grid-template-columns:1fr}
    .analysis-wrap{grid-template-columns:1fr}
    }

    /* ===== PRINT ===== */
    @media print{
    .backbtn,
    .printbtn{display:none!important}
    body{background:#fff;margin:0}
    .panel{border:none;box-shadow:none;padding:0}
    table{font-size:11px}
    th{background:#eaeaea!important;color:#000!important}
    tr,td,th{page-break-inside:avoid}
    .section-title{background:#f0f0f0!important;color:#000}

    /* FORCE COLOR PRINTING */
    *{
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* ENSURE BAR COLORS PRINT */
    .bar.actual{
        background:#4caf50 !important;
    }

    .bar.max{
        background:#cfcfcf !important;
    }

    .bar-value{
        color:#fff !important;
    }

    .bar.max .bar-value{
        color:#333 !important;
    }

    /* SHOW ALL TAB CONTENT */
    .tab-panel{
        display:block !important;
        page-break-inside: avoid;
    }

    /* HIDE TAB BUTTONS */
    .tabs{
        display:none !important;
    }

    /* OPTIONAL: spacing between rank sections */
    .tab-panel{
        margin-bottom:16px;
    }
    }
</style>

<div class="card" style="margin-left: -15px;">
        <div class="panel">
            <header style="text-align: center;">
                <div class="top-center">
                    P H I L I P P I N E &nbsp; A R M Y <input class="backbtn" type="button" value="Back" onclick="window.location.href='index - original.html'"><input type="button" value="Print OCAR" class="printbtn" onclick="window.print()"><br>
                    PERSONNEL MANAGEMENT CENTER<br>
                    Fort Andres Bonifacio, Taguig City<br>
                    <strong>QUANTITATIVE RATING SYSTEM (QRS) SHEET</strong>
                </div>
            </header>
            <div class="header-content" style="display: flex; justify-content: space-between;">
                <div class="meta">
                    <h2 style="margin: 1px;"><strong>{{ $data->RANK }} {{ $data->NAME }}</strong></h2>
                    <h3 style="margin: 1px;">Designation: {{ $data->designations->name }}</h3>
                    <h3 style="margin: 1px;">PM Code: {{ $data->PM_CODE }}</h3>
                    <!-- <h3 style="margin: 1px;">Email: delacruzja@sample.com &nbsp; | &nbsp; Contact Nr.: 0917-123-4567</h3> -->
                    <h3 style="margin: 1px;">Date of Commissionship: {{ $data->DOC }} &nbsp; | &nbsp; {{ $data->DOR }}</h3>
                </div>
            <div class="photo">
                <img src="img/sample-pic.jpg" alt="Profile photo" style="size:2in">
            </div>
            </div>
        </div>
        <div class="sub-panel">
            <div class="panel qrs-wrap1">
                <div class="section-title" style="text-align: center;">Career Summary</div>
                    <table class="career-table">
                        <thead style="height: 65px;">
                            <tr>
                            <th>CATEGORY</th>
                            <th>CRITERIA</th>
                            <th>2LT</th>
                            <th>1LT</th>
                            <th>CPT</th>
                            <th>MAJ</th>
                            <th>LTC</th>
                            <th>COL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Staff Duty Assignments -->
                            <tr><td rowspan="13" class="left-col">Staff Duty<br>Assignments</td><td class="left-col">GUA Staff</td><td>0</td><td>0</td><td>0</td><td>2y 2m</td><td>0</td><td>0</td></tr>
                            <tr><td class="left-col">HPA Staff</td><td>0</td><td>0</td><td>0</td><td>6m</td><td>7m</td><td>0</td></tr>
                            <tr><td class="left-col">PAMU Staff</td><td>0</td><td>0</td><td>2y</td><td>6m</td><td>2y 2m</td><td>0</td></tr>
                            <tr><td class="left-col">Brigade Staff</td><td>0</td><td>0</td><td>7m</td><td>0</td><td>0</td><td>0</td></tr>
                            <tr><td class="left-col">Battalion Staff</td><td>7m</td><td>3y 4m</td><td>7m</td><td>0</td><td>0</td><td>0</td></tr>
                            <tr><td class="left-col">Company Ex-O</td><td>1y 3m</td><td>3y 5m</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                            <tr><td class="left-col">Special Duty</td><td>0</td><td>0</td><td>0</td><td>10m</td><td>0</td><td>0</td></tr>
                            <tr><td class="left-col">Instructor Duty</td><td>0</td><td>0</td><td>0</td><td>10m</td><td>0</td><td>0</td></tr>
                            <tr><td class="left-col">Foreign Duty</td><td>0</td><td>0</td><td>0</td><td>8m</td><td>4m</td><td>0</td></tr>
                            <tr><td class="left-col">ResCom Duty</td><td>0</td><td>0</td><td>0</td><td>5m</td><td>0</td><td>0</td></tr>
                            <tr><td class="left-col">Reassigned/Duty/DS</td><td>7m</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                            <tr><td class="left-col">Schooling</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                            <tr><td class="left-col">Attached/Unassigned</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>

                            <!-- Command Assignments -->
                            <tr><td rowspan="4" class="left-col">Command<br>Assignments</td><td class="left-col">Platoon Leader</td><td>1y 9m</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                            <tr><td class="left-col">Company Commander</td><td>0</td><td>0</td><td>11m</td><td>0</td><td>0</td><td>0</td></tr>
                            <tr><td class="left-col">Battalion Commander</td><td>0</td><td>0</td><td>0</td><td>1y 2m</td><td>0</td><td>0</td></tr>
                            <tr><td class="left-col">Brigade Commander</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>

                            <!-- Types of Assignments -->
                            <tr><td rowspan="3" class="left-col">Types of<br>Assignments</td><td class="left-col">Category A</td><td>3y</td><td>4y</td><td>4y 4m</td><td>1y 3m</td><td>1y 2m</td><td>0</td></tr>
                            <tr><td class="left-col">Category B</td><td>0</td><td>0</td><td>2y</td><td>5m</td><td>0</td><td>0</td></tr>
                            <tr><td class="left-col">Category C</td><td>7m</td><td>0</td><td>0</td><td>8m</td><td>4y 4m</td><td>2y 10m</td></tr>

                            <!-- Geographical Assignments -->
                            <tr><td rowspan="5" class="left-col">Geographical<br>Assignments</td><td class="left-col">NCR</td><td>7m</td><td>0</td><td>0</td><td>3y 2m</td><td>2y 10m</td><td>0</td></tr>
                            <tr><td class="left-col">Luzon</td><td>0</td><td>0</td><td>0</td><td>2y 5m</td><td>0</td><td>0</td></tr>
                            <tr><td class="left-col">Visayas</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                            <tr><td class="left-col">Mindanao</td><td>3y</td><td>4y</td><td>4y 4m</td><td>3y 4m</td><td>0</td><td>0</td></tr>
                            <tr><td class="left-col">Foreign Duty</td><td>0</td><td>0</td><td>8m</td><td>4m</td><td>0</td><td>0</td></tr>
                        </tbody>
                    </table>

                <div class="section-title">Professional Preparation and Development</div>
                <table class="schooling-table">
                <thead><tr><th colspan="2">CRITERIA</th><th colspan="3">COURSE</th><th>RATING</th><th>STANDING</th></tr></thead>
                <tbody>
                    <tr>
                    <td colspan="2">Pre-entry Course</td>
                    <td colspan="3">PMA Cl 2000 (PMA)</td>
                    <td>97</td>
                    <td>25/200</td>
                    </tr>

                    <tr>
                    <td colspan="2">Officer Basic Course</td>
                    <td colspan="3">Infantry Officer Basic Course Cl 50-2003 (CAS, TRADOC, PA)</td>
                    <td>95</td>
                    <td>2/50</td>
                    </tr>

                    <tr>
                    <td colspan="2">Officer Advance Course</td>
                    <td colspan="3">Infantry Officer Advance Course Cl 75-2007 (3ATG, TRADOC, PA)</td>
                    <td>88</td>
                    <td>4/45</td>
                    </tr>

                    <tr>
                    <td colspan="2">Staff Officer Course</td>
                    <td colspan="3">-</td>
                    <td>-</td>
                    <td>-</td>
                    </tr>

                    <tr>
                    <td colspan="2">CGSC</td>
                    <td colspan="3">Command and General Staff Course Cl 65-2018 (AFPETDC)</td>
                    <td>87</td>
                    <td>3/180</td>
                    </tr>

                    <tr>
                    <td colspan="2">Civil Service Eligibility</td>
                    <td colspan="3">Civil Service Exam 2024 (CSC)</td>
                    <td>85</td>
                    <td>-</td>
                    </tr>

                    <tr>
                    <td colspan="2">Post Graduate Course</td>
                    <td colspan="3">MNSA (War College, USA)</td>
                    <td>96</td>
                    <td>7/50</td>
                    </tr>

                    <tr>
                    <td colspan="2">Specialization Course</td>
                    <td colspan="3">Cyber Security Training NCIII; Information System Online Course Cl 200-2010; Scout Sniper Course Cl 15-2011 and 3 others</td>
                    <td>-</td>
                    <td>-</td>
                    </tr>
                </tbody>
                </table>

                <div class="section-title">Awards and Decorations</div>
                <table class="award-table">
                    <thead>
                    <tr>
                        <th>2LT</th>
                        <th>1LT</th>
                        <th>CPT</th>
                        <th>MAJ</th>
                        <th>LTC</th>
                        <th>COL</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td rowspan="3">MMM (A) -1</td><td rowspan="3">GCM-1<br>WPM-1<br>MMM (A)-1</td><td>MMM (S)-1<br>MMM (A)-1<br>MCM-1</td><td>Others-1</td><td>MoV-1<br>OAM-1<br>DCS-1</td><td>GSK-1<br>CSAFPCM-1</td></tr>
                    </tbody>
                </table>

                <div class="section-title">Physical Fitness Test</div>
                <table class="pft-table">
                    <thead>
                    <tr>
                        <th>Description</th>
                        <th>2LT</th>
                        <th>1LT</th>
                        <th>CPT</th>
                        <th>MAJ</th>
                        <th>LTC</th>
                        <th>COL</th>
                    </tr>
                    </thead>
                <tbody>
                    <tr>
                    <td>Rating</td>
                    <td>96.20</td><td>95.10</td><td>94.30</td><td>70.00</td><td>90.20</td><td>91.00</td>
                    </tr>
                    <tr>
                    <td>Date taken</td>
                    <td>01/May/2003</td>
                    <td>01/Jun/2007</td>
                    <td>01/Aug/2012</td>
                    <td>20/Sep/2016</td>
                    <td>01/Oct/2022</td>
                    <td>01/Jun/2025</td>
                    </tr>
                    <tr>
                    <td>Supervising unit</td>
                    <td>SSC, HHSG, PA</td>
                    <td>SSU, 4ID, PA</td>
                    <td>SSU, 2ID, PA</td>
                    <td>SSC, HHSG, PA</td>
                    <td>SSC, HHSG, PA</td>
                    <td>SSC, HHSG, PA</td>
                    </tr>
                </tbody>
                </table>

                <!-- <div class="section-title" style="text-align:left;">Officer Career Analysis Report</div> -->
                <div class="analysis-wrap">
                    <!-- <div class="analysis-panel">
                    <div class="bar-chart">

                        <div class="row">
                        <span class="label">Category A</span>
                        <div class="bars stacked">
                            <div class="bar actual" style="--val:14.25">
                            <span class="bar-value">14.25</span>
                            </div>
                            <div class="bar max" style="--val:2">
                            <span class="bar-value">2</span>
                            </div>
                        </div>
                        </div>

                        <div class="row">
                        <span class="label">Category B</span>
                        <div class="bars stacked">
                            <div class="bar actual" style="--val:10.25">
                            <span class="bar-value">10.25</span>
                            </div>
                            <div class="bar max" style="--val:4">
                            <span class="bar-value">4</span>
                            </div>
                        </div>
                        </div>

                        <div class="row">
                        <span class="label">Category C</span>
                        <div class="bars stacked">
                            <div class="bar actual" style="--val:2.00">
                            <span class="bar-value">2</span>
                            </div>
                            <div class="bar max" style="--val:4">
                            <span class="bar-value">4</span>
                            </div>
                        </div>
                        </div>

                        <div class="row">
                        <span class="label">HHQs Staff</span>
                        <div class="bars stacked">
                            <div class="bar actual" style="--val:1.50">
                            <span class="bar-value">1.50</span>
                            </div>
                            <div class="bar max" style="--val:5">
                            <span class="bar-value">5</span>
                            </div>
                        </div>
                        </div>

                        <div class="row">
                        <span class="label">PAMU Staff</span>
                        <div class="bars stacked">
                            <div class="bar actual" style="--val:0.58">
                            <span class="bar-value">0.58</span>
                            </div>
                            <div class="bar max" style="--val:4">
                            <span class="bar-value">4</span>
                            </div>
                        </div>
                        </div>

                        <div class="row">
                        <span class="label">Bn/Bde Staff</span>
                        <div class="bars stacked">
                            <div class="bar actual" style="--val:10.33">
                            <span class="bar-value">10.33</span>
                            </div>
                            <div class="bar max" style="--val:2">
                            <span class="bar-value">2</span>
                            </div>
                        </div>
                        </div>

                        <div class="row">
                        <span class="label">Instructor Duty</span>
                        <div class="bars stacked">
                            <div class="bar actual" style="--val:3.75">
                            <span class="bar-value">3.75</span>
                            </div>
                            <div class="bar max" style="--val:2">
                            <span class="bar-value">2</span>
                            </div>
                        </div>
                        </div>

                        <div class="row">
                        <span class="label">ResCom Duty</span>
                        <div class="bars stacked">
                            <div class="bar actual" style="--val:1.20">
                            <span class="bar-value">1.20</span>
                            </div>
                            <div class="bar max" style="--val:2">
                            <span class="bar-value">2</span>
                            </div>
                        </div>
                        </div>

                        <div class="row">
                        <span class="label">Special/Foreign</span>
                        <div class="bars stacked">
                            <div class="bar actual" style="--val:3.83">
                            <span class="bar-value">3.83</span>
                            </div>
                            <div class="bar max" style="--val:2">
                            <span class="bar-value">2</span>
                            </div>
                        </div>
                        </div>

                        <div class="row">
                        <span class="label">Company Cmd</span>
                        <div class="bars stacked">
                            <div class="bar actual" style="--val:5.42">
                            <span class="bar-value">5.42</span>
                            </div>
                            <div class="bar max" style="--val:1.5">
                            <span class="bar-value">1.5</span>
                            </div>
                        </div>
                        </div>

                        <div class="row">
                        <span class="label">Pltn Ldr/Coy XO</span>
                        <div class="bars stacked">
                            <div class="bar actual" style="--val:3.75">
                            <span class="bar-value">3.75</span>
                            </div>
                            <div class="bar max" style="--val:2">
                            <span class="bar-value">2</span>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div> -->

                    <!-- RIGHT : STATUS TABLE -->
                    <!-- <div class="analysis-panel">
                    <table class="analysis-status-table">
                        <thead>
                        <tr>
                            <th>Assignment/Duty</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr><td>Category A</td><td class="ok">Complete</td><td></td></tr>
                        <tr><td>Category B</td><td class="ok">Complete</td><td></td></tr>
                        <tr><td>Category C</td><td class="warn">None</td><td>Satisfy HPA/GUA Assignment</td></tr>
                        <tr><td>HHQs Staff</td><td class="warn">None</td><td>Satisfy HHQs Staff Duty</td></tr>
                        <tr><td>PAMU Staff</td><td class="bad">Incomplete</td><td>To complete PAMU Staff Duty</td></tr>
                        <tr><td>Bn/Bde Staff</td><td class="ok">Complete</td><td></td></tr>
                        <tr><td>Instructor Duty</td><td class="ok">Complete</td><td></td></tr>
                        <tr><td>ResCom Duty</td><td class="warn">None</td><td>Satisfy ResCom Duty</td></tr>
                        <tr><td>Special/Foreign</td><td class="ok">Complete</td><td></td></tr>
                        <tr><td>Company Commander</td><td class="ok">Complete</td><td></td></tr>
                        <tr><td>Pltn Ldr/Coy XO</td><td class="ok">Complete</td><td></td></tr>
                        <tr><td>Officer Basic Course</td><td class="ok">Complete</td><td></td></tr>
                        <tr><td>Officer Advance Course</td><td class="ok">Complete</td><td></td></tr>
                        <tr><td>Spcl Crse/Post Grad</td><td class="ok">Complete</td><td></td></tr>
                        <tr><td>Civil Service Eligibility</td><td class="ok">Complete</td><td></td></tr>
                        <tr><td>Awards (Present)</td><td class="bad">Incomplete</td><td>Gain more Awards</td></tr>
                        <tr><td>Awards (Previous)</td><td class="bad">Incomplete</td><td>Gain more Awards</td></tr>
                        </tbody>
                    </table>
                    </div> -->
                </div>
            </div>

            <div class="panel qrs-wrap2">
            <div class="section-title" style="text-align: center;">QRS Requirements</div>
                <div class="tabs">
                <div class="tab active" data-tab="t-2lt">2LT</div>
                <div class="tab" data-tab="t-1lt">1LT</div>
                <div class="tab" data-tab="t-cpt">CPT</div>
                <div class="tab" data-tab="t-maj">MAJ</div>
                <div class="tab" data-tab="t-ltc">LTC</div>
                <div class="tab" data-tab="t-col">COL</div>
                </div>
            <div class="tab-body">
                <!-- Each tab table uses the same number of rows as the Career Summary above to keep alignment -->
                <div id="t-2lt" class="tab-panel">
                    <table class="qrs-table">
                    <thead>
                        <tr>
                        <th>2LT : max year</th>
                        <th>2LT : max points</th>
                        <th>2LT : gained points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>-</td><td>-</td><td>-</td></tr>
                        <tr><td>-</td><td>-</td><td>-</td></tr>
                        <tr><td>4.00</td><td>-</td><td>-</td></tr>
                        <tr><td>2.00</td><td>-</td><td>-</td></tr>
                        <tr><td>2.00</td><td>-</td><td>-</td></tr>
                        <tr><td>2.00</td><td>6.00</td><td>3.25</td></tr>
                        <tr><td>2.00</td><td>2.00</td><td>-</td></tr>
                        <tr><td>2.00</td><td>2.00</td><td>-</td></tr>
                        <tr><td>2.00</td><td>3.00</td><td>-</td></tr>
                        <tr><td>2.00</td><td>3.00</td><td>-</td></tr>
                        <tr><td>-</td><td>-</td><td>-</td></tr>
                        <tr><td>-</td><td>-</td><td>-</td></tr>
                        <tr><td>-</td><td>-</td><td>-</td></tr>
                        <tr><td>3.00</td><td>4.00</td><td>2.25</td></tr>
                        <tr><td>-</td><td>-</td><td>-</td></tr>
                        <tr><td>-</td><td>-</td><td>-</td></tr>
                        <tr><td>-</td><td>-</td><td>-</td></tr>
                        <tr><td>8.00</td><td>3.20</td><td>1.36</td></tr>
                        <tr><td>2.00</td><td>0.80</td><td>-</td></tr>
                        <tr><td>2.00</td><td>0.80</td><td>0.42</td></tr>
                        <tr><td>8.00</td><td>-</td><td>-</td></tr>
                        <tr><td>8.00</td><td>2.00</td><td>-</td></tr>
                        <tr><td>8.00</td><td>2.00</td><td>-</td></tr>
                        <tr><td>8.00</td><td>2.00</td><td>0.69</td></tr>
                        <tr><td>8.00</td><td>-</td><td>-</td></tr>
                    </tbody>
                    </table>

                    <div class="section-title">Schooling points</div>
                        <table class="qrs-table-extra">
                        <thead>
                            <tr>
                            <th colspan="2">max points</th>
                            <th>actual points</th>
                            </tr>
                            <!-- <tr></tr> -->
                        </thead>
                        <tbody>
                            <tr><td colspan="2">2.00</td><td>1.85</td></tr>
                            <tr><td colspan="2">-</td><td>-</td></tr>
                            <tr><td colspan="2">-</td><td>-</td></tr>
                            <tr><td colspan="2">-</td><td>-</td></tr>
                            <tr><td colspan="2">-</td><td>-</td></tr>
                            <tr><td colspan="2">1.00</td><td>1.00</td></tr>
                            <tr><td colspan="2">-</td><td>-</td></tr>
                            <tr><td colspan="2">2.00</td><td>0.50</td></tr>
                        </tbody>
                        </table>

                    <div class="section-title">Awards points</div>
                        <table class="qrs-table-extra">
                        <thead>
                            <tr>
                            <th colspan="2">max points</th>
                            <th>actual points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="2" ><br>10.00<br><br></td><td>1</td></tr>
                        </tbody>
                        </table>

                    <div class="section-title">PFT points</div>
                        <table class="qrs-table-extra">
                        <thead>
                            <tr>
                            <th>min points</th>
                            <th>max points</th>
                            <th>actual points</th>
                            </tr>
                        </thead>
                        <tbody style="height: 81px;">
                            <tr>
                            <td><br><br>(-)<br><br></td>
                            <td>5.00</td>
                            <td>4.37</td>
                            </tr>
                        </tbody>
                        </table>
                        
                    <div style="margin-top:8px">
                        <table class="qrs-table" style="width:100%">
                        <tbody>
                            <tr><td colspan="2" rowspan="3" style="background-color: #9dc791; font-size: large;"><strong>QRS SCORE:</strong></td><td rowspan="3" style="background-color: #9dc791; font-size: large; font-weight: bold;">16.69</td></tr>
                            <tr></tr>
                            <tr></tr>
                        </tbody>
                        </table>
                    </div>
                </div>

                <!-- 1LT tab (same number of rows) -->
                <div id="t-1lt" class="tab-panel" style="display:none">
                <table class="qrs-table">
                    <thead>
                    <tr>
                        <th>1LT : max year</th>
                        <th>1LT : max points</th>
                        <th>1LT : gained points</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>4.00</td><td>-</td><td>-</td></tr>
                    <tr><td>2.00</td><td>-</td><td>-</td></tr>
                    <tr><td>2.00</td><td>-</td><td>-</td></tr>
                    <tr><td>2.00</td><td>6.00</td><td>6.00</td></tr>
                    <tr><td>2.00</td><td>2.00</td><td>-</td></tr>
                    <tr><td>2.00</td><td>2.00</td><td>-</td></tr>
                    <tr><td>2.00</td><td>3.00</td><td>-</td></tr>
                    <tr><td>2.00</td><td>3.00</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>3.00</td><td>4.00</td><td>2.25</td></tr>
                    <tr><td>1.50</td><td>4.00</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>8.00</td><td>3.20</td><td>2.86</td></tr>
                    <tr><td>2.00</td><td>0.80</td><td>-</td></tr>
                    <tr><td>2.00</td><td>0.80</td><td>0.42</td></tr>
                    <tr><td>8.00</td><td>-</td><td>-</td></tr>
                    <tr><td>8.00</td><td>2.00</td><td>-</td></tr>
                    <tr><td>8.00</td><td>2.00</td><td>-</td></tr>
                    <tr><td>8.00</td><td>2.00</td><td>1.76</td></tr>
                    <tr><td>8.00</td><td>-</td><td>-</td></tr>
                    </tbody>
                </table>

                <div class="section-title">Schooling points</div>
                    <table class="qrs-table-extra">
                    <thead>
                        <tr>
                        <th colspan="2">max points</th>
                        <th>actual points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="2">2.00</td><td>1.85</td></tr>
                        <tr><td colspan="2">2.00</td><td>1.93</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">1.00</td><td>1.00</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">2.00</td><td>2.00</td></tr>
                    </tbody>
                    </table>

                <div class="section-title">Awards points</div>
                    <table class="qrs-table-extra">
                    <thead>
                        <tr>
                        <th colspan="2">max points</th>
                        <th>actual points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="2">5.00</td><td>4.80</td></tr>
                        <tr><td colspan="2">5.00</td><td>1.00</td></tr>
                    </tbody>
                    </table>
                
                <div class="section-title">PFT points</div>
                    <table class="qrs-table-extra">
                    <thead>
                        <tr>
                        <th>min points</th>
                        <th>max points</th>
                        <th>actual points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td><br><br>(-)<br><br></td>                  
                        <td>5.00</td>
                        <td>4.18</td>
                        </tr>
                    </tbody>
                    </table>
                    <div style="margin-top:8px">
                        <table class="qrs-table" style="width:100%">
                        <tbody>
                            <tr><td colspan="2" rowspan="3" style="background-color: #9dc791; font-size: large;"><strong>QRS SCORE:</strong></td><td rowspan="3" style="background-color: #9dc791; font-size: large; font-weight: bold;">30.05</td></tr>
                            <tr></tr>
                            <tr></tr>
                        </tbody>
                        </table>
                    </div>
                </div>

                <!-- CPT tab -->
                <div id="t-cpt" class="tab-panel" style="display:none">
                <table class="qrs-table">
                    <thead>
                    <tr>
                        <th>CPT : max year</th>
                        <th>CPT : max points</th>
                        <th>CPT : actual points</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td>4.00</td><td>-</td><td>-</td></tr>
                    <tr><td>4.00</td><td>-</td><td>-</td></tr>
                    <tr><td>4.00</td><td>-</td><td>-</td></tr>
                    <tr><td>2.00</td><td>-</td><td>-</td></tr>
                    <tr><td>2.00</td><td>6.00</td><td>6.00</td></tr>
                    <tr><td>2.00</td><td>6.00</td><td>6.00</td></tr>
                    <tr><td>2.00</td><td>2.00</td><td>-</td></tr>
                    <tr><td>2.00</td><td>2.00</td><td>-</td></tr>
                    <tr><td>2.00</td><td>3.00</td><td>-</td></tr>
                    <tr><td>2.00</td><td>3.00</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>3.00</td><td>4.00</td><td>2.25</td></tr>
                    <tr><td>1.50</td><td>-</td><td>0.29</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>8.00</td><td>3.20</td><td>3.20</td></tr>
                    <tr><td>2.00</td><td>0.80</td><td>-</td></tr>
                    <tr><td>2.00</td><td>0.80</td><td>0.42</td></tr>
                    <tr><td>8.00</td><td>-</td><td>-</td></tr>
                    <tr><td>8.00</td><td>3.00</td><td>-</td></tr>
                    <tr><td>8.00</td><td>3.00</td><td>-</td></tr>
                    <tr><td>8.00</td><td>3.00</td><td>3.00</td></tr>
                    <tr><td>8.00</td><td>1.50</td><td>-</td></tr>
                    </tbody>
                </table>


                <div class="section-title">Schooling points</div>
                    <table class="qrs-table-extra">
                    <thead>
                        <tr>
                        <th colspan="2">max points</th>
                        <th>actual points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="2">2.00</td><td>1.85</td></tr>
                        <tr><td colspan="2">2.00</td><td>1.93</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">1.00</td><td>1.00</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">2.00</td><td>2.00</td></tr>
                    </tbody>
                    </table>

                <div class="section-title">Awards points</div>
                    <table class="qrs-table-extra">
                    <thead>
                        <tr>
                        <th colspan="2">max points</th>
                        <th>actual points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="2">5.00</td><td>4.80</td></tr>
                        <tr><td colspan="2">5.00</td><td>1.00</td></tr>
                    </tbody>
                    </table>
                
                <div class="section-title">PFT points</div>
                    <table class="qrs-table-extra">
                    <thead>
                        <tr>
                        <th>min points</th>
                        <th>max points</th>
                        <th>actual points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td><br><br>(-)<br><br></td>                  
                        <td>5.00</td>
                        <td>4.18</td>
                        </tr>
                    </tbody>
                    </table>
                    <div style="margin-top:8px">
                        <table class="qrs-table" style="width:100%">
                        <tbody>
                            <tr><td colspan="2" rowspan="3" style="background-color: #9dc791; font-size: large;"><strong>QRS SCORE:</strong></td><td rowspan="3" style="background-color: #9dc791; font-size: large; font-weight: bold;">30.05</td></tr>
                            <tr></tr>
                            <tr></tr>
                        </tbody>
                        </table>
                    </div>
                </div>

                <!-- MAJ tab -->
                <div id="t-maj" class="tab-panel" style="display:none">
                <table class="qrs-table">
                    <thead>
                    <tr>
                        <th>MAJ : max year</th>
                        <th>MAJ : max points</th>
                        <th>MAJ : actual points</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td>2.00</td><td>1.50</td><td>-</td></tr>
                    <tr><td>2.00</td><td>1.50</td><td>-</td></tr>
                    <tr><td>2.00</td><td>1.50</td><td>1.50</td></tr>
                    <tr><td>4.00</td><td>4.00</td><td>0.58</td></tr>
                    <tr><td>4.00</td><td>4.00</td><td>4.00</td></tr>
                    <tr><td>3.00</td><td>4.00</td><td>4.00</td></tr>
                    <tr><td>2.00</td><td>0.50</td><td>-</td></tr>
                    <tr><td>2.00</td><td>0.50</td><td>0.50</td></tr>
                    <tr><td>2.00</td><td>1.50</td><td>-</td></tr>
                    <tr><td>2.00</td><td>0.50</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>3.00</td><td>4.00</td><td>2.25</td></tr>
                    <tr><td>2.00</td><td>5.00</td><td>1.75</td></tr>
                    <tr><td>2.00</td><td>5.00</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>8.00</td><td>3.20</td><td>3.20</td></tr>
                    <tr><td>2.00</td><td>0.80</td><td>0.80</td></tr>
                    <tr><td>2.00</td><td>0.80</td><td>0.60</td></tr>
                    <tr><td>8.00</td><td>1.00</td><td>0.01</td></tr>
                    <tr><td>8.00</td><td>3.00</td><td>-</td></tr>
                    <tr><td>8.00</td><td>3.00</td><td>-</td></tr>
                    <tr><td>8.00</td><td>3.00</td><td>3.00</td></tr>
                    <tr><td>8.00</td><td>2.00</td><td>0.04</td></tr>
                    </tbody>
                </table>



                <div class="section-title">Schooling points</div>
                    <table class="qrs-table-extra">
                    <thead>
                        <tr>
                        <th colspan="2">max points</th>
                        <th>actual points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="2">2.00</td><td>1.85</td></tr>
                        <tr><td colspan="2">2.00</td><td>1.93</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">1.00</td><td>1.00</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">2.00</td><td>2.00</td></tr>
                    </tbody>
                    </table>

                <div class="section-title">Awards points</div>
                    <table class="qrs-table-extra">
                    <thead>
                        <tr>
                        <th colspan="2">max points</th>
                        <th>actual points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="2">5.00</td><td>4.80</td></tr>
                        <tr><td colspan="2">5.00</td><td>1.00</td></tr>
                    </tbody>
                    </table>
                
                <div class="section-title">PFT points</div>
                    <table class="qrs-table-extra">
                    <thead>
                        <tr>
                        <th>min points</th>
                        <th>max points</th>
                        <th>actual points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td><br><br>(-)<br><br></td>                  
                        <td>5.00</td>
                        <td>4.18</td>
                        </tr>
                    </tbody>
                    </table>
                    <div style="margin-top:8px">
                        <table class="qrs-table" style="width:100%">
                        <tbody>
                            <tr><td colspan="2" rowspan="3" style="background-color: #9dc791; font-size: large;"><strong>QRS SCORE:</strong></td><td rowspan="3" style="background-color: #9dc791; font-size: large; font-weight: bold;">30.05</td></tr>
                            <tr></tr>
                            <tr></tr>
                        </tbody>
                        </table>
                    </div>
                </div>

                <!-- LTC tab -->
                <div id="t-ltc" class="tab-panel" style="display:none">
                <table class="qrs-table">
                    <thead>
                    <tr>
                        <th>LTC : max year</th>
                        <th>LTC : max points</th>
                        <th>LTC : actual points</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td>2.00</td><td>6.00</td><td>6.00</td></tr>
                    <tr><td>2.00</td><td>6.00</td><td>0.50</td></tr>
                    <tr><td>2.00</td><td>6.00</td><td>6.00</td></tr>
                    <tr><td>1.00</td><td>3.00</td><td>0.92</td></tr>
                    <tr><td>1.00</td><td>3.00</td><td>3.00</td></tr>
                    <tr><td>1.00</td><td>6.00</td><td>6.00</td></tr>
                    <tr><td>1.00</td><td>2.00</td><td>-</td></tr>
                    <tr><td>1.00</td><td>2.00</td><td>2.00</td></tr>
                    <tr><td>1.00</td><td>3.00</td><td>2.17</td></tr>
                    <tr><td>1.00</td><td>3.00</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>2.00</td><td>6.00</td><td>5.08</td></tr>
                    <tr><td>1.50</td><td>7.00</td><td>3.21</td></tr>
                    <tr><td>2.00</td><td>5.00</td><td>2.50</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>2.00</td><td>3.00</td><td>3.00</td></tr>
                    <tr><td>4.00</td><td>3.00</td><td>1.87</td></tr>
                    <tr><td>4.00</td><td>3.00</td><td>3.00</td></tr>
                    <tr><td>8.00</td><td>4.00</td><td>1.73</td></tr>
                    <tr><td>8.00</td><td>3.00</td><td>0.77</td></tr>
                    <tr><td>8.00</td><td>3.00</td><td>-</td></tr>
                    <tr><td>8.00</td><td>3.00</td><td>3.00</td></tr>
                    <tr><td>8.00</td><td>2.00</td><td>0.13</td></tr>
                    </tbody>
                </table>

                <div class="section-title">Schooling points</div>
                    <table class="qrs-table-extra">
                    <thead>
                        <tr>
                        <th colspan="2">max points</th>
                        <th>actual points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="2">2.00</td><td>1.85</td></tr>
                        <tr><td colspan="2">2.00</td><td>1.93</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">1.00</td><td>1.00</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">2.00</td><td>2.00</td></tr>
                    </tbody>
                    </table>

                <div class="section-title">Awards points</div>
                    <table class="qrs-table-extra">
                    <thead>
                        <tr>
                        <th colspan="2">max points</th>
                        <th>actual points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="2">5.00</td><td>4.80</td></tr>
                        <tr><td colspan="2">5.00</td><td>1.00</td></tr>
                    </tbody>
                    </table>
                
                <div class="section-title">PFT points</div>
                    <table class="qrs-table-extra">
                    <thead>
                        <tr>
                        <th>min points</th>
                        <th>max points</th>
                        <th>actual points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td><br><br>(-)<br><br></td>                  
                        <td>5.00</td>
                        <td>4.18</td>
                        </tr>
                    </tbody>
                    </table>
                    <div style="margin-top:8px">
                        <table class="qrs-table" style="width:100%">
                        <tbody>
                            <tr><td colspan="2" rowspan="3" style="background-color: #9dc791; font-size: large;"><strong>QRS SCORE:</strong></td><td rowspan="3" style="background-color: #9dc791; font-size: large; font-weight: bold;">30.05</td></tr>
                            <tr></tr>
                            <tr></tr>
                        </tbody>
                        </table>
                    </div>
                </div>

                <!-- LTC tab -->
                <div id="t-col" class="tab-panel" style="display:none">
                <table class="qrs-table">
                    <thead>
                    <tr>
                        <th>COL : max year</th>
                        <th>COL : max points</th>
                        <th>COL : actual points</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td>2.00</td><td>6.00</td><td>6.00</td></tr>
                    <tr><td>2.00</td><td>6.00</td><td>2.64</td></tr>
                    <tr><td>2.00</td><td>6.00</td><td>6.00</td></tr>
                    <tr><td>1.00</td><td>3.00</td><td>0.92</td></tr>
                    <tr><td>1.00</td><td>3.00</td><td>3.00</td></tr>
                    <tr><td>1.00</td><td>6.00</td><td>6.00</td></tr>
                    <tr><td>1.00</td><td>2.00</td><td>-</td></tr>
                    <tr><td>1.00</td><td>2.00</td><td>2.00</td></tr>
                    <tr><td>1.00</td><td>3.00</td><td>2.17</td></tr>
                    <tr><td>1.00</td><td>3.00</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>-</td><td>-</td><td>-</td></tr>
                    <tr><td>2.00</td><td>6.00</td><td>5.08</td></tr>
                    <tr><td>1.50</td><td>7.00</td><td>3.21</td></tr>
                    <tr><td>2.00</td><td>5.00</td><td>2.50</td></tr>
                    <tr><td>2.00</td><td>5.00</td><td>-</td></tr>
                    <tr><td>2.00</td><td>3.00</td><td>3.00</td></tr>
                    <tr><td>4.00</td><td>3.00</td><td>1.87</td></tr>
                    <tr><td>4.00</td><td>3.00</td><td>3.00</td></tr>
                    <tr><td>8.00</td><td>5.00</td><td>4.11</td></tr>
                    <tr><td>8.00</td><td>3.00</td><td>0.77</td></tr>
                    <tr><td>8.00</td><td>3.00</td><td>-</td></tr>
                    <tr><td>8.00</td><td>3.00</td><td>3.00</td></tr>
                    <tr><td>8.00</td><td>3.00</td><td>0.20</td></tr>
                    </tbody>
                </table>


                <div class="section-title">Schooling points</div>
                    <table class="qrs-table-extra">
                    <thead>
                        <tr>
                        <th colspan="2">max points</th>
                        <th>actual points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="2">2.00</td><td>1.85</td></tr>
                        <tr><td colspan="2">2.00</td><td>1.93</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">1.00</td><td>1.00</td></tr>
                        <tr><td colspan="2">-</td><td>-</td></tr>
                        <tr><td colspan="2">2.00</td><td>2.00</td></tr>
                    </tbody>
                    </table>

                <div class="section-title">Awards points</div>
                    <table class="qrs-table-extra">
                    <thead>
                        <tr>
                        <th colspan="2">max points</th>
                        <th>actual points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="2">5.00</td><td>4.80</td></tr>
                        <tr><td colspan="2">5.00</td><td>1.00</td></tr>
                    </tbody>
                    </table>
                
                <div class="section-title">PFT points</div>
                    <table class="qrs-table-extra">
                    <thead>
                        <tr>
                        <th>min points</th>
                        <th>max points</th>
                        <th>actual points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td><br><br>(-)<br><br></td>                  
                        <td>5.00</td>
                        <td>4.18</td>
                        </tr>
                    </tbody>
                    </table>
                    <div style="margin-top:8px">
                        <table class="qrs-table" style="width:100%">
                        <tbody>
                            <tr><td colspan="2" rowspan="3" style="background-color: #9dc791; font-size: large;"><strong>QRS SCORE:</strong></td><td rowspan="3" style="background-color: #9dc791; font-size: large; font-weight: bold;">30.05</td></tr>
                            <tr></tr>
                            <tr></tr>
                        </tbody>
                        </table>
                    </div>
                </div>

            </div>
            </div>
        </div>
    <!-- </div> -->
</div>
@endsection
@section('scripts')
    
@endsection