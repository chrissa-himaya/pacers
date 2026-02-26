<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Officer;
use App\Models\AssignmentHistory;
use App\Models\Schooling;
use App\Models\DateRank;
use App\Models\Rank;
use App\Models\Pamu;
use App\Models\Scopes\PMCodeScope;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;


class HomeController extends Controller
{
    private const MAX_TENURE = [
        'GEN'   => 3,
        'LTGEN' => 3,
        'MGEN'  => 3,
        'BGEN'  => 5,
        'COL'   => 10,
        'LTC'   => 7,
        'MAJ'   => 6,
        'CPT'   => 6,
    ];

    private const COMPULSORY_RETIREMENT_AGE = 57;

    private const RANK_ORDER        = ["GEN","LTGEN","MGEN","BGEN","COL","LTC","MAJ","CPT","1LT","2LT"];
    private const RANK_2LT_TO_COL   = ["COL","LTC","MAJ","CPT","1LT","2LT"];
    private const RANK_CPT_TO_COL   = ["COL","LTC","MAJ","CPT"];
    private const RANK_LTC_TO_COL   = ["COL","LTC"];
    private const RANK_2LT_TO_CPT   = ["CPT","1LT","2LT"];
    private const PAMU_RANKS        = ["2LT","1LT","CPT","MAJ","LTC","COL"];

    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─────────────────────────────────────────────────────────────
    //  Main dashboard entry point — delegates to private builders
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $today = Carbon::today();

        $stats      = $this->buildStats($today);
        $chartData  = $this->buildChartData();
        [$pamuRecap, $pamuTotals] = $this->buildPamuRecap();
        $pamuRanks  = self::PAMU_RANKS;
        $tab2Data   = $this->buildTab2();
        $tab3Data   = $this->buildTab3();
        $tab4Data   = $this->buildTab4($today);
        $tab5Data   = $this->buildTab5($today);
        $tab6Data   = $this->buildTab6();
        $tab7Data   = $this->buildTab7();

