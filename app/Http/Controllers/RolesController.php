<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Permission;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $config_data;
    public function __construct(Role $role)
    {
        $columnHidden = array_merge($role->getDates(), ['id','permissions']);
        $columnLabels = [''];    
        $optionalFields = ['name', 'email'];

        $this->config_data = (object) [
            "module_name"=>"Roles", //Module name
            "module_perm_name"=>"role", //Permission name
            "module_route"=>"roles", //Web route
            "module_view_folder"=>"user-management.roles", //View folder
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
        $permission = Role::find(1);
        $columnHidden = array_merge($permission->getDates(), ['id','guard_name']);
        $columnLabels = [
            'name'  => 'Roles Name',
        ];       

        $permissions = Permission::all()->pluck('name', 'id');
        
        $data_items = [
            "data" => $permission,
            "column_hidden" => $columnHidden,
            "column_labels" => $columnLabels,
            "operation_type" => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "permissions" => $permissions,
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
        $data['guard_name'] = "web";
        $role = Role::create($data);
        $role->permissions()->sync($request->input('permissions', []));
        return redirect()->route($this->config_data->module_route . '.show', $role->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $permissions = Permission::all()->pluck('name', 'id');
        $role->load('permissions');

        $data_items = [
            "data" => $role,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "show",
            "permissions" => $permissions,
            "role" => $role,
        ];
        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $columnHidden = array_merge($role->getDates(), ['id','guard_name', 'permissions']);
        $columnLabels = [
            'name'  => 'Role Name',
        ];   

        $permissions = Permission::all()->pluck('name', 'id');
        $role->load('permissions');
        $data_items = [
            "data" => $role,
            "column_hidden" => $columnHidden,
            "column_labels" => $columnLabels,
            "operation_type" => "edit",
            "optional_fields" => $this->config_data->optionalFields,
            "permissions" => $permissions,
            "role" => $role,
        ];
        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $data = $request->all();
        $data['guard_name'] = "web";
        $role->update($data);
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route($this->config_data->module_route . '.show', $role->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $role->delete();
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
        $query = Role::query();

        // Search filter
        $search = $request->input('search.value');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Total records
        $totalData = Role::count();
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
