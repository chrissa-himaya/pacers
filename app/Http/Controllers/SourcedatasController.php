<?php

namespace App\Http\Controllers;

use App\Models\Sourcedata;
use App\Models\Assignment;
use App\Models\Rank;
use App\Models\Rankpoint;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;


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
            "module_name" => "Sourcedatas", //Module name
            "module_perm_name" => "sourcedata", //Permission name
            "module_route" => "sourcedatas", //Web route
            "module_view_folder" => "references.sourcedata", //View folder
            "columnHidden" => $columnHidden,
            "columnLabels" => $columnLabels,
            "optionalFields" => $optionalFields,
        ];

        view()->share('config_data', $this->config_data);
    }
    public function index()
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view($this->config_data->module_view_folder . '.index');
    }

    public function create()
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $sourcedata = new Sourcedata(array_fill_keys((new Sourcedata())->getFillable(), ''));
        $assignments = Assignment::with('types') // or 'types' if your relation is plural
            ->get()
            ->mapWithKeys(function ($assignment) {
                return [
                    $assignment->id => $assignment->name . ' - ' . optional($assignment->types)->name
                ];
            })
            ->prepend('Please select', '');

        $ranks = Rank::all()->pluck('code', 'id')->prepend('Please select', '');
        // $columnHidden = array_merge($rank->getDates(), ['id']);      

        $data_items = [
            "data" => $sourcedata,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "create",
            "optional_fields" => $this->config_data->optionalFields,
            "assignments" => $assignments,
            "ranks" => $ranks,
        ];
        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $data = $request->all();
        Sourcedata::create($data);
        return redirect()->route($this->config_data->module_route . '.index');
    }
    public function show(Sourcedata $sourcedata)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assignments = Assignment::with('types')
            ->get()
            ->mapWithKeys(function ($assignment) {
                return [
                    $assignment->id => $assignment->name . ' - ' . optional($assignment->types)->name
                ];
            })
            ->prepend('', '');
        $ranks = Rank::all()->pluck('code', 'id');
        $columnHidden = array_merge($sourcedata->getDates(), ['id', 'type_id']);

        $data_items = [
            "data" => $sourcedata,
            "column_hidden" => $columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "show",
            "assignments" => $assignments,
            "ranks" => $ranks,
            // "rankpoints_grouped" => $rankpointsGrouped,
        ];

        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sourcedata $sourcedata)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $columnHidden = array_merge($sourcedata->getDates(), ['id']);
        $assignments = Assignment::with('types') // or 'types' if your relation is plural
            ->get()
            ->mapWithKeys(function ($assignment) {
                return [
                    $assignment->id => $assignment->name . ' - ' . optional($assignment->types)->name
                ];
            })
            ->prepend('Please select', '');
        $ranks = Rank::all()->pluck('code', 'id');
        $rankpointsGrouped = Rankpoint::with('ranks')->get()->groupBy('name');


        $data_items = [
            "data" => $sourcedata,
            "column_hidden" => $columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "edit",
            "optional_fields" => $this->config_data->optionalFields,
            "assignments" => $assignments,
            "ranks" => $ranks,
            "rankpoints_grouped" => $rankpointsGrouped,
        ];

        // return $data_items["data"];
        return view($this->config_data->module_view_folder . '.show', compact('data_items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sourcedata $sourcedata)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $validated = $request->validate([
            'assignment_id' => ['required', 'exists:assignments,id'],
            'rank_id' => ['required', 'exists:ranks,id'],
        ]);

        $sourcedata->update($validated);
        return redirect()->route($this->config_data->module_route . '.index', $sourcedata->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sourcedata $sourcedata)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $sourcedata->delete();
        return back();
    }

    public function list(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // All columns in the table
        $columns = ['id', 'name', 'created_at'];

        // Pagination values from DataTables
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        // Prevent invalid length (MariaDB requires LIMIT)
        if ($length <= 0) {
            $length = 10;
        }

        // Ordering
        $orderIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

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
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Total records
        $totalData = Sourcedata::count();
        $filteredData = $query->count();

        // Apply ordering and pagination
        $data = $query->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->with(['assignments', 'ranks', 'assignments.types'])
            ->get();

        // Return JSON in DataTables format
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $filteredData,
            'data' => $data,
        ]);
    }
}
