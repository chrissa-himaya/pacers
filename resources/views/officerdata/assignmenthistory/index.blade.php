@extends('layouts.app')
@section('content')

<div class="card">
    <div class="card-header">
        <h4 class="d-inline">{{$config_data->module_name}}</h4>
        <a href="{{ route("$config_data->module_route.create", "") }}" class="btn btn-primary float-end">
            Create
        </a>  
    </div>

    <div class="card-body">
        <table id="dataTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>PMCode</th>
                    <th>Entry</th>
                    <th>Unit</th>
                    <th>PAMU</th>
                    <th>Category</th>
                    <th>Pri/Sec/Special Duty</th>
                    <th>Assignment type</th>
                    <th>Geography</th>
                    <th>start_date</th>
                    <th>end_date</th>
                    <th>rank_during_completion</th>
                    <th>year_earned</th>
                    <th>Action</th>
                </tr>
                <tr class="filter-row">
                    <th></th> <!-- Nr column usually no filter -->
                    <th><input type="text" placeholder="Search PM Code" class="form-control form-control-sm" /></th>
                    <th><input type="text" placeholder="Search Entry" class="form-control form-control-sm" /></th>
                    <th><input type="text" placeholder="Search Unit" class="form-control form-control-sm" /></th>
                    <th><input type="text" placeholder="Search PAMU" class="form-control form-control-sm" /></th>
                    <th><input type="text" placeholder="Search Category" class="form-control form-control-sm" /></th>
                    <th><input type="text" placeholder="Search Type of Duty" class="form-control form-control-sm" /></th>
                    <th><input type="text" placeholder="Search Assignment type" class="form-control form-control-sm" /></th>
                    <th><input type="text" placeholder="Search Geography" class="form-control form-control-sm" /></th>
                    <th><input type="date" placeholder="Search start_date" class="form-control form-control-sm" /></th>
                    <th><input type="date" placeholder="Search end_date" class="form-control form-control-sm" /></th>
                    <th><input type="text" placeholder="Search rank_during_completion" class="form-control form-control-sm" /></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        let perm_name = "{{ $config_data->module_perm_name }}";
        let canView = @json(auth()->user()->can($config_data->module_perm_name.'_show', App\Models\AssignmentHistory::class));
        let canUpdate = @json(auth()->user()->can($config_data->module_perm_name.'_edit', App\Models\AssignmentHistory::class));
        let canDelete = @json(auth()->user()->can($config_data->module_perm_name.'_delete', App\Models\AssignmentHistory::class));
        let url_route = "{{ $config_data->module_route }}";

        const table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,     // disable ordering UI
                order: [],           // remove default order 
                ajax: "{{ route("$config_data->module_route.list") }}",
                pageLength: 50,                 // default rows per page
                lengthMenu: [ [10,25,50,100], [10,25,50,100] ], // dropdown options
                scrollX: true,
                columns: [
                    {
                        data: null,
                        // title: 'Nr',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        orderable: false,
                        searchable: false
                    },
                    { data: 'pm_code', searchable: true },
                    { data: 'designations.name', searchable: true },
                    { data: 'units.name', searchable: true },
                    { data: 'pamus.name', searchable: true },
                    { data: 'assignments.name', searchable: true },
                    {
                        data: 'pri_sec_spec',
                        render: function (data) {
                            const map = {
                                primary: 'Primary',
                                secondary: 'Secondary',
                                special: 'Special'
                            };
                            return map[data] ?? data ?? '';
                        }, searchable: true
                    },
                    { data: 'assignment_type.name', searchable: true },
                    {
                        data: 'geography',
                        render: function (data) {
                            const map = {
                                ncr: 'NCR',
                                luzon: 'Luzon',
                                visayas: 'Visayas',
                                mindanao: 'Mindanao',
                                foreign: 'Foreign Duty'
                            };
                            return map[data] ?? data ?? '';
                        }, searchable: true
                    },
                    { data: 'start_date', searchable: true },
                    { data: 'end_date', searchable: true },
                    { data: 'rank_during_completion', searchable: true },
                    { data: 'year_earned', searchable: true },
                    {
                        data: 'id',
                        render: function (data) {
                            let buttons = '';
                            if(canView) {
                                buttons += `
                                    <a href="/${url_route}/${data}" class="btn btn-sm btn-success">
                                        View
                                    </a>
                                `;
                            }

                            if(canUpdate) {
                                buttons += `
                                    <a href="/${url_route}/${data}/edit" class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                `;
                            }

                            if (canDelete) {
                                buttons += `
                                <form action="/${url_route}/${data}" method="POST" style="display:inline;">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        Delete
                                    </button>
                                </form>
                                `;
                            }                   
                                return buttons;
                        },
                        orderable: false,
                        searchable: false
                    }     
                        
                ],
            });

            $('#dataTable thead th').each(function (i) {

                if (i === 0 || i === 14) return;
                // put an input under the header text
                $(this).append('<br><input type="text" placeholder="Search" style="width: 100%;">');
            });

        
            table.columns().every(function (i) {

                if (i === 0 || i === 14) return;

                let timer = null;
                const column = this;

                // get column metadata from DataTables
                const columnSettings = table.settings()[0].aoColumns[i];
                const columnDataName = columnSettings.data;
                const columnTitle = $(column.header()).text().trim();

                $('input', this.header()).on('input change clear', function () {
                    const value = this.value;

                    clearTimeout(timer);

                    timer = setTimeout(function () {

                        console.log('Searching column ->',
                            'index:', i,
                            'data:', columnDataName,
                            'title:', columnTitle,
                            'value:', value
                        );

                        column.search(value).draw();

                    }, 500);
                });
            });

            $(document).on('click', '.deleteRecord', function () {
                let id = $(this).data('id');

                if (confirm('Are you sure you want to delete this record?')) {
                    $.ajax({
                        url: `/users/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            $('#dataTable').DataTable().ajax.reload();
                        }
                    });
                }
            });        

    </script>
@endsection