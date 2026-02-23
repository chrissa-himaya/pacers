<?php
namespace App\Http\Controllers;

use App\Models\Assignment;
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
use Illuminate\Support\Collection;

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
            "module_name" => "QRS Profiles", //Module name
            "module_perm_name" => "qrsprofile", //Permission name
            "module_route" => "qrsprofiles", //Web route
            "module_view_folder" => "officerdata.qrsprofile", //View folder
            "columnHidden" => $columnHidden,
            "columnLabels" => $columnLabels,
            "optionalFields" => $optionalFields,
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
            'schoolings.assignments',
            'awards',
            'awards.awards',
            'awards.dateranks',
            'pfts',
            'designations'
        ]);

        $types = Type::whereIn('id', [1, 2, 3, 4])->with('assignments')->get();
        $ranks = ['2LT', '1LT', 'CPT', 'MAJ', 'LTC', 'COL'];
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
            ->map(
                fn($rows) => $rows->groupBy('rank_during_completion')
                    ->map(fn($rows2) => $rows2->sum('year_earned'))
            );

        // Computed points totals - for gained points display and QRS score
        $computedTotals = $data->assignmenthistories
            ->filter(fn($h) => $h->pri_sec_spec === 'primary' && !empty($h->assignment_id) && !empty($h->rank_during_completion))
            ->groupBy(fn($h) => $h->assignment_id)
            ->map(
                fn($rows) => $rows->groupBy('rank_during_completion')
                    ->map(fn($rows2) => $rows2->sum('computed_points')) // use computed_points
            );

        // Get all assignments with type_id = 5 (schooling criteria)
        $schoolingCriteria = Assignment::where('type_id', 5)->orderBy('id')->get();

        // Get officer's schooling records, keeping only the most recent per assignment_id
        $schoolingMap = $data->schoolings
            ->sortByDesc('date_completed')
            ->groupBy('assignment_id')
            ->map(function ($records, $assignmentId) {
                if ($assignmentId == 44) {
                    return $records; // for specialization
                }
                return $records->first(); // most recent schooling
            });

        $awardsMap = $data->awards
            ->groupBy('date_rank_id')
            ->map(function ($awards) {
                return $awards
                    ->groupBy('award_id')
                    ->map(function ($group) {
                        $first = $group->first();
                        return [
                            'name' => $first->awards->code ?? 'Unknown',
                            'count' => $group->count(),
                        ];
                    })
                    ->values();
            });

        $pftMap = $data->pfts
            ->sortByDesc('date_taken')
            ->groupBy('rank')
            ->map(fn($records) => $records->first());

        $rankColumnMap = [
            1 => 'seclt',
            2 => 'firstlt',
            3 => 'cpt',
            4 => 'maj',
            5 => 'ltc',
            6 => 'col',
        ];

        $schoolingPoints = [];
            foreach ($schoolingCriteria as $criteria) {
                $schoolingEntry = $schoolingMap->get($criteria->id);

                $schoolingRecord = ($schoolingEntry instanceof Collection)
                    ? $schoolingEntry->first()
                    : $schoolingEntry;

                foreach ($rankColumnMap as $rankId => $col) {
                    $maxPoint = $sourcedataMap[$criteria->id][$rankId]->max_month ?? null;

                    $actualPoint = $schoolingRecord ? (float) ($schoolingRecord->$col ?? 0) : null;
                    $actualPoint = ($actualPoint > 0) ? $actualPoint : null;

                    // Cap actual at max
                    if (!is_null($actualPoint) && !is_null($maxPoint)) {
                        $actualPoint = min($actualPoint, (float) $maxPoint);
                    }

                    $schoolingPoints[$criteria->id][$rankId] = [
                        'max'    => $maxPoint,
                        'actual' => $actualPoint,
                    ];
                }
            }

        // Awards points: assignment_id=45 for max/min, grouped by current rank
        $awardsPoints = [];
            foreach ($rankIdMap as $rankLabel => $rankId) {
                $sd = $sourcedataMap[45][$rankId] ?? null;

                // Previous rank ID
                $prevRankId = $rankId > 1 ? $rankId - 1 : null;
                $sdPrev = $prevRankId ? ($sourcedataMap[45][$prevRankId] ?? null) : null;

                // Current rank awards sum (capped at max_point)
                $currentMax = $sd ? (float) $sd->max_point : null;
                $currentActualRaw = $data->awards
                    ->where('date_rank_id', $rankId)
                    ->sum('points');
                $currentActual = $currentMax !== null 
                    ? min((float) $currentActualRaw, $currentMax) 
                    : (float) $currentActualRaw;

                // Previous rank awards sum (capped at prev min_point)
                $prevMax = $sdPrev ? (float) $sdPrev->min_point : null;
                $prevActualRaw = $prevRankId 
                    ? $data->awards->where('date_rank_id', $prevRankId)->sum('points') 
                    : 0;
                $prevActual = $prevMax !== null 
                    ? min((float) $prevActualRaw, $prevMax) 
                    : (float) $prevActualRaw;

                $awardsPoints[$rankLabel] = [
                    'current_max'    => $currentMax,
                    'prev_min'       => $prevMax,
                    'current_actual' => $currentActual > 0 ? $currentActual : null,
                    'prev_actual'    => $prevActual > 0 ? $prevActual : null,
                ];
            }

            // PFT points: assignment_id=46, actual from pfthistories.points
        $pftPoints = [];
            foreach ($rankIdMap as $rankLabel => $rankId) {
                $sd = $sourcedataMap[46][$rankId] ?? null;
                $pft = $data->pfts->firstWhere('rank', $rankLabel);

                $pftPoints[$rankLabel] = [
                    'max'    => $sd ? (float) $sd->max_point : null,
                    'actual' => $pft && $pft->points > 0 ? (float) $pft->points : null,
                ];
            }

        $qrsScores = [];
        foreach ($rankIdMap as $rank => $rankId) {
            $qrsScores[$rank] = $this->computeQrsScore(
                $data, $rank, $rankId,
                $sourcedataMap, $computedTotals,
                $schoolingPoints, $schoolingCriteria,
                $awardsPoints, $pftPoints
            );
        }

        return view($this->config_data->module_view_folder . '.profile', 
        compact(
            'data',
            'types',
            'ranks',
            'totals',
            'qrsScores',
            'sourcedataMap',
            'rankIdMap',
            'schoolingCriteria',
            'schoolingMap',
            'awardsMap',
            'pftMap',
            'rankColumnMap',
            'schoolingPoints',
            'awardsPoints',
            'pftPoints',
            'computedTotals'
        ));
    }

    private function computeQrsScore(
            $data, $rank, $rankId,
            $sourcedataMap, $computedTotals,
            $schoolingPoints, $schoolingCriteria,
            $awardsPoints, $pftPoints
        ): float {

            // 1. Assignment gained points — sum computed_points capped at max_point
            $assignmentTotal = 0;
            $assignmentData  = data_get($computedTotals, null) ?? collect();
            foreach ($computedTotals as $assignmentId => $rankTotals) {
                $gained  = data_get($rankTotals, $rank, 0);
                $sd      = $sourcedataMap[$assignmentId][$rankId] ?? null;
                $maxPt   = $sd ? (float) $sd->max_point : null;
                $capped  = $maxPt !== null ? min((float) $gained, $maxPt) : (float) $gained;
                $assignmentTotal += $capped;
            }

            // 2. Schooling actual points
            $schoolingTotal = 0;
            foreach ($schoolingCriteria as $criteria) {
                $point  = $schoolingPoints[$criteria->id][$rankId] ?? ['max' => null, 'actual' => null];
                $actual = (float) ($point['actual'] ?? 0);
                $max    = $point['max'] !== null ? (float) $point['max'] : null;
                $schoolingTotal += $max !== null ? min($actual, $max) : $actual;
            }

            // 3. Awards points (current + prev)
            $ap          = $awardsPoints[$rank] ?? [];
            $awardsTotal = ((float)($ap['current_actual'] ?? 0)) + ((float)($ap['prev_actual'] ?? 0));

            // 4. PFT points (capped at max)
            $pp        = $pftPoints[$rank] ?? [];
            $pftActual = (float)($pp['actual'] ?? 0);
            $pftMax    = $pp['max'] !== null ? (float)$pp['max'] : null;
            $pftCapped = $pftMax !== null ? min($pftActual, $pftMax) : $pftActual;

            return round($assignmentTotal + $schoolingTotal + $awardsTotal + $pftCapped, 2);
        }

    // private function computeQrsScore($officer, $rank): float
    // {
    //     // Sum all gained points for this rank from QRS requirements
    //     // Adjust this logic to match your actual QRS computation rules
    //     $assignmentPoints = $officer->assignmenthistories
    //         ->filter(fn($h) => $h->rank_during_completion === $rank)
    //         ->sum('points_earned'); // adjust field name

    //     $schoolingPoints = $officer->schoolings
    //         ->where('rank', $rank)
    //         ->sum('points');

    //     $awardPoints = $officer->awards
    //         ->where('rank', $rank)
    //         ->sum('points');

    //     $pftPoints = $officer->pfts
    //         ->where('rank', $rank)
    //         ->value('points') ?? 0;

    //     return round($assignmentPoints + $schoolingPoints + $awardPoints + $pftPoints, 2);
    // }

    public function index()
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view($this->config_data->module_view_folder . '.index');
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
