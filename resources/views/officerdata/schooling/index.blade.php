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

        /* ── Date Filter Custom ───────────────────────────────────── */
        .date-filter-input {
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .module-header{
            padding-right: 15px;
            padding-left: 15px;
            padding-top: 10px;
        }

        .btn.btn-primary{
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
            <div class="index-header"></div>
            <h4 class="module-header"><i class="fas fa-graduation-cap"></i>{{$config_data->module_name}}
                <a href="{{ route("$config_data->module_route.create", "") }}" class="btn btn-primary float-right">
                    Create New
                </a>
            </h4>
        </div>
        <div class="card-body">
            <table id="dataTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>PMCode</th>
                        <th>Entry</th>
                        <th>Class</th>
                        <th>Unit</th>
                        <th>Assignments</th>
                        <th>Completed</th>
                        <th>Rating</th>
                        <th>Standing</th>
                        <th>Total</th>
                        <th>Rank</th>
                        <th>2LT</th>
                        <th>1LT</th>
                        <th>CPT</th>
                        <th>MAJ</th>
                        <th>LTC</th>
                        <th>COL</th>
                        <th>Action</th>
                    </tr>
                    <tr class="filter-row">
                        <th></th>
                        <th><input type="text" placeholder="Search PM Code" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search Entry" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search Class" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search School/Unit" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search Assignments" class="form-control form-control-sm" /></th>
                        <th><input type="date" placeholder="Search Date Completed" class="form-control form-control-sm" />
                        </th>
                        <th><input type="text" placeholder="Search Rating" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search Standing" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search Total Student" class="form-control form-control-sm" />
                        </th>
                        <th><input type="text" placeholder="Search Rank during completion"
                                class="form-control form-control-sm" /></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
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
        let canView = @json(auth()->user()->can($config_data->module_perm_name . '_show', App\Models\Schooling::class));
        let canUpdate = @json(auth()->user()->can($config_data->module_perm_name . '_edit', App\Models\Schooling::class));
        let canDelete = @json(auth()->user()->can($config_data->module_perm_name . '_delete', App\Models\Schooling::class));
        let canCreate = @json(auth()->user()->can($config_data->module_perm_name . '_create', App\Models\Schooling::class));
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
                { data: 'schoolingnames.name', searchable: true },
                { data: 'classname', searchable: true },
                { data: 'schoolingunits.name', searchable: true },
                { data: 'assignments.name', searchable: true },
                // { data: 'date_completed', searchable: true },
                {
                    data: 'date_completed',
                    render: function (data, type, row) {
                        if (!data) return '';

                        // Format: 25-Mar-2013
                        let date = new Date(data);
                        let day = date.getDate();
                        let monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                        let month = monthNames[date.getMonth()];
                        let year = date.getFullYear();

                        return day + '-' + month + '-' + year;
                    }
                },
                { data: 'rating', searchable: true },
                { data: 'standing', searchable: true },
                { data: 'total_student', searchable: true },
                { data: 'rank_during_completion', searchable: true },
                {
                    data: 'seclt',
                    render: function (data) { return '<span class="mono">' + (data ?? '') + '</span>'; },
                    className: 'mono'
                },
                {
                    data: 'firstlt',
                    render: function (data) { return '<span class="mono">' + (data ?? '') + '</span>'; },
                    className: 'mono'
                },
                {
                    data: 'cpt',
                    render: function (data) { return '<span class="mono">' + (data ?? '') + '</span>'; },
                    className: 'mono'
                },
                {
                    data: 'maj',
                    render: function (data) { return '<span class="mono">' + (data ?? '') + '</span>'; },
                    className: 'mono'
                },
                {
                    data: 'ltc',
                    render: function (data) { return '<span class="mono">' + (data ?? '') + '</span>'; },
                    className: 'mono'
                },
                {
                    data: 'col',
                    render: function (data) { return '<span class="mono">' + (data ?? '') + '</span>'; },
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

            if (i === 0 || i === 17) return;
            $(this).append('<br><input type="text" placeholder="Search" style="width: 100%;">');
        });


        table.columns().every(function (i) {

            if (i === 0 || i === 17) return;

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