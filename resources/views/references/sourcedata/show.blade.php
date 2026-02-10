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
                                    <option value="">-- Select Duty --</option>
                                    @foreach($data_items['assignments'] as $id => $assignment)
                                        <option value="{{ $id }}"
                                            data-type-id="{{ $assignment->type_id ?? ($assignment->types->id ?? '') }}"
                                            @selected(old('assignment_id', $data_items['data']->assignment_id ?? '') == $id)>
                                            {{ $assignment->name }} - {{ $assignment->types->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th style="width:150px;">Rank</th>
                            <td>
                                <select name="rank_id" id="rank_id" class="form-control select2"
                                    @disabled($data_items["operation_type"] === "show")>
                                    <option value="">-- Select Rank --</option>
                                    @foreach($data_items['ranks'] as $id => $rank)
                                        <option value="{{ $id }}" {{ old('rank_id', $data_items['data']->rank_id ?? '') == $id ? 'selected' : '' }}>
                                            {{ $rank }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('rank_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </td>
                        </tr>

                        {{-- Min month --}}
                        <tr>
                            <th id="lbl-min-month">Min month</th>
                            <td>
                                <select name="min_month_rankpoint_id" class="form-control select2"
                                    @disabled($data_items["operation_type"] === "show")>
                                    <option value="">-- Select RankpointId --</option>
                                    {{-- normal min_month options --}}
                                    @foreach(($data_items['rankpoints_grouped']['min_month'] ?? collect()) as $rp)
                                        <option value="{{ $rp->id }}" data-rp-name="min_month" data-rank-id="{{ $rp->rank_id }}"
                                            data-rank-code="{{ $rp->ranks->code ?? '' }}"
                                            @selected(old('min_month_rankpoint_id', $data_items['data']->min_month_rankpoint_id ?? '') == $rp->id)>
                                            {{ $rp->ranks->code }} min_month - {{ $rp->points }}
                                        </option>
                                    @endforeach


                                    {{-- type_id=5 (PPD) factor1 options --}}
                                    @foreach(($data_items['rankpoints_grouped']['factor1'] ?? collect()) as $rp)
                                        <option value="{{ $rp->id }}" data-rp-name="factor1" data-rank-id="{{ $rp->rank_id }}"
                                            data-rank-code="{{ $rp->ranks->code ?? '' }}"
                                            @selected(old('min_month_rankpoint_id', $data_items['data']->min_month_rankpoint_id ?? '') == $rp->id)>
                                            {{ $rp->ranks->code }} factor1 - {{ $rp->points }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        {{-- Min points --}}
                        <tr>
                            <th id="lbl-min-point">Min points</th>
                            <td>
                                <select name="min_point_rankpoint_id" class="form-control select2"
                                    @disabled($data_items["operation_type"] === "show")>
                                    <option value="">-- Select RankpointId --</option>
                                    @foreach(($data_items['rankpoints_grouped']['min_point'] ?? collect()) as $rp)
                                        <option value="{{ $rp->id }}" data-rp-name="min_point" data-rank-id="{{ $rp->rank_id }}"
                                            data-rank-code="{{ $rp->ranks->code ?? '' }}"
                                            @selected(old('min_point_rankpoint_id', $data_items['data']->min_point_rankpoint_id ?? '') == $rp->id)>
                                            {{ $rp->ranks->code }} min_point - {{ $rp->points }}
                                        </option>
                                    @endforeach


                                    @foreach(($data_items['rankpoints_grouped']['factor2'] ?? collect()) as $rp)
                                        <option value="{{ $rp->id }}" data-rp-name="factor2" data-rank-id="{{ $rp->rank_id }}"
                                            data-rank-code="{{ $rp->ranks->code ?? '' }}"
                                            @selected(old('min_point_rankpoint_id', $data_items['data']->min_point_rankpoint_id ?? '') == $rp->id)>
                                            {{ $rp->ranks->code }} factor2 - {{ $rp->points }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        {{-- Max month --}}
                        <tr>
                            <th id="lbl-max-month">Max month</th>
                            <td>
                                <select name="max_month_rankpoint_id" class="form-control select2"
                                    @disabled($data_items["operation_type"] === "show")>
                                    <option value="">-- Select RankpointId --</option>
                                    @foreach(($data_items['rankpoints_grouped']['max_month'] ?? collect()) as $rp)
                                        <option value="{{ $rp->id }}" data-rp-name="max_month" data-rank-id="{{ $rp->rank_id }}"
                                            data-rank-code="{{ $rp->ranks->code ?? '' }}"
                                            @selected(old('max_month_rankpoint_id', $data_items['data']->max_month_rankpoint_id ?? '') == $rp->id)>
                                            {{ $rp->ranks->code }} max_month - {{ $rp->points }}
                                        </option>
                                    @endforeach


                                    @foreach(($data_items['rankpoints_grouped']['maxpt'] ?? collect()) as $rp)
                                        <option value="{{ $rp->id }}" data-rp-name="maxpt" data-rank-id="{{ $rp->rank_id }}"
                                            data-rank-code="{{ $rp->ranks->code ?? '' }}"
                                            @selected(old('max_month_rankpoint_id', $data_items['data']->max_month_rankpoint_id ?? '') == $rp->id)>
                                            {{ $rp->ranks->code }} maxpt - {{ $rp->points }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        {{-- Max points --}}
                        <tr>
                            <th id="lbl-max-point">Max points</th>
                            <td>
                                <select name="max_point_rankpoint_id" class="form-control select2"
                                    @disabled($data_items["operation_type"] === "show")>
                                    <option value="">-- Select RankpointId --</option>
                                    @foreach(($data_items['rankpoints_grouped']['max_point'] ?? collect()) as $rp)
                                        <option value="{{ $rp->id }}" data-rp-name="max_point" data-rank-id="{{ $rp->rank_id }}"
                                            data-rank-code="{{ $rp->ranks->code ?? '' }}"
                                            @selected(old('max_point_rankpoint_id', $data_items['data']->max_point_rankpoint_id ?? '') == $rp->id)>
                                            {{ $rp->ranks->code }} max_point - {{ $rp->points }}
                                        </option>
                                    @endforeach


                                    @foreach(($data_items['rankpoints_grouped']['foreignpt'] ?? collect()) as $rp)
                                        <option value="{{ $rp->id }}" data-rp-name="foreignpt" data-rank-id="{{ $rp->rank_id }}"
                                            data-rank-code="{{ $rp->ranks->code ?? '' }}"
                                            @selected(old('max_point_rankpoint_id', $data_items['data']->max_point_rankpoint_id ?? '') == $rp->id)>
                                            {{ $rp->ranks->code }} foreignpt - {{ $rp->points }}
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

            const assignmentSel = form.querySelector('#assignment_id');
            const thMinMonth = document.getElementById('lbl-min-month');
            const thMinPoint = document.getElementById('lbl-min-point');
            const thMaxMonth = document.getElementById('lbl-max-month');
            const thMaxPoint = document.getElementById('lbl-max-point');

            const selMinMonth = form.querySelector('select[name="min_month_rankpoint_id"]');
            const selMinPoint = form.querySelector('select[name="min_point_rankpoint_id"]');
            const selMaxMonth = form.querySelector('select[name="max_month_rankpoint_id"]');
            const selMaxPoint = form.querySelector('select[name="max_point_rankpoint_id"]');

            function getSelectedTypeId() {
                if (!assignmentSel || !assignmentSel.value) return null;
                const opt = assignmentSel.options[assignmentSel.selectedIndex];
                const v = opt?.dataset?.typeId;
                return v ? String(v) : null;
            }

            function rebuildSelect(selectEl, allowedRpNames) {
                if (!selectEl) return;

                // cache original options once
                if (!selectEl._allOptions) {
                    selectEl._allOptions = Array.from(selectEl.options).map(o => o.cloneNode(true));
                }

                const currentValue = selectEl.value;

                // build a fresh list of options
                selectEl.innerHTML = '';

                // always keep placeholder (option with empty value)
                const placeholder = selectEl._allOptions.find(o => !o.value);
                if (placeholder) selectEl.appendChild(placeholder.cloneNode(true));

                // append only allowed rp-name options
                selectEl._allOptions.forEach(o => {
                    if (!o.value) return;
                    const rpName = o.getAttribute('data-rp-name');
                    if (allowedRpNames.includes(rpName)) {
                        selectEl.appendChild(o.cloneNode(true));
                    }
                });

                // restore selection if still valid
                if (currentValue && selectEl.querySelector(`option[value="${CSS.escape(currentValue)}"]`)) {
                    selectEl.value = currentValue;
                } else {
                    selectEl.value = '';
                }

                // refresh select2 reliably
                if (window.jQuery) {
                    const $el = jQuery(selectEl);
                    if ($el.data('select2')) $el.select2('destroy');
                    $el.select2();
                    $el.trigger('change.select2');
                }
            }

            function applyTypeMode() {
                const typeId = getSelectedTypeId();
                const isPPD = (typeId === '5');


                if (isPPD) {
                    if (thMinMonth) thMinMonth.textContent = 'Factor 1';
                    if (thMinPoint) thMinPoint.textContent = 'Factor 2';
                    if (thMaxMonth) thMaxMonth.textContent = 'Max PT';
                    if (thMaxPoint) thMaxPoint.textContent = 'Foreign PT';

                    rebuildSelect(selMinMonth, ['factor1']);
                    rebuildSelect(selMinPoint, ['factor2']);
                    rebuildSelect(selMaxMonth, ['maxpt']);
                    rebuildSelect(selMaxPoint, ['foreignpt']);
                } else {
                    if (thMinMonth) thMinMonth.textContent = 'Min month';
                    if (thMinPoint) thMinPoint.textContent = 'Min points';
                    if (thMaxMonth) thMaxMonth.textContent = 'Max month';
                    if (thMaxPoint) thMaxPoint.textContent = 'Max points';

                    rebuildSelect(selMinMonth, ['min_month']);
                    rebuildSelect(selMinPoint, ['min_point']);
                    rebuildSelect(selMaxMonth, ['max_month']);
                    rebuildSelect(selMaxPoint, ['max_point']);
                }

                [selMinMonth, selMinPoint, selMaxMonth, selMaxPoint].forEach(s => s?.dispatchEvent(new Event('change')));
            }
            assignmentSel?.addEventListener('change', applyTypeMode);
            if (window.jQuery) {
                jQuery(document).on('change', '#assignment_id', applyTypeMode);
            }
            applyTypeMode();
        })();
    </script>

    <script>
        (function () {
            const form = document.querySelector('form');
            if (!form) return;

            const alertBox = document.getElementById('rank-mismatch-alert');
            const submitBtn = form.querySelector('button[type="submit"]');

            const rankSel = form.querySelector('#rank_id');

            const rpSelects = [
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

            function getSelectedRankFromRankpointSelect(sel) {
                if (!sel || !sel.value) return null;
                const opt = sel.options[sel.selectedIndex];
                return {
                    rankId: opt?.dataset?.rankId ? String(opt.dataset.rankId) : null,
                    rankCode: opt?.dataset?.rankCode || '',
                };
            }


            function getSelectedRankIdFromRankSelect() {
                if (!rankSel || !rankSel.value) return null;
                return String(rankSel.value);
            }

            function validateSameRank() {
                const chosen = rpSelects.map(getSelectedRankFromRankpointSelect).filter(Boolean);

                // if some are not selected yet, let HTML "required" handle it
                if (chosen.length < 2) {
                    clearError();
                    return true;
                }

                // Guard: you must have data-rank-id on ALL rp options
                if (chosen.some(x => !x.rankId)) {
                    setError('Form config error: missing data-rank-id on rankpoint options.');
                    return false;
                }

                // Condition #1: all 4 rankpoint selects must be the same rank
                const firstRpRankId = chosen[0].rankId;
                const allRpSame = chosen.every(x => x.rankId === firstRpRankId);

                if (!allRpSame) {
                    const codes = [...new Set(chosen.map(x => x.rankCode).filter(Boolean))];
                    const suffix = codes.length ? ` (selected ranks: ${codes.join(', ')})` : '';
                    setError('Invalid selection: Min/Max month and points must have the SAME rank.' + suffix);
                    return false;
                }

                // Condition #2: rank_id must match the rankpoints’ rank
                const selectedRankId = getSelectedRankIdFromRankSelect();
                // If rank_id not selected yet, don’t block typing; required will block submit
                if (selectedRankId) {
                    if (selectedRankId !== firstRpRankId) {
                        const rpCode = chosen.find(x => x.rankCode)?.rankCode || '';
                        setError(
                            'Invalid selection: The selected Rank must match the Rank of the selected rankpoints.' +
                            (rpCode ? ` (rankpoints rank: ${rpCode})` : '')
                        );
                        return false;
                    }
                }

                clearError();
                return true;
            }

            // --- Bind events ---
            // Native change
            rpSelects.forEach(sel => sel.addEventListener('change', validateSameRank));
            rankSel?.addEventListener('change', validateSameRank);

            // Select2 triggers (more reliable than plain change in some cases)
            if (window.jQuery) {
                const $doc = jQuery(document);

                // rankpoints
                $doc.on('select2:select select2:clear change', 'select[name="min_month_rankpoint_id"],select[name="min_point_rankpoint_id"],select[name="max_month_rankpoint_id"],select[name="max_point_rankpoint_id"]', validateSameRank);

                // rank
                $doc.on('select2:select select2:clear change', '#rank_id', validateSameRank);

                // when assignment type changes, your applyTypeMode() rebuilds the options + re-inits select2
                // so we validate AFTER it runs.
                $doc.on('change', '#assignment_id', function () {
                    // let applyTypeMode finish first
                    setTimeout(validateSameRank, 0);
                });
            }

            // Validate on submit
            form.addEventListener('submit', function (e) {
                if (!validateSameRank()) {
                    e.preventDefault();
                    e.stopPropagation();
                    alertBox?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });

            // Initial run (edit page loads with pre-selected values)
            validateSameRank();
        })();
    
    </script>
@endsection