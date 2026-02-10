<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\AwardHistory;
use App\Models\Officer;
use App\Models\DateRank;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class AwardHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $config_data;
    public function __construct(AwardHistory $awardhistory)
    {
        $columnHidden = array_merge($awardhistory->getDates(), ['id']);
        $columnLabels = [''];
        $optionalFields = ['name', 'email'];

        $this->config_data = (object) [
            "module_name" => "Award History", //Module name
            "module_perm_name" => "awardhistory", //Permission name
            "module_route" => "awardhistories", //Web route
            "module_view_folder" => "officerdata.awardhistory", //View folder
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

        // mimic Schoolings: use record #1 as template if exists
        $awardhistory = AwardHistory::find(1) ?? new AwardHistory();
        $awardhistory->fill([
            'pm_code' => null,
            'award_id' => null,
            'award_type' => null,
            'date' => null,
            'go_number' => null,
            'date_rank_id' => null,
            'points' => null,
        ]);

        $pmcodes = Officer::query()
            ->select('pm_code')
            ->whereNotNull('pm_code')
            ->where('pm_code', '!=', '')
            ->distinct()
            ->orderBy('pm_code')
            ->pluck('pm_code');

        $awards = Award::query()
            ->distinct()
            ->pluck('code', 'id');

        $award_type = Award::all()->pluck('name', 'id');

        $data_items = [
            "data" => $awardhistory,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "pm_codes" => $pmcodes,
            "awards" => $awards,
            "award_type" => $award_type,
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
                    'type' => $officer?->TYPE,
                    'otd' => $officer?->OTD,
                    'dor' => $officer?->DOR,
                    'sig' => $officer?->SIG,
                    // 'designation' => $officer?->designations->name,
                    // 'unit' => $officer?->units->name,
                ]);
        }

        if ($action === 'Save') {

            $data = $request->validate([
                'pm_code' => ['required', 'string', 'max:50'],
                'award_code' => ['nullable', 'integer'],
                'award_type' => ['nullable', 'integer'],
                'date' => ['nullable', 'date'],
                'go_number' => ['nullable', 'string', 'max:50'],
                'points' => ['nullable', 'numeric'],
            ]);

            // resolve date_rank_id by pm_code + date (like resolveRankDuringCompletion)
            $data['date_rank_id'] = $this->resolveDateRankId(
                $data['pm_code'] ?? null,
                $data['date'] ?? null
            );

            // upsert (match key) like Schoolings
            $match = [
                'pm_code' => $data['pm_code'],
                'award_code' => $data['award_code'] ?? null,
                'award_type' => $data['award_type'] ?? null,
                'date' => $data['date'] ?? null,
                'go_number' => $data['go_number'] ?? null,
            ];

            $existing = AwardHistory::query()->where($match)->first();

            if ($existing) {
                $existing->update($data);
            } else {
                AwardHistory::create($data);
            }

            return redirect()->route($this->config_data->module_route . '.index');
        }

        return back()->withInput($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(AwardHistory $awardhistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $awardhistory->load(['dateRanks.ranks']);

        $officerData = null;
        if ($awardhistory->pm_code) {
            $officerData = Officer::where('PM_CODE', $awardhistory->pm_code)->first();
        }

        $data_items = [
            "data" => $awardhistory,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "show",
            "officerData" => $officerData,
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    public function edit(AwardHistory $awardhistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $awardhistory->load(['dateRanks.ranks']);

        $pmcodes = Officer::query()
            ->select('pm_code')
            ->whereNotNull('pm_code')
            ->where('pm_code', '!=', '')
            ->distinct()
            ->orderBy('pm_code')
            ->pluck('pm_code');

        $officerData = null;
        if ($awardhistory->pm_code) {
            $officerData = Officer::where('PM_CODE', $awardhistory->pm_code)->first();
        }

        $data_items = [
            "data" => $awardhistory,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "edit",
            "optional_fields" => $this->config_data->optionalFields,
            "pm_codes" => $pmcodes,
            "officerData" => $officerData,
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    public function update(Request $request, AwardHistory $awardhistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'pm_code' => ['required', 'string', 'max:50'],
            'award_code' => ['nullable', 'integer'],
            'award_type' => ['nullable', 'integer'],
            'date' => ['nullable', 'date'],
            'go_number' => ['nullable', 'string', 'max:50'],
            'points' => ['nullable', 'numeric'],
        ]);

        $data['date_rank_id'] = $this->resolveDateRankId(
            $data['pm_code'] ?? null,
            $data['date'] ?? null
        );

        $awardhistory->update($data);

        return redirect()->route($this->config_data->module_route . '.index');
    }

    public function destroy(AwardHistory $awardhistory)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $awardhistory->delete();
        return back();
    }

    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // mimic schooling list style: dt columns map
        $dtColumns = [
            null,
            'pm_code',
            'award_code',
            'award_type',
            'date',
            'go_number',
            'rank_at_date', // virtual (handled via whereHas)
            'points',
            null,
        ];

        $globalSearchColumns = [
            'pm_code',
            'award_code',
            'award_type',
            'date',
            'go_number',
            'points',
            'rank_at_date',
        ];

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length <= 0)
            $length = 10;

        $query = AwardHistory::query()->with(['dateRanks.ranks']);

        // global search
        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search, $globalSearchColumns) {
                foreach ($globalSearchColumns as $col) {
                    if ($col === 'rank_at_date') {
                        $q->orWhereHas('dateRanks.ranks', function ($r) use ($search) {
                            $r->where('code', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%");
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

            if ($column === 'rank_at_date') {
                $query->whereHas('dateRanks.ranks', function ($r) use ($colSearch) {
                    $r->where('code', 'like', "%{$colSearch}%")
                        ->orWhere('name', 'like', "%{$colSearch}%");
                });
                continue;
            }

            $query->where($column, 'like', "%{$colSearch}%");
        }

        $totalData = AwardHistory::count();
        $filteredData = (clone $query)->count();

        $query->orderBy('id', 'asc');

        $data = $query->skip($start)
            ->take($length)
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $filteredData,
            'data' => $data,
        ]);
    }

    private function resolveDateRankId(?string $pmCode, ?string $awardDate): ?int
    {
        if (blank($pmCode) || blank($awardDate))
            return null;

        $dr = DateRank::query()
            ->where('pm_code', trim($pmCode))
            ->whereDate('date', '<=', $awardDate)
            ->orderBy('date', 'desc')
            ->first();

        return $dr?->id;
    }

    // Optional: if you want ajax rank fill like schooling
    public function rankAtDate(Request $request)
    {
        $pmCode = $request->string('pm_code')->toString();
        $date = $request->input('date');

        $dr = DateRank::query()
            ->with('ranks:id,code,name')
            ->where('pm_code', trim($pmCode))
            ->whereDate('date', '<=', $date)
            ->orderBy('date', 'desc')
            ->first();

        return response()->json([
            'rank' => $dr?->ranks?->code ?? ''
        ]);
    }

    public function dateRankAtDate(Request $request)
    {
        $pmCode = trim((string) $request->input('pm_code', ''));
        $date = $request->input('date');


        if ($pmCode === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date)) {
            return response()->json([
                'date_rank_id' => '',
                'rank' => '',
            ]);
        }


        // IMPORTANT: adjust relationship name to whatever you actually use in DateRank model.
        // In your Schoolings code: DateRank has relationship "ranks".
        $dr = DateRank::query()
            ->with('ranks:id,code,name')
            ->where('pm_code', $pmCode)
            ->whereDate('date', '<=', $date)
            ->orderBy('date', 'desc')
            ->first();


        return response()->json([
            'date_rank_id' => $dr?->id ?? '',
            'rank' => $dr?->ranks?->code ?? '',
        ]);
    }
}
