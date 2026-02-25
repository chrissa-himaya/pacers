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
    /**
     * Maximum Tenure-In-Grade policy (years) per RA 11939 Sec 10(b).
     */
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

    /**
     * Compulsory retirement age per RA 11939 Sec 6(a)(1):
     * O-1 to O-9 → age 57 or 30 years active duty, whichever comes later.
     * We use age 57 as the primary check (DOB-based).
     */
    private const COMPULSORY_RETIREMENT_AGE = 57;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rankOrder       = ["GEN","LTGEN","MGEN","BGEN","COL","LTC","MAJ","CPT","1LT","2LT"];
        $rankOrder2LTtoCOL = ["COL","LTC","MAJ","CPT","1LT","2LT"];
        $rankOrderCPTtoCOL = ["COL","LTC","MAJ","CPT"];
        $rankOrderLTCtoCOL = ["COL","LTC"];
        $rankOrder2LTtoCPT = ["CPT","1LT","2LT"];

        $orderList = '"' . implode('","', $rankOrder) . '"';

        $sexExpr   = 'UPPER(TRIM(SEX))';
        $rankExpr  = 'UPPER(TRIM(RANK))';
        $afposExpr = 'TRIM(AFPOS)';

        $today = Carbon::today();

        // ─── Core Stats ──────────────────────────────────────
        $totalOfficers = Officer::count();
        $in12 = Carbon::today()->addMonthsNoOverflow(12);

        // Compulsory Retirement per RA 11939 Sec 6(a)(1):
        // Officers O-1 to O-9 retire upon reaching age 57.
        // Count officers who will turn 57 within the next 12 months.
        $retireAgeCutoff = $today->copy()->subYears(self::COMPULSORY_RETIREMENT_AGE);
        $retireAgeCutoff12 = $in12->copy()->subYears(self::COMPULSORY_RETIREMENT_AGE);

        // Officers whose DOB falls between (today - 57yrs - 12mo) and (today - 57yrs)
        // meaning they turn 57 between today and 12 months from now
        // Also include those already past 57 who haven't been processed yet
        $retiring12 = Officer::query()
            ->whereNotNull('DOB')->whereRaw("TRIM(DOB) != ''")
            ->where(function ($q) use ($today, $in12) {
                $turnDate57Start = $today->copy()->subYears(57);
                $turnDate57End   = $in12->copy()->subYears(57);
                $q->whereBetween('DOB', [$turnDate57End, $turnDate57Start]);
            })
            ->count();

        $retiring12ByDOR = Officer::query()
            ->whereNotNull('DOR')->whereRaw("TRIM(DOR) != ''")
            ->whereDate('DOR', '>=', $today)->whereDate('DOR', '<=', $in12)
            ->count();

        $retiring12Total = max($retiring12, $retiring12ByDOR);

        // REPLACE WITH:
        $retiring12Total = Officer::query()
            ->whereNotNull('RET')
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

        $topRankRow = Officer::query()
            ->whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->selectRaw("{$rankExpr} as rank, COUNT(*) as total")
            ->groupBy('rank')->orderByDesc('total')->first();

        $topAfposRow = Officer::query()
            ->whereNotNull('AFPOS')->whereRaw("{$afposExpr} != ''")
            ->selectRaw("{$afposExpr} as afpos, COUNT(*) as total")
            ->groupBy('afpos')->orderByDesc('total')->first();

        $stats = [
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

        // ─── Tab 1: Rank Distribution ────────────────────────
        $rankData = Officer::query()
            ->whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
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
            $afposData[$r] = Officer::query()
                ->whereRaw("{$rankExpr} = ?", [$r])
                ->whereNotNull('AFPOS')->whereRaw("{$afposExpr} != ''")
                ->selectRaw("{$afposExpr} as AFPOS, COUNT(*) as count")
                ->groupBy('AFPOS')->orderByDesc('count')->limit(6)->get();
        }

        $chartData = [
            'ranks'      => $rankData->pluck('rank')->toArray(),
            'total'      => $rankData->pluck('total')->map(fn($v) => (int)$v)->toArray(),
            'male'       => $rankData->pluck('male')->map(fn($v) => (int)$v)->toArray(),
            'female'     => $rankData->pluck('female')->map(fn($v) => (int)$v)->toArray(),
            'assigned'   => $rankData->pluck('assigned')->map(fn($v) => (int)$v)->toArray(),
            'unassigned' => $rankData->pluck('unassigned')->map(fn($v) => (int)$v)->toArray(),
            'afpos'      => $afposData,
        ];

        // ─── Tab 1: PAMU by Rank recap table ─────────────────
        $pamuRanks = ["2LT","1LT","CPT","MAJ","LTC","COL"];
        $pamus = Pamu::orderBy('code')->get();

        // Get officer counts grouped by pamu_id and rank
        $pamuOfficerData = Officer::query()
            ->whereNotNull('pamu_id')
            ->whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->whereRaw("{$rankExpr} IN ('" . implode("','", $pamuRanks) . "')")
            ->selectRaw("pamu_id, {$rankExpr} as rank, COUNT(*) as total")
            ->groupBy('pamu_id', 'rank')
            ->get();

        // Build lookup: [pamu_id][rank] => count
        $pamuMap = [];
        foreach ($pamuOfficerData as $row) {
            $pamuMap[$row->pamu_id][$row->rank] = (int) $row->total;
        }

        $pamuRecap = [];
        foreach ($pamus as $pamu) {
            $row = ['code' => $pamu->code, 'name' => $pamu->name];
            $rowTotal = 0;
            foreach ($pamuRanks as $r) {
                $cnt = $pamuMap[$pamu->id][$r] ?? 0;
                $row[$r] = $cnt;
                $rowTotal += $cnt;
            }
            $row['total'] = $rowTotal;
            if ($rowTotal > 0) { // Only include PAMUs that have officers
                $pamuRecap[] = $row;
            }
        }

        // Compute column totals
        $pamuTotals = [];
        $grandTotal = 0;
        foreach ($pamuRanks as $r) {
            $pamuTotals[$r] = array_sum(array_column($pamuRecap, $r));
            $grandTotal += $pamuTotals[$r];
        }
        $pamuTotals['total'] = $grandTotal;

        // ═══ Tab 2: AFPOS per Rank (2LT–COL) ════════════════
        $afposPerRankData = Officer::query()
            ->whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->whereRaw("{$rankExpr} IN ('" . implode("','", $rankOrder2LTtoCOL) . "')")
            ->whereNotNull('AFPOS')->whereRaw("{$afposExpr} != ''")
            ->selectRaw("{$rankExpr} as rank, {$afposExpr} as afpos, COUNT(*) as count")
            ->groupBy('rank', 'afpos')->get();

        $afposPerRank = [];
        foreach ($afposPerRankData as $row) {
            $afposPerRank[$row->rank][$row->afpos] = (int) $row->count;
        }

        $tab2Data = [
            'ranks'     => $rankOrder2LTtoCOL,
            'afposList' => $afposPerRankData->pluck('afpos')->unique()->sort()->values()->toArray(),
            'data'      => $afposPerRank,
        ];

        // ═══ Tab 3: Population Pyramid (2LT–COL) ════════════
        $popData = Officer::query()
            ->whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->whereRaw("{$rankExpr} IN ('" . implode("','", $rankOrder2LTtoCOL) . "')")
            ->selectRaw("{$rankExpr} as rank, COUNT(*) as total")
            ->groupBy('rank')->get()->keyBy('rank');

        $pyramidRanks  = array_reverse($rankOrder2LTtoCOL);
        $pyramidTotals = array_map(fn($r) => (int) ($popData[$r]->total ?? 0), $pyramidRanks);

        $tab3Data = ['ranks' => $pyramidRanks, 'totals' => $pyramidTotals];

        // ═══ Tab 4: Tenure in Grade (CPT–COL) ═══════════════
        // Two views: exceeded AND not-exceeded max tenure
        $tenureRanks = $rankOrderCPTtoCOL;

        $tenureOfficers = Officer::query()
            ->whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->whereRaw("{$rankExpr} IN ('" . implode("','", $tenureRanks) . "')")
            ->whereNotNull('DOR')->whereRaw("TRIM(DOR) != ''")
            ->whereNotNull('AFPOS')->whereRaw("{$afposExpr} != ''")
            ->selectRaw("PM_CODE as pm_code, {$rankExpr} as rank, {$afposExpr} as afpos, DOR")
            ->get();

        $tenuredMap      = []; // exceeded: [rank][afpos] => count
        $notTenuredMap   = []; // not exceeded: [rank][afpos] => count
        $allTenureMap    = []; // all: [rank][afpos] => [sum, count]

        foreach ($tenureOfficers as $off) {
            $r = $off->rank;
            $a = $off->afpos;
            $maxYears = self::MAX_TENURE[$r] ?? null;
            if ($maxYears === null) continue;

            try {
                $dor = Carbon::parse($off->DOR);
                $tenureYears = round($dor->diffInDays($today) / 365.25, 2);
            } catch (\Throwable $e) { continue; }

            if (!isset($allTenureMap[$r][$a])) $allTenureMap[$r][$a] = ['sum' => 0, 'count' => 0];
            $allTenureMap[$r][$a]['sum'] += $tenureYears;
            $allTenureMap[$r][$a]['count']++;

            if ($tenureYears >= $maxYears) {
                $tenuredMap[$r][$a] = ($tenuredMap[$r][$a] ?? 0) + 1;
            } else {
                $notTenuredMap[$r][$a] = ($notTenuredMap[$r][$a] ?? 0) + 1;
            }
        }

        // Top AFPOS
        $tenureAfposCounts = [];
        foreach ($allTenureMap as $r => $arr) {
            foreach ($arr as $a => $vals) {
                $tenureAfposCounts[$a] = ($tenureAfposCounts[$a] ?? 0) + $vals['count'];
            }
        }
        arsort($tenureAfposCounts);
        $topTenureAfpos = array_slice(array_keys($tenureAfposCounts), 0, 12);

        $tenuredCounts    = [];
        $notTenuredCounts = [];
        $tenureAverages   = [];
        foreach ($tenureRanks as $r) {
            foreach ($topTenureAfpos as $a) {
                $tenuredCounts[$r][$a]    = $tenuredMap[$r][$a] ?? 0;
                $notTenuredCounts[$r][$a] = $notTenuredMap[$r][$a] ?? 0;
                $d = $allTenureMap[$r][$a] ?? null;
                $tenureAverages[$r][$a]   = $d ? round($d['sum'] / $d['count'], 2) : 0;
            }
        }

        $tab4Data = [
            'ranks'       => $tenureRanks,
            'afposList'   => $topTenureAfpos,
            'tenured'     => $tenuredCounts,
            'notTenured'  => $notTenuredCounts,
            'averages'    => $tenureAverages,
            'maxTenure'   => self::MAX_TENURE,
        ];

        // ═══ Tab 5: Age per Rank (LTC–COL) per AFPOS ════════
        $ageRanks = $rankOrderLTCtoCOL;

        $ageOfficers = Officer::query()
            ->whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->whereRaw("{$rankExpr} IN ('" . implode("','", $ageRanks) . "')")
            ->whereNotNull('DOB')->whereRaw("TRIM(DOB) != ''")
            ->whereNotNull('AFPOS')->whereRaw("{$afposExpr} != ''")
            ->selectRaw("PM_CODE as pm_code, {$rankExpr} as rank, {$afposExpr} as afpos, DOB")
            ->get();

        $ageMap = [];
        foreach ($ageOfficers as $row) {
            try { $age = Carbon::parse($row->DOB)->diffInYears($today); } catch (\Throwable $e) { continue; }
            $r = $row->rank; $a = $row->afpos;
            if (!isset($ageMap[$r][$a])) $ageMap[$r][$a] = ['sum' => 0, 'count' => 0];
            $ageMap[$r][$a]['sum'] += $age;
            $ageMap[$r][$a]['count']++;
        }

        $ageAfposCounts = [];
        foreach ($ageMap as $r => $arr) {
            foreach ($arr as $a => $vals) {
                $ageAfposCounts[$a] = ($ageAfposCounts[$a] ?? 0) + $vals['count'];
            }
        }
        arsort($ageAfposCounts);
        $topAgeAfpos = array_slice(array_keys($ageAfposCounts), 0, 12);
        $allAgeAfpos = array_keys($ageAfposCounts);

        $ageAverages = [];
        $ageCounts   = [];
        foreach ($ageRanks as $r) {
            foreach ($allAgeAfpos as $a) {
                $d = $ageMap[$r][$a] ?? null;
                $ageAverages[$r][$a] = $d ? round($d['sum'] / $d['count'], 1) : 0;
                $ageCounts[$r][$a]   = $d ? $d['count'] : 0;
            }
        }

        $tab5Data = [
            'ranks'        => $ageRanks,
            'afposList'    => $topAgeAfpos,
            'allAfposList' => $allAgeAfpos,
            'averages'     => $ageAverages,
            'counts'       => $ageCounts,
        ];

        // ═══ Tab 6: Company Commanders – with/without OAC ════
        $ccRanks = $rankOrder2LTtoCPT;

        $currentCCPMs = AssignmentHistory::withoutGlobalScope(PMCodeScope::class)
            ->where('assignment_id', 24)
            ->whereNotNull('start_date')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '');
            })
            ->pluck('pm_code')->unique()->toArray();

        $ccOfficers = Officer::query()
            ->whereIn('PM_CODE', $currentCCPMs)
            ->whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->whereRaw("{$rankExpr} IN ('" . implode("','", $ccRanks) . "')")
            ->whereNotNull('AFPOS')->whereRaw("{$afposExpr} != ''")
            ->selectRaw("PM_CODE as pm_code, {$rankExpr} as rank, {$afposExpr} as afpos")
            ->get();

        $oacPMs = Schooling::withoutGlobalScope(PMCodeScope::class)
            ->where('assignment_id', 37)
            ->whereIn('pm_code', $currentCCPMs)
            ->pluck('pm_code')->unique()->toArray();

        $ccMap = [];
        foreach ($ccOfficers as $off) {
            $r = $off->rank; $a = $off->afpos;
            $hasOAC = in_array($off->pm_code, $oacPMs);
            if (!isset($ccMap[$r][$a])) $ccMap[$r][$a] = ['with' => 0, 'without' => 0];
            $ccMap[$r][$a][$hasOAC ? 'with' : 'without']++;
        }

        $ccAfposCounts = [];
        foreach ($ccMap as $r => $arr) {
            foreach ($arr as $a => $vals) {
                $ccAfposCounts[$a] = ($ccAfposCounts[$a] ?? 0) + $vals['with'] + $vals['without'];
            }
        }
        arsort($ccAfposCounts);

        $tab6Data = [
            'ranks'     => $ccRanks,
            'afposList' => array_slice(array_keys($ccAfposCounts), 0, 12),
            'data'      => $ccMap,
        ];

        // ═══ Tab 7: CGSC Graduates (assignment_id = 39) ══════
        $cgscRanks = ["COL", "LTC"];

        $cgscPMs = Schooling::withoutGlobalScope(PMCodeScope::class)
            ->where('assignment_id', 39)
            ->pluck('pm_code')->unique();

        $cgscOfficers = Officer::query()
            ->whereIn('PM_CODE', $cgscPMs)
            ->whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''")
            ->whereRaw("{$rankExpr} IN ('" . implode("','", $cgscRanks) . "')")
            ->whereNotNull('AFPOS')->whereRaw("{$afposExpr} != ''")
            ->selectRaw("PM_CODE as pm_code, {$rankExpr} as rank, {$afposExpr} as afpos")
            ->get();

        $currentBnCdrPMs = AssignmentHistory::withoutGlobalScope(PMCodeScope::class)
            ->whereNotNull('start_date')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '');
            })
            ->pluck('pm_code')->unique()->toArray();

        $cgscMap = [];
        foreach ($cgscOfficers as $off) {
            $r = $off->rank; $a = $off->afpos;
            $isCurrent = in_array($off->pm_code, $currentBnCdrPMs);
            if (!isset($cgscMap[$r][$a])) $cgscMap[$r][$a] = ['current' => 0, 'not_designated' => 0];
            $cgscMap[$r][$a][$isCurrent ? 'current' : 'not_designated']++;
        }

        $cgscAfposCounts = [];
        foreach ($cgscMap as $r => $arr) {
            foreach ($arr as $a => $vals) {
                $cgscAfposCounts[$a] = ($cgscAfposCounts[$a] ?? 0) + $vals['current'] + $vals['not_designated'];
            }
        }
        arsort($cgscAfposCounts);

        $tab7Data = [
            'ranks'     => $cgscRanks,
            'afposList' => array_slice(array_keys($cgscAfposCounts), 0, 12),
            'data'      => $cgscMap,
        ];

        return view('dashboard', compact(
            'stats', 'chartData',
            'pamuRecap', 'pamuRanks', 'pamuTotals',
            'tab2Data', 'tab3Data', 'tab4Data',
            'tab5Data', 'tab6Data', 'tab7Data'
        ));
    }

    // ═══════════════════════════════════════════════════════════
    //  AJAX Drill-down endpoints
    // ═══════════════════════════════════════════════════════════

    /**
     * GET /dashboard/retiring-officers
     * Officers retiring within 12 months per RA 11939 (age 57).
     */
    // public function retiringOfficers(Request $request)
    // {
    //     abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN);

    //     $today = Carbon::today();
    //     $in12  = Carbon::today()->addMonthsNoOverflow(12);
    //     $turnDate57Start = $today->copy()->subYears(57);
    //     $turnDate57End   = $in12->copy()->subYears(57);

    //     $officers = Officer::query()
    //         ->whereNotNull('DOB')->whereRaw("TRIM(DOB) != ''")
    //         ->whereBetween('DOB', [$turnDate57End, $turnDate57Start])
    //         ->selectRaw("PM_CODE, NAME, UPPER(TRIM(RANK)) as rank_display, TRIM(AFPOS) as afpos, DOB, DOR")
    //         ->orderBy('DOB', 'asc')->limit(300)->get()
    //         ->map(function ($o) {
    //             $age = 0; $retDate = '';
    //             try {
    //                 $dob = Carbon::parse($o->DOB);
    //                 $age = $dob->diffInYears(Carbon::today());
    //                 $retDate = $dob->copy()->addYears(57)->format('Y-m-d');
    //             } catch (\Throwable $e) {}
    //             return [
    //                 'pm_code' => $o->PM_CODE, 'name' => $o->NAME,
    //                 'rank' => $o->rank_display, 'afpos' => $o->afpos,
    //                 'dob' => $o->DOB, 'age' => $age,
    //                 'retirement_date' => $retDate,
    //             ];
    //         });

    //     return response()->json(['officers' => $officers]);
    // }

    public function retiringOfficers(Request $request)
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN);

        $today = Carbon::today();
        $in12  = Carbon::today()->addMonthsNoOverflow(12);

        $officers = Officer::query()
            ->whereNotNull('RET')
            ->whereRaw("TRIM(RET) != ''")
            ->whereDate('RET', '>=', $today)
            ->whereDate('RET', '<=', $in12)
            ->selectRaw("PM_CODE, NAME, UPPER(TRIM(RANK)) as rank_display, TRIM(AFPOS) as afpos, DOB, RET")
            ->orderBy('RET', 'asc')
            ->limit(300)
            ->get()
            ->map(function ($o) {
                $age = 0;
                try {
                    $age = $o->DOB ? Carbon::parse($o->DOB)->diffInYears(Carbon::today()) : 0;
                } catch (\Throwable $e) {}
                return [
                    'pm_code'         => $o->PM_CODE,
                    'name'            => $o->NAME,
                    'rank'            => $o->rank_display,
                    'afpos'           => $o->afpos,
                    'dob'             => $o->DOB,
                    'age'             => $age,
                    'retirement_date' => $o->RET,
                ];
            });

        return response()->json(['officers' => $officers]);
    }

    /**
     * GET /dashboard/tenured-officers?rank=COL&afpos=INF&mode=exceeded|not_exceeded
     */
    public function tenuredOfficers(Request $request)
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN);

        $rank  = strtoupper(trim($request->input('rank', '')));
        $afpos = trim($request->input('afpos', ''));
        $mode  = $request->input('mode', 'exceeded'); // exceeded or not_exceeded

        $maxYears = self::MAX_TENURE[$rank] ?? null;
        if (!$maxYears) return response()->json(['officers' => [], 'maxTenure' => 0]);

        $cutoffDate = Carbon::today()->subYears($maxYears)->toDateString();

        $query = Officer::query()
            ->whereRaw("UPPER(TRIM(RANK)) = ?", [$rank])
            ->whereNotNull('DOR')->whereRaw("TRIM(DOR) != ''");

        if ($mode === 'exceeded') {
            $query->whereDate('DOR', '<=', $cutoffDate);
        } else {
            $query->whereDate('DOR', '>', $cutoffDate);
        }

        if ($afpos) $query->whereRaw("TRIM(AFPOS) = ?", [$afpos]);

        $officers = $query
            ->selectRaw("PM_CODE, NAME, UPPER(TRIM(RANK)) as rank_display, TRIM(AFPOS) as afpos, DOR")
            ->orderBy('DOR', 'asc')->limit(300)->get()
            ->map(function ($o) {
                $tenure = 0;
                try { $tenure = round(Carbon::parse($o->DOR)->diffInDays(Carbon::today()) / 365.25, 1); } catch (\Throwable $e) {}
                return [
                    'pm_code' => $o->PM_CODE, 'name' => $o->NAME,
                    'rank' => $o->rank_display, 'afpos' => $o->afpos,
                    'dor' => $o->DOR, 'tenure_years' => $tenure,
                ];
            });

        return response()->json(['officers' => $officers, 'maxTenure' => $maxYears, 'rank' => $rank, 'afpos' => $afpos, 'mode' => $mode]);
    }

    /**
     * GET /dashboard/age-officers?rank=COL&afpos=INF
     */
    public function ageOfficers(Request $request)
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN);

        $rank  = strtoupper(trim($request->input('rank', '')));
        $afpos = trim($request->input('afpos', ''));

        $query = Officer::query()
            ->whereRaw("UPPER(TRIM(RANK)) = ?", [$rank])
            ->whereNotNull('DOB')->whereRaw("TRIM(DOB) != ''");

        if ($afpos) $query->whereRaw("TRIM(AFPOS) = ?", [$afpos]);

        $officers = $query
            ->selectRaw("PM_CODE, NAME, UPPER(TRIM(RANK)) as rank_display, TRIM(AFPOS) as afpos, DOB")
            ->orderBy('DOB', 'asc')->limit(300)->get()
            ->map(function ($o) {
                $age = 0;
                try { $age = Carbon::parse($o->DOB)->diffInYears(Carbon::today()); } catch (\Throwable $e) {}
                return [
                    'pm_code' => $o->PM_CODE, 'name' => $o->NAME,
                    'rank' => $o->rank_display, 'afpos' => $o->afpos,
                    'dob' => $o->DOB, 'age' => $age,
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
        $type  = $request->input('type', 'all'); // with, without, all

        // Get current Company Commanders (assignment_id = 24)
        $ccPMs = AssignmentHistory::withoutGlobalScope(PMCodeScope::class)
            ->where('assignment_id', 24)
            ->whereNotNull('start_date')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '');
            })
            ->pluck('pm_code')->unique()->toArray();

        $query = Officer::query()
            ->whereIn('PM_CODE', $ccPMs)
            ->whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''");

        if ($rank) $query->whereRaw("UPPER(TRIM(RANK)) = ?", [$rank]);
        if ($afpos) $query->whereRaw("TRIM(AFPOS) = ?", [$afpos]);

        $officers = $query
            ->selectRaw("PM_CODE, NAME, UPPER(TRIM(RANK)) as rank_display, TRIM(AFPOS) as afpos")
            ->orderByRaw("FIELD(UPPER(TRIM(RANK)), 'CPT','1LT','2LT') ASC")
            ->limit(300)->get();

        // Check OAC status
        $oacPMs = Schooling::withoutGlobalScope(PMCodeScope::class)
            ->where('assignment_id', 37)
            ->whereIn('pm_code', $officers->pluck('PM_CODE')->toArray())
            ->pluck('pm_code')->unique()->toArray();

        $result = $officers->map(function ($o) use ($oacPMs) {
            $hasOAC = in_array($o->PM_CODE, $oacPMs);
            return [
                'pm_code' => $o->PM_CODE, 'name' => $o->NAME,
                'rank' => $o->rank_display, 'afpos' => $o->afpos,
                'has_oac' => $hasOAC,
            ];
        });

        // Filter by type
        if ($type === 'with') $result = $result->filter(fn($o) => $o['has_oac']);
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

        // CGSC graduates (assignment_id = 39)
        $cgscPMs = Schooling::withoutGlobalScope(PMCodeScope::class)
            ->where('assignment_id', 39)
            ->pluck('pm_code')->unique()->toArray();

        $query = Officer::query()
            ->whereIn('PM_CODE', $cgscPMs)
            ->whereNotNull('RANK')->whereRaw("TRIM(RANK) != ''");

        if ($rank) $query->whereRaw("UPPER(TRIM(RANK)) = ?", [$rank]);
        if ($afpos) $query->whereRaw("TRIM(AFPOS) = ?", [$afpos]);

        $officers = $query
            ->selectRaw("PM_CODE, NAME, UPPER(TRIM(RANK)) as rank_display, TRIM(AFPOS) as afpos")
            ->limit(300)->get();

        // Check current Bn Cdr status
        $currentPMs = AssignmentHistory::withoutGlobalScope(PMCodeScope::class)
            ->whereNotNull('start_date')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '');
            })
            ->whereIn('pm_code', $officers->pluck('PM_CODE')->toArray())
            ->pluck('pm_code')->unique()->toArray();

        $result = $officers->map(function ($o) use ($currentPMs) {
            $isCurrent = in_array($o->PM_CODE, $currentPMs);
            return [
                'pm_code' => $o->PM_CODE, 'name' => $o->NAME,
                'rank' => $o->rank_display, 'afpos' => $o->afpos,
                'is_current' => $isCurrent,
            ];
        });

        if ($type === 'current') $result = $result->filter(fn($o) => $o['is_current']);
        elseif ($type === 'not_designated') $result = $result->filter(fn($o) => !$o['is_current']);

        return response()->json(['officers' => $result->values()]);
    }
}