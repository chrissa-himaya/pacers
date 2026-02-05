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

            $data = $request->validate([
                'pm_code' => ['required', 'string'],
                'schoolingname_id' => ['nullable', 'string'],
                'classname_id' => ['nullable', 'string'],
                'schooling_unit_id' => ['nullable', 'string'],
                'assignment_id' => ['nullable', 'string'],
                'date_completed' => ['nullable', 'date'],
                'rating' => ['nullable', 'string'],
                'standing' => ['nullable', 'string'],
                'total_student' => ['nullable', 'string'],
                'rank_during_completion' => ['nullable', 'string'],
                'seclt' => ['nullable', 'string'],
                'firstlt' => ['nullable', 'string'],
                'cpt' => ['nullable', 'string'],
                'maj' => ['nullable', 'string'],
                'ltc' => ['nullable', 'string'],
                'col' => ['nullable', 'string'],
            ]);

                    $data['rank_during_completion'] = $this->resolveRankDuringCompletion(
            $data['pm_code'] ?? null,
    $data['date_completed'] ?? null
            ) ?? '';

            $svcInput = [
                'assignment_id'   => $data['assignment_id'] ?? null,
                'school_location' => $request->input('school_location'),
                'rating'          => $data['rating'] ?? null,
                'standing'        => $data['standing'] ?? null,
                'total_students'  => $data['total_student'] ?? null,
            ];

            $svc = app(SchoolingPointsService::class);

            foreach ([1=>'seclt', 2=>'firstlt', 3=>'cpt', 4=>'maj', 5=>'ltc', 6=>'col'] as $rankId => $col) {
                $data[$col] = $svc->computeForRank($svcInput, $rankId);
            }

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
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $columnHidden = array_merge($schooling->getDates(), ['id']);
        $schooling->load(['schoolingnames', 'classnames', 'schoolingunits', 'assignments']);
        
        // dropdown sources (same as create)
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

        // officer panel on top
        $officerData = null;
        if ($schooling->pm_code) {
            $officerData = Officer::where('PM_CODE', $schooling->pm_code)->first();
        }
        
        $data_items = [
            "data" => $schooling,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "edit",

            "optional_fields" => $this->config_data->optionalFields,
            "pm_codes" => $pmcodes,
            "schoolingnames" => $schoolingnames,
            "classnames" => $classnames,
            "schoolingUnits" => $schoolingUnits,
            "assignments" => $assignments,

            "officerData" => $officerData,
        ];

        // return $data_items["data"];
        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    public function update(Request $request, Schooling $schooling)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'pm_code' => ['required', 'string'],

            'schoolingname_id' => ['nullable', 'string'],
            'classname_id' => ['nullable', 'string'],
            'schooling_unit_id' => ['nullable', 'string'],
            'assignment_id' => ['nullable', 'string'],

            'date_completed' => ['nullable', 'date'],
            'rating' => ['nullable', 'numeric'],
            'standing' => ['nullable', 'numeric'],
            'total_student' => ['nullable', 'numeric'],

            // rank columns will be overwritten anyway; keep nullable
            'rank_during_completion' => ['nullable', 'string'],
            'seclt' => ['nullable', 'numeric'],
            'firstlt' => ['nullable', 'numeric'],
            'cpt' => ['nullable', 'numeric'],
            'maj' => ['nullable', 'numeric'],
            'ltc' => ['nullable', 'numeric'],
            'col' => ['nullable', 'numeric'],
        ]);

        // always resolve rank during completion based on pm_code + date_completed
        $data['rank_during_completion'] = $this->resolveRankDuringCompletion(
            $data['pm_code'] ?? null,
            $data['date_completed'] ?? null
        ) ?? '';

        // recompute points per rank (same logic as store)
        $svcInput = [
            'assignment_id'   => $data['assignment_id'] ?? $schooling->assignment_id,
            'school_location' => $request->input('school_location'), // comes from readonly input
            'rating'          => $data['rating'] ?? $schooling->rating,
            'standing'        => $data['standing'] ?? $schooling->standing,
            'total_students'  => $data['total_student'] ?? $schooling->total_student,
        ];

        $svc = app(SchoolingPointsService::class);

        foreach ([1=>'seclt', 2=>'firstlt', 3=>'cpt', 4=>'maj', 5=>'ltc', 6=>'col'] as $rankId => $col) {
            $data[$col] = $svc->computeForRank($svcInput, $rankId);
        }

        $schooling->update($data);

        return redirect()->route($this->config_data->module_route . '.index');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schooling $schooling)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $schooling->delete();
        return back();
    }

    public function list(Request $request)
    {
        abort_if(
            Gate::denies($this->config_data->module_perm_name.'_access'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // DataTables column index mapping (Nr and Actions are null)
        $dtColumns = [
            null,
            'pm_code',
            'schoolingnames.name',
            'classnames.year',
            'schoolingunits.name',              
            'assignments.name', 
            'date_completed',
            'rating',
            'standing',
            'total_student',
            'rank_during_completion',
            'seclt',
            'firstlt',
            'cpt',
            'maj',
            'ltc',
            'col',
            null,
        ];

        $globalSearchColumns = [
            'pm_code',
            'schoolingnames.name',
            'classnames.year',
            'schoolingunits.name',              
            'assignments.name', 
            'date_completed',
            'rating',
            'standing',
            'total_student',
            'rank_during_completion',
            'seclt',
            'firstlt',
            'cpt',
            'maj',
            'ltc',
            'col',
        ];

        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length <= 0) $length = 10;

        $query = Schooling::query();

        //for global search *DO NOT DELETE THIS*
        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search, $globalSearchColumns) {
                foreach ($globalSearchColumns as $col) {

                    if (str_contains($col, '.')) {
                        [$relation, $field] = explode('.', $col, 2);

                        $q->orWhereHas($relation, function ($r) use ($field, $search) {
                            $r->where($field, 'like', "%{$search}%");
                        });

                        continue;
                    }
                    $q->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        //for each column search *DO NOT DELETE THIS*
        foreach ($dtColumns as $index => $column) {
            if (!$column) continue;

            $colSearch = trim((string) $request->input("columns.$index.search.value", ''));
            if ($colSearch === '') continue;

            if (str_contains($column, '.')) {
                [$relation, $field] = explode('.', $column, 2);

                $query->whereHas($relation, function ($r) use ($field, $colSearch) {
                    $r->where($field, 'like', "%{$colSearch}%");
                });
            } else {
                $query->where($column, 'like', "%{$colSearch}%");
            }
        }

        $totalData    = Schooling::count();
        $filteredData = (clone $query)->count();

        $query->orderBy('id', 'asc');

        $data = $query->skip($start)
        ->take($length)
        ->with(['schoolingnames:id,name', 'classnames:id,year', 'schoolingunits:id,name,location', 'assignments:id,name'])
        ->get();

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $filteredData,
            'data'            => $data,
        ]);
    }

    private function resolveRankDuringCompletion(?string $pmCode, ?string $dateCompleted): ?string
    {
        if (blank($pmCode) || blank($dateCompleted)) {
            return null;
        }

        $dr = DateRank::query()
            ->with('ranks:id,code,name')
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
        $svcInput = [
            'assignment_id'   => $request->input('assignment_id'),
            'school_location' => $request->input('school_location'),
            'rating'          => $request->input('rating'),
            'standing'        => $request->input('standing'),
            'total_students'  => $request->input('total_students'),
        ];

        $out = [];
        foreach ([1=>'seclt', 2=>'firstlt', 3=>'cpt', 4=>'maj', 5=>'ltc', 6=>'col'] as $rankId => $field) {
            $out[$field] = $svc->computeForRank($svcInput, $rankId);
        }

        return response()->json($out);
    }
}
