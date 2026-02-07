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
              value="{{isset($data_items['officerData']) ? $data_items['officerData']->designations->name : session('designation')}}"
              disabled>
          </div>
          <div class="col-md-3">
            <label>Current Unit</label>
            <input type="text" class="form-control"
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
              <label>Award</label>
              @if(in_array($op, ['create', 'edit']))
                <select name="award_id" class="form-control select2">
                  <option value="">-</option>
                  @foreach($data_items['awards'] as $id => $entry)
                    <option value="{{ $id }}" @selected(old('award_id', $data_items['data']->award_id ?? session('award_id')) == $id)>
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
              <label>Award Type</label>

              @php
                $selectedAwardType = old('award_type', $data_items['data']->award_type ?? '');
              @endphp

              @if(in_array($op, ['create', 'edit']))
                <select name="award_type" class="form-control select2">
                  <option value="">-</option>
                  @foreach($data_items['award_type'] as $id => $entry)
                    <option value="{{ $id }}" @selected((string)$selectedAwardType === (string)$id)>
                      {{ $entry }}
                    </option>
                  @endforeach
                </select>
              @else
                <input type="text" class="form-control"
                  value="{{ $data_items['data']->awardType->name ?? '' }}" disabled>
              @endif
            </div>

          <div class="col-md-3">
            <label>Date</label>
            <input type="date" id="award_date" name="date" class="form-control"
            value="{{ old('date', $op==='create' ? '' : ($data_items['data']->date ?? '')) }}"
            {{ $op==='show' ? 'disabled' : '' }}>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-3">
            <label>GO #</label>
            <input type="text" class="form-control" name="go_number">
          </div>

          <div class="col">
          <label>Rank</label>
          <input type="text" id="rank_display" class="form-control"
            value="{{ old('rank_display', data_get($data_items['data'], 'dateranks.ranks.code') ?? '') }}"
            {{ $op === 'show' ? 'disabled' : 'readonly' }}>

          <input type="hidden" id="date_rank_id" name="date_rank_id"
            value="{{ old('date_rank_id', $data_items['data']->date_rank_id ?? '') }}">
        </div>

          <div class="col-md-3">
            <label>Points</label>
            <input type="text" class="form-control" name="points">
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
@endsection
@section('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const OP = @json($data_items['operation_type'] ?? '');
      if (OP === 'show') return;

      const pmCodeEl = document.getElementById('pm_code');
      const dateEl = document.getElementById('award_date');
      const rankEl = document.getElementById('rank_display');
      const dateRankIdEl = document.getElementById('date_rank_id');

      if (!pmCodeEl || !dateEl || !rankEl || !dateRankIdEl) return;

      async function fetchDateRank() {
        const pm_code = pmCodeEl.value;
        const date = dateEl.value;

        if (!pm_code || !/^\d{4}-\d{2}-\d{2}$/.test(date)) {
          rankEl.value = '';
          dateRankIdEl.value = '';
          return;
        }

        const url = new URL(@json(route('awardhistories.dateRankAtDate')));
        url.searchParams.set('pm_code', pm_code);
        url.searchParams.set('date', date);

        try {
          const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
          const json = await res.json();
          rankEl.value = json.rank ?? '';
          dateRankIdEl.value = json.date_rank_id ?? '';
        } catch (e) {
          rankEl.value = '';
          dateRankIdEl.value = '';
        }
      }

      let t = null;
      function schedule() {
        clearTimeout(t);
        t = setTimeout(fetchDateRank, 300);
      }

      dateEl.addEventListener('change', schedule);
      pmCodeEl.addEventListener('change', schedule);

      // select2 support
      if (window.jQuery && jQuery(pmCodeEl).data('select2')) {
        jQuery(pmCodeEl).on('select2:select select2:clear', schedule);
      }

      schedule(); // important for edit mode
    });

  </script>
@endsection