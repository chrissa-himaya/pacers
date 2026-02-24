@extends('layouts.app')
@section('content')

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
            <table id="dataTable" class="table table-bordered" style="width: 100% !important;">
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