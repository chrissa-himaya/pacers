<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Officer; // Assuming your model is called Officer
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        // Define rank order for consistent display
        $rankOrder = ['2LT', '1LT', 'CPT', 'MAJ', 'LTC', 'COL', 'BGEN', 'MGEN', 'LTGEN', 'GEN'];
        
        // 1. Get total officers per rank (for Christmas tree chart)
        $officersPerRank = DB::table('personnel_records')
            ->select('RANK', DB::raw('COUNT(*) as count'))
            ->whereNotNull('RANK')
            ->groupBy('RANK')
            ->get()
            ->keyBy('RANK');
        
        // 2. Get gender breakdown per rank
        $genderByRank = DB::table('personnel_records')
            ->select('RANK', 'SEX', DB::raw('COUNT(*) as count'))
            ->whereNotNull('RANK')
            ->whereNotNull('SEX')
            ->groupBy('RANK', 'SEX')
            ->get();
        
        // 3. Get AFPOS breakdown per rank
        $afposByRank = DB::table('personnel_records')
            ->select('RANK', 'AFPOS', DB::raw('COUNT(*) as count'))
            ->whereNotNull('RANK')
            ->whereNotNull('AFPOS')
            ->groupBy('RANK', 'AFPOS')
            ->orderBy('RANK')
            ->orderBy('count', 'DESC')
            ->get();
        
        // Process data for charts
        $chartData = $this->processChartData($officersPerRank, $genderByRank, $afposByRank, $rankOrder);
        
        // Get some additional statistics
        $stats = [
            'total_officers' => DB::table('personnel_records')->count(),
            'male_officers' => DB::table('personnel_records')->where('SEX', 'male')->count(),
            'female_officers' => DB::table('personnel_records')->where('SEX', 'female')->count(),
            'total_ranks' => DB::table('personnel_records')->distinct('RANK')->count('RANK'),
            'total_afpos' => DB::table('personnel_records')->distinct('AFPOS')->count('AFPOS'),
        ];
        
        return view('dashboard', compact('chartData', 'stats', 'afposByRank'));
    }
    
    /**
     * Process data for charts
     */
    private function processChartData($officersPerRank, $genderByRank, $afposByRank, $rankOrder)
    {
        $data = [
            'ranks' => [],
            'total' => [],
            'male' => [],
            'female' => [],
            'afpos' => []
        ];
        
        foreach ($rankOrder as $rank) {
            // Skip if rank doesn't exist in data
            if (!isset($officersPerRank[$rank])) {
                continue;
            }
            
            $data['ranks'][] = $rank;
            $data['total'][] = $officersPerRank[$rank]->count;
            
            // Get male count for this rank
            $maleCount = $genderByRank->where('RANK', $rank)
                ->where('SEX', 'male')
                ->first();
            $data['male'][] = $maleCount ? $maleCount->count : 0;
            
            // Get female count for this rank
            $femaleCount = $genderByRank->where('RANK', $rank)
                ->where('SEX', 'female')
                ->first();
            $data['female'][] = $femaleCount ? $femaleCount->count : 0;
        }
        
        // Process AFPOS data
        foreach ($rankOrder as $rank) {
            $afposForRank = $afposByRank->where('RANK', $rank)->values();
            $data['afpos'][$rank] = $afposForRank;
        }
        
        return $data;
    }
}