        return view('dashboard', compact(
            'stats', 'chartData',
            'pamuRecap', 'pamuRanks', 'pamuTotals',
            'tab2Data', 'tab3Data', 'tab4Data',
            'tab5Data', 'tab6Data', 'tab7Data'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    //  Private builder methods
    // ─────────────────────────────────────────────────────────────

    private function buildStats(Carbon $today): array
    {
        $sexExpr   = 'UPPER(TRIM(SEX))';
        $rankExpr  = 'UPPER(TRIM(RANK))';
        $afposExpr = 'TRIM(AFPOS)';
        $in12      = $today->copy()->addMonthsNoOverflow(12);

        $totalOfficers = Officer::count();

        $retiring12Total = Officer::whereNotNull('RET')
            ->whereRaw("TRIM(RET) != ''")
            ->whereDate('RET', '>=', $today)
            ->whereDate('RET', '<=', $in12)
            ->count();

        $male       = Officer::whereRaw("{$sexExpr} IN ('M','MALE')")->count();
        $female     = Officer::whereRaw("{$sexExpr} IN ('F','FEMALE')")->count();
        $assigned   = Officer::whereNotNull('designation_id')->count();
        $unassigned = Officer::whereNull('designation_id')->count();

        $totalAfpos = Officer::whereNotNull('AFPOS')
            ->whereRaw("{$afposExpr} != ''")
            ->distinct('AFPOS')->count('AFPOS');

        $topRankRow = Officer::whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->selectRaw("{$rankExpr} as rank, COUNT(*) as total")
            ->groupBy('rank')->orderByDesc('total')->first();

        $topAfposRow = Officer::whereNotNull('AFPOS')->whereRaw("{$afposExpr} != ''")
            ->selectRaw("{$afposExpr} as afpos, COUNT(*) as total")
            ->groupBy('afpos')->orderByDesc('total')->first();

        return [
            'total_officers'      => $totalOfficers,
            'male_officers'       => $male,
            'female_officers'     => $female,
            'total_afpos'         => $totalAfpos,
            'assigned_officers'   => $assigned,
            'unassigned_officers' => $unassigned,
            'assigned_rate'       => $totalOfficers > 0 ? round(($assigned / $totalOfficers) * 100, 1) : 0,
            'top_rank'            => $topRankRow->rank ?? null,
            'top_rank_total'      => $topRankRow ? (int) $topRankRow->total : 0,
            'top_afpos'           => $topAfposRow->afpos ?? null,
            'top_afpos_total'     => $topAfposRow ? (int) $topAfposRow->total : 0,
            'retiring_12_months'  => $retiring12Total,
        ];
    }

    private function buildChartData(): array
    {
        $rankExpr  = 'UPPER(TRIM(RANK))';
        $sexExpr   = 'UPPER(TRIM(SEX))';
        $afposExpr = 'TRIM(AFPOS)';
        $orderList = '"' . implode('","', self::RANK_ORDER) . '"';

        $rankData = Officer::whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->selectRaw("{$rankExpr} as rank")
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN {$sexExpr} IN ('M','MALE') THEN 1 ELSE 0 END) as male")
            ->selectRaw("SUM(CASE WHEN {$sexExpr} IN ('F','FEMALE') THEN 1 ELSE 0 END) as female")
            ->selectRaw("SUM(CASE WHEN designation_id IS NOT NULL THEN 1 ELSE 0 END) as assigned")
            ->selectRaw("SUM(CASE WHEN designation_id IS NULL THEN 1 ELSE 0 END) as unassigned")
            ->groupBy('rank')
            ->orderByRaw("(FIELD(rank, {$orderList}) = 0) ASC")
            ->orderByRaw("FIELD(rank, {$orderList}) ASC")
            ->get();

        $afposData = [];
        foreach ($rankData as $rankRow) {
            $r = $rankRow->rank;
            $afposData[$r] = Officer::whereRaw("{$rankExpr} = ?", [$r])
                ->whereNotNull('AFPOS')->whereRaw("{$afposExpr} != ''")
                ->selectRaw("{$afposExpr} as AFPOS, COUNT(*) as count")
                ->groupBy('AFPOS')->orderByDesc('count')->limit(6)->get();
        }

        return [
            'ranks'      => $rankData->pluck('rank')->toArray(),
            'total'      => $rankData->pluck('total')->map(fn($v) => (int)$v)->toArray(),
            'male'       => $rankData->pluck('male')->map(fn($v) => (int)$v)->toArray(),
            'female'     => $rankData->pluck('female')->map(fn($v) => (int)$v)->toArray(),
            'assigned'   => $rankData->pluck('assigned')->map(fn($v) => (int)$v)->toArray(),
            'unassigned' => $rankData->pluck('unassigned')->map(fn($v) => (int)$v)->toArray(),
            'afpos'      => $afposData,
        ];
    }

    private function buildPamuRecap(): array
    {
        $rankExpr  = 'UPPER(TRIM(RANK))';
        $ranks     = self::PAMU_RANKS;
        $pamus     = Pamu::orderBy('code')->get();

        $pamuOfficerData = Officer::whereNotNull('pamu_id')
            ->whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->whereRaw("{$rankExpr} IN ('" . implode("','", $ranks) . "')")
            ->selectRaw("pamu_id, {$rankExpr} as rank, COUNT(*) as total")
            ->groupBy('pamu_id', 'rank')
            ->get();

        $pamuMap = [];
        foreach ($pamuOfficerData as $row) {
            $pamuMap[$row->pamu_id][$row->rank] = (int) $row->total;
        }

        $pamuRecap = [];
        foreach ($pamus as $pamu) {
            $row      = ['code' => $pamu->code, 'name' => $pamu->name];
            $rowTotal = 0;
            foreach ($ranks as $r) {
                $cnt       = $pamuMap[$pamu->id][$r] ?? 0;
                $row[$r]   = $cnt;
                $rowTotal += $cnt;
            }
            $row['total'] = $rowTotal;
            if ($rowTotal > 0) {
                $pamuRecap[] = $row;
            }
        }

        $pamuTotals = [];
        $grandTotal = 0;
        foreach ($ranks as $r) {
            $pamuTotals[$r] = array_sum(array_column($pamuRecap, $r));
            $grandTotal    += $pamuTotals[$r];
        }
        $pamuTotals['total'] = $grandTotal;

        return [$pamuRecap, $pamuTotals];
    }

    private function buildTab2(): array
    {
        $rankExpr  = 'UPPER(TRIM(RANK))';
        $afposExpr = 'TRIM(AFPOS)';
        $ranks     = self::RANK_2LT_TO_COL;

        $data = Officer::whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->whereRaw("{$rankExpr} IN ('" . implode("','", $ranks) . "')")
            ->whereNotNull('AFPOS')->whereRaw("{$afposExpr} != ''")
            ->selectRaw("{$rankExpr} as rank, {$afposExpr} as afpos, COUNT(*) as count")
            ->groupBy('rank', 'afpos')->get();

        $perRank = [];
        foreach ($data as $row) {
            $perRank[$row->rank][$row->afpos] = (int) $row->count;
        }

        return [
            'ranks'     => $ranks,
            'afposList' => $data->pluck('afpos')->unique()->sort()->values()->toArray(),
            'data'      => $perRank,
        ];
    }

    private function buildTab3(): array
    {
        $rankExpr = 'UPPER(TRIM(RANK))';
        $ranks    = self::RANK_2LT_TO_COL;

        $popData = Officer::whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->whereRaw("{$rankExpr} IN ('" . implode("','", $ranks) . "')")
            ->selectRaw("{$rankExpr} as rank, COUNT(*) as total")
            ->groupBy('rank')->get()->keyBy('rank');

        $pyramidRanks  = array_reverse($ranks);
        $pyramidTotals = array_map(fn($r) => (int) ($popData[$r]->total ?? 0), $pyramidRanks);

        return ['ranks' => $pyramidRanks, 'totals' => $pyramidTotals];
    }

    private function buildTab4(Carbon $today): array
    {
        $rankExpr  = 'UPPER(TRIM(RANK))';
        $afposExpr = 'TRIM(AFPOS)';
        $ranks     = self::RANK_CPT_TO_COL;

        $officers = Officer::whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->whereRaw("{$rankExpr} IN ('" . implode("','", $ranks) . "')")
            ->whereNotNull('DOR')->whereRaw("TRIM(DOR) != ''")
            ->whereNotNull('AFPOS')->whereRaw("{$afposExpr} != ''")
            ->selectRaw("PM_CODE as pm_code, {$rankExpr} as rank, {$afposExpr} as afpos, DOR")
            ->get();

        $tenuredMap    = [];
        $notTenuredMap = [];
        $allTenureMap  = [];

        foreach ($officers as $off) {
            $r        = $off->rank;
            $a        = $off->afpos;
            $maxYears = self::MAX_TENURE[$r] ?? null;
            if ($maxYears === null) continue;

            try {
                $tenureYears = round(Carbon::parse($off->DOR)->diffInDays($today) / 365.25, 2);
            } catch (\Throwable $e) {
                continue;
            }

            if (!isset($allTenureMap[$r][$a])) {
                $allTenureMap[$r][$a] = ['sum' => 0, 'count' => 0];
            }
            $allTenureMap[$r][$a]['sum']   += $tenureYears;
            $allTenureMap[$r][$a]['count'] += 1;

            if ($tenureYears >= $maxYears) {
                $tenuredMap[$r][$a] = ($tenuredMap[$r][$a] ?? 0) + 1;
            } else {
                $notTenuredMap[$r][$a] = ($notTenuredMap[$r][$a] ?? 0) + 1;
            }
        }

        $afposCounts = [];
        foreach ($allTenureMap as $r => $arr) {
            foreach ($arr as $a => $vals) {
                $afposCounts[$a] = ($afposCounts[$a] ?? 0) + $vals['count'];
            }
        }
        arsort($afposCounts);
        $topAfpos = array_slice(array_keys($afposCounts), 0, 12);

        $tenuredCounts = $notTenuredCounts = $tenureAverages = [];
        foreach ($ranks as $r) {
            foreach ($topAfpos as $a) {
                $tenuredCounts[$r][$a]    = $tenuredMap[$r][$a] ?? 0;
                $notTenuredCounts[$r][$a] = $notTenuredMap[$r][$a] ?? 0;
                $d                        = $allTenureMap[$r][$a] ?? null;
                $tenureAverages[$r][$a]   = $d ? round($d['sum'] / $d['count'], 2) : 0;
            }
        }

        return [
            'ranks'      => $ranks,
            'afposList'  => $topAfpos,
            'tenured'    => $tenuredCounts,
            'notTenured' => $notTenuredCounts,
            'averages'   => $tenureAverages,
            'maxTenure'  => self::MAX_TENURE,
        ];
    }

    private function buildTab5(Carbon $today): array
    {
        $rankExpr  = 'UPPER(TRIM(RANK))';
        $afposExpr = 'TRIM(AFPOS)';
        $ranks     = self::RANK_LTC_TO_COL;

        $officers = Officer::whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->whereRaw("{$rankExpr} IN ('" . implode("','", $ranks) . "')")
            ->whereNotNull('DOB')->whereRaw("TRIM(DOB) != ''")
            ->whereNotNull('AFPOS')->whereRaw("{$afposExpr} != ''")
            ->selectRaw("PM_CODE as pm_code, {$rankExpr} as rank, {$afposExpr} as afpos, DOB")
            ->get();

        $ageMap = [];
        foreach ($officers as $row) {
            try {
                $age = Carbon::parse($row->DOB)->diffInYears($today);
            } catch (\Throwable $e) {
                continue;
            }
            $r = $row->rank;
            $a = $row->afpos;
            if (!isset($ageMap[$r][$a])) {
                $ageMap[$r][$a] = ['sum' => 0, 'count' => 0];
            }
            $ageMap[$r][$a]['sum']   += $age;
            $ageMap[$r][$a]['count'] += 1;
        }

        $afposCounts = [];
        foreach ($ageMap as $r => $arr) {
            foreach ($arr as $a => $vals) {
                $afposCounts[$a] = ($afposCounts[$a] ?? 0) + $vals['count'];
            }
        }
        arsort($afposCounts);
        $allAfpos = array_keys($afposCounts);
        $topAfpos = array_slice($allAfpos, 0, 12);

        $ageAverages = $ageCounts = [];
        foreach ($ranks as $r) {
            foreach ($allAfpos as $a) {
                $d                   = $ageMap[$r][$a] ?? null;
                $ageAverages[$r][$a] = $d ? round($d['sum'] / $d['count'], 1) : 0;
                $ageCounts[$r][$a]   = $d ? $d['count'] : 0;
            }
        }

        return [
            'ranks'        => $ranks,
            'afposList'    => $topAfpos,
            'allAfposList' => $allAfpos,
            'averages'     => $ageAverages,
            'counts'       => $ageCounts,
        ];
    }

    private function buildTab6(): array
    {
        $rankExpr  = 'UPPER(TRIM(RANK))';
        $afposExpr = 'TRIM(AFPOS)';
        $ranks     = self::RANK_2LT_TO_CPT;

        $currentCCPMs = AssignmentHistory::withoutGlobalScope(PMCodeScope::class)
            ->where('assignment_id', 24)
            ->whereNotNull('start_date')
            ->where(function ($q) { $q->whereNull('end_date')->orWhere('end_date', ''); })
            ->pluck('pm_code')->unique()->toArray();

        $ccOfficers = Officer::whereIn('PM_CODE', $currentCCPMs)
            ->whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->whereRaw("{$rankExpr} IN ('" . implode("','", $ranks) . "')")
            ->whereNotNull('AFPOS')->whereRaw("{$afposExpr} != ''")
            ->selectRaw("PM_CODE as pm_code, {$rankExpr} as rank, {$afposExpr} as afpos")
            ->get();

        $oacPMs = Schooling::withoutGlobalScope(PMCodeScope::class)
            ->where('assignment_id', 37)
            ->whereIn('pm_code', $currentCCPMs)
            ->pluck('pm_code')->unique()->toArray();

        $ccMap = [];
        foreach ($ccOfficers as $off) {
            $r      = $off->rank;
            $a      = $off->afpos;
            $hasOAC = in_array($off->pm_code, $oacPMs);
            if (!isset($ccMap[$r][$a])) {
                $ccMap[$r][$a] = ['with' => 0, 'without' => 0];
            }
            $ccMap[$r][$a][$hasOAC ? 'with' : 'without']++;
        }

        $afposCounts = [];
        foreach ($ccMap as $r => $arr) {
            foreach ($arr as $a => $vals) {
                $afposCounts[$a] = ($afposCounts[$a] ?? 0) + $vals['with'] + $vals['without'];
            }
        }
        arsort($afposCounts);

        return [
            'ranks'     => $ranks,
            'afposList' => array_slice(array_keys($afposCounts), 0, 12),
            'data'      => $ccMap,
        ];
    }

    private function buildTab7(): array
    {
        $rankExpr  = 'UPPER(TRIM(RANK))';
        $afposExpr = 'TRIM(AFPOS)';
        $ranks     = ["COL", "LTC"];

        $cgscPMs = Schooling::withoutGlobalScope(PMCodeScope::class)
            ->where('assignment_id', 39)
            ->pluck('pm_code')->unique();

        $cgscOfficers = Officer::whereIn('PM_CODE', $cgscPMs)
            ->whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->whereRaw("{$rankExpr} IN ('" . implode("','", $ranks) . "')")
            ->whereNotNull('AFPOS')->whereRaw("{$afposExpr} != ''")
            ->selectRaw("PM_CODE as pm_code, {$rankExpr} as rank, {$afposExpr} as afpos")
            ->get();

        $currentBnCdrPMs = AssignmentHistory::withoutGlobalScope(PMCodeScope::class)
            ->whereNotNull('start_date')
            ->where(function ($q) { $q->whereNull('end_date')->orWhere('end_date', ''); })
            ->pluck('pm_code')->unique()->toArray();

        $cgscMap = [];
        foreach ($cgscOfficers as $off) {
            $r         = $off->rank;
            $a         = $off->afpos;
            $isCurrent = in_array($off->pm_code, $currentBnCdrPMs);
            if (!isset($cgscMap[$r][$a])) {
                $cgscMap[$r][$a] = ['current' => 0, 'not_designated' => 0];
            }
            $cgscMap[$r][$a][$isCurrent ? 'current' : 'not_designated']++;
        }

        $afposCounts = [];
        foreach ($cgscMap as $r => $arr) {
            foreach ($arr as $a => $vals) {
                $afposCounts[$a] = ($afposCounts[$a] ?? 0) + $vals['current'] + $vals['not_designated'];
            }
        }
        arsort($afposCounts);

        return [
            'ranks'     => $ranks,
            'afposList' => array_slice(array_keys($afposCounts), 0, 12),
            'data'      => $cgscMap,
        ];
    }

    // ─────────────────────────────────────────────────────────────
    //  AJAX Drill-down endpoints
    // ─────────────────────────────────────────────────────────────

    /**
     * GET /dashboard/assigned-officers?type=assigned|unassigned
     */
    public function assignedOfficers(Request $request)
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN);

