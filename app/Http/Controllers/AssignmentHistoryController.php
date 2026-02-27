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
            "module_name"        => "Assignment History",
            "module_perm_name"   => "assignmenthistory",
            "module_route"       => "assignmenthistories",
            "module_view_folder" => "officerdata.assignmenthistory",
            "columnHidden"       => $columnHidden,
            "columnLabels"       => $columnLabels,
            "optionalFields"     => $optionalFields,
        ];

        view()->share('config_data', $this->config_data);
    }

    // ──────────────────────────────────────────────────────────────
    //  INDEX
    // ──────────────────────────────────────────────────────────────
    public function index()
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view($this->config_data->module_view_folder . '.index');
    }

    // ──────────────────────────────────────────────────────────────
    //  CREATE (blank form)
    // ──────────────────────────────────────────────────────────────
    public function create()
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (!old('pm_code')) {
            session()->forget([
                'pm_code', 'name', 'rank', 'afpos', 'afpsn',
                'sex', 'dob', 'date_ret', 'dor', 'soc', 'sig',
                'designation', 'unit', 'designation_id', 'unit_id',
                'assignment_id', 'relatedHistories',
            ]);
        }

        $assignmenthistory = new AssignmentHistory();
        $data_items        = $this->buildFormDataItems($assignmenthistory, 'create', null, null);

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    // ──────────────────────────────────────────────────────────────
    //  CREATE FROM EXISTING  (prefill pm_code)
    // ──────────────────────────────────────────────────────────────
    public function createFromExisting(AssignmentHistory $assignmenthistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $blank = new AssignmentHistory();
        $blank->fill([
            'pm_code'        => $assignmenthistory->pm_code,
            'designation_id' => null,
            'unit_id'        => null,
            'assignment_id'  => null,
        ]);

        $officerData = Officer::where('PM_CODE', $assignmenthistory->pm_code)->first();
        $this->storeOfficerInSession($assignmenthistory->pm_code, $officerData);

        $data_items = $this->buildFormDataItems($blank, 'create', $officerData, $assignmenthistory->pm_code);

        return view($this->config_data->module_view_folder . '.show', compact('data_items'))
            ->with('config_data', $this->config_data);
    }

    // ──────────────────────────────────────────────────────────────
    //  STORE  (create form POST)
    // ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $action  = $request->input('action');
        $pm_code = $request->input('pm_code');

        // ── Fetch Data action ──────────────────────────────────────
        if ($action === 'Fetch Data') {

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
                    'rank'             => $officer->RANK,
                    'name'             => $officer->NAME,
                    'afpsn'            => $officer->AFPSN,
                    'afpos'            => $officer->AFPOS,
                    'sex'              => $officer->SEX,
                    'dob'              => $officer->DOB,
                    'date_ret'         => $officer->RET,
                    'soc'              => $officer->SOC,
                    'type'             => $officer->TYPE,
                    'otd'              => $officer->OTD,
                    'dor'              => $officer->DOR,
                    'sig'              => $officer->SIG,
                    'designation'      => $officer->designations->name ?? '',
                    'unit'             => $officer->units?->name ?? '',
                    'relatedHistories' => $relatedHistories,
                ]);
        }

        // ── Save action ───────────────────────────────────────────
        if ($action === 'Save') {
            return $this->saveRecord($request, null);
        }
    }

    // ──────────────────────────────────────────────────────────────
    //  SHOW
    // ──────────────────────────────────────────────────────────────
    public function show(AssignmentHistory $assignmenthistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $officerData = Officer::with(['designations', 'units'])
            ->where('PM_CODE', $assignmenthistory->pm_code)
            ->first();

        $data_items = $this->buildFormDataItems($assignmenthistory, 'show', $officerData, $assignmenthistory->pm_code);

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    // ──────────────────────────────────────────────────────────────
    //  EDIT
    // ──────────────────────────────────────────────────────────────
    public function edit(AssignmentHistory $assignmenthistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Determine which pm_code to use – allow a flash from a Fetch inside edit
        $pmCode = session('edit_fetch_pm_code_' . $assignmenthistory->id, $assignmenthistory->pm_code);
        session()->forget('edit_fetch_pm_code_' . $assignmenthistory->id);

        $officerData = Officer::with(['designations', 'units'])
            ->where('PM_CODE', $pmCode)
            ->first();

        $data_items = $this->buildFormDataItems($assignmenthistory, 'edit', $officerData, $pmCode);

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    // ──────────────────────────────────────────────────────────────
    //  UPDATE
    // ──────────────────────────────────────────────────────────────
    public function update(Request $request, AssignmentHistory $assignmenthistory)
    {
        $action = $request->input('action');

        // ── Fetch Data inside edit form ────────────────────────────
        if ($action === 'Fetch Data') {
            $pm_code = $request->input('pm_code');

            $officer = Officer::with(['designations', 'units'])
                ->where('PM_CODE', $pm_code)
                ->first();

            if (!$officer) {
                return back()->withInput($request->all())
                    ->withErrors(['pm_code' => 'PM Code not found.']);
            }

            $relatedHistories = AssignmentHistory::where('pm_code', $pm_code)
                ->where('id', '!=', $assignmenthistory->id)
                ->with(['designations', 'units', 'pamus', 'assignments', 'assignmentType'])
                ->orderBy('start_date', 'desc')
                ->get();

            return back()->withInput($request->all())
                ->with([
                    'rank'             => $officer->RANK,
                    'name'             => $officer->NAME,
                    'afpsn'            => $officer->AFPSN,
                    'afpos'            => $officer->AFPOS,
                    'sex'              => $officer->SEX,
                    'dob'              => $officer->DOB,
                    'date_ret'         => $officer->RET,
                    'soc'              => $officer->SOC,
                    'dor'              => $officer->DOR,
                    'sig'              => $officer->SIG,
                    'designation'      => $officer->designations->name ?? '',
                    'unit'             => $officer->units?->name ?? '',
                    'relatedHistories' => $relatedHistories,
                    'fetched_officer'  => true,
                ]);
        }

        // ── Update / Save ─────────────────────────────────────────
        return $this->saveRecord($request, $assignmenthistory);
    }

    // ──────────────────────────────────────────────────────────────
    //  DESTROY
    // ──────────────────────────────────────────────────────────────
    public function destroy(AssignmentHistory $assignmenthistory)
    {
        $assignmenthistory->delete();
        return redirect()->route($this->config_data->module_route . '.index');
    }

    // ──────────────────────────────────────────────────────────────
    //  LIST  (DataTables server-side)
    // ──────────────────────────────────────────────────────────────
    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $dtColumns = [
            null,                // 0 row number
            'pm_code',           // 1
            'start_date',        // 2
            'end_date',          // 3
            'designations.name', // 4
            'subunit',           // 5
            'units.name',        // 6
            'pamus.name',        // 7
            'assignments.name',  // 8
            'pri_sec_spec',      // 9
            'assignment_type',   // 10
            'geography',         // 11
            'rank_during_completion', // 12
            'year_earned',       // 13
            'computed_points',   // 14
            null,                // 15 action
        ];

        $globalSearchColumns = [
            'pm_code', 'start_date', 'end_date', 'designations.name',
            'subunit', 'units.name', 'pamus.name', 'assignments.name',
            'pri_sec_spec', 'geography', 'rank_during_completion',
            'year_earned', 'computed_points',
        ];

        $start  = (int) $request->input('start', 0);
        $length = max(1, (int) $request->input('length', 10));

        $query = AssignmentHistory::query();

        // Global search
        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search, $globalSearchColumns) {
                foreach ($globalSearchColumns as $col) {
                    if (str_contains($col, '.')) {
                        [$relation, $field] = explode('.', $col, 2);
                        $q->orWhereHas($relation, fn ($r) => $r->where($field, 'like', "%{$search}%"));
                        continue;
                    }
                    $q->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        // Per-column search
        foreach ($dtColumns as $index => $column) {
            if (!$column) continue;
            $colSearch = trim((string) $request->input("columns.$index.search.value", ''));
            if ($colSearch === '') continue;

            if (str_contains($column, '.')) {
                [$relation, $field] = explode('.', $column, 2);
                $query->whereHas($relation, fn ($r) => $r->where($field, 'like', "%{$colSearch}%"));
            } else {
                $query->where($column, 'like', "%{$colSearch}%");
            }
        }

        $totalData    = AssignmentHistory::count();
        $filteredData = (clone $query)->count();

        $data = $query
            ->orderBy('start_date', 'desc')
            ->orderBy('id', 'desc')
            ->skip($start)
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
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $filteredData,
            'data'            => $data,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  AJAX – compute year_earned + rank
    // ──────────────────────────────────────────────────────────────
    public function computeYearEarned(Request $request)
    {
        $data = $request->validate([
            'pm_code'      => ['required', 'string'],
            'start_date'   => ['nullable', 'date'],
            'end_date'     => ['nullable', 'date'],
            'pri_sec_spec' => ['nullable', 'string'],
            'id'           => ['nullable', 'integer'],
        ]);

        $pm  = $data['pm_code'];
        $pri = $data['pri_sec_spec'] ?? '';

        // Empty dates → return blanks (allow saving without dates)
        if (empty($data['start_date']) || empty($data['end_date'])) {
            return response()->json([
                'ok'                   => true,
                'year_earned'          => null,
                'rank_during_completion' => '',
                'message'              => '',
            ]);
        }

        $sd = Carbon::parse($data['start_date'])->startOfDay();
        $ed = Carbon::parse($data['end_date'])->startOfDay();

        if ($sd->gt($ed)) {
            return response()->json([
                'ok'                     => false,
                'year_earned'            => null,
                'rank_during_completion' => '',
                'rank_warning'           => '',
                'message'                => 'Invalid dates: start date must be before end date',
            ], 422);
        }

        // Rank lookup – failure becomes a rank_warning, not a fatal error
        $rankResult  = $this->computeRankDuringCompletionExcel($pm, $sd, $ed, $pri);
        $rankValue   = $rankResult['rank'];
        $rankWarning = $rankResult['ok'] ? '' : ($rankResult['message'] ?? 'No rank found');

        // Overlap check (primary only) – still blocks year_earned → 0
        $isPrimary = ($pri === 'primary');
        if ($isPrimary && $this->overlapsExistingPrimary($pm, $sd, $ed, $data['id'] ?? null)) {
            return response()->json([
                'ok'                     => true,
                'year_earned'            => 0,
                'rank_during_completion' => $rankValue,
                'rank_warning'           => $rankWarning,
                'message'                => 'Dates overlap – year earned set to 0',
            ]);
        }

        // Always compute year_earned regardless of rank status
        return response()->json([
            'ok'                     => true,
            'year_earned'            => $this->yearfrac_us_30_360($sd, $ed),
            'rank_during_completion' => $rankValue,
            'rank_warning'           => $rankWarning,
            'message'                => '',
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  AJAX – create designation on-the-fly
    // ──────────────────────────────────────────────────────────────
    public function storeDesignation(Request $request)
    {
        $data        = $request->validate(['name' => ['required', 'string', 'max:50']]);
        $designation = Designation::firstOrCreate(['name' => $data['name']]);

        return response()->json(['ok' => true, 'id' => $designation->id, 'name' => $designation->name]);
    }

    // ──────────────────────────────────────────────────────────────
    //  AJAX – create unit on-the-fly
    // ──────────────────────────────────────────────────────────────
    public function storeUnit(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:50'],
            'pamu_id'       => ['nullable', 'integer', 'exists:pamus,id'],
            'new_pamu_name' => ['nullable', 'string', 'max:50'],
        ]);

        $pamuId   = $data['pamu_id'] ?? null;
        $pamuName = '';

        if (!empty($data['new_pamu_name'])) {
            $pamu     = Pamu::firstOrCreate(['name' => $data['new_pamu_name']]);
            $pamuId   = $pamu->id;
            $pamuName = $pamu->name;
        } elseif ($pamuId) {
            $pamuName = Pamu::find($pamuId)->name ?? '';
        }

        $unit = Unit::create(['name' => $data['name'], 'pamu_id' => $pamuId]);

        return response()->json([
            'ok'        => true,
            'id'        => $unit->id,
            'name'      => $unit->name,
            'pamu_id'   => $pamuId,
            'pamu_name' => $pamuName,
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    //  PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════

    /**
     * Shared save logic for both store() and update().
     */
    private function saveRecord(Request $request, ?AssignmentHistory $existing)
    {
        $data = $request->validate([
            'pm_code'                  => ['required', 'string'],
            'designation_id'           => ['nullable', 'string'],
            'new_designation_name'     => ['nullable', 'required_if:designation_id,new', 'string', 'max:255'],
            'subunit'                  => ['nullable', 'string'],
            'unit_id'                  => ['nullable', 'string'],
            'new_unit_name'            => ['nullable', 'required_if:unit_id,new', 'string', 'max:255'],
            'new_unit_pamu_id'         => ['nullable', 'string'],
            'new_pamu_name'            => ['nullable', 'required_if:new_unit_pamu_id,new_pamu', 'string', 'max:255'],
            'pamu_id'                  => ['nullable', 'string'],
            'new_pamu_standalone_name' => ['nullable', 'required_if:pamu_id,new', 'string', 'max:255'],
            'assignment_id'            => ['nullable', 'string'],
            'pri_sec_spec'             => ['nullable', 'string'],
            'assignment_type'          => ['nullable', 'string'],
            'geography'                => ['nullable', 'string'],
            'start_date'               => ['nullable', 'date'],
            'end_date'                 => ['nullable', 'date'],
            'year_earned'              => ['nullable', 'string'],
        ]);

        // ── Inline designation creation ───────────────────────────
        if (($data['designation_id'] ?? '') === 'new' && !empty($data['new_designation_name'])) {
            $data['designation_id'] = Designation::firstOrCreate(['name' => $data['new_designation_name']])->id;
        }

        // ── Inline unit + PAMU creation ───────────────────────────
        if (($data['unit_id'] ?? '') === 'new' && !empty($data['new_unit_name'])) {
            $pamuId = null;

            if (($data['new_unit_pamu_id'] ?? '') === 'new_pamu' && !empty($data['new_pamu_name'])) {
                $pamuId = Pamu::firstOrCreate(['name' => $data['new_pamu_name']])->id;
            } elseif (!empty($data['new_unit_pamu_id']) && $data['new_unit_pamu_id'] !== 'new_pamu') {
                $pamuId = $data['new_unit_pamu_id'];
            }

            $newUnit         = Unit::create(['name' => $data['new_unit_name'], 'pamu_id' => $pamuId]);
            $data['unit_id'] = $newUnit->id;
        }

        // ── Standalone PAMU creation ──────────────────────────────
        if (($data['pamu_id'] ?? '') === 'new' && !empty($data['new_pamu_standalone_name'])) {
            $data['pamu_id'] = Pamu::firstOrCreate(['name' => $data['new_pamu_standalone_name']])->id;
        }

        // ── Compute rank + year_earned only when both dates present ─
        $hasDates = !empty($data['start_date']) && !empty($data['end_date']);

        if ($hasDates) {
            $sd  = Carbon::parse($data['start_date'])->startOfDay();
            $ed  = Carbon::parse($data['end_date'])->startOfDay();
            $pri = $data['pri_sec_spec'] ?? '';

            if ($sd->gt($ed)) {
                return back()->withErrors(['year_earned' => 'Invalid dates'])->withInput();
            }

            $rankResult = $this->computeRankDuringCompletionExcel($data['pm_code'], $sd, $ed, $pri);
            $data['rank_during_completion'] = $rankResult['rank'] ?: null;

            $ignoreId = $existing?->id;

            if ($pri === 'primary' && $this->overlapsExistingPrimary($data['pm_code'], $sd, $ed, $ignoreId)) {
                $data['year_earned'] = 0;
            } else {
                $data['year_earned'] = $this->yearfrac_us_30_360($sd, $ed);
            }
        } else {
            $data['rank_during_completion'] = $data['rank_during_completion'] ?? null;
            $data['year_earned']            = null;
        }

        // ── Persist ───────────────────────────────────────────────
        if ($existing) {
            $existing->update($data);
            $record = $existing->fresh();
        } else {
            $match  = [
                'pm_code'        => $data['pm_code'],
                'designation_id' => $data['designation_id'] ?? null,
                'unit_id'        => $data['unit_id'] ?? null,
                'assignment_id'  => $data['assignment_id'] ?? null,
                'pri_sec_spec'   => $data['pri_sec_spec'] ?? null,
                'geography'      => $data['geography'] ?? null,
                'start_date'     => $data['start_date'] ?? null,
                'end_date'       => $data['end_date'] ?? null,
                'rank_during_completion' => $data['rank_during_completion'] ?? null,
            ];
            $record = AssignmentHistory::updateOrCreate($match, $data);
        }

        $this->pointsService->recomputeAndSave($record);

        return redirect()->route($this->config_data->module_route . '.index');
    }

    /**
     * Build the $data_items array shared by create / edit / show views.
     */
    private function buildFormDataItems(
        AssignmentHistory $record,
        string $op,
        ?Officer $officerData,
        ?string $pmCode
    ): array {
        $relatedHistories = collect([]);

        if ($pmCode) {
            $q = AssignmentHistory::where('pm_code', $pmCode)
                ->with(['designations', 'units', 'pamus', 'assignments', 'assignmentType'])
                ->orderBy('start_date', 'desc');

            if ($record->id) {
                $q->where('id', '!=', $record->id);
            }

            $relatedHistories = $q->get();
        }

        return [
            'data'              => $record,
            'column_hidden'     => $this->config_data->columnHidden,
            'column_labels'     => $this->config_data->columnLabels,
            'operation_type'    => $op,
            'optional_fields'   => $this->config_data->optionalFields,
            'pm_codes'          => $this->getPmCodes(),
            'designations'      => Designation::all()->pluck('name', 'id'),
            'units'             => Unit::with('pamus')->get()->keyBy('id'),
            'pamus'             => Pamu::all()->pluck('name', 'id'),
            'assignments'       => Assignment::whereNotIn('type_id', [5, 10, 11])->orderBy('name')->pluck('name', 'id'),
            'assignment_type3'  => Assignment::where('type_id', 3)->orderBy('name')->pluck('name', 'id'),
            'officerData'       => $officerData,
            'relatedHistories'  => $relatedHistories,
            'currentPmCode'     => $pmCode ?? '',
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

    private function storeOfficerInSession(string $pmCode, ?Officer $officer): void
    {
        session([
            'pm_code'     => $pmCode,
            'name'        => $officer->NAME ?? '',
            'rank'        => $officer->RANK ?? '',
            'afpos'       => $officer->AFPOS ?? '',
            'afpsn'       => $officer->AFPSN ?? '',
            'sex'         => $officer->SEX ?? '',
            'dob'         => $officer->DOB ?? '',
            'date_ret'    => $officer->RET ?? '',
            'dor'         => $officer->DOR ?? '',
            'soc'         => $officer->SOC ?? '',
            'sig'         => $officer->SIG ?? '',
            'designation' => $officer->designations->name ?? '',
            'unit'        => $officer->units->name ?? '',
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

        if ($d1 == 31) $d1 = 30;
        if ($d2 == 31 && $d1 == 30) $d2 = 30;

        $days360 = (360 * ($y2 - $y1)) + (30 * ($m2 - $m1)) + ($d2 - $d1);
        return round($days360 / 360, 6);
    }

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

    private function lookupRankAt(string $pmCode, Carbon $date): ?string
    {
        return DateRank::query()
            ->where('pm_code', $pmCode)
            ->whereDate('date', '<=', $date->toDateString())
            ->join('ranks', 'date_ranks.rank_id', '=', 'ranks.id')
            ->orderBy('date_ranks.date', 'desc')
            ->value('ranks.code');
    }

    private function computeRankDuringCompletionExcel(string $pmCode, Carbon $sd, Carbon $ed, string $priSecSpec): array
    {
        $edAdj = $ed->gt($sd) ? $ed->copy()->subDay() : $sd->copy();

        $sr = $this->lookupRankAt($pmCode, $sd);
        $er = $this->lookupRankAt($pmCode, $edAdj);

        if (empty($sr) || empty($er)) {
            return ['ok' => false, 'rank' => '', 'message' => 'No rank found'];
        }

        $isPrimary = ($priSecSpec === 'primary');

        if ($isPrimary) {
            if ($sr === $er) {
                return ['ok' => true, 'rank' => $sr, 'message' => null];
            }
            return ['ok' => false, 'rank' => '', 'message' => 'Rank conflict – rank changed during period'];
        }

        return ['ok' => true, 'rank' => $sr, 'message' => null];
    }
}