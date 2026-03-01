@extends('layouts.app')
@section('content')

    <div class="index-wrapper">
        <div class="card">
            <div class="index-header">
                <h4>
                    <i class="fas fa-user me-1"></i>
                    {{ $config_data->module_name }} |
                    @if($operation_type === 'show')
                        VIEW &mdash; {{ $officer->NAME }}
                    @elseif($operation_type === 'edit')
                        EDIT &mdash; {{ $officer->NAME }}
                    @else
                        CREATE NEW
                    @endif
                </h4>
                <div>
                    <a href="{{ route($config_data->module_route.'.index') }}" class="btn-create">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-body">

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form
                    action="{{
                        $operation_type === 'create'
                            ? route($config_data->module_route.'.store')
                            : route($config_data->module_route.'.update', [$officer->PM_CODE])
                    }}"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    @csrf
                    @if($operation_type === 'edit')
                        @method('PUT')
                    @endif

                    <div class="row g-3">

                        {{-- SRTY --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">SRTY</label>
                            <input
                                type="text"
                                name="SRTY"
                                class="form-control @error('SRTY') is-invalid @enderror"
                                value="{{ old('SRTY', $officer->SRTY) }}"
                                @disabled($operation_type === 'show')
                            >
                            @error('SRTY')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- PM_CODE --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">PM CODE <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                name="PM_CODE"
                                class="form-control @error('PM_CODE') is-invalid @enderror"
                                value="{{ old('PM_CODE', $officer->PM_CODE) }}"
                                @disabled($operation_type === 'show')
                                required
                            >
                            @error('PM_CODE')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- NAME --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">NAME</label>
                            <input
                                type="text"
                                name="NAME"
                                class="form-control @error('NAME') is-invalid @enderror"
                                value="{{ old('NAME', $officer->NAME) }}"
                                @disabled($operation_type === 'show')
                            >
                            @error('NAME')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- SUFFIX --}}
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">SUFFIX</label>
                            <input
                                type="text"
                                name="SUFFIX"
                                class="form-control @error('SUFFIX') is-invalid @enderror"
                                value="{{ old('SUFFIX', $officer->SUFFIX) }}"
                                @disabled($operation_type === 'show')
                            >
                            @error('SUFFIX')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- RANK --}}
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">RANK</label>
                            <input
                                type="text"
                                name="RANK"
                                class="form-control @error('RANK') is-invalid @enderror"
                                value="{{ old('RANK', $officer->RANK) }}"
                                @disabled($operation_type === 'show')
                            >
                            @error('RANK')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- AFPSN --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">AFPSN</label>
                            <input
                                type="text"
                                name="AFPSN"
                                class="form-control @error('AFPSN') is-invalid @enderror"
                                value="{{ old('AFPSN', $officer->AFPSN) }}"
                                @disabled($operation_type === 'show')
                            >
                            @error('AFPSN')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- AFPOS --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">AFPOS</label>
                            <input
                                type="text"
                                name="AFPOS"
                                class="form-control @error('AFPOS') is-invalid @enderror"
                                value="{{ old('AFPOS', $officer->AFPOS) }}"
                                @disabled($operation_type === 'show')
                            >
                            @error('AFPOS')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- TYPE --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">TYPE</label>
                            <input
                                type="text"
                                name="TYPE"
                                class="form-control @error('TYPE') is-invalid @enderror"
                                value="{{ old('TYPE', $officer->TYPE) }}"
                                @disabled($operation_type === 'show')
                            >
                            @error('TYPE')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- SIG --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">SIG</label>
                            <input
                                type="text"
                                name="SIG"
                                class="form-control @error('SIG') is-invalid @enderror"
                                value="{{ old('SIG', $officer->SIG) }}"
                                @disabled($operation_type === 'show')
                            >
                            @error('SIG')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- SEX --}}
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">SEX</label>
                            @if($operation_type === 'show')
                                <input type="text" class="form-control" value="{{ $officer->SEX }}" disabled>
                            @else
                                <select name="SEX" class="form-control @error('SEX') is-invalid @enderror">
                                    <option value="">-- Select --</option>
                                    <option value="M" {{ old('SEX', $officer->SEX) === 'M' ? 'selected' : '' }}>Male</option>
                                    <option value="F" {{ old('SEX', $officer->SEX) === 'F' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('SEX')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                        </div>

                        {{-- DOR --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">DOR</label>
                            <input
                                type="date"
                                name="DOR"
                                class="form-control @error('DOR') is-invalid @enderror"
                                value="{{ old('DOR', $officer->DOR) }}"
                                @disabled($operation_type === 'show')
                            >
                            @error('DOR')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- TACS --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">TACS</label>
                            <input
                                type="text"
                                name="TACS"
                                class="form-control @error('TACS') is-invalid @enderror"
                                value="{{ old('TACS', $officer->TACS) }}"
                                @disabled($operation_type === 'show')
                            >
                            @error('TACS')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- DOB --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">DOB</label>
                            <input
                                type="date"
                                name="DOB"
                                class="form-control @error('DOB') is-invalid @enderror"
                                value="{{ old('DOB', $officer->DOB) }}"
                                @disabled($operation_type === 'show')
                            >
                            @error('DOB')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- DOC --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">DOC</label>
                            <input
                                type="date"
                                name="DOC"
                                class="form-control @error('DOC') is-invalid @enderror"
                                value="{{ old('DOC', $officer->DOC) }}"
                                @disabled($operation_type === 'show')
                            >
                            @error('DOC')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- RET --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">RET</label>
                            <input
                                type="text"
                                name="RET"
                                class="form-control @error('RET') is-invalid @enderror"
                                value="{{ old('RET', $officer->RET) }}"
                                @disabled($operation_type === 'show')
                            >
                            @error('RET')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- HCC --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">HCC</label>
                            <input
                                type="text"
                                name="HCC"
                                class="form-control @error('HCC') is-invalid @enderror"
                                value="{{ old('HCC', $officer->HCC) }}"
                                @disabled($operation_type === 'show')
                            >
                            @error('HCC')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- SOC --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">SOC</label>
                            <input
                                type="text"
                                name="SOC"
                                class="form-control @error('SOC') is-invalid @enderror"
                                value="{{ old('SOC', $officer->SOC) }}"
                                @disabled($operation_type === 'show')
                            >
                            @error('SOC')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- REMARKS --}}
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">REMARKS</label>
                            <textarea
                                name="REMARKS"
                                rows="3"
                                class="form-control @error('REMARKS') is-invalid @enderror"
                                @disabled($operation_type === 'show')
                            >{{ old('REMARKS', $officer->REMARKS) }}</textarea>
                            @error('REMARKS')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- DESIGNATION --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">DESIGNATION</label>
                            @if($operation_type === 'show')
                                <input type="text" class="form-control" value="{{ optional($officer->designations)->name }}" disabled>
                            @else
                                <select name="designation_id" class="form-control select2 @error('designation_id') is-invalid @enderror">
                                    <option value="">-- Select Designation --</option>
                                    @foreach($designations as $id => $name)
                                        <option value="{{ $id }}" {{ old('designation_id', $officer->designation_id) == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('designation_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                        </div>

                        {{-- UNIT --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">UNIT</label>
                            @if($operation_type === 'show')
                                <input type="text" class="form-control" value="{{ optional($officer->units)->name }}" disabled>
                            @else
                                <select name="unit_id" class="form-control select2 @error('unit_id') is-invalid @enderror">
                                    <option value="">-- Select Unit --</option>
                                    @foreach($units as $id => $name)
                                        <option value="{{ $id }}" {{ old('unit_id', $officer->unit_id) == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                        </div>

                        {{-- ROLE --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">ROLE</label>
                            @if($operation_type === 'show')
                                <input type="text" class="form-control" value="{{ optional($officer->roles)->name }}" disabled>
                            @else
                                <select name="role_id" class="form-control select2 @error('role_id') is-invalid @enderror">
                                    <option value="">-- Select Role --</option>
                                    @foreach($roleType as $id => $name)
                                        <option value="{{ $id }}" {{ old('role_id', $officer->role_id) == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                        </div>

                    </div>{{-- /.row --}}

                    @if($operation_type !== 'show')
                        <div class="mt-4">
                            <button type="submit" class="btn-create">
                                <i class="fas fa-save me-1"></i>
                                {{ $operation_type === 'create' ? 'Create' : 'Update' }}
                            </button>
                        </div>
                    @endif

                </form>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        // Initialize Select2 if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2();
        }
    </script>
@endsection