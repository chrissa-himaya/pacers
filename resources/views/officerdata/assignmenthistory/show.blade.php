@extends('layouts.app')
@section('content')

<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');
  .sf{font-family:Arial, Helvetica, sans-serif}.sf .card{border:none;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden}
  .sf-hdr{background:linear-gradient(135deg,#1e3a5f,#2d5a8e);color:#fff;padding:10px 18px;display:flex;align-items:center;justify-content:space-between}
  .sf-hdr h4{margin:0;font-weight:700;font-size:1.05rem;letter-spacing:.3px}
  .sf-hdr .badge-op{display:inline-block;padding:2px 10px;border-radius:20px;font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-left:8px}
  .badge-op.view{background:rgba(255,255,255,.2)}.badge-op.edit{background:#f0ad4e;color:#3d2e00}.badge-op.create{background:#5cb85c;color:#fff}
  .sf-legend{display:flex;gap:16px;padding:5px 18px;font-size:.8rem;font-weight:500;color:#777;border-bottom:1px solid #eee;background:#fcfcfd}
  .sf-legend .li{display:flex;align-items:center;gap:4px}.sf-legend .sw{width:14px;height:14px;border-radius:2px;border:1px solid rgba(0,0,0,.08)}
  .sw-ro{background:#a4a7ab}.sw-ed{background:#fff5c9}.sw-au{background:#9de9b0}
  .sb{border-radius:7px;padding:8px 12px 6px;margin-bottom:6px;position:relative}
  .sb::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;border-radius:7px 0 0 7px}
  .sb-ro{background:#f0f4f8;border:1px solid #d6e0eb}.sb-ro::before{background:#7b9bc0}
  .sb-ed{background:#fffdf5;border:1px solid #f3e339}.sb-ed::before{background:#fbf7e7}
  .sb-au{background:#f2faf4;border:1px solid #c8e6ce}.sb-au::before{background:#5ba96e}
  .sb-lbl{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;display:flex;align-items:center;gap:5px}
  .sb-lbl .dot{width:5px;height:5px;border-radius:50%;display:inline-block}
  .sb-ro .sb-lbl{color:#5a7a9b}.sb-ro .dot{background:#7b9bc0}
  .sb-ed .sb-lbl{color:#9a7d2e}.sb-ed .dot{background:#d4a843}
  .sb-au .sb-lbl{color:#3d7a4f}.sb-au .dot{background:#5ba96e}
  .sf-steps{display:flex;margin-bottom:6px}
  .sf-step{flex:1;text-align:center;padding:4px;font-size:.9rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#aaa;background:#f0f0f0;border-bottom:2px solid #ddd}
  .sf-step.on{color:#2d5a8e;background:#e8f0fa;border-bottom-color:#2d5a8e}
  .sf-step .sn{display:inline-flex;width:14px;height:14px;border-radius:50%;background:#ccc;color:#fff;font-size:.55rem;align-items:center;justify-content:center;margin-right:2px}
  .sf-step.on .sn{background:#2d5a8e}.sf-step:first-child{border-radius:5px 0 0 0}.sf-step:last-child{border-radius:0 5px 0 0}
  .sf label{font-size:.7rem;font-weight:600;color:#444;margin-bottom:0;line-height:1.1}
  .sf .form-control,.sf .form-control-sm{font-size:.76rem;border-radius:4px;padding:3px 6px;height:28px}
  .sf .form-control:focus{border-color:#2d5a8e;box-shadow:0 0 0 2px rgba(45,90,142,.1)}
  .sf .form-control[disabled],.sf .form-control[readonly]{background-color:#e9eef3;color:#555;border-color:#d0d8e0;cursor:default}
  .sb-au .form-control[readonly]{background-color:#e3f2e7;border-color:#b0d9b8;color:#2d6e3f;font-family:Arial, Helvetica, sans-serif, var(--font-body);font-size:.73rem}
  .sf select.form-control{height:28px;padding:1px 6px}
  .btn-fetch{background:linear-gradient(135deg,#2d5a8e,#3a7bd5);color:#fff;border:none;border-radius:4px;padding:3px 12px;font-size:.76rem;font-weight:600;height:28px;white-space:nowrap}
  .btn-fetch:hover{box-shadow:0 3px 10px rgba(45,90,142,.3);color:#fff}
  .sf-actions{display:flex;justify-content:flex-end;gap:8px;background:#dce0e5;border-top:1px solid #e9ecef;border-radius:0 0 10px 10px;padding:6px 12px}
  .btn-save{background:linear-gradient(135deg,#2d8a4e,#3db562);color:#fff;border:none;border-radius:5px;padding:5px 20px;font-weight:600;font-size:.8rem;height:35px;margin-top:4px}.btn-save:hover{box-shadow:0 3px 10px rgba(45,138,78,.3);color:#fff}
  .btn-update{background:linear-gradient(135deg,#c77c0a,#e6a21a);color:#fff;border:none;border-radius:5px;padding:5px 20px;font-weight:600;font-size:.8rem;height:35px;margin-top:4px}.btn-update:hover{box-shadow:0 3px 10px rgba(199,124,10,.3);color:#fff}
  .btn-back{background:#a9adb1;color:#0d0f11;border:none;border-radius:5px;padding:5px 16px;font-weight:600;font-size:.8rem;height:35px;margin-top:4px}.btn-back:hover{background:#ffffff;color:#333}
  .rc{border:none;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden}
  .rc .card-header{background:linear-gradient(135deg,#3a3f47,#4a5568);color:#fff;padding:7px 14px;border:none}
  .rc .card-header h6{font-weight:600;font-size:.78rem;margin:0}
  .rc .table th{background:#f7f8fa;font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.3px;color:#555;border-bottom:2px solid #e2e6ea;padding:4px 3px}
  .rc .table td{font-size:.72rem;padding:3px;vertical-align:middle}.rc .table tbody tr:hover{background:#f0f7ff}
  .select2-container--default .select2-selection--single{border-radius:4px!important;border-color:#ced4da!important;height:28px!important;min-height:28px!important}
  .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:26px!important;font-size:.76rem}
  .select2-container--default .select2-selection--single .select2-selection__arrow{height:26px!important}
  .gx{--bs-gutter-x:.35rem;--bs-gutter-y:.2rem}
  .btn.btn-primary{border:none;border-radius:5px;padding:5px 16px;font-weight:600;font-size:.8rem;height:35px;margin-top:4px}.btn-primary:hover{background:#a0b9f1;}
  .input-group{display:flex;gap:2px}.input-group .select2-container{flex:1}
  /* field message tags */
  .field-msg{font-size:.6rem;margin-top:2px;border-radius:3px;padding:1px 5px;display:none}
  .field-msg.warn{color:#b45309;background:#fef3c7}
  .field-msg.err {color:#991b1b;background:#fee2e2}
</style>

@php
  $op = $data_items['operation_type'] ?? '';

  $isFetchedCreate = ($op === 'create') && session('name');
  $isFetchedEdit   = ($op === 'edit')   && session('fetched_officer');
  $hasOfficer      = isset($data_items['officerData']) && !empty($data_items['officerData']?->NAME);

  $showInputs = $isFetchedCreate || $isFetchedEdit
             || ($op === 'edit' && $hasOfficer)
             || ($op === 'show' && $hasOfficer);

  $currentPmCode = $data_items['currentPmCode']
      ?? old('pm_code', $data_items['data']->pm_code ?? '');

  $histories = session('relatedHistories')
      ?? $data_items['relatedHistories']
      ?? collect([]);
@endphp

<div class="sf">
  <div class="card">
    <div class="sf-hdr">
      <h4><i class="fas fa-history me-1"></i>{{ $config_data->module_name }}
        @if($op == 'show')<span class="badge-op view">View</span>
        @elseif($op == 'edit')<span class="badge-op edit">Edit</span>
        @elseif($op == 'create')<span class="badge-op create">New</span>@endif
      </h4>
    </div>
    <div class="sf-legend">
      <div class="li"><span class="sw sw-ro"></span> Read-only</div>
      <div class="li"><span class="sw sw-ed"></span> Your inputs</div>
      <div class="li"><span class="sw sw-au"></span> Auto-computed</div>
    </div>

    <div class="card-body py-1 px-2">
      <form id="mainForm"
        action="{{ $op === 'create'
            ? route("$config_data->module_route.store")
            : route("$config_data->module_route.update", [$data_items['data']->id]) }}"
        method="POST"
        enctype="multipart/form-data">
        @csrf
        @if($op === 'edit') @method('PUT') @endif

        <div class="sf-steps">
          <div class="sf-step on"><span class="sn">1</span>Officer</div>
          <div class="sf-step {{ $showInputs ? 'on' : '' }}"><span class="sn">2</span>Assignment</div>
          <div class="sf-step {{ $showInputs ? 'on' : '' }}"><span class="sn">3</span>Details</div>
          <div class="sf-step {{ $showInputs ? 'on' : '' }}"><span class="sn">4</span>Period</div>
        </div>

        {{-- ── OFFICER ── --}}
        <div class="sb sb-ro">
          <div class="sb-lbl"><span class="dot"></span> Officer Selection &amp; Information</div>
          <div class="row gx align-items-end">
            <div class="col" style="min-width:170px;max-width:210px;">
              <label>PM Code <span class="text-danger">*</span></label>
              @php $currentPm = old('pm_code', $data_items['data']->pm_code ?? ''); @endphp
              @if($op !== 'show')
                <div class="d-flex gap-1">
                  <select name="pm_code" id="pm_code" class="form-control select2" style="flex:1">
                    <option value="">— PM Code —</option>
                    @foreach(($data_items['pm_codes'] ?? []) as $pmcode)
                      <option value="{{ $pmcode }}" {{ $currentPm == $pmcode ? 'selected' : '' }}>{{ $pmcode }}</option>
                    @endforeach
                  </select>
                  <button type="submit" class="btn btn-fetch" name="action" value="Fetch Data"
                          id="fetchBtn" onclick="markFetch()">
                    <i class="fas fa-download"></i> Fetch
                  </button>
                </div>
                @error('pm_code')
                  <div class="text-danger" style="font-size:.6rem">{{ $message }}</div>
                @enderror
              @else
                <input type="text" class="form-control" value="{{ $currentPm }}" disabled>
              @endif
            </div>

            @php
              $off = $data_items['officerData'] ?? null;
              $f = fn($key, $offKey) =>
                  $isFetchedEdit
                    ? session($key, old($key, $off?->{$offKey} ?? ''))
                    : ($off?->{$offKey} ?? session($key, ''));
            @endphp
            <div class="col" style="max-width:60px"><label>Rank</label>
              <input type="text" class="form-control" value="{{ $f('rank','RANK') }}" disabled></div>
            <div class="col" style="min-width:140px"><label>Name</label>
              <input type="text" class="form-control" value="{{ $f('name','NAME') }}" disabled></div>
            <div class="col" style="max-width:70px"><label>AFPOS</label>
              <input type="text" class="form-control" value="{{ $f('afpos','AFPOS') }}" disabled></div>
            <div class="col" style="max-width:70px"><label>AFPSN</label>
              <input type="text" class="form-control" value="{{ $f('afpsn','AFPSN') }}" disabled></div>
            <div class="col" style="max-width:45px"><label>Sex</label>
              <input type="text" class="form-control" value="{{ $f('sex','SEX') }}" disabled></div>
            <div class="col" style="max-width:90px"><label>DOB</label>
              <input type="text" class="form-control" value="{{ $f('dob','DOB') }}" disabled></div>
            <div class="col" style="max-width:90px"><label>Date Ret</label>
              <input type="text" class="form-control" value="{{ $f('date_ret','RET') }}" disabled></div>
          </div>
          <div class="row gx mt-1">
            <div class="col-md-2"><label>DOR</label>
              <input type="text" class="form-control" value="{{ $f('dor','DOR') }}" disabled></div>
            <div class="col-md-2"><label>SOC</label>
              <input type="text" class="form-control" value="{{ $f('soc','SOC') }}" disabled></div>
            <div class="col-md-2"><label>SIG</label>
              <input type="text" class="form-control" value="{{ $f('sig','SIG') }}" disabled></div>
            <div class="col-md-3">
              <label>Current Designation</label>
              @php
                $curDesig = $isFetchedEdit
                    ? session('designation', $off?->designations?->name ?? '')
                    : ($off?->designations?->name ?? session('designation', ''));
              @endphp
              <input type="text" class="form-control" value="{{ $curDesig }}" disabled>
            </div>
            <div class="col-md-3">
              <label>Current Unit</label>
              @php
                $curUnit = $isFetchedEdit
                    ? session('unit', $off?->units?->name ?? '')
                    : ($off?->units?->name ?? session('unit', ''));
              @endphp
              <input type="text" class="form-control" value="{{ $curUnit }}" disabled>
            </div>
          </div>
        </div>
        <hr>

        @if($showInputs)
        {{-- ── ASSIGNMENT DETAILS ── --}}
        <div class="sb sb-ed">
          <div class="sb-lbl"><span class="dot"></span> Assignment Details</div>
          <div class="row gx align-items-end">
            <div class="col" style="min-width:170px">
              <label>Designation</label>
              @if(in_array($op, ['create', 'edit']))
                <select name="designation_id" id="designation_id" class="form-control select2">
                  <option value="">— Designation —</option>
                  @foreach($data_items['designations'] as $id => $entry)
                    <option value="{{ $id }}" @selected(old('designation_id', $data_items['data']->designation_id ?? '') == $id)>{{ $entry }}</option>
                  @endforeach
                  <option value="new" style="font-weight:bold;color:#007bff;border-top:2px solid #ccc;">➕ Create New Designation</option>
                </select>
                <div id="new_designation_container" style="display:none;margin-top:3px;">
                  <input type="text" name="new_designation_name" id="new_designation_name"
                         class="form-control" value="{{ old('new_designation_name') }}">
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
                value="{{ old('subunit', $data_items['data']->subunit ?? '') }}"
                {{ $op === 'show' ? 'disabled' : '' }}>
            </div>

            <div class="col" style="min-width:170px">
              <label>Unit</label>
              @if(in_array($op, ['create','edit']))
                <select name="unit_id" id="unit_id" class="form-control select2">
                  <option value="">— Unit —</option>
                  @foreach($data_items['units'] as $unit)
                    <option value="{{ $unit->id }}"
                      data-pamu-id="{{ $unit->pamu_id ?? '' }}"
                      data-pamu-name="{{ $unit->pamus->name ?? '' }}"
                      @selected(old('unit_id', $data_items['data']->unit_id ?? '') == $unit->id)>
                      {{ $unit->name }}
                    </option>
                  @endforeach
                  <option value="new" style="font-weight:bold;color:#007bff;border-top:2px solid #ccc;">➕ Create New Unit</option>
                </select>
                <div id="new_unit_container" style="display:none;margin-top:3px;">
                  <input type="text" name="new_unit_name" id="new_unit_name" class="form-control"
                         value="{{ old('new_unit_name') }}" style="margin-bottom:3px;">
                  
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

            {{-- ── PAMU (independent select, not driven by Unit) ── --}}
            <div class="col" style="min-width:120px">
              <label>PAMU</label>
              @if(in_array($op, ['create','edit']))
                <select name="pamu_id" id="pamu_id" class="form-control select2">
                  <option value="">— PAMU —</option>
                  @foreach(($data_items['pamus'] ?? []) as $id => $name)
                    <option value="{{ $id }}"
                      @selected(old('pamu_id', $data_items['data']->pamu_id ?? '') == $id)>
                      {{ $name }}
                    </option>
                  @endforeach
                  <option value="new" style="font-weight:bold;color:#007bff;border-top:2px solid #ccc;">➕ Create New PAMU</option>
                </select>
                <div id="new_pamu_standalone_container" style="display:none;margin-top:3px;">
                  <input type="text" name="new_pamu_standalone_name" id="new_pamu_standalone_name"
                         class="form-control" value="{{ old('new_pamu_standalone_name') }}"
                         placeholder="New PAMU name">
                  @error('new_pamu_standalone_name')
                    <div class="text-danger" style="font-size:.6rem">{{ $message }}</div>
                  @enderror
                </div>
                @error('pamu_id')
                  <div class="text-danger" style="font-size:.6rem">{{ $message }}</div>
                @enderror
              @else
                <input type="text" class="form-control"
                       value="{{ $data_items['data']->pamus->name ?? '' }}" disabled>
              @endif
            </div>
          </div>

          <div class="row gx mt-2 align-items-end">
            <div class="col" style="min-width:170px">
              <label>Category</label>
              @if(in_array($op, ['create', 'edit']))
                <select name="assignment_id" id="assignment_id" class="form-control select2">
                  <option value="">— Category —</option>
                  @foreach($data_items['assignments'] as $id => $entry)
                    <option value="{{ $id }}" @selected(old('assignment_id', $data_items['data']->assignment_id ?? '') == $id)>{{ $entry }}</option>
                  @endforeach
                </select>
              @else
                <input type="text" class="form-control" value="{{ $data_items['data']->assignments->name ?? '' }}" disabled>
              @endif
            </div>

            <div class="col" style="min-width:130px">
              <label>Primary/Secondary/Special</label>
              @if(in_array($op, ['create','edit']))
                <select name="pri_sec_spec" id="pri_sec_spec" class="form-control select2">
                  <option value="">— Type —</option>
                  <option value="primary"   @selected(old('pri_sec_spec', $data_items['data']->pri_sec_spec ?? '') == 'primary')>Primary</option>
                  <option value="secondary" @selected(old('pri_sec_spec', $data_items['data']->pri_sec_spec ?? '') == 'secondary')>Secondary</option>
                  <option value="special"   @selected(old('pri_sec_spec', $data_items['data']->pri_sec_spec ?? '') == 'special')>Special</option>
                </select>
              @else
                <input type="text" class="form-control" value="{{ ucfirst($data_items['data']->pri_sec_spec ?? '') }}" disabled>
              @endif
            </div>

            <div class="col" style="min-width:150px">
              <label>Assignment Type</label>
              @php $selAsgType = old('assignment_type', $data_items['data']->assignment_type ?? ''); @endphp
              @if(in_array($op, ['create', 'edit']))
                <select name="assignment_type" id="assignment_type" class="form-control select2">
                  <option value="">— Assign Type —</option>
                  @foreach($data_items['assignment_type3'] as $id => $entry)
                    <option value="{{ $id }}" @selected((string)$selAsgType === (string)$id)>{{ $entry }}</option>
                  @endforeach
                </select>
              @else
                <input type="text" class="form-control" value="{{ $data_items['data']->assignmentType->name ?? '' }}" disabled>
              @endif
            </div>

            <div class="col" style="min-width:120px">
              <label>Geography</label>
              @if(in_array($op, ['create','edit']))
                <select name="geography" id="geography" class="form-control select2">
                  <option value="">— Geography —</option>
                  <option value="ncr"      @selected(old('geography', $data_items['data']->geography ?? '') == 'ncr')>NCR</option>
                  <option value="luzon"    @selected(old('geography', $data_items['data']->geography ?? '') == 'luzon')>Luzon</option>
                  <option value="visayas"  @selected(old('geography', $data_items['data']->geography ?? '') == 'visayas')>Visayas</option>
                  <option value="mindanao" @selected(old('geography', $data_items['data']->geography ?? '') == 'mindanao')>Mindanao</option>
                  <option value="foreign"  @selected(old('geography', $data_items['data']->geography ?? '') == 'foreign')>Foreign Duty</option>
                </select>
              @else
                <input type="text" class="form-control" value="{{ ucfirst($data_items['data']->geography ?? '') }}" disabled>
              @endif
            </div>
          </div>
        </div>

        {{-- ── PERIOD ── --}}
        <div class="sb sb-au">
          <div class="sb-lbl"><span class="dot"></span> Assignment Period
            <span style="text-transform:none;font-weight:400;font-size:.58rem;color:#aaa;margin-left:4px">(auto-calculated)</span>
          </div>
          <div class="row gx align-items-end">
            <div class="col" style="max-width:140px">
              <label>Start date</label>
              <input type="date" id="start_date" name="start_date" class="form-control"
                value="{{ old('start_date', $data_items['data']->start_date ?? '') }}"
                {{ $op === 'show' ? 'disabled' : '' }}>
            </div>
            <div class="col" style="max-width:140px">
              <label>End date</label>
              <input type="date" id="end_date" name="end_date" class="form-control"
                value="{{ old('end_date', $data_items['data']->end_date ?? '') }}"
                {{ $op === 'show' ? 'disabled' : '' }}>
            </div>

            {{-- Rank – rank_warning shown here --}}
            <div class="col" style="max-width:120px">
              <label>Rank</label>
              <input type="text" id="rank_during_completion" name="rank_during_completion" class="form-control"
                value="{{ old('rank_during_completion', $data_items['data']->rank_during_completion ?? '') }}"
                {{ $op === 'show' ? 'disabled' : 'readonly' }}>
              <div id="rank_warning" class="field-msg warn"></div>
            </div>

            {{-- Year earned – overlap warning or hard save error shown here --}}
            <div class="col" style="max-width:140px">
              <label>Year earned</label>
              <input type="text" id="year_earned" name="year_earned" class="form-control"
                value="{{ old('year_earned', $data_items['data']->year_earned ?? '') }}"
                {{ $op === 'show' ? 'disabled' : 'readonly' }}>
              <div id="year_earned_error" class="field-msg err">
                @error('year_earned') {{ $message }} @enderror
                @error('start_date')  {{ $message }} @enderror
              </div>
              <div id="year_overlap_warn" class="field-msg warn">
                ⚠ Dates overlap – year earned will be 0
              </div>
            </div>
          </div>
        </div>
        @endif

        <div class="sf-actions">
          <a href="{{ route("$config_data->module_route.index") }}" class="btn btn-back">
            <i class="fas fa-arrow-left me-1"></i> Back
          </a>
          @if($op === 'create' && $showInputs)
            <button type="submit" class="btn btn-save" name="action" value="Save" onclick="markSave()">
              <i class="fas fa-save"></i> Save
            </button>
          @elseif($op === 'edit')
            <button type="submit" class="btn btn-update" name="action" value="Update" onclick="markSave()">
              <i class="fas fa-edit"></i> Update
            </button>
          @endif
        </div>
      </form>
    </div>
  </div>

  {{-- ── RELATED HISTORIES ── --}}
  @if($showInputs && $histories && $histories->count() > 0)
  <div class="card rc mt-2">
    <div class="card-header">
      <h6><i class="fas fa-history me-1"></i> Assignment History — {{ $currentPmCode }}</h6>
    </div>
    <div class="card-body p-1">
      <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped table-hover datatable-AssignmentHistory mb-0">
          <thead>
            <tr>
              <th>PM Code</th><th>Start</th><th>End</th><th>Designation</th>
              <th>Sub unit</th><th>Unit</th><th>PAMU</th><th>Category</th>
              <th>Type</th><th>Assign Type</th><th>Geography</th>
              <th>Rank</th><th>Years</th><th>Points</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($histories as $history)
            <tr>
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
              <td class="small">{{ $history->rank_during_completion ?? '-' }}</td>
              <td class="small">{{ number_format((float)($history->year_earned ?? 0), 6) }}</td>
              <td class="small">{{ number_format((float)($history->computed_points ?? 0), 4) }}</td>
              <td style="white-space:nowrap">
                @can($config_data->module_perm_name . '_show')
                  <a class="btn btn-xs btn-primary py-0 px-1 small"
                     href="{{ route('assignmenthistories.show', $history->id) }}">View</a>
                @endcan
                @can($config_data->module_perm_name . '_edit')
                  <a class="btn btn-xs btn-info py-0 px-1 small"
                     href="{{ route('assignmenthistories.edit', $history->id) }}">Edit</a>
                @endcan
                @can($config_data->module_perm_name . '_delete')
                  <form action="{{ route('assignmenthistories.destroy', $history->id) }}"
                        method="POST" onsubmit="return confirm('Delete?');" style="display:inline-block">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-xs btn-danger py-0 px-1">
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
@endsection

@section('scripts')
@parent

<script>
$(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);
  $.extend(true, $.fn.dataTable.defaults, { order: [[1, 'desc']], pageLength: 10 });
  $('.datatable-AssignmentHistory:not(.ajaxTable)').DataTable({ buttons: dtButtons });
});
</script>

<script>
  let _isFetch = false;
  function markFetch() { _isFetch = true; }
  function markSave()  { _isFetch = false; }
</script>

{{-- PAMU auto-fill (kept for Unit field's internal new-unit PAMU sub-select only) --}}
<script>
(function () {
  const unitSel  = document.getElementById('unit_id');
  const pamuName = document.getElementById('pamu_name');
  const pamuId   = document.getElementById('pamu_id');
  // pamu_name and pamu_id no longer exist as text/hidden inputs;
  // this block is a no-op but kept to avoid removing any unrelated logic.
  if (!unitSel || !pamuName || !pamuId) return;

  function fillPamu() {
    if (unitSel.value === 'new') return;
    const opt  = unitSel.options[unitSel.selectedIndex];
    pamuName.value = (opt && opt.getAttribute('data-pamu-name')) || '';
    pamuId.value   = (opt && opt.getAttribute('data-pamu-id'))   || '';
  }

  unitSel.addEventListener('change', fillPamu);
  if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
    jQuery(unitSel).on('select2:select select2:clear', fillPamu);
  }
  fillPamu();
})();
</script>

{{-- Auto-compute rank + year_earned --}}
<script>
  (function () {
    const op = @json($data_items['operation_type'] ?? '');
    if (op === 'show') return;

    const pmCodeEl  = document.getElementById('pm_code');
    const startEl   = document.getElementById('start_date');
    const endEl     = document.getElementById('end_date');
    const priEl     = document.getElementById('pri_sec_spec');
    const rankOutEl = document.getElementById('rank_during_completion');
    const yearEl    = document.getElementById('year_earned');

    // Three separate message elements
    const rankWarnEl = document.getElementById('rank_warning');     // below Rank
    const yearOverEl = document.getElementById('year_overlap_warn');// below Year earned
    const yearErrEl  = document.getElementById('year_earned_error');// below Year earned (hard errors)

    if (!pmCodeEl || !startEl || !endEl || !rankOutEl || !yearEl) return;

    const recordId = @json($data_items['data']->id ?? null);
    let timer   = null;
    let aborter = null;

    function show(el, visible, text) {
      if (!el) return;
      el.style.display = visible ? 'block' : 'none';
      if (text !== undefined) el.textContent = text;
    }

    function clearAll() {
      rankOutEl.value = '';
      yearEl.value    = '';
      show(rankWarnEl, false);
      show(yearOverEl, false);
      show(yearErrEl,  false);
    }

    function payload() {
      return {
        id:           recordId,
        pm_code:      pmCodeEl.value || '',
        start_date:   startEl.value  || '',
        end_date:     endEl.value    || '',
        pri_sec_spec: priEl?.value   || '',
      };
    }

    async function compute() {
      const p = payload();

      // Missing pm_code or either date → clear and bail (no error shown)
      if (!p.pm_code || !p.start_date || !p.end_date) {
        clearAll();
        return;
      }

      if (aborter) aborter.abort();
      aborter = new AbortController();

      try {
        const res = await fetch(@json(route('assignmenthistories.computeYearEarned')), {
          method:  'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': @json(csrf_token()),
            'Accept':       'application/json',
          },
          body:   JSON.stringify(p),
          signal: aborter.signal,
        });

        const json = await res.json().catch(() => ({}));

        if (!res.ok) {
          // Hard error (422 invalid dates etc.) – show below year_earned
          rankOutEl.value = '';
          yearEl.value    = '';
          show(rankWarnEl, false);
          show(yearOverEl, false);
          show(yearErrEl,  true, json?.message || 'Error');
          return;
        }

        // ── Rank ──────────────────────────────────────────────────
        rankOutEl.value = (json.rank_during_completion ?? '').toString();
        // rank_warning (no rank / conflict) → below Rank field only
        show(rankWarnEl, !!json.rank_warning, json.rank_warning || '');

        // ── Year earned ───────────────────────────────────────────
        yearEl.value = (json.year_earned !== null && json.year_earned !== undefined)
                        ? json.year_earned.toString() : '';

        // Overlap warning → year=0, record saves fine, shown below Year earned
        show(yearOverEl, json.ok && json.year_earned === 0 && !!json.message);

        // Clear any lingering hard error
        show(yearErrEl, false);

      } catch (e) {
        if (e?.name !== 'AbortError') console.warn('Compute error:', e);
      }
    }

    function schedule() {
      clearTimeout(timer);
      timer = setTimeout(compute, 350);
    }

    [pmCodeEl, startEl, endEl, priEl].filter(Boolean).forEach(el => {
      el.addEventListener('change', schedule);
      el.addEventListener('input',  schedule);
    });

    if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
      jQuery(pmCodeEl).on('select2:select select2:clear', schedule);
      if (priEl) jQuery(priEl).on('select2:select select2:clear', schedule);
    }

    compute(); // fire immediately on edit form load
  })();
  </script>

  {{-- Designation / Unit / PAMU toggles --}}
  <script>
  (function () {
    const desigSel      = document.getElementById('designation_id');
    const newDesigBox   = document.getElementById('new_designation_container');
    const newDesigInput = document.getElementById('new_designation_name');

    if (desigSel && newDesigBox && newDesigInput) {
      function toggleDesig() {
        const isNew = desigSel.value === 'new';
        newDesigBox.style.display = isNew ? 'block' : 'none';
        newDesigInput.required    = isNew;
        if (isNew) newDesigInput.focus(); else newDesigInput.value = '';
      }
      desigSel.addEventListener('change', toggleDesig);
      if (window.jQuery) jQuery(desigSel).on('select2:select select2:clear', toggleDesig);
    }

    const unitSel    = document.getElementById('unit_id');
    const newUnitBox = document.getElementById('new_unit_container');
    const newUnitIn  = document.getElementById('new_unit_name');
    const pamuSel    = document.getElementById('new_unit_pamu_id');
    const newPamuIn  = document.getElementById('new_pamu_name');

    if (unitSel && newUnitBox && newUnitIn) {
      function toggleUnit() {
        const isNew = unitSel.value === 'new';
        newUnitBox.style.display = isNew ? 'block' : 'none';
        newUnitIn.required = isNew;
        if (isNew) newUnitIn.focus();
        else {
          newUnitIn.value = '';
          if (pamuSel)   pamuSel.value = '';
          if (newPamuIn) { newPamuIn.style.display = 'none'; newPamuIn.value = ''; }
        }
      }
      unitSel.addEventListener('change', toggleUnit);
      if (window.jQuery) jQuery(unitSel).on('select2:select select2:clear', toggleUnit);

      if (pamuSel && newPamuIn) {
        pamuSel.addEventListener('change', function () {
          const isNewPamu = this.value === 'new_pamu';
          newPamuIn.style.display = isNewPamu ? 'block' : 'none';
          if (isNewPamu) newPamuIn.focus(); else newPamuIn.value = '';
        });
      }
    }

    // ── Standalone PAMU select toggle ────────────────────────────
    const pamuStandaloneSel = document.getElementById('pamu_id');
    const newPamuStandaloneBox = document.getElementById('new_pamu_standalone_container');
    const newPamuStandaloneIn  = document.getElementById('new_pamu_standalone_name');

    if (pamuStandaloneSel && newPamuStandaloneBox && newPamuStandaloneIn) {
      function togglePamuStandalone() {
        const isNew = pamuStandaloneSel.value === 'new';
        newPamuStandaloneBox.style.display = isNew ? 'block' : 'none';
        newPamuStandaloneIn.required = isNew;
        if (isNew) newPamuStandaloneIn.focus(); else newPamuStandaloneIn.value = '';
      }
      pamuStandaloneSel.addEventListener('change', togglePamuStandalone);
      if (window.jQuery) jQuery(pamuStandaloneSel).on('select2:select select2:clear', togglePamuStandalone);
    }
  })();
</script>
@endsection