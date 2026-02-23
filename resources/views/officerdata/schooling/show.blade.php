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
  /* New unit creation sub-panel */
  .new-unit-panel{background:#f8f9ff;border:1px solid #c8d5f5;border-radius:5px;padding:6px 8px;margin-top:4px}
  .new-unit-panel label{font-size:.65rem;font-weight:600;color:#3a5a9b}
  .loc-btn{display:inline-flex;align-items:center;gap:4px;padding:2px 10px;border-radius:20px;border:1.5px solid #ced4da;background:#fff;font-size:.7rem;font-weight:600;cursor:pointer;transition:.15s}
  .loc-btn:hover{border-color:#2d5a8e}
  .loc-btn.active-local{border-color:#2d8a4e;background:#e3f2e7;color:#2d6e3f}
  .loc-btn.active-foreign{border-color:#8a4e2d;background:#f2ebe3;color:#6e3d2d}
</style>

<div class="sf">
<div class="card">
  <div class="sf-hdr">
    <h4><i class="fas fa-graduation-cap me-1"></i>{{ $config_data->module_name }}
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
      : route("$config_data->module_route.update", [$data_items['data']->id]) }}"
      method="POST" enctype="multipart/form-data">
      @csrf
      @if($data_items["operation_type"] == "edit") @method('PUT') @endif

      @php
        $op = $data_items['operation_type'] ?? '';
        $hasOfficer = isset($data_items['officerData']) && !empty($data_items['officerData']?->NAME);
        $hasSessionOfficer = session('name');
        $showInputs = ($op === 'create' && $hasSessionOfficer)
                   || ($op === 'edit'   && $hasOfficer)
                   || ($op === 'show'   && $hasOfficer);
      @endphp

      <div class="sf-steps">
        <div class="sf-step on"><span class="sn">1</span>Officer</div>
        <div class="sf-step {{ $showInputs ? 'on' : '' }}"><span class="sn">2</span>Info</div>
        <div class="sf-step {{ $showInputs ? 'on' : '' }}"><span class="sn">3</span>Schooling</div>
        <div class="sf-step {{ $showInputs ? 'on' : '' }}"><span class="sn">4</span>Points</div>
      </div>

      {{-- OFFICER SELECTION & INFORMATION --}}
      <div class="sb sb-ro">
        <div class="sb-lbl"><span class="dot"></span> Officer Selection &amp; Information</div>
        <div class="row gx align-items-end">
          <div class="col" style="min-width:170px;max-width:210px;">
            <label>PM Code <span class="text-danger">*</span></label>
            @php $current = old('pm_code', $data_items['data']->pm_code ?? ''); @endphp
            @if($data_items["operation_type"] !== "show")
              <div class="d-flex gap-1">
                <select name="pm_code" id="pm_code" class="form-control select2" style="flex:1">
                  <option value="">— PM Code —</option>
                  @foreach(($data_items['pm_codes'] ?? []) as $pmcode)
                    <option value="{{ $pmcode }}" {{ $current == $pmcode ? 'selected' : '' }}>{{ $pmcode }}</option>
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
            <input type="text" class="form-control" value="{{ session('rank') ?? ($data_items['officerData']->RANK ?? '') }}" disabled>
          </div>
          <div class="col" style="min-width:140px">
            <label>Name</label>
            <input type="text" class="form-control" value="{{ session('name') ?? ($data_items['officerData']->NAME ?? '') }}" disabled>
          </div>
          <div class="col" style="max-width:70px">
            <label>AFPOS</label>
            <input type="text" class="form-control" value="{{ session('afpos') ?? ($data_items['officerData']->AFPOS ?? '') }}" disabled>
          </div>
          <div class="col" style="max-width:70px">
            <label>AFPSN</label>
            <input type="text" class="form-control" value="{{ session('afpsn') ?? ($data_items['officerData']->AFPSN ?? '') }}" disabled>
          </div>
          <div class="col" style="max-width:45px">
            <label>Sex</label>
            <input type="text" class="form-control" value="{{ session('sex') ?? ($data_items['officerData']->SEX ?? '') }}" disabled>
          </div>
          <div class="col" style="max-width:90px">
            <label>DOB</label>
            <input type="text" class="form-control" value="{{ session('dob') ?? ($data_items['officerData']->DOB ?? '') }}" disabled>
          </div>
          <div class="col" style="max-width:90px">
            <label>Date Ret</label>
            <input type="text" class="form-control" value="{{ session('date_ret') ?? ($data_items['officerData']->RET ?? '') }}" disabled>
          </div>
        </div>
        <div class="row gx mt-1">
          <div class="col-md-2">
            <label>DOR</label>
            <input type="text" class="form-control" value="{{ session('dor') ?? ($data_items['officerData']->DOR ?? '') }}" disabled>
          </div>
          <div class="col-md-2">
            <label>SOC</label>
            <input type="text" class="form-control" value="{{ session('soc') ?? ($data_items['officerData']->SOC ?? '') }}" disabled>
          </div>
          <div class="col-md-2">
            <label>SIG</label>
            <input type="text" class="form-control" value="{{ session('sig') ?? ($data_items['officerData']->SIG ?? '') }}" disabled>
          </div>
          <div class="col-md-3">
            <label>Current Designation</label>
            <input type="text" class="form-control" value="{{ session('designation') ?? ($data_items['officerData']->designations->name ?? '') }}" disabled>
          </div>
          <div class="col-md-3">
            <label>Current Unit</label>
            <input type="text" class="form-control" value="{{ session('unit') ?? ($data_items['officerData']->units->name ?? '') }}" disabled>
          </div>
        </div>
      </div>
      <hr>

      {{-- CAREER ADVISER INPUTS --}}
      @if(($op === 'create' && $hasSessionOfficer) || ($op === 'edit' && $hasOfficer) || ($op === 'show' && $hasOfficer))

      <div class="sb sb-ed">
        <div class="sb-lbl"><span class="dot"></span> Schooling Details</div>
        <div class="row gx align-items-end">

          {{-- Assignment Category --}}
          <div class="col" style="min-width:170px">
            <label>Assignment Category <span class="text-danger">*</span></label>
            @if(in_array($op, ['create','edit']))
              <select name="assignment_id" id="assignment_id" class="form-control select2">
                <option value="">— Category —</option>
                @foreach($data_items['assignments'] as $id => $entry)
                  <option value="{{ $id }}" @selected(old('assignment_id', $data_items['data']->assignment_id ?? session('assignment_id')) == $id)>
                    {{ $entry }}
                  </option>
                @endforeach
              </select>
              @error('assignment_id')<div class="text-danger" style="font-size:.6rem">{{ $message }}</div>@enderror
            @else
              <input type="text" class="form-control" value="{{ $data_items['data']->assignments->name ?? '' }}" disabled>
            @endif
          </div>

          {{-- Entry --}}
          <div class="col" style="min-width:170px">
            <label>Entry <span class="text-danger">*</span></label>
            @if(in_array($op, ['create','edit']))
              <select name="schoolingname_id" id="schoolingname_id" class="form-control select2" disabled>
                <option value="">— Select Assignment First —</option>
              </select>
              <div id="new_entry_container" style="display:none;margin-top:3px">
                <input type="text" name="new_entry_name" id="new_entry_name" class="form-control" value="{{ old('new_entry_name') }}">
                @error('new_entry_name')<div class="text-danger" style="font-size:.6rem">{{ $message }}</div>@enderror
              </div>
              @error('schoolingname_id')<div class="text-danger" style="font-size:.6rem">{{ $message }}</div>@enderror
            @else
              <input type="text" class="form-control" value="{{ $data_items['data']->schoolingnames->name ?? '' }}" disabled>
            @endif
          </div>

          {{-- Class --}}
          <div class="col" style="max-width:90px">
            <label>Class</label>
            <input type="text" id="classname" name="classname" class="form-control"
              value="{{ old('classname', $op === 'create' ? '' : ($data_items['data']->classname ?? '')) }}"
              {{ $op === 'show' ? 'disabled' : '' }}>
          </div>

          {{-- ══════════════════════════════════════════════════════════
               SCHOOLING UNIT — mirrors the Entry field pattern:
               • existing units in dropdown
               • "➕ Create New Unit" option reveals name + Local/Foreign choice
               • on form submit the controller resolveOrCreateSchoolingUnit()
                 saves to schooling_units and merges the new id back
          ══════════════════════════════════════════════════════════ --}}
          <div class="col" style="min-width:170px">
            <label>Schooling Unit</label>
            @if(in_array($op, ['create','edit']))

              {{-- Main dropdown --}}
              <select name="schooling_unit_id" id="schooling_unit_id" class="form-control select2">
                <option value="">— Unit —</option>
                @foreach($data_items['schoolingUnits'] as $su)
                  <option value="{{ $su->id }}"
                          data-location="{{ $su->location }}"
                          @selected(old('schooling_unit_id', $data_items['data']->schooling_unit_id ?? '') == $su->id)>
                    {{ $su->name }}
                    ({{ ucfirst($su->location) }})
                  </option>
                @endforeach
                <option value="new" style="font-weight:bold;color:#007bff;border-top:2px solid #ccc;">
                  ➕ Create New Unit
                </option>
              </select>

              {{-- New-unit sub-panel (hidden until "Create New Unit" is chosen) --}}
              <div id="new_unit_container" style="display:none;margin-top:4px">
                <div class="new-unit-panel">
                  <div class="mb-1">
                    <label>Unit Name <span class="text-danger">*</span></label>
                    <input type="text"
                           name="new_unit_name"
                           id="new_unit_name"
                           class="form-control"
                           placeholder="e.g. CGSC, Fort Leavenworth"
                           value="{{ old('new_unit_name') }}">
                    @error('new_unit_name')
                      <div class="text-danger" style="font-size:.6rem">{{ $message }}</div>
                    @enderror
                  </div>
                  <div>
                    <label class="d-block mb-1">Location <span class="text-danger">*</span></label>
                    {{-- Hidden input carries the actual value submitted --}}
                    <input type="hidden" name="new_unit_location" id="new_unit_location" value="{{ old('new_unit_location', '') }}">
                    <button type="button" id="btn_local"
                            class="loc-btn {{ old('new_unit_location') === 'local' ? 'active-local' : '' }}"
                            data-val="local">
                      <i class="fas fa-map-marker-alt"></i> Local
                    </button>
                    <button type="button" id="btn_foreign"
                            class="loc-btn {{ old('new_unit_location') === 'foreign' ? 'active-foreign' : '' }}"
                            data-val="foreign">
                      <i class="fas fa-plane"></i> Foreign
                    </button>
                    @error('new_unit_location')
                      <div class="text-danger" style="font-size:.6rem">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

            @else
              <input type="text" class="form-control" value="{{ $data_items['data']->schoolingunits->name ?? '' }}" disabled>
            @endif
          </div>

          {{-- Loc/For (auto-filled from selected unit's location) --}}
          <div class="col" style="max-width:65px">
            <label>Loc/For</label>
            <input type="text" id="schooling_unit_location" name="school_location" class="form-control"
              value="{{ old('school_location', session('school_location', $data_items['data']->schoolingunits->location ?? '')) }}"
              readonly>
          </div>

          {{-- Date Completed --}}
          <div class="col" style="max-width:140px">
            <label>Date Completed</label>
            <input type="date" id="date_completed" name="date_completed" class="form-control"
              value="{{ old('date_completed', $op === 'create' ? '' : ($data_items['data']->date_completed ?? '')) }}"
              {{ $op === 'show' ? 'disabled' : '' }}>
          </div>

          {{-- Rating --}}
          <div class="col" style="max-width:95px">
            <label>Rating</label>
            <input type="number" step="any" name="rating" class="form-control"
              value="{{ old('rating', $op === 'create' ? '' : $data_items['data']->rating ?? '') }}"
              {{ $op === 'show' ? 'disabled' : '' }}>
          </div>

          {{-- Standing --}}
          <div class="col" style="max-width:80px">
            <label>Standing</label>
            <input type="number" name="standing" class="form-control"
              value="{{ old('standing', $op === 'create' ? '' : $data_items['data']->standing ?? '') }}"
              {{ $op === 'show' ? 'disabled' : '' }}>
          </div>

          {{-- Total --}}
          <div class="col" style="max-width:80px">
            <label>Total</label>
            <input type="number" name="total_student" class="form-control"
              value="{{ old('total_student', $op === 'create' ? '' : $data_items['data']->total_student ?? '') }}"
              {{ $op === 'show' ? 'disabled' : '' }}>
          </div>

        </div>
      </div>

      {{-- COMPUTED POINTS --}}
      <div class="sb sb-au">
        <div class="sb-lbl"><span class="dot"></span> Computed Points
          <span style="text-transform:none;font-weight:400;font-size:.58rem;color:#aaa;margin-left:4px">(auto-calculated)</span>
        </div>
        <input type="number" step="any" id="computed_points" class="form-control"
          value="{{ old('computed_points', $data_items['data']->computed_points ?? '') }}"
          hidden>
        <div class="row gx align-items-end">
          <div class="col" style="max-width:70px">
            <label>Rank</label>
            <input type="text" id="rank_during_completion" name="rank_during_completion" class="form-control"
              value="{{ old('rank_during_completion', $data_items['data']->rank_during_completion ?? '') }}"
              {{ $op === 'show' ? 'disabled' : 'readonly' }}>
          </div>
          @foreach(['seclt'=>'2LT','firstlt'=>'1LT','cpt'=>'CPT','maj'=>'MAJ','ltc'=>'LTC','col'=>'COL'] as $field => $label)
          <div class="col">
            <label>{{ $label }}</label>
            <input type="number" step="any" name="{{ $field }}" id="{{ $field }}" class="form-control"
              value="{{ old($field, $op === 'create' ? '' : number_format((float)($data_items['data']->{$field} ?? 0), 5, '.', '')) }}"
              {{ $op === 'show' ? 'disabled' : '' }} readonly>
          </div>
          @endforeach
        </div>
      </div>
      @endif

      <div class="sf-actions">
        <a href="{{ route("$config_data->module_route.index") }}" class="btn btn-back">
          <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        @if($op === 'create' && $showInputs)
          <button type="submit" class="btn btn-primary" name="action" value="Save">
            <i class="fas fa-save"></i> Save
          </button>
        @elseif($op === 'edit')
          <button type="submit" class="btn btn-warning" name="action" value="Update">
            <i class="fas fa-edit"></i> Update
          </button>
        @endif
      </div>
    </form>
  </div>
</div>

{{-- RELATED SCHOOLING RECORDS TABLE --}}
@php
  if ($data_items['operation_type'] === 'create' || $data_items['operation_type'] === 'edit') {
      $schoolingHistories = session('relatedSchoolings', $data_items['relatedSchoolings'] ?? collect([]));
      $currentPmCode = session('pm_code', old('pm_code', $data_items['data']->pm_code ?? ''));
  } else {
      $schoolingHistories = $data_items['relatedSchoolings'] ?? collect([]);
      $currentPmCode = $data_items['data']->pm_code ?? '';
  }
@endphp

@if($schoolingHistories && $schoolingHistories->count() > 0)
<div class="card rc mt-2">
  <div class="card-header"><h6><i class="fas fa-book me-1"></i> Schooling Records — {{ $currentPmCode }}</h6></div>
  <div class="card-body p-1">
    <div class="table-responsive">
      <table class="table table-sm table-bordered table-striped table-hover mb-0">
        <thead>
          <tr>
            <th>PM Code</th><th>Entry</th><th>Class</th><th>School/Unit</th><th>Category</th>
            <th>Completed</th><th>Rating</th><th>Standing</th><th>Total</th><th>Rank</th>
            <th>2LT</th><th>1LT</th><th>CPT</th><th>MAJ</th><th>LTC</th><th>COL</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($schoolingHistories as $history)
          <tr>
            <td class="small">{{ $history->pm_code }}</td>
            <td class="small">{{ $history->schoolingnames->name ?? '-' }}</td>
            <td class="small">{{ $history->classname ?? '-' }}</td>
            <td class="small">{{ $history->schoolingunits->name ?? '-' }}</td>
            <td class="small">{{ $history->assignments->name ?? '-' }}</td>
            <td class="small">{{ $history->date_completed }}</td>
            <td class="small">{{ $history->rating }}</td>
            <td class="small">{{ $history->standing }}</td>
            <td class="small">{{ $history->total_student }}</td>
            <td class="small">{{ $history->rank_during_completion }}</td>
            <td class="small">{{ number_format((float)$history->seclt,   5) }}</td>
            <td class="small">{{ number_format((float)$history->firstlt, 5) }}</td>
            <td class="small">{{ number_format((float)$history->cpt,     5) }}</td>
            <td class="small">{{ number_format((float)$history->maj,     5) }}</td>
            <td class="small">{{ number_format((float)$history->ltc,     5) }}</td>
            <td class="small">{{ number_format((float)$history->col,     5) }}</td>
            <td style="white-space:nowrap">
              @can($config_data->module_perm_name . '_show')
                <a class="btn btn-xs btn-primary py-0 px-1 small" href="{{ route('schoolings.show', $history->id) }}">View</a>
              @endcan
              @can($config_data->module_perm_name . '_edit')
                <a class="btn btn-xs btn-info py-0 px-1 small" href="{{ route('schoolings.edit', $history->id) }}">Edit</a>
              @endcan
              @can($config_data->module_perm_name . '_delete')
                <form action="{{ route('schoolings.destroy', $history->id) }}" method="POST"
                      onsubmit="return confirm('Delete?')" style="display:inline-block">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-xs btn-danger py-0 px-1"><i class="fas fa-trash"></i></button>
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

{{-- ASSIGNMENT HISTORY TABLE --}}
@php
  if ($data_items['operation_type'] === 'create') {
      $histories = session('relatedHistories', collect([]));
      $ahPmCode  = session('pm_code', old('pm_code', ''));
  } else {
      $histories = $data_items['relatedHistories'] ?? collect([]);
      $ahPmCode  = $data_items['data']->pm_code ?? '';
  }
@endphp

@if($histories && $histories->count() > 0)
<div class="card rc mt-2">
  <div class="card-header"><h6><i class="fas fa-history me-1"></i> Assignment History — {{ $ahPmCode }}</h6></div>
  <div class="card-body p-1">
    <div class="table-responsive">
      <table class="table table-sm table-bordered table-striped table-hover datatable-AssignmentHistory mb-0">
        <thead>
          <tr>
            <th>PM Code</th><th>Start</th><th>End</th><th>Designation</th><th>Sub unit</th>
            <th>Unit</th><th>PAMU</th><th>Category</th><th>Type</th><th>Assign Type</th>
            <th>Geography</th><th>Rank</th><th>Years</th><th>Actions</th>
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
            <td style="white-space:nowrap">
              @can($config_data->module_perm_name . '_show')
                <a class="btn btn-xs btn-primary py-0 px-1 small" href="{{ route('assignmenthistories.show', $history->id) }}">View</a>
              @endcan
              @can($config_data->module_perm_name . '_edit')
                <a class="btn btn-xs btn-info py-0 px-1 small" href="{{ route('assignmenthistories.edit', $history->id) }}">Edit</a>
              @endcan
              @can($config_data->module_perm_name . '_delete')
                <form action="{{ route('assignmenthistories.destroy', $history->id) }}" method="POST"
                      onsubmit="return confirm('Delete?')" style="display:inline-block">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-xs btn-danger py-0 px-1"><i class="fas fa-trash"></i></button>
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
  <script>
      document.addEventListener('DOMContentLoaded', function () {

        // ── Unit dropdown + Loc/For sync ──────────────────────────────────────────
        const unitSelect  = document.getElementById('schooling_unit_id');
        const locInput    = document.getElementById('schooling_unit_location');
        const newUnitBox  = document.getElementById('new_unit_container');
        const newUnitName = document.getElementById('new_unit_name');
        const newUnitLoc  = document.getElementById('new_unit_location');
        const btnLocal    = document.getElementById('btn_local');
        const btnForeign  = document.getElementById('btn_foreign');

        function syncLocation() {
          if (!unitSelect || !locInput) return;
          const opt = unitSelect.options[unitSelect.selectedIndex];
          locInput.value = opt ? (opt.dataset.location || '') : '';
        }

        function handleUnitChange() {
          if (!unitSelect) return;
          if (unitSelect.value === 'new') {
            if (newUnitBox) newUnitBox.style.display = 'block';
            if (newUnitName) newUnitName.focus();
            if (locInput) locInput.value = '';          // clear until user picks Local/Foreign
          } else {
            if (newUnitBox) newUnitBox.style.display = 'none';
            resetNewUnitPanel();
            syncLocation();
          }
          schedulePoints();
        }

        function resetNewUnitPanel() {
          if (newUnitName) newUnitName.value = '';
          if (newUnitLoc)  newUnitLoc.value  = '';
          if (btnLocal)    btnLocal.classList.remove('active-local');
          if (btnForeign)  btnForeign.classList.remove('active-foreign');
        }

        // Local / Foreign toggle buttons
        [btnLocal, btnForeign].forEach(btn => {
          if (!btn) return;
          btn.addEventListener('click', function () {
            const val = this.dataset.val;
            if (newUnitLoc) newUnitLoc.value = val;
            btnLocal?.classList.toggle('active-local',   val === 'local');
            btnForeign?.classList.toggle('active-foreign', val === 'foreign');
            // Mirror into Loc/For field so user sees it immediately
            if (locInput) locInput.value = val;
          });
        });

        if (unitSelect) {
          syncLocation();
          unitSelect.addEventListener('change', handleUnitChange);
          if (window.jQuery) jQuery(unitSelect).on('select2:select select2:clear', handleUnitChange);
        }

        // ── Early exit for show mode ──────────────────────────────────────────────
        const OP = @json($data_items['operation_type']);
        if (OP === 'show') return;

        // ── Element refs ──────────────────────────────────────────────────────────
        const assignmentSelect  = document.getElementById('assignment_id');
        const entrySelect       = document.getElementById('schoolingname_id');
        const newEntryContainer = document.getElementById('new_entry_container');
        const newEntryInput     = document.getElementById('new_entry_name');
        const dateEl            = document.getElementById('date_completed');
        const rankEl            = document.getElementById('rank_during_completion');
        const computedEl        = document.getElementById('computed_points');

        // ── Cascading: Assignment → Entry ─────────────────────────────────────────
        if (assignmentSelect && entrySelect) {

          async function loadEntriesByAssignment() {
            const assignmentId = assignmentSelect.value;
            if (newEntryContainer) newEntryContainer.style.display = 'none';
            if (newEntryInput) { newEntryInput.required = false; newEntryInput.value = ''; }

            if (!assignmentId) {
              entrySelect.innerHTML = '<option value="">-- Select Assignment First --</option>';
              entrySelect.disabled  = true;
              if (window.jQuery && jQuery(entrySelect).data('select2')) jQuery(entrySelect).trigger('change.select2');
              return;
            }
            entrySelect.disabled = false;

            try {
              const res     = await fetch(`{{ route('schoolings.getEntriesByAssignment') }}?assignment_id=${assignmentId}`,
                                          { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
              const entries = await res.json();
              const oldSelected = @json(old('schoolingname_id', $data_items['data']->schoolingname_id ?? ''));

              let opts = '<option value="">-- Select Entry --</option>';
              entries.forEach(e => {
                const sel = String(oldSelected) === String(e.id) ? 'selected' : '';
                opts += `<option value="${e.id}" ${sel}>${e.name}</option>`;
              });
              opts += '<option value="new" style="font-weight:bold;color:#007bff;border-top:2px solid #ccc;">➕ Create New Entry</option>';
              entrySelect.innerHTML = opts;

              if (window.jQuery && jQuery(entrySelect).data('select2')) jQuery(entrySelect).trigger('change.select2');
              if (entrySelect.value) handleEntryChange();
            } catch (err) {
              console.error('Entry load error:', err);
              entrySelect.innerHTML = '<option value="">-- Error loading entries --</option>';
            }
          }

          function handleEntryChange() {
            const isNew = entrySelect.value === 'new';
            if (newEntryContainer) newEntryContainer.style.display = isNew ? 'block' : 'none';
            if (newEntryInput) { newEntryInput.required = isNew; if (isNew) newEntryInput.focus(); else newEntryInput.value = ''; }
          }

          function clearDependentFields() {
            entrySelect.innerHTML = '<option value="">-- Select Assignment First --</option>';
            entrySelect.value = ''; entrySelect.disabled = true;
            if (window.jQuery && jQuery(entrySelect).data('select2')) jQuery(entrySelect).trigger('change.select2');
            if (newEntryContainer) newEntryContainer.style.display = 'none';
            if (newEntryInput) { newEntryInput.required = false; newEntryInput.value = ''; }

            const classEl = document.getElementById('classname'); if (classEl) classEl.value = '';

            // Reset unit dropdown and new-unit panel
            if (unitSelect) {
              unitSelect.value = '';
              if (window.jQuery && jQuery(unitSelect).data('select2')) jQuery(unitSelect).trigger('change.select2');
            }
            if (newUnitBox) newUnitBox.style.display = 'none';
            resetNewUnitPanel();
            if (locInput) locInput.value = '';
            if (dateEl)   dateEl.value   = '';
            if (rankEl)   rankEl.value   = '';

            ['rating','standing','total_student'].forEach(n => {
              const el = document.querySelector(`input[name="${n}"]`); if (el) el.value = '';
            });
            clearPointFields();
          }

          assignmentSelect.addEventListener('change', () => { clearDependentFields(); loadEntriesByAssignment(); });
          if (window.jQuery) {
            jQuery(assignmentSelect).off('select2:select select2:clear')
              .on('select2:select select2:clear', () => { clearDependentFields(); loadEntriesByAssignment(); });
          }
          entrySelect.addEventListener('change', handleEntryChange);
          if (window.jQuery) jQuery(entrySelect).on('select2:select select2:clear', handleEntryChange);

          if (assignmentSelect.value) loadEntriesByAssignment();
        }

        // ── Submit: enable disabled selects, remove stale required ───────────────
        const form = document.querySelector('form');
        if (form) {
          form.addEventListener('submit', function () {
            if (entrySelect && entrySelect.disabled) entrySelect.disabled = false;
            if (newEntryInput && newEntryContainer?.style.display === 'none') newEntryInput.required = false;
            // If not creating new unit, clear the hidden fields so they don't trigger server-side errors
            if (unitSelect && unitSelect.value !== 'new') {
              if (newUnitName) newUnitName.name  = '';   // strip from POST
              if (newUnitLoc)  newUnitLoc.name   = '';
            }
          });
        }

        // ── Helpers ───────────────────────────────────────────────────────────────
        function clearPointFields() {
          ['seclt','firstlt','cpt','maj','ltc','col'].forEach(n => {
            const el = document.getElementById(n); if (el) el.value = '';
          });
          if (computedEl) computedEl.value = '';
        }

        function getSchoolLocation() {
          return (locInput?.value || '').trim();
        }

        // ── Rank During Completion (AJAX) ─────────────────────────────────────────
        const pmCodeEl = document.getElementById('pm_code');

        async function fetchRank() {
          const pm_code        = pmCodeEl?.value || '';
          const date_completed = dateEl?.value   || '';
          if (!/^(\d{4})-(\d{2})-(\d{2})$/.test(date_completed) || parseInt(date_completed, 10) < 1900) {
            if (rankEl) rankEl.value = ''; return;
          }
          const url = new URL(@json(route('schoolings.rankDuringCompletion')));
          url.searchParams.set('pm_code', pm_code);
          url.searchParams.set('date_completed', date_completed);
          try {
            const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            if (rankEl) rankEl.value = data.rank ?? '';
            schedulePoints();
          } catch (e) { console.error('fetchRank error:', e); if (rankEl) rankEl.value = ''; }
        }

        let rankTimer = null;
        function scheduleRank() { clearTimeout(rankTimer); rankTimer = setTimeout(fetchRank, 300); }

        if (dateEl)   dateEl.addEventListener('change', scheduleRank);
        if (pmCodeEl) pmCodeEl.addEventListener('change', scheduleRank);
        if (window.jQuery && pmCodeEl && jQuery(pmCodeEl).data('select2'))
          jQuery(pmCodeEl).on('select2:select select2:clear', scheduleRank);

        // ── Compute Points (AJAX) ─────────────────────────────────────────────────
        const ratingEl   = document.querySelector('input[name="rating"]');
        const standingEl = document.querySelector('input[name="standing"]');
        const totalEl    = document.querySelector('input[name="total_student"]');

        async function fetchPoints() {
          const assignmentId   = assignmentSelect?.value || '';
          const schoolLocation = getSchoolLocation();
          if (!assignmentId || assignmentId === 'new') { clearPointFields(); return; }

          const params = new URLSearchParams({
            assignment_id:   assignmentId,
            school_location: schoolLocation,
            rating:          ratingEl?.value   || '0',
            standing:        standingEl?.value || '0',
            total_students:  totalEl?.value    || '0',
          });
          try {
            const res  = await fetch(`{{ route('schoolings.computePoints') }}?${params}`,
                                    { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const json = await res.json();
            ['seclt','firstlt','cpt','maj','ltc','col'].forEach(n => {
              const el = document.getElementById(n); if (el) el.value = json[n] ?? '';
            });
            const rankMap = {'2LT':'seclt','1LT':'firstlt','CPT':'cpt','MAJ':'maj','LTC':'ltc','COL':'col'};
            const cur = (rankEl?.value || '').toUpperCase().trim();
            if (computedEl) computedEl.value = (rankMap[cur] ? (json[rankMap[cur]] ?? '') : '');
          } catch (e) { console.error('fetchPoints error:', e); clearPointFields(); }
        }

        let pTimer = null;
        function schedulePoints() { clearTimeout(pTimer); pTimer = setTimeout(fetchPoints, 250); }

        [ratingEl, standingEl, totalEl].forEach(el => {
          if (!el) return;
          el.addEventListener('input',  schedulePoints);
          el.addEventListener('change', schedulePoints);
        });
        if (assignmentSelect) {
          assignmentSelect.addEventListener('change', schedulePoints);
          if (window.jQuery) jQuery(assignmentSelect).on('change', schedulePoints);
        }

        // Re-expose for fetchRank callback
        window._schedulePoints = schedulePoints;

        scheduleRank();
        schedulePoints();
      });
  </script>
@endsection