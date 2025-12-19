@extends('themes.master')
@section('page_title', 'Riwayat Absensi Guru')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Absensi Guru</h1>
        <a href="{{ route('v1.attendance.teacher.index') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-calendar-check"></i> Input Absensi
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Absensi Mengajar</h6>
                </div>
                <div class="card-body">
                    @if($isAdmin && $teachers->count() > 0)
                        <!-- Teacher Filter for Admin -->
                        <form method="GET" class="mb-4">
                            <div class="form-row align-items-end">
                                <div class="form-group col-md-4">
                                    <label>Filter Guru</label>
                                    <select name="teacher" class="form-control">
                                        <option value="">-- Semua Guru --</option>
                                        @foreach($teachers as $teacherValue => $teacherLabel)
                                            <option value="{{ $teacherValue }}" {{ $teacherValue === $selectedTeacher ? 'selected' : '' }}>
                                                {{ $teacherLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    @if($selectedTeacher)
                                        <a href="{{ route('v1.attendance.teacher.history') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Reset
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    @endif
                    
                    @if($attendances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        @if($isAdmin)
                                            <th width="15%">Nama Guru</th>
                                        @endif
                                        <th width="12%">Tanggal</th>
                                        <th width="10%">Hari</th>
                                        <th width="13%">Tahun Ajaran</th>
                                        <th width="10%">Semester</th>
                                        <th width="10%">Status</th>
                                        <th width="10%">Jumlah Kelas</th>
                                        <th width="10%">Jumlah Mapel</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendances as $attendance)
                                        @php
                                            $date = \Carbon\Carbon::parse($attendance->attendance_date);
                                            $attendanceData = is_array($attendance->data) ? $attendance->data : [];
                                            $schedule = $attendanceData['schedule'] ?? [];
                                            $attendStatus = $attendanceData['attend_status'] ?? null;
                                            $totalKelas = is_array($schedule) ? count($schedule) : 0;
                                            $totalMapel = 0;
                                            if (is_array($schedule)) {
                                                foreach ($schedule as $item) {
                                                    if (isset($item['mapel']) && is_array($item['mapel'])) {
                                                        $totalMapel += count($item['mapel']);
                                                    }
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ ($attendances->currentPage() - 1) * $attendances->perPage() + $loop->iteration }}</td>
                                            @if($isAdmin)
                                                <td>{{ $attendance->teacher_name }}</td>
                                            @endif
                                            <td>{{ $date->format('d/m/Y') }}</td>
                                            <td>{{ $date->locale('id')->isoFormat('dddd') }}</td>
                                            <td>{{ $attendance->tahun_ajaran }}</td>
                                            <td>{{ $attendance->semester }}</td>
                                            <td>
                                                @if($attendStatus === 'hadir')
                                                    <span class="badge badge-success">Hadir</span>
                                                @elseif($attendStatus === 'izin')
                                                    <span class="badge badge-warning">Izin</span>
                                                @elseif($attendStatus === 'sakit')
                                                    <span class="badge badge-info">Sakit</span>
                                                @elseif($attendStatus === 'alpha')
                                                    <span class="badge badge-danger">Alpha</span>
                                                @else
                                                    <span class="badge badge-secondary">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $totalKelas }} Kelas</td>
                                            <td>{{ $totalMapel }} Mapel</td>
                                            <td>
                                                <button class="btn btn-info btn-sm" onclick="viewDetail('{{ $attendance->id }}')">
                                                    <i class="fas fa-eye"></i> Detail
                                                </button>
                                                <a href="{{ route('v1.attendance.teacher.index', ['date' => $attendance->attendance_date, 'guru' => $attendance->teacher_name]) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $attendances->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Belum ada riwayat absensi. Silakan input absensi terlebih dahulu.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Absensi Mengajar</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin"></i> Loading...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function viewDetail(attendanceId) {
    $('#detailModal').modal('show');
    $('#detailContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
    
    // Get attendance data via AJAX
    $.ajax({
        url: '/v1/attendance/teacher/detail/' + attendanceId,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let html = buildDetailHTML(response.data);
                $('#detailContent').html(html);
            } else {
                $('#detailContent').html('<div class="alert alert-danger">Gagal memuat data</div>');
            }
        },
        error: function() {
            $('#detailContent').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>');
        }
    });
}

