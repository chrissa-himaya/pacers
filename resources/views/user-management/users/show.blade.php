@extends('layouts.app')
@section('content')

<div class="card">
    <div class="card-header">
        @if($data_items['operation_type']=="show")
            <h4 class="d-inline">{{$config_data->module_name}} | PROFILE - {{$data_items["data"]->name}}</h4>
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
                                    <input
                                        type="text"
                                        class="form-control"
                                        value="{{ $data_items["operation_type"] == "create" ? "" : $value }}"
                                        name="{{ $key }}"
                                        @disabled($data_items["operation_type"] === "show")
                                        @if(isset($data_items['required_fields']) && in_array($key, $data_items['required_fields'])) required @endif
                                    >                                
                                </td>
                            </tr>
                        @endif
                    @endforeach

                        <tr>
                            <th>
                                Roles</br>

                            </th>
                            <td>
                                @unless($data_items['operation_type'] === 'show')
                                    <span class="btn btn-info btn-sm select-all">{{ "Select all" }}</span>
                                    <span class="btn btn-info btn-sm deselect-all">{{ "Deselect all" }}</span>
                                @endunless
                                <select name="roles[]" id="selectItem" class="form-control select2" multiple="multiple">
                                    @foreach($data_items["roles"] as $id => $roleName)
                                        <option value="{{ $id }}"
                                            {{ $data_items['user']->roles->contains($id) ? 'selected' : '' }}>
                                            {{ $roleName }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>


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