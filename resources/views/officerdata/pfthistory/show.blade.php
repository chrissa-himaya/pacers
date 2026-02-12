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
</style>

<div class="sf">
  <div class="card">
    <div class="sf-hdr">
      <h4><i class="fas fa-heartbeat me-1"></i>{{ $config_data->module_name }}
        @if(($data_items['operation_type'] ?? '') == 'show')<span class="badge-op view">View</span>
        @elseif(($data_items['operation_type'] ?? '') == 'edit')<span class="badge-op edit">Edit</span>
        @elseif(($data_items['operation_type'] ?? '') == 'create')<span class="badge-op create">New</span>@endif
      </h4>
    </div>
    <div class="sf-legend">
      <div class="li"><span class="sw sw-ro"></span> Read-only</div>
      <div class="li"><span class="sw sw-ed"></span> Your inputs</div>
      <div class="li"><span class="sw sw-au"></span> Auto-computed</div>
    </div>

    <div class="card-body py-1 px-2">
      @php
        $op = $data_items['operation_type'] ?? '';
        $hasOfficer = isset($data_items['officerData']) && !empty($data_items['officerData']?->NAME);
        $hasSessionOfficer = session('name');
        $showInputs = ($op === 'create' && $hasSessionOfficer) || ($op === 'edit') || ($op === 'show' && $hasOfficer);
      @endphp

      <form id="mainForm"
        action="{{ $op === 'create'
            ? route("$config_data->module_route.store")
            : route("$config_data->module_route.update", [$data_items['data']->id]) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @if($op === 'edit')
          @method('PUT')
        @endif

        <div class="sf-steps">
          <div class="sf-step on"><span class="sn">1</span>Officer</div>
          <div class="sf-step {{ $showInputs ? 'on' : '' }}"><span class="sn">2</span>PFT Data</div>
          <div class="sf-step {{ $showInputs ? 'on' : '' }}"><span class="sn">3</span>Points</div>
        </div>

        {{-- ═══ OFFICER SELECTION & INFORMATION ═══ --}}
        <div class="sb sb-ro">
          <div class="sb-lbl"><span class="dot"></span> Officer Selection &amp; Information</div>
          <div class="row gx align-items-end">
            <div class="col" style="min-width:170px;max-width:210px;">
              <label>PM Code <span class="text-danger">*</span></label>
              @php $current = old('pm_code', $data_items['data']->pm_code ?? ''); @endphp
              @if($op !== 'show')
                <div class="d-flex gap-1">
                  <select name="pm_code" id="pm_code" class="form-control select2" style="flex:1">
                    <option value="">— PM Code —</option>
                    @foreach(($data_items['pm_codes'] ?? []) as $pmcode)
                      <option value="{{ $pmcode }}" {{ $current == $pmcode ? 'selected' : '' }}>{{ $pmcode }}</option>
                    @endforeach
                  </select>
                  <button type="submit" class="btn btn-fetch" name="action" value="Fetch Data"><i class="fas fa-download"></i> Fetch</button>
                </div>
              @else
                <input type="text" class="form-control" value="{{ $current }}" disabled>
              @endif
            </div>

            <div class="col" style="max-width:60px">
              <label>Rank</label>
              <input type="text" class="form-control" value="{{ isset($data_items['officerData']) ? $data_items['officerData']->RANK : session('rank') }}" disabled>
            </div>

            <div class="col" style="min-width:140px">
              <label>Name</label>
              <input type="text" class="form-control" value="{{ isset($data_items['officerData']) ? $data_items['officerData']->NAME : session('name') }}" disabled>
            </div>

            <div class="col" style="max-width:70px">
              <label>AFPOS</label>
              <input type="text" class="form-control" value="{{ isset($data_items['officerData']) ? $data_items['officerData']->AFPOS : session('afpos') }}" disabled>
            </div>

            <div class="col" style="max-width:70px">
              <label>AFPSN</label>
              <input type="text" class="form-control" value="{{ isset($data_items['officerData']) ? $data_items['officerData']->AFPSN : session('afpsn') }}" disabled>
            </div>

            <div class="col" style="max-width:45px">
              <label>Sex</label>
              <input type="text" class="form-control" value="{{ isset($data_items['officerData']) ? $data_items['officerData']->SEX : session('sex') }}" disabled>
            </div>

            <div class="col" style="max-width:90px">
              <label>DOB</label>
              <input type="text" class="form-control" value="{{ isset($data_items['officerData']) ? $data_items['officerData']->DOB : session('dob') }}" disabled>
            </div>

            <div class="col" style="max-width:90px">
              <label>Date Ret</label>
              <input type="text" class="form-control" value="{{ isset($data_items['officerData']) ? $data_items['officerData']->RET : session('date_ret') }}" disabled>
            </div>
          </div>

          <div class="row gx mt-1">
            <div class="col-md-2">
              <label>DOR</label>
              <input type="text" class="form-control" value="{{ isset($data_items['officerData']) ? $data_items['officerData']->DOR : session('dor') }}" disabled>
            </div>
            <div class="col-md-2">
              <label>SOC</label>
              <input type="text" class="form-control" value="{{ isset($data_items['officerData']) ? $data_items['officerData']->SOC : session('soc') }}" disabled>
            </div>
            <div class="col-md-2">
              <label>SIG</label>
              <input type="text" class="form-control" value="{{ isset($data_items['officerData']) ? $data_items['officerData']->SIG : session('sig') }}" disabled>
            </div>
            <div class="col-md-3">
              <label>Current Designation</label>
              <input type="text" class="form-control" value="{{ isset($data_items['officerData']) ? ($data_items['officerData']->designations->name ?? '') : session('designation') }}" disabled>
            </div>
            <div class="col-md-3">
              <label>Current Unit</label>
              <input type="text" class="form-control" value="{{ isset($data_items['officerData']) ? ($data_items['officerData']->units->name ?? '') : session('unit') }}" disabled>
            </div>
          </div>
        </div>

        <hr>

        {{-- ═══ PFT DATA (shown after Fetch) ═══ --}}
        @if($showInputs)
          <div class="sb sb-ed">
            <div class="sb-lbl"><span class="dot"></span> PFT Test Details</div>
            <div class="row gx align-items-end">

              <div class="col" style="min-width:150px">
                <label>Entry</label>
                @if(in_array($op, ['create','edit']))
                  <input type="text" name="entry" id="entry" class="form-control" placeholder="e.g. Promo to CPT" value="{{ old('entry', $data_items['data']->entry ?? '') }}">
                @else
                  <input type="text" class="form-control" value="{{ $data_items['data']->entry ?? '-' }}" disabled>
                @endif
              </div>

              <div class="col" style="min-width:120px">
                <label>Rating (%) <span class="text-danger">*</span></label>
                @if(in_array($op, ['create','edit']))
                  <input type="number" id="rating" name="rating" class="form-control" step="0.01" min="0" max="100" placeholder="87.00" value="{{ old('rating', $data_items['data']->rating ?? '') }}">
                @else
                  <input type="text" class="form-control" value="{{ $data_items['data']->rating ?? '-' }}" disabled>
                @endif
              </div>

              <div class="col" style="max-width:140px">
                <label>Date Taken <span class="text-danger">*</span></label>
                @if(in_array($op, ['create','edit']))
                  <input type="date" id="date_taken" name="date_taken" class="form-control" value="{{ old('date_taken', $op === 'create' ? '' : ($data_items['data']->date_taken ?? '')) }}">
                @else
                  <input type="text" class="form-control" value="{{ $data_items['data']->date_taken ?? '-' }}" disabled>
                @endif
              </div>

              <div class="col" style="max-width:80px">
                <label>Rank</label>
                @if(in_array($op, ['create','edit']))
                  <input type="text" name="rank" id="rank" class="form-control"
                         value="{{ old('rank', $data_items['data']->rank ?? '') }}"
                         placeholder="Auto" readonly>
                @else
                  <input type="text" class="form-control" value="{{ $data_items['data']->rank ?? '-' }}" disabled>
                @endif
              </div>

            </div>

            <div class="row gx mt-2">
              <div class="col" style="min-width:200px">
                <label>Supervising Unit</label>
                @if(in_array($op, ['create','edit']))
                  <input type="text" name="supervising_unit" id="supervising_unit" class="form-control" placeholder="SSC, HHSG, PA" value="{{ old('supervising_unit', $data_items['data']->supervising_unit ?? '') }}">
                @else
                  <input type="text" class="form-control" value="{{ $data_items['data']->supervising_unit ?? '-' }}" disabled>
                @endif
              </div>

              <div class="col" style="max-width:100px">
                <label>Age</label>
                @if(in_array($op, ['create','edit']))
                  <input type="text" name="age" id="age" class="form-control" placeholder="25y 7m" value="{{ old('age', $data_items['data']->age ?? '') }}">
                @else
                  <input type="text" class="form-control" value="{{ $data_items['data']->age ?? '-' }}" disabled>
                @endif
              </div>

              <div class="col" style="max-width:80px">
                <label>Profile</label>
                @if(in_array($op, ['create','edit']))
                  <input type="text" name="profile" id="profile" class="form-control" placeholder="P1" value="{{ old('profile', $data_items['data']->profile ?? '') }}">
                @else
                  <input type="text" class="form-control" value="{{ $data_items['data']->profile ?? '-' }}" disabled>
                @endif
              </div>
            </div>
          </div>

          <div class="sb sb-au">
            <div class="sb-lbl">
              <span class="dot"></span> Computed Points
              <span style="text-transform:none;font-weight:400;font-size:.58rem;color:#aaa;margin-left:4px">(auto-calculated from rating &amp; rank)</span>
            </div>
            <div class="row gx align-items-end">
              <div class="col" style="max-width:140px">
                <label>Points</label>
                <input type="number" step="any" id="points_display" name="points" class="form-control"
                  value="{{ old('points', $op === 'create' ? '' : ($data_items['data']->points ?? '')) }}"
                  {{ $op === 'show' ? 'disabled' : 'readonly' }}>
              </div>
            </div>
          </div>
        @endif

        <div class="sf-actions">
          <a href="{{ route("$config_data->module_route.index") }}" class="btn btn-back"><i class="fas fa-arrow-left me-1"></i> Back</a>
          @if($op === 'create' && $showInputs)
            <button type="submit" class="btn btn-save" name="action" value="Save"><i class="fas fa-save me-1"></i> Save</button>
          @elseif($op === 'edit')
            <button type="submit" class="btn btn-update" name="action" value="Update"><i class="fas fa-edit me-1"></i> Update</button>
          @endif
        </div>
      </form>
    </div>
  </div>

  {{-- ═══ RELATED PFT HISTORY TABLE ═══ --}}
  @php
    if (($data_items['operation_type'] ?? '') === 'create') {
        $pftRecords = session('relatedPftRecords', $data_items['relatedPftRecords'] ?? collect([]));
        $currentPmCode  = session('pm_code', old('pm_code', $data_items['data']->pm_code ?? ''));
    } else {
        $pftRecords = $data_items['relatedPftRecords'] ?? collect([]);
        $currentPmCode  = $data_items['data']->pm_code ?? '';
    }
  @endphp

  @if($pftRecords && $pftRecords->count() > 0)
    <div class="card rc mt-2">
      <div class="card-header"><h6><i class="fas fa-heartbeat me-1"></i> PFT History — {{ $currentPmCode }}</h6></div>
      <div class="card-body p-1">
        <div class="table-responsive">
          <table class="table table-sm table-bordered table-striped table-hover mb-0">
            <thead>
              <tr>
                <th>PM Code</th>
                <th>Entry</th>
                <th>Rating (%)</th>
                <th>Date Taken</th>
                <th>Rank</th>
                <th>Unit</th>
                <th>Points</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pftRecords as $record)
                <tr>
                  <td class="small">{{ $record->pm_code }}</td>
                  <td class="small">{{ $record->entry ?? '-' }}</td>
                  <td class="small">{{ $record->rating !== null ? number_format($record->rating, 2) . '%' : '-' }}</td>
                  <td class="small">{{ $record->date_taken ? $record->date_taken->format('d-M-Y') : '-' }}</td>
                  <td class="small">{{ $record->rank ?? '-' }}</td>
                  <td class="small">{{ $record->supervising_unit ?? '-' }}</td>
                  <td class="small"><strong>{{ $record->points !== null ? number_format($record->points, 2) : '-' }}</strong></td>
                  <td style="white-space:nowrap;">
                    @can($config_data->module_perm_name . '_show')
                      <a class="btn btn-xs btn-primary py-0 px-1 small" href="{{ route('pfthistories.show', $record->id) }}">View</a>
                    @endcan
                    @can($config_data->module_perm_name . '_edit')
                      <a class="btn btn-xs btn-info py-0 px-1 small" href="{{ route('pfthistories.edit', $record->id) }}">Edit</a>
                    @endcan
                    @can($config_data->module_perm_name . '_delete')
                      <form action="{{ route('pfthistories.destroy', $record->id) }}" method="POST" onsubmit="return confirm('Delete?');" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-danger py-0 px-1" title="Delete"><i class="fas fa-trash"></i></button>
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

      const pmCodeEl    = document.getElementById('pm_code');
      const ratingEl    = document.getElementById('rating');
      const dateTakenEl = document.getElementById('date_taken');
      const rankEl      = document.getElementById('rank');
      const pointsEl    = document.getElementById('points_display');

      const rankLookupUrl   = @json($data_items['rank_lookup_url'] ?? null);
      const pointsLookupUrl = @json($data_items['points_lookup_url'] ?? null);

      async function safeJson(res) {
        const ct = res.headers.get('content-type') || '';
        const text = await res.text();

        if (!res.ok) {
          console.error('Request failed:', res.status, text);
          return null;
        }
        if (!ct.includes('application/json')) {
          console.error('Expected JSON but got:', ct, text.slice(0, 200));
          return null;
        }
        try { return JSON.parse(text); } catch (e) {
          console.error('JSON parse error:', e, text.slice(0, 200));
          return null;
        }
      }

      async function autoSetPoints() {
        if (!pointsLookupUrl || !ratingEl || !rankEl || !pointsEl) return;

        const rating = ratingEl.value;
        const rank = rankEl.value;

        if (rating === '' || rank === '') { // allow rating like "8" but empty should stop
          pointsEl.value = '';
          return;
        }

        const url = new URL(pointsLookupUrl, window.location.origin);
        url.searchParams.set('rating', rating);
        url.searchParams.set('rank', rank);

        const res = await fetch(url.toString(), {
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          cache: 'no-store'
        });

        const json = await safeJson(res);
        if (!json) { pointsEl.value = ''; return; }

        const pts = parseFloat(json.points);
        pointsEl.value = Number.isFinite(pts) ? pts.toFixed(2) : '';
      }

      async function autoSetRank() {
        if (!rankLookupUrl || !pmCodeEl || !dateTakenEl || !rankEl) return;

        const pmCode = pmCodeEl.value;
        const dateTaken = dateTakenEl.value;

        if (!pmCode || !dateTaken) {
          rankEl.value = '';
          pointsEl && (pointsEl.value = '');
          return;
        }

        const url = new URL(rankLookupUrl, window.location.origin);
        url.searchParams.set('pm_code', pmCode);
        url.searchParams.set('date', dateTaken);

        const res = await fetch(url.toString(), {
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          cache: 'no-store'
        });

        const json = await safeJson(res);
        if (!json) { rankEl.value = ''; return; }

        rankEl.value = json.rank || '';
        await autoSetPoints();
      }

      let timer = null;
      function scheduleRank() {
        clearTimeout(timer);
        timer = setTimeout(autoSetRank, 300);
      }
      function schedulePoints() {
        clearTimeout(timer);
        timer = setTimeout(autoSetPoints, 300);
      }

      dateTakenEl?.addEventListener('change', scheduleRank);

      pmCodeEl?.addEventListener('change', scheduleRank);
      if (window.jQuery && pmCodeEl && jQuery(pmCodeEl).data('select2')) {
        jQuery(pmCodeEl).on('select2:select select2:clear', scheduleRank);
      }

      ratingEl?.addEventListener('input', schedulePoints);

      // initial
      if (pmCodeEl?.value && dateTakenEl?.value) scheduleRank();
    });
  </script>
@endsection