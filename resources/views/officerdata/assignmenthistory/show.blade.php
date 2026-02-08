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
        <div class="row">
          <div class="col-md-2">
            <label class="small mb-1">PM Code</label>
            <div class="input-group input-group-sm">
              @php
                $key = 'pm_code';
                $current = old('pm_code', $data_items['data']->pm_code ?? '');
              @endphp

              @if($data_items["operation_type"] !== "show")
                <select name="pm_code" id="pm_code" class="form-control form-control-sm select2">
                  <option value="">-- Select --</option>
                  @foreach(($data_items['pm_codes'] ?? []) as $pmcode)
                    <option value="{{ $pmcode }}" {{ $current == $pmcode ? 'selected' : '' }}>
                      {{ $pmcode }}
                    </option>
                  @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-info" name="action" value="Fetch Data">
                  Fetch
                </button>
              @else
                <input type="text" class="form-control form-control-sm" value="{{ $current }}" disabled>
              @endif
            </div>
          </div>
          <div class="col-md-1">
            <label class="small mb-1">Rank</label>
            <input type="text" class="form-control form-control-sm"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->RANK : session('rank')}}" disabled>
          </div>
          <div class="col-md-2">
            <label class="small mb-1">Name</label>
            <input type="text" class="form-control form-control-sm"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->NAME : session('name')}}" disabled>
          </div>
          <div class="col-md-1">
            <label class="small mb-1">AFPOS</label>
            <input type="text" class="form-control form-control-sm"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->AFPOS : session('afpos')}}" disabled>
          </div>
          <div class="col-md-1">
            <label class="small mb-1">AFPSN</label>
            <input type="text" class="form-control form-control-sm"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->AFPSN : session('afpsn')}}" disabled>
          </div>
          <div class="col-md-1">
            <label class="small mb-1">Sex</label>
            <input type="text" class="form-control form-control-sm"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->SEX : session('sex')}}" disabled>
          </div>
          <div class="col-md-1">
            <label class="small mb-1">DOB</label>
            <input type="text" class="form-control form-control-sm"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->DOB : session('dob')}}" disabled>
          </div>
          <div class="col-md-1">
            <label class="small mb-1">DOB</label>
            <input type="text" class="form-control form-control-sm"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->DOB : session('dob')}}" disabled>
          </div>
          <div class="col-md-1">
            <label class="small mb-1">Date Ret</label>
            <input type="text" class="form-control form-control-sm"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->RET : session('date_ret')}}" disabled>
          </div>
        </div>

        <div class="row mt-2">
          <div class="col-md-1">
            <label class="small mb-1">DOR</label>
            <input type="text" class="form-control form-control-sm"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->DOR : session('dor')}}" disabled>
          </div>
          <div class="col-md-2">
            <label class="small mb-1">SOC</label>
            <input type="text" class="form-control form-control-sm"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->SOC : session('soc')}}" disabled>
          </div>
          <!-- <div class="col-md-2">
            <label class="small mb-1">Type</label>
            <input type="text" class="form-control form-control-sm"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->TYPE : session('type')}}" disabled>
          </div> -->
          <div class="col-md-2">
            <label class="small mb-1">SIG</label>
            <input type="text" class="form-control form-control-sm"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->SIG : session('sig')}}" disabled>
          </div>
          <div class="col-md-3">
            <label class="small mb-1">Current Designation</label>
            <input type="text" class="form-control form-control-sm"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->designations->name : session('designation')}}" disabled>
          </div>
          <div class="col-md-3">
            <label class="small mb-1">Current Unit</label>
            <input type="text" class="form-control form-control-sm"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->units->name : session('unit')}}" disabled>
          </div>
        </div>
        <hr>
        <!-- CAREER ADVISER INPUTS -->
        @php
          $op = $data_items['operation_type'] ?? '';
          $hasOfficer = isset($data_items['officerData']) && !empty($data_items['officerData']?->NAME);
          $hasSessionOfficer = session('name');
        @endphp

        @if(
            ($op === 'create' && $hasSessionOfficer)
            || ($op === 'edit' && $hasOfficer)
            || ($op === 'show' && $hasOfficer)
          )

          <div class="row mt-3">
            <div class="col-md-3">
              <label>Designation</label>
              @if(in_array($op, ['create', 'edit']))
                <select name="designation_id" class="form-control select2">
                  <option value="">-</option>
                  @foreach($data_items['designations'] as $id => $entry)
                    <option value="{{ $id }}" @selected(old('designation_id', $data_items['data']->designation_id ?? session('designation_id')) == $id)>
                      {{ $entry }}
                    </option>
                  @endforeach
                </select>
              @else
                <input type="text" class="form-control" value="{{ $data_items['data']->designations->name ?? '' }}"
                  disabled>
              @endif
            </div>
            <div class="col-md-3">
              <label>Sub unit</label>
                <input type="text" id="subunit" name="subunit" class="form-control"
                value="{{ old('subunit', $op === 'create' ? '' : ($data_items['data']->subunit ?? '')) }}"
                {{ $op === 'show' ? 'disabled' : '' }}>
            </div>
            <div class="col-md-3">
              <label>Unit</label>
              @if(in_array($op, ['create','edit']))
                <select name="unit_id" id="unit_id" class="form-control select2">
                  <option value="">-</option>
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
              @else
                <input type="text" class="form-control" value="{{ $data_items['data']->units->name ?? '' }}" disabled>
              @endif
            </div>

            <div class="col-md-3">
              <label>PAMU</label>
              <input type="text" class="form-control" id="pamu_name" name="pamu_display" readonly
                    value="{{ old('pamu_display', $data_items['data']->pamus->name ?? '') }}">
              <input type="hidden" id="pamu_id" name="pamu_id"
                    value="{{ old('pamu_id', $data_items['data']->pamu_id ?? '') }}">
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-3">
              <label>Category</label>
              @if(in_array($op, ['create', 'edit']))
                <select name="assignment_id" id="assignment_id" class="form-control select2">
                  <option value="">-</option>
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

            <div class="col-md-3">
            <label>Primary/Secondary/Special</label>

            @if(in_array($op, ['create','edit']))
              <select name="pri_sec_spec" class="form-control select2">
                <option value="">-- Select Type --</option>
                <option value="primary"   @selected(old('pri_sec_spec', $data_items['data']->pri_sec_spec ?? '') == 'primary')>Primary</option>
                <option value="secondary" @selected(old('pri_sec_spec', $data_items['data']->pri_sec_spec ?? '') == 'secondary')>Secondary</option>
                <option value="special"   @selected(old('pri_sec_spec', $data_items['data']->pri_sec_spec ?? '') == 'special')>Special</option>
              </select>
            @else
              <input type="text" class="form-control"
                value="{{ ucfirst($data_items['data']->pri_sec_spec ?? '') }}" disabled>
            @endif
          </div>    
            
            <div class="col-md-3">
              <label>Assignment Type</label>

              @php
                $selectedAssignmentType = old('assignment_type', $data_items['data']->assignment_type ?? '');
              @endphp

              @if(in_array($op, ['create', 'edit']))
                <select name="assignment_type" class="form-control select2">
                  <option value="">-</option>
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
            
            <div class="col-md-3">
              <label>Geography</label>
              @if(in_array($op, ['create','edit']))
                <select name="geography" class="form-control select2">
                  <option value="">-- Select Geography --</option>
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

          <div class="row mt-3">
            <div class="col-md-3">
              <label>Start date</label>
                <input type="date" id="start_date" name="start_date" class="form-control"
                value="{{ old('start_date', $op === 'create' ? '' : ($data_items['data']->start_date ?? '')) }}"
                {{ $op === 'show' ? 'disabled' : '' }}>
            </div>
            <div class="col-md-3">
              <label>End date</label>
              <input type="date" id="end_date" name="end_date" class="form-control"
                value="{{ old('end_date', $op === 'create' ? '' : ($data_items['data']->end_date ?? '')) }}"
                {{ $op === 'show' ? 'disabled' : '' }}>
            </div>
            <div class="col">
              <label>Rank</label>
              <input type="text" id="rank_during_completion" name="rank_during_completion" class="form-control"
                value="{{ old('rank_during_completion', $data_items['data']->rank_during_completion ?? '') }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : 'readonly' }}>
            </div>

            <div class="col-md-3">
              <label>Year earned</label>
              <input type="text" id="year_earned" name="year_earned" class="form-control"
                value="{{ old('year_earned', $data_items['data']->year_earned ?? '') }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : 'readonly' }}>

                <div class="text-danger small mt-1" id="year_earned_error">
                  @error('year_earned') {{ $message }} @enderror
                </div>
            </div>
          </div>
          <div class="mt-4 text-right">
            @if(in_array($op, ['create', 'edit']))
              <button type="submit" class="btn btn-primary" name="action" value="Save">Save</button>
            @endif
        @endif
          <a href="{{ route("$config_data->module_route.index") }}" class="btn btn-secondary">
            Back
          </a>
        </div>
      </form>
    </div>
  </div>

 {{-- Assignment History Records Table --}}
    @php
      if ($data_items['operation_type'] === 'create') {
          $histories = session('relatedHistories', collect([]));
          $currentPmCode = session('pm_code', old('pm_code', ''));
      } else {
          $histories = $data_items['relatedHistories'] ?? collect([]);
          $currentPmCode = $data_items['data']->pm_code ?? '';
      }
    @endphp

    @if($histories && $histories->count() > 0)
    <div class="card mt-3">
      <div class="card-header py-2">
        <h6 class="mb-0">Related Assignment History (PM Code: {{ $currentPmCode }})</h6>
      </div>
      <div class="card-body p-2">
        <div class="table-responsive">
          <table class="table table-sm table-bordered table-striped table-hover datatable-AssignmentHistory">
            <thead class="table-light">
              <tr>
                <!-- <th style="width: 30px;">ID</th> -->
                <th style="width: 90px;">PM Code</th>
                <th style="width: 90px;">Start</th>
                <th style="width: 90px;">End</th>
                <th>Designation</th>
                <th>Sub unit</th>
                <th>Unit</th>
                <th>PAMU</th>
                <th>Category</th>
                <th style="width: 70px;">Type</th>
                <th style="width: 100px;">Assign Type</th>
                <th style="width: 80px;">Geography</th>
                <th style="width: 60px;">Rank</th>
                <th style="width: 80px;">Years</th>
                <th style="width: 140px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($histories as $history)
              <tr data-entry-id="{{ $history->id }}">
                <!-- <td>{{ $history->id }}</td> -->
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
@endsection
@section('scripts')
@parent
{{-- DataTable initialization --}}
      <script>
        $(function () {
          let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

          $.extend(true, $.fn.dataTable.defaults, {
            order: [[ 2, 'desc' ]], // Order by start_date descending
            pageLength: 10,
          });
          
          $('.datatable-AssignmentHistory:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        })
      </script>
      <script>
        (function () {
          const unitSel  = document.getElementById('unit_id');
          const pamuName = document.getElementById('pamu_name');
          const pamuId   = document.getElementById('pamu_id');

          if (!unitSel || !pamuName || !pamuId) {
            console.log('PAMU autofill: missing elements', { unitSel, pamuName, pamuId });
            return;
          }

          function fillPamu() {
            const opt = unitSel.options[unitSel.selectedIndex];
            const name = (opt && opt.getAttribute('data-pamu-name')) || '';
            const id   = (opt && opt.getAttribute('data-pamu-id')) || '';

            console.log('fillPamu()', { selectedIndex: unitSel.selectedIndex, name, id });

            pamuName.value = name;
            pamuId.value   = id;
          }

          // native select change
          unitSel.addEventListener('change', fillPamu);

          // if Select2 is active, also listen to its events (requires jQuery/select2)
          if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
            jQuery(unitSel).on('select2:select select2:clear', fillPamu);
          }

          // initial fill
          fillPamu();
        })();
      </script>


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

          if (!pmCodeEl || !startEl || !endEl || !rankOutEl || !yearEarnedEl) {
            console.log('Auto compute: missing element(s)', { pmCodeEl, startEl, endEl, rankOutEl, yearEarnedEl });
            return;
          }

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

                  // if server includes rank_during_completion, keep it visible
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
              
              // FIXED: Show warning message even on success if there's a message
              if (yearErrEl) {
                if (json.message && json.year_earned === 0) {

                  yearErrEl.textContent = json.message;
                  yearErrEl.className = 'text-danger small mt-1';
                } else {
                  yearErrEl.textContent = '';
                  yearErrEl.className = 'text-danger small mt-1'; // Reset to default
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
@endsection