<?php

namespace App\Http\Controllers;

use App\Models\Sourcedata;
use App\Models\Assignment;
use App\Models\Type;
use App\Models\Rank;
use App\Models\Rankpoint;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;


class SourcedatasController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $config_data;
    public function __construct(Sourcedata $sourcedata)
    {
        $columnHidden = array_merge($sourcedata->getDates(), ['id']);
        $columnLabels = [''];    
        $optionalFields = [''];

        $this->config_data = (object) [
            "module_name"=>"Sourcedatas", //Module name
            "module_perm_name"=>"sourcedata", //Permission name
            "module_route"=>"sourcedatas", //Web route
            "module_view_folder"=>"references.sourcedata", //View folder
            "columnHidden"=>$columnHidden,
            "columnLabels"=>$columnLabels,
            "optionalFields"=>$optionalFields,
        ];

        view()->share('config_data', $this->config_data);
    }
    public function index()
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view($this->config_data->module_view_folder.'.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sourcedata = new Sourcedata();
        $assignments = Assignment::with('types')->get()->keyBy('id');
        $rankpointsGrouped = Rankpoint::with('ranks')->get()->groupBy('name');

        $data_items = [
            "data" => $sourcedata,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "assignments" => $assignments,
            "rankpoints_grouped" => $rankpointsGrouped,
        ];

        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'assignment_id' => ['required','exists:assignments,id'],
            'min_month_rankpoint_id' => ['required','exists:rankpoints,id'],
            'min_point_rankpoint_id' => ['required','exists:rankpoints,id'],
            'max_month_rankpoint_id' => ['required','exists:rankpoints,id'],
            'max_point_rankpoint_id' => ['required','exists:rankpoints,id'],
            ]);

            $this->assertSameRank($validated);
            Sourcedata::create($validated);
        return redirect()->route($this->config_data->module_route . '.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sourcedata $sourcedata)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $sourcedata->load([
            'assignments.types',
            'minMonthRankpoint.ranks',
            'minPointRankpoint.ranks',
            'maxMonthRankpoint.ranks',
            'maxPointRankpoint.ranks',
        ]);


        $assignments = Assignment::with('types')->get()->keyBy('id');
        $rankpointsGrouped = Rankpoint::with('ranks')->get()->groupBy('name');


        $columnHidden = array_merge($sourcedata->getDates(), ['id','assignment_id','type_id']);


        $data_items = [
        "data" => $sourcedata,
        "column_hidden" => $columnHidden,
        "column_labels" => $this->config_data->columnLabels,
        "operation_type" => "show",
        "assignments" => $assignments,
        "rankpoints_grouped" => $rankpointsGrouped,
        ];


        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sourcedata $sourcedata)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $columnHidden = array_merge($sourcedata->getDates(), ['id']);  
        $assignments = Assignment::with('types')->get()->keyBy('id');
        $rankpointsGrouped = Rankpoint::with('ranks')->get()->groupBy('name');


        $data_items = [
            "data" => $sourcedata,
            "column_hidden" => $columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "edit",
            "optional_fields" => $this->config_data->optionalFields,
            "assignments" => $assignments,
            "rankpoints_grouped" => $rankpointsGrouped,
        ];

        // return $data_items["data"];
        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sourcedata $sourcedata)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $validated = $request->validate([
            'assignment_id' => ['required','exists:assignments,id'],
            'min_month_rankpoint_id' => ['required','exists:rankpoints,id'],
            'min_point_rankpoint_id' => ['required','exists:rankpoints,id'],
            'max_month_rankpoint_id' => ['required','exists:rankpoints,id'],
            'max_point_rankpoint_id' => ['required','exists:rankpoints,id'],
            ]);

            $this->assertSameRank($validated);
            $sourcedata->update($validated);
        return redirect()->route($this->config_data->module_route . '.index', $sourcedata->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sourcedata $sourcedata)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $sourcedata->delete();
        return back();
    }

    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // All columns in the table
        $columns = ['id', 'assignment_id', 'min_month_rankpoint_id','min_point_rankpoint_id','max_month_rankpoint_id','max_point_rankpoint_id', 'created_at', 'updated_at'];

        // Pagination values from DataTables
        $start  = $request->input('start', 0);
        $length = $request->input('length', 10);

        // Prevent invalid length (MariaDB requires LIMIT)
        if ($length <= 0) {
            $length = 10;
        }

        // Ordering
        $orderIndex = $request->input('order.0.column', 0);
        $orderDir   = $request->input('order.0.dir', 'asc');

        // Validate order direction
        if (!in_array($orderDir, ['asc', 'desc'])) {
            $orderDir = 'asc';
        }

        $orderColumn = $columns[$orderIndex] ?? 'id';

        // Base query
        $query = Sourcedata::query();

        // Search filter
        $search = $request->input('search.value');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('assignments', fn($aq) => $aq->where('name','like',"%{$search}%"))
                    ->orWhereHas('minMonthRankpoint.ranks', fn($rq) => $rq
                    ->where('code','like',"%{$search}%"));
            });
        }

        // Total records
        $totalData = Sourcedata::count();
        $filteredData = $query->count();

        // Apply ordering and pagination
        $data = $query->orderBy($orderColumn, $orderDir)
                      ->skip($start)
                      ->take($length)
                      ->with([
                            'assignments:id,name,type_id',
                            'assignments.types:id,name',

                            'minMonthRankpoint:id,rank_id,points',
                            'minMonthRankpoint.ranks:id,code',

                            'minPointRankpoint:id,rank_id,points',
                            'minPointRankpoint.ranks:id,code',

                            'maxMonthRankpoint:id,rank_id,points',
                            'maxMonthRankpoint.ranks:id,code',

                            'maxPointRankpoint:id,rank_id,points',
                            'maxPointRankpoint.ranks:id,code',
                        ])
                    ->get();

       

        // Return JSON in DataTables format
        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $filteredData,
            'data'            => $data,
        ]);     
    }

    private function assertSameRank(array $validated): void
    {
        $ids = [
        $validated['min_month_rankpoint_id'],
        $validated['min_point_rankpoint_id'],
        $validated['max_month_rankpoint_id'],
        $validated['max_point_rankpoint_id'],
        ];


        $rankIds = Rankpoint::whereIn('id', $ids)->pluck('rank_id')->unique();


        if ($rankIds->count() !== 1) {
        abort(422, 'Selected rankpoints must belong to the same rank.');
        }
    }
}
