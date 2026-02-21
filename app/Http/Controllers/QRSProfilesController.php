<?php
namespace App\Http\Controllers;

use App\Models\AssignmentHistory;
use App\Models\Award;
use App\Models\AwardHistory;
use App\Models\PftHistory;
use App\Models\QRSProfile;
use App\Models\Schooling;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use App\Models\Officer;
use App\Models\Type;
use App\Models\Sourcedata;

class QRSProfilesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $config_data;
    public function __construct(QRSProfile $qrsprofile)
    {
        $columnHidden = array_merge($qrsprofile->getDates(), ['id']);
        $columnLabels = [''];    
        $optionalFields = ['name', 'email'];

        $this->config_data = (object) [
            "module_name"=>"QRS Profiles", //Module name
            "module_perm_name"=>"qrsprofile", //Permission name
            "module_route"=>"qrsprofiles", //Web route
            "module_view_folder"=>"officerdata.qrsprofile", //View folder
            "columnHidden"=>$columnHidden,
            "columnLabels"=>$columnLabels,
            "optionalFields"=>$optionalFields,
        ];

        view()->share('config_data', $this->config_data);
    }

    // public function profile(Officer $officer)
    // {
    //     $data = $officer->load(['assignmenthistories', 'schoolings', 'awards', 'pfts', 'assignmenthistories.assignments']);

    //     // return $officer;
    //     return view($this->config_data->module_view_folder.'.profile', compact('data'));
    // }

    
    
    public function profile(Officer $officer)
    {
        $data = $officer->load([
            'assignmenthistories',
            'assignmenthistories.assignments',
            'assignmenthistories.assignments.types',
            'schoolings',
            'awards',
            'pfts',
            'designations'
        ]);

        $types = Type::whereIn('id', [1,2,3,4])->with('assignments')->get();
        $ranks = ['2LT','1LT','CPT','MAJ','LTC','COL'];
        $rankIdMap = [
            '2LT' => 1,
            '1LT' => 2,
            'CPT' => 3,
            'MAJ' => 4,
            'LTC' => 5,
            'COL' => 6,
        ];

        // Load ALL sourcedatas, keyed by [assignment_id][rank_id]
        $sourcedatas = Sourcedata::all();
        $sourcedataMap = [];
        foreach ($sourcedatas as $sd) {
            $sourcedataMap[$sd->assignment_id][$sd->rank_id] = $sd;
        }

        // Assignment totals (already working)
        $totals = $data->assignmenthistories
            ->filter(fn($h) => $h->pri_sec_spec === 'primary' && !empty($h->assignment_id) && !empty($h->rank_during_completion))
            ->groupBy(fn($h) => $h->assignment_id)
            ->map(fn($rows) => $rows->groupBy('rank_during_completion')
                ->map(fn($rows2) => $rows2->sum('year_earned'))
            );

        // QRS scores per rank
        $qrsScores = [];
        foreach ($ranks as $rank) {
            $qrsScores[$rank] = $this->computeQrsScore($data, $rank);
        }

        return view($this->config_data->module_view_folder . '.profile', compact(
            'data', 'types', 'ranks', 'totals', 'qrsScores', 'sourcedataMap', 'rankIdMap'
        ));
    }

    private function computeQrsScore($officer, $rank): float
    {
        // Sum all gained points for this rank from QRS requirements
        // Adjust this logic to match your actual QRS computation rules
        $assignmentPoints = $officer->assignmenthistories
            ->filter(fn($h) => $h->rank_during_completion === $rank)
            ->sum('points_earned'); // adjust field name

        $schoolingPoints = $officer->schoolings
            ->where('rank', $rank)
            ->sum('points');

        $awardPoints = $officer->awards
            ->where('rank', $rank)
            ->sum('points');

        $pftPoints = $officer->pfts
            ->where('rank', $rank)
            ->value('points') ?? 0;

        return round($assignmentPoints + $schoolingPoints + $awardPoints + $pftPoints, 2);
    }
    
    public function index()
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view($this->config_data->module_view_folder.'.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(QRSProfile $qRSProfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QRSProfile $qRSProfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QRSProfile $qRSProfile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QRSProfile $qRSProfile)
    {
        //
    }


}
