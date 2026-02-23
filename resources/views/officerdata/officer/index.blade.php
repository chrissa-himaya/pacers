@extends('layouts.app')
@section('content')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');

        .index-wrapper {
            font-family: 'DM Sans', sans-serif;
        }

        .index-wrapper .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
            overflow: hidden;
        }

        .index-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8e 100%);
            color: #fff;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .index-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: .3px;
        }

        .index-header h4 i {
            margin-right: 8px;
            opacity: .8;
        }

        .btn-create {
            background: rgba(255, 255, 255, .15);
            color: #fff;
            border: 1.5px solid rgba(255, 255, 255, .35);
            border-radius: 8px;
            padding: 7px 20px;
            font-weight: 600;
            font-size: .85rem;
            transition: all .2s;
            text-decoration: none;
            margin-left: 10px;
        }

        .btn-create:hover {
            background: rgba(255, 255, 255, .25);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
        }

        .btn-bulk {
            background: rgba(255, 255, 255, .15);
            color: #fff;
            border: 1.5px solid rgba(255, 255, 255, .35);
            border-radius: 8px;
            padding: 7px 20px;
            font-weight: 600;
            font-size: .85rem;
            transition: all .2s;
            text-decoration: none;
        }

        .btn-bulk:hover {
            background: rgba(255, 255, 255, .25);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
        }

        .index-wrapper thead tr.filter-row th {
            background: #f8f9fb;
            padding: 6px 4px;
            border-bottom: 2px solid #d6dce5;
        }

        .index-wrapper thead tr.filter-row input,
        .index-wrapper thead tr.filter-row select {
            font-family: 'DM Sans', sans-serif;
            font-size: .76rem;
            border-radius: 5px;
            border: 1px solid #d0d8e0;
            padding: 4px 8px;
            width: 100%;
            transition: border-color .2s, box-shadow .2s;
        }

        .index-wrapper thead tr.filter-row input:focus,
        .index-wrapper thead tr.filter-row select:focus {
            border-color: #2d5a8e;
            box-shadow: 0 0 0 2px rgba(45, 90, 142, .12);
            outline: none;
        }

        .index-wrapper .table thead th {
            background: #eef2f7;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
            color: #4a5568;
            border-bottom: 2px solid #d6dce5;
            padding: 10px 6px;
            white-space: nowrap;
        }

        .index-wrapper .table tbody td {
            font-size: .8rem;
            padding: 8px 6px;
            vertical-align: middle;
            color: #374151;
        }

        .index-wrapper .table tbody tr:hover {
            background: #f0f7ff;
        }

        .index-wrapper .table tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }

        .index-wrapper .table tbody tr:nth-child(even):hover {
            background: #f0f7ff;
        }

        .index-wrapper .table tbody td.mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: .75rem;
            color: #2d6e3f;
        }

        .action-btns {
            display: flex;
            gap: 4px;
            flex-wrap: nowrap;
        }

        .action-btns .btn {
            font-size: .72rem;
            padding: 3px 10px;
            border-radius: 5px;
            font-weight: 600;
            white-space: nowrap;
            border: none;
            transition: transform .1s;
        }

        .action-btns .btn:hover {
            transform: translateY(-1px);
        }

        .action-btns .btn-view {
            background: #d1fae5;
            color: #065f46;
        }

        .action-btns .btn-edit {
            background: #fef3c7;
            color: #92400e;
        }

        .action-btns .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .index-wrapper .dataTables_wrapper .dataTables_info,
        .index-wrapper .dataTables_wrapper .dataTables_length,
        .index-wrapper .dataTables_wrapper .dataTables_filter {
            font-size: .82rem;
            color: #666;
        }

        .index-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #2d5a8e !important;
            color: #fff !important;
            border-color: #2d5a8e !important;
            border-radius: 6px;
        }

        .module-header {
            padding-right: 15px;
            padding-left: 15px;
            padding-top: 10px;
        }

        .date-badge {
            display: inline-block;
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #7dd3fc;
            border-radius: 4px;
            padding: 1px 6px;
            font-family: 'JetBrains Mono', monospace;
            font-size: .72rem;
            font-weight: 600;
        }

        /* DataTables Buttons Styling */
        /* .index-wrapper .dt-buttons {
            margin-bottom: 10px;
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        } */

        .index-wrapper .dt-buttons .btn {
            background: #fff;
            border: 1px solid #d0d8e0;
            color: #4a5568;
            font-size: .78rem;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: 500;
            transition: all .2s;
        }

        .index-wrapper .dt-buttons .btn:hover {
            background: #f0f4f8;
            border-color: #2d5a8e;
            color: #2d5a8e;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(45, 90, 142, .15);
        }

        .index-wrapper .dt-buttons .btn i {
            margin-right: 4px;
        }

        /* DataTables wrapper spacing */
        .index-wrapper .dataTables_wrapper .dataTables_length,
        .index-wrapper .dataTables_wrapper .dataTables_filter {
            margin-bottom: 10px;
        }

        .index-wrapper .dataTables_wrapper .row:first-child {
            margin-bottom: 10px;
        }

        .index-wrapper .card-body {
            padding: 1.25rem;
        }

        .dt-button-collection {
            background: white !important;
            border: 1px solid #d0d8e0 !important;
            border-radius: 5px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            padding: 5px 0 !important;
            width: auto !important;
            max-width: none !important;
        }

        .dt-button-collection button {
            background: white !important;
            color: #4a5568 !important;
            border: none !important;
            padding: 8px 15px !important;
            text-align: left !important;
            font-size: 0.85rem !important;
            width: 100% !important;
            display: block !important;
            transition: all 0.2s !important;
        }

        .dt-button-collection button:hover {
            background: #f0f4f8 !important;
            color: #2d5a8e !important;
        }

        .dt-button-collection button.active {
            background: #e0f2fe !important;
            color: #0369a1 !important;
        }
    </style>

    <div class="index-wrapper">
        <div class="card">
            <div class="index-header">
                <h4><i class="fas fa-users"></i>{{$config_data->module_name}}</h4>
                <div>
                    <a href="{{ route($config_data->module_route.'.bulkcreate') }}" class="btn-bulk">
                        <i class="fas fa-upload me-1"></i> Bulk Upload
                    </a>
                    <a href="{{ route("$config_data->module_route.create", "") }}" class="btn-create">
                        <i class="fas fa-plus me-1"></i> Create New
                    </a>
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
                { data: 'designations.name', searchable: true },
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