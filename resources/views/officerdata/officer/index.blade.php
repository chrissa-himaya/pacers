@extends('layouts.app')
@section('content')

    <div class="index-wrapper">
        <div class="card">
            <div class="index-header">
                <h4><i class="fas fa-users"></i>{{$config_data->module_name}}</h4>
                <div>
                    @can($config_data->module_perm_name . '_create')
                    <a href="{{ route($config_data->module_route.'.bulkcreate') }}" class="btn-create">
                        <i class="fas fa-upload me-1"></i> Bulk Upload
                    </a>
                    &nbsp;
                    <a href="{{ route("$config_data->module_route.create", "") }}" class="btn-create">
                        <i class="fas fa-plus me-1"></i> Create New
                    </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nr</th>
                                <th>SRTY</th>
                                <th>PM CODE</th>
                                <th>NAME</th>
                                <th>SUFFIX</th>
                                <th>RANK</th>
                                <th>AFPSN</th>
                                <th>AFPOS</th>
                                <th>TYPE</th>
                                <th>SIG</th>
                                <th>SEX</th>
                                <th>DOR</th>
                                <th>TACS</th>
                                <th>DOB</th>
                                <th>DOC</th>
                                <th>RET</th>
                                <th>HCC</th>
                                <th>SOC</th>
                                <th>REMARKS</th>
                                <th>DESIGNATION</th>
                                <th>UNIT</th>
                                <th>Roles</th>
                                <th>Action</th>
                            </tr>
                            <tr class="filter-row">
                                <th></th>
                                <th><input type="text" placeholder="Search SRTY" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search PM CODE" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search NAME" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search SUFFIX" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search RANK" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search AFPSN" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search AFPOS" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search TYPE" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search SIG" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search SEX" class="form-control form-control-sm" /></th>
                                <th><input type="date" placeholder="Search DOR" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search TACS" class="form-control form-control-sm" /></th>
                                <th><input type="date" placeholder="Search DOB" class="form-control form-control-sm" /></th>
                                <th><input type="date" placeholder="Search DOC" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search RET" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search HCC" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search SOC" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search REMARKS" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search DESIGNATION" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Search UNIT" class="form-control form-control-sm" /></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        let perm_name = "{{ $config_data->module_perm_name }}";
        let canView = @json(auth()->user()->can($config_data->module_perm_name . '_show', App\Models\Officer::class));
        let canUpdate = @json(auth()->user()->can($config_data->module_perm_name . '_edit', App\Models\Officer::class));
        let canDelete = @json(auth()->user()->can($config_data->module_perm_name . '_delete', App\Models\Officer::class));
        let url_route = "{{ $config_data->module_route }}";

        const table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            order: [],
            ajax: {
                url: "{{ route("$config_data->module_route.list") }}",
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('DataTables AJAX Error:', textStatus, errorThrown);
                }
            },
            pageLength: 50,
            lengthMenu: [ [10,25,50,100], [10,25,50,100] ],
            scrollX: true,
            autoWidth: false,
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn btn-sm btn-default',
                    text: '<i class="fas fa-copy"></i> Copy',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn btn-sm btn-default',
                    text: '<i class="fas fa-file-csv"></i> CSV',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn btn-sm btn-default',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-sm btn-default',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-default',
                    text: '<i class="fas fa-print"></i> Print',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'colvis',
                    className: 'btn btn-sm btn-info',
                    text: '<i class="fas fa-columns"></i> Column visibility',
                    columnText: function ( dt, idx, title ) {
                            return $(dt.column(idx).header()).closest('thead').find('tr:first-child th').eq(idx).text();
                        }
                }
            ],
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false,
                    searchable: false
                },
                { data: 'SRTY', searchable: true },
                { 
                    data: 'PM_CODE', 
                    searchable: true,
                    render: function (data, type, row) {
                        return `<a href="/profile/${data}" class="text-primary font-weight-bold">
                                    ${data}
                                </a>`;
                    }
                },
                { data: 'NAME', searchable: true },
                { data: 'SUFFIX', searchable: true },
                { data: 'RANK', searchable: true },
                { data: 'AFPSN', searchable: true },
                { data: 'AFPOS', searchable: true },
                { data: 'TYPE', searchable: true },
                { data: 'SIG', searchable: true },
                { data: 'SEX', searchable: true },
                { data: 'DOR', searchable: true },
                { data: 'TACS', searchable: true },
                { data: 'DOB', searchable: true },
                { data: 'DOC', searchable: true },
                { data: 'RET', searchable: true },
                { data: 'HCC', searchable: true },
                { data: 'SOC', searchable: true },
                { data: 'REMARKS', searchable: true },
                { data: 'designations', searchable: true },
                { data: 'units.name', searchable: true },
                { data: 'roles.name', searchable: true },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        let buttons = '<div class="action-btns">';
                        if (canView) {
                            buttons += `
                                    <a href="/profile/${row.PM_CODE}" class="btn btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                `;
                        }

                        if (canUpdate) {
                            buttons += `
                                    <a href="/${url_route}/${data}/edit" class="btn btn-edit">
                                        <i class="fas fa-pen"></i> Edit
                                    </a>
                                `;
                        }

                        if (canDelete) {
                            buttons += `
                                <form action="/${url_route}/${data}" method="POST" style="display:inline;">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                `;
                        }
                        buttons += '</div>';
                        return buttons;
                    },
                    orderable: false,
                    searchable: false
                }

            ],
        });

        // Recalculate responsive layout when column visibility changes
        table.on('column-visibility.dt', function (e, settings, column, state) {
            // Force table to recalculate column widths and adjust
            setTimeout(function() {
                table.columns.adjust().responsive.recalc();
            }, 10);
        });

        table.columns().every(function (i) {
            if (i === 0 || i === 23) return;

            let timer = null;
            const column = this;

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