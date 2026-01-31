@extends('layouts.app')
@section('content')

  <div class="card">
    <div class="card-header">
      @if($data_items['operation_type'] == "show")
        <h4 class="d-inline">{{$config_data->module_name}} | VIEW - {{$data_items["data"]->name}}</h4>
      @elseif($data_items['operation_type'] == "edit")
        <h4 class="d-inline">{{$config_data->module_name}} | EDIT - {{$data_items["data"]->name}}</h4>
      @elseif($data_items['operation_type'] == "create")
        <h4 class="d-inline">{{$config_data->module_name}} | CREATE</h4>
      @endif
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
        <!-- OFFICER INFORMATION -->
        <!-- <div class="form-section-title">Officer Information (Read-Only)</div> -->
        <div class="row">
          <div class="col-md-3">
            <label>PM Code</label>
            <div class="input-group">
              @php
                $key = 'pm_code';
                $current = old('pm_code', $data_items['data']->pm_code ?? '');
              @endphp

              @if($data_items["operation_type"] !== "show")
                <select name="pm_code" id="pm_code" class="form-control select2">
                  <option value="">-- Select PM Code --</option>

                  @foreach(($data_items['pm_codes'] ?? []) as $pmcode)
                    <option value="{{ $pmcode }}" {{ $current == $pmcode ? 'selected' : '' }}>
                      {{ $pmcode }}
                    </option>
                  @endforeach
                </select>

                <button type="submit" class="btn btn-info" name="action" value="Fetch Data">
                  Fetch Data
                </button>
              @else
                <input type="text" class="form-control" value="{{ $current }}" disabled>
              @endif
            </div>
          </div>
          <div class="col-md-3">
            <label>Rank</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->RANK : session('rank')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>Name</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->NAME : session('name')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>AFPOS</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->AFPOS : session('afpos')}}" disabled>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-3">
            <label>AFPSN</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->AFPSN : session('afpsn')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>Sex</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->SEX : session('sex')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>DOB</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->DOB : session('dob')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>Date Ret</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->RET : session('date_ret')}}" disabled>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-3">
            <label>DOR</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->DOR : session('dor')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>SOC</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->SOC : session('soc')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>Type</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->TYPE : session('type')}}" disabled>
          </div>

          <div class="col-md-3">
            <label>SIG</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->SIG : session('sig')}}" disabled>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-md-3">
            <label>Current Designation</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->DESIGNATION : session('designation')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>Current Unit</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->UNIT : session('unit')}}" disabled>
          </div>
        </div>
        <hr>

        <div class="row mt-3">
          <div class="col-md-3">
            <label>Entry</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->DOR : session('dor')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>Unit</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->SOC : session('soc')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>PAMU</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->TYPE : session('type')}}" disabled>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-3">
            <label>Category</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->SIG : session('sig')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>Primary/Secondary</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->DOR : session('dor')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>Assignment Type</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->SOC : session('soc')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>Geography</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->TYPE : session('type')}}" disabled>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-3">
            <label>Start date</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->SIG : session('sig')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>End date</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->SOC : session('soc')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>Rank During Completion</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->TYPE : session('type')}}" disabled>
          </div>

          <div class="col-md-3">
            <label>Year</label>
            <input type="text" class="form-control" value="{{isset($data_items['officerData']) ? $data_items['officerData']->SIG : session('sig')}}" disabled>
          </div>
        </div>
        
          <a href="{{ route("$config_data->module_route.index") }}" class="btn btn-secondary">
            Back
        </a>
        </div>
      </form>
    </div>

  </div>
@endsection
@section('scripts')
  <script>

  </script>
@endsection