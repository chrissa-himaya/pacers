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
          <div class="col-md-3">
            <label>Rank</label>
            <input type="text" class="form-control"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->RANK : session('rank')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>Name</label>
            <input type="text" class="form-control"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->NAME : session('name')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>AFPOS</label>
            <input type="text" class="form-control"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->AFPOS : session('afpos')}}"
              disabled>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-3">
            <label>AFPSN</label>
            <input type="text" class="form-control"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->AFPSN : session('afpsn')}}"
              disabled>
          </div>
          <div class="col-md-3">
            <label>Sex</label>
            <input type="text" class="form-control"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->SEX : session('sex')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>DOB</label>
            <input type="text" class="form-control"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->DOB : session('dob')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>Date Ret</label>
            <input type="text" class="form-control"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->RET : session('date_ret')}}"
              disabled>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-3">
            <label>DOR</label>
            <input type="text" class="form-control"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->DOR : session('dor')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>SOC</label>
            <input type="text" class="form-control"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->SOC : session('soc')}}" disabled>
          </div>
          <div class="col-md-3">
            <label>Type</label>
            <input type="text" class="form-control"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->TYPE : session('type')}}" disabled>
          </div>

          <div class="col-md-3">
            <label>SIG</label>
            <input type="text" class="form-control"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->SIG : session('sig')}}" disabled>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-md-3">
            <label>Current Designation</label>
            <input type="text" class="form-control"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->DESIGNATION : session('designation')}}"
              disabled>
          </div>
          <div class="col-md-3">
            <label>Current Unit</label>
            <input type="text" class="form-control"
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->UNIT : session('unit')}}" disabled>
          </div>
        </div>
        <hr>
        <!-- CAREER ADVISER INPUTS -->
        @if((session('name') && $data_items['operation_type'] == 'create') || (isset($data_items['officerData']->NAME) && $data_items['operation_type'] == 'show'))
          <div class="row mt-3">
            <div class="col-md-3">
              <label>Entry</label>
              @if($data_items["operation_type"] == "create")
                <select name="schoolingname_id" class="form-control select2">
                  <option value="">-</option>
                  @foreach($data_items['schoolingnames'] as $id => $entry)
                    <option value="{{ $id }}" {{ session('schoolingname_id') == $id ? 'selected' : '' }}>
                      {{ $entry }}
                    </option>
                  @endforeach
                </select>
                <input type="hidden" name="schoolingname_id" value="{{ session('schoolingname_id') }}">
              @else
                <input type="text" class="form-control" value="{{ $data_items['data']->schoolingnames->name }}" disabled>
              @endif
            </div>

            <div class="col-md-3" style="max-width: 12%; flex: 0 0 12%;">
              <label>Class</label>
              @if($data_items["operation_type"] == "create")
                <select name="classname_id" class="form-control select2">
                  <option value="">-</option>
                  @foreach($data_items['classnames'] as $id => $entry)
                    <option value="{{ $id }}" {{ session('classname_id') == $id ? 'selected' : '' }}>
                      {{ $entry }}
                    </option>
                  @endforeach
                </select>
                <input type="hidden" name="classname_id" value="{{ session('classname_id') }}">
              @else
                <input type="text" class="form-control" value="{{ $data_items['data']->classnames->year }}" disabled>
              @endif
            </div>

            <div class="col-md-3">
              <label>Schooling Unit</label>
              @if($data_items['operation_type'] == 'create')
                <select name="schooling_unit_id" id="schooling_unit_id" class="form-control select2">
                  <option value="">-</option>


                  @foreach($data_items['schoolingUnits'] as $su)
                    <option value="{{ $su->id }}" data-location="{{ $su->location }}" @selected(old('schooling_unit_id', session('schooling_unit_id')) == $su->id)>
                      {{ $su->name }}
                    </option>
                  @endforeach
                </select>
                
              @else
                <input type="text" class="form-control" value="{{ $data_items['data']->schoolingunits->name ?? '' }}"
                  disabled>
              @endif
            </div>

            <div class="col-md-3" style="max-width: 12%; flex: 0 0 12%;">
              <label>Local/Foreign</label>
              <input type="text" id="schooling_unit_location" class="form-control"
                value="{{ old('schooling_unit_location', session('schooling_unit_location', $data_items['data']->schoolingunits->location ?? '')) }}"
                readonly>
            </div>

            <div class="col-md-3">
              <label>Assignment</label>
              @if($data_items["operation_type"] == "create")
                <select name="assignment_id" class="form-control select2">
                  <option value="">-</option>
                  @foreach($data_items['assignments'] as $id => $entry)
                    <option value="{{ $id }}" {{ session('assignment_id') == $id ? 'selected' : '' }}>
                      {{ $entry }}
                    </option>
                  @endforeach
                </select>
                <input type="hidden" name="assignment_id" value="{{ session('assignment_id') }}">
              @else
                <input type="text" class="form-control" value="{{ $data_items['data']->assignments->name }}" disabled>
              @endif
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-md-3">
              <label>Date completed</label>
              <input type="date" id="date_completed" name="date_completed" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? $data_items["data"]->date_completed : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }}>
            </div>
            <div class="col-md-3">
              <label>Rating</label>
              <input type="number" name="rating" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? $data_items["data"]->rating : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }}>
            </div>
            <div class="col-md-3">
              <label>Standing</label>
              <input type="number" name="standing" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? $data_items["data"]->standing : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }}>
            </div>
            <div class="col-md-3">
              <label>Total Students</label>
              <input type="number" name="total_student" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? $data_items["data"]->total_student : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }}>
            </div>
          </div>

          <div class="row mt-3 row-cols-7">
            <div class="col">
              <label>Rank during completion</label>
              <input type="text" id="rank_during_completion" name="rank_during_completion" class="form-control"
                value="{{ old('rank_during_completion', $data_items['data']->rank_during_completion ?? '') }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : 'readonly' }}>
            </div>
            <div class="col">
              <label>2LT</label>
              <input type="number" name="2lt" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? $data_items["data"]->rating : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }}>
            </div>
            <div class="col">
              <label>1lt</label>
              <input type="number" name="1lt" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? $data_items["data"]->standing : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }}>
            </div>
            <div class="col">
              <label>CPT</label>
              <input type="number" name="cpt" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? $data_items["data"]->total_student : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }}>
            </div>
            <div class="col">
              <label>MAJ</label>
              <input type="number" name="maj" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? $data_items["data"]->total_student : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }}>
            </div>
            <div class="col">
              <label>LTC</label>
              <input type="number" name="ltc" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? $data_items["data"]->total_student : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }}>
            </div>
            <div class="col">
              <label>COL</label>
              <input type="number" name="col" class="form-control"
                value="{{ (isset($data_items["data"]) && $data_items['operation_type'] === 'show') ? $data_items["data"]->total_student : '' }}"
                {{ $data_items['operation_type'] === 'show' ? 'disabled' : '' }}>
            </div>
          </div>

          <div class="mt-4 text-right">
            @if($data_items['operation_type'] === 'create')
              <input type="submit" class="btn btn-primary" name="action" value="Save">
            @endif
        @endif
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
    document.addEventListener('DOMContentLoaded', function () {
      const select = document.getElementById('schooling_unit_id');
      const locInput = document.getElementById('schooling_unit_location');

      if (select && locInput) {
        function syncLocation() {
          const opt = select.options[select.selectedIndex];
          const location = opt ? (opt.dataset.location || '') : '';
          locInput.value = location;
        }
        syncLocation();
        select.addEventListener('change', syncLocation);
        if (window.jQuery) jQuery(select).on('change', syncLocation);
      }

      // ---------- rank during completion autofill ----------
      const OP = @json($data_items['operation_type']);
      if (OP === 'show') return; 

      const pmCodeEl = document.getElementById('pm_code');
      const dateEl = document.getElementById('date_completed');
      const rankEl = document.getElementById('rank_during_completion');

      if (!pmCodeEl || !dateEl || !rankEl) return;

      async function fetchRank() {
        const pm_code = pmCodeEl.value;
        const date_completed = dateEl.value;

        console.log('fetchRank inputs:', { pm_code, date_completed });

        // ignore bad years / partial values
        if (!/^\d{4}-\d{2}-\d{2}$/.test(date_completed) ||
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
        } catch (e) {
          console.error('fetchRank error:', e);
          rankEl.value = '';
        }
      }

      // ---- debounce so we only run after the final value settles ----
      let t = null;

      function scheduleFetchRank() {
        clearTimeout(t);
        t = setTimeout(fetchRank, 300);
      }

      // listen to BOTH input and change (some datepickers fire input first)
      dateEl.addEventListener('change', scheduleFetchRank);
      pmCodeEl.addEventListener('change', scheduleFetchRank);

      // if Select2 is used
      if (window.jQuery && jQuery(pmCodeEl).data('select2')) {
        jQuery(pmCodeEl).on('select2:select select2:clear', scheduleFetchRank);
      }

      // initial run (important for edit mode)
      scheduleFetchRank();

    });
  </script>
@endsection