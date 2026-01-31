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
            <input type="text" class="form-control" name="pm_code">
          </div>
          <div class="col-md-3">
            <label>Rank</label>
            <input type="text" class="form-control" disabled>
          </div>
          <div class="col-md-3">
            <label>Name</label>
            <input type="text" class="form-control" disabled>
          </div>
          <div class="col-md-3">
            <label>AFPOS</label>
            <input type="text" class="form-control" disabled>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-3">
            <label>AFPSN</label>
            <input type="text" class="form-control" disabled>
          </div>
          <div class="col-md-3">
            <label>Sex</label>
            <input type="text" class="form-control" disabled>
          </div>
          <div class="col-md-3">
            <label>DOB</label>
            <input type="text" class="form-control" disabled>
          </div>
          <div class="col-md-3">
            <label>Date Ret</label>
            <input type="text" class="form-control" disabled>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-3">
            <label>DOR</label>
            <input type="text" class="form-control" disabled>
          </div>
          <div class="col-md-3">
            <label>SOC</label>
            <input type="text" class="form-control" disabled>
          </div>
          <div class="col-md-3">
            <label>Type</label>
            <input type="text" class="form-control" disabled>
          </div>

          <div class="col-md-3">
            <label>SIG</label>
            <input type="text" class="form-control" disabled>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-md-3">
            <label>Current Designation</label>
            <input type="text" class="form-control" disabled>
          </div>
          <div class="col-md-3">
            <label>Current Unit</label>
            <input type="text" class="form-control" disabled>
          </div>
        </div>
        <hr>

        <div class="row mt-3">
          <div class="col-md-3">
            <label>Entry</label>
            <input type="text" class="form-control" name="entry" >
          </div>
          <div class="col-md-3">
            <label>Type</label>
            <input type="text" class="form-control" name="type">
          </div>
          <div class="col-md-3">
            <label>Date</label>
            <input type="date" class="form-control" name="date">
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-3">
            <label>GO #</label>
            <input type="text" class="form-control" name="go_number">
          </div>
          <div class="col-md-3">
            <label>Rank</label>
            <input type="text" class="form-control" name="rank">
          </div>
          <div class="col-md-3">
            <label>Points</label>
            <input type="text" class="form-control" name="points">
          </div>
        </div>
        
         <input type="submit" class="btn btn-primary" name="action" value="Save">

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