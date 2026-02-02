@extends('layouts.app')
@section('content')

<div class="card">
    <div class="card-header">
        @if($data_items['operation_type']=="show")
            <h4 class="d-inline">{{$config_data->module_name}} | VIEW - {{$data_items["data"]->name}}</h4>
        @elseif($data_items['operation_type']=="edit")
            <h4 class="d-inline">{{$config_data->module_name}} | EDIT - {{$data_items["data"]->name}}</h4>
        @elseif($data_items['operation_type']=="create")
            <h4 class="d-inline">{{$config_data->module_name}} | CREATE</h4>            
        @endif
        <a href="{{ route("$config_data->module_route.index") }}" class="btn btn-secondary float-end">
            Back
        </a>
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
        <div id="rank-mismatch-alert" class="alert alert-danger d-none" role="alert"></div>

            <table class="table table-bordered">
                <tbody>
                    {{-- Duty --}}
                    <tr>
                        <th style="width:150px;">Duty</th>
                        <td>
                            <select name="assignment_id" id="assignment_id" class="form-control select2"
                                @disabled($data_items["operation_type"] === "show")>
                                <option value="">-- Select Assignment --</option>
                                @foreach($data_items['assignments'] as $id => $assignment)
                                    <option value="{{ $id }}"
                                        @selected(old('assignment_id', $data_items['data']->assignment_id ?? '') == $id)>
                                        {{ $assignment->name }} - {{ $assignment->types->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

                    {{-- Min month --}}
                    <tr>
                        <th>Min month</th>
                        <td>
                            <select name="min_month_rankpoint_id" class="form-control select2" @disabled($data_items["operation_type"] === "show")>
                                <option value="">-- Select RankpointId --</option>
                                @foreach(($data_items['rankpoints_grouped']['min_month'] ?? collect()) as $rp)
                                    <option value="{{ $rp->id }}"
                                        data-rank-id="{{ $rp->rank_id }}"
                                        data-rank-code="{{ $rp->ranks->code ?? '' }}"
                                        @selected(old('min_month_rankpoint_id', $data_items['data']->min_month_rankpoint_id ?? '') == $rp->id)>
                                        {{ $rp->ranks->code }} min_month - {{ $rp->points }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

                    {{-- Min points --}}
                    <tr>
                        <th>Min points</th>
                        <td>
                            <select name="min_point_rankpoint_id" class="form-control select2" @disabled($data_items["operation_type"] === "show")>
                                <option value="">-- Select RankpointId --</option>
                                @foreach(($data_items['rankpoints_grouped']['min_point'] ?? collect()) as $rp)
                                    <option value="{{ $rp->id }}"
                                    data-rank-id="{{ $rp->rank_id }}"
                                        data-rank-code="{{ $rp->ranks->code ?? '' }}"
                                        @selected(old('min_point_rankpoint_id', $data_items['data']->min_point_rankpoint_id ?? '') == $rp->id)>
                                        {{ $rp->ranks->code }} min_point - {{ $rp->points }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

                    {{-- Max month --}}
                    <tr>
                        <th>Max month</th>
                        <td>
                            <select name="max_month_rankpoint_id" class="form-control select2" @disabled($data_items["operation_type"] === "show")>
                                <option value="">-- Select RankpointId --</option>
                                @foreach(($data_items['rankpoints_grouped']['max_month'] ?? collect()) as $rp)
                                    <option value="{{ $rp->id }}"
                                    data-rank-id="{{ $rp->rank_id }}"
                                        data-rank-code="{{ $rp->ranks->code ?? '' }}"
                                        @selected(old('max_month_rankpoint_id', $data_items['data']->max_month_rankpoint_id ?? '') == $rp->id)>
                                        {{ $rp->ranks->code }} max_month - {{ $rp->points }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

                    {{-- Max points --}}
                    <tr>
                        <th>Max points</th>
                        <td>
                            <select name="max_point_rankpoint_id" class="form-control select2" @disabled($data_items["operation_type"] === "show")>
                                <option value="">-- Select RankpointId --</option>
                                @foreach(($data_items['rankpoints_grouped']['max_point'] ?? collect()) as $rp)
                                    <option value="{{ $rp->id }}"
                                    data-rank-id="{{ $rp->rank_id }}"
                                        data-rank-code="{{ $rp->ranks->code ?? '' }}"
                                        @selected(old('max_point_rankpoint_id', $data_items['data']->max_point_rankpoint_id ?? '') == $rp->id)>
                                        {{ $rp->ranks->code }} max_point - {{ $rp->points }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

                    {{-- Action --}}
                    @unless($data_items['operation_type'] === 'show')
                    <tr>
                        <th>Action</th>
                        <td>
                            <button type="submit"
                                class="form-control btn {{ $data_items['operation_type'] === 'create' ? 'btn-primary' : 'btn-warning' }}">
                                Submit
                            </button>
                        </td>
                    </tr>
                    @endunless
                </tbody>
            </table>
        </form>    
    </div>

</div>
@endsection
@section('scripts')
    <script>
        (function () {
        const form = document.querySelector('form');
        if (!form) return;

        const alertBox = document.getElementById('rank-mismatch-alert');
        const submitBtn = form.querySelector('button[type="submit"]');

        const selects = [
            form.querySelector('select[name="min_month_rankpoint_id"]'),
            form.querySelector('select[name="min_point_rankpoint_id"]'),
            form.querySelector('select[name="max_month_rankpoint_id"]'),
            form.querySelector('select[name="max_point_rankpoint_id"]'),
        ].filter(Boolean);

        function setError(msg) {
            if (alertBox) {
            alertBox.textContent = msg;
            alertBox.classList.remove('d-none');
            }
            if (submitBtn) submitBtn.disabled = true;
        }

        function clearError() {
            if (alertBox) {
            alertBox.textContent = '';
            alertBox.classList.add('d-none');
            }
            if (submitBtn) submitBtn.disabled = false;
        }

        function selectedRank(sel) {
            if (!sel || !sel.value) return null;
            const opt = sel.options[sel.selectedIndex];
            return {
            rankId: opt?.dataset?.rankId || null,
            rankCode: opt?.dataset?.rankCode || '',
            };
        }

        function validateSameRank() {
            const info = selects.map(selectedRank).filter(Boolean);

            // if some are not selected yet, let HTML "required" handle it
            if (info.length < 2) {
            clearError();
            return true;
            }

            // If rankId is missing, you forgot to add data-rank-id
            if (info.some(i => !i.rankId)) {
            setError('Form config error: missing data-rank-id on rankpoint options.');
            return false;
            }

            const first = info[0].rankId;
            const ok = info.every(i => i.rankId === first);

            if (!ok) {
            const codes = [...new Set(info.map(i => i.rankCode).filter(Boolean))];
            const suffix = codes.length ? ` (selected ranks: ${codes.join(', ')})` : '';
            setError('Invalid selection: Min/Max month and points must have the SAME rank.' + suffix);
            return false;
            }

            clearError();
            return true;
        }

        // validate on change
        selects.forEach(sel => sel.addEventListener('change', validateSameRank));

        // validate on submit
        form.addEventListener('submit', function (e) {
            if (!validateSameRank()) {
            e.preventDefault();
            e.stopPropagation();
            alertBox?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        // initial run (edit page)
        validateSameRank();
        })();
</script>

@endsection