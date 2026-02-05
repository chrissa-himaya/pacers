<?php

namespace App\Http\Controllers;

use App\Models\AssignmentHistory;
use App\Models\Designation;
use App\Models\Officer;
use App\Models\Unit;
use App\Models\Assignment;
use App\Models\DateRank;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AssignmentHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $config_data;
    public function __construct(AssignmentHistory $assignmenthistory)
    {
        $columnHidden = array_merge($assignmenthistory->getDates(), ['id']);
        $columnLabels = [''];
        $optionalFields = ['name', 'email'];

        $this->config_data = (object) [
            "module_name" => "Assignment History", //Module name
            "module_perm_name" => "assignmenthistory", //Permission name
            "module_route" => "assignmenthistories", //Web route
            "module_view_folder" => "officerdata.assignmenthistory", //View folder
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
        $assignmenthistory = AssignmentHistory::find(1);
        $assignmenthistory->fill([
            'pm_code' => null,
            'designation_id' => null,
            'unit_id' => null,
            'pamu_id' => null,
            'assignment_id' => null,
            'pri_sec_spec' => null,
            'assignment_type' => null,
            'geography' => null,
            'start_date' => null,
            'end_date' => null,
            'rank_during_completion' => null,
            'year_earned' => null,
        ]);

        $designations = Designation::all()->pluck('name', 'id');
        $units = Unit::with('pamus')->get()->keyBy('id');
        $assignment_type3 = Assignment::where('type_id', 3)
            ->orderBy('name')
            ->pluck('name', 'id');

        $assignments = Assignment::where('type_id', '!=', 5)
            ->orderBy('name')
            ->pluck('name', 'id');

        $pmcodes = Officer::with('designations', 'units')
            ->select('pm_code')
            ->whereNotNull('pm_code')
            ->where('pm_code', '!=', '')
            ->distinct()
            ->orderBy('pm_code')
            ->pluck('pm_code');

        $data_items = [
            "data" => $assignmenthistory,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "pm_codes" => $pmcodes,
            "designations" => $designations,
            "units" => $units,
            "assignments" => $assignments,
            "assignment_type3" => $assignment_type3,
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

        if ($action == 'Fetch Data') {

            $officer = Officer::with(['designations', 'units'])
                ->where('PM_CODE', $pm_code)
                ->first();

            // return $officer;

            if (!$officer) {
                return back()->withInput($request->all())
                    ->withErrors(['pm_code' => 'PM Code not found.']);
            }
            return back()->withInput($request->all())
                ->with([
                    'rank' => $officer->RANK,
                    'name' => $officer->NAME,
                    'afpsn' => $officer->AFPSN,
                    'afpos' => $officer->AFPOS,
                    'sex' => $officer->SEX,
                    'dob' => $officer->DOB,
                    'date_ret' => $officer->RET,
                    'soc' => $officer->SOC,
                    'type' => $officer->TYPE,
                    'otd' => $officer->OTD,
                    'dor' => $officer->DOR,
                    'sig' => $officer->SIG,
                    'designation' => $officer->designations->name,
                    'unit' => $officer->units?->name,
                ]);
        } elseif ($action == 'Save') {
            $data = $request->validate([
                'pm_code' => ['required', 'string'],
                'designation_id' => ['nullable', 'string'],
                'unit_id' => ['nullable', 'integer', 'exists:units,id'],
                'pamu_id' => ['nullable', 'integer'],
                'assignment_id' => ['nullable', 'string'],
                'pri_sec_spec' => ['nullable', 'string'],
                'assignment_type' => ['nullable', 'string'],
                'geography' => ['nullable', 'string'],
                'start_date' => ['nullable', 'date'],
                'end_date' => ['nullable', 'date'],
                'rank_during_completion' => ['nullable', 'string'],
                'year_earned' => ['nullable', 'string'],
            ]);

            $sd = Carbon::parse($data['start_date'])->startOfDay();
            $ed = Carbon::parse($data['end_date'])->startOfDay();
            $pri = $data['pri_sec_spec'] ?? '';

            if ($sd->gt($ed)) {
                return back()->withErrors(['year_earned' => 'Invalid dates'])->withInput();
            }

            $rankResult = $this->computeRankDuringCompletionExcel($data['pm_code'], $sd, $ed, $pri);
            if (!$rankResult['ok']) {
                return back()->withErrors(['rank_during_completion' => $rankResult['message']])->withInput();
            }
            $data['rank_during_completion'] = $rankResult['rank'];

            $isPrimary = (($pri ?? '') === 'primary');

            if ($isPrimary && $this->overlapsExistingPrimary($data['pm_code'], $sd, $ed, null)) {
                $data['year_earned'] = 0;
                return back()->withErrors(['year_earned' => 'Dates overlap'])->withInput();
            }

            // allowed => compute
            $data['year_earned'] = $this->yearfrac_us_30_360($sd, $ed);

            AssignmentHistory::create($data);
            return redirect()->route($this->config_data->module_route . '.index');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(AssignmentHistory $assignmenthistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $data_items = [
            "data" => $assignmenthistory,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "show",
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssignmentHistory $assignmenthistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data_items = [
            "data" => $assignmenthistory,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "edit",
            "optional_fields" => $this->config_data->optionalFields,
        ];
        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssignmentHistory $assignmentHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssignmentHistory $assignmentHistory)
    {
        //
    }

    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $dtColumns = [
            null,
            'pm_code',
            'designations.name',
            'units.name',
            'pamus.name',
            'assignments.name',
            'pri_sec_spec',
            'assignments.name',
            'geography',
            'start_date',
            'end_date',
            'rank_during_completion',
            'year_earned',
            null,
        ];

        $globalSearchColumns = [
            'pm_code',
            'designations.name',
            'units.name',
            'pamus.name',
            'assignments.name',
            'pri_sec_spec',
            'assignments.name',
            'geography',
            'start_date',
            'end_date',
            'rank_during_completion',
            'year_earned',
        ];

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length <= 0)
            $length = 10;

        $query = AssignmentHistory::query();

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
            if (!$column)
                continue;

            $colSearch = trim((string) $request->input("columns.$index.search.value", ''));
            if ($colSearch === '')
                continue;

            if (str_contains($column, '.')) {
                [$relation, $field] = explode('.', $column, 2);

                $query->whereHas($relation, function ($r) use ($field, $colSearch) {
                    $r->where($field, 'like', "%{$colSearch}%");
                });
            } else {
                $query->where($column, 'like', "%{$colSearch}%");
            }
        }

        $totalData = AssignmentHistory::count();
        $filteredData = (clone $query)->count();

        $query->orderBy('id', 'asc');

        $data = $query->skip($start)
            ->take($length)
            ->with(['designations:id,name', 'units:id,name', 'pamus:id,name', 'assignments:id,name,type_id', 'assignments.types:id,name',])
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $filteredData,
            'data' => $data,
        ]);
    }
    private function yearfrac_us_30_360(Carbon $start, Carbon $end): float
    {
        $d1 = $start->day;
        $m1 = $start->month;
        $y1 = $start->year;

        $d2 = $end->day;
        $m2 = $end->month;
        $y2 = $end->year;

        if ($d1 == 31)
            $d1 = 30;
        if ($d2 == 31 && $d1 == 30)
            $d2 = 30;

        $days360 = (360 * ($y2 - $y1)) + (30 * ($m2 - $m1)) + ($d2 - $d1);
        return round($days360 / 360, 6);
    }

    private function overlapsExistingPrimary(string $pmCode, Carbon $sd, Carbon $ed, ?int $ignoreId = null): bool
    {
        $q = AssignmentHistory::query()
            ->where('pm_code', $pmCode)
            ->where('pri_sec_spec', 'primary')
            ->whereDate('start_date', '<=', $ed)
            ->whereDate('end_date', '>=', $sd);


        if ($ignoreId) {
            $q->where('id', '!=', $ignoreId);
        }


        return $q->exists();
    }

    private function lookupRankAt(string $pmCode, Carbon $date): ?string
    {
        // last rank where date <= $date
        // join ranks to get rank name (adjust column names if your ranks table differs)
        return DateRank::query()
            ->where('pm_code', $pmCode)
            ->whereDate('date', '<=', $date->toDateString())
            ->join('ranks', 'date_ranks.rank_id', '=', 'ranks.id')
            ->orderBy('date_ranks.date', 'desc')
            ->value('ranks.code');
    }

    private function computeRankDuringCompletionExcel(string $pmCode, Carbon $sd, Carbon $ed, string $priSecSpec): array
    {
        // Excel: ed_adj = IF(ed>sd, ed-1, sd)
        $edAdj = $ed->gt($sd) ? $ed->copy()->subDay() : $sd->copy();


        $sr = $this->lookupRankAt($pmCode, $sd);
        $er = $this->lookupRankAt($pmCode, $edAdj);


        if (empty($sr) || empty($er)) {
            return ['ok' => false, 'rank' => '', 'message' => 'No rank'];
        }


        $isPrimary = ($priSecSpec === 'primary');


        // Excel: IF(prim, IF(sr=er, sr, "rank conflict"), sr)
        if ($isPrimary) {
            if ($sr === $er) {
                return ['ok' => true, 'rank' => $sr, 'message' => null];
            }
            return ['ok' => false, 'rank' => '', 'message' => 'Dates overlap'];
        }


        return ['ok' => true, 'rank' => $sr, 'message' => null];
    }

    public function computeYearEarned(Request $request)
    {
        $data = $request->validate([
            'pm_code' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'pri_sec_spec' => ['nullable', 'string'],
            'id' => ['nullable', 'integer'], // current record id in edit
        ]);

        $pm = $data['pm_code'];
        $sd = Carbon::parse($data['start_date'])->startOfDay();
        $ed = Carbon::parse($data['end_date'])->startOfDay();
        $pri = $data['pri_sec_spec'] ?? '';

        // invalid dates
        if ($sd->gt($ed)) {
            return response()->json([
                'ok' => false,
                'year_earned' => 0,
                'rank_during_completion' => '',
                'message' => 'Invalid dates',
            ], 422);
        }

        // rank logic (Excel)
        $rankResult = $this->computeRankDuringCompletionExcel($pm, $sd, $ed, $pri);
        if (!$rankResult['ok']) {
            return response()->json([
                'ok' => false,
                'year_earned' => 0,
                'rank_during_completion' => '',
                'message' => $rankResult['message'] ?? 'No rank',
            ], 422);
        }

        $isPrimary = (($pri ?? '') === 'primary');

        if ($isPrimary && $this->overlapsExistingPrimary($pm, $sd, $ed, $data['id'] ?? null)) {
            return response()->json([
                'ok' => false,
                'year_earned' => 0,
                'rank_during_completion' => $rankResult['rank'],
                'message' => 'Dates overlap',
            ], 422);
        }


        return response()->json([
            'ok' => true,
            'year_earned' => $this->yearfrac_us_30_360($sd, $ed),
            'rank_during_completion' => $rankResult['rank'],
        ]);
    }

}
