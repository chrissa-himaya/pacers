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
                value="{{isset($data_items['officerData']) ? $data_items['officerData']->designations->name : session('designation')}}" disabled>
            </div>
            <div class="col-md-3">
              <label>Current Unit</label>
              <input type="text" class="form-control"
                value="{{isset($data_items['officerData']) ? $data_items['officerData']->units->name : session('unit')}}" disabled>
            </div>
          </div>
        </div>
        <hr>
        
        <!-- CAREER ADVISER INPUTS -->
          @if(
          ($op === 'create' && $hasSessionOfficer)
          || ($op === 'edit' && $hasOfficer)
          || ($op === 'show' && $hasOfficer)
          )

      <div class="sb sb-ed">
        <div class="sb-lbl"><span class="dot"></span> Schooling Details</div>
          <div class="row gx align-items-end">
            <div class="col" style="min-width:170px">
              <label>Assignment Category <span class="text-danger">*</span></label>
              @if(in_array($op, ['create','edit']))
                <select name="assignment_id" id="assignment_id" class="form-control select2">
                  <option value="">— Category —</option>
                  @foreach($data_items['assignments'] as $id => $entry)
                    <option value="{{ $id }}" 
                      @selected(old('assignment_id', $data_items['data']->assignment_id ?? session('assignment_id')) == $id)>
                      {{ $entry }}
                    </option>
                  @endforeach
                </select>
                @error('assignment_id')
                  <div class="text-danger" style="font-size:.6rem">{{ $message }}</div>
                @enderror
              @else
                <input type="text" class="form-control" 
                       value="{{ $data_items['data']->assignments->name ?? '' }}" disabled>
              @endif
            </div>

            <div class="col" style="min-width:170px">
              <label>Entry <span class="text-danger">*</span></label>
              @if(in_array($op, ['create','edit']))
                <select name="schoolingname_id" id="schoolingname_id" class="form-control select2" disabled>
                  <option value="">— Select Assignment First —</option>
                </select>
                
                <div id="new_entry_container" style="display:none; margin-top:3px;">
                  <input type="text" 
                         name="new_entry_name" 
                         id="new_entry_name" 
                         class="form-control" 
                         value="{{ old('new_entry_name') }}">
                  @error('new_entry_name')
                    <div class="text-danger" style="font-size:.6rem">{{ $message }}</div>
                  @enderror
                </div>
                
                @error('schoolingname_id')
                  <div class="text-danger" style="font-size:.6rem">{{ $message }}</div>
                @enderror
              @else
                <input type="text" class="form-control" 
                       value="{{ $data_items['data']->schoolingnames->name ?? '' }}" disabled>
              @endif
            </div>

            <div class="col" style="max-width:90px">
              <label>Class</label>
              <input type="text" id="classname" name="classname" class="form-control"
                value="{{ old('classname', $op === 'create' ? '' : ($data_items['data']->classname ?? '')) }}"
                
                {{ $op === 'show' ? 'disabled' : '' }}>
            </div>

            <div class="col" style="min-width:150px">
              <label>Schooling Unit</label>
              @if(in_array($op, ['create','edit']))
                <select name="schooling_unit_id" id="schooling_unit_id" class="form-control select2">
                  <option value="">— Unit —</option>
                  @foreach($data_items['schoolingUnits'] as $su)
                    <option value="{{ $su->id }}" data-location="{{ $su->location }}"
                      @selected(old('schooling_unit_id', $data_items['data']->schooling_unit_id ?? session('schooling_unit_id')) == $su->id)>
                      {{ $su->name }}
                    </option>
                  @endforeach
                </select>
              @else
                <input type="text" class="form-control" value="{{ $data_items['data']->schoolingunits->name ?? '' }}" disabled>
              @endif
            </div>

            <div class="col" style="max-width:65px">
              <label>Loc/For</label>
              <input type="text" id="schooling_unit_location" name="school_location" class="form-control"
                value="{{ old('school_location', session('school_location', $data_items['data']->schoolingunits->location ?? '')) }}"
                readonly>
            </div>

            <div class="col" style="max-width:140px">
              <label>Date Completed</label>
              <input type="date" id="date_completed" name="date_completed" class="form-control"
                value="{{ old('date_completed', $op === 'create' ? '' : ($data_items['data']->date_completed ?? '')) }}"
                {{ $op === 'show' ? 'disabled' : '' }}>
            </div>

            <div class="col" style="max-width:95px">
              <label>Rating</label>
              <input type="number" 
                     step="any"
                     name="rating" 
                     class="form-control"
                     value="{{ old('rating', $op === 'create' ? '' : $data_items['data']->rating ?? '') }}"
                     {{ $op === 'show' ? 'disabled' : '' }}>
            </div>

            <div class="col" style="max-width:80px">
              <label>Standing</label>
              <input type="number" 
                     name="standing" 
                     class="form-control"
                     value="{{ old('standing', $op === 'create' ? '' : $data_items['data']->standing ?? '') }}"
                     {{ $op === 'show' ? 'disabled' : '' }}>
            </div>

            <div class="col" style="max-width:80px">
              <label>Total</label>
              <input type="number" 
                     name="total_student" 
                     class="form-control"
                     value="{{ old('total_student', $op === 'create' ? '' : $data_items['data']->total_student ?? '') }}"
                     {{ $op === 'show' ? 'disabled' : '' }}>
            </div>
          </div>
        </div>

          {{-- COMPUTED POINTS --}}
        <div class="sb sb-au">
          <div class="sb-lbl"><span class="dot"></span> Computed Points <span style="text-transform:none;font-weight:400;font-size:.58rem;color:#aaa;margin-left:4px">(auto-calculated)</span></div>
          <input type="number" step="any" id="computed_points" class="form-control"
            value="{{ old('computed_points', $data_items['data']->computed_points ?? '') }}"
            hidden>
          <div class="row gx align-items-end">
            <div class="col" style="max-width:70px">
              <label>Rank</label>
              <input type="text" id="rank_during_completion" name="rank_during_completion" class="form-control"
                value="{{ old('rank_during_completion', $data_items['data']->rank_during_completion ?? '') }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : 'readonly' }}>
            </div>

            <div class="col">
              <label>2LT</label>
              <input type="number" step="any" name="seclt" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? number_format($data_items["data"]->seclt, 5, '.', '') : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }} readonly>
            </div>

            <div class="col">
              <label>1LT</label>
              <input type="number" step="any" name="firstlt" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? number_format($data_items["data"]->firstlt, 5, '.', '') : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }} readonly>
            </div>

            <div class="col">
              <label>CPT</label>
              <input type="number" step="any" name="cpt" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? number_format($data_items["data"]->cpt, 5, '.', '') : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }} readonly>
            </div>

            <div class="col">
              <label>MAJ</label>
              <input type="number" step="any" name="maj" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? number_format($data_items["data"]->maj, 5, '.', '') : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }} readonly>
            </div>

            <div class="col">
              <label>LTC</label>
              <input type="number" step="any" name="ltc" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? number_format($data_items["data"]->ltc, 5, '.', '') : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }} readonly>
            </div>

            <div class="col">
              <label>COL</label>
              <input type="number" step="any" name="col" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? number_format($data_items["data"]->col, 5, '.', '') : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }} readonly>
            </div>
          </div>
        </div>
        @endif

          <div class="sf-actions">
          <a href="{{ route("$config_data->module_route.index") }}" class="btn btn-back"><i class="fas fa-arrow-left me-1"></i> Back</a>
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

    {{-- ══════════════════════════════════════════════════════════════
       RELATED SCHOOLING RECORDS TABLE
       Shows all schooling records for the same PM Code.
       Visible during: create (after Fetch Data), edit, and show.
       ══════════════════════════════════════════════════════════════ --}}
  @php
    if ($data_items['operation_type'] === 'create') {
        $schoolingHistories = session('relatedSchoolings', $data_items['relatedSchoolings'] ?? collect([]));
        $currentPmCode = session('pm_code', old('pm_code', $data_items['data']->pm_code ?? ''));
    } else {
        $schoolingHistories = $data_items['relatedSchoolings'] ?? collect([]);
        $currentPmCode = $data_items['data']->pm_code ?? '';
    }
  @endphp

  @if($schoolingHistories && $schoolingHistories->count() > 0)
  <div class="card rc mt-2">
    <div class="card-header">
      <h6><i class="fas fa-book me-1"></i> Schooling Records — {{ $currentPmCode }}</h6>
    </div>
    <div class="card-body p-1">
      <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped table-hover mb-0">
          <thead>
            <tr>
              <th>PM Code</th>
              <th>Entry</th>
              <th>Class</th>
              <th>School/Unit</th>
              <th>Category</th>
              <th>Completed</th>
              <th>Rating</th>
              <th>Standing</th>
              <th>Total</th>
              <th>Rank</th>
              <th>2LT</th>
              <th>1LT</th>
              <th>CPT</th>
              <th>MAJ</th>
              <th>LTC</th>
              <th>COL</th>
              <th>Actions</th>
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
              <td class="small">{{ number_format($history->seclt, 5) }}</td>
              <td class="small">{{ number_format($history->firstlt, 5) }}</td>
              <td class="small">{{ number_format($history->cpt, 5) }}</td>
              <td class="small">{{ number_format($history->maj, 5) }}</td>
              <td class="small">{{ number_format($history->ltc, 5) }}</td>
              <td class="small">{{ number_format($history->col, 5) }}</td>
              <td style="white-space: nowrap;">
                @can($config_data->module_perm_name . '_show')
                  <a class="btn btn-xs btn-primary py-0 px-1 small" href="{{ route('schoolings.show', $history->id) }}">
                    View
                  </a>
                @endcan
                @can($config_data->module_perm_name . '_edit')
                  <a class="btn btn-xs btn-info py-0 px-1 small" href="{{ route('schoolings.edit', $history->id) }}">
                    Edit
                  </a>
                @endcan
                @can($config_data->module_perm_name . '_delete')
                  <form action="{{ route('schoolings.destroy', $history->id) }}" 
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

  {{-- Assignment History Records Table (existing) --}}
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
    <div class="card-header">
      <h6><i class="fas fa-history me-1"></i> Assignment History — {{ $ahPmCode }}</h6>
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
@endsection

