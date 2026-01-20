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
        : route("$config_data->module_route.update", [$data_items['data']->id]) }}" 
        method="POST" 
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
                <input type="text" class="form-control" name="pm_code" value="{{ old('pm_code') }}" required>
                <input type="submit" class="btn btn-info" name="action" value="Fetch Data">
            </div>
        </div>
        </div>

        <div class="row mt-3">
          
        <div class="col-md-3">
            <label>AFPSN</label>
            <input type="text" class="form-control" value="{{ session('afpsn') }}" disabled>
          </div>
          <div class="col-md-3">
            <label>Rank</label>
            <input type="text" class="form-control" value="{{ session('rank') }}" disabled>
          </div>
          <div class="col-md-3">
            <label>Name</label>
            <input type="text" class="form-control" value="{{ session('name') }}" disabled>
          </div>
          <div class="col-md-3">
            <label>AFPOS</label>
            <input type="text" class="form-control" value="{{ session('afpos') }}" disabled>
          </div>
        </div>

        <div class="row mt-3"> 
          
          <!-- <div class="col-md-3">
            <label>Sex</label>
            <input type="text" class="form-control" name="sex" value="{{ session('sex') }}">
          </div> -->
          <div class="col-md-3">
            <label>DOB</label>
            <input type="text" class="form-control" value="{{ session('dob') }}" disabled>
          </div>
          <div class="col-md-3">
            <label>Date Ret</label>
            <input type="text" class="form-control" value="{{ session('date_ret') }}" disabled>
          </div>
          <div class="col-md-3">
            <label>DOR</label>
            <input type="text" class="form-control" value="{{ session(key: 'dor') }}" disabled>
          </div>
          <div class="col-md-3">
            <label>SOC</label>
            <input type="text" class="form-control" value="{{ session('soc') }}" disabled>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-md-3">
            <label>Type</label>
            <input type="text" class="form-control" value="{{ session('type') }}" disabled>
          </div>
          
          <div class="col-md-3">
            <label>TIG</label>
            <input type="text" class="form-control" value="{{ session('tig') }}" disabled>
          </div>
          
          <div class="col-md-3">
            <label>Designation</label>
            <input type="text" class="form-control" value="{{ session('designation') }}" disabled>
          </div>
          <div class="col-md-3">
            <label>Unit</label>
            <input type="text" class="form-control" value="{{ session('unit') }}" disabled>
          </div>
        </div>

        <hr>

        <!-- CAREER ADVISER INPUTS -->
        <div class="row">
            <div class="col-md-3">
                <label>Entry</label>
                <select name="schooling_entries_id" class="form-control select2">
                    <option value="">-- Select Entry --</option>
                    @foreach($data_items['schoolingentries'] as $id => $entry)
                        <option value="{{ $id }}" 
                            {{ old('schooling_entries_id', $data_items['data']->schooling_entries_id) == $id ? 'selected' : '' }}>
                            {{ $entry->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Assignment</label>
                <select name="assignment_id" class="form-control select2">
                    <option value="">-- Select Assignment --</option>
                    @foreach($data_items['assignments'] as $id => $assignment)
                        <option value="{{ $id }}" 
                            {{ old('assignment_id', $data_items['data']->assignment_id) == $id ? 'selected' : '' }}>
                            {{ $assignment->name }} - {{ $assignment->types->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Category</label>
                <input type="text" name="category" class="form-control" value="{{ old('category') }}">
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-3">
                <label>School Location</label>
                <select name="school_location" class="form-control">
                    <option value="Local" selected>Local</option>
                    <option value="Foreign">Foreign</option>
                </select>
            </div>

            <div class="col-md-3">
                <label>Date Completed</label>
                <input type="date" name="date_completed" class="form-control" value="{{ old('date_completed') }}">
            </div>

            <div class="col-md-3">
                <label>Rating</label>
                <input type="number" name="rating" class="form-control" value="{{ old('rating') }}">
            </div>

            <div class="col-md-3">
                <label>Standing</label>
                <input type="number" name="standing" class="form-control" value="{{ old('standing') }}">
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-3">
                <label>Total Students</label>
                <input type="number" name="total_students" class="form-control" value="{{ old('total_students') }}">
            </div>
            <div class="col-md-3">
                <input type="submit" class="form-control btn btn-primary mt-4" value="Save">
            </div>
        </div>

        <!-- AUTO-POPULATED -->
        <!-- <div class="form-section-title">Auto-Populated (Read-Only)</div> -->

        <div class="row">
          <div class="col-md-3">
            <label>Rank During Completion</label>
            <input type="text" class="form-control readonly" value="2LT" readonly>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-4">
            <label>2LT</label>
            <input type="text" class="form-control readonly" value="1.85" readonly>
          </div>
          <div class="col-md-4">
            <label>1LT</label>
            <input type="text" class="form-control readonly" value="1.85" readonly>
          </div>
          <div class="col-md-4">
            <label>CPT</label>
            <input type="text" class="form-control readonly" value="-" readonly>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-4">
            <label>MAJ</label>
            <input type="text" class="form-control readonly" value="-" readonly>
          </div>
          <div class="col-md-4">
            <label>LTC</label>
            <input type="text" class="form-control readonly" value="-" readonly>
          </div>
          <div class="col-md-4">
            <label>COL</label>
            <input type="text" class="form-control readonly" value="-" readonly>
          </div>
        </div>
        

        <div class="mt-4 text-right">
          <a href="schooling-data.html" class="btn btn-secondary">Cancel</a>
          <input type="submit" class="btn btn-primary" name="action" value="Save">
        </div>
    </form>    
</div>

</div>
@endsection
@section('scripts')
    <script>

    </script>
@endsection