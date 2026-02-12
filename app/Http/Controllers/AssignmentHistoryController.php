<?php

namespace App\Http\Controllers;

use App\Models\AssignmentHistory;
use App\Models\Designation;
use App\Models\Officer;
use App\Models\Unit;
use App\Models\Pamu;
use App\Models\Assignment;
use App\Models\DateRank;
use App\Services\AssignmentHistoryPointsService;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AssignmentHistoryController extends Controller
{
    protected $config_data;
    protected $pointsService;

    public function __construct(AssignmentHistory $assignmenthistory, AssignmentHistoryPointsService $pointsService)
    {
        $columnHidden = array_merge($assignmenthistory->getDates(), ['id']);
        $columnLabels = [''];
        $optionalFields = ['name', 'email'];

        $this->pointsService = $pointsService;

        $this->config_data = (object) [
            "module_name" => "Assignment History",
            "module_perm_name" => "assignmenthistory",
            "module_route" => "assignmenthistories",
            "module_view_folder" => "officerdata.assignmenthistory",
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

    public function create()
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Only clear session on fresh GET visits (not redirect-back from Fetch Data)
        if (!old('pm_code')) {
            session()->forget([
                'pm_code',
                'name',
                'rank',
                'afpos',
                'afpsn',
                'sex',
                'dob',
                'date_ret',
                'dor',
                'soc',
                'sig',
                'designation',
                'unit',
                'designation_id',
                'unit_id',
                'assignment_id',
                'relatedHistories',
            ]);
        }

        $assignmenthistory = new AssignmentHistory();

        $designations = Designation::all()->pluck('name', 'id');
        $units = Unit::with('pamus')->get()->keyBy('id');
        $pamus = Pamu::all()->pluck('name', 'id');
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
            "pamus" => $pamus,
            "assignments" => $assignments,
            "assignment_type3" => $assignment_type3,
            "relatedHistories" => collect([]),
        ];
        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    /* ─── CREATE FROM EXISTING (prefill pm_code + officer info) ─── */
    public function createFromExisting(AssignmentHistory $assignmentHistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $blank = new AssignmentHistory();
        $blank->fill([
            'pm_code' => $assignmentHistory->pm_code,
            'designation_id' => null,
            'unit_id' => null,
            'assignment_id' => null,
        ]);

        $officerData = Officer::where('PM_CODE', $assignmentHistory->pm_code)->first();

        $this->storeOfficerInSession($assignmentHistory->pm_code, $officerData);

        $data_items = $this->buildCreateDataItems($blank, $officerData, $assignmentHistory->pm_code);

        return view($this->config_data->module_view_folder . '.show', compact('data_items'))
            ->with('config_data', $this->config_data);
    }

    private function storeOfficerInSession(string $pmCode, ?Officer $officer): void
    {
        session([
            'pm_code' => $pmCode,
            'name' => $officer->NAME ?? '',
            'rank' => $officer->RANK ?? '',
            'afpos' => $officer->AFPOS ?? '',
            'afpsn' => $officer->AFPSN ?? '',
            'sex' => $officer->SEX ?? '',
            'dob' => $officer->DOB ?? '',
            'date_ret' => $officer->RET ?? '',
            'dor' => $officer->DOR ?? '',
            'soc' => $officer->SOC ?? '',
            'sig' => $officer->SIG ?? '',
            'designation' => $officer->designations->name ?? '',
            'unit' => $officer->units->name ?? '',
        ]);
    }

    private function buildCreateDataItems(AssignmentHistory $blank, ?Officer $officerData, string $pmCode): array
    {
        $relatedHistories = AssignmentHistory::where('pm_code', $pmCode)
            ->with(['designations', 'units', 'pamus', 'assignments', 'assignmentType'])
            ->orderBy('start_date', 'desc')
            ->get();

        session(['relatedHistories' => $relatedHistories]);

        return [
            'data' => $blank,
            'column_hidden' => $this->config_data->columnHidden,
            'column_labels' => $this->config_data->columnLabels,
            'operation_type' => 'create',
            'optional_fields' => $this->config_data->optionalFields,
            'pm_codes' => $this->getPmCodes(),
            'designations' => Designation::all()->pluck('name', 'id'),
            'units' => Unit::with('pamus')->get()->keyBy('id'),
            'pamus' => Pamu::all()->pluck('name', 'id'),
            'assignments' => Assignment::where('type_id', '!=', 5)->orderBy('name')->pluck('name', 'id'),
            'assignment_type3' => Assignment::where('type_id', 3)->orderBy('name')->pluck('name', 'id'),
            'officerData' => $officerData,
            'relatedHistories' => $relatedHistories,
        ];
    }

    private function getPmCodes()
    {
        return Officer::query()
            ->select('pm_code')
            ->whereNotNull('pm_code')
            ->where('pm_code', '!=', '')
            ->distinct()
            ->orderBy('pm_code')
            ->pluck('pm_code');
    }

    public function store(Request $request)
    {
        $action = $request->input('action');
        $pm_code = $request->input('pm_code');

        if ($action == 'Fetch Data') {

            $officer = Officer::with(['designations', 'units'])
                ->where('PM_CODE', $pm_code)
                ->first();

            if (!$officer) {
                return back()->withInput($request->all())
                    ->withErrors(['pm_code' => 'PM Code not found.']);
            }

            $relatedHistories = AssignmentHistory::where('pm_code', $pm_code)
                ->with(['designations', 'units', 'pamus', 'assignments', 'assignmentType'])
                ->orderBy('start_date', 'desc')
                ->get();

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
                    'designation' => $officer->designations->name ?? '',
                    'unit' => $officer->units?->name ?? '',
                    'relatedHistories' => $relatedHistories,
                ]);
        } elseif ($action == 'Save') {

            $data = $request->validate([
                'pm_code' => ['required', 'string'],
                'designation_id' => ['nullable', 'string'],
                'new_designation_name' => ['nullable', 'required_if:designation_id,new', 'string', 'max:255'],
                'subunit' => ['nullable', 'string'],
                'unit_id' => ['nullable', 'integer'],
                'new_unit_name' => ['nullable', 'required_if:unit_id,new', 'string', 'max:255'],
                'new_unit_pamu_id' => ['nullable', 'string'],
                'new_pamu_name' => ['nullable', 'required_if:new_unit_pamu_id,new_pamu', 'string', 'max:255'],
                'pamu_id' => ['nullable', 'integer'],
                'assignment_id' => ['nullable', 'string'],
                'pri_sec_spec' => ['nullable', 'string'],
                'assignmentType' => ['nullable', 'string'],
                'geography' => ['nullable', 'string'],
                'start_date' => ['nullable', 'date'],
                'end_date' => ['nullable', 'date'],
                'rank_during_completion' => ['nullable', 'string'],
                'year_earned' => ['nullable', 'string'],
            ]);

            // Handle new designation creation
            if ($data['designation_id'] === 'new' && !empty($data['new_designation_name'])) {
                $newDesignation = Designation::firstOrCreate(['name' => $data['new_designation_name']]);
                $data['designation_id'] = $newDesignation->id;
            }

            // Handle new unit + PAMU creation
            if (isset($data['unit_id']) && $data['unit_id'] === 'new' && !empty($data['new_unit_name'])) {
                $pamuId = null;

                if (isset($data['new_unit_pamu_id']) && $data['new_unit_pamu_id'] === 'new_pamu' && !empty($data['new_pamu_name'])) {
                    $newPamu = Pamu::firstOrCreate(['name' => $data['new_pamu_name']]);
                    $pamuId = $newPamu->id;
                } elseif (!empty($data['new_unit_pamu_id']) && $data['new_unit_pamu_id'] !== 'new_pamu') {
                    $pamuId = $data['new_unit_pamu_id'];
                }

                $newUnit = Unit::create([
                    'name' => $data['new_unit_name'],
                    'pamu_id' => $pamuId,
                ]);
                $data['unit_id'] = $newUnit->id;
                $data['pamu_id'] = $pamuId;
            }


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
            } else {
                $data['year_earned'] = $this->yearfrac_us_30_360($sd, $ed);
            }

            $match = [
                'pm_code' => $data['pm_code'],
                'designation_id' => $data['designation_id'] ?? null,
                'unit_id' => $data['unit_id'] ?? null,
                'assignment_id' => $data['assignment_id'] ?? null,
                'pri_sec_spec' => $data['pri_sec_spec'] ?? null,
                'geography' => $data['geography'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'rank_during_completion' => $data['rank_during_completion'] ?? null,
            ];

            $record = AssignmentHistory::updateOrCreate($match, $data);

            // Compute points after saving
            $this->pointsService->recomputeAndSave($record);

            return redirect()->route($this->config_data->module_route . '.index');
        }
    }

    public function show(AssignmentHistory $assignmenthistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $officerData = Officer::with(['designations', 'units'])
            ->where('PM_CODE', $assignmenthistory->pm_code)
            ->first();

        $designations = Designation::pluck('name', 'id');
        $units = Unit::with('pamus')->get()->keyBy('id');
        $pamus = Pamu::all()->pluck('name', 'id');

        $assignment_type3 = Assignment::where('type_id', 3)
            ->orderBy('name')
            ->pluck('name', 'id');

        $assignments = Assignment::where('type_id', '!=', 5)
            ->orderBy('name')
            ->pluck('name', 'id');

        $pmcodes = Officer::select('pm_code')
            ->whereNotNull('pm_code')
            ->where('pm_code', '!=', '')
            ->distinct()
            ->orderBy('pm_code')
            ->pluck('pm_code');

        $relatedHistories = AssignmentHistory::query()
            ->where('pm_code', $assignmenthistory->pm_code)
            ->where('id', '!=', $assignmenthistory->id)
            ->where('pm_code', '!=', '')
            ->whereNotNull('pm_code')
            ->with(['designations', 'units', 'pamus', 'assignments', 'assignmentType'])
            ->orderBy('start_date', 'desc')
            ->get();

        $data_items = [
            'data' => $assignmenthistory,
            'column_hidden' => $this->config_data->columnHidden,
            'column_labels' => $this->config_data->columnLabels,
            'operation_type' => 'show',
            'optional_fields' => $this->config_data->optionalFields,
            'pm_codes' => $pmcodes,
            'designations' => $designations,
            'units' => $units,
            'pamus' => $pamus,
            'assignments' => $assignments,
            'assignment_type3' => $assignment_type3,
            'officerData' => $officerData,
            'relatedHistories' => $relatedHistories,
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    public function edit(AssignmentHistory $assignmenthistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $officerData = Officer::with(['designations', 'units'])
            ->where('PM_CODE', $assignmenthistory->pm_code)
            ->first();

        // Store officer data in session for edit mode
        $this->storeOfficerInSession($assignmenthistory->pm_code, $officerData);

        $designations = Designation::pluck('name', 'id');
        $units = Unit::with('pamus')->get()->keyBy('id');
        $pamus = Pamu::all()->pluck('name', 'id');

        $assignment_type3 = Assignment::where('type_id', 3)
            ->orderBy('name')
            ->pluck('name', 'id');

        $assignments = Assignment::where('type_id', '!=', 5)
            ->orderBy('name')
            ->pluck('name', 'id');

        $pmcodes = Officer::select('pm_code')
            ->whereNotNull('pm_code')
            ->where('pm_code', '!=', '')
            ->distinct()
            ->orderBy('pm_code')
            ->pluck('pm_code');

        $relatedHistories = AssignmentHistory::query()
            ->where('pm_code', $assignmenthistory->pm_code)
            ->where('id', '!=', $assignmenthistory->id)
            ->where('pm_code', '!=', '')
            ->whereNotNull('pm_code')
            ->with(['designations', 'units', 'pamus', 'assignments', 'assignmentType'])
            ->orderBy('start_date', 'desc')
            ->get();

        session(['relatedHistories' => $relatedHistories]);

        $data_items = [
            'data' => $assignmenthistory,
            'column_hidden' => $this->config_data->columnHidden,
            'column_labels' => $this->config_data->columnLabels,
            'operation_type' => 'edit',
            'optional_fields' => $this->config_data->optionalFields,
            'pm_codes' => $pmcodes,
            'designations' => $designations,
            'units' => $units,
            'pamus' => $pamus,
            'assignments' => $assignments,
            'assignment_type3' => $assignment_type3,
            'officerData' => $officerData,
            'relatedHistories' => $relatedHistories,
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    public function update(Request $request, AssignmentHistory $assignmentHistory)
    {
        // CRITICAL FIX: Separate Fetch from Update
        $action = $request->input('action');
        
        // If Fetch Data button clicked in edit mode
        if ($action === 'Fetch Data') {
            $pm_code = $request->input('pm_code');
            
            $officer = Officer::with(['designations', 'units'])
                ->where('PM_CODE', $pm_code)
                ->first();

            if (!$officer) {
                return back()->withInput($request->all())
                    ->withErrors(['pm_code' => 'PM Code not found.']);
            }

            // Store officer in session
            $this->storeOfficerInSession($pm_code, $officer);

            $relatedHistories = AssignmentHistory::where('pm_code', $pm_code)
                ->where('id', '!=', $assignmentHistory->id)
                ->with(['designations', 'units', 'pamus', 'assignments', 'assignmentType'])
                ->orderBy('start_date', 'desc')
                ->get();

            session(['relatedHistories' => $relatedHistories]);

            // Return to edit page with officer data
            return back()->withInput($request->all());
        }

        // Otherwise proceed with Update
        $data = $request->validate([
            'pm_code' => ['required', 'string'],
            'designation_id' => ['nullable', 'string'],
            'new_designation_name' => ['nullable', 'required_if:designation_id,new', 'string', 'max:255'],
            'subunit' => ['nullable', 'string'],
            'unit_id' => ['nullable', 'integer'],
            'new_unit_name' => ['nullable', 'required_if:unit_id,new', 'string', 'max:255'],
            'new_unit_pamu_id' => ['nullable', 'string'],
            'new_pamu_name' => ['nullable', 'required_if:new_unit_pamu_id,new_pamu', 'string', 'max:255'],
            'pamu_id' => ['nullable', 'integer'],
            'assignment_id' => ['nullable', 'string'],
            'pri_sec_spec' => ['nullable', 'string'],
            'assignmentType' => ['nullable', 'string'],
            'geography' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'rank_during_completion' => ['nullable', 'string'],
            'year_earned' => ['nullable', 'string'],
        ]);

        // Handle new designation creation
        if ($data['designation_id'] === 'new' && !empty($data['new_designation_name'])) {
            $newDesignation = Designation::firstOrCreate(['name' => $data['new_designation_name']]);
            $data['designation_id'] = $newDesignation->id;
        }

        // Handle new unit + PAMU creation
        if (isset($data['unit_id']) && $data['unit_id'] === 'new' && !empty($data['new_unit_name'])) {
            $pamuId = null;

            if (isset($data['new_unit_pamu_id']) && $data['new_unit_pamu_id'] === 'new_pamu' && !empty($data['new_pamu_name'])) {
                $newPamu = Pamu::firstOrCreate(['name' => $data['new_pamu_name']]);
                $pamuId = $newPamu->id;
            } elseif (!empty($data['new_unit_pamu_id']) && $data['new_unit_pamu_id'] !== 'new_pamu') {
                $pamuId = $data['new_unit_pamu_id'];
            }

            $newUnit = Unit::create([
                'name' => $data['new_unit_name'],
                'pamu_id' => $pamuId,
            ]);
            $data['unit_id'] = $newUnit->id;
            $data['pamu_id'] = $pamuId;
        }

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

        if ($pri === 'primary' && $this->overlapsExistingPrimary($data['pm_code'], $sd, $ed, $assignmentHistory->id)) {
            $data['year_earned'] = 0;
        } else {
            $data['year_earned'] = $this->yearfrac_us_30_360($sd, $ed);
        }

        $assignmentHistory->update($data);

        // Recompute points after update
        $this->pointsService->recomputeAndSave($assignmentHistory->fresh());

        return redirect()->route($this->config_data->module_route . '.index');
    }

    public function destroy(AssignmentHistory $assignmentHistory)
    {
        $assignmentHistory->delete();
        return redirect()->route($this->config_data->module_route . '.index');
    }

    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $dtColumns = [
            null,           // 0: row number
            'pm_code',      // 1
            'start_date',   // 2
            'end_date',     // 3
            'designations.name', // 4
            'subunit',      // 5
            'units.name',   // 6
            'pamus.name',   // 7
            'assignments.name', // 8
            'pri_sec_spec', // 9
            'assignment_type', // 10
            'geography',    // 11
            'rank_during_completion', // 12
            'year_earned',  // 13
            'computed_points', // 14
            null,           // 15: action
        ];

        $globalSearchColumns = [
            'pm_code',
            'start_date',
            'end_date',
            'designations.name',
            'subunit',
            'units.name',
            'pamus.name',
            'assignments.name',
            'pri_sec_spec',
            'geography',
            'rank_during_completion',
            'year_earned',
            'computed_points',
        ];

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length <= 0)
            $length = 10;

        $query = AssignmentHistory::query();

        // global search
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

        // per-column search
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

        $query->orderBy('start_date', 'desc')->orderBy('id', 'desc');

        $data = $query->skip($start)
            ->take($length)
            ->with([
                'designations:id,name',
                'units:id,name',
                'pamus:id,name',
                'assignments:id,name,type_id',
                'assignmentType:id,name',
            ])
            ->get()
            ->map(function ($item) {
                $item->assignment_type_name = $item->assignmentType->name ?? '';
                return $item;
            });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $filteredData,
            'data' => $data,
        ]);
    }

    /**
     * YEARFRAC US 30/360 - matches Excel YEARFRAC(sd, ed) with basis=0 (default)
     */
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

    /**
     * Check if a primary assignment overlaps with existing primary assignments
     */
    private function overlapsExistingPrimary(string $pmCode, Carbon $sd, Carbon $ed, ?int $ignoreId = null): bool
    {
        $edMinus1 = $ed->copy()->subDay();

        $q = AssignmentHistory::query()
            ->where('pm_code', $pmCode)
            ->where('pri_sec_spec', 'primary')
            ->whereRaw('DATE(start_date) <= ?', [$edMinus1->toDateString()])
            ->whereRaw('DATE(end_date) - INTERVAL 1 DAY >= ?', [$sd->toDateString()]);

        if ($ignoreId) {
            $q->where('id', '!=', $ignoreId);
        }

        return $q->exists();
    }

    /**
     * Lookup the rank at a given date from the date_ranks table
     */
    private function lookupRankAt(string $pmCode, Carbon $date): ?string
    {
        return DateRank::query()
            ->where('pm_code', $pmCode)
            ->whereDate('date', '<=', $date->toDateString())
            ->join('ranks', 'date_ranks.rank_id', '=', 'ranks.id')
            ->orderBy('date_ranks.date', 'desc')
            ->value('ranks.code');
    }

    /**
     * Compute rank during completion following the Excel formula
     */
    private function computeRankDuringCompletionExcel(string $pmCode, Carbon $sd, Carbon $ed, string $priSecSpec): array
    {
        $edAdj = $ed->gt($sd) ? $ed->copy()->subDay() : $sd->copy();

        $sr = $this->lookupRankAt($pmCode, $sd);
        $er = $this->lookupRankAt($pmCode, $edAdj);

        if (empty($sr) || empty($er)) {
            return ['ok' => false, 'rank' => '', 'message' => 'No rank'];
        }

        $isPrimary = ($priSecSpec === 'primary');

        if ($isPrimary) {
            if ($sr === $er) {
                return ['ok' => true, 'rank' => $sr, 'message' => null];
            }
            return ['ok' => false, 'rank' => '', 'message' => 'Rank conflict - rank changed during assignment period'];
        }

        return ['ok' => true, 'rank' => $sr, 'message' => null];
    }

    /**
     * AJAX endpoint for live year_earned + rank computation
     */
    public function computeYearEarned(Request $request)
    {
        $data = $request->validate([
            'pm_code' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'pri_sec_spec' => ['nullable', 'string'],
            'id' => ['nullable', 'integer'],
        ]);

        $pm = $data['pm_code'];
        $sd = Carbon::parse($data['start_date'])->startOfDay();
        $ed = Carbon::parse($data['end_date'])->startOfDay();
        $pri = $data['pri_sec_spec'] ?? '';

        if ($sd->gt($ed)) {
            return response()->json([
                'ok' => false,
                'year_earned' => 0,
                'rank_during_completion' => '',
                'message' => 'Invalid dates',
            ], 422);
        }

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
                'ok' => true,
                'year_earned' => 0,
                'rank_during_completion' => $rankResult['rank'],
                'message' => 'Dates overlap - year earned is 0',
            ]);
        }

        return response()->json([
            'ok' => true,
            'year_earned' => $this->yearfrac_us_30_360($sd, $ed),
            'rank_during_completion' => $rankResult['rank'],
        ]);
    }

    /**
     * AJAX: Create a new designation on-the-fly
     */
    public function storeDesignation(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
        ]);

        $designation = Designation::firstOrCreate(['name' => $data['name']]);

        return response()->json([
            'ok' => true,
            'id' => $designation->id,
            'name' => $designation->name,
        ]);
    }

    /**
     * AJAX: Create a new unit on-the-fly (with optional new PAMU)
     */
    public function storeUnit(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'pamu_id' => ['nullable', 'integer', 'exists:pamus,id'],
            'new_pamu_name' => ['nullable', 'string', 'max:50'],
        ]);

        // If new_pamu_name is provided, create PAMU first
        $pamuId = $data['pamu_id'] ?? null;
        $pamuName = '';

        if (!empty($data['new_pamu_name'])) {
            $pamu = Pamu::firstOrCreate(['name' => $data['new_pamu_name']]);
            $pamuId = $pamu->id;
            $pamuName = $pamu->name;
        } elseif ($pamuId) {
            $pamuName = Pamu::find($pamuId)->name ?? '';
        }

        $unit = Unit::create([
            'name' => $data['name'],
            'pamu_id' => $pamuId,
        ]);

        return response()->json([
            'ok' => true,
            'id' => $unit->id,
            'name' => $unit->name,
            'pamu_id' => $pamuId,
            'pamu_name' => $pamuName,
        ]);
    }
}