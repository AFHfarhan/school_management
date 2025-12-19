@extends('themes.master')
@section('page_title', 'Absensi Guru')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Absensi Guru - {{ $teacherName }}</h1>
        <a href="{{ route('v1.attendance.teacher.history') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-history"></i> Riwayat Absensi
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Absensi Mengajar</h6>
                </div>
                <div class="card-body">
                    <!-- Date Selection -->
                    <form method="GET" class="mb-4">
                        <div class="form-row align-items-end">
                            <div class="form-group col-md-4">
                                <label>Guru</label>
                                <select name="guru" class="form-control" required>
                                    @foreach($teachers as $teacherValue => $teacherLabel)
                                        <option value="{{ $teacherValue }}" {{ $teacherValue === $selectedGuru ? 'selected' : '' }}>
                                            {{ $teacherLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Tanggal</label>
                                <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" required>
                            </div>
                            <div class="form-group col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-calendar-alt"></i> Lihat Jadwal
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="alert alert-info">
                        <strong>Informasi:</strong><br>
                        Tanggal: {{ \Carbon\Carbon::parse($selectedDate)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}<br>
                        Hari: {{ $dayName }}<br>
                        Tahun Ajaran: {{ $tahunAjaran }}<br>
                        Semester: {{ $semester }}
                    </div>

                    <form method="POST" action="{{ route('v1.attendance.teacher.store') }}" id="attendanceForm">
                        @csrf
                        <input type="hidden" name="teacher_name" value="{{ $selectedGuru }}">
                        <input type="hidden" name="attendance_date" value="{{ $selectedDate }}">
                        <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">
                        <input type="hidden" name="semester" value="{{ $semester }}">

                        @if(count($daySchedule) > 0)
                            <h5 class="mb-3">Jadwal Mengajar Hari Ini</h5>

                            @foreach($daySchedule as $idx => $scheduleItem)
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <strong>{{ $scheduleItem['hari'] ?? '-' }} - Kelas {{ $scheduleItem['kelas'] ?? '-' }}</strong>
                                    </div>
                                    <div class="card-body">
                                        @if(isset($scheduleItem['mapel']) && is_array($scheduleItem['mapel']))
                                            <input type="hidden" name="schedule[{{ $idx }}][hari]" value="{{ $scheduleItem['hari'] ?? '' }}">
                                            <input type="hidden" name="schedule[{{ $idx }}][kelas]" value="{{ $scheduleItem['kelas'] ?? '' }}">

                                            <table class="table table-bordered table-sm">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th width="20%">Waktu</th>
                                                        <th width="40%">Mata Pelajaran</th>
                                                        <th width="20%">Status</th>
                                                        <th width="20%">Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($scheduleItem['mapel'] as $mIdx => $mapel)
                                                        @php
                                                            $existingMapel = null;
                                                            if ($existingAttendance) {
                                                                $existingData = is_array($existingAttendance->data) ? $existingAttendance->data : [];
                                                                $existingSchedule = $existingData['schedule'] ?? [];
                                                                if (isset($existingSchedule[$idx]['mapel'][$mIdx])) {
                                                                    $existingMapel = $existingSchedule[$idx]['mapel'][$mIdx];
                                                                }
                                                            }
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $mapel['waktu'] ?? '-' }}</td>
                                                            <td>
                                                                <strong>{{ $mapel['nama'] ?? '-' }}</strong>
                                                                <input type="hidden" name="schedule[{{ $idx }}][mapel][{{ $mIdx }}][waktu]" value="{{ $mapel['waktu'] ?? '' }}">
                                                                <input type="hidden" name="schedule[{{ $idx }}][mapel][{{ $mIdx }}][nama]" value="{{ $mapel['nama'] ?? '' }}">
                                                            </td>
                                                            <td>
                                                                <select class="form-control form-control-sm" name="schedule[{{ $idx }}][mapel][{{ $mIdx }}][status]" required>
                                                                    <option value="">-- Pilih --</option>
                                                                    <option value="hadir" {{ ($existingMapel['status'] ?? '') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                                    <option value="izin" {{ ($existingMapel['status'] ?? '') === 'izin' ? 'selected' : '' }}>Izin</option>
                                                                    <option value="sakit" {{ ($existingMapel['status'] ?? '') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                                    <option value="alpha" {{ ($existingMapel['status'] ?? '') === 'alpha' ? 'selected' : '' }}>Alpha</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm" name="schedule[{{ $idx }}][mapel][{{ $mIdx }}][keterangan]" placeholder="Opsional" value="{{ $existingMapel['keterangan'] ?? '' }}">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p class="text-muted">Tidak ada mata pelajaran terdaftar</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-info-circle"></i> 
                                Tidak ada jadwal mengajar untuk hari <strong>{{ $dayName }}</strong> pada tanggal yang dipilih.
                            </div>

                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong>Status Kehadiran Hari Ini</strong>
                                </div>
                                <div class="card-body">
                                    @php
                                        $existingStatus = '';
                                        $existingNotes = '';
                                        if ($existingAttendance) {
                                            $existingData = is_array($existingAttendance->data) ? $existingAttendance->data : [];
                                            $existingStatus = $existingData['attend_status'] ?? '';
                                            $existingNotes = $existingData['attend_notes'] ?? '';
                                        }
                                    @endphp
                                    <div class="form-group">
                                        <label>Status Kehadiran <span class="text-danger">*</span></label>
                                        <select class="form-control" name="attend_status" id="attendStatus" required>
                                            <option value="">-- Pilih Status --</option>
                                            <option value="hadir" {{ $existingStatus === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                            <option value="izin" {{ $existingStatus === 'izin' ? 'selected' : '' }}>Izin</option>
                                            <option value="sakit" {{ $existingStatus === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                            <option value="alpha" {{ $existingStatus === 'alpha' ? 'selected' : '' }}>Alpha</option>
                                        </select>
                                        <small class="form-text text-muted">Tidak ada jadwal mengajar, cukup isi status kehadiran harian.</small>
                                    </div>
                                    <div class="form-group" id="attendNotesGroup" style="display: {{ $existingStatus && $existingStatus !== 'hadir' ? 'block' : 'none' }};">
                                        <label>Keterangan</label>
                                        <textarea class="form-control" name="attend_notes" id="attendNotes" rows="3" placeholder="Masukkan keterangan...">{{ $existingNotes }}</textarea>
                                        <small class="form-text text-muted">Wajib diisi untuk status selain Hadir.</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="text-right mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Absensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Show/hide attend notes based on status
    $('#attendStatus').on('change', function() {
        var status = $(this).val();
        if (status && status !== 'hadir') {
            $('#attendNotesGroup').show();
            $('#attendNotes').prop('required', true);
        } else {
            $('#attendNotesGroup').hide();
            $('#attendNotes').prop('required', false).val('');
        }
    });

    // Optional: Add validation or interactive features
    $('#attendanceForm').on('submit', function(e) {
        let allFilled = true;
        $('select[name*="[status]"]').each(function() {
            if (!$(this).val()) {
                allFilled = false;
            }
        });
        
        if (!allFilled) {
            if (!confirm('Beberapa status absensi belum diisi. Lanjutkan?')) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endsection
