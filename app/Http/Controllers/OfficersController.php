<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class OfficersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $config_data;
    public function __construct(Officer $officer)
    {
        $columnHidden = array_merge($officer->getDates(), ['id']);
        $columnLabels = [''];    
        $optionalFields = ['name', 'email'];

        $this->config_data = (object) [
            "module_name"=>"Officers", //Module name
            "module_perm_name"=>"officer", //Permission name
            "module_route"=>"officers", //Web route
            "module_view_folder"=>"officerdata.officer", //View folder
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
    public function show(Officer $officer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Officer $officer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Officer $officer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Officer $officer)
    {
        //
    }

public function list(Request $request)
{
    abort_if(
        Gate::denies($this->config_data->module_perm_name.'_access'),
        Response::HTTP_FORBIDDEN,
        '403 Forbidden'
    );

    // DataTables column index mapping (Nr and Actions are null)
    $dtColumns = [
        null,        // 0 Nr
        'SRTY',
        'PM_CODE',
        'NAME',
        'SUFFIX',
        'RANK',
        'AFPSN',
        'AFPOS',
        'TYPE',
        'SIG',
        'SEX',
        'DOR',
        'TACS',
        'DOB',
        'DOC',
        'RET',
        'HCC',
        'SOC',
        'REMARKS',
        'DESIGNATION',
        'UNIT',
        null,        // 19 Actions
    ];

    $globalSearchColumns = ['SRTY','PM_CODE','NAME','SUFFIX','RANK','AFPSN','AFPOS','TYPE','SIG','SEX','DOR','TACS','DOB','DOC','RET','HCC','SOC','REMARKS','DESIGNATION','UNIT',]; // change as you like

    $start  = (int) $request->input('start', 0);
    $length = (int) $request->input('length', 10);
    if ($length <= 0) $length = 10;

    $query = Officer::query();

    $search = trim((string) $request->input('search.value', ''));
    if ($search !== '') {
        $query->where(function ($q) use ($search, $globalSearchColumns) {
            foreach ($globalSearchColumns as $col) {
                $q->orWhere($col, 'like', "%{$search}%");
            }
        });
    }

    foreach ($dtColumns as $index => $column) {
        if (!$column) continue;

        $colSearch = trim((string) $request->input("columns.$index.search.value", ''));
        if ($colSearch !== '') {
            $query->where($column, 'like', "%{$colSearch}%");
            // $query->where($column, '=', "{$colSearch}");
        }
    }

    $totalData    = Officer::count();
    $filteredData = (clone $query)->count();

    // (optional) but you can still add a stable default if you want:
    $query->orderBy('id', 'asc');

    $data = $query->skip($start)->take($length)->get();

    return response()->json([
        'draw'            => (int) $request->input('draw'),
        'recordsTotal'    => $totalData,
        'recordsFiltered' => $filteredData,
        'data'            => $data,
    ]);
}



}
