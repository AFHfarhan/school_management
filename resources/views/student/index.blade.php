@extends('themes.master')
@section('page_title', 'Daftar Pendaftaran Siswa')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Daftar Siswa</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row">
                <div class="col-lg-6">
                    <h6 class="m-0 font-weight-bold text-primary">Data Siswa yang telah Mendaftar</h6>
                </div>
                <div class="col-lg-6" style="text-align: right;">
                    <a href="{{ route('v1.students.create')}}" class="btn btn-primary btn-icon-split">
                        <span class="icon text-white-50">
                            <i class="fa fa-plus-circle"></i>
                        </span>
                        <span class="text">Pendaftaran Siswa Baru</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTablePendaftaranSiswa" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Form Date</th>
                            <th>Form Reg</th>
                            <th>Birthdate</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                        <td>{{ $student->name }}</td>
                                <td>{{ $student->data['form_date'] ?? 'N/A' }}</td>
                                <td>{{ $student->data['form_reg'] ?? 'N/A' }}</td>
                                <td>{{ $student->data['personal']['birthdate'] ?? 'N/A' }}</td>
                                <td>
                                    @can('view', $student)
                                        <a href="{{ route('v1.students.show', $student) }}" class="btn btn-info btn-sm">View</a>
                                    @endcan
                                    @can('update', $student)
                                        <a href="{{ route('v1.students.edit', $student) }}" class="btn btn-primary btn-sm">Edit</a>
                                    @endcan
                                    @can('delete', $student)
                                        <form id="delete-form-{{ $student->id }}" action="{{ route('v1.students.destroy', $student->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm btn-delete" data-form-id="delete-form-{{ $student->id }}">Delete</button>
                                        </form>
                                    @endcan
                                </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data pendaftaran ini? Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteFormId = null;
    var modalEl = document.getElementById('confirmDeleteModal');
    var bsModal = null;

    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        try { bsModal = new bootstrap.Modal(modalEl); } catch (e) { bsModal = null; }
    }

    document.querySelectorAll('.btn-delete').forEach(function(btn){
        btn.addEventListener('click', function (e) {
            deleteFormId = this.getAttribute('data-form-id');
            if (bsModal) {
                bsModal.show();
            } else {
                // fallback to native confirm if bootstrap modal isn't available
                if (confirm('Apakah Anda yakin ingin menghapus data pendaftaran ini?')) {
                    var form = document.getElementById(deleteFormId);
                    if (form) form.submit();
                }
            }
        });
    });

    var confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            if (!deleteFormId) return;
            var form = document.getElementById(deleteFormId);
            if (form) form.submit();
            if (bsModal) bsModal.hide();
        });
    }
});
</script>
@endsection

