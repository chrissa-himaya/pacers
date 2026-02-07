@extends('layouts.app')
@section('content')
<div class="card">
  <div class="card-header">
    <h4 class="d-inline">{{ $config_data->module_name }} | BULK UPLOAD</h4>
    <a href="{{ route($config_data->module_route.'.index') }}" class="btn btn-secondary float-end">Back</a>
  </div>

  <div class="card-body">
    <form action="{{ route($config_data->module_route.'.bulkstore') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div class="mb-3">
        <label class="form-label">Upload Excel / CSV</label>
        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
        @error('file') <div class="text-danger mt-1">{{ $message }}</div> @enderror
      </div>

      <button type="submit" class="btn btn-primary">Upload</button>
    </form>
  </div>
</div>
@endsection
