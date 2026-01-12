@extends('themes.master')
@section('page_title', 'Detail Absensi')

@section('content')
<div class="container-fluid">
@if(isset($studentDetail) && $studentDetail)

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Ketidakhadiran Siswa</h1>
        <a href="{{ route('v1.attendance.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @php
        $sData = is_array($student->data) ? $student->data : json_decode($student->data, true);
        $academic = $sData['academic'] ?? [];
        $class = $academic['class'] ?? '-';
        $grade = $academic['grade'] ?? '-';
        $period = $academic['period'] ?? '-';
        $absentCount = count($absentDetails);
    @endphp

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Profil Siswa</h6>
                    <span class="badge badge-info px-3 py-2">Total Absen: {{ $absentCount }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Nama:</strong> {{ $student->name }}</p>
                            <p class="mb-1"><strong>Kelas:</strong> {{ strtoupper($class) }}</p>
                            <p class="mb-1"><strong>Grade:</strong> {{ $grade }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Periode:</strong> {{ $period }}</p>
                            <p class="mb-1"><strong>Rentang Tahun Ajaran:</strong> {{ $startDate ? date('d/m/Y', strtotime($startDate)) : '-' }} - {{ $endDate ? date('d/m/Y', strtotime($endDate)) : '-' }}</p>
                        </div>
                    </div>

                    <hr>

                    @if($absentCount > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="8%">#</th>
                                        <th width="20%">Tanggal</th>
                                        <th width="15%">Status</th>
                                        <th width="57%">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($absentDetails as $idx => $absent)
                                        @php
                                            $absStatus = $absent['status'] ?? '';
                                            $badge = 'secondary';
                                            switch(strtolower($absStatus)) {
                                                case 'sakit':
                                                    $badge = 'info';
                                                    break;
                                                case 'izin':
                                                    $badge = 'warning';
                                                    break;
                                                case 'alpa':
                                                    $badge = 'danger';
                                                    break;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td>{{ isset($absent['attendance_date']) ? date('d/m/Y', strtotime($absent['attendance_date'])) : '-' }}</td>
                                            <td><span class="badge badge-{{ $badge }} px-3 py-2">{{ strtoupper($absStatus) }}</span></td>
                                            <td>{{ $absent['note'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-success mb-0 text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h5 class="mb-1">Tidak ada ketidakhadiran</h5>
                            <p class="mb-0">Siswa ini belum memiliki catatan ketidakhadiran pada Tahun Ajaran yang dipilih.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@else

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Absensi Siswa</h1>
        <a href="{{ route('v1.attendance.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Attendance Detail Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Absensi</h6>
                </div>
                <div class="card-body">
                    @php
                        $data = is_array($attendance->data) ? $attendance->data : json_decode($attendance->data, true);
                        $status = $data['status'] ?? 'Mixed';
                        $abstainList = $data['abstain'] ?? [];
                    @endphp

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Tanggal Absensi</th>
                                    <td>: {{ $attendance->attendance_date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Hari</th>
                                    <td>: {{ $attendance->dayOfWeek }}</td>
                                </tr>
                                <tr>
                                    <th>Kelas</th>
                                    <td>: <strong>{{ strtoupper($attendance->class) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Grade</th>
                                    <td>: {{ $data['grade'] ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Periode</th>
                                    <td>: {{ $data['period'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Semester</th>
                                    <td>: {{ $attendance->semester ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Siswa</th>
                                    <td>: <span class="badge badge-info">{{ $data['student_count'] ?? 0 }}</span></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>: 
                                        @if($status === 'hadir')
                                            <span class="badge badge-success">Semua Hadir</span>
                                        @else
                                            <span class="badge badge-warning">Ada yang Abstain</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Dicatat Oleh:</strong> {{ $data['recorded_by'] ?? '-' }}</p>
                            <p class="mb-1"><strong>Waktu Pencatatan:</strong> {{ $data['recorded_at'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Abstain List (if any) -->
            @if(!empty($abstainList))
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Daftar Siswa Tidak Hadir</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="50%">Nama Siswa</th>
                                    <th width="20%">Status</th>
                                    <th width="25%">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($abstainList as $index => $abstain)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $abstain['student_name'] ?? '-' }}</td>
                                        <td>
                                            @php
                                                $abstainStatus = $abstain['status'] ?? '';
                                                $badgeClass = 'secondary';
                                                switch(strtolower($abstainStatus)) {
                                                    case 'sakit':
                                                        $badgeClass = 'info';
                                                        break;
                                                    case 'izin':
                                                        $badgeClass = 'warning';
                                                        break;
                                                    case 'alpa':
                                                        $badgeClass = 'danger';
                                                        break;
                                                }
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">{{ strtoupper($abstainStatus) }}</span>
                                        </td>
                                        <td>{{ $abstain['note'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <p class="mb-1"><strong>Ringkasan:</strong></p>
                        <ul class="mb-0">
                            @php
                                $sakitCount = collect($abstainList)->where('status', 'sakit')->count();
                                $izinCount = collect($abstainList)->where('status', 'izin')->count();
                                $alpaCount = collect($abstainList)->where('status', 'alpa')->count();
                            @endphp
                            @if($sakitCount > 0)
                                <li>Sakit: {{ $sakitCount }} siswa</li>
                            @endif
                            @if($izinCount > 0)
                                <li>Izin: {{ $izinCount }} siswa</li>
                            @endif
                            @if($alpaCount > 0)
                                <li>Alpa: {{ $alpaCount }} siswa</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Attending Students List -->
            @if(isset($attendingStudents) && count($attendingStudents) > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Daftar Siswa Hadir</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th width="10%">#</th>
                                    <th width="65%">Nama Siswa</th>
                                    <th width="25%" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendingStudents as $index => $student)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $student->name }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-success">HADIR</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <p class="mb-0"><strong>Total Hadir:</strong> {{ count($attendingStudents) }} siswa</p>
                    </div>
                </div>
            </div>
            @endif
            @else
            <!-- All Students Present -->
            @if(isset($attendingStudents) && count($attendingStudents) > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Daftar Siswa Hadir</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-success text-center mb-3">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h5>Semua Siswa Hadir</h5>
                        <p class="mb-0">Tidak ada siswa yang abstain pada tanggal ini.</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th width="10%">#</th>
                                    <th width="65%">Nama Siswa</th>
                                    <th width="25%" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendingStudents as $index => $student)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $student->name }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-success">HADIR</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <p class="mb-0"><strong>Total Hadir:</strong> {{ count($attendingStudents) }} siswa</p>
                    </div>
                </div>
            </div>
            @else
            <div class="card shadow mb-4">
                <div class="card-body text-center text-success">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h5>Semua Siswa Hadir</h5>
                    <p class="text-muted">Tidak ada siswa yang abstain pada tanggal ini.</p>
                </div>
            </div>
            @endif
            @endif

        </div>
    </div>

@endif

</div>
@endsection
