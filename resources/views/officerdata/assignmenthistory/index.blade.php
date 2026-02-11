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

        /* ── Header ───────────────────────────────────────────────── */
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
            color: #0a0a0a;
            border: 1.5px solid rgba(255, 255, 255, .35);
            border-radius: 8px;
            padding: 7px 20px;
            font-weight: 600;
            font-size: .85rem;
            transition: all .2s;
            text-decoration: none;
        }

        .btn-create:hover {
            background: rgba(255, 255, 255, .25);
            color: #aeb9bb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
        }

        /* ── Filter Row ───────────────────────────────────────────── */
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

        .index-wrapper thead tr.filter-row input::placeholder {
            color: #aab4c0;
            font-style: italic;
        }

        /* ── Table Styling ────────────────────────────────────────── */
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

        /* ── Numeric Columns ──────────────────────────────────────── */
        .index-wrapper .table tbody td.mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: .75rem;
            color: #2d6e3f;
        }

        /* ── Action buttons ───────────────────────────────────────── */
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

        .action-btns .btn-add {
            background: #dbeafe;
            color: #1e40af;
        }

        /* ── DataTables overrides ─────────────────────────────────── */
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

        .btn.btn-primary {
            background: #6eaef2;
            color: #0a0a0a;
            border: 1.5px solid rgba(255, 255, 255, .35);
            border-radius: 8px;
            font-weight: 600;
            font-size: .85rem;
            transition: all .2s;
            text-decoration: none;
        }
    </style>

    <div class="index-wrapper">
        <div class="card">
            <div class="index-header">
                <h4 class="module-header"><i class="fas fa-history"></i>{{$config_data->module_name}}</h4>
                <a href="{{ route("$config_data->module_route.create", "") }}" class="btn btn-primary float-end">
                    Create New
                </a>
            </div>

            <div class="card-body">
                <table id="dataTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>PM Code</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Designation</th>
                            <th>Sub unit</th>
                            <th>Unit</th>
                            <th>PAMU</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Assign Type</th>
                            <th>Geography</th>
                            <th>Rank</th>
                            <th>Years</th>
                            <th>Points</th>
                            <th>Action</th>
                        </tr>
                        <tr class="filter-row">
                            <th></th>
                            <th><input type="text" placeholder="Search PM Code" class="form-control form-control-sm" /></th>
                            <th><input type="date" class="form-control form-control-sm" /></th>
                            <th><input type="date" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Search Designation" class="form-control form-control-sm" />
                            </th>
                            <th><input type="text" placeholder="Search Sub unit" class="form-control form-control-sm" />
                            </th>
                            <th><input type="text" placeholder="Search Unit" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Search PAMU" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Search Category" class="form-control form-control-sm" />
                            </th>
                            <th><input type="text" placeholder="Search Type" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Search Assign Type" class="form-control form-control-sm" />
                            </th>
                            <th><input type="text" placeholder="Search Geography" class="form-control form-control-sm" />
                            </th>
                            <th><input type="text" placeholder="Search Rank" class="form-control form-control-sm" /></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let perm_name = "{{ $config_data->module_perm_name }}";
        let canView = @json(auth()->user()->can($config_data->module_perm_name . '_show', App\Models\AssignmentHistory::class));
        let canUpdate = @json(auth()->user()->can($config_data->module_perm_name . '_edit', App\Models\AssignmentHistory::class));
        let canDelete = @json(auth()->user()->can($config_data->module_perm_name . '_delete', App\Models\AssignmentHistory::class));
        let canCreate = @json(auth()->user()->can($config_data->module_perm_name . '_create', App\Models\AssignmentHistory::class));
        let url_route = "{{ $config_data->module_route }}";

        const table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            order: [],
            ajax: "{{ route("$config_data->module_route.list") }}",
            pageLength: 50,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            scrollX: true,
            columns: [
                {
                    data: null,
                    title: 'Nr',
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false,
                    searchable: false
                },
                { data: 'pm_code', searchable: true },
                {
                    data: 'start_date',
                    render: function (data, type, row) {
                        if (!data) return '';
                        let date = new Date(data);
                        let day = date.getDate();
                        let monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                        let month = monthNames[date.getMonth()];
                        let year = date.getFullYear();
                        return day + '-' + month + '-' + year;
                    }
                },
                {
                    data: 'end_date',
                    render: function (data, type, row) {
                        if (!data) return '';
                        let date = new Date(data);
                        let day = date.getDate();
                        let monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                        let month = monthNames[date.getMonth()];
                        let year = date.getFullYear();
                        return day + '-' + month + '-' + year;
                    }
                },
                { data: 'designations.name', searchable: true },
                { data: 'subunit', searchable: true },
                { data: 'units.name', searchable: true },
                { data: 'pamus.name', searchable: true },
                { data: 'assignments.name', searchable: true },
                {
                    data: 'pri_sec_spec',
                    render: function (data) {
                        const map = { primary: 'Primary', secondary: 'Secondary', special: 'Special' };
                        return map[data] ?? data ?? '';
                    }
                },
                {
                    data: 'assignment_type_name', // CHANGED
                    searchable: true,
                    render: function(data) {
                        return data || '';
                    }
                },
                {
                    data: 'geography',
                    render: function (data) {
                        const map = { ncr: 'NCR', luzon: 'Luzon', visayas: 'Visayas', mindanao: 'Mindanao', foreign: 'Foreign Duty' };
                        return map[data] ?? data ?? '';
                    }
                },
                { data: 'rank_during_completion', searchable: true },
                {
                    data: 'year_earned',
                    render: function (data) { return '<span class="mono">' + (data !== null && data !== undefined ? parseFloat(data).toFixed(6) : '') + '</span>'; },
                    className: 'mono'
                },
                {
                    data: 'computed_points',
                    render: function (data) { return '<span class="mono">' + (data !== null && data !== undefined ? parseFloat(data).toFixed(4) : '') + '</span>'; },
                    className: 'mono'
                },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        let btns = '<div class="action-btns">';
                        if (canView) btns += `<a href="/${url_route}/${data}" class="btn btn-view"><i class="fas fa-eye"></i> View</a>`;
                        if (canUpdate) btns += `<a href="/${url_route}/${data}/edit" class="btn btn-edit"><i class="fas fa-pen"></i> Edit</a>`;
                        if (canDelete) btns += `
                                    <form action="/${url_route}/${data}" method="POST" style="display:inline; margin:0;">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this record?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    </form>`;
                        if (canCreate) btns += `<a href="/${url_route}/${data}/add-entry" class="btn btn-add" title="Add new entry for same officer"><i class="fas fa-plus"></i> Add</a>`;
                        btns += '</div>';
                        return btns;
                    }
                }
            ],
        });

        $('#dataTable thead th').each(function (i) {
            if (i === 0 || i === 15) return;
            $(this).append('<br><input type="text" placeholder="Search" style="width: 100%;">');
        });

        table.columns().every(function (i) {
            if (i === 0 || i === 15) return;

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
    </script>
@endsection