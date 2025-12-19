@extends('themes.master')
@section('page_title', 'Absensi Siswa')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Form Absensi Siswa</h1>
        <a href="{{ route('v1.attendance.index') ?? '#' }}" class="btn btn-secondary btn-sm">
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
            <!-- Search Form Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pencarian Siswa</h6>
                </div>
                <div class="card-body">
                    <form id="searchForm" method="GET" action="{{ route('v1.attendance.search') }}">
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label for="class">Kelas</label>
                                <select class="form-control" id="class" name="class" required onchange="autoFillGrade()">
                                    <option value="">-- Pilih Kelas --</option>
                                    @if(isset($classes) && is_array($classes))
                                        @foreach($classes as $classKey => $classValue)
                                            <option value="{{ strtoupper($classKey) }}" 
                                                    data-tingkat="{{ $classValue }}"
                                                    @if(request('class') == $classKey) selected @endif>
                                                {{ strtoupper($classKey) }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="period">Periode</label>
                                <select class="form-control" id="period" name="period" required>
                                    <option value="">-- Pilih Periode --</option>
                                    <option value="Semester Ganjil" @if(request('period') === 'Semester Ganjil') selected @endif>Semester Ganjil</option>
                                    <option value="Semester Genap" @if(request('period') === 'Semester Genap') selected @endif>Semester Genap</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="grade">Grade</label>
                                <input type="text" class="form-control" id="grade" name="grade" placeholder="Contoh: 10" value="{{ request('grade') }}" readonly required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Attendance Form Card -->
            @if(isset($students) && count($students) > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Absensi Siswa</h6>
                </div>
                <div class="card-body">
                    <form id="attendanceForm" method="POST" action="{{ route('v1.attendance.store') }}">
                        @csrf
                        
                        <!-- Hidden Fields for Filter Data -->
                        <input type="hidden" name="class" value="{{ request('class') }}">
                        <input type="hidden" name="period" value="{{ request('period') }}">
                        <input type="hidden" name="grade" value="{{ request('grade') }}">

                        <!-- Display Class, Attendance Date and Day -->
                        <div class="form-row mb-4">
                            <div class="col-md-3">
                                <label for="display_class"><strong>Kelas</strong></label>
                                <input type="text" class="form-control" id="display_class" value="{{ request('class') }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="attendance_date"><strong>Tanggal Absensi</strong></label>
                                <input type="date" class="form-control" id="attendance_date" name="attendance_date" required>
                            </div>
                            <div class="col-md-3">
                                <label for="dayOfWeek"><strong>Hari</strong></label>
                                <input type="text" class="form-control" id="dayOfWeek" name="dayOfWeek" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="semester"><strong>Semester</strong></label>
                                <input type="text" class="form-control" id="semester" name="semester" value="{{ request('period') }}" readonly>
                            </div>
                        </div>

                        <hr>

                        <!-- Student Attendance Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="attendanceTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="28%">Nama Siswa</th>
                                        <th width="17%">NIS</th>
                                        <th width="30%">Status Kehadiran</th>
                                        <th width="20%">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $index => $student)
                                        @php
                                            $data = is_array($student->data) ? $student->data : json_decode($student->data, true);
                                            $studentId = $data['academic']['student_id'] ?? $student->id;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $studentId }}</td>
                                            <td>
                                                <div class="btn-group btn-group-toggle" role="group" data-toggle="buttons">
                                                    <label class="btn btn-outline-success btn-sm">
                                                        <input type="radio" name="attendance[{{ $student->id }}]" value="hadir" required> 
                                                        <i class="fas fa-check"></i> Hadir
                                                    </label>
                                                    <label class="btn btn-outline-warning btn-sm">
                                                        <input type="radio" name="attendance[{{ $student->id }}]" value="sakit" required> 
                                                        <i class="fas fa-heartbeat"></i> Sakit
                                                    </label>
                                                    <label class="btn btn-outline-info btn-sm">
                                                        <input type="radio" name="attendance[{{ $student->id }}]" value="izin" required> 
                                                        <i class="fas fa-clipboard"></i> Izin
                                                    </label>
                                                    <label class="btn btn-outline-danger btn-sm">
                                                        <input type="radio" name="attendance[{{ $student->id }}]" value="alpa" required> 
                                                        <i class="fas fa-times"></i> Alpa
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm" name="notes[{{ $student->id }}]" placeholder="Catatan (opsional)">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <!-- Submit Button -->
                        <div class="form-row">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fas fa-redo"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Absensi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @elseif(request('class'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle"></i> Tidak ada siswa yang ditemukan dengan kriteria pencarian tersebut.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set today's date as default
    const today = new Date();
    const dateString = today.toISOString().split('T')[0];
    const attendanceDateField = document.getElementById('attendance_date');
    if (attendanceDateField) {
        document.getElementById('attendance_date').value = dateString;
        updateDayOfWeek();
        
        // Update day of week when date changes
        document.getElementById('attendance_date').addEventListener('change', updateDayOfWeek);
    }

    // Auto-fill grade on page load if class is already selected
    const classSelect = document.getElementById('class');
    if (classSelect && classSelect.value) {
        autoFillGrade();
    }
});

