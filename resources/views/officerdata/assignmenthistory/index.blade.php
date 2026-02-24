@extends('layouts.app')
@section('content')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');

        .index-wrapper {
            font-family: 'DM Sans', sans-serif;
        }

        .index-wrapper .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
            overflow: hidden;
        }

        /* ── Header ───────────────────────────────────────────────── */
        .index-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8e 100%);
            color: #fff;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .index-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: .3px;
        }

        .index-header h4 i {
            margin-right: 8px;
            opacity: .8;
        }

        /* ── Filter Row ───────────────────────────────────────────── */
        .index-wrapper thead tr.filter-row th {
            background: #f8f9fb;
            padding: 6px 4px;
            border-bottom: 2px solid #d6dce5;
        }

        .index-wrapper thead tr.filter-row input,
        .index-wrapper thead tr.filter-row select {
            font-family: 'DM Sans', sans-serif;
            font-size: .76rem;
            border-radius: 5px;
            border: 1px solid #d0d8e0;
            padding: 4px 8px;
            width: 100%;
            transition: border-color .2s, box-shadow .2s;
        }

        .index-wrapper thead tr.filter-row input:focus,
        .index-wrapper thead tr.filter-row select:focus {
            border-color: #2d5a8e;
            box-shadow: 0 0 0 2px rgba(45, 90, 142, .12);
            outline: none;
        }

        .index-wrapper thead tr.filter-row input::placeholder {
            color: #aab4c0;
            font-style: italic;
        }

        /* ── Table Styling ────────────────────────────────────────── */
        .index-wrapper .table thead th {
            background: #eef2f7;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
            color: #4a5568;
            border-bottom: 2px solid #d6dce5;
            padding: 10px 6px;
            white-space: nowrap;
        }

        .index-wrapper .table tbody td {
            font-size: .8rem;
            padding: 8px 6px;
            vertical-align: middle;
            color: #374151;
        }

        .index-wrapper .table tbody tr:hover {
            background: #f0f7ff;
        }

        .index-wrapper .table tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }

        .index-wrapper .table tbody tr:nth-child(even):hover {
            background: #f0f7ff;
        }

        /* ── Numeric Columns ──────────────────────────────────────── */
        .index-wrapper .table tbody td.mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: .75rem;
            color: #2d6e3f;
        }

        /* ── Action buttons ───────────────────────────────────────── */
        .action-btns {
            display: flex;
            gap: 4px;
            flex-wrap: nowrap;
        }

        .action-btns .btn {
            font-size: .72rem;
            padding: 3px 10px;
            border-radius: 5px;
            font-weight: 600;
            white-space: nowrap;
            border: none;
            transition: transform .1s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            cursor: pointer;
        }

        .action-btns .btn:hover {
            transform: translateY(-1px);
        }

        .action-btns .btn-view {
            background: #d1fae5;
            color: #065f46;
        }

        .action-btns .btn-edit {
            background: #fef3c7;
            color: #92400e;
        }

        .action-btns .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-btns .btn-add {
            background: #dbeafe;
            color: #1e40af;
        }

        /* ── DataTables overrides ─────────────────────────────────── */
        .index-wrapper .dataTables_wrapper .dataTables_info,
        .index-wrapper .dataTables_wrapper .dataTables_length,
        .index-wrapper .dataTables_wrapper .dataTables_filter {
            font-size: .82rem;
            color: #666;
        }

        .index-wrapper .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #2d5a8e !important;
            color: #fff !important;
            border-color: #2d5a8e !important;
            border-radius: 6px;
        }

        .module-header {
            padding-right: 15px;
            padding-left: 15px;
            padding-top: 10px;
        }

        .btn.btn-primary {
            background: #6eaef2;
            color: #0a0a0a;
            border: 1.5px solid rgba(255, 255, 255, .35);
            border-radius: 8px;
            font-weight: 600;
            font-size: .85rem;
            transition: all .2s;
            text-decoration: none;
        }
    </style>

    <div class="index-wrapper">
        <div class="card">
            <div class="index-header">
                <h4 class="module-header"><i class="fas fa-history"></i>{{ $config_data->module_name }}</h4>
                @can($config_data->module_perm_name . '_create')
                    <a href="{{ route("$config_data->module_route.create") }}" class="btn btn-primary float-end">
                        + Create New
                    </a>
                @endcan
            </div>

            <div class="card-body">
                <table id="dataTable" class="table table-bordered">
                    <thead>
                        {{-- Column headers --}}
                        <tr>
                            <th>#</th>
                            <th>PM Code</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Designation</th>
                            <th>Sub Unit</th>
                            <th>Unit</th>
                            <th>PAMU</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Assign Type</th>
                            <th>Geography</th>
                            <th>Rank</th>
                            <th>Years</th>
                            <th>Action</th>
                        </tr>
                        {{-- Per-column filter row (indices must match columns array below) --}}
                        <tr class="filter-row">
                            <th></th>{{-- 0: # --}}
                            <th><input type="text" placeholder="PM Code" /></th>{{-- 1 --}}
                            <th><input type="text" placeholder="YYYY-MM-DD" /></th>{{-- 2 --}}
                            <th><input type="text" placeholder="YYYY-MM-DD" /></th>{{-- 3 --}}
                            <th><input type="text" placeholder="Designation" /></th>{{-- 4 --}}
                            <th><input type="text" placeholder="Sub Unit" /></th>{{-- 5 --}}
                            <th><input type="text" placeholder="Unit" /></th>{{-- 6 --}}
                            <th><input type="text" placeholder="PAMU" /></th>{{-- 7 --}}
                            <th><input type="text" placeholder="Category" /></th>{{-- 8 --}}
                            <th><input type="text" placeholder="Type" /></th>{{-- 9 --}}
                            <th><input type="text" placeholder="Assign Type" /></th>{{-- 10 --}}
                            <th><input type="text" placeholder="Geography" /></th>{{-- 11 --}}
                            <th><input type="text" placeholder="Rank" /></th>{{-- 12 --}}
                            <th></th>{{-- 13: Years (no filter) --}}
                            <th></th>{{-- 14: Action (no filter) --}}
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Hidden DELETE form – reused by all rows --}}
    <form id="deleteForm" method="POST" style="display:none">
        @csrf
        @method('DELETE')
    </form>

