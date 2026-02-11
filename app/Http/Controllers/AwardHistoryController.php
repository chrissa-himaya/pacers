<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\AwardHistory;
use App\Models\Officer;
use App\Models\DateRank;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class AwardHistoryController extends Controller
{
    protected $config_data;

    public function __construct(AwardHistory $awardhistory)
    {
        $columnHidden  = array_merge($awardhistory->getDates(), ['id']);
        $columnLabels  = [''];
        $optionalFields = ['name', 'email'];

        $this->config_data = (object) [
            "module_name"        => "Award History",
            "module_perm_name"   => "awardhistory",
            "module_route"       => "awardhistories",
            "module_view_folder" => "officerdata.awardhistory",
            "columnHidden"       => $columnHidden,
            "columnLabels"       => $columnLabels,
            "optionalFields"     => $optionalFields,
        ];

        view()->share('config_data', $this->config_data);
    }

    // ─────────────────────────────────────────────────────────
    //  INDEX
    // ─────────────────────────────────────────────────────────

    public function index()
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view($this->config_data->module_view_folder . '.index');
    }

    // ─────────────────────────────────────────────────────────
    //  CREATE
    // ─────────────────────────────────────────────────────────

    public function create()
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (!old('pm_code')) {
            session()->forget([
                'pm_code', 'name', 'rank', 'afpos', 'afpsn', 'sex',
                'dob', 'date_ret', 'dor', 'soc', 'sig',
                'designation', 'unit', 'relatedAwards',
            ]);
        }

        $awardhistory = new AwardHistory();

        $data_items = [
            "data"            => $awardhistory,
            "column_hidden"   => $this->config_data->columnHidden,
            "column_labels"   => $this->config_data->columnLabels,
            "operation_type"  => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "pm_codes"        => $this->getPmCodes(),
            "awards"          => $this->getAwardCodes(),
            "award_types"     => $this->getAwardTypes(),
            "relatedAwards"   => collect([]),
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    // ─────────────────────────────────────────────────────────
    //  CREATE FROM EXISTING (prefill pm_code)
    // ─────────────────────────────────────────────────────────

    public function createFromExisting(AwardHistory $awardhistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $blank = new AwardHistory();
        $blank->pm_code = $awardhistory->pm_code;

        $officerData = Officer::where('PM_CODE', $awardhistory->pm_code)->first();
        $this->storeOfficerInSession($awardhistory->pm_code, $officerData);

        $relatedAwards = $this->getRelatedAwards($awardhistory->pm_code);
        session(['relatedAwards' => $relatedAwards]);

        $data_items = [
            "data"            => $blank,
            "column_hidden"   => $this->config_data->columnHidden,
            "column_labels"   => $this->config_data->columnLabels,
            "operation_type"  => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "pm_codes"        => $this->getPmCodes(),
            "awards"          => $this->getAwardCodes(),
            "award_types"     => $this->getAwardTypes(),
            "officerData"     => $officerData,
            "relatedAwards"   => $relatedAwards,
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    // ─────────────────────────────────────────────────────────
    //  STORE
    // ─────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $action  = $request->input('action');
        $pm_code = $request->input('pm_code');
        $officer = $pm_code ? Officer::where('PM_CODE', $pm_code)->first() : null;

        // ── Fetch Data ──────────────────────────────────────
        if ($action === 'Fetch Data') {
            $relatedAwards = $this->getRelatedAwards($pm_code);
            $this->storeOfficerInSession($pm_code, $officer);
            session(['relatedAwards' => $relatedAwards]);

            return back()->withInput($request->all())
                ->with([
                    'rank'        => $officer?->RANK,
                    'name'        => $officer?->NAME,
                    'afpsn'       => $officer?->AFPSN,
                    'afpos'       => $officer?->AFPOS,
                    'sex'         => $officer?->SEX,
                    'dob'         => $officer?->DOB,
                    'date_ret'    => $officer?->RET,
                    'soc'         => $officer?->SOC,
                    'dor'         => $officer?->DOR,
                    'sig'         => $officer?->SIG,
                    'designation' => $officer?->designations->name ?? '',
                    'unit'        => $officer?->units->name ?? '',
                    'pm_code'     => $pm_code,
                    'relatedAwards' => $relatedAwards,
                ]);
        }

        // ── Save ────────────────────────────────────────────
        if ($action === 'Save') {
            $awardId    = $this->resolveOrCreateAward($request);
            $newAwardId = session('new_award_id');
            $awardTypeId = $this->resolveOrCreateAwardType($request, $newAwardId);

            $data = $request->validate([
                'pm_code'   => ['required', 'string', 'max:50'],
                'date'      => ['nullable', 'date'],
                'go_number' => ['nullable', 'string', 'max:50'],
                'points'    => ['nullable', 'numeric'],
            ]);

            $data['award_id']   = $awardId;
            $data['award_type'] = $awardTypeId;

            $data['date_rank_id'] = $this->resolveDateRankId(
                $data['pm_code'],
                $data['date'] ?? null
            );

            if (empty($data['points']) && $awardId && $awardId !== 'new') {
                $award = Award::find($awardId);
                $data['points'] = $award?->points;
            }

            $match = [
                'pm_code'   => $data['pm_code'],
                'award_id'  => $data['award_id'] ?? null,
                'award_type'=> $data['award_type'] ?? null,
                'date'      => $data['date'] ?? null,
                'go_number' => $data['go_number'] ?? null,
            ];

            $existing = AwardHistory::where($match)->first();
            if ($existing) {
                $existing->update($data);
            } else {
                AwardHistory::create($data);
            }

            return redirect()->route($this->config_data->module_route . '.index');
        }

        return back()->withInput($request->all());
    }

    // ─────────────────────────────────────────────────────────
    //  SHOW
    // ─────────────────────────────────────────────────────────

    public function show(AwardHistory $awardhistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $awardhistory->load(['awards', 'awardType', 'dateranks.ranks']);
        $officerData = null;
        if ($awardhistory->pm_code) {
            $officerData = Officer::where('PM_CODE', $awardhistory->pm_code)->first();
        }

        $relatedAwards = $this->getRelatedAwards($awardhistory->pm_code);

        $data_items = [
            "data"           => $awardhistory,
            "column_hidden"  => $this->config_data->columnHidden,
            "column_labels"  => $this->config_data->columnLabels,
            "operation_type" => "show",
            "officerData"    => $officerData,
            "relatedAwards"  => $relatedAwards,
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    public function edit(AwardHistory $awardhistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $awardhistory->load(['awards', 'awardType', 'dateranks.ranks']);
        // Check if returning from a Fetch Data action
        $wasFetched    = session('fetched', false);
        $fetchedPmCode = session('pm_code');

        if ($wasFetched && $fetchedPmCode) {
            // ── Post-fetch: use the FETCHED pm_code ──
            // Officer data is already in session from storeOfficerInSession()
            // Query it fresh to pass as officerData to the view
            $officerData   = Officer::where('PM_CODE', $fetchedPmCode)->first();
            $relatedAwards = session('relatedAwards', $this->getRelatedAwards($fetchedPmCode));
        } else {
            // ── Normal edit: use the record's own pm_code ──
            $officerData   = null;
            $relatedAwards = collect([]);

            if ($awardhistory->pm_code) {
                $officerData   = Officer::where('PM_CODE', $awardhistory->pm_code)->first();
                $relatedAwards = $this->getRelatedAwards($awardhistory->pm_code);
            }
        }

        $data_items = [
            "data"            => $awardhistory,
            "column_hidden"   => $this->config_data->columnHidden,
            "column_labels"   => $this->config_data->columnLabels,
            "operation_type"  => "edit",
            "optional_fields" => $this->config_data->optionalFields,
            "pm_codes"        => $this->getPmCodes(),
            "awards"          => $this->getAwardCodes(),
            "award_types"     => $this->getAwardTypes(),
            "officerData"     => $officerData,
            "relatedAwards"   => $relatedAwards,
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    // ─────────────────────────────────────────────────────────
    //  UPDATE — FIX #1: Fetch Data does NOT update the record
    // ─────────────────────────────────────────────────────────

    public function update(Request $request, AwardHistory $awardhistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $action  = $request->input('action');
        $pm_code = $request->input('pm_code');

        // ── Fetch Data — only fetch officer info, do NOT touch the record ──
        if ($action === 'Fetch Data') {
            $officer       = $pm_code ? Officer::where('PM_CODE', $pm_code)->first() : null;
            $relatedAwards = $pm_code ? $this->getRelatedAwards($pm_code) : collect([]);

            // Persist into session so edit() picks it up after redirect
            $this->storeOfficerInSession($pm_code, $officer);
            session(['relatedAwards' => $relatedAwards]);

            return redirect()->route($this->config_data->module_route . '.edit', $awardhistory->id)
                ->withInput($request->all())
                ->with([
                    'fetched'       => true,
                    'rank'          => $officer?->RANK,
                    'name'          => $officer?->NAME,
                    'afpsn'         => $officer?->AFPSN,
                    'afpos'         => $officer?->AFPOS,
                    'sex'           => $officer?->SEX,
                    'dob'           => $officer?->DOB,
                    'date_ret'      => $officer?->RET,
                    'soc'           => $officer?->SOC,
                    'dor'           => $officer?->DOR,
                    'sig'           => $officer?->SIG,
                    'designation'   => $officer?->designations->name ?? '',
                    'unit'          => $officer?->units->name ?? '',
                    'pm_code'       => $pm_code,
                    'relatedAwards' => $relatedAwards,
                ]);
        }

        // ── Update (only when explicitly clicking "Update" button) ──
        if ($action === 'Update') {
            $awardId     = $this->resolveOrCreateAward($request);
            $newAwardId  = session('new_award_id');
            $awardTypeId = $this->resolveOrCreateAwardType($request, $newAwardId);

            $data = $request->validate([
                'pm_code'   => ['required', 'string', 'max:50'],
                'date'      => ['nullable', 'date'],
                'go_number' => ['nullable', 'string', 'max:50'],
                'points'    => ['nullable', 'numeric'],
            ]);

            $data['award_id']   = $awardId;
            $data['award_type'] = $awardTypeId;

            $data['date_rank_id'] = $this->resolveDateRankId(
                $data['pm_code'],
                $data['date'] ?? null
            );

            if (empty($data['points']) && $awardId) {
                $award = Award::find($awardId);
                $data['points'] = $award?->points;
            }

            $awardhistory->update($data);

            return redirect()->route($this->config_data->module_route . '.index');
        }

        return back()->withInput($request->all());
    }

    // ─────────────────────────────────────────────────────────
    //  DESTROY
    // ─────────────────────────────────────────────────────────

    public function destroy(AwardHistory $awardhistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $awardhistory->delete();
        return back();
    }

    // ─────────────────────────────────────────────────────────
    //  LIST (DataTables server-side)
    // ─────────────────────────────────────────────────────────

    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $dtColumns = [
            null,           // 0 Nr
            'pm_code',      // 1
            'awards.code',  // 2
            'award_types.name', // 3
            'date',         // 4
            'go_number',    // 5
            'rank_at_date', // 6 virtual
            'points',       // 7
            null,           // 8 actions
        ];

        $globalSearchColumns = ['pm_code', 'date', 'go_number', 'points'];

        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length <= 0) $length = 10;

        // $query = AwardHistory::query()->with(['awards', 'awardType', 'dateranks.ranks']);
        $query = AwardHistory::query()->with(['awards', 'awardType', 'dateranks.ranks']);

        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('pm_code', 'like', "%{$search}%")
                  ->orWhere('go_number', 'like', "%{$search}%")
                  ->orWhere('date', 'like', "%{$search}%")
                  ->orWhereHas('awards', fn($r) => $r->where('code', 'like', "%{$search}%")
                                                       ->orWhere('name', 'like', "%{$search}%"))
                  ->orWhereHas('awardType', fn($r) => $r->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('dateranks.ranks', fn($r) => $r->where('code', 'like', "%{$search}%"));
            });
        }

        foreach ($dtColumns as $index => $column) {
            if (!$column) continue;
            $colSearch = trim((string) $request->input("columns.$index.search.value", ''));
            if ($colSearch === '') continue;

            match ($column) {
                'awards.code'      => $query->whereHas('awards', fn($r) => $r->where('code', 'like', "%{$colSearch}%")),
                'award_types.name' => $query->whereHas('awardType', fn($r) => $r->where('name', 'like', "%{$colSearch}%")),
                'rank_at_date'     => $query->whereHas('dateranks.ranks', fn($r) => $r->where('code', 'like', "%{$colSearch}%")),
                default            => $query->where($column, 'like', "%{$colSearch}%"),
            };
        }

        $totalData    = AwardHistory::count();
        $filteredData = (clone $query)->count();

        $data = $query->orderBy('id', 'asc')
                      ->skip($start)
                      ->take($length)
                      ->get();

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $filteredData,
            'data'            => $data,
        ]);
    }


    public function dateRankAtDate(Request $request)
    {
        $pmCode = trim((string) $request->input('pm_code', ''));
        $date   = $request->input('date');

        if ($pmCode === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date)) {
            return response()->json(['date_rank_id' => '', 'rank' => '']);
        }

        $dr = DateRank::query()
            ->with('ranks:id,code,name') 
            ->where('pm_code', $pmCode)
            ->where('date', '<=', $date)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->first();

        return response()->json([
            'date_rank_id' => $dr?->id ?? '',
            'rank'         => $dr?->ranks?->code ?? '',  // ✅ Access ranks relationship
        ]);
    }

    public function pointsForAward(Request $request)
    {
        $awardId = $request->input('award_id');

        if (!$awardId) {
            return response()->json(['points' => '']);
        }

        $award = Award::find($awardId);

        return response()->json([
            'points' => $award?->points ?? '',
        ]);
    }

    // ─────────────────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────

    private function resolveDateRankId(?string $pmCode, ?string $awardDate): ?int
    {
    if (blank($pmCode) || blank($awardDate)) return null;


    $dr = DateRank::query()
    ->where('pm_code', trim($pmCode))
    ->where('date', '<=', $awardDate) // (awardDate is already Y-m-d)
    ->orderByDesc('date')
    ->orderByDesc('id')
    ->first();


    return $dr?->id;
    }

    private function resolveOrCreateAward(Request $request): ?int
    {
        if ($request->input('award_id') !== 'new') {
            return $request->input('award_id') ? (int) $request->input('award_id') : null;
        }

        $request->validate([
            'new_award_code'   => ['required', 'string', 'max:50'],
            'new_award_name'   => ['required', 'string', 'max:255'],
            'new_award_points' => ['nullable', 'numeric'],
        ], [
            'new_award_code.required'  => 'Award code is required when creating a new award.',
            'new_award_name.required'  => 'Award name is required when creating a new award.',
        ]);

        $existing = Award::where('code', trim($request->input('new_award_code')))->first();
        if ($existing) {
            session()->flash('new_award_id', $existing->id);
            return $existing->id;
        }

        $award = Award::create([
            'code'   => trim($request->input('new_award_code')),
            'name'   => trim($request->input('new_award_name')),
            'points' => $request->input('new_award_points') ?? 0,
        ]);

        session()->flash('success', "New award '{$award->code}' created successfully!");
        session()->flash('new_award_id', $award->id);

        return $award->id;
    }

    private function resolveOrCreateAwardType(Request $request, ?int $newAwardId = null): ?int
    {
        if ($newAwardId) {
            return $newAwardId;
        }

        if ($request->input('award_type') !== 'new') {
            return $request->input('award_type') ? (int) $request->input('award_type') : null;
        }

        $request->validate([
            'new_award_type_code'   => ['required', 'string', 'max:50'],
            'new_award_type_name'   => ['required', 'string', 'max:255'],
            'new_award_type_points' => ['nullable', 'numeric'],
        ], [
            'new_award_type_code.required' => 'Award type code is required.',
            'new_award_type_name.required' => 'Award type name is required.',
        ]);

        $existing = Award::where('code', trim($request->input('new_award_type_code')))->first();
        if ($existing) {
            return $existing->id;
        }

        $awardType = Award::create([
            'code'   => trim($request->input('new_award_type_code')),
            'name'   => trim($request->input('new_award_type_name')),
            'points' => $request->input('new_award_type_points') ?? 0,
        ]);

        session()->flash('success', "New award type '{$awardType->name}' created successfully!");
        return $awardType->id;
    }

    private function storeOfficerInSession(string $pmCode, ?Officer $officer): void
    {
        session([
            'pm_code'     => $pmCode,
            'name'        => $officer?->NAME ?? '',
            'rank'        => $officer?->RANK ?? '',
            'afpos'       => $officer?->AFPOS ?? '',
            'afpsn'       => $officer?->AFPSN ?? '',
            'sex'         => $officer?->SEX ?? '',
            'dob'         => $officer?->DOB ?? '',
            'date_ret'    => $officer?->RET ?? '',
            'dor'         => $officer?->DOR ?? '',
            'soc'         => $officer?->SOC ?? '',
            'sig'         => $officer?->SIG ?? '',
            'designation' => $officer?->designations?->name ?? '',
            'unit'        => $officer?->units?->name ?? '',
        ]);
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

    private function getAwardCodes(): array
    {
        return Award::orderBy('code')->pluck('code', 'id')->toArray();
    }

    private function getAwardTypes(): array
    {
        return Award::orderBy('name')->pluck('name', 'id')->toArray();
    }

    private function getRelatedAwards(string $pmCode)
    {
        return AwardHistory::where('pm_code', $pmCode)
            ->with(['awards', 'awardType', 'dateranks.ranks'])
            ->orderBy('date', 'desc')
            ->get();
    }
}