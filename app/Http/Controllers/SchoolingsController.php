<?php

namespace App\Http\Controllers;

use App\Models\Schooling;
use App\Models\Assignment;
use App\Models\SchoolingEntry;
use App\Models\SchoolingUnit;
use App\Models\Officer;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class SchoolingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $config_data;
    public function __construct(Schooling $schooling)
    {
        $columnHidden = array_merge($schooling->getDates(), ['id']);
        $columnLabels = [''];    
        $optionalFields = ['name', 'email'];

        $this->config_data = (object) [
            "module_name"=>"Schoolings", //Module name
            "module_perm_name"=>"schooling", //Permission name
            "module_route"=>"schoolings", //Web route
            "module_view_folder"=>"officerdata.schooling", //View folder
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
        $schooling = Schooling::find(1);
        $schooling->fill([
            'schooling_entries_id' => null,
            'school_unit_id' => null,
            'assignment_id' => null,
        ]);

        $schoolingentries = SchoolingEntry::with('schoolingunits')->get()->keyBy('id');
        $assignments = Assignment::with('types')->get()->keyBy('id');
        $data_items = [
            "data" => $schooling,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "bulk_insert" => $id,
            "assignments" => $assignments,
            "schoolingentries" => $schoolingentries,
        ];

        return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $action = $request->input('action');
        $pm_code = $request->input('pm_code');

        if ($action == 'Fetch Data') {
            echo "Fetch Data button was clicked.".$pm_code;

            $schooling = Officer::where('PM_CODE', $pm_code)->first();

            return back()->withInput($request->all())
            ->with([
                'rank' => $schooling->RANK,
                'name' => $schooling->NAME,
                'afpsn' => $schooling->AFPSN,
                'afpos' => $schooling->AFPOS,
                // 'sex' => $schooling->SEX,
                'dob' => $schooling->DOB,
                'date_ret' => $schooling->RET,
                'soc' => $schooling->SOC,
                'type' => $schooling->TYPE,
                'otd' => $schooling->OTD,
                'dor' => $schooling->DOR,
                'tig' => $schooling->TIG,
                'designation' => $schooling->DESIGNATION,
                'unit' => $schooling->UNIT,
            ]);

        } elseif ($action == 'Save') {
            dd( $request->all());
        }
            
    }

    /**
     * Display the specified resource.
     */
    public function show(Schooling $schooling)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schooling $schooling)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schooling $schooling)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schooling $schooling)
    {
        //
    }

    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // All columns in the table
        $columns = [ 'id', 'pm_code', 'schooling_entries_id', 'schooling_unit_id', 'assignment_id', 'rating', 'standing', 'total_student', 'rank_during_completion', '2lt', '1lt', 'cpt', 'ltc', 'col', 'created_at', 'updated_at' ];

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
        $query = Schooling::query();

        // Search filter
        $search = $request->input('search.value');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('pm_code', 'like', "%{$search}%");
            });
        }

        // Total records
        $totalData = Schooling::count();
        $filteredData = $query->count();

        // Apply ordering and pagination
        $data = $query->orderBy($orderColumn, $orderDir)
                      ->skip($start)
                      ->take($length)
                      ->with(['schoolingentries', 'schoolingunits', 'assignments'])
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
