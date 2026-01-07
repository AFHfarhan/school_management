@extends('themes.master')
@section('page_title', 'Daftar Absensi')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Absensi</h1>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Attendance List Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Data Absensi Kelas</h6>
                    <a href="{{ route('v1.attendance.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Buat Absensi Siswa
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('v1.attendance.index') }}" class="mb-3">
                        <div class="form-row align-items-end">
                            <div class="col-md-3 mb-2">
                                <label for="month">Bulan</label>
                                <input type="month" id="month" name="month" class="form-control" value="{{ $selectedMonth }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="class">Kelas</label>
                                <select id="class" name="class" class="form-control">
                                    <option value="">-- Semua Kelas --</option>
                                    @if(isset($classes) && is_array($classes))
                                        @foreach($classes as $classKey => $grade)
                                            @php $val = strtoupper($classKey); @endphp
                                            <option value="{{ $val }}" {{ ($selectedClass ?? '') === $val ? 'selected' : '' }}>{{ strtoupper($classKey) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="period">Periode</label>
                                <select id="period" name="period" class="form-control">
                                    <option value="">-- Semua Periode --</option>
                                    <option value="Semester Ganjil" {{ ($selectedPeriod ?? '') === 'Semester Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                                    <option value="Semester Genap" {{ ($selectedPeriod ?? '') === 'Semester Genap' ? 'selected' : '' }}>Semester Genap</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2 text-right">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Filter</button>
                                <a href="{{ route('v1.attendance.index') }}" class="btn btn-light btn-block mt-2">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="attendanceListTable">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="15%">Tanggal Absensi</th>
                                    <th width="10%">Hari</th>
                                    <th width="10%">Kelas</th>
                                    <th width="10%">Periode</th>
                                    <th width="10%">Grade</th>
                                    <th width="15%">Jumlah Siswa</th>
                                    <th width="15%">Status</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $index => $attendance)
                                    @php
                                        $data = is_array($attendance->data) ? $attendance->data : json_decode($attendance->data, true);
                                        $studentCount = $data['student_count'] ?? 0;
                                        $status = $data['status'] ?? 'Mixed';
                                        $period = $data['period'] ?? '-';
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $attendance->attendance_date->format('d/m/Y') }}</td>
                                        <td>{{ $attendance->dayOfWeek }}</td>
                                        <td><strong>{{ strtoupper($attendance->class) }}</strong></td>
                                        <td>{{ $data['period'] ?? '-' }}</td>
                                        <td>{{ $data['grade'] ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $studentCount }}</span>
                                        </td>
                                        <td>
                                            @if($status === 'hadir')
                                                <span class="badge badge-success">Semua Hadir</span>
                                            @else
                                                <span class="badge badge-warning">Ada yang Abstain</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('v1.attendance.show', $attendance->id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('v1.attendance.edit', $attendance->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox"></i> Tidak ada data absensi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Student Absent History Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa & Riwayat Ketidakhadiran</h6>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('v1.attendance.index') }}" class="mb-3">
                        <div class="form-row align-items-end">
                            <div class="col-md-4 mb-2">
                                <label for="studentClassFilter">Kelas</label>
                                <select id="studentClassFilter" name="student_class" class="form-control">
                                    <option value="">-- Semua Kelas --</option>
                                    @php
                                        $uniqueClasses = [];
                                        foreach($studentsWithAbsent as $student) {
                                            $class = $student['class'];
                                            if ($class !== '-' && !in_array($class, $uniqueClasses)) {
                                                $uniqueClasses[] = $class;
                                            }
                                        }
                                        sort($uniqueClasses);
                                    @endphp
                                    @foreach($uniqueClasses as $cls)
                                        <option value="{{ $cls }}" {{ ($selectedStudentClass ?? '') === $cls ? 'selected' : '' }}>{{ strtoupper($cls) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8 mb-2 text-right">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Terapkan Filter</button>
                                <a href="{{ route('v1.attendance.index') }}" class="btn btn-light"><i class="fas fa-redo"></i> Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="studentAbsentTable">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="23%">Nama Siswa</th>
                                    <th width="10%">Kelas</th>
                                    <th width="8%">Total Absen</th>
                                    <th width="18%">Status</th>
                                    <th width="18%">Download Surat</th>
                                    <th width="13%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $count = 1; @endphp
                                @forelse($studentsWithAbsent as $index => $student)
                                    @if($student['class'] !== '-')
                                        @php
                                            $absentCount = $student['absent_count'];
                                            $statusBadge = '';
                                            $statusText = '';
                                            
                                            if ($absentCount < 5) {
                                                $statusBadge = 'badge-success';
                                                $statusText = 'Clear';
                                            } elseif ($absentCount >= 5 && $absentCount < 10) {
                                                $statusBadge = 'badge-warning';
                                                $statusText = 'Surat Peringatan 1';
                                            } elseif ($absentCount >= 10 && $absentCount < 15) {
                                                $statusBadge = 'badge-danger';
                                                $statusText = 'Surat Peringatan 2';
                                            } else {
                                                $statusBadge = 'badge-dark';
                                                $statusText = 'Surat Pemanggilan Orang Tua';
                                            }
                                        @endphp
                                        <tr class="student-row" data-class="{{ $student['class'] }}">
                                            <td>{{ $count++ }}</td>
                                            <td><strong>{{ $student['name'] }}</strong></td>
                                            <td>{{ strtoupper($student['class']) }}</td>
                                            <td>
                                                @if($student['absent_count'] > 0)
                                                    <span class="badge badge-danger">{{ $student['absent_count'] }}</span>
                                                @else
                                                    <span class="badge badge-success">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $statusBadge }} px-3 py-2">{{ $statusText }}</span>
                                            </td>
                                            <td>
                                                @if($absentCount >= 5 && $absentCount <= 9)
                                                    <a href="{{ route('v1.attendance.previewWarningLetter', ['studentId' => $student['id'], 'type' => 'sp1']) }}" class="btn btn-sm btn-info" title="Preview SP 1">
                                                        <i class="fas fa-eye"></i> Preview
                                                    </a>
                                                    <a href="{{ route('v1.attendance.downloadWarningLetter', ['studentId' => $student['id'], 'type' => 'sp1']) }}" class="btn btn-sm btn-warning" title="Download SP 1">
                                                        <i class="fas fa-download"></i> SP 1
                                                    </a>
                                                @elseif($absentCount >= 10 && $absentCount <= 14)
                                                    <a href="{{ route('v1.attendance.previewWarningLetter', ['studentId' => $student['id'], 'type' => 'sp2']) }}" class="btn btn-sm btn-info" title="Preview SP 2">
                                                        <i class="fas fa-eye"></i> Preview
                                                    </a>
                                                    <a href="{{ route('v1.attendance.downloadWarningLetter', ['studentId' => $student['id'], 'type' => 'sp2']) }}" class="btn btn-sm btn-danger" title="Download SP 2">
                                                        <i class="fas fa-download"></i> SP 2
                                                    </a>
                                                @elseif($absentCount >= 15)
                                                    <a href="{{ route('v1.attendance.previewWarningLetter', ['studentId' => $student['id'], 'type' => 'sp_ortu']) }}" class="btn btn-sm btn-info" title="Preview SP Orang Tua">
                                                        <i class="fas fa-eye"></i> Preview
                                                    </a>
                                                    <a href="{{ route('v1.attendance.downloadWarningLetter', ['studentId' => $student['id'], 'type' => 'sp_ortu']) }}" class="btn btn-sm btn-dark" title="Download SP Orang Tua">
                                                        <i class="fas fa-download"></i> SP Ortu
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('v1.attendance.studentDetail', $student['id']) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox"></i> Tidak ada data siswa.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('global_assets/css/dataTables.bootstrap4.min.css') }}">
<style>
    .absent-details small {
        display: inline-block;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('global_assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('global_assets/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Attendance table
    var attendanceTable = $('#attendanceListTable').DataTable({
        pageLength: 10,
        order: [[1, 'desc']],
        columnDefs: [
            { targets: 0, orderable: false, searchable: false }
        ]
    });

    attendanceTable.on('order.dt search.dt', function() {
        let i = 1;
        attendanceTable.column(0, { search: 'applied', order: 'applied' }).nodes().each(function(cell) {
            cell.innerHTML = i++;
        });
    }).draw();

    // Student absent table with custom filter
    var studentTable = $('#studentAbsentTable').DataTable({
        pageLength: 15,
        order: [[3, 'desc']],
        columnDefs: [
            { targets: [0, 5, 6], orderable: false, searchable: false }
        ]
    });

    studentTable.on('order.dt search.dt draw.dt', function() {
        let i = 1;
        studentTable.column(0, { search: 'applied', order: 'applied' }).nodes().each(function(cell) {
            cell.innerHTML = i++;
        });
    }).draw();
});
</script>
@endpush
