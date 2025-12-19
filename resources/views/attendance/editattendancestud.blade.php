@extends('themes.master')
@section('page_title', 'Edit Absensi')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Absensi Siswa</h1>
        <a href="{{ route('v1.attendance.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Success Message -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @php
                $data = is_array($attendance->data) ? $attendance->data : json_decode($attendance->data, true);
            @endphp

            <!-- Attendance Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Absensi</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Kelas:</strong> {{ strtoupper($attendance->class) }}</p>
                            <p><strong>Grade:</strong> {{ $data['grade'] ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Periode:</strong> {{ $data['period'] ?? '-' }}</p>
                            <p><strong>Semester:</strong> {{ $attendance->semester ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Tanggal:</strong> {{ $attendance->attendance_date->format('d/m/Y') }}</p>
                            <p><strong>Hari:</strong> {{ $attendance->dayOfWeek }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Attendance Form Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Status Kehadiran</h6>
                </div>
                <div class="card-body">
                    <form id="attendanceForm" method="POST" action="{{ route('v1.attendance.update', $attendance->id) }}">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="class" value="{{ $attendance->class }}">
                        <input type="hidden" name="period" value="{{ $data['period'] ?? '' }}">
                        <input type="hidden" name="grade" value="{{ $data['grade'] ?? '' }}">
                        <input type="hidden" name="attendance_date" value="{{ $attendance->attendance_date->format('Y-m-d') }}">
                        <input type="hidden" name="dayOfWeek" value="{{ $attendance->dayOfWeek }}">
                        <input type="hidden" name="semester" value="{{ $attendance->semester }}">

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="33%">Nama Siswa</th>
                                        <th width="42%" class="text-center">Status Kehadiran</th>
                                        <th width="20%">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $index => $student)
                                        @php
                                            $studentData = is_array($student->data) ? $student->data : json_decode($student->data, true);
                                            
                                            // Get current attendance status for this student
                                            $currentStatus = 'hadir'; // default
                                            $abstainList = $data['abstain'] ?? [];
                                            foreach ($abstainList as $abstain) {
                                                if ($abstain['student_name'] === $student->name) {
                                                    $currentStatus = $abstain['status'];
                                                    break;
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $student->name }}</strong>
                                                <input type="hidden" name="student_ids[]" value="{{ $student->id }}">
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-around">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" 
                                                               name="attendance[{{ $student->id }}]" 
                                                               id="hadir_{{ $student->id }}" 
                                                               value="hadir"
                                                               {{ $currentStatus === 'hadir' ? 'checked' : '' }}>
                                                        <label class="form-check-label text-success" for="hadir_{{ $student->id }}">
                                                            <i class="fas fa-check-circle"></i> Hadir
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" 
                                                               name="attendance[{{ $student->id }}]" 
                                                               id="sakit_{{ $student->id }}" 
                                                               value="sakit"
                                                               {{ $currentStatus === 'sakit' ? 'checked' : '' }}>
                                                        <label class="form-check-label text-info" for="sakit_{{ $student->id }}">
                                                            <i class="fas fa-notes-medical"></i> Sakit
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" 
                                                               name="attendance[{{ $student->id }}]" 
                                                               id="izin_{{ $student->id }}" 
                                                               value="izin"
                                                               {{ $currentStatus === 'izin' ? 'checked' : '' }}>
                                                        <label class="form-check-label text-warning" for="izin_{{ $student->id }}">
                                                            <i class="fas fa-clipboard-check"></i> Izin
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" 
                                                               name="attendance[{{ $student->id }}]" 
                                                               id="alpa_{{ $student->id }}" 
                                                               value="alpa"
                                                               {{ $currentStatus === 'alpa' ? 'checked' : '' }}>
                                                        <label class="form-check-label text-danger" for="alpa_{{ $student->id }}">
                                                            <i class="fas fa-times-circle"></i> Alpa
                                                        </label>
                                                    </div>
                                                </div>
                                            </td>
                                            @php
                                                $currentNote = '';
                                                foreach ($abstainList as $ab) {
                                                    if (($ab['student_name'] ?? null) === $student->name) {
                                                        $currentNote = $ab['note'] ?? '';
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            <td>
                                                <input type="text" class="form-control form-control-sm" name="notes[{{ $student->id }}]" value="{{ $currentNote }}" placeholder="Catatan (opsional)">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox"></i> Tidak ada data siswa untuk kelas ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(count($students) > 0)
                        <div class="mt-4 d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                <i class="fas fa-times"></i> Batal
                            </button>
                            <div>
                                <button type="reset" class="btn btn-warning">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Summary Card -->
            @if(count($students) > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Ringkasan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h4 class="text-success" id="hadirCount">0</h4>
                            <p class="mb-0">Hadir</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-info" id="sakitCount">0</h4>
                            <p class="mb-0">Sakit</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-warning" id="izinCount">0</h4>
                            <p class="mb-0">Izin</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-danger" id="alpaCount">0</h4>
                            <p class="mb-0">Alpa</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize counts
    updateSummary();

    // Update summary on any radio change
    document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
        radio.addEventListener('change', updateSummary);
    });

    function updateSummary() {
        let hadir = 0, sakit = 0, izin = 0, alpa = 0;
        
        document.querySelectorAll('input[type="radio"]:checked').forEach(function(radio) {
            switch(radio.value) {
                case 'hadir': hadir++; break;
                case 'sakit': sakit++; break;
                case 'izin': izin++; break;
                case 'alpa': alpa++; break;
            }
        });

        document.getElementById('hadirCount').textContent = hadir;
        document.getElementById('sakitCount').textContent = sakit;
        document.getElementById('izinCount').textContent = izin;
        document.getElementById('alpaCount').textContent = alpa;
    }

    // Form validation
    document.getElementById('attendanceForm').addEventListener('submit', function(e) {
        const checkedRadios = document.querySelectorAll('input[type="radio"]:checked');
        const totalStudents = document.querySelectorAll('input[name="student_ids[]"]').length;
        
        if (checkedRadios.length !== totalStudents) {
            e.preventDefault();
            alert('Mohon lengkapi status kehadiran untuk semua siswa!');
            return false;
        }
    });
});
</script>
@endsection
