@extends('themes.master')
@section('page_title', 'Laporan Absen Guru')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3 text-gray-800">Laporan Absensi Guru</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('v1.reports.attendance.teachers') }}">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Nama Guru</label>
                        <input type="text" name="teacher_name" class="form-control" placeholder="Cari nama" value="{{ $filters['teacher_name'] }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Semester</label>
                        <select name="semester" class="form-control">
                            <option value="">Semua</option>
                            <option value="Semester Ganjil" {{ $filters['semester'] === 'Semester Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                            <option value="Semester Genap" {{ $filters['semester'] === 'Semester Genap' ? 'selected' : '' }}>Semester Genap</option>
                            <option value="1" {{ $filters['semester'] === '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ $filters['semester'] === '2' ? 'selected' : '' }}>2</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Terapkan</button>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data ({{ $attendances->count() }})</h6>
            <button class="btn btn-success" data-toggle="modal" data-target="#exportTeacherAttendanceModal">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="teacherAttendanceReportTable" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Guru</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th>Jumlah Jadwal</th>
                            <th>Dicatat Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $index => $attendance)
                            @php $data = is_array($attendance->data) ? $attendance->data : []; @endphp
                            @php $schedule = is_array($data['schedule'] ?? null) ? $data['schedule'] : []; @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ optional($attendance->attendance_date)->format('Y-m-d') }}</td>
                                <td>{{ $attendance->teacher_name }}</td>
                                <td>{{ $attendance->semester }}</td>
                                <td>{{ $data['attend_status'] ?? '' }}</td>
                                <td>{{ count($schedule) }}</td>
                                <td>{{ $data['recorded_by'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exportTeacherAttendanceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Kolom</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('v1.reports.attendance.teachers.export') }}">
                @csrf
                <input type="hidden" name="start_date" value="{{ $filters['start_date'] }}">
                <input type="hidden" name="end_date" value="{{ $filters['end_date'] }}">
                <input type="hidden" name="teacher_name" value="{{ $filters['teacher_name'] }}">
                <input type="hidden" name="semester" value="{{ $filters['semester'] }}">
                <div class="modal-body">
                    <div class="row">
                        @foreach($availableColumns as $key => $label)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="columns[]" value="{{ $key }}" id="tatt_col_{{ $loop->index }}" {{ in_array($key, $defaultColumns) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tatt_col_{{ $loop->index }}">{{ $label }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-download"></i> Download</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('#teacherAttendanceReportTable').DataTable({
            order: [[1, 'desc']]
        });
    });
</script>
@endpush