function buildDetailHTML(data) {
    let html = '<div class="container-fluid">';
    
    // Header Info
    html += '<div class="row mb-3">';
    html += '<div class="col-md-6"><strong>Tanggal:</strong> ' + formatDate(data.attendance_date) + '</div>';
    html += '<div class="col-md-6"><strong>Guru:</strong> ' + data.teacher_name + '</div>';
    html += '<div class="col-md-6"><strong>Tahun Ajaran:</strong> ' + data.tahun_ajaran + '</div>';
    html += '<div class="col-md-6"><strong>Semester:</strong> ' + data.semester + '</div>';
    
    // Show attendance status if exists
    if (data.data && data.data.attend_status) {
        html += '<div class="col-md-6"><strong>Status Kehadiran:</strong> ' + getStatusBadge(data.data.attend_status) + '</div>';
        if (data.data.attend_notes) {
            html += '<div class="col-md-6"><strong>Keterangan:</strong> ' + data.data.attend_notes + '</div>';
        }
    }
    html += '</div>';
    
    // Schedule Details
    let schedule = data.data && data.data.schedule ? data.data.schedule : [];
    
    if (schedule && schedule.length > 0) {
        schedule.forEach(function(item, idx) {
            html += '<div class="card mb-3">';
            html += '<div class="card-header bg-light"><strong>' + item.hari + ' - Kelas ' + item.kelas + '</strong></div>';
            html += '<div class="card-body">';
            
            if (item.mapel && item.mapel.length > 0) {
                html += '<table class="table table-sm table-bordered">';
                html += '<thead><tr><th>Waktu</th><th>Mata Pelajaran</th><th>Status</th><th>Keterangan</th></tr></thead>';
                html += '<tbody>';
                
                item.mapel.forEach(function(mapel) {
                    let statusBadge = getStatusBadge(mapel.status);
                    html += '<tr>';
                    html += '<td>' + (mapel.waktu || '-') + '</td>';
                    html += '<td>' + (mapel.nama || '-') + '</td>';
                    html += '<td>' + statusBadge + '</td>';
                    html += '<td>' + (mapel.keterangan || '-') + '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
            } else {
                html += '<p class="text-muted">Tidak ada mata pelajaran</p>';
            }
            
            html += '</div></div>';
        });
    } else {
        html += '<div class="alert alert-warning">Tidak ada data jadwal</div>';
    }
    
    // Recorded Info
    if (data.data && data.data.recorded_at) {
        html += '<div class="alert alert-info mt-3">';
        html += '<small><strong>Dicatat pada:</strong> ' + formatDateTime(data.data.recorded_at) + '<br>';
        html += '<strong>Dicatat oleh:</strong> ' + (data.data.recorded_by || '-') + '</small>';
        html += '</div>';
    }
    
    html += '</div>';
    return html;
}

function getStatusBadge(status) {
    let badges = {
        'hadir': '<span class="badge badge-success">Hadir</span>',
        'izin': '<span class="badge badge-warning">Izin</span>',
        'sakit': '<span class="badge badge-info">Sakit</span>',
        'alpha': '<span class="badge badge-danger">Alpha</span>'
    };
    return badges[status] || '<span class="badge badge-secondary">' + (status || '-') + '</span>';
}

function formatDate(dateString) {
    let date = new Date(dateString);
    let options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}

function formatDateTime(dateTimeString) {
    let date = new Date(dateTimeString);
    let options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return date.toLocaleDateString('id-ID', options);
}

$(document).ready(function() {
    // Initialize DataTable if available and not already initialized
    if (typeof $.fn.DataTable !== 'undefined') {
        // Check if DataTable is already initialized
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().destroy();
        }
        
        // Determine sort column based on view (admin has extra column)
        var isAdmin = {{ $isAdmin ? 'true' : 'false' }};
        var sortColumn = isAdmin ? 2 : 1; // Date column index
        
        $('#dataTable').DataTable({
            "pageLength": 20,
            "order": [[sortColumn, 'desc']], // Sort by date descending
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    }
});
</script>
@endsection
