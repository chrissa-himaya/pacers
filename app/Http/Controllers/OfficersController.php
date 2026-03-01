<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Models\Designation;
use App\Models\Role;
use App\Models\Unit;

use PhpOffice\PhpSpreadsheet\Shared\Date;

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
            "module_name" => "Officers", //Module name
            "module_perm_name" => "officer", //Permission name
            "module_route" => "officers", //Web route
            "module_view_folder" => "officerdata.officer", //View folder
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $officer      = new Officer();          // empty model for create
        $designations = Designation::all()->pluck('name', 'id');
        $units        = Unit::all()->pluck('name', 'id');
        $roleType     = Role::all()->pluck('name', 'id');

        return view($this->config_data->module_view_folder . '.show', [
            'operation_type' => 'create',
            'officer'        => $officer,
            'designations'   => $designations,
            'units'          => $units,
            'roleType'       => $roleType,
        ]);
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'PM_CODE' => ['required', 'string', 'unique:personnel_records,PM_CODE'],
            // add more rules as needed
        ]);

        Officer::create($request->only([
            'SRTY','PM_CODE','NAME','SUFFIX','RANK','AFPSN','AFPOS','TYPE','SIG','SEX',
            'DOR','TACS','DOC','DOB','RET','HCC','SOC','REMARKS',
            'designation_id','unit_id','role_id',
        ]));

        return redirect()->route($this->config_data->module_route . '.index')
            ->with('success', 'Officer created successfully.');
    }

    public function bulkCreate()
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $officer = Officer::find(1);
        $officer->fill([
            'pm_code' => null,
            'designation_id' => null,
            'unit_id' => null,
            'role_id' => null,
        ]);

        $designations = Designation::all()->pluck('name', 'id');
        $units = Unit::all()->pluck('name', 'id');
        $roleType = Role::all()->pluck('name', 'id');

        $data_items = [
            "data" => $officer,
            "column_hidden" => $this->config_data->columnHidden,
            "column_labels" => $this->config_data->columnLabels,
            "operation_type" => "create",
            "optional_fields" => $this->config_data->optionalFields,
            'designations' => $designations,
            'units' => $units,
            'roleType' => $roleType,
            'bulk_insert' => 'bulk',
        ];

        return view($this->config_data->module_view_folder . '.bulk');
        // return view($this->config_data->module_view_folder.'.show', compact('data_items'));
    }

    public function bulkStore(Request $request)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        // Must match DB columns (and your Excel template)
        $expectedHeaders = [
            'SRTY','PM_CODE','NAME','SUFFIX','RANK','AFPSN','AFPOS','TYPE','SIG','SEX',
            'DOR','TACS','DOC','DOB','RET','HCC','SOC','REMARKS',
            'designation_id','unit_id','role_id',
        ];

        $path = $request->file('file')->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, true);
        if (count($rows) < 2) {
            return back()->withErrors(['file' => 'Excel file has no data rows.'])->withInput();
        }

        // Header row
        $rawHeader = $rows[1];               // ['A'=>'SRTY', 'B'=>'PM_CODE', ...]
        $letters   = array_keys($rawHeader); // ['A','B',...]
        $headerRow = array_values($rawHeader);

        $headerNorm   = array_map([$this, 'normalizeHeader'], $headerRow);
        $expectedNorm = array_map([$this, 'normalizeHeader'], $expectedHeaders);

        if ($this->sortedUnique($headerNorm) !== $this->sortedUnique($expectedNorm)) {
            return back()->withErrors([
                'file' => "Invalid headers. Required headers are: " . implode(', ', $expectedHeaders)
            ])->withInput();
        }

        // Map normalized header => column letter (A,B,C...)
        $headerMap = [];
        foreach ($letters as $i => $colLetter) {
            $h = $headerNorm[$i] ?? null;
            if ($h) $headerMap[$h] = $colLetter;
        }

        $dateFields = ['dor','doc','dob','ret'];

        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($rows as $rowNumber => $row) {
            if ($rowNumber === 1) continue;

            // Skip empty rows
            $allEmpty = true;
            foreach ($headerMap as $hNorm => $colLetter) {
                $v = $row[$colLetter] ?? null;
                if ($v !== null && trim((string)$v) !== '') { $allEmpty = false; break; }
            }
            if ($allEmpty) { $skipped++; continue; }

            // Build payload based on expected headers
            $payload = [];
            foreach ($expectedHeaders as $hdr) {
                $hNorm = $this->normalizeHeader($hdr);
                $colLetter = $headerMap[$hNorm] ?? null;
                $val = $colLetter ? ($row[$colLetter] ?? null) : null;

                if (is_string($val)) $val = trim($val);
                if ($val === '') $val = null;

                // Convert Excel date serials / strings to Y-m-d
                if ($val !== null && in_array($hNorm, $dateFields, true)) {
                    $val = $this->parseExcelDate($val);
                }

                // Use DB column names exactly (case sensitive columns are ok in MySQL but keep same)
                $payload[$hdr] = $val;
            }

            // Choose your unique key (PM_CODE is the best here)
            $pm = $payload['PM_CODE'] ?? null;
            if (!$pm) {
                $errors[] = "Row {$rowNumber}: PM_CODE is required.";
                $skipped++;
                continue;
            }

            $officer = Officer::where('PM_CODE', $pm)->first();
            if ($officer) {
                $officer->fill($payload)->save();
                $updated++;
            } else {
                Officer::create($payload);
                $inserted++;
            }
        }

        return redirect()->route($this->config_data->module_route . '.index')
            ->with('success', "Bulk upload done. Inserted: {$inserted}, Updated: {$updated}, Skipped: {$skipped}")
            ->with('bulk_errors', $errors);
    }

    /**
     * Display the specified resource.
     */
    public function show(Officer $officer)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $designations = Designation::all()->pluck('name', 'id');
        $units        = Unit::all()->pluck('name', 'id');
        $roleType     = Role::all()->pluck('name', 'id');

        return view($this->config_data->module_view_folder . '.show', [
            'operation_type' => 'show',
            'officer'        => $officer->load(['designations', 'units', 'roles']),
            'designations'   => $designations,
            'units'          => $units,
            'roleType'       => $roleType,
        ]);
    }

    public function edit(Officer $officer)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $designations = Designation::all()->pluck('name', 'id');
        $units        = Unit::all()->pluck('name', 'id');
        $roleType     = Role::all()->pluck('name', 'id');

        return view($this->config_data->module_view_folder . '.show', [
            'operation_type' => 'edit',
            'officer'        => $officer->load(['designations', 'units', 'roles']),
            'designations'   => $designations,
            'units'          => $units,
            'roleType'       => $roleType,
        ]);
    }

    public function update(Request $request, Officer $officer)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'PM_CODE' => ['required', 'string', 'unique:personnel_records,PM_CODE,' . $officer->id],
            // add more rules as needed
        ]);

        $officer->fill($request->only([
            'SRTY','PM_CODE','NAME','SUFFIX','RANK','AFPSN','AFPOS','TYPE','SIG','SEX',
            'DOR','TACS','DOC','DOB','RET','HCC','SOC','REMARKS',
            'designation_id','unit_id','role_id',
        ]))->save();

        return redirect()->route($this->config_data->module_route . '.index')
            ->with('success', 'Officer updated successfully.');
    }

    public function destroy(Officer $officer)
    {
        abort_if(Gate::denies($this->config_data->module_perm_name . '_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $officer->delete();

        return redirect()->route($this->config_data->module_route . '.index')
            ->with('success', 'Officer deleted successfully.');
    }

    public function list(Request $request)
    {
        abort_if(
            Gate::denies($this->config_data->module_perm_name . '_access'),
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
            'designations.name',
            'units.name',
            'role_id',
            null,        // 23 Actions
        ];

        $globalSearchColumns = ['SRTY', 'PM_CODE', 'NAME', 'SUFFIX', 'RANK', 'AFPSN', 'AFPOS', 'TYPE', 'SIG', 'SEX', 'DOR', 'TACS', 'DOB', 'DOC', 'RET', 'HCC', 'SOC', 'REMARKS', 'designations.name', 'units.name', 'role_id',];

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length <= 0)
            $length = 10;

        $query = Officer::query();

        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search, $globalSearchColumns) {
                foreach ($globalSearchColumns as $col) {

                    if (str_contains($col, '.')) {
                        [$relation, $field] = explode('.', $col, 2);

                        $q->orWhereHas($relation, function ($r) use ($field, $search) {
                            $r->where($field, 'like', "%{$search}%");
                        });

                        continue;
                    }
                    $q->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        foreach ($dtColumns as $index => $column) {
            if (!$column)
                continue;

        $colSearch = trim((string) $request->input("columns.$index.search.value", ''));
        if ($colSearch === '')
            continue;
        if (str_contains($column, '.')) {
                [$relation, $field] = explode('.', $column, 2);

                $query->whereHas($relation, function ($r) use ($field, $colSearch) {
                    $r->where($field, 'like', "%{$colSearch}%");
                });
            } else {
                $query->where($column, 'like', "%{$colSearch}%");
            }
        }

        $totalData = Officer::count();
        $filteredData = (clone $query)->count();

        // (optional) but you can still add a stable default if you want:
        $query->orderBy('id', 'asc');

        $data = $query->skip($start)
            ->take($length)
            ->with([
                'designations',
                'units',
                'roles',
            ])
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $filteredData,
            'data' => $data,
        ]);
    }


    // Put these inside OfficersController class

    private function normalizeHeader($h): string
    {
        $h = (string) $h;
        $h = trim($h);
        $h = preg_replace('/\s+/', ' ', $h); // collapse multiple spaces
        $h = mb_strtolower($h);              // case-insensitive compare
        return $h;
    }

    private function sortedUnique(array $arr): array
    {
        $arr = array_filter($arr, fn($v) => $v !== null && $v !== '');
        $arr = array_values(array_unique($arr));
        sort($arr);
        return $arr;
    }

    private function parseExcelDate($value): ?string
    {
        // Excel serial number
        if (is_numeric($value)) {
            try {
                return Carbon::instance(
                    Date::excelToDateTimeObject($value)
                )->format('yyyy-mm-dd');
            } catch (\Throwable $e) {
                return null;
            }
        }

        // String date
        try {
            return Carbon::parse((string)$value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
