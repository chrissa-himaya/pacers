<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
// use OwenIt\Auditing\Models\Audit;
use App\Models\Audit;

class AuditController extends Controller
{
    protected $config_data;
    public function __construct(Audit $audit)
    {
        $columnHidden = array_merge($audit->getDates(), ['id','permissions']);
        $columnLabels = [''];    
        $optionalFields = ['name', 'email'];

        $this->config_data = (object) [
            "module_name"=>"Audit Trails", //Module name
            "module_perm_name"=>"audit", //Permission name
            "module_route"=>"audit-trails", //Web route
            "module_view_folder"=>"user-management.audit-trails", //View folder
            "columnHidden"=>$columnHidden,
            "columnLabels"=>$columnLabels,
            "optionalFields"=>$optionalFields,
        ];
        view()->share('config_data', $this->config_data);
    }  


    /**
     * Display a listing of the resource.
     */
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $audit = Audit::with('user')->findOrFail($id);
        $data_items = [
            "data" => $audit,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "show",
        ];
        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function list(Request $request)
    {
        abort_if(
            Gate::denies($this->config_data->module_perm_name . '_access'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );
    
        // All columns in the table
        $columns = [
            'id',
            'event',
            'auditable_type',
            'auditable_id',
            'created_at',
        ];
    
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
    
        // Base query (Laravel Auditing)
        $query = Audit::with('user');
    
        // Search filter
        $search = $request->input('search.value');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('event', 'like', "%{$search}%")
                  ->orWhere('auditable_type', 'like', "%{$search}%")
                  ->orWhere('auditable_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }
    
        // Total records
        $totalData     = Audit::count();
        $filteredData  = $query->count();
    
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
