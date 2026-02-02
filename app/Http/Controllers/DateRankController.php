<?php

namespace App\Http\Controllers;

use App\Models\DateRank;
use App\Models\Rank;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class DateRankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $config_data;
    public function __construct(DateRank $daterank)
    {
        $columnHidden = array_merge($daterank->getDates(), ['id']);
        $columnLabels = [''];    
        $optionalFields = [''];

        $this->config_data = (object) [
            "module_name"=>"Date of Ranks", //Module name
            "module_perm_name"=>"daterank", //Permission name
            "module_route"=>"dateranks", //Web route
            "module_view_folder"=>"references.daterank", //View folder
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
        $daterank = DateRank::find(1);    
        $daterank->fill([
            'rank_id' => null,
        ]);
        $ranks = Rank::all()->pluck('code', 'id');
        $data_items = [
            "data" => $daterank,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "ranks" => $ranks,
        ];
        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $data = $request->all();
        DateRank::create($data);
        return redirect()->route($this->config_data->module_route . '.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(DateRank $daterank)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $daterank->load('ranks');
        $columnHidden = array_merge($daterank->getDates(), ['id','rank_id']);
        $data_items = [
            "data" => $daterank,
            "column_hidden" => $columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "show",
        ];

        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DateRank $daterank)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $columnHidden = array_merge($daterank->getDates(), ['id']);   
        $ranks = Rank::all()->pluck('code', 'id')   ;
        $data_items = [
            "data" => $daterank,
            "column_hidden" => $columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "edit",
            "optional_fields" => $this->config_data->optionalFields,
            "ranks" => $ranks,
        ];
        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DateRank $daterank)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $data = $request->all();
        $daterank->update($data);
        return redirect()->route($this->config_data->module_route . '.index', $daterank->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DateRank $daterank)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $daterank->delete();
        return back();
    }

    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // All columns in the table
        $columns = ['id', 'pm_code', 'rank_id', 'date', 'created_at', 'updated_at'];

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
        $query = DateRank::query();

        // Search filter
        $search = $request->input('search.value');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Total records
        $totalData = DateRank::count();
        $filteredData = $query->count();

        // Apply ordering and pagination
        $data = $query->orderBy($orderColumn, $orderDir)
                      ->skip($start)
                      ->take($length)
                       ->with(relations: 'ranks') 
                      ->get();

        // Return JSON in DataTables format
        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $filteredData,
            'data'            => $data,
        ]);     
    }
}