@endsection

@section('scripts')
<script>
    const canView   = @json(auth()->user()->can($config_data->module_perm_name . '_show',   App\Models\AssignmentHistory::class));
    const canUpdate = @json(auth()->user()->can($config_data->module_perm_name . '_edit',   App\Models\AssignmentHistory::class));
    const canDelete = @json(auth()->user()->can($config_data->module_perm_name . '_delete', App\Models\AssignmentHistory::class));
    const canCreate = @json(auth()->user()->can($config_data->module_perm_name . '_create', App\Models\AssignmentHistory::class));
    const urlRoute  = "{{ $config_data->module_route }}";
    const csrfToken = "{{ csrf_token() }}";

    // ── Column definitions (15 columns, no Points) ─────────────────
    // Indices 0-14 must match the filter-row <th> order above.
    const dtColumns = [
        // 0 – row number
        {
            data: null, orderable: false, searchable: false,
            render: (d, t, r, meta) => meta.row + meta.settings._iDisplayStart + 1
        },
        // 1 – pm_code
        { data: 'pm_code', searchable: true },
        // 2 – start_date
        {
            data: 'start_date', searchable: true,
            render: d => d ? formatDate(d) : ''
        },
        // 3 – end_date
        {
            data: 'end_date', searchable: true,
            render: d => d ? formatDate(d) : ''
        },
        // 4 – designation
        { data: 'designations.name', searchable: true, defaultContent: '' },
        // 5 – subunit
        { data: 'subunit', searchable: true, defaultContent: '' },
        // 6 – unit
        { data: 'units.name', searchable: true, defaultContent: '' },
        // 7 – pamu
        { data: 'pamus.name', searchable: true, defaultContent: '' },
        // 8 – category / assignment
        { data: 'assignments.name', searchable: true, defaultContent: '' },
        // 9 – pri_sec_spec
        {
            data: 'pri_sec_spec', searchable: true,
            render: d => ({ primary: 'Primary', secondary: 'Secondary', special: 'Special' })[d] ?? (d ?? '')
        },
        // 10 – assignment type
        {
            data: 'assignment_type_name', searchable: true,
            render: d => d || ''
        },
        // 11 – geography
        {
            data: 'geography', searchable: true,
            render: d => ({ ncr: 'NCR', luzon: 'Luzon', visayas: 'Visayas', mindanao: 'Mindanao', foreign: 'Foreign Duty' })[d] ?? (d ?? '')
        },
        // 12 – rank
        { data: 'rank_during_completion', searchable: true, defaultContent: '' },
        // 13 – year_earned  (no Points column)
        {
            data: 'year_earned', searchable: false,
            render: d => `<span class="mono">${d != null ? parseFloat(d).toFixed(6) : ''}</span>`,
            className: 'mono'
        },
        // 14 – actions
        {
            data: 'id', orderable: false, searchable: false,
            render: function (id) {
                let b = '<div class="action-btns">';
                if (canView)
                    b += `<a href="/${urlRoute}/${id}" class="btn btn-view"><i class="fas fa-eye"></i> View</a>`;
                if (canUpdate)
                    b += `<a href="/${urlRoute}/${id}/edit" class="btn btn-edit"><i class="fas fa-pen"></i> Edit</a>`;
                if (canDelete)
                    b += `<button type="button" class="btn btn-delete" onclick="confirmDelete(${id})"><i class="fas fa-trash"></i></button>`;
                if (canCreate)
                    b += `<a href="/${urlRoute}/${id}/add-entry" class="btn btn-add"><i class="fas fa-plus"></i> Add</a>`;
                b += '</div>';
                return b;
            }
        },
    ];

    // ── DataTable init ──────────────────────────────────────────────
    const table = $('#dataTable').DataTable({
        processing:  true,
        serverSide:  true,
        ordering:    false,
        ajax:        "{{ route("$config_data->module_route.list") }}",
        pageLength:  50,
        lengthMenu:  [[10, 25, 50, 100], [10, 25, 50, 100]],
        scrollX:     true,
        columns:     dtColumns,
    });

    // ── Per-column filter (uses the <input> already in the filter-row) ──
    // Skip column 0 (#) and column 14 (action).
    table.columns().every(function (i) {
        if (i === 0 || i === 14) return;

        const column = this;
        let timer = null;

        $('input', this.header()).on('input change', function () {
            const val = this.value;
            clearTimeout(timer);
            timer = setTimeout(() => column.search(val).draw(), 400);
        });
    });

    // ── Delete helper ───────────────────────────────────────────────
    function confirmDelete(id) {
        if (!confirm('Are you sure you want to delete this record?')) return;

        const form = document.getElementById('deleteForm');
        form.action = `/${urlRoute}/${id}`;
        form.submit();
    }

    // ── Date formatter ──────────────────────────────────────────────
    function formatDate(str) {
        const d = new Date(str);
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        // Use UTC parts to avoid timezone day-shift
        return d.getUTCDate() + '-' + months[d.getUTCMonth()] + '-' + d.getUTCFullYear();
    }
</script>
@endsection