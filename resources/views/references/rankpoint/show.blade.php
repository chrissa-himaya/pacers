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
    ? route("$config_data->module_route.store")
    : route("$config_data->module_route.update", [$data_items['data']->id]) }}" method="POST"
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
                                    @if($key=='rank_id')
                                        <select name="rank_id" id="rank_id" class="form-control select2">
                                            <option value="">-- Select Rank --</option>
                                            @foreach($data_items['ranks'] as $id => $rank)
                                                <option value="{{ $id }}"
                                                    {{ old('rank_id', $data_items['data']->rank_id ?? '') == $id ? 'selected' : '' }}>
                                                    {{ $rank }}
                                                </option>
                                            @endforeach
                                        </select>

                                    @elseif($key=='ranks' && $data_items["operation_type"] === "show")
                                        <input type="text" class="form-control" value="{{$value['name']}}" disabled>

                                    @elseif($key == 'name')
                                        @php
                                            $selectedName = $data_items['operation_type'] === 'create'
                                                ? old('name', '')
                                                : old('name', $data_items['data']->name ?? '');
                                        @endphp

                                        <select name="name" class="form-control select2">
                                            <option value="">-- Select Type --</option>
                                            <option value="min_month" {{ $selectedName == 'min_month' ? 'selected' : '' }}>Min Month</option>
                                            <option value="min_point" {{ $selectedName == 'min_point' ? 'selected' : '' }}>Min Point</option>
                                            <option value="max_month" {{ $selectedName == 'max_month' ? 'selected' : '' }}>Max Month</option>
                                            <option value="max_point" {{ $selectedName == 'max_point' ? 'selected' : '' }}>Max Point</option>
                                            <option value="factor1" {{ $selectedName == 'factor1' ? 'selected' : '' }}>Factor1</option>
                                            <option value="factor2" {{ $selectedName == 'factor2' ? 'selected' : '' }}>Factor2</option>
                                            <option value="maxpt" {{ $selectedName == 'maxpt' ? 'selected' : '' }}>Prof. Max point</option>
                                            <option value="foreignpt" {{ $selectedName == 'foreignpt' ? 'selected' : '' }}>Foreign point</option>
                                        </select>

                                    @else
                                        <input
                                            type="text"
                                            class="form-control"
                                            value="{{ $data_items["operation_type"] == "create" ? "" : $value }}"
                                            name="{{ $key }}"
                                            @disabled($data_items["operation_type"] === "show")
                                            @if(isset($data_items['required_fields']) && in_array($key, $data_items['required_fields'])) required @endif
                                        >  
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