@extends('themes.master')
@section('page_title', 'Laporan Pendaftaran Siswa')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3 text-gray-800">Laporan Pendaftaran Siswa</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('v1.reports.students') }}">
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
                        <label>Kelas/Grade</label>
                        <input type="text" name="grade" class="form-control" placeholder="Contoh: X" value="{{ $filters['grade'] }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Program/Jurusan</label>
                        <input type="text" name="program" class="form-control" placeholder="Contoh: RPL" value="{{ $filters['program'] }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Terapkan</button>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data ({{ $students->count() }})</h6>
            <button class="btn btn-success" data-toggle="modal" data-target="#exportStudentModal">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="studentReportTable" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Tanggal Formulir</th>
                            <th>No Registrasi</th>
                            <th>Grade</th>
                            <th>Program</th>
                            <th>Tanggal Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $student)
                            @php $data = is_array($student->data) ? $student->data : []; @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $data['form_date'] ?? '' }}</td>
                                <td>{{ $data['form_reg'] ?? '' }}</td>
                                <td>{{ $data['grade'] ?? '' }}</td>
                                <td>{{ $data['program'] ?? '' }}</td>
                                <td>{{ optional($student->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exportStudentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Kolom</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('v1.reports.students.export') }}">
                @csrf
                <input type="hidden" name="start_date" value="{{ $filters['start_date'] }}">
                <input type="hidden" name="end_date" value="{{ $filters['end_date'] }}">
                <input type="hidden" name="grade" value="{{ $filters['grade'] }}">
                <input type="hidden" name="program" value="{{ $filters['program'] }}">
                <div class="modal-body">
                    <div class="row">
                        @foreach($availableColumns as $key => $label)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="columns[]" value="{{ $key }}" id="stu_col_{{ $loop->index }}" {{ in_array($key, $defaultColumns) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="stu_col_{{ $loop->index }}">{{ $label }}</label>
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
        $('#studentReportTable').DataTable({
            order: [[6, 'desc']]
        });
    });
</script>
@endpush