@section('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const select = document.getElementById('schooling_unit_id');
      const locInput = document.getElementById('schooling_unit_location');

      // Keep syncLocation accessible to other handlers
      function syncLocation() {
        if (!select || !locInput) return;
        const opt = select.options[select.selectedIndex];
        const location = opt ? (opt.dataset.location || '') : '';
        locInput.value = location;
      }

      if (select && locInput) {
        syncLocation();
        select.addEventListener('change', syncLocation);
        if (window.jQuery) jQuery(select).on('change', syncLocation);
      }

      // CASCADING DROPDOWN: Assignment → Entry
      const OP = @json($data_items['operation_type']);
      if (OP === 'show') return;

      const assignmentSelect = document.getElementById('assignment_id');
      const entrySelect = document.getElementById('schoolingname_id');
      const newEntryContainer = document.getElementById('new_entry_container');
      const newEntryInput = document.getElementById('new_entry_name');

      // Elements used by clearing logic (declare BEFORE the clear function)
      const dateEl = document.getElementById('date_completed');
      const rankEl = document.getElementById('rank_during_completion');
      const computedEl = document.getElementById('computed_points');

        if (assignmentSelect && entrySelect) {
          async function loadEntriesByAssignment() {
              const assignmentId = assignmentSelect.value;

              // Always reset new-entry UI when reloading entries
              if (newEntryContainer) newEntryContainer.style.display = 'none';
              if (newEntryInput) {
                  newEntryInput.required = false;
                  newEntryInput.value = '';
              }

              if (!assignmentId) {
                  entrySelect.innerHTML = '<option value="">-- Select Assignment First --</option>';
                  entrySelect.disabled = true;
                  if (window.jQuery && jQuery(entrySelect).data('select2')) {
                      jQuery(entrySelect).trigger('change.select2');
                  }
                  return;
              }

              entrySelect.disabled = false;

              try {
                  const response = await fetch(`{{ route('schoolings.getEntriesByAssignment') }}?assignment_id=${assignmentId}`, {
                      headers: { 'X-Requested-With': 'XMLHttpRequest' }
                  });
                  const entries = await response.json();

                  const oldSelected = @json(old('schoolingname_id', $data_items['data']->schoolingname_id ?? ''));

                  let options = '<option value="">-- Select Entry --</option>';
                  entries.forEach(entry => {
                      const selected = (String(oldSelected) === String(entry.id)) ? 'selected' : '';
                      options += `<option value="${entry.id}" ${selected}>${entry.name}</option>`;
                  });

                  options += '<option value="new" style="font-weight:bold; color:#007bff; border-top:2px solid #ccc;">➕ Create New Entry</option>';

                  entrySelect.innerHTML = options;

                  // Re-initialize select2 properly after replacing options
                  if (window.jQuery && jQuery(entrySelect).data('select2')) {
                      jQuery(entrySelect).trigger('change.select2');
                  }

                  // If old value was pre-selected, trigger the entry change handler
                  if (entrySelect.value) {
                      handleEntryChange();
                  }
              } catch (error) {
                  console.error('Error loading entries:', error);
                  entrySelect.innerHTML = '<option value="">-- Error loading entries --</option>';
              }
          }

          function handleEntryChange() {
              if (entrySelect.value === 'new') {
                  if (newEntryContainer) newEntryContainer.style.display = 'block';
                  if (newEntryInput) {
                      newEntryInput.required = true;
                      newEntryInput.focus();
                  }
              } else {
                  if (newEntryContainer) newEntryContainer.style.display = 'none';
                  if (newEntryInput) {
                      newEntryInput.required = false;
                      newEntryInput.value = '';
                  }
              }
          }

          // Clears everything below Assignment Category
          function clearDependentFieldsOnAssignmentChange() {
              // ENTRY
              entrySelect.innerHTML = '<option value="">-- Select Assignment First --</option>';
              entrySelect.value = '';
              entrySelect.disabled = true;

              if (window.jQuery && jQuery(entrySelect).data('select2')) {
                  jQuery(entrySelect).trigger('change.select2');
              }

              // NEW ENTRY UI
              if (newEntryContainer) newEntryContainer.style.display = 'none';
              if (newEntryInput) {
                  newEntryInput.required = false;
                  newEntryInput.value = '';
              }

              // CLASS
              const classEl = document.getElementById('classname');
              if (classEl) classEl.value = '';

              // SCHOOLING UNIT
              const suEl = document.getElementById('schooling_unit_id');
              if (suEl) {
                  suEl.value = '';
                  if (window.jQuery && jQuery(suEl).data('select2')) {
                      jQuery(suEl).trigger('change.select2');
                  }
              }

              // LOCAL/FOREIGN
              const locEl = document.getElementById('schooling_unit_location');
              if (locEl) locEl.value = '';

              // DATE COMPLETED + RANK DURING COMPLETION
              if (dateEl) dateEl.value = '';
              if (rankEl) rankEl.value = '';

              // INPUTS
              const ratingEl = document.querySelector('input[name="rating"]');
              const standingEl = document.querySelector('input[name="standing"]');
              const totalEl = document.querySelector('input[name="total_student"]');
              if (ratingEl) ratingEl.value = '';
              if (standingEl) standingEl.value = '';
              if (totalEl) totalEl.value = '';

              // COMPUTED POINTS
              if (computedEl) computedEl.value = '';

              // RANK POINTS
              ['seclt','firstlt','cpt','maj','ltc','col'].forEach(n => {
                  const el = document.querySelector(`input[name="${n}"]`);
                  if (el) el.value = '';
              });

              syncLocation();
          }

          // Assignment change: clear first, then load new entries
          assignmentSelect.addEventListener('change', function () {
              clearDependentFieldsOnAssignmentChange();
              loadEntriesByAssignment();
          });

          if (window.jQuery) {
              jQuery(assignmentSelect).off('select2:select select2:clear');
              jQuery(assignmentSelect).on('select2:select select2:clear', function () {
                  clearDependentFieldsOnAssignmentChange();
                  loadEntriesByAssignment();
              });
          }

          // Entry change handler (native + select2)
          entrySelect.addEventListener('change', handleEntryChange);
          if (window.jQuery) {
              jQuery(entrySelect).on('select2:select select2:clear', handleEntryChange);
          }

          // Initial load (edit page / when old value exists)
          if (assignmentSelect.value) {
              loadEntriesByAssignment();
          }
      }

      // === Enable all disabled selects before form submit ===
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function () {
                // Enable entry select so its value gets submitted
                if (entrySelect && entrySelect.disabled) {
                    entrySelect.disabled = false;
                }
                // If new entry is hidden, ensure required is removed
                if (newEntryInput && newEntryContainer && newEntryContainer.style.display === 'none') {
                    newEntryInput.required = false;
                }
            });
        }

      // ---------- rank during completion autofill ----------
      const pmCodeEl = document.getElementById('pm_code');

      if (!pmCodeEl || !dateEl || !rankEl) return;

      async function fetchRank() {
        const pm_code = pmCodeEl.value;
        const date_completed = dateEl.value;

        console.log('fetchRank inputs:', { pm_code, date_completed });

        if (!/^(\d{4})-(\d{2})-(\d{2})$/.test(date_completed) ||
            parseInt(date_completed.slice(0, 4), 10) < 1900) {
          console.warn('Skipping fetchRank; invalid date:', date_completed);
          rankEl.value = '';
          return;
        }

        const url = new URL(@json(route('schoolings.rankDuringCompletion')));
        url.searchParams.set('pm_code', pm_code);
        url.searchParams.set('date_completed', date_completed);

        try {
          const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
          const data = await res.json();
          console.log('fetchRank response:', data);
          rankEl.value = data.rank ?? '';
          scheduleFetchPoints();
        } catch (e) {
          console.error('fetchRank error:', e);
          rankEl.value = '';
        }
      }

      let t = null;
      function scheduleFetchRank() {
        clearTimeout(t);
        t = setTimeout(fetchRank, 300);
      }

      dateEl.addEventListener('change', scheduleFetchRank);
      pmCodeEl.addEventListener('change', scheduleFetchRank);

      if (window.jQuery && jQuery(pmCodeEl).data('select2')) {
        jQuery(pmCodeEl).on('select2:select select2:clear', scheduleFetchRank);
      }

      scheduleFetchRank();

      // ---------- compute schooling points ----------
      // computedEl already declared above
      let scheduleFetchPoints = function(){};

      if (OP !== 'show' && computedEl) {
        const ratingEl = document.querySelector('input[name="rating"]');
        const standingEl = document.querySelector('input[name="standing"]');
        const totalEl = document.querySelector('input[name="total_student"]');
        const assignmentEl = document.querySelector('select[name="assignment_id"]');
        const locationEl = document.querySelector('input[name="school_location"]');

        const clearRankFields = () => {
          ['seclt','firstlt','cpt','maj','ltc','col'].forEach(n => {
            const el = document.querySelector(`input[name="${n}"]`);
            if (el) el.value = '';
          });
        };

        function getSchoolLocation() {
          const fromInput = (locationEl?.value || '').trim();
          if (fromInput) return fromInput;

          if (select) {
            const opt = select.options[select.selectedIndex];
            return (opt?.dataset?.location || '').trim();
          }
          return '';
        }

        async function fetchPoints() {
          const rating = ratingEl?.value ?? '';
          const standing = standingEl?.value ?? '';
          const totalStudents = totalEl?.value ?? '';
          const assignmentId = assignmentEl?.value ?? '';
          const schoolLocation = getSchoolLocation();

          console.log('fetchPoints inputs:', { assignmentId, schoolLocation, rating, standing, totalStudents });

          if (!assignmentId || assignmentId === 'new') {
            computedEl.value = '';
            clearRankFields();
            return;
          }

          const params = new URLSearchParams({
            rating: rating || '0',
            standing: standing || '0',
            total_students: totalStudents || '0',
            assignment_id: assignmentId,
            school_location: schoolLocation,
          });

          try {
            const res = await fetch(`{{ route('schoolings.computePoints') }}?${params.toString()}`, {
              headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            console.log('computePoints response:', json);

            const set = (name, val) => {
              const el = document.querySelector(`input[name="${name}"]`);
              if (el) el.value = (val ?? '');
            };

            set('seclt', json.seclt);
            set('firstlt', json.firstlt);
            set('cpt', json.cpt);
            set('maj', json.maj);
            set('ltc', json.ltc);
            set('col', json.col);

            const currentRank = (rankEl?.value || '').toUpperCase().trim();
            const map = { '2LT':'seclt', '1LT':'firstlt', 'CPT':'cpt', 'MAJ':'maj', 'LTC':'ltc', 'COL':'col' };
            computedEl.value = (map[currentRank] ? (json[map[currentRank]] ?? '') : '');
          } catch (e) {
            console.error('fetchPoints error:', e);
            computedEl.value = '';
            clearRankFields();
          }
        }

        let pTimer = null;
        scheduleFetchPoints = function () {
          clearTimeout(pTimer);
          pTimer = setTimeout(fetchPoints, 250);
        };

        // Regular inputs
        [ratingEl, standingEl, totalEl].forEach(el => {
          if (!el) return;
          el.addEventListener('input', scheduleFetchPoints);
          el.addEventListener('change', scheduleFetchPoints);
        });

        // Assignment dropdown triggers point recalculation
        if (assignmentEl) {
          assignmentEl.addEventListener('change', scheduleFetchPoints);
          if (window.jQuery) jQuery(assignmentEl).on('change', scheduleFetchPoints);
        }

        // Schooling Unit triggers location sync + recalculation
        if (select) {
          select.addEventListener('change', function() {
            syncLocation();
            scheduleFetchPoints();
          });
          if (window.jQuery) {
            jQuery(select).on('change', function() {
              syncLocation();
              scheduleFetchPoints();
            });
          }
        }

        scheduleFetchPoints();
      }
    });
  </script>
@endsection