@extends('layouts.app')
@section('content')

  <div class="card">
    <div class="card-header">
      @if($data_items['operation_type'] == "show")
        <h4 class="d-inline">{{$config_data->module_name}} | VIEW</h4>
      @elseif($data_items['operation_type'] == "edit")
        <h4 class="d-inline">{{$config_data->module_name}} | EDIT</h4>
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
            {{-- STEP 1: Assignment Category (MUST SELECT FIRST) --}}
            <div class="col-md-3">
              <label>Assignment Category <span class="text-danger">*</span></label>
              @if(in_array($op, ['create','edit']))
                <select name="assignment_id" id="assignment_id" class="form-control select2">
                  <option value="">-- Select Category First --</option>
                  @foreach($data_items['assignments'] as $id => $entry)
                    <option value="{{ $id }}" 
                      @selected(old('assignment_id', $data_items['data']->assignment_id ?? session('assignment_id')) == $id)>
                      {{ $entry }}
                    </option>
                  @endforeach
                </select>
                @error('assignment_id')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              @else
                <input type="text" class="form-control" 
                       value="{{ $data_items['data']->assignments->name ?? '' }}" disabled>
              @endif
            </div>

            {{-- STEP 2: Entry (Filtered by Assignment) --}}
            <div class="col-md-3">
              <label>Entry <span class="text-danger">*</span></label>
              @if(in_array($op, ['create','edit']))
                <select name="schoolingname_id" id="schoolingname_id" class="form-control select2" disabled>
                  <option value="">-- Select Assignment First --</option>
                </select>
                
                {{-- Hidden container for "Create New Entry" input --}}
                <div id="new_entry_container" style="display:none; margin-top:10px;">
                  <label>New Entry Name <span class="text-danger">*</span></label>
                  <input type="text" 
                         name="new_entry_name" 
                         id="new_entry_name" 
                         class="form-control" 
                         placeholder="e.g., PMA Class 2025"
                         value="{{ old('new_entry_name') }}">
                  <small class="text-muted">This entry will be linked to the selected assignment</small>
                  @error('new_entry_name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </div>
                
                @error('schoolingname_id')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              @else
                <input type="text" class="form-control" 
                       value="{{ $data_items['data']->schoolingnames->name ?? '' }}" disabled>
              @endif
            </div>

            <div class="col-md-3" style="max-width: 12%; flex: 0 0 12%;">
              <label>Class</label>
              <input type="text" id="classname" name="classname" class="form-control"
                value="{{ old('classname', $op === 'create' ? '' : ($data_items['data']->classname ?? '')) }}"
                {{ $op === 'show' ? 'disabled' : '' }}>
            </div>

            <div class="col-md-3">
              <label>Schooling Unit</label>
              @if(in_array($op, ['create','edit']))
                <select name="schooling_unit_id" id="schooling_unit_id" class="form-control select2">
                  <option value="">-</option>
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

            <div class="col-md-3" style="max-width: 12%; flex: 0 0 12%;">
              <label>Local/Foreihaystack: haystack: haystack: gn</label>
              <input type="text" id="schooling_unit_location" name="school_location" class="form-control"
                value="{{ old('school_location', session('school_location', $data_items['data']->schoolingunits->location ?? '')) }}"
                readonly>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-3">
              <label>Date completed</label>
              <input type="date" id="date_completed" name="date_completed" class="form-control"
                value="{{ old('date_completed', $op === 'create' ? '' : ($data_items['data']->date_completed ?? '')) }}"
                {{ $op === 'show' ? 'disabled' : '' }}>
            </div>

            <div class="col-md-3">
              <label>Rating</label>
              <input type="number" 
                     step="0.0001"
                     name="rating" 
                     class="form-control"
                     placeholder="e.g., 92.86"
                     value="{{ old('rating', $op === 'create' ? '' : $data_items['data']->rating ?? '') }}"
                     {{ $op === 'show' ? 'disabled' : '' }}>
            </div>

            <div class="col-md-3">
              <label>Standing</label>
              <input type="number" 
                     name="standing" 
                     class="form-control"
                     placeholder="e.g., 1"
                     value="{{ old('standing', $op === 'create' ? '' : $data_items['data']->standing ?? '') }}"
                     {{ $op === 'show' ? 'disabled' : '' }}>
            </div>

            <div class="col-md-3">
              <label>Total Students</label>
              <input type="number" 
                     name="total_student" 
                     class="form-control"
                     placeholder="e.g., 13"
                     value="{{ old('total_student', $op === 'create' ? '' : $data_items['data']->total_student ?? '') }}"
                     {{ $op === 'show' ? 'disabled' : '' }}>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-3">
              <input type="number" step="0.0001" id="computed_points" class="form-control"
                value="{{ old('computed_points', $data_items['data']->computed_points ?? '') }}"
                hidden>
            </div>
          </div>

          <div class="row mt-3 row-cols-7">
            <div class="col">
              <label>Rank</label>
              <input type="text" id="rank_during_completion" name="rank_during_completion" class="form-control"
                value="{{ old('rank_during_completion', $data_items['data']->rank_during_completion ?? '') }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : 'readonly' }}>
            </div>

            <div class="col">
              <label>2LT</label>
              <input type="number" step="0.00001" name="seclt" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? number_format($data_items["data"]->seclt, 5, '.', '') : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }} readonly>
            </div>

            <div class="col">
              <label>1LT</label>
              <input type="number" step="0.00001" name="firstlt" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? number_format($data_items["data"]->firstlt, 5, '.', '') : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }} readonly>
            </div>

            <div class="col">
              <label>CPT</label>
              <input type="number" step="0.00001" name="cpt" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? number_format($data_items["data"]->cpt, 5, '.', '') : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }} readonly>
            </div>

            <div class="col">
              <label>MAJ</label>
              <input type="number" step="0.00001" name="maj" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? number_format($data_items["data"]->maj, 5, '.', '') : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }} readonly>
            </div>

            <div class="col">
              <label>LTC</label>
              <input type="number" step="0.00001" name="ltc" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? number_format($data_items["data"]->ltc, 5, '.', '') : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }} readonly>
            </div>

            <div class="col">
              <label>COL</label>
              <input type="number" step="0.00001" name="col" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? number_format($data_items["data"]->col, 5, '.', '') : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }} readonly>
            </div>
          </div>

          <div class="mt-4 text-right">
            @if($op === 'create')
              <button type="submit" class="btn btn-primary" name="action" value="Save">
                <i class="fas fa-save"></i> Save
              </button>
            @elseif($op === 'edit')
              <button type="submit" class="btn btn-warning" name="action" value="Update">
                <i class="fas fa-edit"></i> Update
              </button>
            @endif
          @endif
          <a href="{{ route("$config_data->module_route.index") }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
          </a>
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
  <div class="card mt-3">
    <div class="card-header py-2">
      <h6 class="mb-0">Related Schooling Records (PM Code: {{ $currentPmCode }})</h6>
    </div>
    <div class="card-body p-2">
      <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped table-hover">
          <thead class="table-light">
            <tr>
              <th style="width: 90px;">PM Code</th>
              <th>Entry</th>
              <th style="width: 70px;">Class</th>
              <th>School/Unit</th>
              <th>Category</th>
              <th style="width: 100px;">Date Completed</th>
              <th style="width: 70px;">Rating</th>
              <th style="width: 60px;">Standing</th>
              <th style="width: 60px;">Total</th>
              <th style="width: 60px;">Rank</th>
              <th style="width: 70px;">2LT</th>
              <th style="width: 70px;">1LT</th>
              <th style="width: 70px;">CPT</th>
              <th style="width: 70px;">MAJ</th>
              <th style="width: 70px;">LTC</th>
              <th style="width: 70px;">COL</th>
              <th style="width: 140px;">Actions</th>
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
  <div class="card mt-3">
    <div class="card-header py-2">
      <h6 class="mb-0">Related Assignment History (PM Code: {{ $ahPmCode }})</h6>
    </div>
    <div class="card-body p-2">
      <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped table-hover datatable-AssignmentHistory">
          <thead class="table-light">
            <tr>
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

            if (window.jQuery && jQuery(entrySelect).data('select2')) {
              // Don’t destroy/recreate unless you have to; just notify select2
              jQuery(entrySelect).trigger('change.select2');
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
          } else {
            entrySelect.dispatchEvent(new Event('change'));
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
            } else {
              suEl.dispatchEvent(new Event('change'));
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

          // Also sync location display (in case schooling_unit reset didn’t fire)
          syncLocation();
        }

        // ✅ Assignment change: clear first, then load new entries
        assignmentSelect.addEventListener('change', function () {
          clearDependentFieldsOnAssignmentChange();
          loadEntriesByAssignment();
        });

        if (window.jQuery) {
          // Ensure no duplicate handlers, then attach the combined handler
          jQuery(assignmentSelect).off('select2:select select2:clear');
          jQuery(assignmentSelect).on('select2:select select2:clear', function () {
            clearDependentFieldsOnAssignmentChange();
            loadEntriesByAssignment();
          });
        }

        // ✅ Keep entry change handler (native + select2)
        entrySelect.addEventListener('change', handleEntryChange);
        if (window.jQuery) jQuery(entrySelect).on('select2:select', handleEntryChange);

        // Initial load (edit page / when old value exists)
        if (assignmentSelect.value) {
          loadEntriesByAssignment();
        }
      }

      // ---------- rank during completion autofill ----------
      const pmCodeEl = document.getElementById('pm_code');
      // dateEl + rankEl already declared above

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
