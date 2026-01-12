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

    <!-- Bulk Teacher Import Section -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Import Akun Guru dari Excel/CSV/ODS</h6>
                </div>
                <div class="card-body">
                    @if(session('teacher_import_success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('teacher_import_success') }}
                        </div>
                    @endif
                    @if(session('teacher_import_error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> {{ session('teacher_import_error') }}
                        </div>
                    @endif
                    @if(session('teacher_import_errors') && is_array(session('teacher_import_errors')))
                        <div class="alert alert-warning">
                            <strong>Terdapat error pada beberapa baris:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach(session('teacher_import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <h6 class="font-weight-bold"><i class="fas fa-info-circle"></i> Format File yang Diperlukan:</h6>
                        <p class="mb-2">File Excel/CSV harus memiliki kolom-kolom berikut (urutan bebas):</p>
                        <ul>
                            <li><code>name</code> - Nama lengkap guru (wajib)</li>
                            <li><code>email</code> - Email guru (wajib, harus unik)</li>
                            <li><code>password</code> - Password (opsional, default: password123)</li>
                            <li><code>role</code> - Role guru (opsional, default: guru). Pilihan: guru, tata_usaha, ka_tata_usaha, kepala_sekolah, kesiswaan</li>
                        </ul>
                        <p class="mb-0 mt-2">
                            <small class="text-muted">
                                * Jika kolom password kosong, sistem akan otomatis menggunakan password default: <strong>password123</strong><br>
                                * Sistem mendukung format: .xlsx, .csv, .ods<br>
                                * Format .xls (Excel 97-2003) tidak didukung. Silakan konversi ke .xlsx atau .ods, atau gunakan .csv.<br>
                                * Jika email sudah ada di database, data guru akan diperbarui (termasuk password jika diisi)
                            </small>
                        </p>
                    </div>

                    <div class="alert alert-secondary border">
                        <h6 class="font-weight-bold mb-2"><i class="fas fa-lightbulb"></i> Tips Konversi dari CSV ke XLSX (jika diperlukan)</h6>
                        <ul class="mb-0">
                            <li><strong>Microsoft Excel</strong>: Buka file .csv → pilih <em>File</em> → <em>Save As</em> → pilih tipe <em>Excel Workbook (*.xlsx)</em> → Simpan.</li>
                            <li><strong>LibreOffice Calc</strong>: Buka file .csv → pilih <em>File</em> → <em>Save As</em> → pilih tipe <em>Excel 2007-365 (.xlsx)</em> atau <em>ODF Spreadsheet (.ods)</em> → Simpan.</li>
                            <li><strong>Google Sheets</strong>: Upload file .csv ke Google Drive → buka dengan Google Sheets → pilih <em>File</em> → <em>Download</em> → pilih <em>Microsoft Excel (.xlsx)</em> atau <em>OpenDocument format (.ods)</em>.</li>
                            <li><strong>Online Converter</strong>: Gunakan layanan online seperti convertio.co atau cloudconvert.com untuk konversi cepat.</li>
                        </ul>
                    </div>

                    <form action="{{ route('v1.teacher.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Pilih File Excel/CSV/ODS (.xlsx, .csv, .ods)</label>
                            <input type="file" name="excel_file" class="form-control" accept=".xlsx,.csv,.ods" required>
                            @error('excel_file')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-upload"></i> Import Akun Guru
                        </button>
                        <a href="{{ route('v1.teacher.download-template') }}" class="btn btn-secondary">
                            <i class="fas fa-download"></i> Download Template CSV
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Student Import Section -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Import Siswa dari Excel/CSV/ODS</h6>
                </div>
                <div class="card-body">
                    @if(session('import_success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('import_success') }}
                        </div>
                    @endif
                    @if(session('import_error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> {{ session('import_error') }}
                        </div>
                    @endif
                    @if(session('import_errors') && is_array(session('import_errors')))
                        <div class="alert alert-warning">
                            <strong>Terdapat error pada beberapa baris:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <h6 class="font-weight-bold"><i class="fas fa-info-circle"></i> Format File yang Diperlukan:</h6>
                        <p class="mb-2">File Excel/CSV harus memiliki kolom-kolom berikut (urutan bebas):</p>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Kolom Utama:</strong>
                                <ul>
                                    <li><code>name</code> - Nama lengkap siswa</li>
                                </ul>
                                <strong>Data Kontak:</strong>
                                <ul>
                                    <li><code>phone</code> - Nomor telepon</li>
                                    <li><code>email</code> - Email siswa</li>
                                    <li><code>address</code> - Alamat lengkap</li>
                                    <li><code>emergency_contact</code> - Kontak darurat</li>
                                    <li><code>parent_name</code> - Nama orang tua</li>
                                </ul>
                                <strong>Data Akademik:</strong>
                                <ul>
                                    <li><code>grade</code> - Tingkat kelas (10, 11, 12)</li>
                                    <li><code>class</code> - Kelas (TKJ, RPL, MM)</li>
                                    <li><code>student_id</code> - ID/NIS siswa</li>
                                    <li><code>major</code> - Jurusan</li>
                                    <li><code>entry_year</code> - Tahun masuk</li>
                                    <li><code>period</code> - Periode/semester</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <strong>Data Biodata:</strong>
                                <ul>
                                    <li><code>nisn</code> - NISN</li>
                                    <li><code>nik</code> - NIK</li>
                                    <li><code>birth_place</code> - Tempat lahir</li>
                                    <li><code>birth_date</code> - Tanggal lahir (YYYY-MM-DD)</li>
                                    <li><code>religion</code> - Agama</li>
                                    <li><code>blood_type</code> - Golongan darah</li>
                                    <li><code>height</code> - Tinggi badan (cm)</li>
                                    <li><code>weight</code> - Berat badan (kg)</li>
                                    <li><code>hobbies</code> - Hobi (pisahkan dengan titik koma ;)</li>
                                    <li><code>achievements</code> - Prestasi (pisahkan dengan titik koma ;)</li>
                                </ul>
                                <strong>Data Lainnya:</strong>
                                <ul>
                                    <li><code>age</code> - Umur</li>
                                    <li><code>gender</code> - Jenis kelamin (male/female)</li>
                                </ul>
                            </div>
                        </div>
                        <p class="mb-0 mt-2">
                            <small class="text-muted">
                                * Untuk kolom array (hobbies, achievements), pisahkan dengan titik koma (;). Contoh: "Reading;Sports;Music"<br>
                                * Sistem mendukung format: .xlsx, .csv, .ods<br>
                                * Format .xls (Excel 97-2003) tidak didukung. Silakan konversi ke .xlsx atau .ods, atau gunakan .csv.
                            </small>
                        </p>
                    </div>

                    <div class="alert alert-secondary border">
                        <h6 class="font-weight-bold mb-2"><i class="fas fa-lightbulb"></i> Tips Konversi dari .xls ke .xlsx / .ods</h6>
                        <ul class="mb-0">
                            <li><strong>Microsoft Excel</strong>: Buka file .xls → pilih <em>File</em> → <em>Save As</em> → pilih tipe <em>Excel Workbook (*.xlsx)</em> → Simpan.</li>
                            <li><strong>LibreOffice Calc</strong>: Buka file .xls → pilih <em>File</em> → <em>Save As</em> → pilih tipe <em>ODF Spreadsheet (.ods)</em> atau <em>Excel 2007-365 (.xlsx)</em> → Simpan.</li>
                            <li><strong>Google Sheets</strong>: Upload file .xls ke Google Drive → buka dengan Google Sheets → pilih <em>File</em> → <em>Download</em> → pilih <em>Microsoft Excel (.xlsx)</em>.</li>
                        </ul>
                    </div>

                    <div class="alert alert-secondary border">
                        <h6 class="font-weight-bold mb-2"><i class="fas fa-lightbulb"></i> Tips Konversi dari CSV ke XLSX (jika diperlukan)</h6>
                        <ul class="mb-0">
                            <li><strong>Microsoft Excel</strong>: Buka file .csv → pilih <em>File</em> → <em>Save As</em> → pilih tipe <em>Excel Workbook (*.xlsx)</em> → Simpan.</li>
                            <li><strong>LibreOffice Calc</strong>: Buka file .csv → pilih <em>File</em> → <em>Save As</em> → pilih tipe <em>Excel 2007-365 (.xlsx)</em> atau <em>ODF Spreadsheet (.ods)</em> → Simpan.</li>
                            <li><strong>Google Sheets</strong>: Upload file .csv ke Google Drive → buka dengan Google Sheets → pilih <em>File</em> → <em>Download</em> → pilih <em>Microsoft Excel (.xlsx)</em> atau <em>OpenDocument format (.ods)</em>.</li>
                            <li><strong>Online Converter</strong>: Gunakan layanan online seperti convertio.co atau cloudconvert.com untuk konversi cepat.</li>
                        </ul>
                    </div>

                    <form action="{{ route('v1.student.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Pilih File Excel/CSV/ODS (.xlsx, .csv, .ods)</label>
                            <input type="file" name="excel_file" class="form-control" accept=".xlsx,.csv,.ods" required>
                            @error('excel_file')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-upload"></i> Import Siswa
                        </button>
                        <a href="{{ route('v1.student.download-template') }}" class="btn btn-secondary">
                            <i class="fas fa-download"></i> Download Template CSV
                        </a>
                    </form>
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
