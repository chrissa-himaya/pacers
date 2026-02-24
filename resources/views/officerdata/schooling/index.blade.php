@extends('layouts.app')
@section('content')

    <div class="index-wrapper">
        <div class="card">
            <div class="index-header">
                <h4><i class="fas fa-graduation-cap"></i>{{ $config_data->module_name }}</h4>
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