        $type = $request->input('type', 'assigned'); // 'assigned' or 'unassigned'

        $query = Officer::whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''");

        if ($type === 'assigned') {
            $query->whereNotNull('designation_id');
        } else {
            $query->whereNull('designation_id');
        }

        $officers = $query
            ->selectRaw("PM_CODE, NAME, UPPER(TRIM(RANK)) as rank_display, TRIM(AFPOS) as afpos, DOB")
            ->orderByRaw("NAME ASC")
            ->limit(500)
            ->get()
            ->map(function ($o) {
                $age          = 0;
                $dobFormatted = '';
                try {
                    if ($o->DOB) {
                        $dob          = Carbon::parse($o->DOB);
                        $age          = (int) $dob->diffInYears(Carbon::today());
                        $dobFormatted = $dob->format('d-M-Y');
                    }
                } catch (\Throwable $e) {}
                return [
                    'pm_code' => $o->PM_CODE,
                    'name'    => $o->NAME,
                    'rank'    => $o->rank_display,
                    'afpos'   => $o->afpos,
                    'dob'     => $dobFormatted,
                    'age'     => $age,
                ];
            });

        return response()->json(['officers' => $officers, 'type' => $type]);
    }

    /**
     * GET /dashboard/retiring-officers
     */
    public function retiringOfficers(Request $request)
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN);

        $today = Carbon::today();
        $in12  = Carbon::today()->addMonthsNoOverflow(12);

        $officers = Officer::whereNotNull('RET')
            ->whereRaw("TRIM(RET) != ''")
            ->whereDate('RET', '>=', $today)
            ->whereDate('RET', '<=', $in12)
            ->selectRaw("PM_CODE, NAME, UPPER(TRIM(RANK)) as rank_display, TRIM(AFPOS) as afpos, DOB, RET")
            ->orderBy('RET', 'asc')
            ->limit(300)
            ->get()
            ->map(function ($o) {
                $age = 0;
                $dobFormatted = '';
                try {
                    if ($o->DOB) {
                        $dob          = Carbon::parse($o->DOB);
                        $age          = (int) $dob->diffInYears(Carbon::today());
                        $dobFormatted = $dob->format('d-M-Y');
                    }
                } catch (\Throwable $e) {}
                return [
                    'pm_code'         => $o->PM_CODE,
                    'name'            => $o->NAME,
                    'rank'            => $o->rank_display,
                    'afpos'           => $o->afpos,
                    'dob'             => $dobFormatted,
                    'age'             => $age,
                    'retirement_date' => $o->RET,
                ];
            });

        return response()->json(['officers' => $officers]);
    }

    /**
     * GET /dashboard/population-officers?rank=CPT
     */
    public function populationOfficers(Request $request)
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN);

        $rank     = strtoupper(trim($request->input('rank', '')));
        $rankExpr = 'UPPER(TRIM(RANK))';

        $query = Officer::whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''");
        if ($rank) {
            $query->whereRaw("{$rankExpr} = ?", [$rank]);
        }

        $officers = $query
            ->selectRaw("PM_CODE, NAME, UPPER(TRIM(RANK)) as rank_display, TRIM(AFPOS) as afpos, DOB")
            ->orderByRaw("NAME ASC")
            ->limit(500)
            ->get()
            ->map(function ($o) {
                $age          = 0;
                $dobFormatted = '';
                try {
                    if ($o->DOB) {
                        $dob          = Carbon::parse($o->DOB);
                        $age          = (int) $dob->diffInYears(Carbon::today());
                        $dobFormatted = $dob->format('d-M-Y');
                    }
                } catch (\Throwable $e) {}
                return [
                    'pm_code' => $o->PM_CODE,
                    'name'    => $o->NAME,
                    'rank'    => $o->rank_display,
                    'afpos'   => $o->afpos,
                    'dob'     => $dobFormatted,
                    'age'     => $age,
                ];
            });

        return response()->json(['officers' => $officers, 'rank' => $rank]);
    }

    /**
     * GET /dashboard/afpos-rank-officers?rank=CPT&afpos=INF
     */
    public function afposRankOfficers(Request $request)
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN);

        $rank      = strtoupper(trim($request->input('rank', '')));
        $afpos     = trim($request->input('afpos', ''));
        $rankExpr  = 'UPPER(TRIM(RANK))';
        $afposExpr = 'TRIM(AFPOS)';

        $query = Officer::whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''");
        if ($rank)  $query->whereRaw("{$rankExpr} = ?", [$rank]);
        if ($afpos) $query->whereRaw("{$afposExpr} = ?", [$afpos]);

        $officers = $query
            ->selectRaw("PM_CODE, NAME, UPPER(TRIM(RANK)) as rank_display, TRIM(AFPOS) as afpos, DOB")
            ->orderByRaw("NAME ASC")
            ->limit(500)
            ->get()
            ->map(function ($o) {
                $age          = 0;
                $dobFormatted = '';
                try {
                    if ($o->DOB) {
                        $dob          = Carbon::parse($o->DOB);
                        $age          = (int) $dob->diffInYears(Carbon::today());
                        $dobFormatted = $dob->format('d-M-Y');
                    }
                } catch (\Throwable $e) {}
                return [
                    'pm_code' => $o->PM_CODE,
                    'name'    => $o->NAME,
                    'rank'    => $o->rank_display,
                    'afpos'   => $o->afpos,
                    'dob'     => $dobFormatted,
                    'age'     => $age,
                ];
            });

        return response()->json(['officers' => $officers, 'rank' => $rank, 'afpos' => $afpos]);
    }

    /**
     * GET /dashboard/tenured-officers?rank=COL&afpos=INF&mode=exceeded|not_exceeded
     */
    public function tenuredOfficers(Request $request)
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN);

        $rank     = strtoupper(trim($request->input('rank', '')));
        $afpos    = trim($request->input('afpos', ''));
        $mode     = $request->input('mode', 'exceeded');
        $maxYears = self::MAX_TENURE[$rank] ?? null;

        if (!$maxYears) {
            return response()->json(['officers' => [], 'maxTenure' => 0]);
        }

        $cutoffDate = Carbon::today()->subYears($maxYears)->toDateString();

        $query = Officer::whereRaw("UPPER(TRIM(RANK)) = ?", [$rank])
            ->whereNotNull('DOR')->whereRaw("TRIM(DOR) != ''");

        if ($mode === 'exceeded') {
            $query->whereDate('DOR', '<=', $cutoffDate);
        } else {
            $query->whereDate('DOR', '>', $cutoffDate);
        }

        if ($afpos) {
            $query->whereRaw("TRIM(AFPOS) = ?", [$afpos]);
        }

        $officers = $query
            ->selectRaw("PM_CODE, NAME, UPPER(TRIM(RANK)) as rank_display, TRIM(AFPOS) as afpos, DOR")
            ->orderBy('DOR', 'asc')
            ->limit(300)
            ->get()
            ->map(function ($o) {
                $tenure = 0;
                try {
                    $tenure = round(Carbon::parse($o->DOR)->diffInDays(Carbon::today()) / 365.25, 1);
                } catch (\Throwable $e) {}
                return [
                    'pm_code'      => $o->PM_CODE,
                    'name'         => $o->NAME,
                    'rank'         => $o->rank_display,
                    'afpos'        => $o->afpos,
                    'dor'          => $o->DOR,
                    'tenure_years' => $tenure,
                ];
            });

        return response()->json([
            'officers'  => $officers,
            'maxTenure' => $maxYears,
            'rank'      => $rank,
            'afpos'     => $afpos,
            'mode'      => $mode,
        ]);
    }

    /**
     * GET /dashboard/age-officers?rank=COL&afpos=INF
     */
    public function ageOfficers(Request $request)
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN);

        $rank  = strtoupper(trim($request->input('rank', '')));
        $afpos = trim($request->input('afpos', ''));

        $query = Officer::whereRaw("UPPER(TRIM(RANK)) = ?", [$rank])
            ->whereNotNull('DOB')->whereRaw("TRIM(DOB) != ''");

        if ($afpos) {
            $query->whereRaw("TRIM(AFPOS) = ?", [$afpos]);
        }

        $officers = $query
            ->selectRaw("PM_CODE, NAME, UPPER(TRIM(RANK)) as rank_display, TRIM(AFPOS) as afpos, DOB")
            ->orderBy('DOB', 'asc')
            ->limit(300)
            ->get()
            ->map(function ($o) {
                $age          = 0;
                $dobFormatted = '';
                try {
                    $dob          = Carbon::parse($o->DOB);
                    // Use integer truncation (floor), not rounding
                    $age          = (int) floor($dob->diffInYears(Carbon::today()));
                    $dobFormatted = $dob->format('d-M-Y');
                } catch (\Throwable $e) {}
                return [
                    'pm_code' => $o->PM_CODE,
                    'name'    => $o->NAME,
                    'rank'    => $o->rank_display,
                    'afpos'   => $o->afpos,
                    'dob'     => $dobFormatted,
                    'age'     => $age,
                ];
            });

        return response()->json(['officers' => $officers, 'rank' => $rank, 'afpos' => $afpos]);
    }

    /**
     * GET /dashboard/cc-officers?rank=CPT&afpos=INF&type=with|without
     */
    public function ccOfficers(Request $request)
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN);

        $rank  = strtoupper(trim($request->input('rank', '')));
        $afpos = trim($request->input('afpos', ''));
        $type  = $request->input('type', 'all');

        $ccPMs = AssignmentHistory::withoutGlobalScope(PMCodeScope::class)
            ->where('assignment_id', 24)
            ->whereNotNull('start_date')
            ->where(function ($q) { $q->whereNull('end_date')->orWhere('end_date', ''); })
            ->pluck('pm_code')->unique()->toArray();

        $query = Officer::whereIn('PM_CODE', $ccPMs)
            ->whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''");

        if ($rank)  $query->whereRaw("UPPER(TRIM(RANK)) = ?", [$rank]);
        if ($afpos) $query->whereRaw("TRIM(AFPOS) = ?", [$afpos]);

        $officers = $query
            ->selectRaw("PM_CODE, NAME, UPPER(TRIM(RANK)) as rank_display, TRIM(AFPOS) as afpos")
            ->orderByRaw("FIELD(UPPER(TRIM(RANK)), 'CPT','1LT','2LT') ASC")
            ->limit(300)
            ->get();

        $oacPMs = Schooling::withoutGlobalScope(PMCodeScope::class)
            ->where('assignment_id', 37)
            ->whereIn('pm_code', $officers->pluck('PM_CODE')->toArray())
            ->pluck('pm_code')->unique()->toArray();

        $result = $officers->map(function ($o) use ($oacPMs) {
            return [
                'pm_code' => $o->PM_CODE,
                'name'    => $o->NAME,
                'rank'    => $o->rank_display,
                'afpos'   => $o->afpos,
                'has_oac' => in_array($o->PM_CODE, $oacPMs),
            ];
        });

        if ($type === 'with')    $result = $result->filter(fn($o) => $o['has_oac']);
        elseif ($type === 'without') $result = $result->filter(fn($o) => !$o['has_oac']);

        return response()->json(['officers' => $result->values()]);
    }

    /**
     * GET /dashboard/cgsc-officers?rank=COL&afpos=INF&type=current|not_designated
     */
    public function cgscOfficers(Request $request)
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN);

        $rank  = strtoupper(trim($request->input('rank', '')));
        $afpos = trim($request->input('afpos', ''));
        $type  = $request->input('type', 'all');

        $cgscPMs = Schooling::withoutGlobalScope(PMCodeScope::class)
            ->where('assignment_id', 39)
            ->pluck('pm_code')->unique()->toArray();

        $query = Officer::whereIn('PM_CODE', $cgscPMs)
            ->whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''");

        if ($rank)  $query->whereRaw("UPPER(TRIM(RANK)) = ?", [$rank]);
        if ($afpos) $query->whereRaw("TRIM(AFPOS) = ?", [$afpos]);

        $officers = $query
            ->selectRaw("PM_CODE, NAME, UPPER(TRIM(RANK)) as rank_display, TRIM(AFPOS) as afpos")
            ->limit(300)
            ->get();

        $currentPMs = AssignmentHistory::withoutGlobalScope(PMCodeScope::class)
            ->whereNotNull('start_date')
            ->where(function ($q) { $q->whereNull('end_date')->orWhere('end_date', ''); })
            ->whereIn('pm_code', $officers->pluck('PM_CODE')->toArray())
            ->pluck('pm_code')->unique()->toArray();

        $result = $officers->map(function ($o) use ($currentPMs) {
            return [
                'pm_code'    => $o->PM_CODE,
                'name'       => $o->NAME,
                'rank'       => $o->rank_display,
                'afpos'      => $o->afpos,
                'is_current' => in_array($o->PM_CODE, $currentPMs),
            ];
        });

        if ($type === 'current')        $result = $result->filter(fn($o) => $o['is_current']);
        elseif ($type === 'not_designated') $result = $result->filter(fn($o) => !$o['is_current']);

        return response()->json(['officers' => $result->values()]);
    }
}