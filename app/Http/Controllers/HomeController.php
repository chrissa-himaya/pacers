<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Officer;
use Illuminate\Support\Facades\DB;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;


class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rankOrder = [
            "GEN", 
            "LTGEN", 
            "MGEN", 
            "BGEN",
            "COL", 
            "LTC", 
            "MAJ",
            "CPT", 
            "1LT", 
            "2LT"
        ];

        $orderList = '"' . implode('","', $rankOrder) . '"';


        // Normalize expressions (MySQL compatible)
        $sexExpr  = 'UPPER(TRIM(SEX))';
        $rankExpr = 'TRIM(RANK)';
        $afposExpr = 'TRIM(AFPOS)';

        // ✅ Core stats
        $totalOfficers = Officer::count();
        $today = Carbon::today();
        $in12  = Carbon::today()->addMonthsNoOverflow(12);

        $retiring12 = Officer::query()
            ->whereNotNull('DOR')
            ->whereRaw("TRIM(DOR) != ''")
            ->whereDate('DOR', '>=', $today)
            ->whereDate('DOR', '<=', $in12)
            ->count();

        $male = Officer::whereRaw("{$sexExpr} IN ('M','MALE')")->count();
        $female = Officer::whereRaw("{$sexExpr} IN ('F','FEMALE')")->count();

        $assigned = Officer::whereNotNull('designation_id')->count();
        $unassigned = Officer::whereNull('designation_id')->count();

        $totalAfpos = Officer::whereNotNull('AFPOS')
            ->whereRaw("{$afposExpr} != ''")
            ->distinct('AFPOS')
            ->count('AFPOS');

        // ✅ Quick “Top” insights
        $topRankRow = Officer::query()
            ->whereNotNull('RANK')
            ->whereRaw("{$rankExpr} != ''")
            ->selectRaw("{$rankExpr} as rank")
            ->selectRaw('COUNT(*) as total')
            ->groupBy('rank')
            ->orderByDesc('total')
            ->first();

        $topAfposRow = Officer::query()
            ->whereNotNull('AFPOS')
            ->whereRaw("{$afposExpr} != ''")
            ->selectRaw("{$afposExpr} as afpos")
            ->selectRaw('COUNT(*) as total')
            ->groupBy('afpos')
            ->orderByDesc('total')
            ->first();

        $stats = [
            'total_officers' => $totalOfficers,
            'male_officers'  => $male,
            'female_officers'=> $female,
            'total_afpos'    => $totalAfpos,

            'assigned_officers'   => $assigned,
            'unassigned_officers' => $unassigned,
            'assigned_rate'       => $totalOfficers > 0 ? round(($assigned / $totalOfficers) * 100, 1) : 0,

            'top_rank'  => $topRankRow ? $topRankRow->rank : null,
            'top_rank_total' => $topRankRow ? (int) $topRankRow->total : 0,
            'top_afpos' => $topAfposRow ? $topAfposRow->afpos : null,
            'top_afpos_total' => $topAfposRow ? (int) $topAfposRow->total : 0,
            'retiring_12_months' => $retiring12,

        ];

        // ✅ Rank distribution (TRIM + normalized sex)
        // $rankData = Officer::query()
        //     ->whereNotNull('RANK')
        //     ->whereRaw("{$rankExpr} != ''")
        //     ->selectRaw("{$rankExpr} as rank")
        //     ->selectRaw('COUNT(*) as total')
        //     ->selectRaw("SUM(CASE WHEN {$sexExpr} IN ('M','MALE') THEN 1 ELSE 0 END) as male")
        //     ->selectRaw("SUM(CASE WHEN {$sexExpr} IN ('F','FEMALE') THEN 1 ELSE 0 END) as female")
        //     ->selectRaw('SUM(CASE WHEN designation_id IS NOT NULL THEN 1 ELSE 0 END) as assigned')
        //     ->selectRaw('SUM(CASE WHEN designation_id IS NULL THEN 1 ELSE 0 END) as unassigned')
        //     ->groupBy('rank')
        //     // Put known ranks first in your desired order; unknown ranks go last
        //     ->orderByRaw(
        //         'CASE WHEN FIELD(rank, "' . implode('\",\"', $rankOrder) . '") = 0 THEN 999 ELSE FIELD(rank, "' . implode('\",\"', $rankOrder) . '") END'
        //     )
        //     ->get();

        $rankData = Officer::query()
            ->whereNotNull('RANK')
            ->whereRaw("TRIM(RANK) != ''")
            ->selectRaw("UPPER(TRIM(RANK)) as rank")
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(SEX)) IN ('M','MALE') THEN 1 ELSE 0 END) as male")
            ->selectRaw("SUM(CASE WHEN UPPER(TRIM(SEX)) IN ('F','FEMALE') THEN 1 ELSE 0 END) as female")
            ->selectRaw("SUM(CASE WHEN designation_id IS NOT NULL THEN 1 ELSE 0 END) as assigned")
            ->selectRaw("SUM(CASE WHEN designation_id IS NULL THEN 1 ELSE 0 END) as unassigned")
            ->groupBy('rank')

            ->orderByRaw("(FIELD(rank, {$orderList}) = 0) ASC")
            ->orderByRaw("FIELD(rank, {$orderList}) ASC")
            ->get();

        // ✅ AFPOS breakdown per rank
        $afposData = [];
        foreach ($rankData as $rankRow) {
            $r = $rankRow->rank;

            $afposBreakdown = Officer::query()
                ->whereRaw("{$rankExpr} = ?", [$r])
                ->whereNotNull('AFPOS')
                ->whereRaw("{$afposExpr} != ''")
                ->selectRaw("{$afposExpr} as AFPOS")
                ->selectRaw('COUNT(*) as count')
                ->groupBy('AFPOS')
                ->orderByDesc('count')
                ->limit(6)
                ->get();

            $afposData[$r] = $afposBreakdown;
        }

        $chartData = [
            'ranks'      => $rankData->pluck('rank')->toArray(),
            'total'      => $rankData->pluck('total')->map(fn ($v) => (int) $v)->toArray(),
            'male'       => $rankData->pluck('male')->map(fn ($v) => (int) $v)->toArray(),
            'female'     => $rankData->pluck('female')->map(fn ($v) => (int) $v)->toArray(),
            'assigned'   => $rankData->pluck('assigned')->map(fn ($v) => (int) $v)->toArray(),
            'unassigned' => $rankData->pluck('unassigned')->map(fn ($v) => (int) $v)->toArray(),
            'afpos'      => $afposData,
        ];

        return view('dashboard', compact('stats', 'chartData'));
    }
}
