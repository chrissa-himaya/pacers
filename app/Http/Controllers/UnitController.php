<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Pamu;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;


class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $config_data;
    public function __construct(Unit $unit)
    {
        $columnHidden = array_merge($unit->getDates(), ['id']);
        $columnLabels = [''];    
        $optionalFields = [''];

        $this->config_data = (object) [
            "module_name"=>"Unit", //Module name
            "module_perm_name"=>"unit", //Permission name
            "module_route"=>"units", //Web route
            "module_view_folder"=>"references.unit", //View folder
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
        $unit = Unit::find(1);    
        $unit->fill([
            'pamu_id' => null,
        ]);
        $pamus = Pamu::all()->pluck('name', 'id');
        $data_items = [
            "data" => $unit,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "pamus" => $pamus,
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
        Unit::create($data);
        return redirect()->route($this->config_data->module_route . '.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $unit->load('pamus');
        $columnHidden = array_merge($unit->getDates(), ['id','pamu_id']);
        $data_items = [
            "data" => $unit,
            "column_hidden" => $columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "show",
        ];

        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $columnHidden = array_merge($unit->getDates(), ['id']);   
        $pamus = Pamu::all()->pluck('name', 'id')   ;
        $data_items = [
            "data" => $unit,
            "column_hidden" => $columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "edit",
            "optional_fields" => $this->config_data->optionalFields,
            "pamus" => $pamus,
        ];
        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $data = $request->all();
        $unit->update($data);
        return redirect()->route($this->config_data->module_route . '.index', $unit->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $unit->delete();
        return back();
    }

    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // All columns in the table
        $columns = ['id', 'name', 'pamu_id', 'created_at', 'updated_at'];

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
        $query = Unit::query();

        // Search filter
        $search = $request->input('search.value');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Total records
        $totalData = Unit::count();
        $filteredData = $query->count();

        // Apply ordering and pagination
       $data = $query->orderBy($orderColumn, $orderDir)
                      ->skip($start)
                      ->take($length)
                       ->with(relations: 'pamus') 
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
