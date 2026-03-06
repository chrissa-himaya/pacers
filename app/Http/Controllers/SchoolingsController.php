<?php
namespace App\Http\Controllers;

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
    protected $config_data;

    public function __construct(Schooling $schooling)
    {
        $columnHidden = array_merge($schooling->getDates(), ['id']);
        $columnLabels = [''];
        $optionalFields = ['name', 'email'];

        $this->config_data = (object) [
            "module_name"       => "Schooling",
            "module_perm_name"  => "schooling",
            "module_route"      => "schoolings",
            "module_view_folder"=> "officerdata.schooling",
            "columnHidden"      => $columnHidden,
            "columnLabels"      => $columnLabels,
            "optionalFields"    => $optionalFields,
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

        if (!old('pm_code')) {
            session()->forget([
                'pm_code', 'name', 'rank', 'afpos', 'afpsn', 'sex',
                'dob', 'date_ret', 'dor', 'soc', 'sig',
                'designation', 'unit', 'relatedSchoolings',
            ]);
        }

        $schooling = Schooling::find(1);
        $schooling->fill([
            'pm_code'           => null,
            'schoolingname_id'  => null,
            'schooling_unit_id' => null,
            'assignment_id'     => null,
        ]);

        $schoolingnamesWithAssignments = SchoolingName::all()->map(fn($item) => [
            'id'            => $item->id,
            'name'          => $item->name,
            'assignment_id' => $item->assignment_id,
        ]);

        $schoolingnames = SchoolingName::all()->pluck('name', 'id');
        $assignments    = Assignment::where('type_id', 5)->orderBy('name')->pluck('name', 'id');
        $schoolingUnits = SchoolingUnit::query()->select('id', 'name', 'location')->orderBy('name')->get();
        $pmcodes        = $this->getPmCodes();

        $data_items = [
            "data"                            => $schooling,
            "column_hidden"                   => $this->config_data->columnHidden,
            "column_labels"                   => $this->config_data->columnLabels,
            "operation_type"                  => "create",
            "optional_fields"                 => $this->config_data->optionalFields,
            "pm_codes"                        => $pmcodes,
            "schoolingnames"                  => $schoolingnames,
            "schoolingnames_with_assignments" => $schoolingnamesWithAssignments,
            "schoolingUnits"                  => $schoolingUnits,
            "assignments"                     => $assignments,
            "seclt" => 0, "firstlt" => 0, "cpt" => 0,
            "maj"   => 0, "ltc"     => 0, "col" => 0,
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    /* ─── CREATE FROM EXISTING ─── */
    public function createFromExisting(Schooling $schooling)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $blank = Schooling::find(1);
        $blank->fill([
            'pm_code'           => $schooling->pm_code,
            'schoolingname_id'  => null,
            'schooling_unit_id' => null,
            'assignment_id'     => null,
        ]);

        $officerData = Officer::where('PM_CODE', $schooling->pm_code)->first();
        $this->storeOfficerInSession($schooling->pm_code, $officerData);
        $data_items = $this->buildCreateDataItems($blank, $officerData, $schooling->pm_code);

        return view($this->config_data->module_view_folder . '.show', compact('data_items'))
            ->with('config_data', $this->config_data);
    }

    private function storeOfficerInSession(string $pmCode, ?Officer $officer): void
    {
        session([
            'pm_code'     => $pmCode,
            'name'        => $officer->NAME      ?? '',
            'rank'        => $officer->RANK      ?? '',
            'afpos'       => $officer->AFPOS     ?? '',
            'afpsn'       => $officer->AFPSN     ?? '',
            'sex'         => $officer->SEX       ?? '',
            'dob'         => $officer->DOB       ?? '',
            'date_ret'    => $officer->RET       ?? '',
            'dor'         => $officer->DOR       ?? '',
            'soc'         => $officer->SOC       ?? '',
            'sig'         => $officer->SIG       ?? '',
            'designation' => $officer->designations->name ?? '',
            'unit'        => $officer->units->name        ?? '',
        ]);
    }

    private function buildCreateDataItems(Schooling $blank, ?Officer $officerData, string $pmCode): array
    {
        $relatedSchoolings = Schooling::where('pm_code', $pmCode)
            ->with(['schoolingnames', 'schoolingunits', 'assignments'])
            ->orderBy('date_completed', 'desc')
            ->get();

        session(['relatedSchoolings' => $relatedSchoolings]);

        return [
            'data'                            => $blank,
            'column_hidden'                   => $this->config_data->columnHidden,
            'column_labels'                   => $this->config_data->columnLabels,
            'operation_type'                  => 'create',
            'optional_fields'                 => $this->config_data->optionalFields,
            'pm_codes'                        => $this->getPmCodes(),
            'schoolingnames'                  => SchoolingName::all()->pluck('name', 'id'),
            'schoolingnames_with_assignments' => SchoolingName::all()->map(fn($i) => [
                'id' => $i->id, 'name' => $i->name, 'assignment_id' => $i->assignment_id,
            ]),
            'schoolingUnits'    => SchoolingUnit::select('id', 'name', 'location')->orderBy('name')->get(),
            'assignments'       => Assignment::where('type_id', 5)->orderBy('name')->pluck('name', 'id'),
            'officerData'       => $officerData,
            'relatedSchoolings' => $relatedSchoolings,
            'relatedHistories'  => collect([]),
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

    /* ─── AJAX: entries by assignment ─── */
    public function getEntriesByAssignment(Request $request)
    {
        $assignmentId = $request->input('assignment_id');
        if (!$assignmentId) return response()->json([]);

        return response()->json(
            SchoolingName::where('assignment_id', $assignmentId)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    /* ─── AJAX: create new schooling unit ─── */
    public function createUnit(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:50'],
            'location' => ['required', 'in:local,foreign'],
        ]);

        $name     = trim($request->input('name'));
        $location = $request->input('location');

        // Return existing unit if name+location already exists (idempotent)
        $existing = SchoolingUnit::where('name', $name)
            ->where('location', $location)
            ->first();

        if ($existing) {
            return response()->json([
                'id'       => $existing->id,
                'name'     => $existing->name,
                'location' => $existing->location,
            ]);
        }

        $unit = SchoolingUnit::create([
            'name'     => $name,
            'location' => $location,
        ]);

        return response()->json([
            'id'       => $unit->id,
            'name'     => $unit->name,
            'location' => $unit->location,
        ], 201);
    }

    /* ─── Resolve or create SchoolingName ─── */
    private function resolveOrCreateSchoolingName(Request $request): void
    {
        if ($request->input('schoolingname_id') !== 'new') return;

        $newEntryName = trim((string) $request->input('new_entry_name'));
        $assignmentId = $request->input('assignment_id');

        $request->validate([
            'new_entry_name' => ['required', 'string'],
            'assignment_id'  => ['required'],
        ], [
            'new_entry_name.required' => 'Entry name is required when creating new entry',
            'assignment_id.required'  => 'Assignment must be selected before creating new entry',
        ]);

        $existing = SchoolingName::where('assignment_id', $assignmentId)
            ->where('name', $newEntryName)
            ->first();

        if ($existing) {
            $request->merge(['schoolingname_id' => $existing->id]);
            return;
        }

        $newEntry = SchoolingName::create([
            'name'          => $newEntryName,
            'assignment_id' => $assignmentId,
        ]);

        $request->merge(['schoolingname_id' => $newEntry->id]);
        session()->flash('success', "New entry '{$newEntryName}' created successfully!");
    }

    /* ─── Resolve or create SchoolingUnit ─── */
    private function resolveOrCreateSchoolingUnit(Request $request): void
    {
        if ($request->input('schooling_unit_id') !== 'new') return;

        $request->validate([
            'new_unit_name'     => ['required', 'string', 'max:50'],
            'new_unit_location' => ['required', 'in:local,foreign'],
        ], [
            'new_unit_name.required'     => 'Unit name is required when creating a new unit',
            'new_unit_location.required' => 'Please select Local or Foreign for the new unit',
        ]);

        $name     = trim((string) $request->input('new_unit_name'));
        $location = $request->input('new_unit_location');

        $existing = SchoolingUnit::where('name', $name)
            ->where('location', $location)
            ->first();

        if ($existing) {
            $request->merge(['schooling_unit_id' => $existing->id]);
            return;
        }

        $unit = SchoolingUnit::create(['name' => $name, 'location' => $location]);
        $request->merge(['schooling_unit_id' => $unit->id]);
        session()->flash('success', "New unit '{$name}' ({$location}) created successfully!");
    }

    public function store(Request $request)
    {
        $action  = $request->input('action');
        $pm_code = $request->input('pm_code');
        $officer = Officer::where('PM_CODE', $pm_code)->first();

        /* ── Fetch Data ── */
        if ($action == 'Fetch Data') {
            $relatedSchoolings = Schooling::where('pm_code', $pm_code)
                ->with(['schoolingnames', 'schoolingunits', 'assignments'])
                ->orderBy('date_completed', 'desc')
                ->get();

            session(['relatedSchoolings' => $relatedSchoolings]);

            return back()->withInput($request->all())
                ->with([
                    'rank'        => $officer->RANK,
                    'name'        => $officer->NAME,
                    'afpsn'       => $officer->AFPSN,
                    'afpos'       => $officer->AFPOS,
                    'sex'         => $officer->SEX,
                    'dob'         => $officer->DOB,
                    'date_ret'    => $officer->RET,
                    'soc'         => $officer->SOC,
                    'type'        => $officer->TYPE,
                    'otd'         => $officer->OTD,
                    'dor'         => $officer->DOR,
                    'sig'         => $officer->SIG,
                    'designation' => $officer->designations->name ?? '',
                    'unit'        => $officer->units->name ?? '',
                    'pm_code'     => $pm_code,
                ]);

        /* ── Save ── */
        } elseif ($action == 'Save') {

            $this->resolveOrCreateSchoolingName($request);
            $this->resolveOrCreateSchoolingUnit($request);   // ← NEW

            $data = $request->validate([
                'pm_code'                => ['required', 'string'],
                'schoolingname_id'       => ['required', 'integer'],
                'classname'              => ['nullable', 'string'],
                'schooling_unit_id'      => ['nullable', 'integer'],
                'assignment_id'          => ['required', 'integer'],
                'date_completed'         => ['nullable', 'date'],
                'rating'                 => ['nullable', 'string'],
                'standing'               => ['nullable', 'string'],
                'total_student'          => ['nullable', 'string'],
                'rank_during_completion' => ['nullable', 'string'],
                'seclt'   => ['nullable', 'string'],
                'firstlt' => ['nullable', 'string'],
                'cpt'     => ['nullable', 'string'],
                'maj'     => ['nullable', 'string'],
                'ltc'     => ['nullable', 'string'],
                'col'     => ['nullable', 'string'],
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
            foreach ([1=>'seclt',2=>'firstlt',3=>'cpt',4=>'maj',5=>'ltc',6=>'col'] as $rankId => $col) {
                $data[$col] = $svc->computeForRank($svcInput, $rankId);
            }

            $match = [
                'pm_code'                => $data['pm_code'],
                'schoolingname_id'       => $data['schoolingname_id'] ?? null,
                'classname'              => $data['classname'] ?? null,
                'assignment_id'          => $data['assignment_id'] ?? null,
                'date_completed'         => $data['date_completed'] ?? null,
                'rank_during_completion' => $data['rank_during_completion'] ?? null,
            ];

            $existing = Schooling::query()->where($match)->first();
            $existing ? $existing->update($data) : Schooling::create($data);

            return redirect()->route($this->config_data->module_route . '.index');
        }
    }

    public function show(Schooling $schooling)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $schooling->load(['schoolingnames', 'schoolingunits', 'assignments']);

        $officerData = $schooling->pm_code
            ? Officer::where('PM_CODE', $schooling->pm_code)->first()
            : null;

        $relatedSchoolings = Schooling::where('pm_code', $schooling->pm_code)
            ->where('id', '!=', $schooling->id)
            ->with(['schoolingnames', 'schoolingunits', 'assignments'])
            ->orderBy('date_completed', 'desc')
            ->get();

        $data_items = [
            'data'              => $schooling,
            'column_hidden'     => array_merge($schooling->getDates(), ['id']),
            'column_labels'     => $this->config_data->columnLabels,
            'operation_type'    => 'show',
            'schooling'         => $schooling,
            'officerData'       => $officerData,
            'relatedSchoolings' => $relatedSchoolings,
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    public function edit(Schooling $schooling)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $schooling->load(['schoolingnames', 'schoolingunits', 'assignments']);

        $officerData = $schooling->pm_code
            ? Officer::where('PM_CODE', $schooling->pm_code)->first()
            : null;

        $relatedSchoolings = Schooling::where('pm_code', $schooling->pm_code)
            ->where('id', '!=', $schooling->id)
            ->with(['schoolingnames', 'schoolingunits', 'assignments'])
            ->orderBy('date_completed', 'desc')
            ->get();

        $data_items = [
            "data"              => $schooling,
            "column_hidden"     => $this->config_data->columnHidden,
            "column_labels"     => $this->config_data->columnLabels,
            "operation_type"    => "edit",
            "optional_fields"   => $this->config_data->optionalFields,
            "pm_codes"          => $this->getPmCodes(),
            "schoolingnames"    => SchoolingName::all()->pluck('name', 'id'),
            "schoolingUnits"    => SchoolingUnit::select('id','name','location')->orderBy('name')->get(),
            "assignments"       => Assignment::where('type_id', 5)->orderBy('name')->pluck('name', 'id'),
            "officerData"       => $officerData,
            "relatedSchoolings" => $relatedSchoolings,
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    public function update(Request $request, Schooling $schooling)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $action  = $request->input('action');
        $pm_code = $request->input('pm_code');

        /* ── Fetch Data ── */
        if ($action === 'Fetch Data') {
            $officer = Officer::where('PM_CODE', $pm_code)->first();

            if (! $officer) {
                return back()->withInput($request->all())
                    ->with('error', "No officer found for PM Code: {$pm_code}");
            }

            $relatedSchoolings = Schooling::where('pm_code', $pm_code)
                ->where('id', '!=', $schooling->id)
                ->with(['schoolingnames', 'schoolingunits', 'assignments'])
                ->orderBy('date_completed', 'desc')
                ->get();

            session(['relatedSchoolings' => $relatedSchoolings]);

            return back()->withInput($request->all())
                ->with([
                    'rank'        => $officer->RANK,
                    'name'        => $officer->NAME,
                    'afpsn'       => $officer->AFPSN,
                    'afpos'       => $officer->AFPOS,
                    'sex'         => $officer->SEX,
                    'dob'         => $officer->DOB,
                    'date_ret'    => $officer->RET,
                    'soc'         => $officer->SOC,
                    'type'        => $officer->TYPE,
                    'otd'         => $officer->OTD,
                    'dor'         => $officer->DOR,
                    'sig'         => $officer->SIG,
                    'designation' => $officer->designations->name ?? '',
                    'unit'        => $officer->units->name ?? '',
                    'pm_code'     => $pm_code,
                ]);
        }

        /* ── Update ── */
        $this->resolveOrCreateSchoolingName($request);
        $this->resolveOrCreateSchoolingUnit($request);   // ← NEW

        $data = $request->validate([
            'pm_code'                => ['required', 'string'],
            'schoolingname_id'       => ['nullable', 'integer'],
            'classname'              => ['nullable', 'string'],
            'schooling_unit_id'      => ['nullable', 'integer', 'exists:schooling_units,id'],
            'assignment_id'          => ['nullable', 'integer'],
            'date_completed'         => ['nullable', 'date'],
            'rating'                 => ['nullable', 'numeric'],
            'standing'               => ['nullable', 'numeric'],
            'total_student'          => ['nullable', 'numeric'],
            'rank_during_completion' => ['nullable', 'string'],
            'seclt'   => ['nullable', 'numeric'],
            'firstlt' => ['nullable', 'numeric'],
            'cpt'     => ['nullable', 'numeric'],
            'maj'     => ['nullable', 'numeric'],
            'ltc'     => ['nullable', 'numeric'],
            'col'     => ['nullable', 'numeric'],
        ]);

        $data['rank_during_completion'] = $this->resolveRankDuringCompletion(
            $data['pm_code'] ?? null,
            $data['date_completed'] ?? null
        ) ?? '';

        $svcInput = [
            'assignment_id'   => $data['assignment_id'] ?? $schooling->assignment_id,
            'school_location' => $request->input('school_location'),
            'rating'          => $data['rating'] ?? $schooling->rating,
            'standing'        => $data['standing'] ?? $schooling->standing,
            'total_students'  => $data['total_student'] ?? $schooling->total_student,
        ];

        $svc = app(SchoolingPointsService::class);
        foreach ([1=>'seclt',2=>'firstlt',3=>'cpt',4=>'maj',5=>'ltc',6=>'col'] as $rankId => $col) {
            $data[$col] = $svc->computeForRank($svcInput, $rankId);
        }

        $schooling->update($data);

        return redirect()->route($this->config_data->module_route . '.index');
    }

    public function destroy(Schooling $schooling)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $schooling->delete();
        return back();
    }

    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $dtColumns = [
            null,'pm_code','schoolingnames.name','classname','schoolingunits.name',
            'assignments.name','date_completed','rating','standing','total_student',
            'rank_during_completion','seclt','firstlt','cpt','maj','ltc','col',null,
        ];

        $globalSearchColumns = [
            'pm_code','schoolingnames.name','classname','schoolingunits.name',
            'assignments.name','date_completed','rating','standing','total_student',
            'rank_during_completion','seclt','firstlt','cpt','maj','ltc','col',
        ];

        $start  = (int) $request->input('start', 0);
        $length = max(1, (int) $request->input('length', 10));

        $query  = Schooling::query();

        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search, $globalSearchColumns) {
                foreach ($globalSearchColumns as $col) {
                    if (str_contains($col, '.')) {
                        [$rel, $field] = explode('.', $col, 2);
                        $q->orWhereHas($rel, fn($r) => $r->where($field, 'like', "%{$search}%"));
                    } else {
                        $q->orWhere($col, 'like', "%{$search}%");
                    }
                }
            });
        }

        foreach ($dtColumns as $index => $column) {
            if (!$column) continue;
            $colSearch = trim((string) $request->input("columns.$index.search.value", ''));
            if ($colSearch === '') continue;
            if (str_contains($column, '.')) {
                [$rel, $field] = explode('.', $column, 2);
                $query->whereHas($rel, fn($r) => $r->where($field, 'like', "%{$colSearch}%"));
            } else {
                $query->where($column, 'like', "%{$colSearch}%");
            }
        }

        $totalData    = Schooling::count();
        $filteredData = (clone $query)->count();

        $data = $query->orderBy('id')
            ->skip($start)->take($length)
            ->with(['schoolingnames:id,name','schoolingunits:id,name,location','assignments:id,name'])
            ->get();

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $filteredData,
            'data'            => $data,
        ]);
    }

    /* ─── Private: resolve rank from date_ranks ─── */
    private function resolveRankDuringCompletion(?string $pmCode, ?string $dateCompleted): ?string
    {
        if (blank($pmCode) || blank($dateCompleted)) return null;

        $dr = DateRank::query()
            ->with('ranks:id,code,name')
            ->where('pm_code', trim($pmCode))
            ->whereDate('date', '<=', $dateCompleted)
            ->orderBy('date', 'desc')
            ->first();

        return $dr?->ranks?->code;
    }

    /* ─── AJAX: rank during completion ─── */
    public function rankDuringCompletion(Request $request)
    {
        return response()->json([
            'rank' => $this->resolveRankDuringCompletion(
                $request->string('pm_code')->toString(),
                $request->input('date_completed')
            ) ?? '',
        ]);
    }

    /* ─── AJAX: compute points ─── */
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
        foreach ([1=>'seclt',2=>'firstlt',3=>'cpt',4=>'maj',5=>'ltc',6=>'col'] as $rankId => $field) {
            $out[$field] = $svc->computeForRank($svcInput, $rankId);
        }

        return response()->json($out);
    }
}