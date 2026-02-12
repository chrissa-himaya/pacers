<?php

namespace App\Http\Controllers;

use App\Models\CareerAdvising;
use App\Models\Officer;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class CareerAdvisingController extends Controller
{
    protected $config_data;

    public function __construct(CareerAdvising $careeradvising)
    {
        $columnHidden   = array_merge($careeradvising->getDates(), ['id']);
        $columnLabels   = [''];
        $optionalFields = ['name', 'email'];

        $this->config_data = (object) [
            "module_name"        => "Career Advising Records",
            "module_perm_name"   => "careeradvising",
            "module_route"       => "careeradvising",
            "module_view_folder" => "officerdata.careeradvising",
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

        // same behavior as AwardHistory: reset session officer info when landing fresh on create
        if (!old('pm_code')) {
            session()->forget([
                'pm_code', 'name', 'rank', 'afpos', 'afpsn', 'sex',
                'dob', 'date_ret', 'dor', 'soc', 'sig',
                'designation', 'unit', 'relatedRecords',
            ]);
        }

        $careeradvising = new CareerAdvising();

        $data_items = [
            "data"            => $careeradvising,
            "column_hidden"   => $this->config_data->columnHidden,
            "column_labels"   => $this->config_data->columnLabels,
            "operation_type"  => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "pm_codes"        => $this->getPmCodes(),
            "relatedRecords"  => collect([]),
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    // ─────────────────────────────────────────────────────────
    //  CREATE FROM EXISTING (prefill pm_code)
    // ─────────────────────────────────────────────────────────

    public function createFromExisting(CareerAdvising $careeradvising)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $blank = new CareerAdvising();
        $blank->pm_code = $careeradvising->pm_code;

        $officerData = Officer::where('PM_CODE', $careeradvising->pm_code)->first();
        $this->storeOfficerInSession($careeradvising->pm_code, $officerData);

        $relatedRecords = $this->getRelatedRecords($careeradvising->pm_code);
        session(['relatedRecords' => $relatedRecords]);

        $data_items = [
            "data"            => $blank,
            "column_hidden"   => $this->config_data->columnHidden,
            "column_labels"   => $this->config_data->columnLabels,
            "operation_type"  => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "pm_codes"        => $this->getPmCodes(),
            "officerData"     => $officerData,
            "relatedRecords"  => $relatedRecords,
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

        // ── Fetch Data (same flow as AwardHistory) ──
        if ($action === 'Fetch Data') {
            $relatedRecords = $pm_code ? $this->getRelatedRecords($pm_code) : collect([]);

            $this->storeOfficerInSession($pm_code, $officer);
            session(['relatedRecords' => $relatedRecords]);

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
                    'relatedRecords' => $relatedRecords,
                ]);
        }

        // ── Save (only when explicitly clicking Save) ──
        if ($action === 'Save') {
            $validated = $request->validate([
                'pm_code'        => ['string', 'max:50'],
                'date_of_advise' => ['date'],
                'career_adviser' => ['string', 'max:255'],
                'mode_of_coms'   => ['string', 'max:255'],
                'venue'          => ['nullable', 'string', 'max:255'],
                'remarks'        => ['nullable', 'string'],
            ]);

            // Prevent duplicate record insertion (AwardHistory-style "upsert")
            $match = [
                'pm_code'        => $validated['pm_code'],
                'date_of_advise' => $validated['date_of_advise'],
                'career_adviser' => $validated['career_adviser'],
                'mode_of_coms'   => $validated['mode_of_coms'],
                'venue'          => $validated['venue'] ?? null,
            ];

            $existing = CareerAdvising::where($match)->first();
            if ($existing) {
                $existing->update($validated);
            } else {
                CareerAdvising::create($validated);
            }

            return redirect()->route($this->config_data->module_route . '.index');
        }

        return back()->withInput($request->all());
    }

    // ─────────────────────────────────────────────────────────
    //  SHOW
    // ─────────────────────────────────────────────────────────

    public function show(CareerAdvising $careeradvising)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $officerData = null;
        if ($careeradvising->pm_code) {
            $officerData = Officer::where('PM_CODE', $careeradvising->pm_code)->first();
        }

        $relatedRecords = $careeradvising->pm_code
            ? $this->getRelatedRecords($careeradvising->pm_code, $careeradvising->id)
            : collect([]);

        $data_items = [
            "data"           => $careeradvising,
            "column_hidden"  => $this->config_data->columnHidden,
            "column_labels"  => $this->config_data->columnLabels,
            "operation_type" => "show",
            "officerData"    => $officerData,
            "relatedRecords" => $relatedRecords,
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    // ─────────────────────────────────────────────────────────
    //  EDIT
    // ─────────────────────────────────────────────────────────

    public function edit(CareerAdvising $careeradvising)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // AwardHistory behavior: after Fetch Data we use fetched pm_code from session
        $wasFetched    = session('fetched', false);
        $fetchedPmCode = session('pm_code');

        if ($wasFetched && $fetchedPmCode) {
            $officerData   = Officer::where('PM_CODE', $fetchedPmCode)->first();
            $relatedRecords = session('relatedRecords', $this->getRelatedRecords($fetchedPmCode));
        } else {
            $officerData   = null;
            $relatedRecords = collect([]);

            if ($careeradvising->pm_code) {
                $officerData   = Officer::where('PM_CODE', $careeradvising->pm_code)->first();
                $relatedRecords = $this->getRelatedRecords($careeradvising->pm_code, $careeradvising->id);
            }
        }

        $data_items = [
            "data"            => $careeradvising,
            "column_hidden"   => $this->config_data->columnHidden,
            "column_labels"   => $this->config_data->columnLabels,
            "operation_type"  => "edit",
            "optional_fields" => $this->config_data->optionalFields,
            "pm_codes"        => $this->getPmCodes(),
            "officerData"     => $officerData,
            "relatedRecords"  => $relatedRecords,
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    // ─────────────────────────────────────────────────────────
    //  UPDATE — FIX: Fetch Data does NOT update the record
    // ─────────────────────────────────────────────────────────

    public function update(Request $request, CareerAdvising $careeradvising)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $action  = $request->input('action');
        $pm_code = $request->input('pm_code');

        // ── Fetch Data — only fetch officer info, do NOT touch the record ──
        if ($action === 'Fetch Data') {
            $officer       = $pm_code ? Officer::where('PM_CODE', $pm_code)->first() : null;
            $relatedRecords = $pm_code ? $this->getRelatedRecords($pm_code, $careeradvising->id) : collect([]);

            $this->storeOfficerInSession($pm_code, $officer);
            session(['relatedRecords' => $relatedRecords]);

            return redirect()->route($this->config_data->module_route . '.edit', $careeradvising->id)
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
                    'relatedRecords' => $relatedRecords,
                ]);
        }

        // ── Update (only when explicitly clicking Update button) ──
        if ($action === 'Update') {
            $validated = $request->validate([
                'pm_code'        => ['string', 'max:50'],
                'date_of_advise' => ['date'],
                'career_adviser' => ['string', 'max:255'],
                'mode_of_coms'   => ['string', 'max:255'],
                'venue'          => ['nullable', 'string', 'max:255'],
                'remarks'        => ['nullable', 'string'],
            ]);

            $careeradvising->update($validated);

            return redirect()->route($this->config_data->module_route . '.index');
        }

        return back()->withInput($request->all());
    }

    // ─────────────────────────────────────────────────────────
    //  DESTROY
    // ─────────────────────────────────────────────────────────

    public function destroy(CareerAdvising $careeradvising)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $careeradvising->delete();
        return back();
    }

    // ─────────────────────────────────────────────────────────
    //  LIST (DataTables server-side) — aligned with AwardHistory style
    // ─────────────────────────────────────────────────────────

    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // DataTables columns mapping (must match your index blade)
        $dtColumns = [
            null,             // 0 Nr
            'pm_code',        // 1
            'date_of_advise', // 2
            'career_adviser', // 3
            'mode_of_coms',   // 4
            'venue',          // 5
            'remarks',        // 6
            null,             // 7 actions
        ];

        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length <= 0) $length = 10;

        $query = CareerAdvising::query();

        // Global search
        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('pm_code', 'like', "%{$search}%")
                  ->orWhere('date_of_advise', 'like', "%{$search}%")
                  ->orWhere('career_adviser', 'like', "%{$search}%")
                  ->orWhere('mode_of_coms', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        // Column-specific search (filter row)
        foreach ($dtColumns as $index => $column) {
            if (!$column) continue;
            $colSearch = trim((string) $request->input("columns.$index.search.value", ''));
            if ($colSearch === '') continue;

            $query->where($column, 'like', "%{$colSearch}%");
        }

        $totalData    = CareerAdvising::count();
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

    // ─────────────────────────────────────────────────────────
    //  PRIVATE HELPERS (copied behavior pattern from AwardHistory)
    // ─────────────────────────────────────────────────────────

    private function storeOfficerInSession(?string $pmCode, ?Officer $officer): void
    {
        session([
            'pm_code'     => (string) $pmCode,
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
            ->select('PM_CODE')
            ->whereNotNull('PM_CODE')
            ->where('PM_CODE', '!=', '')
            ->distinct()
            ->orderBy('PM_CODE')
            ->pluck('PM_CODE'); // returns a collection of PM_CODE strings
    }

    private function getRelatedRecords(string $pmCode, ?int $excludeId = null)
    {
        $q = CareerAdvising::query()
            ->where('pm_code', $pmCode)
            ->orderBy('date_of_advise', 'desc');

        if ($excludeId) {
            $q->where('id', '!=', $excludeId);
        }

        return $q->get();
    }
}
