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
  /* FIX #2: Make auto-computed readonly fields clearly visible with darker text */
  .sb-au .form-control[readonly]{background-color:#e3f2e7;border-color:#b0d9b8;color:#1a4d2b;font-family:'JetBrains Mono',monospace;font-size:.78rem;font-weight:600}
  .sf select.form-control{height:28px;padding:1px 6px}
  .btn-fetch{background:linear-gradient(135deg,#2d5a8e,#3a7bd5);color:#fff;border:none;border-radius:4px;padding:3px 12px;font-size:.76rem;font-weight:600;height:28px;white-space:nowrap}
  .btn-fetch:hover{box-shadow:0 3px 10px rgba(45,90,142,.3);color:#fff}
  .sf-actions{display:flex;justify-content:flex-end;gap:8px;background:#dce0e5;border-top:1px solid #e9ecef;border-radius:0 0 10px 10px;padding:6px 12px}
  .btn-save{background:linear-gradient(135deg,#2d8a4e,#3db562);color:#fff;border:none;border-radius:5px;padding:5px 20px;font-weight:600;font-size:.8rem;height:35px;margin-top:4px}
  .btn-save:hover{box-shadow:0 3px 10px rgba(45,138,78,.3);color:#fff}
  .btn-update{background:linear-gradient(135deg,#c77c0a,#e6a21a);color:#fff;border:none;border-radius:5px;padding:5px 20px;font-weight:600;font-size:.8rem;height:35px;margin-top:4px}
  .btn-update:hover{box-shadow:0 3px 10px rgba(199,124,10,.3);color:#fff}
  .btn-back{background:#a9adb1;color:#0d0f11;border:none;border-radius:5px;padding:5px 16px;font-weight:600;font-size:.8rem;height:35px;margin-top:4px}
  .btn-back:hover{background:#ffffff;color:#333}
  .rc{border:none;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden}
  .rc .card-header{background:linear-gradient(135deg,#3a3f47,#4a5568);color:#fff;padding:7px 14px;border:none}
  .rc .card-header h6{font-weight:600;font-size:.78rem;margin:0}
  .rc .table th{background:#f7f8fa;font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.3px;color:#555;border-bottom:2px solid #e2e6ea;padding:4px 3px}
  .rc .table td{font-size:.72rem;padding:3px;vertical-align:middle}.rc .table tbody tr:hover{background:#f0f7ff}
  .select2-container--default .select2-selection--single{border-radius:4px!important;border-color:#ced4da!important;height:28px!important;min-height:28px!important}
  .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:26px!important;font-size:.76rem}
  .select2-container--default .select2-selection--single .select2-selection__arrow{height:26px!important}
  .gx{--bs-gutter-x:.35rem;--bs-gutter-y:.2rem}
  .pts-pill{display:inline-block;background:#e0f2fe;color:#0369a1;border:1px solid #7dd3fc;border-radius:20px;padding:2px 12px;font-family:'JetBrains Mono',monospace;font-size:.78rem;font-weight:700}
  /* FIX #3: Visual indicator when award type is auto-linked */
  .award-type-auto-note{font-size:.6rem;color:#3d7a4f;font-style:italic;margin-top:2px;display:none}
  .award-type-auto-note.visible{display:block}
</style>

  <div class="sf">
  <div class="card">
    <div class="sf-hdr">
      <h4><i class="fas fa-medal me-1"></i>{{ $config_data->module_name }}
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

      {{-- FIX #1: In edit mode, the Fetch button uses a separate form that POSTs to the update
           route with action=Fetch Data. The main form (below) only handles Save/Update.
           We use JavaScript to intercept the Fetch button and submit via AJAX or a hidden form
           so the main form is not submitted. --}}

      @php
        $op = $data_items['operation_type'] ?? '';
        $hasOfficer = isset($data_items['officerData']) && !empty($data_items['officerData']?->NAME);
        $hasSessionOfficer = session('name');
        $showInputs = ($op === 'create' && $hasSessionOfficer) || ($op === 'edit') || ($op === 'show' && $hasOfficer);
      @endphp

      {{-- FIX #1: Separate fetch form for edit mode --}}
      @if($op === 'edit')
        <form id="fetchForm" action="{{ route("$config_data->module_route.update", [$data_items['data']->id]) }}" method="POST" style="display:none;">
          @csrf
          @method('PUT')
          <input type="hidden" name="action" value="Fetch Data">
          <input type="hidden" name="pm_code" id="fetch_pm_code" value="">
          {{-- Preserve other field values during fetch --}}
          <input type="hidden" name="award_id" id="fetch_award_id" value="">
          <input type="hidden" name="award_type" id="fetch_award_type" value="">
          <input type="hidden" name="date" id="fetch_date" value="">
          <input type="hidden" name="go_number" id="fetch_go_number" value="">
          <input type="hidden" name="points" id="fetch_points" value="">
        </form>
      @endif

      <form id="mainForm"
        action="{{ $op === 'create'
            ? route("$config_data->module_route.store")
            : route("$config_data->module_route.update", [$data_items['data']->id]) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @if($op == "edit")
          @method('PUT')
        @endif

        <div class="sf-steps">
          <div class="sf-step on"><span class="sn">1</span>Officer</div>
          <div class="sf-step {{ $showInputs ? 'on' : '' }}"><span class="sn">2</span>Award</div>
          <div class="sf-step {{ $showInputs ? 'on' : '' }}"><span class="sn">3</span>Points</div>
        </div>

        {{-- ═══ OFFICER SELECTION & INFORMATION ═══ --}}
        <div class="sb sb-ro">
          <div class="sb-lbl"><span class="dot"></span> Officer Selection &amp; Information</div>
          <div class="row gx align-items-end">
            <div class="col" style="min-width:170px;max-width:210px;">
              <label>PM Code <span class="text-danger">*</span></label>
              @php
                $current = old('pm_code', $data_items['data']->pm_code ?? '');
              @endphp
              @if($op !== "show")
                <div class="d-flex gap-1">
                  <select name="pm_code" id="pm_code" class="form-control select2" style="flex:1">
                    <option value="">— PM Code —</option>
                    @foreach(($data_items['pm_codes'] ?? []) as $pmcode)
                      <option value="{{ $pmcode }}" {{ $current == $pmcode ? 'selected' : '' }}>
                        {{ $pmcode }}
                      </option>
                    @endforeach
                  </select>
                  {{-- FIX #1: In edit mode, use a regular button (not submit) to trigger fetch --}}
                  @if($op === 'edit')
                    <button type="button" class="btn btn-fetch" id="fetchBtn">
                      <i class="fas fa-download"></i> Fetch
                    </button>
                  @else
                    <button type="submit" class="btn btn-fetch" name="action" value="Fetch Data">
                      <i class="fas fa-download"></i> Fetch
                    </button>
                  @endif
                </div>
              @else
                <input type="text" class="form-control" value="{{ $current }}" disabled>
              @endif
            </div>
            <div class="col" style="max-width:60px">
              <label>Rank</label>
              <input type="text" class="form-control"
                value="{{ isset($data_items['officerData']) ? $data_items['officerData']->RANK : session('rank') }}" disabled>
            </div>
            <div class="col" style="min-width:140px">
              <label>Name</label>
              <input type="text" class="form-control"
                value="{{ isset($data_items['officerData']) ? $data_items['officerData']->NAME : session('name') }}" disabled>
            </div>
            <div class="col" style="max-width:70px">
              <label>AFPOS</label>
              <input type="text" class="form-control"
                value="{{ isset($data_items['officerData']) ? $data_items['officerData']->AFPOS : session('afpos') }}" disabled>
            </div>
            <div class="col" style="max-width:70px">
              <label>AFPSN</label>
              <input type="text" class="form-control"
                value="{{ isset($data_items['officerData']) ? $data_items['officerData']->AFPSN : session('afpsn') }}" disabled>
            </div>
            <div class="col" style="max-width:45px">
              <label>Sex</label>
              <input type="text" class="form-control"
                value="{{ isset($data_items['officerData']) ? $data_items['officerData']->SEX : session('sex') }}" disabled>
            </div>
            <div class="col" style="max-width:90px">
              <label>DOB</label>
              <input type="text" class="form-control"
                value="{{ isset($data_items['officerData']) ? $data_items['officerData']->DOB : session('dob') }}" disabled>
            </div>
            <div class="col" style="max-width:90px">
              <label>Date Ret</label>
              <input type="text" class="form-control"
                value="{{ isset($data_items['officerData']) ? $data_items['officerData']->RET : session('date_ret') }}" disabled>
            </div>
          </div>

          <div class="row gx mt-1">
            <div class="col-md-2">
              <label>DOR</label>
              <input type="text" class="form-control"
                value="{{ isset($data_items['officerData']) ? $data_items['officerData']->DOR : session('dor') }}" disabled>
            </div>
            <div class="col-md-2">
              <label>SOC</label>
              <input type="text" class="form-control"
                value="{{ isset($data_items['officerData']) ? $data_items['officerData']->SOC : session('soc') }}" disabled>
            </div>
            <div class="col-md-2">
              <label>SIG</label>
              <input type="text" class="form-control"
                value="{{ isset($data_items['officerData']) ? $data_items['officerData']->SIG : session('sig') }}" disabled>
            </div>
            <div class="col-md-3">
              <label>Current Designation</label>
              <input type="text" class="form-control"
                value="{{ isset($data_items['officerData']) ? ($data_items['officerData']->designations->name ?? '') : session('designation') }}" disabled>
            </div>
            <div class="col-md-3">
              <label>Current Unit</label>
              <input type="text" class="form-control"
                value="{{ isset($data_items['officerData']) ? ($data_items['officerData']->units->name ?? '') : session('unit') }}" disabled>
            </div>
          </div>
        </div>
        <hr>

        {{-- ═══ AWARD DETAILS (shown after Fetch) ═══ --}}
        @if($showInputs)

        <div class="sb sb-ed">
          <div class="sb-lbl"><span class="dot"></span> Award Details</div>
          <div class="row gx align-items-end">

            {{-- Award Code --}}
            <div class="col" style="min-width:180px">
              <label>Award Code <span class="text-danger">*</span></label>
              @if(in_array($op, ['create', 'edit']))
                <select name="award_id" id="award_id" class="form-control select2">
                  <option value="">— Select Award —</option>
                  @foreach($data_items['awards'] as $id => $code)
                    <option value="{{ $id }}"
                      @selected(old('award_id', $data_items['data']->award_id ?? '') == $id)>
                      {{ $code }}
                    </option>
                  @endforeach
                  <option value="new" style="font-weight:bold;color:#007bff;border-top:2px solid #ccc;">➕ Create New Award</option>
                </select>
                <div id="new_award_container" style="display:none;margin-top:3px;">
                  <div class="row gx">
                    <div class="col">
                      <input type="text" name="new_award_code" id="new_award_code" class="form-control" value="{{ old('new_award_code') }}">
                    </div>
                    <div class="col">
                      <input type="text" name="new_award_name" id="new_award_name" class="form-control" value="{{ old('new_award_name') }}">
                    </div>
                    <div class="col" style="max-width:90px">
                      <input type="number" step="any" name="new_award_points" id="new_award_points" class="form-control"
                             value="{{ old('new_award_points') }}">
                    </div>
                  </div>
                </div>
              @else
                <input type="text" class="form-control"
                       value="{{ $data_items['data']->awards->code ?? '-' }}" disabled>
              @endif
            </div>

            {{-- Award Type --}}
            <div class="col" style="min-width:220px">
              <label>Award Type</label>
              @if(in_array($op, ['create', 'edit']))
                <select name="award_type" id="award_type" class="form-control select2">
                  <option value="">— Select Type —</option>
                  @foreach($data_items['award_types'] as $id => $name)
                    <option value="{{ $id }}"
                      @selected(old('award_type', $data_items['data']->award_type ?? '') == $id)>
                      {{ $name }}
                    </option>
                  @endforeach
                  <option value="new" style="font-weight:bold;color:#007bff;border-top:2px solid #ccc;">➕ Create New Award Type</option>
                </select>
                {{-- FIX #3: Note shown when award type is auto-linked to new award --}}
                <div class="award-type-auto-note" id="awardTypeAutoNote">
                  <i class="fas fa-info-circle"></i> Award type will be set automatically from the new award name.
                </div>
                <div id="new_award_type_container" style="display:none;margin-top:3px;">
                  <div class="row gx">
                    <div class="col">
                      <input type="text" name="new_award_type_code" id="new_award_type_code" class="form-control"
                              value="{{ old('new_award_type_code') }}">
                    </div>
                    <div class="col">
                      <input type="text" name="new_award_type_name" id="new_award_type_name" class="form-control"
                             value="{{ old('new_award_type_name') }}">
                    </div>
                    <div class="col" style="max-width:90px">
                      <input type="number" step="any" name="new_award_type_points" id="new_award_type_points" class="form-control"
                              value="{{ old('new_award_type_points') }}">
                    </div>
                  </div>
                </div>
              @else
                <input type="text" class="form-control"
                       value="{{ $data_items['data']->awardType->name ?? '-' }}" disabled>
              @endif
            </div>

            {{-- Date --}}
            <div class="col" style="max-width:140px">
              <label>Date</label>
              <input type="date" id="award_date" name="date" class="form-control"
                value="{{ old('date', $op === 'create' ? '' : ($data_items['data']->date ?? '')) }}"
                {{ $op === 'show' ? 'disabled' : '' }}>
            </div>

            {{-- GO Number --}}
            <div class="col" style="max-width:120px">
              <label>GO #</label>
              <input type="text" name="go_number" class="form-control"
                value="{{ old('go_number', $op === 'create' ? '' : ($data_items['data']->go_number ?? '')) }}"
                {{ $op === 'show' ? 'disabled' : '' }}>
            </div>
          </div>
        </div>

        {{-- ═══ AUTO-COMPUTED SECTION ═══ --}}
        <div class="sb sb-au">
          <div class="sb-lbl">
            <span class="dot"></span> Computed Award Info
            <span style="text-transform:none;font-weight:400;font-size:.58rem;color:#aaa;margin-left:4px">(auto-filled from date &amp; award)</span>
          </div>
          <div class="row gx align-items-end">
            {{-- FIX #2: Rank at Date — ensure the value is displayed properly --}}
            <div class="col" style="max-width:80px">
              <label>Rank at Date</label>
              <input type="text" id="rank_display" name="rank_at_date_display" class="form-control"
                value="{{ old('rank_at_date_display', $data_items['data']->dateranks->ranks->code ?? '') }}"
                {{ $op === 'show' ? 'disabled' : 'readonly' }}
                style="font-weight:700;">
              <input type="hidden" id="date_rank_id" name="date_rank_id"
                value="{{ old('date_rank_id', $data_items['data']->date_rank_id ?? '') }}">
            </div>
            <div class="col" style="max-width:110px">
              <label>Points</label>
              <input type="number" step="any" id="points_display" name="points" class="form-control"
                value="{{ old('points', $op === 'create' ? '' : ($data_items['data']->points ?? '')) }}"
                {{ $op === 'show' ? 'disabled' : 'readonly' }}>
            </div>
          </div>
        </div>

        @endif

        <div class="sf-actions">
          <a href="{{ route("$config_data->module_route.index") }}" class="btn btn-back">
            <i class="fas fa-arrow-left me-1"></i> Back
          </a>
          @if($op === 'create' && $showInputs)
            <button type="submit" class="btn btn-save" name="action" value="Save">
              <i class="fas fa-save me-1"></i> Save
            </button>
          @elseif($op === 'edit')
            <button type="submit" class="btn btn-update" name="action" value="Update">
              <i class="fas fa-edit me-1"></i> Update
            </button>
          @endif
        </div>
      </form>
    </div>
  </div>

  {{-- ═══ RELATED AWARD HISTORY TABLE ═══ --}}
  @php
    if ($data_items['operation_type'] === 'create') {
        $awardHistories = session('relatedAwards', $data_items['relatedAwards'] ?? collect([]));
        $currentPmCode  = session('pm_code', old('pm_code', $data_items['data']->pm_code ?? ''));
    } else {
        $awardHistories = $data_items['relatedAwards'] ?? collect([]);
        $currentPmCode  = $data_items['data']->pm_code ?? '';
    }
  @endphp

  @if($awardHistories && $awardHistories->count() > 0)
  <div class="card rc mt-2">
    <div class="card-header">
      <h6><i class="fas fa-medal me-1"></i> Award History — {{ $currentPmCode }}</h6>
    </div>
    <div class="card-body p-1">
      <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped table-hover mb-0">
          <thead>
            <tr>
              <th>PM Code</th>
              <th>Award Code</th>
              <th>Award Type</th>
              <th>Date</th>
              <th>GO #</th>
              <th>Rank at Date</th>
              <th>Points</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($awardHistories as $history)
            <tr>
              <td class="small">{{ $history->pm_code }}</td>
              <td class="small">{{ $history->awards->code ?? '-' }}</td>
              <td class="small">{{ $history->awardType->name ?? '-' }}</td>
              <td class="small">{{ $history->date }}</td>
              <td class="small">{{ $history->go_number ?? '-' }}</td>
              <td class="small">{{ $history->dateranks->ranks->code ?? '-' }}</td>
              <td class="small"><strong>{{ $history->points }}</strong></td>
              <td style="white-space:nowrap;">
                @can($config_data->module_perm_name . '_show')
                  <a class="btn btn-xs btn-primary py-0 px-1 small" href="{{ route('awardhistories.show', $history->id) }}">View</a>
                @endcan
                @can($config_data->module_perm_name . '_edit')
                  <a class="btn btn-xs btn-info py-0 px-1 small" href="{{ route('awardhistories.edit', $history->id) }}">Edit</a>
                @endcan
                @can($config_data->module_perm_name . '_delete')
                  <form action="{{ route('awardhistories.destroy', $history->id) }}" method="POST"
                    onsubmit="return confirm('Delete?');" style="display:inline-block;">
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
  const OP = @json($data_items['operation_type'] ?? '');
  if (OP === 'show') return;

  const pmCodeEl         = document.getElementById('pm_code');
  const dateEl           = document.getElementById('award_date');
  const rankEl           = document.getElementById('rank_display');
  const dateRankIdEl     = document.getElementById('date_rank_id');
  const pointsEl         = document.getElementById('points_display');
  const awardIdEl        = document.getElementById('award_id');
  const awardTypeEl      = document.getElementById('award_type');
  const awardTypeAutoNote = document.getElementById('awardTypeAutoNote');

  // ── FIX #1: Separate fetch handler for edit mode ──
  if (OP === 'edit') {
    const fetchBtn      = document.getElementById('fetchBtn');
    const fetchForm     = document.getElementById('fetchForm');
    const fetchPmCode   = document.getElementById('fetch_pm_code');
    const fetchAwardId  = document.getElementById('fetch_award_id');
    const fetchAwardType = document.getElementById('fetch_award_type');
    const fetchDate     = document.getElementById('fetch_date');
    const fetchGoNumber = document.getElementById('fetch_go_number');
    const fetchPoints   = document.getElementById('fetch_points');

    if (fetchBtn && fetchForm) {
      fetchBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        // Copy current form values into the hidden fetch form
        fetchPmCode.value   = pmCodeEl ? pmCodeEl.value : '';
        fetchAwardId.value  = awardIdEl ? awardIdEl.value : '';
        fetchAwardType.value = awardTypeEl ? awardTypeEl.value : '';
        fetchDate.value     = dateEl ? dateEl.value : '';
        fetchGoNumber.value = document.querySelector('[name="go_number"]')?.value || '';
        fetchPoints.value   = pointsEl ? pointsEl.value : '';

        // Submit the separate fetch form (not the main form)
        fetchForm.submit();
      });
    }
  }

  // ── New Award creation toggle ──
  const newAwardContainer     = document.getElementById('new_award_container');
  const newAwardTypeContainer = document.getElementById('new_award_type_container');
  const newAwardNameInput     = document.getElementById('new_award_name');

  // Track whether "Create New Award" is selected
  let isNewAward = false;

  if (awardIdEl) {
    awardIdEl.addEventListener('change', function () {
      isNewAward = (this.value === 'new');

      if (newAwardContainer) {
        newAwardContainer.style.display = isNewAward ? 'block' : 'none';
        document.querySelectorAll('#new_award_container input').forEach(el => {
          el.required = isNewAward;
        });
      }

      // FIX #3: When creating new award, disable award type and show note
      if (isNewAward) {
        if (awardTypeEl) {
          // Clear and disable award type selection
          awardTypeEl.value = '';
          awardTypeEl.disabled = true;
          if (window.jQuery && jQuery(awardTypeEl).data('select2')) {
            jQuery(awardTypeEl).val('').trigger('change.select2');
            jQuery(awardTypeEl).prop('disabled', true).trigger('change.select2');
          }
        }
        // Hide new award type container if it was open
        if (newAwardTypeContainer) {
          newAwardTypeContainer.style.display = 'none';
        }
        // Show auto-note
        if (awardTypeAutoNote) {
          awardTypeAutoNote.classList.add('visible');
        }
      } else {
        // Re-enable award type selection
        if (awardTypeEl) {
          awardTypeEl.disabled = false;
          if (window.jQuery && jQuery(awardTypeEl).data('select2')) {
            jQuery(awardTypeEl).prop('disabled', false).trigger('change.select2');
          }
        }
        // Hide auto-note
        if (awardTypeAutoNote) {
          awardTypeAutoNote.classList.remove('visible');
        }
      }

      schedulePointsFetch();
    });

    if (window.jQuery) jQuery(awardIdEl).on('select2:select select2:clear', function () {
      awardIdEl.dispatchEvent(new Event('change'));
    });

    // Check initial state on page load (e.g. if old('award_id') was 'new')
    if (awardIdEl.value === 'new') {
      awardIdEl.dispatchEvent(new Event('change'));
    }
  }

  if (awardTypeEl) {
    awardTypeEl.addEventListener('change', function () {
      if (newAwardTypeContainer) {
        newAwardTypeContainer.style.display = (this.value === 'new') ? 'block' : 'none';
        document.querySelectorAll('#new_award_type_container input').forEach(el => {
          el.required = (this.value === 'new');
        });
      }
    });
    if (window.jQuery) jQuery(awardTypeEl).on('select2:select select2:clear', function () {
      awardTypeEl.dispatchEvent(new Event('change'));
    });
  }

  // FIX #3: Before form submit, re-enable award_type so its value is sent
  // (disabled fields are not submitted). Since the backend handles auto-populating
  // the award_type when a new award is created, we can just leave it empty.
  const mainForm = document.getElementById('mainForm');
  if (mainForm) {
    mainForm.addEventListener('submit', function () {
      // Re-enable award_type so the form includes it (even if empty)
      if (awardTypeEl && awardTypeEl.disabled) {
        awardTypeEl.disabled = false;
      }
    });
  }

  // ── FIX #2: Rank-at-date autofill (mirrors XLOOKUP logic) ──
  async function fetchDateRank() {
    const pm_code = pmCodeEl ? pmCodeEl.value : '';
    const date    = dateEl ? dateEl.value : '';

    if (!pm_code || !/^\d{4}-\d{2}-\d{2}$/.test(date)) {
      if (rankEl) rankEl.value = '';
      if (dateRankIdEl) dateRankIdEl.value = '';
      return;
    }

    const url = new URL(@json(route('awardhistories.dateRankAtDate')));
    url.searchParams.set('pm_code', pm_code);
    url.searchParams.set('date', date);

    try {
      const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' });
      const json = await res.json();

      // FIX #2: Explicitly set the rank value and ensure it's visible
      if (rankEl) {
        rankEl.value = json.rank ?? '';
        rankEl.style.color = '#1a4d2b';
        rankEl.style.fontWeight = '700';
      }
      if (dateRankIdEl) {
        dateRankIdEl.value = json.date_rank_id ?? '';
      }

      schedulePointsFetch();
    } catch (e) {
      console.error('fetchDateRank error:', e);
      if (rankEl)       rankEl.value      = '';
      if (dateRankIdEl) dateRankIdEl.value = '';
    }
  }

  // ── Points autofill from awards table ──
  async function fetchPoints() {
    const awardId = awardIdEl ? awardIdEl.value : '';
    if (!awardId || awardId === 'new') {
      // FIX #3: For new awards, try to use the new_award_points field
      if (awardId === 'new') {
        const newPointsEl = document.getElementById('new_award_points');
        if (pointsEl && newPointsEl && newPointsEl.value) {
          pointsEl.value = newPointsEl.value;
        }
      } else {
        if (pointsEl) pointsEl.value = '';
      }
      return;
    }

    const url = new URL(@json(route('awardhistories.pointsForAward')));
    url.searchParams.set('award_id', awardId);

    try {
      const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const json = await res.json();
      if (pointsEl) pointsEl.value = json.points ?? '';
    } catch (e) {
      if (pointsEl) pointsEl.value = '';
    }
  }

  let rankTimer = null, pointsTimer = null;

  function scheduleDateRankFetch() {
    clearTimeout(rankTimer);
    rankTimer = setTimeout(fetchDateRank, 300);
  }

  function schedulePointsFetch() {
    clearTimeout(pointsTimer);
    pointsTimer = setTimeout(fetchPoints, 300);
  }

  if (dateEl)   dateEl.addEventListener('change',   scheduleDateRankFetch);
  if (pmCodeEl) pmCodeEl.addEventListener('change',  scheduleDateRankFetch);

  if (window.jQuery && pmCodeEl && jQuery(pmCodeEl).data('select2')) {
    jQuery(pmCodeEl).on('select2:select select2:clear', scheduleDateRankFetch);
  }

  // FIX #3: Sync new award points to the computed points field in real-time
  const newAwardPointsEl = document.getElementById('new_award_points');
  if (newAwardPointsEl) {
    newAwardPointsEl.addEventListener('input', function () {
      if (isNewAward && pointsEl) {
        pointsEl.value = this.value || '';
      }
    });
  }

  // Trigger on load (for edit mode — fetch rank and points based on existing data)
  scheduleDateRankFetch();
  schedulePointsFetch();
});
</script>
@endsection