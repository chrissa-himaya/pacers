@extends('layouts.app')
@section('content')

<div class="card">
    <div class="card-header">
        @if($data_items['operation_type']=="show")
            <h4 class="d-inline">{{$config_data->module_name}} | VIEW - {{$data_items["data"]->name}}</h4>
        @elseif($data_items['operation_type']=="edit")
            <h4 class="d-inline">{{$config_data->module_name}} | EDIT - {{$data_items["data"]->name}}</h4>
        @elseif($data_items['operation_type']=="create")
            <h4 class="d-inline">{{$config_data->module_name}} | {{$data_items["bulk_insert"]=="bulk" ? "BULK" : ""}} CREATE</h4>            
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
                                                    {{ $assignment->name }} - {{ $assignment->types->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                            @elseif($key=='assignments' && $data_items["operation_type"] === "show")
                                                <input type="text" class="form-control" value="{{$value['name']}} - {{$data_items['data']->assignments->types->name}}" disabled>                             
                                        </td>

                                        <td>
                                            @elseif($key=='rank_id')
                                                <select name="rank_id" id="rank_id" class="form-control select2">
                                                    <option value="">-- Select Rank --</option>
                                                    @foreach($data_items['ranks'] as $id => $rank)
                                                        <option value="{{ $id }}"
                                                            {{ old('rank_id', $data_items['data']->rank_id ?? null) == $id ? 'selected' : '' }}>
                                                            {{ $rank }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                            @elseif($key=='ranks' && $data_items["operation_type"] === "show")
                                                <input type="text" class="form-control" value="{{$value['name']}}" disabled>                             
                                        </td>

                                        <td>
                                            @elseif($key=='rankpoint_id')
                                                <select name="rankpoint_id" id="rankpoint_id" class="form-control select2">
                                                    <option value="">-- Select RankpointId --</option>
                                                    @foreach($data_items['rankpoints'] as $id => $rankpoint)
                                                        <option value="{{ $id }}"
                                                            {{ old('rankpoint_id', $data_items['data']->rankpoint_id ?? "") == $id ? 'selected' : '' }}>
                                                             {{ $rankpoint->name }} - {{ $rankpoint->points }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                            @elseif($key=='rankpoints' && $data_items["operation_type"] === "show")
                                                <input type="text" class="form-control" value="{{$value['name'] ." - ". $value['points']}}" disabled>  
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