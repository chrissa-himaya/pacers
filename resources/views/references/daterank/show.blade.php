@extends('layouts.app')
@section('content')

<div class="card">
    <div class="card-header">
        @if($data_items['operation_type']=="show")
            <h4 class="d-inline">{{$config_data->module_name}} | VIEW</h4>
        @elseif($data_items['operation_type']=="edit")
            <h4 class="d-inline">{{$config_data->module_name}} | EDIT</h4>
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
                                    @if($key == 'pm_code')
                                        @if($data_items["operation_type"] !== "show")
                                            <select name="pm_code" id="pm_code" class="form-control select2" required>
                                                <option value="">-- Select PM Code --</option>
                                                @foreach(($data_items['pm_codes'] ?? []) as $pmcode)
                                                    <option value="{{ $pmcode }}" 
                                                        {{ old('pm_code', $value ?? '') == $pmcode ? 'selected' : '' }}>
                                                        {{ $pmcode }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text" class="form-control" 
                                                value="{{ $value }}" disabled>
                                        @endif

                                    @elseif($key == 'rank_id')
                                        @if($data_items["operation_type"] !== "show")
                                            <select name="rank_id" id="rank_id" class="form-control select2" required>
                                                <option value="">-- Select Rank --</option>
                                                @foreach($data_items['ranks'] as $id => $type)
                                                    <option value="{{ $id }}"
                                                        {{ old('rank_id', $value ?? '') == $id ? 'selected' : '' }}>
                                                        {{ $type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif

                                    @elseif($key == 'date')
                                        @php
                                            $dateValue = $data_items['data']->date;
                                            if ($dateValue) {
                                                // Handle both Carbon instance and string
                                                if (is_string($dateValue)) {
                                                    $dateValue = \Carbon\Carbon::parse($dateValue);
                                                }
                                            }
                                        @endphp
                                        
                                        @if($data_items["operation_type"] !== "show")
                                            <input type="date" id="date" name="date" class="form-control" required
                                                value="{{ old('date', $dateValue ? $dateValue->format('Y-m-d') : '') }}">
                                        @else
                                            <input type="text" class="form-control" 
                                                   value="{{ $dateValue ? $dateValue->format('d-M-Y') : '' }}" 
                                                   disabled>
                                        @endif

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
                                    value="{{ $data_items['operation_type'] === 'create' ? 'Create' : 'Update' }}"
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
        $(document).ready(function() {
            if ($.fn.select2) {
                $('.select2').select2({
                    theme: 'bootstrap4',
                    width: '100%'
                });
            }
        });
    </script>
@endsection