<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Role;
use App\Models\Officer;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $config_data;
    public function __construct(User $user)
    {
        $columnHidden = array_merge($user->getDates(), ['email_verified_at', 'roles']);
        $columnLabels = [
            'id'    => 'User ID',
            'name'  => 'Full Name',
            'email' => 'Email Address',
        ];    
        $optionalFields = ['name', 'email'];

        $this->config_data = (object) [
            "module_name"=>"Users", //Module name
            "module_perm_name"=>"user", //Permission name
            "module_route"=>"users", //Web route
            "module_view_folder"=>"user-management.users", //View folder
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
        $user = User::find(1);
        $user->password = 'password';
        $user->makeVisible('password'); 
        $columnHidden = array_merge($user->getDates(), ['id','email_verified_at','roles']);
        $columnLabels = [
            'name'  => 'Full Name',
            'email' => 'Email Address',
        ];       
        $roles = Role::all()->pluck('name', 'id');
        $user->load('roles');
        $pmcode = Officer::all()->pluck('PM_CODE', 'PM_CODE')->prepend('Please select', '');
        $data_items = [
            "data" => $user,
            "column_hidden" => $columnHidden,
            "column_labels" => $columnLabels,
            "operation_type" => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "roles" => $roles,
            "user" => $user,
            "pmcode" => $pmcode, 
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
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        $user->roles()->sync($request->input('roles', []));
        return redirect()->route($this->config_data->module_route . '.show', $user->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $roles = Role::all()->pluck('name', 'id');
        $user->load('roles');
        $pmcode = Officer::all()->pluck('PM_CODE', 'PM_CODE')->prepend('NO PM CODE', '');
        $data_items = [
            "data" => $user,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "show",
            "user" => $user,
            "roles" => $roles,
            "pmcode" => $pmcode, 
        ];
        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $columnHidden = array_merge($user->getDates(), ['id','email_verified_at','roles']);
        $pmcode = Officer::all()->pluck('PM_CODE', 'PM_CODE')->prepend('NO PM CODE', '');
        $columnLabels = [
            'name'  => 'Full Name',
            'email' => 'Email Address',
        ];  
        $roles = Role::all()->pluck('name', 'id'); 
        $user->load('roles');
        $data_items = [
            "data" => $user,
            "column_hidden" => $columnHidden,
            "column_labels" => $columnLabels,
            "operation_type" => "edit",
            "optional_fields" => $this->config_data->optionalFields,
            "roles" => $roles,
            "user" => $user,
            "pmcode" => $pmcode, 
        ];
        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {   
        abort_if(Gate::denies($this->config_data->module_perm_name.'_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user->update($request->all());
        $user->roles()->sync($request->input('roles', []));
        return redirect()->route($this->config_data->module_route . '.show', $user->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user->delete();
        return back();
    }

    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // All columns in the table
        $columns = ['id', 'name', 'email', 'created_at'];

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
        $query = User::query();

        // Search filter
        $search = $request->input('search.value');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Total records
        $totalData = User::count();
        $filteredData = $query->count();

        // Apply ordering and pagination
        $data = $query->orderBy($orderColumn, $orderDir)
                      ->skip($start)
                      ->take($length)
                      ->with('roles') 
                      ->get();
        // Transform roles so DataTables can display them
        $data = $data->map(function ($user) {
            $user->roles_list = $user->roles->pluck('name')->toArray();
            return $user;
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