function autoFillGrade() {
    const classSelect = document.getElementById('class');
    const gradeInput = document.getElementById('grade');
    const selectedOption = classSelect.options[classSelect.selectedIndex];
    
    if (selectedOption && selectedOption.dataset.tingkat) {
        gradeInput.value = selectedOption.dataset.tingkat;
    } else {
        gradeInput.value = '';
    }
}

function updateDayOfWeek() {
    const dateInput = document.getElementById('attendance_date').value;
    if (!dateInput) return;

    const date = new Date(dateInput);
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const dayOfWeek = days[date.getDay()];
    document.getElementById('dayOfWeek').value = dayOfWeek;
}

function resetForm() {
    document.getElementById('attendanceForm').reset();
    updateDayOfWeek();
}

// Form validation before submit
const attendanceForm = document.getElementById('attendanceForm');
if (attendanceForm) {
    attendanceForm.addEventListener('submit', function(e) {
        const attendanceTable = document.getElementById('attendanceTable');
        const rows = attendanceTable.querySelectorAll('tbody tr');
        let allFilled = true;

        rows.forEach(row => {
            const radios = row.querySelectorAll('input[type="radio"]');
            const isChecked = Array.from(radios).some(radio => radio.checked);
            if (!isChecked) {
                allFilled = false;
            }
        });

        if (!allFilled) {
            e.preventDefault();
            alert('Mohon isi status kehadiran semua siswa sebelum menyimpan!');
            return false;
        }
    });
}
</script>

<style>
    .btn-group-toggle .btn {
        margin: 2px;
        cursor: pointer;
    }

    .btn-group-toggle .btn.active {
        box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
    }

    .btn-outline-success.active,
    .btn-outline-success:not(.disabled):not(:disabled).active,
    .btn-outline-success:not(.disabled):not(:disabled):active {
        background-color: #28a745;
        border-color: #28a745;
        color: #fff;
    }

    .btn-outline-warning.active,
    .btn-outline-warning:not(.disabled):not(:disabled).active,
    .btn-outline-warning:not(.disabled):not(:disabled):active {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #000;
    }

    .btn-outline-info.active,
    .btn-outline-info:not(.disabled):not(:disabled).active,
    .btn-outline-info:not(.disabled):not(:disabled):active {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: #fff;
    }

    .btn-outline-danger.active,
    .btn-outline-danger:not(.disabled):not(:disabled).active,
    .btn-outline-danger:not(.disabled):not(:disabled):active {
        background-color: #dc3545;
        border-color: #dc3545;
        color: #fff;
    }
</style>

@endsection
