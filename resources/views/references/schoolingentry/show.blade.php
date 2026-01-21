@extends('layouts.app')
@section('content')

<div class="card">
    <div class="card-header">
        @if($data_items['operation_type']=="show")
            <h4 class="d-inline">{{$config_data->module_name}} | VIEW - {{$data_items["data"]->name}}</h4>
        @elseif($data_items['operation_type']=="edit")
            <h4 class="d-inline">{{$config_data->module_name}} | EDIT - {{$data_items["data"]->name}}</h4>
        @elseif($data_items['operation_type']=="create")
            <h4 class="d-inline">{{$config_data->module_name}} | CREATE</h4>            
        @endif
        <a href="{{ route("$config_data->module_route.index") }}" class="btn btn-secondary float-end">
            Back
        </a>

    </div>

    <div class="card-body">
        <form action="{{ $data_items['operation_type'] === 'create' 
        ? $data_items["bulk_insert"]=="bulk" ? route("$config_data->module_route.bulkstore") : route("$config_data->module_route.store") 
        : route("$config_data->module_route.update", [$data_items['data']->id]) }}" 
        method="POST" 
        enctype="multipart/form-data">
        @csrf
        @if($data_items["operation_type"] == "edit")
            @method('PUT')
        @endif
            <table class="table table-bordered">
                <tbody>
                    @foreach($data_items['data']->toArray() as $key => $value)
                        @if(!in_array($key, $data_items['column_hidden']))
                            <tr>
                                <th style="width: 150px;">
                                    {{ $data_items['column_labels'][$key] ?? ucfirst(str_replace('_', ' ', $key)) }}
                                </th>
                                <td>
                                    @if($key=='assignment_id')
                                        <select name="assignment_id" id="assignment_id" class="form-control select2">
                                            <option value="">-- Select Assignment --</option>
                                            @foreach($data_items['assignments'] as $id => $assignment)
                                                <option value="{{ $id }}"
                                                    {{ old('assignment_id', $data_items['data']->assignment_id ?? '') == $id ? 'selected' : '' }}>
                                                    {{ $assignment}}
                                                </option>
                                            @endforeach
                                        </select>

                                            @elseif($key=='assignments' && $data_items["operation_type"] === "show")
                                                <input type="text" class="form-control" value="{{$value['name']}}" disabled>                             
                                        </td>

                                        <td>
                                            @elseif($key=='schooling_unit_id')
                                                <select name="schooling_unit_id" id="schooling_unit_id" class="form-control select2">
                                                    <option value="">-- Select Schooling Unit --</option>
                                                    @foreach($data_items['schoolingunits'] as $id => $schoolingunit) 
                                                        <option value="{{ $id }}"
                                                            {{ old('schooling_unit_id', $data_items['data']->schooling_unit_id ?? null) == $id ? 'selected' : '' }}>
                                                            {{ $schoolingunit }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                            @elseif($key=='schoolingunits' && $data_items["operation_type"] === "show")
                                                <input type="text" class="form-control" value="{{$value['name']}}" disabled>                             
                                          
                                            @else
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    value="{{ $data_items["operation_type"] == "create" ? "" : $value }}"
                                                    name="{{ $key }}"
                                                @disabled($data_items["operation_type"] === "show")
                                            @if(isset($data_items['required_fields']) && in_array($key, $data_items['required_fields'])) required @endif>  
                                    @endif                              
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    @unless($data_items['operation_type'] === 'show')
                        <tr>
                            <th>Action</th>
                            <td>
                                <input 
                                    type="submit" 
                                    class="form-control btn {{ $data_items['operation_type'] === 'create' ? 'btn-primary' : 'btn-warning' }}"
                                >
                            </td>
                        </tr>
                    @endunless
                </tbody>
            </table>
        </form>    
    </div>

</div>
@endsection
@section('scripts')
    <script>

    </script>
@endsection