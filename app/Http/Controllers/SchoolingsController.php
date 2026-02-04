<?php

namespace App\Http\Controllers;

use App\Models\ClassName;
use App\Models\Schooling;
use App\Models\Assignment;
use App\Models\SchoolingName;
use App\Models\SchoolingUnit;
use App\Models\Officer;
use App\Models\DateRank;
use App\Services\SchoolingPointsService;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class SchoolingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $config_data;
    public function __construct(Schooling $schooling)
    {
        $columnHidden = array_merge($schooling->getDates(), ['id']);
        $columnLabels = [''];
        $optionalFields = ['name', 'email'];

        $this->config_data = (object) [
            "module_name" => "Schoolings", //Module name
            "module_perm_name" => "schooling", //Permission name
            "module_route" => "schoolings", //Web route
            "module_view_folder" => "officerdata.schooling", //View folder
            "columnHidden" => $columnHidden,
            "columnLabels" => $columnLabels,
            "optionalFields" => $optionalFields,
        ];

        view()->share('config_data', $this->config_data);
    }

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
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $schooling = Schooling::find(1);
        $schooling->fill([
            'pm_code' => null,
            'schoolingname_id' => null,
            'classname_id' => null,
            'schooling_unit_id' => null,
            'assignment_id' => null,
        ]);

        $schoolingnames = SchoolingName::all()->pluck('name', 'id');
        $classnames = ClassName::all()->pluck('year', 'id');
        $assignments = Assignment::where('type_id', 5)
            ->orderBy('name')
            ->pluck('name', 'id');
        $schoolingUnits = SchoolingUnit::query()
            ->select('id', 'name', 'location')
            ->orderBy('name')
            ->get();

        $pmcodes = Officer::query()
            ->select('pm_code')
            ->whereNotNull('pm_code')
            ->where('pm_code', '!=', '')
            ->distinct()
            ->orderBy('pm_code')
            ->pluck('pm_code');

        $data_items = [
            "data" => $schooling,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "pm_codes" => $pmcodes,
            "schoolingnames" => $schoolingnames,
            "classnames" => $classnames,
            "schoolingUnits" => $schoolingUnits,
            "assignments" => $assignments,
            "seclt" => 0,
            "firstlt" => 0,
            "cpt" => 0,
            "maj" => 0,
            "ltc" => 0,
            "col" => 0,
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $action = $request->input('action');
        $pm_code = $request->input('pm_code');
        $schooling = Officer::where('PM_CODE', $pm_code)->first();


        if ($action == 'Fetch Data') {
            return back()->withInput($request->all())
                ->with([
                    'rank' => $schooling->RANK,
                    'name' => $schooling->NAME,
                    'afpsn' => $schooling->AFPSN,
                    'afpos' => $schooling->AFPOS,
                    'sex' => $schooling->SEX,
                    'dob' => $schooling->DOB,
                    'date_ret' => $schooling->RET,
                    'soc' => $schooling->SOC,
                    'type' => $schooling->TYPE,
                    'otd' => $schooling->OTD,
                    'dor' => $schooling->DOR,
                    'sig' => $schooling->SIG,
                    'designation' => $schooling->DESIGNATION,
                    'unit' => $schooling->UNIT,
                ]);

        } elseif ($action == 'Save') {

        // Validate what you actually need for saving
            $data = $request->validate([
                'pm_code' => ['required', 'string'],
                'schoolingname_id' => ['nullable', 'integer'],
                'classname_id' => ['nullable', 'integer'],
                'schooling_unit_id' => ['nullable', 'integer'],
                'assignment_id' => ['nullable', 'integer'],
                'date_completed' => ['nullable', 'date'],
                'rating' => ['nullable', 'numeric'],
                'standing' => ['nullable', 'numeric'],
                'total_student' => ['nullable', 'numeric'],
                '2lt' => ['nullable', 'numeric'],
                '1lt' => ['nullable', 'numeric'],
                'cpt' => ['nullable', 'numeric'],
                'maj' => ['nullable', 'numeric'],
                'ltc' => ['nullable', 'numeric'],
                'col' => ['nullable', 'numeric'],
            ]);

            $data['rank_during_completion'] = $this->resolveRankDuringCompletion(
    $data['pm_code'] ?? null,
    $data['date_completed'] ?? null
            ) ?? '';

            // ---- compute points on server (same as AJAX) ----
            $points = app(SchoolingPointsService::class)->compute([
                'assignment_id' => $data['assignment_id'] ?? null,
                'school_location' => $request->input('school_location'),
                'rating' => $data['rating'] ?? null,
                'standing' => $data['standing'] ?? null,
                'total_students' => $data['total_student'] ?? null,
            ]);

            // ---- push points into the correct rank column ----
            $this->applyPointsToRankColumns($data, $points);

            Schooling::create($data);

            return redirect()->route($this->config_data->module_route . '.index');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Schooling $schooling)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // Load relationships for the schooling record
        $schooling->load(['schoolingnames', 'classnames', 'schoolingunits', 'assignments']);
        // If the schooling record has a PM code, look up the officer.
        // Use the same column name you use in your store() method ('PMCODE').
        if ($schooling->pm_code) {
            $officerData = Officer::where('PM_CODE', $schooling->pm_code)->first();

            if ($officerData) {
                // Set session values using the officer's attributes (use uppercase names)
                // session([
                //     'rank'        => $officer->RANK,
                //     'name'        => $officer->NAME,
                //     'afpsn'       => $officer->AFPSN,
                //     'afpos'       => $officer->AFPOS,
                //     'sex'         => $officer->SEX,
                //     'dob'         => $officer->DOB,
                //     'date_ret'    => $officer->RET,
                //     'soc'         => $officer->SOC,
                //     'type'        => $officer->TYPE,
                //     'otd'         => $officer->OTD,
                //     'dor'         => $officer->DOR,
                //     'sig'         => $officer->SIG,
                //     'designation' => $officer->DESIGNATION,
                //     'unit'        => $officer->UNIT,
                // ]);
            }
        }


        // Build the data array with the additional collections you need (as before)
        $columnHidden = array_merge($schooling->getDates(), ['id']);
        $data_items = [
            'data' => $schooling,
            'column_hidden' => $columnHidden,
            'column_labels' => $this->config_data->columnLabels,
            'operation_type' => 'show',
            'schooling' => $schooling,
            'officerData' => $officerData
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schooling $schooling)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schooling $schooling)
    {
        $data = $request->validate([
            'pm_code' => ['required', 'string'],
            'date_completed' => ['nullable', 'date'],
            'rating' => ['nullable', 'numeric'],
            'standing' => ['nullable', 'numeric'],
            'total_student' => ['nullable', 'numeric'],
            // ... add the rest of your fields
        ]);

            $data['rank_during_completion'] = $this->resolveRankDuringCompletion(
    $data['pm_code'] ?? null,
    $data['date_completed'] ?? null
            ) ?? '';

            // ---- recompute points ----
            $points = app(\App\Services\SchoolingPointsService::class)->compute([
                'assignment_id' => $data['assignment_id'] ?? $schooling->assignment_id,
                'school_location' => $request->input('school_location'),
                'rating' => $data['rating'] ?? $schooling->rating,
                'standing' => $data['standing'] ?? $schooling->standing,
                'total_students' => $data['total_student'] ?? $schooling->total_student,
            ]);

            $this->applyPointsToRankColumns($data, $points);

            $schooling->update($data);


            return redirect()->route($this->config_data->module_route . '.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schooling $schooling)
    {
        //
    }

    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // All columns in the table
        $columns = ['id', 'pm_code', 'schoolingname_id','classname_id','schooling_unit_id','assignment_id','date_completed','rating','standing','total_student','rank_during_completion','2lt','1lt','cpt','maj','ltc','col', 'created_at', 'updated_at'];

        // Pagination values from DataTables
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        // Prevent invalid length (MariaDB requires LIMIT)
        if ($length <= 0) {
            $length = 10;
        }

        // Ordering
        $orderIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

        // Validate order direction
        if (!in_array($orderDir, ['asc', 'desc'])) {
            $orderDir = 'asc';
        }

        $orderColumn = $columns[$orderIndex] ?? 'id';

        // Base query
        $query = Schooling::query();


        // Search filter
        $search = $request->input('search.value');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('pm_code', 'like', "%{$search}%");
            });
        }

        // Total records
        $totalData = Schooling::count();
        $filteredData = $query->count();

        // Apply ordering and pagination
        $data = $query->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->with(['schoolingnames', 'classnames', 'schoolingunits', 'assignments'])
            ->get();

        // Return JSON in DataTables format
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $filteredData,
            'data' => $data,
        ]);
    }

    private function resolveRankDuringCompletion(?string $pmCode, ?string $dateCompleted): ?string
    {
        if (blank($pmCode) || blank($dateCompleted)) {
            return null;
        }

        // Find the most recent rank record on/before dateCompleted for that pm_code
        $dr = DateRank::query()
            ->with('ranks:id,code,name')   // IMPORTANT: ranks (plural)
            ->where('pm_code', trim($pmCode))
            ->whereDate('date', '<=', $dateCompleted)
            ->orderBy('date', 'desc')
            ->first();

        return $dr?->ranks?->code;
    }

    public function rankDuringCompletion(Request $request)
    {
        $pmCode = $request->string('pm_code')->toString();
        $dateCompleted = $request->input('date_completed');

        return response()->json([
        'rank' => $this->resolveRankDuringCompletion($pmCode, $dateCompleted) ?? ''
    ]);
    }

    public function computePoints(Request $request, SchoolingPointsService $svc)
    {
        $points = $svc->compute([
        'assignment_id' => $request->input('assignment_id'),
        'school_location' => $request->input('school_location'),
        'rating' => $request->input('rating'),
        'standing' => $request->input('standing'),
        'total_students' => $request->input('total_students'),
        ]);


        return response()->json(['points' => $points]);
    }

    private function applyPointsToRankColumns(array &$data, float $points): void
    {
        // Reset all rank columns
        foreach (['2lt','1lt','cpt','maj','ltc','col'] as $col) {
            $data[$col] = 0;
        }

        $rank = strtoupper(trim($data['rank_during_completion'] ?? ''));

        // Map rank code -> column name
        $map = [
            '2LT' => '2lt',
            '1LT' => '1lt',
            'CPT' => 'cpt',
            'MAJ' => 'maj',
            'LTC' => 'ltc',
            'COL' => 'col',
        ];

        if (isset($map[$rank])) {
            $data[$map[$rank]] = round($points, 4);
        }
    }

}
