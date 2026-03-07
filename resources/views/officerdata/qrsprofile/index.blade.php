@extends('layouts.app')
@section('content')

    <div class="index-wrapper">
        <div class="card">
            <div class="index-header">
                <h4><i class="fas fa-id-card"></i> {{ $config_data->module_name }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="qrsTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle text-center">Nr</th>
                                <th rowspan="2" class="align-middle text-center">PM Code</th>
                                <th rowspan="2" class="align-middle text-center">SRTY</th>
                                <th rowspan="2" class="align-middle text-center">RANK</th>
                                <th rowspan="2" class="align-middle text-center">NAME</th>
                                <th rowspan="2" class="align-middle text-center">AFPSN</th>
                                <th rowspan="2" class="align-middle text-center">AFPOS</th>
                                <th rowspan="2" class="align-middle text-center">SEX</th>
                                <th rowspan="2" class="align-middle text-center">DOB</th>
                                <th rowspan="2" class="align-middle text-center">SOC</th>
                                <th rowspan="2" class="align-middle text-center">Current Designation</th>
                                <th rowspan="2" class="align-middle text-center">Current Unit</th>
                                <th colspan="13" class="text-center table-info">Assignment Points (Gained)</th>
                                <th colspan="4" class="text-center table-success">Command</th>
                                <th colspan="3" class="text-center table-warning">Category</th>
                                <th colspan="4" class="text-center table-secondary">Area of Operations</th>
                                <th colspan="5" class="text-center table-primary">Military Schooling</th>
                                <th colspan="3" class="text-center table-danger">Civil Education</th>
                                <th rowspan="2" class="align-middle text-center">Civil Service Eligibility</th>
                                <th rowspan="2" class="align-middle text-center">Specialization Courses</th>
                                <th rowspan="2" class="align-middle text-center table-dark text-white">Current QRS Score
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center">GUA Staff</th>
                                <th class="text-center">HPA Staff</th>
                                <th class="text-center">PAMU Staff</th>
                                <th class="text-center">Brigade Staff</th>
                                <th class="text-center">Battalion Staff</th>
                                <th class="text-center">Company Ex-O</th>
                                <th class="text-center">Special Duty</th>
                                <th class="text-center">Foreign Duty</th>
                                <th class="text-center">Instructor Duty</th>
                                <th class="text-center">ResCom Duty</th>
                                <th class="text-center">Reassigned / Duty / DS</th>
                                <th class="text-center">Schooling (Assign)</th>
                                <th class="text-center">Attached / Unassigned</th>
                                <th class="text-center">Platoon Leader</th>
                                <th class="text-center">Company Commander</th>
                                <th class="text-center">Battalion Commander</th>
                                <th class="text-center">Brigade Commander</th>
                                <th class="text-center">Category A</th>
                                <th class="text-center">Category B</th>
                                <th class="text-center">Category C</th>
                                <th class="text-center">NCR</th>
                                <th class="text-center">Luzon</th>
                                <th class="text-center">Visayas</th>
                                <th class="text-center">Mindanao</th>
                                <th class="text-center">Pre-Entry Course</th>
                                <th class="text-center">Officer Basic Course</th>
                                <th class="text-center">Officer Advance Course</th>
                                <th class="text-center">Staff Officer Course</th>
                                <th class="text-center">CGSC</th>
                                <th class="text-center">Undergraduate</th>
                                <th class="text-center">Graduate</th>
                                <th class="text-center">Post Graduate</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="filter-row">
                                <th></th>
                                <th><input type="text" placeholder="PM Code" class="form-control form-control-sm col-filter"
                                        data-col="1"></th>
                                <th><input type="text" placeholder="SRTY" class="form-control form-control-sm col-filter"
                                        data-col="2"></th>
                                <th><input type="text" placeholder="RANK" class="form-control form-control-sm col-filter"
                                        data-col="3"></th>
                                <th><input type="text" placeholder="NAME" class="form-control form-control-sm col-filter"
                                        data-col="4"></th>
                                <th><input type="text" placeholder="AFPSN" class="form-control form-control-sm col-filter"
                                        data-col="5"></th>
                                <th><input type="text" placeholder="AFPOS" class="form-control form-control-sm col-filter"
                                        data-col="6"></th>
                                <th><input type="text" placeholder="SEX" class="form-control form-control-sm col-filter"
                                        data-col="7"></th>
                                <th><input type="text" placeholder="DOB" class="form-control form-control-sm col-filter"
                                        data-col="8"></th>
                                <th><input type="text" placeholder="SOC" class="form-control form-control-sm col-filter"
                                        data-col="9"></th>
                                <th><input type="text" placeholder="Designation"
                                        class="form-control form-control-sm col-filter" data-col="10"></th>
                                <th><input type="text" placeholder="Unit" class="form-control form-control-sm col-filter"
                                        data-col="11"></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        let url_route = "{{ $config_data->module_route }}";

        const table = $('#qrsTable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            order: [],
            deferRender: true,
            ajax: {
                url: "{{ route('qrsprofiles.list') }}",
                type: 'GET',
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('DataTables AJAX Error:', textStatus, errorThrown, jqXHR.responseText);
                }
            },
            pageLength: 50,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            scrollX: true,
            autoWidth: false,
            orderCellsTop: true,
            dom: 'lBfrtip',
            buttons: [
                { extend: 'copy', className: 'btn btn-sm btn-default', text: '<i class="fas fa-copy"></i> Copy', exportOptions: { columns: ':visible' } },
                { extend: 'csv', className: 'btn btn-sm btn-default', text: '<i class="fas fa-file-csv"></i> CSV', exportOptions: { columns: ':visible' } },
                { extend: 'excel', className: 'btn btn-sm btn-default', text: '<i class="fas fa-file-excel"></i> Excel', exportOptions: { columns: ':visible' } },
                { extend: 'pdf', className: 'btn btn-sm btn-default', text: '<i class="fas fa-file-pdf"></i> PDF', exportOptions: { columns: ':visible' } },
                { extend: 'print', className: 'btn btn-sm btn-default', text: '<i class="fas fa-print"></i> Print', exportOptions: { columns: ':visible' } },
                { extend: 'colvis', className: 'btn btn-sm btn-info', text: '<i class="fas fa-columns"></i> Column Visibility' }
            ],
            columns: [
                {
                    data: null, orderable: false, searchable: false,
                    render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }
                },
                {
                    data: 'PM_CODE', searchable: true,
                    render: function (data, type, row) {
                        return `<a href="/profile/${data}" target="_blank" class="text-primary font-weight-bold">
                                        ${data}
                                    </a>`;
                    }

                },
                { data: 'SRTY', searchable: true, defaultContent: '-' },
                { data: 'RANK', searchable: true, defaultContent: '-' },
                { data: 'NAME', searchable: true, defaultContent: '-' },
                { data: 'AFPSN', searchable: true, defaultContent: '-' },
                { data: 'AFPOS', searchable: true, defaultContent: '-' },
                { data: 'SEX', searchable: true, defaultContent: '-' },
                { data: 'DOB', searchable: true, defaultContent: '-' },
                { data: 'SOC', searchable: true, defaultContent: '-' },
                { data: 'designation_name', searchable: true, defaultContent: '-' },
                { data: 'unit_name', searchable: true, defaultContent: '-' },
                { data: 'pts_gua_staff', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_hpa_staff', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_pamu_staff', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_brigade_staff', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_battalion_staff', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_company_exo', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_special_duty', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_foreign_duty', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_instructor_duty', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_rescom_duty', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_reassigned', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_schooling_assign', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_attached', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_platoon_leader', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_company_commander', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_battalion_commander', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_brigade_commander', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_category_a', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_category_b', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_category_c', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_ncr', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_luzon', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_visayas', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'pts_mindanao', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'sch_pre_entry', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'sch_obc', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'sch_oac', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'sch_soc', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'sch_cgsc', visible: false, searchable: false, orderable: false, defaultContent: '-', className: 'text-center' },
                { data: 'edu_undergrad', visible: false, searchable: false, orderable: false, defaultContent: '-' },
                { data: 'edu_graduate', visible: false, searchable: false, orderable: false, defaultContent: '-' },
                { data: 'edu_postgrad', visible: false, searchable: false, orderable: false, defaultContent: '-' },
                { data: 'cse', visible: false, searchable: false, orderable: false, defaultContent: '-' },
                { data: 'specializations', visible: false, searchable: false, orderable: false, defaultContent: '-' },
                {
                    data: 'qrs_score', searchable: false, orderable: false, className: 'text-center fw-bold',
                    render: function (data, type) {
                        if (type === 'display' && data !== null && data !== undefined) {
                            const score = parseFloat(data);
                            if (isNaN(score)) return '-';
                            const cls = score >= 70 ? 'text-success' : score >= 50 ? 'text-warning' : 'text-danger';
                            return '<span class="' + cls + '">' + score.toFixed(2) + '</span>';
                        }
                        return data ?? '-';
                    }
                },
            ],
            initComplete: function () {
                this.api().columns.adjust().draw(false);
            },
        });

        let searchTimers = {};
        $(document).on('input change', '.col-filter', function () {
            const colIdx = parseInt($(this).data('col'), 10);
            const value = this.value;
            clearTimeout(searchTimers[colIdx]);
            searchTimers[colIdx] = setTimeout(function () {
                table.column(colIdx).search(value).draw();
            }, 500);
        });

        table.on('column-visibility.dt', function () {
            setTimeout(function () { table.columns.adjust(); }, 10);
        });
    </script>
@endsection