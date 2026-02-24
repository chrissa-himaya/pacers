@extends('layouts.app')
@section('content')

<div class="card">
    <div class="card-header">
        @if($data_items['operation_type']=="show")
            <h4 class="d-inline">{{$config_data->module_name}} | VIEW </h4>     
        @endif
        <a href="{{ route("$config_data->module_route.index") }}" class="btn btn-secondary float-end">
            Back
        </a>

    </div>

    <div class="card-body">
            <table class="table table-bordered">
                <tbody>
                    @foreach($data_items['data']->toArray() as $key => $value)
                        @if(!in_array($key, $data_items['column_hidden']))
                            <tr>
                                <th style="width: 150px;">
                                    {{ ucfirst(str_replace('_', ' ', $key)) }}
                                </th>
                                <td>
                                    @if($key=="user")
                                    <input type="text" class="form-control" value="{{ $value['name'] }}" disabled>
                                    @else
                                        <input
                                            type="text"
                                            class="form-control"
                                            value="{{ ($key=='user') ? $value['name'] : $value }}"
                                            @disabled($data_items["operation_type"] === "show")
                                        >  
                                    @endif                              
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    
                </tbody>
            </table>
    </div>

</div>
@endsection
@section('scripts')
<script>
</script>
@endsection