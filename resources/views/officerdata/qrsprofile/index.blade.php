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
        <div class="table-responsive">
            <table id="dataTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Nr</th>
                        <th>SRTY</th>
                        <th>PM CODE</th>
                        <th>NAME</th>
                        <th>RANK</th>
                        <th>AFPSN</th>
                        <th>AFPOS</th>
                        <th>SIG</th>
                        <th>DOR</th>
                        <th>TACS</th>
                        <th>DOC</th>
                        <th>DOB</th>
                        <th>RET</th>
                        <th>HCC</th>
                        <th>SOC</th>
                        <th>REMARKS</th>
                        <th>LAST NAME</th>
                        <th>FIRST NAME</th>
                        <th>MID INITIAL</th>
                        <th>SUFFIX</th>
                        <th>Action</th>
                    </tr>
                    <tr class="filter-row">
                        <th></th> <!-- Nr column usually no filter -->
                        <th><input type="text" placeholder="Search SRTY" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search PM CODE" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search NAME" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search RANK" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search AFPSN" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search AFPOS" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search SIG" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search DOR" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search TACS" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search DOC" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search DOB" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search RET" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search HCC" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search SOC" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search REMARKS" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search LAST NAME" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search FIRST NAME" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search MID INITIAL" class="form-control form-control-sm" /></th>
                        <th><input type="text" placeholder="Search SUFFIX" class="form-control form-control-sm" /></th>
                        <th></th> <!-- Action column no filter -->
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
    let canView = @json(auth()->user()->can($config_data->module_perm_name.'_show', App\Models\Rank::class));
    let canUpdate = @json(auth()->user()->can($config_data->module_perm_name.'_edit', App\Models\Rank::class));
    let canDelete = @json(auth()->user()->can($config_data->module_perm_name.'_delete', App\Models\Rank::class));
    let url_route = "{{ $config_data->module_route }}";

        $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route("$config_data->module_route.list") }}",
            scrollX: true,
            order: [[0, 'asc']], // default ordering
            columns: [
                {
                    data: null,
                    title: 'Nr',
                    orderable: false, 
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false,
                    searchable: false
                },
                { data: 'SRTY' },
                { data: 'PM_CODE' },
                { data: 'NAME' },
                { data: 'RANK' },
                { data: 'AFPSN' },
                { data: 'AFPOS' },
                { data: 'SIG' },
                { data: 'DOR' },
                { data: 'TACS' },
                { data: 'DOC' },
                { data: 'DOB' },
                { data: 'RET' },
                { data: 'HCC' },
                { data: 'SOC' },
                { data: 'REMARKS' },
                { data: 'LAST_NAME' },
                { data: 'FIRST_NAME' },
                { data: 'MID_INITIAL' },
                { data: 'SUFFIX' },
                {
                    data: 'id',
                    orderable: false, 
                    searchable: false,
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
        $('#dataTable thead tr.filter-row input').on('keyup change', function() {
            table.draw();
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