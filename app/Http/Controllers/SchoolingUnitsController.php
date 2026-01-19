<?php

namespace App\Http\Controllers;

use App\Models\SchoolUnit;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class SchoolingUnitsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $config_data;
    public function __construct(SchoolUnit $schoolingunit)
    {
        $columnHidden = array_merge($schoolingunit->getDates(), ['id']);
        $columnLabels = [''];    
        $optionalFields = ['name', 'location'];

        $this->config_data = (object) [
            "module_name"=>"Schooling Units", //Module name
            "module_perm_name"=>"schoolingunit", //Permission name
            "module_route"=>"schoolingunits", //Web route
            "module_view_folder"=>"references.schoolingunit", //View folder
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
    public function create($id="0")
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $schoolingunit = SchoolUnit::find(1);
        $columnHidden = array_merge($schoolingunit->getDates(), ['id']);
        $columnLabels = [
            'name'  => 'School/Unit Name',
            'location' => 'Location',
        ];       
        
        $data_items = [
            "data" => $schoolingunit,
            "column_hidden" => $columnHidden,
            "column_labels" => $columnLabels,
            "operation_type" => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "bulk_insert" => $id,
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
        $schoolingunit = SchoolUnit::create($data);
        return redirect()->route($this->config_data->module_route . '.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(SchoolUnit $schoolingunit)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $data_items = [
            "data" => $schoolingunit,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "show",
        ];
        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SchoolUnit $schoolingunit)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $columnHidden = array_merge($schoolingunit->getDates(), ['id']);
        $columnLabels = [
            'name'  => 'School/Unit Name',
            'location' => 'Location',
        ];   

        $data_items = [
            "data" => $schoolingunit,
            "column_hidden" => $columnHidden,
            "column_labels" => $columnLabels,
            "operation_type" => "edit",
            "optional_fields" => $this->config_data->optionalFields,
        ];
        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SchoolUnit $schoolingunit)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $data = $request->all();
        $schoolingunit->update($data);
        return redirect()->route($this->config_data->module_route . '.index', $schoolingunit->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolUnit $schoolingunit)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $schoolingunit->delete();
        return back();
    }

    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // All columns in the table
        $columns = ['id', 'name', 'created_at'];

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
        $query = SchoolUnit::query();

        // Search filter
        $search = $request->input('search.value');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Total records
        $totalData = SchoolUnit::count();
        $filteredData = $query->count();

        // Apply ordering and pagination
        $data = $query->orderBy($orderColumn, $orderDir)
                      ->skip($start)
                      ->take($length)
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
