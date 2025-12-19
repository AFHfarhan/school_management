@extends('themes.master')
@section('page_title', 'Kelola Akun (Super Admin)')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Kelola Akun</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tambah Akun Baru</h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <form action="{{ route('v1.teacher.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password (optional)</label>
                            <input type="password" name="password" class="form-control">
                            <small class="form-text text-muted">If left empty a default password will be generated.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-control">
                                <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                                <option value="tata_usaha" {{ old('role') == 'tata_usaha' ? 'selected' : '' }}>Tata Usaha</option>
                                <option value="ka_tata_usaha" {{ old('role') == 'ka_tata_usaha' ? 'selected' : '' }}>Ka. Tata Usaha</option>
                                <option value="kepala_sekolah" {{ old('role') == 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                <option value="kesiswaan" {{ old('role') == 'kesiswaan' ? 'selected' : '' }}>Kesiswaan</option>
                            </select>
                        </div>
                        <button class="btn btn-primary" type="submit">Buat Teacher</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Akun</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="teachersTable" width="100%">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Terakhir Login</th>
                                    <th width="25%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teachers as $t)
                                    @php 
                                        $td = is_array($t->data) ? $t->data : json_decode($t->data, true) ?? [];
                                        $isActive = $td['isActive'] ?? 1;
                                        $statusBadge = $isActive == 1 ? 'badge-success' : ($isActive == 2 ? 'badge-warning' : 'badge-danger');
                                        $statusText = $isActive == 1 ? 'Aktif' : ($isActive == 2 ? 'Tidak Aktif' : 'Dihapus');
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $t->name }}</strong></td>
                                        <td>{{ $t->email }}</td>
                                        <td><span class="badge badge-info">{{ $td['role'] ?? '-' }}</span></td>
                                        <td><span class="badge {{ $statusBadge }}">{{ $statusText }}</span></td>
                                        <td><small>{{ $td['latest_login'] ?? '-' }}</small></td>
                                        <td class="text-center">
                                            <a href="{{ route('v1.teacher.edit', $t->id) }}" class="btn btn-sm btn-info" title="Edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            @if($isActive == 1)
                                                <form method="POST" action="{{ route('v1.teacher.deactivate', $t->id) }}" style="display:inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Deactivate" onclick="return confirm('Deactivate this teacher?')">
                                                        <i class="fas fa-ban"></i> Nonactive
                                                    </button>
                                                </form>
                                            @elseif($isActive == 2)
                                                <form method="POST" action="{{ route('v1.teacher.reactivate', $t->id) }}" style="display:inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-success" title="Reactivate" onclick="return confirm('Reactivate this teacher?')">
                                                        <i class="fas fa-check"></i> Reactivate
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('v1.teacher.delete', $t->id) }}" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this teacher?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox"></i> Tidak ada data guru
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

@section('scripts')
<script>
$(document).ready(function(){
    $('#teachersTable').DataTable({
        "order": [[0, "asc"]],
        "language": {
            "emptyTable": "Tidak ada data guru"
        }
    });
});
</script>
@endsection
