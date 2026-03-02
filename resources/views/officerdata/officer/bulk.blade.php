@extends('layouts.app')
@section('content')

    <div class="index-wrapper">
        <div class="card">
            <div class="index-header">
                <h4><i class="fas fa-upload"></i> {{ $config_data->module_name }} | BULK UPLOAD</h4>
                <div>
                    <a href="{{ route($config_data->module_route.'.index') }}" class="btn-create">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('bulk_errors') && count(session('bulk_errors')))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Some rows had issues:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach(session('bulk_errors') as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route($config_data->module_route.'.bulkstore') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Upload Excel / CSV</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        @error('file')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">
                            Accepted formats: <strong>.xlsx</strong>, <strong>.xls</strong>, <strong>.csv</strong>.
                        </div>
                    </div>

                    <button type="submit" class="btn-create">
                        <i class="fas fa-upload me-1"></i> Upload
                    </button>
                </form>

            </div>
        </div>
    </div>

@endsection