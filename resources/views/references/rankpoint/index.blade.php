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
                    <th>Rank</th>
                    <th>
                        <div class="d-flex justify-content-between align-items-center">
                        <span>Type</span>
                        <span>Points</span>
                        </div>
                    </th>
                    <th>Action</th>
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
    let canView = @json(auth()->user()->can($config_data->module_perm_name.'_show', App\Models\Rankpoint::class));
    let canUpdate = @json(auth()->user()->can($config_data->module_perm_name.'_edit', App\Models\Rankpoint::class));
    let canDelete = @json(auth()->user()->can($config_data->module_perm_name.'_delete', App\Models\Rankpoint::class));
    let url_route = "{{ $config_data->module_route }}";

        $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route("$config_data->module_route.list") }}",
            order: [[0, 'asc']], // default ordering
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
                { data: 'ranks.code' },
                {
                    data: null,
                    name: 'name',
                    render: function (data, type, row) {
                        return `
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fw-bold">${row.name ?? ''}</span>
                                <span class="badge bg-primary">${row.points ?? ''}</span>
                            </div>
                        `;
                    }
                },
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