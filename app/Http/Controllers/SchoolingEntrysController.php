<?php

namespace App\Http\Controllers;

use App\Models\SchoolingEntry;
use App\Models\SchoolingUnit;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class SchoolingEntrysController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $config_data;
    public function __construct(SchoolingEntry $schoolingentry)
    {
        $columnHidden = array_merge($schoolingentry->getDates(), ['id']);
        $columnLabels = ['schoolingunits'  => 'Schooling Unit', 'schooling_unit_id'  => 'Schooling Unit', 'name'  => 'Schooling Unit Name',];    
        $optionalFields = ['name', 'schooling_unit_id'];

        $this->config_data = (object) [
            "module_name"=>"Schooling Entries", //Module name
            "module_perm_name"=>"schoolingentry", //Permission name
            "module_route"=>"schoolingentries", //Web route
            "module_view_folder"=>"references.schoolingentry", //View folder
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
        $schoolingentry = SchoolingEntry::find(1);    
        $schoolingentry->fill([
            'schooling_unit_id' => null,
            'assignment_id' => null,
        ]);
        $schoolingunits = SchoolingUnit::all()->pluck('name', 'id');
        $assignments = Assignment::all()->pluck('name', 'id');
        $data_items = [
            "data" => $schoolingentry,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "bulk_insert" => $id,
            "schoolingunits" => $schoolingunits,
            "assignments" => $assignments,
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
        SchoolingEntry::create($data);
        return redirect()->route($this->config_data->module_route . '.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(SchoolingEntry $schoolingentry)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $schoolingentry->load('schoolingunits');
        $columnHidden = array_merge($schoolingentry->getDates(), ['id','schooling_unit_id']);
        $data_items = [
            "data" => $schoolingentry,
            "column_hidden" => $columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "show",
        ];

        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SchoolingEntry $schoolingentry)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $columnHidden = array_merge($schoolingentry->getDates(), ['id']);   
        $schoolingunits = SchoolingUnit::all()->pluck('name', 'id');
        $assignments = Assignment::all()->pluck('name', 'id');
        $data_items = [
            "data" => $schoolingentry,
            "column_hidden" => $columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "edit",
            "optional_fields" => $this->config_data->optionalFields,
            "schoolingunits" => $schoolingunits,
            "assignments" => $assignments,
        ];
        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SchoolingEntry $schoolingentry)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $data = $request->all();
        $schoolingentry->update($data);
        return redirect()->route($this->config_data->module_route . '.index', $schoolingentry->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolingEntry $schoolingentry)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $schoolingentry->delete();
        return back();
    }

    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // All columns in the table
        $columns = ['id', 'name', 'schooling_unit_id', 'assignment_id', 'created_at'];

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
        $query = SchoolingEntry::query();

        // Search filter
        $search = $request->input('search.value');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Total records
        $totalData = SchoolingEntry::count();
        $filteredData = $query->count();

        // Apply ordering and pagination
        $data = $query->orderBy($orderColumn, $orderDir)
                      ->skip($start)
                      ->take($length)
                       ->with(relations:[ 'schoolingunits', 'assignments'])
                      ->get();

        // Transform types so DataTables can display them
        $data = $data->map(function ($schoolingentry) {
            $schoolingentry->types_list = $schoolingentry->schoolingunits->pluck('name')->toArray();
            return $schoolingentry;
        });

        // Return JSON in DataTables format
        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $filteredData,
            'data'            => $data,
        ]);     
    }
}
