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
                        <th>Action</th>
                    </tr>
                    <tr class="filter-row">
                        <th></th> <!-- Nr column usually no filter -->
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
    let canView = @json(auth()->user()->can($config_data->module_perm_name.'_show', App\Models\Officer::class));
    let canUpdate = @json(auth()->user()->can($config_data->module_perm_name.'_edit', App\Models\Officer::class));
    let canDelete = @json(auth()->user()->can($config_data->module_perm_name.'_delete', App\Models\Officer::class));
    let url_route = "{{ $config_data->module_route }}";
    
        const table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,     // disable ordering UI
            order: [],           // remove default order           
            ajax: "{{ route("$config_data->module_route.list") }}",
            scrollX: true,
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
                { data: 'SRTY', searchable: true },
                { data: 'PM_CODE', searchable: true },
                { data: 'NAME', searchable: true },
                { data: 'SUFFIX', searchable: true},
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
                { data: 'DESIGNATION', searchable: true },
                { data: 'UNIT', searchable: true },
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


        // ✅ Add per-column search inputs AFTER DataTable is created
        $('#dataTable thead th').each(function (i) {

            // skip "Nr" (0) and "Actions" (last column)
            if (i === 0 || i === 21) return;

            // put an input under the header text
            $(this).append('<br><input type="text" placeholder="Search" style="width: 100%;">');
        });

        // ✅ Bind search event per column
// ✅ Bind search event per column (DEBOUNCED)
table.columns().every(function (i) {
  if (i === 0 || i === 21) return;

  let timer = null;
  const column = this;

  $('input', this.header()).on('input change clear', function () {
    const value = this.value;

    clearTimeout(timer);

    timer = setTimeout(function () {
      column.search(value).draw();
    }, 500); // ⏳ wait 500ms after user stops typing
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