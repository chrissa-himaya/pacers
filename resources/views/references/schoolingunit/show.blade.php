@extends('layouts.app')
@section('content')

    <div class="card">
        <div class="card-header">
            @if($data_items['operation_type'] == "show")
                <h4 class="d-inline">{{$config_data->module_name}} | VIEW - {{$data_items["data"]->name}}</h4>
            @elseif($data_items['operation_type'] == "edit")
                <h4 class="d-inline">{{$config_data->module_name}} | EDIT - {{$data_items["data"]->name}}</h4>
            @elseif($data_items['operation_type'] == "create")
                <h4 class="d-inline">{{$config_data->module_name}} | {{$data_items["bulk_insert"] == "bulk" ? "BULK" : ""}}
                    CREATE
                </h4>
            @endif
            <a href="{{ route("$config_data->module_route.index") }}" class="btn btn-secondary float-end">
                Back
            </a>

        </div>

        <div class="card-body">
            <form action="{{ $data_items['operation_type'] === 'create'
        ? $data_items["bulk_insert"] == "bulk" ? route("$config_data->module_route.bulkstore") : route("$config_data->module_route.store")
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
                                        @if($key === 'location')
                                            @php
                                                $current = old('location', $data_items['operation_type'] === 'create' ? '' : ($value ?? ''));
                                            @endphp


                                            <select name="location" class="form-control"
                                                @disabled($data_items['operation_type'] === 'show')
                                                @if(isset($data_items['required_fields']) && in_array($key, $data_items['required_fields'])) required @endif>
                                                <option value="">-- Select Location --</option>
                                                <option value="local" @selected($current === 'local')>Local</option>
                                                <option value="foreign" @selected($current === 'foreign')>Foreign</option>
                                            </select>
                                        @else
                                            <input type="text" class="form-control"
                                                value="{{ old($key, $data_items['operation_type'] == 'create' ? '' : $value) }}"
                                                name="{{ $key }}" @disabled($data_items['operation_type'] === 'show')
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
                                    <input type="submit"
                                        class="form-control btn {{ $data_items['operation_type'] === 'create' ? 'btn-primary' : 'btn-warning' }}">
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