@extends('layouts.app')
@section('content')

<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');
  .sf{font-family:'DM Sans',sans-serif}.sf .card{border:none;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden}
  .sf-hdr{background:linear-gradient(135deg,#1e3a5f,#2d5a8e);color:#fff;padding:10px 18px;display:flex;align-items:center;justify-content:space-between}
  .sf-hdr h4{margin:0;font-weight:700;font-size:1.05rem;letter-spacing:.3px}
  .sf-hdr .badge-op{display:inline-block;padding:2px 10px;border-radius:20px;font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-left:8px}
  .badge-op.view{background:rgba(255,255,255,.2)}.badge-op.edit{background:#f0ad4e;color:#3d2e00}.badge-op.create{background:#5cb85c;color:#fff}
  .sf-legend{display:flex;gap:16px;padding:5px 18px;font-size:.68rem;font-weight:500;color:#777;border-bottom:1px solid #eee;background:#fcfcfd}
  .sf-legend .li{display:flex;align-items:center;gap:4px}.sf-legend .sw{width:10px;height:10px;border-radius:2px;border:1px solid rgba(0,0,0,.08)}
  .sw-ro{background:#a4a7ab}.sw-ed{background:#fff7d5}.sw-au{background:#daf2e0}
  .sb{border-radius:7px;padding:8px 12px 6px;margin-bottom:6px;position:relative}
  .sb::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;border-radius:7px 0 0 7px}
  .sb-ro{background:#f0f4f8;border:1px solid #d6e0eb}.sb-ro::before{background:#7b9bc0}
  .sb-ed{background:#fffdf5;border:1px solid #efe5c7}.sb-ed::before{background:#d4a843}
  .sb-au{background:#f2faf4;border:1px solid #c8e6ce}.sb-au::before{background:#5ba96e}
  .sb-lbl{font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;display:flex;align-items:center;gap:5px}
  .sb-lbl .dot{width:5px;height:5px;border-radius:50%;display:inline-block}
  .sb-ro .sb-lbl{color:#5a7a9b}.sb-ro .dot{background:#7b9bc0}
  .sb-ed .sb-lbl{color:#9a7d2e}.sb-ed .dot{background:#d4a843}
  .sb-au .sb-lbl{color:#3d7a4f}.sb-au .dot{background:#5ba96e}
  .sf-steps{display:flex;margin-bottom:6px}
  .sf-step{flex:1;text-align:center;padding:4px;font-size:.6rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#aaa;background:#f0f0f0;border-bottom:2px solid #ddd}
  .sf-step.on{color:#2d5a8e;background:#e8f0fa;border-bottom-color:#2d5a8e}
  .sf-step .sn{display:inline-flex;width:14px;height:14px;border-radius:50%;background:#ccc;color:#fff;font-size:.55rem;align-items:center;justify-content:center;margin-right:2px}
  .sf-step.on .sn{background:#2d5a8e}.sf-step:first-child{border-radius:5px 0 0 0}.sf-step:last-child{border-radius:0 5px 0 0}
  .sf label{font-size:.7rem;font-weight:600;color:#444;margin-bottom:0;line-height:1.1}
  .sf .form-control,.sf .form-control-sm{font-size:.76rem;border-radius:4px;padding:3px 6px;height:28px}
  .sf .form-control:focus{border-color:#2d5a8e;box-shadow:0 0 0 2px rgba(45,90,142,.1)}
  .sf .form-control[disabled],.sf .form-control[readonly]{background-color:#e9eef3;color:#555;border-color:#d0d8e0;cursor:default}
  .sb-au .form-control[readonly]{background-color:#e3f2e7;border-color:#b0d9b8;color:#2d6e3f;font-family:'JetBrains Mono',monospace;font-size:.73rem}
  .sf select.form-control{height:28px;padding:1px 6px}
  .btn-fetch{background:linear-gradient(135deg,#2d5a8e,#3a7bd5);color:#fff;border:none;border-radius:4px;padding:3px 12px;font-size:.76rem;font-weight:600;height:28px;white-space:nowrap}
  .btn-fetch:hover{box-shadow:0 3px 10px rgba(45,90,142,.3);color:#fff}
  .sf-actions{display:flex;justify-content:flex-end;gap:8px;background:#dce0e5;border-top:1px solid #e9ecef;border-radius:0 0 10px 10px}
  .btn-save{background:linear-gradient(135deg,#2d8a4e,#3db562);color:#fff;border:none;border-radius:5px;padding:5px 20px;font-weight:600;font-size:.8rem;height: 35px; margin-top: 4px;}.btn-save:hover{box-shadow:0 3px 10px rgba(45,138,78,.3);color:#fff}
  .btn-update{background:linear-gradient(135deg,#c77c0a,#e6a21a);color:#fff;border:none;border-radius:5px;padding:5px 20px;font-weight:600;font-size:.8rem;height: 35px; margin-top: 4px;}.btn-update:hover{box-shadow:0 3px 10px rgba(199,124,10,.3);color:#fff}
  .btn-back{background:#a9adb1;color:#0d0f11;border:none;border-radius:5px;padding:5px 16px;font-weight:600;font-size:.8rem;height: 35px; margin-top: 4px;}.btn-back:hover{background:#ffffff;color:#333}
  .rc{border:none;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden}
  .rc .card-header{background:linear-gradient(135deg,#3a3f47,#4a5568);color:#fff;padding:7px 14px;border:none}
  .rc .card-header h6{font-weight:600;font-size:.78rem;margin:0}
  .rc .table th{background:#f7f8fa;font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.3px;color:#555;border-bottom:2px solid #e2e6ea;padding:4px 3px}
  .rc .table td{font-size:.72rem;padding:3px;vertical-align:middle}.rc .table tbody tr:hover{background:#f0f7ff}
  .select2-container--default .select2-selection--single{border-radius:4px!important;border-color:#ced4da!important;height:28px!important;min-height:28px!important}
  .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:26px!important;font-size:.76rem}
  .select2-container--default .select2-selection--single .select2-selection__arrow{height:26px!important}
  .gx{--bs-gutter-x:.35rem;--bs-gutter-y:.2rem}

  .btn.btn-primary{border: none; border-radius: 5px; padding: 5px 16px; font-weight: 600; font-size: .8rem; height: 35px; margin-top: 4px;}.btn-primary:hover{background:#a0b9f1;}
  .btn.btn-outline-success{border-radius:4px;padding:3px 8px;font-size:.76rem;height:28px}
  .input-group{display:flex;gap:2px}
  .input-group .select2-container{flex:1}
</style>

  <div class="sf">
  <div class="card">
    <div class="sf-hdr">
      <h4><i class="fas fa-history me-1"></i>{{ $config_data->module_name }}
        @if($data_items['operation_type'] == "show")<span class="badge-op view">View</span>
        @elseif($data_items['operation_type'] == "edit")<span class="badge-op edit">Edit</span>
        @elseif($data_items['operation_type'] == "create")<span class="badge-op create">New</span>@endif
      </h4>
    </div>
    <div class="sf-legend">
      <div class="li"><span class="sw sw-ro"></span> Read-only</div>
      <div class="li"><span class="sw sw-ed"></span> Your inputs</div>
      <div class="li"><span class="sw sw-au"></span> Auto-computed</div>
    </div>

    <div class="card-body py-1 px-2">
      <form action="{{ $data_items['operation_type'] === 'create'
    ? route("$config_data->module_route.store")
    : route("$config_data->module_route.update", [$data_items['data']->id]) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @if($data_items["operation_type"] == "edit")
          @method('PUT')
        @endif

        @php
          $op = $data_items['operation_type'] ?? '';
          $hasOfficer = isset($data_items['officerData']) && !empty($data_items['officerData']?->NAME);
          $hasSessionOfficer = session('name');
          $showInputs = ($op === 'create' && $hasSessionOfficer) || ($op === 'edit' && $hasOfficer) || ($op === 'show' && $hasOfficer);
        @endphp

        <div class="sf-steps">
          <div class="sf-step on"><span class="sn">1</span>Officer</div>
          <div class="sf-step {{ $showInputs ? 'on' : '' }}"><span class="sn">2</span>Assignment</div>
          <div class="sf-step {{ $showInputs ? 'on' : '' }}"><span class="sn">3</span>Details</div>
          <div class="sf-step {{ $showInputs ? 'on' : '' }}"><span class="sn">4</span>Period</div>
        </div>
        
        {{-- OFFICER SELECTION & INFORMATION --}}
        <div class="sb sb-ro">
          <div class="sb-lbl"><span class="dot"></span> Officer Selection &amp; Information</div>
          <div class="row gx align-items-end">
            <div class="col" style="min-width:170px;max-width:210px;">
              <label>PM Code <span class="text-danger">*</span></label>
              @php
                $key = 'pm_code';
                $current = old('pm_code', $data_items['data']->pm_code ?? '');
              @endphp

              @if($data_items["operation_type"] !== "show")
                <div class="d-flex gap-1">
                  <select name="pm_code" id="pm_code" class="form-control select2" style="flex:1">
                    <option value="">— PM Code —</option>

                    @foreach(($data_items['pm_codes'] ?? []) as $pmcode)
                      <option value="{{ $pmcode }}" {{ $current == $pmcode ? 'selected' : '' }}>
                        {{ $pmcode }}
                      </option>
                    @endforeach
                  </select>

                  <button type="submit" class="btn btn-fetch" name="action" value="Fetch Data">
                    <i class="fas fa-download"></i> Fetch
                  </button>
                </div>
              @else
                <input type="text" class="form-control" value="{{ $current }}" disabled>
              @endif
            </div>
            <div class="col" style="max-width:60px">
              <label>Rank</label>
              <input type="text" class="form-control"
                value="{{isset($data_items['officerData']) ? $data_items['officerData']->RANK : session('rank')}}" disabled>
            </div>
            <div class="col" style="min-width:140px">
              <label>Name</label>
              <input type="text" class="form-control"
                value="{{isset($data_items['officerData']) ? $data_items['officerData']->NAME : session('name')}}" disabled>
            </div>
            <div class="col" style="max-width:70px">
              <label>AFPOS</label>
              <input type="text" class="form-control"
                value="{{isset($data_items['officerData']) ? $data_items['officerData']->AFPOS : session('afpos')}}" disabled>
            </div>
            <div class="col" style="max-width:70px">
              <label>AFPSN</label>
              <input type="text" class="form-control"
                value="{{isset($data_items['officerData']) ? $data_items['officerData']->AFPSN : session('afpsn')}}" disabled>
            </div>
            <div class="col" style="max-width:45px">
              <label>Sex</label>
              <input type="text" class="form-control"
                value="{{isset($data_items['officerData']) ? $data_items['officerData']->SEX : session('sex')}}" disabled>
            </div>
            <div class="col" style="max-width:90px">
              <label>DOB</label>
              <input type="text" class="form-control"
                value="{{isset($data_items['officerData']) ? $data_items['officerData']->DOB : session('dob')}}" disabled>
            </div>
            <div class="col" style="max-width:90px">
              <label>Date Ret</label>
              <input type="text" class="form-control"
                value="{{isset($data_items['officerData']) ? $data_items['officerData']->RET : session('date_ret')}}" disabled>
            </div>
          </div>

          <div class="row gx mt-1">
            <div class="col-md-2">
              <label>DOR</label>
              <input type="text" class="form-control"
                value="{{isset($data_items['officerData']) ? $data_items['officerData']->DOR : session('dor')}}" disabled>
            </div>
            <div class="col-md-2">
              <label>SOC</label>
              <input type="text" class="form-control"
                value="{{isset($data_items['officerData']) ? $data_items['officerData']->SOC : session('soc')}}" disabled>
            </div>
            <div class="col-md-2">
              <label>SIG</label>
              <input type="text" class="form-control"
                value="{{isset($data_items['officerData']) ? $data_items['officerData']->SIG : session('sig')}}" disabled>
            </div>
            <div class="col-md-3">
              <label>Current Designation</label>
              <input type="text" class="form-control"
                value="{{isset($data_items['officerData']) ? ($data_items['officerData']->designations->name ?? '') : session('designation')}}" disabled>
            </div>
            <div class="col-md-3">
              <label>Current Unit</label>
              <input type="text" class="form-control"
                value="{{isset($data_items['officerData']) ? ($data_items['officerData']->units->name ?? '') : session('unit')}}" disabled>
            </div>
          </div>
        </div>
        <hr>
        
        <!-- ASSIGNMENT INPUTS -->
      @if($showInputs)

        <div class="sb sb-ed">
        <div class="sb-lbl"><span class="dot"></span> Assignment Details</div>
          <div class="row gx align-items-end">
            {{-- Designation with Add New --}}
            <div class="col" style="min-width:170px">
              <label>Designation</label>
              @if(in_array($op, ['create', 'edit']))
                <select name="designation_id" id="designation_id" class="form-control select2">
                  <option value="">— Designation —</option>
                  @foreach($data_items['designations'] as $id => $entry)
                    <option value="{{ $id }}" @selected(old('designation_id', $data_items['data']->designation_id ?? session('designation_id')) == $id)>
                      {{ $entry }}
                    </option>
                  @endforeach
                  <option value="new" style="font-weight:bold; color:#007bff; border-top:2px solid #ccc;">➕ Create New Designation</option>
                </select>
                
                <div id="new_designation_container" style="display:none; margin-top:3px;">
                  <input type="text" 
                        name="new_designation_name" 
                        id="new_designation_name" 
                        class="form-control" 
                        placeholder="New designation name"
                        value="{{ old('new_designation_name') }}">
                  @error('new_designation_name')
                    <div class="text-danger" style="font-size:.6rem">{{ $message }}</div>
                  @enderror
                </div>
                
                @error('designation_id')
                  <div class="text-danger" style="font-size:.6rem">{{ $message }}</div>
                @enderror
              @else
                <input type="text" class="form-control" value="{{ $data_items['data']->designations->name ?? '' }}" disabled>
              @endif
            </div>

            <div class="col" style="min-width:100px">
              <label>Sub unit</label>
              <input type="text" id="subunit" name="subunit" class="form-control"
                value="{{ old('subunit', $op === 'create' ? '' : ($data_items['data']->subunit ?? '')) }}"
                placeholder="Sub unit"
                {{ $op === 'show' ? 'disabled' : '' }}>
            </div>

            {{-- Unit with Add New --}}
            <div class="col" style="min-width:170px">
              <label>Unit</label>
              @if(in_array($op, ['create','edit']))
                <select name="unit_id" id="unit_id" class="form-control select2">
                  <option value="">— Unit —</option>
                  @foreach($data_items['units'] as $unit)
                    <option
                      value="{{ $unit->id }}"
                      data-pamu-id="{{ $unit->pamu_id ?? '' }}"
                      data-pamu-name="{{ $unit->pamus->name ?? '' }}"
                      @selected(old('unit_id', $data_items['data']->unit_id ?? '') == $unit->id)>
                      {{ $unit->name }}
                    </option>
                  @endforeach
                  <option value="new" style="font-weight:bold; color:#007bff; border-top:2px solid #ccc;">➕ Create New Unit</option>
                </select>
                
                <div id="new_unit_container" style="display:none; margin-top:3px;">
                  <input type="text" 
                        name="new_unit_name" 
                        id="new_unit_name" 
                        class="form-control" 
                        placeholder="New unit name"
                        value="{{ old('new_unit_name') }}"
                        style="margin-bottom:3px;">
                  
                  <select name="new_unit_pamu_id" id="new_unit_pamu_id" class="form-control">
                    <option value="">— Select PAMU (optional) —</option>
                    @foreach(($data_items['pamus'] ?? []) as $id => $name)
                      <option value="{{ $id }}" @selected(old('new_unit_pamu_id') == $id)>{{ $name }}</option>
                    @endforeach
                    <option value="new_pamu" style="font-weight:bold; color:#007bff;">➕ Create New PAMU</option>
                  </select>
                  
                  <input type="text" 
                        name="new_pamu_name" 
                        id="new_pamu_name" 
                        class="form-control" 
                        placeholder="New PAMU name (if creating new)"
                        value="{{ old('new_pamu_name') }}"
                        style="margin-top:3px; display:none;">
                  
                  @error('new_unit_name')
                    <div class="text-danger" style="font-size:.6rem">{{ $message }}</div>
                  @enderror
                </div>
                
                @error('unit_id')
                  <div class="text-danger" style="font-size:.6rem">{{ $message }}</div>
                @enderror
              @else
                <input type="text" class="form-control" value="{{ $data_items['data']->units->name ?? '' }}" disabled>
              @endif
            </div>
            <!-- <div class="col" style="min-width:170px">
              <label>Unit</label>
              @if(in_array($op, ['create','edit']))
                <div class="input-group">
                  <select name="unit_id" id="unit_id" class="form-control select2">
                    <option value="">— Unit —</option>
                    @foreach($data_items['units'] as $unit)
                      <option
                        value="{{ $unit->id }}"
                        data-pamu-id="{{ $unit->pamu_id ?? '' }}"
                        data-pamu-name="{{ $unit->pamus->name ?? '' }}"
                        @selected(old('unit_id', $data_items['data']->unit_id ?? '') == $unit->id)>
                        {{ $unit->name }}
                      </option>
                    @endforeach
                  </select>
                  <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#addUnitModal" title="Add new unit">
                    <i class="fas fa-plus"></i>
                  </button>
                </div>
              @else
                <input type="text" class="form-control" value="{{ $data_items['data']->units->name ?? '' }}" disabled>
              @endif
            </div> -->

            <div class="col" style="min-width:120px">
              <label>PAMU</label>
              <input type="text" class="form-control" id="pamu_name" name="pamu_display" readonly
                    value="{{ old('pamu_display', $data_items['data']->pamus->name ?? '') }}">
              <input type="hidden" id="pamu_id" name="pamu_id"
                    value="{{ old('pamu_id', $data_items['data']->pamu_id ?? '') }}">
            </div>
          </div>

          <div class="row gx mt-2 align-items-end">
            <div class="col" style="min-width:170px">
              <label>Category</label>
              @if(in_array($op, ['create', 'edit']))
                <select name="assignment_id" id="assignment_id" class="form-control select2">
                  <option value="">— Category —</option>
                  @foreach($data_items['assignments'] as $id => $entry)
                    <option value="{{ $id }}" @selected(old('assignment_id', $data_items['data']->assignment_id ?? session('assignment_id')) == $id)>
                      {{ $entry }}
                    </option>
                  @endforeach
                </select>
              @else
                <input type="text" class="form-control" value="{{ $data_items['data']->assignments->name ?? '' }}" disabled>
              @endif
            </div>

            <div class="col" style="min-width:130px">
              <label>Primary/Secondary/Special</label>
              @if(in_array($op, ['create','edit']))
                <select name="pri_sec_spec" class="form-control select2">
                  <option value="">— Type —</option>
                  <option value="primary"   @selected(old('pri_sec_spec', $data_items['data']->pri_sec_spec ?? '') == 'primary')>Primary</option>
                  <option value="secondary" @selected(old('pri_sec_spec', $data_items['data']->pri_sec_spec ?? '') == 'secondary')>Secondary</option>
                  <option value="special"   @selected(old('pri_sec_spec', $data_items['data']->pri_sec_spec ?? '') == 'special')>Special</option>
                </select>
              @else
                <input type="text" class="form-control"
                  value="{{ ucfirst($data_items['data']->pri_sec_spec ?? '') }}" disabled>
              @endif
            </div>

            <div class="col" style="min-width:150px">
              <label>Assignment Type</label>
              @php
                $selectedAssignmentType = old('assignment_type', $data_items['data']->assignment_type ?? '');
              @endphp
              @if(in_array($op, ['create', 'edit']))
                <select name="assignment_type" class="form-control select2">
                  <option value="">— Assign Type —</option>
                  @foreach($data_items['assignment_type3'] as $id => $entry)
                    <option value="{{ $id }}" @selected((string)$selectedAssignmentType === (string)$id)>
                      {{ $entry }}
                    </option>
                  @endforeach
                </select>
              @else
                <input type="text" class="form-control"
                  value="{{ $data_items['data']->assignmentType->name ?? '' }}" disabled>
              @endif
            </div>

            <div class="col" style="min-width:120px">
              <label>Geography</label>
              @if(in_array($op, ['create','edit']))
                <select name="geography" class="form-control select2">
                  <option value="">— Geography —</option>
                  <option value="ncr"      @selected(old('geography', $data_items['data']->geography ?? '') == 'ncr')>NCR</option>
                  <option value="luzon"    @selected(old('geography', $data_items['data']->geography ?? '') == 'luzon')>Luzon</option>
                  <option value="visayas"  @selected(old('geography', $data_items['data']->geography ?? '') == 'visayas')>Visayas</option>
                  <option value="mindanao" @selected(old('geography', $data_items['data']->geography ?? '') == 'mindanao')>Mindanao</option>
                  <option value="foreign"  @selected(old('geography', $data_items['data']->geography ?? '') == 'foreign')>Foreign Duty</option>
                </select>
              @else
                <input type="text" class="form-control"
                  value="{{ ucfirst($data_items['data']->geography ?? '') }}" disabled>
              @endif
            </div>
          </div>
        </div>

          {{-- PERIOD & COMPUTED VALUES --}}
        <div class="sb sb-au">
          <div class="sb-lbl"><span class="dot"></span> Assignment Period <span style="text-transform:none;font-weight:400;font-size:.58rem;color:#aaa;margin-left:4px">(auto-calculated)</span></div>
          <div class="row gx align-items-end">
            <div class="col" style="max-width:140px">
              <label>Start date</label>
              <input type="date" id="start_date" name="start_date" class="form-control"
                value="{{ old('start_date', $op === 'create' ? '' : ($data_items['data']->start_date ?? '')) }}"
                {{ $op === 'show' ? 'disabled' : '' }}>
            </div>
            <div class="col" style="max-width:140px">
              <label>End date</label>
              <input type="date" id="end_date" name="end_date" class="form-control"
                value="{{ old('end_date', $op === 'create' ? '' : ($data_items['data']->end_date ?? '')) }}"
                {{ $op === 'show' ? 'disabled' : '' }}>
            </div>
            <div class="col" style="max-width:70px">
              <label>Rank</label>
              <input type="text" id="rank_during_completion" name="rank_during_completion" class="form-control"
                value="{{ old('rank_during_completion', $data_items['data']->rank_during_completion ?? '') }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : 'readonly' }}>
            </div>
            <div class="col" style="max-width:120px">
              <label>Year earned</label>
              <input type="text" id="year_earned" name="year_earned" class="form-control"
                value="{{ old('year_earned', $data_items['data']->year_earned ?? '') }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : 'readonly' }}>
              <div class="text-danger small mt-1" id="year_earned_error" style="font-size:.6rem">
                @error('year_earned') {{ $message }} @enderror
              </div>
            </div>
          </div>
        </div>
      @endif

          <div class="sf-actions">
          <a href="{{ route("$config_data->module_route.index") }}" class="btn btn-back"><i class="fas fa-arrow-left me-1"></i> Back</a>
          @if($op === 'create' && $showInputs)
              <button type="submit" class="btn btn-save" name="action" value="Save">
                <i class="fas fa-save"></i> Save
              </button>
            @elseif($op === 'edit')
              <button type="submit" class="btn btn-update" name="action" value="Update">
                <i class="fas fa-edit"></i> Update
              </button>
            @endif
        </div>
      </form>
    </div>
  </div>
  {{-- Assignment History Records Table --}}
  <!-- @php
    if (($data_items['operation_type'] === 'create')) {
        $histories = session('relatedHistories', collect([]));
        $currentPmCode = session('pm_code', old('pm_code', ''));
    } else {
        $histories = $data_items['relatedHistories'] ?? collect([]);
        $currentPmCode = $data_items['data']->pm_code ?? '';
    }
  @endphp -->

  @if($showInputs)

    @if($histories && $histories->count() > 0)
      <div class="card rc mt-2">
        <div class="card-header">
          <h6><i class="fas fa-history me-1"></i> Assignment History — {{ $currentPmCode }}</h6>
        </div>
        <div class="card-body p-1">
          <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped table-hover datatable-AssignmentHistory mb-0">
              <thead>
                <tr>
                  <th>PM Code</th>
                  <th>Start</th>
                  <th>End</th>
                  <th>Designation</th>
                  <th>Sub unit</th>
                  <th>Unit</th>
                  <th>PAMU</th>
                  <th>Category</th>
                  <th>Type</th>
                  <th>Assign Type</th>
                  <th>Geography</th>
                  <th>Rank</th>
                  <th>Years</th>
                  <th>Points</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($histories as $history)
                <tr data-entry-id="{{ $history->id }}">
                  <td class="small">{{ $history->pm_code }}</td>
                  <td class="small">{{ $history->start_date }}</td>
                  <td class="small">{{ $history->end_date }}</td>
                  <td class="small">{{ $history->designations->name ?? '-' }}</td>
                  <td class="small">{{ $history->subunit ?? '-' }}</td>
                  <td class="small">{{ $history->units->name ?? '-' }}</td>
                  <td class="small">{{ $history->pamus->name ?? '-' }}</td>
                  <td class="small">{{ $history->assignments->name ?? '-' }}</td>
                  <td class="small">{{ ucfirst($history->pri_sec_spec ?? '-') }}</td>
                  <td class="small">{{ $history->assignmentType->name ?? '-' }}</td>
                  <td class="small">{{ ucfirst($history->geography ?? '-') }}</td>
                  <td class="small">{{ $history->rank_during_completion }}</td>
                  <td class="small">{{ number_format($history->year_earned, 6) }}</td>
                  <td class="small">{{ number_format($history->computed_points ?? 0, 4) }}</td>
                  <td style="white-space: nowrap;">
                      @can($config_data->module_perm_name . '_show')
                        <a class="btn btn-xs btn-primary py-0 px-1 small" href="{{ route('assignmenthistories.show', $history->id) }}">
                          View
                        </a>
                      @endcan
                      @can($config_data->module_perm_name . '_edit')
                        <a class="btn btn-xs btn-info py-0 px-1 small" href="{{ route('assignmenthistories.edit', $history->id) }}">
                          Edit
                        </a>
                      @endcan
                      @can($config_data->module_perm_name . '_delete')
                        <form action="{{ route('assignmenthistories.destroy', $history->id) }}"
                          method="POST"
                          onsubmit="return confirm('Delete?');"
                          style="display: inline-block;">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-xs btn-danger py-0 px-1" title="Delete">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      @endcan
                    </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    @endif
</div>
@endif
@endsection

@section('scripts')
@parent

{{-- DataTable initialization --}}
<script>
  $(function () {
    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
    $.extend(true, $.fn.dataTable.defaults, {
      order: [[ 1, 'desc' ]],
      pageLength: 10,
    });
    $('.datatable-AssignmentHistory:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });
  })
</script>

{{-- PAMU autofill from unit selection --}}
<script>
  (function () {
    const unitSel  = document.getElementById('unit_id');
    const pamuName = document.getElementById('pamu_name');
    const pamuId   = document.getElementById('pamu_id');

    if (!unitSel || !pamuName || !pamuId) return;

    function fillPamu() {
      const opt = unitSel.options[unitSel.selectedIndex];
      const name = (opt && opt.getAttribute('data-pamu-name')) || '';
      const id   = (opt && opt.getAttribute('data-pamu-id')) || '';
      pamuName.value = name;
      pamuId.value   = id;
    }

    unitSel.addEventListener('change', fillPamu);
    if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
      jQuery(unitSel).on('select2:select select2:clear', fillPamu);
    }
    fillPamu();
  })();
</script>

{{-- Auto-compute rank + year earned --}}
  <script>
    (function () {
      const form = document.querySelector('form');
      if (!form) return;

      const pmCodeEl     = document.getElementById('pm_code');
      const startEl      = document.getElementById('start_date');
      const endEl        = document.getElementById('end_date');
      const priEl        = form.querySelector('[name="pri_sec_spec"]');

      const rankOutEl    = document.getElementById('rank_during_completion');
      const yearEarnedEl = document.getElementById('year_earned');
      const yearErrEl    = document.getElementById('year_earned_error');

      const op = @json($data_items['operation_type'] ?? '');
      if (op === 'show') return;

      if (!pmCodeEl || !startEl || !endEl || !rankOutEl || !yearEarnedEl) return;

      let timer = null;
      let aborter = null;

      function getPayload() {
        return {
          id: @json($data_items['data']->id ?? null),
          pm_code: pmCodeEl.value || '',
          start_date: startEl.value || '',
          end_date: endEl.value || '',
          pri_sec_spec: priEl?.value || '',
        };
      }

      function hasRequired(p) {
        return p.pm_code && p.start_date && p.end_date;
      }

      async function computeNow() {
        const payload = getPayload();

        if (!hasRequired(payload)) {
          rankOutEl.value = '';
          yearEarnedEl.value = '';
          if (yearErrEl) yearErrEl.textContent = '';
          return;
        }

        if (aborter) aborter.abort();
        aborter = new AbortController();

        try {
          const res = await fetch(@json(route('assignmenthistories.computeYearEarned')), {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': @json(csrf_token()),
              'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
            signal: aborter.signal,
          });

          if (!res.ok) {
            if (res.status === 422) {
              const err = await res.json().catch(() => null);
              rankOutEl.value = (err?.rank_during_completion ?? '').toString();
              yearEarnedEl.value = '0';
              if (yearErrEl) yearErrEl.textContent = err?.message || 'Error';
              return;
            }
            rankOutEl.value = '';
            yearEarnedEl.value = '';
            if (yearErrEl) yearErrEl.textContent = '';
            return;
          }

          const json = await res.json();
          rankOutEl.value = (json.rank_during_completion ?? '').toString();
          yearEarnedEl.value = (json.year_earned ?? '').toString();

          if (yearErrEl) {
            if (json.message && json.year_earned === 0) {
              yearErrEl.textContent = json.message;
              yearErrEl.className = 'text-danger small mt-1';
            } else {
              yearErrEl.textContent = '';
            }
          }

        } catch (e) {
          if (e?.name !== 'AbortError') console.log('Compute error:', e);
        }
      }

      function scheduleCompute() {
        clearTimeout(timer);
        timer = setTimeout(computeNow, 350);
      }

      [pmCodeEl, startEl, endEl, priEl].filter(Boolean).forEach(el => {
        el.addEventListener('change', scheduleCompute);
        el.addEventListener('input', scheduleCompute);
      });

      if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
        jQuery(pmCodeEl).on('select2:select select2:clear', scheduleCompute);
        if (priEl) jQuery(priEl).on('select2:select select2:clear', scheduleCompute);
      }

      scheduleCompute();
    })();
  </script>

    <script>
      (function () {
        const designationSelect = document.getElementById('designation_id');
        const newDesignationContainer = document.getElementById('new_designation_container');
        const newDesignationInput = document.getElementById('new_designation_name');

        if (designationSelect && newDesignationContainer && newDesignationInput) {
          function handleDesignationChange() {
            if (designationSelect.value === 'new') {
              newDesignationContainer.style.display = 'block';
              newDesignationInput.required = true;
              newDesignationInput.focus();
            } else {
              newDesignationContainer.style.display = 'none';
              newDesignationInput.required = false;
              newDesignationInput.value = '';
            }
          }

          designationSelect.addEventListener('change', handleDesignationChange);
          if (window.jQuery) {
            jQuery(designationSelect).on('select2:select select2:clear', handleDesignationChange);
          }
        }

        // Unit + PAMU inline creation
        const unitSelect = document.getElementById('unit_id');
        const newUnitContainer = document.getElementById('new_unit_container');
        const newUnitInput = document.getElementById('new_unit_name');
        const newUnitPamuSelect = document.getElementById('new_unit_pamu_id');
        const newPamuInput = document.getElementById('new_pamu_name');

        if (unitSelect && newUnitContainer && newUnitInput) {
          function handleUnitChange() {
            if (unitSelect.value === 'new') {
              newUnitContainer.style.display = 'block';
              newUnitInput.required = true;
              newUnitInput.focus();
            } else {
              newUnitContainer.style.display = 'none';
              newUnitInput.required = false;
              newUnitInput.value = '';
              if (newUnitPamuSelect) newUnitPamuSelect.value = '';
              if (newPamuInput) {
                newPamuInput.style.display = 'none';
                newPamuInput.value = '';
              }
            }
          }

          unitSelect.addEventListener('change', handleUnitChange);
          if (window.jQuery) {
            jQuery(unitSelect).on('select2:select select2:clear', handleUnitChange);
          }

          if (newUnitPamuSelect && newPamuInput) {
            newUnitPamuSelect.addEventListener('change', function() {
              if (this.value === 'new_pamu') {
                newPamuInput.style.display = 'block';
                newPamuInput.focus();
              } else {
                newPamuInput.style.display = 'none';
                newPamuInput.value = '';
              }
            });
          }
        }
      })();
    </script>
@endsection