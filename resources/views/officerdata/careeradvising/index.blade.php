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
        }

        .btn-create:hover {
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

        /* .btn.btn-primary {
            background: #6eaef2;
            color: #0a0a0a;
            border: 1.5px solid rgba(255, 255, 255, .35);
            border-radius: 8px;
            font-weight: 600;
            font-size: .85rem;
            transition: all .2s;
            text-decoration: none;
        } */

        .action-btns .btn-add {
            background: #dbeafe;
            color: #1e40af;
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
    </style>

    <div class="index-wrapper">
        <div class="card">
            <div class="index-header">
                <h4><i class="fas fa-comments"></i>{{ $config_data->module_name }}</h4>
                @can($config_data->module_perm_name . '_create')
                    <a href="{{ route("$config_data->module_route.create", "") }}" class="btn-create">
                        <i class="fas fa-plus me-1"></i> Create New
                    </a>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <table id="dataTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nr</th>
                        <th>PM Code</th>
                        <th>Date of Advise</th>
                        <th>Career Adviser</th>
                        <th>Mode of Communication</th>
                        <th>Venue</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                    <tr class="filter-row">
                        <th></th>
                        <th><input type="text" placeholder="PM Code" class="form-control form-control-sm" /></th>
                        <th><input type="date" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Adviser" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Mode" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Venue" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Remarks" class="form-control form-control-sm" /></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        let canView    = @json(auth()->user()->can($config_data->module_perm_name.'_show', App\Models\CareerAdvising::class));
        let canUpdate  = @json(auth()->user()->can($config_data->module_perm_name.'_edit', App\Models\CareerAdvising::class));
        let canDelete  = @json(auth()->user()->can($config_data->module_perm_name.'_delete', App\Models\CareerAdvising::class));
        let url_route  = "{{ $config_data->module_route }}";

        const table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            order: [[2, 'desc']], // Default sort by date
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
                    data: 'date_of_advise',
                    render: function (data) {
                        if (!data) return '';
                        let d = new Date(data);
                        let months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
                        return '<span class="date-badge">' + d.getDate() + '-' + months[d.getMonth()] + '-' + d.getFullYear() + '</span>';
                    }
                },
                { data: 'career_adviser', searchable: true, defaultContent: '-' },
                { data: 'mode_of_coms', searchable: true, defaultContent: '-' },
                { data: 'venue', searchable: true, defaultContent: '-' },
                { data: 'remarks', searchable: true, defaultContent: '-' },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        let btns = '<div class="action-btns">';
                        if (canView)   btns += `<a href="/${url_route}/${data}" class="btn btn-view"><i class="fas fa-eye"></i> View</a>`;
                        if (canUpdate) btns += `<a href="/${url_route}/${data}/edit" class="btn btn-edit"><i class="fas fa-pen"></i> Edit</a>`;
                        if (canDelete) btns += `
                            <form action="/${url_route}/${data}" method="POST" style="display:inline;margin:0;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-delete" onclick="return confirm('Delete this career advising record?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>`;
                        if (@json(auth()->user()->can($config_data->module_perm_name.'_create', App\Models\CareerAdvising::class))) {
                            btns += `<a href="/${url_route}/${data}/create-from-existing" class="btn btn-add" title="Add record for same officer"><i class="fas fa-plus"></i> Add</a>`;
                            // or use a smaller style if you want
                        }

                        btns += '</div>';
                        return btns;
                    }
                }
            ],
        });

        // Per-column search
        table.columns().every(function (i) {
            if (i === 0 || i === 7) return;
            let timer = null;
            const column = this;
            $('input, select', this.header()).on('input change', function () {
                const value = this.value;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    column.search(value).draw();
                }, 500);
            });
        });
    </script>
@endsection