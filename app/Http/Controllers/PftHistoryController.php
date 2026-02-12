<?php

namespace App\Http\Controllers;

use App\Models\PftHistory;
use App\Models\Officer;
use App\Models\Rank;
use App\Models\Pft;
use Illuminate\Http\Request;
use App\Models\DateRank;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class PftHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $config_data;

    public function __construct(PftHistory $pfthistory)
    {
        $columnHidden = array_merge($pfthistory->getDates(), ['id']);
        $columnLabels = [''];
        $optionalFields = ['name', 'email'];

        $this->config_data = (object) [
            "module_name" => "PFT History",
            "module_perm_name" => "pfthistory",
            "module_route" => "pfthistories",
            "module_view_folder" => "officerdata.pfthistory",
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
                'relatedPftRecords',
            ]);
        }

        $pfthistory = new PftHistory();

        $data_items = [
            "data" => $pfthistory,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "pm_codes" => $this->getPmCodes(),
            "ranks" => $this->getRanks(),
            "relatedPftRecords" => collect([]),
            "rank_lookup_url" => route('date_ranks.lookupRank'),
            "points_lookup_url" => route('pfthistories.calcPoints'),
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    public function createFromExisting(PftHistory $pfthistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $blank = new PftHistory();
        $blank->pm_code = $pfthistory->pm_code;

        $officerData = Officer::where('PM_CODE', $pfthistory->pm_code)->first();
        $this->storeOfficerInSession($pfthistory->pm_code, $officerData);

        $relatedPftRecords = $this->getRelatedPftRecords($pfthistory->pm_code);
        session(['relatedPftRecords' => $relatedPftRecords]);

        $data_items = [
            "data" => $blank,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "pm_codes" => $this->getPmCodes(),
            "ranks" => $this->getRanks(),
            "officerData" => $officerData,
            "relatedPftRecords" => $relatedPftRecords,
            "rank_lookup_url" => route('date_ranks.lookupRank'),
            "points_lookup_url" => route('pfthistories.calcPoints'),
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $action = $request->input('action');
        $pm_code = $request->input('pm_code');
        $officer = $pm_code ? Officer::where('PM_CODE', $pm_code)->first() : null;

        if ($action === 'Fetch Data') {
            $relatedPftRecords = $this->getRelatedPftRecords($pm_code);
            $this->storeOfficerInSession($pm_code, $officer);
            session(['relatedPftRecords' => $relatedPftRecords]);

            return back()->withInput($request->all())
                ->with([
                    'rank' => $officer?->RANK,
                    'name' => $officer?->NAME,
                    'afpsn' => $officer?->AFPSN,
                    'afpos' => $officer?->AFPOS,
                    'sex' => $officer?->SEX,
                    'dob' => $officer?->DOB,
                    'date_ret' => $officer?->RET,
                    'soc' => $officer?->SOC,
                    'dor' => $officer?->DOR,
                    'sig' => $officer?->SIG,
                    'designation' => $officer?->designations->name ?? '',
                    'unit' => $officer?->units->name ?? '',
                    'pm_code' => $pm_code,
                    'relatedPftRecords' => $relatedPftRecords,
                ]);
        }

        if ($action === 'Save') {
            $data = $request->validate([
                'pm_code' => ['required', 'string', 'max:50'],
                'entry' => ['nullable', 'string', 'max:255'],
                'rating' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'date_taken' => ['nullable', 'date'],
                'supervising_unit' => ['nullable', 'string', 'max:255'],
                'rank' => ['nullable', 'string', 'max:50'],
                'age' => ['nullable', 'string', 'max:50'],
                'profile' => ['nullable', 'string', 'max:50'],
            ]);

            $rating = array_key_exists('rating', $data) ? $data['rating'] : null;
            $rank = array_key_exists('rank', $data) ? $data['rank'] : null;
            $data['points'] = $this->computePftPointsValue($rating, $rank);

            $match = [
                'pm_code' => $data['pm_code'],
                'date_taken' => $data['date_taken'] ?? null,
            ];

            $existing = PftHistory::where($match)->first();
            if ($existing) {
                $existing->update($data);
            } else {
                PftHistory::create($data);
            }

            return redirect()->route($this->config_data->module_route . '.index');
        }

        return back()->withInput($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(PftHistory $pfthistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $officerData = null;
        if ($pfthistory->pm_code) {
            $officerData = Officer::where('PM_CODE', $pfthistory->pm_code)->first();
        }

        $relatedPftRecords = $this->getRelatedPftRecords($pfthistory->pm_code);

        $data_items = [
            "data" => $pfthistory,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "show",
            "officerData" => $officerData,
            "relatedPftRecords" => $relatedPftRecords,
            "rank_lookup_url" => route('date_ranks.lookupRank'),
            "points_lookup_url" => route('pfthistories.calcPoints'),
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PftHistory $pfthistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $wasFetched = session('fetched', false);
        $fetchedPmCode = session('pm_code');

        if ($wasFetched && $fetchedPmCode) {
            // Use fetched data from session
            $officerData = Officer::where('PM_CODE', $fetchedPmCode)->first();
            $relatedPftRecords = session('relatedPftRecords', $this->getRelatedPftRecords($fetchedPmCode));
        } else {
            // Use existing record's PM code
            $officerData = null;
            $relatedPftRecords = collect([]);

            if ($pfthistory->pm_code) {
                $officerData = Officer::where('PM_CODE', $pfthistory->pm_code)->first();
                $relatedPftRecords = $this->getRelatedPftRecords($pfthistory->pm_code);
            }
        }

        $data_items = [
            "data" => $pfthistory,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "edit",
            "optional_fields" => $this->config_data->optionalFields,
            "pm_codes" => $this->getPmCodes(),
            "ranks" => $this->getRanks(),
            "officerData" => $officerData,
            "relatedPftRecords" => $relatedPftRecords,
            "rank_lookup_url" => route('date_ranks.lookupRank'),
            "points_lookup_url" => route('pfthistories.calcPoints'),
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PftHistory $pfthistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $action = $request->input('action');
        $pm_code = $request->input('pm_code');

        if ($action === 'Fetch Data') {
            $officer = $pm_code ? Officer::where('PM_CODE', $pm_code)->first() : null;
            $relatedPftRecords = $pm_code ? $this->getRelatedPftRecords($pm_code) : collect([]);

            $this->storeOfficerInSession($pm_code, $officer);
            session(['relatedPftRecords' => $relatedPftRecords, 'fetched' => true]);

            return redirect()->route($this->config_data->module_route . '.edit', $pfthistory->id)
                ->withInput($request->except('_token', '_method', 'action'))
                ->with([
                    'rank' => $officer?->RANK,
                    'name' => $officer?->NAME,
                    'afpsn' => $officer?->AFPSN,
                    'afpos' => $officer?->AFPOS,
                    'sex' => $officer?->SEX,
                    'dob' => $officer?->DOB,
                    'date_ret' => $officer?->RET,
                    'soc' => $officer?->SOC,
                    'dor' => $officer?->DOR,
                    'sig' => $officer?->SIG,
                    'designation' => $officer?->designations->name ?? '',
                    'unit' => $officer?->units->name ?? '',
                    'pm_code' => $pm_code,
                ]);
        }

        if ($action === 'Update') {
            $data = $request->validate([
                'pm_code' => ['required', 'string', 'max:50'],
                'entry' => ['nullable', 'string', 'max:255'],
                'rating' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'date_taken' => ['nullable', 'date'],
                'supervising_unit' => ['nullable', 'string', 'max:255'],
                'rank' => ['nullable', 'string', 'max:50'],
                'age' => ['nullable', 'string', 'max:50'],
                'profile' => ['nullable', 'string', 'max:50'],
            ]);

            $rating = array_key_exists('rating', $data) ? $data['rating'] : null;
            $rank = array_key_exists('rank', $data) ? $data['rank'] : null;
            $data['points'] = $this->computePftPointsValue($rating, $rank);

            $pfthistory->update($data);
            
            // Clear session after successful update
            session()->forget(['fetched', 'pm_code', 'relatedPftRecords']);
            
            return redirect()->route($this->config_data->module_route . '.index');
        }

        return back()->withInput($request->all());
    }

    public function destroy(PftHistory $pfthistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $pfthistory->delete();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $dtColumns = [
            null,               // 0 Nr
            'pm_code',          // 1
            'entry',            // 2
            'rating',           // 3
            'date_taken',       // 4
            'rank',             // 5
            'supervising_unit', // 6
            'points',           // 7
            null,               // 8 actions
        ];

        $globalSearchColumns = ['pm_code', 'entry', 'rating', 'date_taken'];

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length <= 0)
            $length = 10;

        $query = PftHistory::query();

        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('pm_code', 'like', "%{$search}%")
                    ->orWhere('entry', 'like', "%{$search}%")
                    ->orWhere('date_taken', 'like', "%{$search}%")
                    ->orWhere('rating', 'like', "%{$search}%");
            });
        }

        foreach ($dtColumns as $index => $column) {
            if (!$column)
                continue;
            $colSearch = trim((string) $request->input("columns.$index.search.value", ''));
            if ($colSearch === '')
                continue;

            $query->where($column, 'like', "%{$colSearch}%");
        }

        $totalData = PftHistory::count();
        $filteredData = (clone $query)->count();

        $data = $query->orderBy('id', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $filteredData,
            'data' => $data,
        ]);
    }

    private function storeOfficerInSession(string $pmCode, ?Officer $officer): void
    {
        session([
            'pm_code' => $pmCode,
            'name' => $officer?->NAME ?? '',
            'rank' => $officer?->RANK ?? '',
            'afpos' => $officer?->AFPOS ?? '',
            'afpsn' => $officer?->AFPSN ?? '',
            'sex' => $officer?->SEX ?? '',
            'dob' => $officer?->DOB ?? '',
            'date_ret' => $officer?->RET ?? '',
            'dor' => $officer?->DOR ?? '',
            'soc' => $officer?->SOC ?? '',
            'sig' => $officer?->SIG ?? '',
            'designation' => $officer?->designations?->name ?? '',
            'unit' => $officer?->units?->name ?? '',
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

    private function getRanks(): array
    {
        return Rank::orderBy('code')->pluck('code', 'code')->toArray();
    }

    private function getRelatedPftRecords(string $pmCode)
    {
        return PftHistory::where('pm_code', $pmCode)
            ->orderBy('date_taken', 'desc')
            ->get();
    }

        public function calculatePftPoints(Request $request)
    {
        $rating = $request->query('rating');
        $rank   = $request->query('rank');

        $points = $this->computePftPointsValue($rating, $rank);

        return response()->json(['points' => $points]);
    }

    private function computePftPointsValue($rating, $rank): float
    {
        if ($rating === null || $rating === '' || !is_numeric($rating)) return 0.0;
        $rating = (float) $rating;
        if ($rating < 70) return 0.0;

        $rank = trim((string) ($rank ?? ''));
        if ($rank === '') return 0.0;

        $rankRecord = Rank::where('code', $rank)->first();
        if (!$rankRecord) return 0.0;

        $maxPts = Pft::where('ranks_id', $rankRecord->id)->value('points');
        if ($maxPts === null || !is_numeric($maxPts)) return 0.0;

        $maxPts = (float) $maxPts;

        // scale 70..100 -> 0..maxPts
        $scaled = $maxPts * (($rating - 70.0) / 30.0);
        $scaled = max(0.0, min($maxPts, $scaled));

        return round($scaled, 2);
    }

    public function lookupRankByDate(Request $request)
    {
        $pmCode = (string) $request->query('pm_code', '');
        $date   = $request->query('date');

        if ($pmCode === '' || !$date) {
            return response()->json(['rank' => '']);
        }

        $rankCode = DateRank::query()
            ->where('pm_code', $pmCode)
            ->where('date', '<=', $date)
            ->join('ranks', 'ranks.id', '=', 'date_ranks.rank_id')
            ->orderBy('date_ranks.date', 'desc')
            ->limit(1)
            ->value('ranks.code');

        return response()->json(['rank' => $rankCode ?? '']);
    }
    
   
}