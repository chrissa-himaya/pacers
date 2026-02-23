@extends('layouts.app')
@section('content')

    <style>
        :root {
            --muted: #666;
            --green: #dfefe0;
            --accent: #9dc791;
            --bg: #f6f7f7;
            --ok: #2e7d32;
            --warn: #ef6c00;
            --bad: #c62828;
        }

        /* ===== BASE ===== */
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 12px;
            background: var(--bg);
            color: #111;
        }

        .page {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .panel {
            background: #fff;
            border: 1px solid #d0d0d0;
            border-radius: 4px;
            padding: 10px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
            overflow-x: auto;
        }

        .sub-panel {
            display: grid;
            grid-template-columns: 50% 40%;
            gap: 12px;
        }

        /* ===== HEADER ===== */
        .photo {
            width: 120px;
            height: 120px;
            border: 2px solid #4f8f5a;
            border-radius: 4px;
            overflow: hidden;
        }

        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        h1 {
            font-size: 15px;
            margin: 0
        }

        h2 {
            font-size: 20px;
            margin: 0 0 4px 0
        }

        .meta h3 {
            font-size: 13px;
            font-weight: normal;
            margin: 1px 0;
        }

        .meta h3 strong {
            font-weight: 600
        }

        .backbtn {
            float: left;
            margin-bottom: 6px
        }

        .printbtn {
            float: right;
            margin-right: 6px;
            background: #2e7d32;
            color: #fff;
            border: none;
            padding: 6px 12px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 3px;
        }

        .printbtn:hover {
            background: #1b5e20
        }

        /* ===== TABLES ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            table-layout: fixed;
        }

        thead th {
            background: var(--green);
            padding: 6px;
            border: 1px solid #d8d8d8;
            font-weight: 700;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        th,
        td {
            border: 1px solid #eaeaea;
            padding: 6px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            font-variant-numeric: tabular-nums;
        }

        tbody tr:nth-child(even) {
            background: #fafafa
        }

        tbody tr:hover {
            background: #eef6ee
        }

        .left-col {
            text-align: left
        }

        .small {
            font-size: 11px;
            color: var(--muted)
        }

        .section-title {
            font-weight: 700;
            background: #f1f1f1;
            padding: 4px;
            margin: 8px 0 2px 0;
            border-radius: 3px;
        }

        /* Career table sizing */
        .career-table tr,
        .qrs-table tr {
            height: 28px
        }

        .career-table td:nth-child(1) {
            width: 12%
        }

        .career-table td:nth-child(2) {
            width: 38%
        }

        .career-table td:nth-child(n+3) {
            width: 8%
        }

        /* ===== TABS ===== */
        .qrs-wrap {
            display: flex;
            flex-direction: column
        }

        .tabs {
            display: flex;
            gap: 6px;
        }

        .tab {
            flex: 1;
            padding: 8px;
            border-radius: 4px;
            background: #f3f3f3;
            border: 1px solid #d0d0d0;
            text-align: center;
            cursor: pointer;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .tab.active {
            background: #4f8f5a;
            color: #fff;
            border-color: #4f8f5a;
        }

        .tab-body {
            flex: 1;
            overflow: auto
        }

        /* QRS tables */
        .qrs-table th {
            background: #f6f8f6;
            border: 1px solid #d6d6d6;
        }

        .qrs-table td {
            text-align: center
        }

        .qrs-table-extra {
            width: 100%;
            text-align: center;
            border-collapse: collapse;
        }

        .qrs-table-extra th,
        .qrs-table-extra td {
            border: 1px solid #e0e0e0;
            padding: 6px;
        }

        /* ===== ANALYSIS SECTION ===== */
        .analysis-wrap {
            display: grid;
            grid-template-columns: 55% 45%;
            gap: 10px;
        }

        .analysis-panel {
            border: 1px solid #d0d0d0;
            background: #fff;
            padding: 6px;
        }

        /* Status colors */
        .ok {
            color: var(--ok);
            font-weight: 700
        }

        .warn {
            color: var(--warn);
            font-weight: 700
        }

        .bad {
            color: var(--bad);
            font-weight: 700
        }

        /* ===== HORIZONTAL BAR CHART (STACKED VERTICAL) ===== */

        .bar-chart {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .row {
            display: grid;
            grid-template-columns: 160px 1fr;
            align-items: center;
            gap: 10px;
        }

        .bars.stacked {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        /* ACTUAL (TOP BAR) */
        .bar.actual {
            position: relative;
            height: 20px;
            background: #4caf50;
            width: calc(var(--val) * 20px);
            border-radius: 2px;
        }

        /* MAX (BOTTOM BAR) */
        .bar.max {
            position: relative;
            height: 20px;
            background: #cfcfcf;
            width: calc(var(--val) * 20px);
            border-radius: 2px;
            color: #333;
            font-weight: 700;
            font-size: 12px;
        }

        /* VALUE LABEL (used by both bars if needed) */
        .bar-value {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            white-space: nowrap;
            pointer-events: none;
        }

        /* darker text for max bar */
        .bar.max .bar-value {
            color: #333;
        }

        /* ===== RESPONSIVE ===== */
        @media(max-width:1000px) {
            .sub-panel {
                grid-template-columns: 1fr
            }

            .analysis-wrap {
                grid-template-columns: 1fr
            }
        }

        /* ===== PRINT ===== */
        @media print {

            .backbtn,
            .printbtn {
                display: none !important
            }

            body {
                background: #fff;
                margin: 0
            }

            .panel {
                border: none;
                box-shadow: none;
                padding: 0
            }

            table {
                font-size: 11px
            }

            th {
                background: #eaeaea !important;
                color: #000 !important
            }

            tr,
            td,
            th {
                page-break-inside: avoid
            }

            .section-title {
                background: #f0f0f0 !important;
                color: #000
            }

            /* FORCE COLOR PRINTING */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* ENSURE BAR COLORS PRINT */
            .bar.actual {
                background: #4caf50 !important;
            }

            .bar.max {
                background: #cfcfcf !important;
            }

            .bar-value {
                color: #fff !important;
            }

            .bar.max .bar-value {
                color: #333 !important;
            }

            /* SHOW ALL TAB CONTENT */
            .tab-panel {
                display: block !important;
                page-break-inside: avoid;
            }

            /* HIDE TAB BUTTONS */
            .tabs {
                display: none !important;
            }

            /* OPTIONAL: spacing between rank sections */
            .tab-panel {
                margin-bottom: 16px;
            }
        }

        .career-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        /* Give more space to CATEGORY + CRITERIA */
        .career-table col.col-category {
            width: 8%;
        }

        .career-table col.col-criteria {
            width: 8%;
        }

        /* 6 rank columns x 4% = 24% total (tight) */
        .career-table col.col-rank {
            width: 4%;
        }

        .career-table th,
        .career-table td {
            padding: 6px 8px;
        }

        /* Wrap only text columns */
        .career-table .left-col {
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        /* Make rank columns compact + centered */
        .career-table .rank-col {
            text-align: center;
            white-space: nowrap;
            padding: 3px 2px;
            font-size: 11px;
            font-weight: bold;
            font-variant-numeric: tabular-nums;
            /* makes numbers look aligned */
        }
    </style>

    <div class="card" style="margin-left: -15px;">
        <div class="panel">
            <header style="text-align: center;">
                <div class="top-center">
                    P H I L I P P I N E &nbsp; A R M Y <input class="backbtn" type="button" value="Back"
                        onclick="window.location.href='index - original.html'"><input type="button" value="Print OCAR"
                        class="printbtn" onclick="window.print()"><br>
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
                        @php
                            function yearsToYrM($decimalYears): string {
                                if (!$decimalYears || $decimalYears == 0) return '-';
                                $totalMonths = round($decimalYears * 12);
                                $yrs = intdiv($totalMonths, 12);
                                $mos = $totalMonths % 12;
                                if ($yrs > 0 && $mos > 0) return "{$yrs}yr{$mos}m";
                                if ($yrs > 0) return "{$yrs}yr";
                                return "{$mos}m";
                            }
                        @endphp
                    <table class="career-table">
                        <colgroup>
                            <col class="col-category">
                            <col class="col-criteria">
                            @foreach ($ranks as $rank)
                                <col class="col-rank">
                            @endforeach
                        </colgroup>

                        <thead style="height: 72px;">
                            <tr>
                                <th class="left-col">CATEGORY</th>
                                <th class="left-col">CRITERIA</th>
                                <th class="rank-col">2LT</th>
                                <th class="rank-col">1LT</th>
                                <th class="rank-col">CPT</th>
                                <th class="rank-col">MAJ</th>
                                <th class="rank-col">LTC</th>
                                <th class="rank-col">COL</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($types as $type)
                                @php
                                    $criteria = $type->assignments;
                                    $rowspan = max(1, $criteria->count());
                                @endphp

                                @forelse ($criteria as $i => $assignment)
                                    <tr>
                                        @if ($i === 0)
                                            <td rowspan="{{ $rowspan }}" class="left-col">
                                                {{ $type->name }}
                                            </td>
                                        @endif

                                        <td class="left-col">{{ $assignment->name }}</td>

                                        @foreach ($ranks as $rank)
                                            @php
                                                $val = data_get($totals, $assignment->id . '.' . $rank, 0);
                                            @endphp
                                            <td class="rank-col">
                                                {{ yearsToYrM($val) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="left-col">{{ $type->name }}</td>
                                        <td class="left-col">—</td>
                                        @foreach ($ranks as $rank)
                                            <td class="rank-col">0</td>
                                        @endforeach
                                    </tr>
                                @endforelse
                            @endforeach
                        </tbody>
                    </table>
                
                <div class="section-title">Professional Preparation and Development</div>
                    <table class="schooling-table">
                        <thead>
                            <tr>
                                <th>CATEGORY</th>
                                <th>CRITERIA</th>
                                <th colspan="3">COURSE</th>
                                <th>RATING</th>
                                <th>STANDING</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schoolingCriteria as $index => $criteria)
                                @php
                                    $schoolingEntry = $schoolingMap->get($criteria->id);
                                    $isMultiple = $criteria->id == 44 && $schoolingEntry;
                                    $schooling = $isMultiple ? $schoolingEntry->first() : $schoolingEntry;
                                @endphp
                                <tr>
                                    @if ($index === 0)
                                        <td rowspan="{{ $schoolingCriteria->count() }}" class="category-cell">
                                            Professional Preparation and Development
                                        </td>
                                    @endif
                                    <td>{{ $criteria->name }}</td>
                                    <td colspan="3">
                                        @if ($isMultiple && $schoolingEntry->isNotEmpty())
                                            {{ $schoolingEntry->map(fn($s) => trim(($s->schoolingnames->name ?? '') . ' ' . ($s->classname ?? '')))->filter()->implode(', ') }}
                                        @elseif ($schooling && $schooling->date_completed)
                                            {{ $schooling->schoolingnames->name ?? '' }} {{ $schooling->classname ?? '' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="{{ !$schooling || !$schooling->rating ? 'greyed' : '' }}">
                                        {{ $schooling->rating ?? '-' }}
                                    </td>
                                    <td class="{{ !$schooling || !$schooling->standing ? 'greyed' : '' }}">
                                        {{ $schooling->standing ?? '-' }} / {{ $schooling->total_student ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                <div class="section-title">Awards and Decorations</div>
                    <table class="award-table" id="awards-deco-table">
                        <thead>
                            <tr>
                                <th>CATEGORY</th>
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
                                <td class="category-cell">Awards and Decorations</td>
                                @foreach ([1, 2, 3, 4, 5, 6] as $rankId)
                                    @php
                                        $rankAwards = $awardsMap->get($rankId, collect());
                                    @endphp
                                    <td class="{{ $rankAwards->isEmpty() ? 'greyed' : '' }}">
                                        @forelse ($rankAwards as $award)
                                            {{ $award['name'] }}-{{ $award['count'] }}<br>
                                        @empty
                                            -
                                        @endforelse
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>

                <div class="section-title">Physical Fitness Test</div>
                    <table class="pft-table">
                        <thead>
                            <tr>
                                <th class="category-cell" rowspan="4">PFT</th>
                                @foreach (['2LT', '1LT', 'CPT', 'MAJ', 'LTC', 'COL'] as $rank)
                                    <th>{{ $rank }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Rating</td>
                                @foreach (['2LT', '1LT', 'CPT', 'MAJ', 'LTC', 'COL'] as $rank)
                                    @php $pft = $pftMap->get($rank); @endphp
                                    <td class="{{ !$pft ? 'greyed' : '' }}">
                                        {{ $pft ? number_format($pft->rating, 2) : '' }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>Date Taken</td>
                                @foreach (['2LT', '1LT', 'CPT', 'MAJ', 'LTC', 'COL'] as $rank)
                                    @php $pft = $pftMap->get($rank); @endphp
                                    <td class="{{ !$pft ? 'greyed' : '' }}">
                                        {{ $pft ? \Carbon\Carbon::parse($pft->date_taken)->format('d/M/Y') : '' }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>Supervising Unit</td>
                                @foreach (['2LT', '1LT', 'CPT', 'MAJ', 'LTC', 'COL'] as $rank)
                                    @php $pft = $pftMap->get($rank); @endphp
                                    <td class="{{ !$pft ? 'greyed' : '' }}">
                                        {{ $pft->supervising_unit ?? '' }}
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
            </div>

            <div class="panel qrs-wrap2">
                <div class="section-title" style="text-align: center;">QRS Requirements</div>
                    <div class="tabs">
                        @foreach ($rankIdMap as $rankLabel => $rankId)
                            <div class="tab @if($loop->first) active @endif" 
                                data-tab="t-{{ strtolower($rankLabel) }}">
                                {{ $rankLabel }}
                            </div>
                        @endforeach
                    </div>

                <div class="tab-body">
                    @foreach ($rankIdMap as $rankLabel => $rankId)
                    @php
                        $tabId = 't-' . strtolower($rankLabel);
                    @endphp

                        <div id="{{ $tabId }}" class="tab-panel" @if(!$loop->first) style="display:none" @endif>
                            <table class="assignmentpts-table">
                                <thead>
                                    <tr>
                                        <th>{{ $rankLabel }} : max year</th>
                                        <th>{{ $rankLabel }} : max points</th>
                                        <th>{{ $rankLabel }} : gained points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($types as $type)
                                        @foreach ($type->assignments as $assignment)
                                            @php
                                                $sd         = $sourcedataMap[$assignment->id][$rankId] ?? null;
                                                $maxYear    = $sd ? number_format($sd->max_month / 12, 2) : '-';
                                                $maxPoints  = $sd ? number_format($sd->max_point, 2) : '-';

                                                // Use computed_points for gained, capped at max_point
                                                $gained     = data_get($computedTotals, $assignment->id . '.' . $rankLabel, 0);
                                                $maxPt      = $sd ? (float) $sd->max_point : null;
                                                $gainedCapped = $maxPt !== null ? min((float) $gained, $maxPt) : (float) $gained;
                                                $gainedDisplay = $gainedCapped > 0 ? number_format($gainedCapped, 2) : '-';
                                            @endphp
                                        <tr>
                                            <td>{{ $maxYear }}</td>
                                            <td>{{ $maxPoints }}</td>
                                            <td>{{ $gainedDisplay }}</td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>

                        <div class="section-title">Schooling points</div>
                            <table class="schoolingpts-table">
                                <thead>
                                    <tr>
                                        <th>max points</th>
                                        <th>actual points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($schoolingCriteria as $criteria)
                                        @php
                                            $point = $schoolingPoints[$criteria->id][$rankId] ?? ['max' => null, 'actual' => null];
                                            $hasMax = !is_null($point['max']);
                                            $hasActual = !is_null($point['actual']) && $point['actual'] > 0;
                                        @endphp
                                        <tr>
                                            <td class="{{ !$hasMax ? 'greyed' : '' }}">
                                                {{ $hasMax ? number_format($point['max'], 2) : '-' }}
                                            </td>
                                            <td class="{{ !$hasActual ? 'greyed' : '' }}">
                                                {{ $hasActual ? number_format($point['actual'], 2) : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        {{-- Awards Points --}}
                        <div class="section-title">Awards points</div>
                            <table class="awardpts-table" id="awards-points-table">
                                <thead>
                                    <tr>
                                        <th>max points</th>
                                        <th>actual points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $ap = $awardsPoints[$rankLabel] ?? [];
                                        $currentMax    = $ap['current_max'] ?? null;
                                        $prevMin       = $ap['prev_min'] ?? null;
                                        $currentActual = $ap['current_actual'] ?? null;
                                        $prevActual    = $ap['prev_actual'] ?? null;
                                    @endphp
                                    <tr class="awards-points-row">
                                        <td class="{{ is_null($currentMax) ? 'greyed' : '' }}">
                                            {{ !is_null($currentMax) ? number_format($currentMax, 1) : '-' }}
                                        </td>
                                        <td class="{{ is_null($currentActual) ? 'greyed' : '' }}">
                                            {{ !is_null($currentActual) ? number_format($currentActual, 2) : '-' }}
                                        </td>
                                    </tr>
                                    <tr class="awards-points-row">
                                        <td class="{{ is_null($prevMin) ? 'greyed' : '' }}">
                                            {{ !is_null($prevMin) ? number_format($prevMin, 1) : '-' }}
                                        </td>
                                        <td class="{{ is_null($prevActual) ? 'greyed' : '' }}">
                                            {{ !is_null($prevActual) ? number_format($prevActual, 2) : '-' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        {{-- PFT Points --}}
                        <div class="section-title">PFT points</div>
                            <table class="pftpts-table">
                                <thead>
                                    <tr>
                                        <th>max points</th>
                                        <th>actual points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $pp = $pftPoints[$rankLabel] ?? ['max' => null, 'actual' => null];
                                        $hasMax    = !is_null($pp['max']);
                                        $hasActual = !is_null($pp['actual']);
                                    @endphp
                                    <tr>
                                        <td class="{{ !$hasMax ? 'greyed' : '' }}">
                                            {{ $hasMax ? number_format($pp['max'], 1) : '-' }}
                                        </td>
                                        <td class="{{ !$hasActual ? 'greyed' : '' }}">
                                            {{ $hasActual ? number_format($pp['actual'], 2) : '-' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        {{-- QRS Score --}}
                        <div style="margin-top:8px">
                            <table class="qrs-table" style="width:100%">
                                <tbody>
                                    <tr>
                                        <td rowspan="3" 
                                            style="background-color: #9dc791; font-size: large;">
                                            <strong>QRS SCORE:</strong>
                                        </td>
                                        <td rowspan="3"
                                            style="background-color: #9dc791; font-size: large; font-weight: bold;">
                                            {{ number_format($qrsScores[$rankLabel] ?? 0, 2) }}
                                        </td>
                                    </tr>
                                    <tr></tr>
                                    <tr></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- </div> -->
    </div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.qrs-wrap2 .tab');
            const panels = document.querySelectorAll('.qrs-wrap2 .tab-panel');

            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    // Remove active from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    // Hide all panels
                    panels.forEach(p => p.style.display = 'none');

                    // Activate clicked tab
                    tab.classList.add('active');
                    // Show matching panel
                    const target = document.getElementById(tab.dataset.tab);
                    if (target) target.style.display = 'block';
                });
            });
        });
    </script>

    <script>
        function syncAwardsHeight() {
            const decoTable = document.getElementById('awards-deco-table');
            const pointsTable = document.getElementById('awards-points-table');
            if (!decoTable || !pointsTable) return;

            // Get the tbody rows of the decorations table
            const decoRows = decoTable.querySelectorAll('tbody tr');
            const pointRows = pointsTable.querySelectorAll('tbody tr.awards-points-row');

            if (decoRows.length === 0) return;

            // Total height of awards deco tbody
            const totalDecoHeight = Array.from(decoRows)
                .reduce((sum, row) => sum + row.offsetHeight, 0);

            // Split evenly between the 2 points rows
            const rowHeight = Math.floor(totalDecoHeight / pointRows.length);
            pointRows.forEach(row => {
                row.style.height = rowHeight + 'px';
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const tabs   = document.querySelectorAll('.qrs-wrap2 .tab');
            const panels = document.querySelectorAll('.qrs-wrap2 .tab-panel');

            function syncAwardsHeight(activePanel) {
                const decoTable   = document.getElementById('awards-deco-table');
                const pointsTable = activePanel.querySelector('.awardpts-table');
                const pftMain     = document.querySelector('.pft-table');
                const pftPoints   = activePanel.querySelector('.pftpts-table');

                // Sync awards height
                if (decoTable && pointsTable) {
                    const decoRows  = decoTable.querySelectorAll('tbody tr');
                    const pointRows = pointsTable.querySelectorAll('tbody tr.awards-points-row');

                    if (decoRows.length > 0 && pointRows.length > 0) {
                        const totalDecoHeight = Array.from(decoRows)
                            .reduce((sum, row) => sum + row.offsetHeight, 0);
                        const rowHeight = Math.floor(totalDecoHeight / pointRows.length);
                        pointRows.forEach(row => row.style.height = rowHeight + 'px');
                    }
                }

                // Sync PFT height
                if (pftMain && pftPoints) {
                    const totalHeight  = pftMain.offsetHeight;
                    const theadHeight  = pftPoints.querySelector('thead').offsetHeight;
                    const singleRow    = pftPoints.querySelector('tbody tr');
                    pftPoints.style.height = totalHeight + 'px';
                    if (singleRow) {
                        singleRow.style.height = (totalHeight - theadHeight) + 'px';
                    }
                }
            }

            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    tabs.forEach(t => t.classList.remove('active'));
                    panels.forEach(p => p.style.display = 'none');
                    tab.classList.add('active');

                    const target = document.getElementById(tab.dataset.tab);
                    if (target) {
                        target.style.display = 'block';
                        // Small delay to let DOM render before measuring
                        setTimeout(() => syncAwardsHeight(target), 10);
                    }
                });
            });

            // Run on initial load — find the first visible panel
            const firstPanel = document.querySelector('.tab-panel');
            if (firstPanel) {
                setTimeout(() => syncAwardsHeight(firstPanel), 10);
            }

            window.addEventListener('resize', () => {
                const activePanel = document.querySelector('.tab-panel[style*="block"], .tab-panel:not([style])');
                if (activePanel) syncAwardsHeight(activePanel);
            });
        });
    </script>

@endsection