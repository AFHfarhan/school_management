@extends('themes.master')
@section('page_title', 'Detail Pendaftaran Siswa')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Detail Pendaftaran Siswa</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Lengkap Peserta Didik</h6>
            <div>
                <a href="{{ route('v1.students.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                <a href="{{ route('v1.students.edit', $student) }}" class="btn btn-primary btn-sm">Ubah</a>
            </div>
        </div>
        <div class="card-body">
            @php $data = $student->data ?? []; @endphp

            @php
                $regdate = $data['form_reg'] ?? '';
                $regdateParts = explode('/', $regdate);
                $regDay = $regdateParts[0] ?? '';
                $regMonth = $regdateParts[1] ?? '';
                $regYear = $regdateParts[2] ?? '';
            @endphp
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label"><strong>Nama:</strong></label>
                    <input type="text" class="form-control" value="{{ $student->name ?? '' }}" readonly disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label"><strong>Tanggal Form:</strong></label>
                    <input type="text" id="registerdate" class="form-control" value="{{ $data['form_date'] ?? '' }}" readonly disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label"><strong>Form Reg:</strong></label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ $regDay }}" readonly disabled>
                        <span class="input-group-text">/</span>
                        <input type="text" class="form-control" value="{{ $regMonth }}" readonly disabled>
                        <span class="input-group-text">/</span>
                        <input type="text" class="form-control" value="{{ $regYear }}" readonly disabled>
                    </div>
                </div>
            </div>

            <h6 class="text-primary">Identitas Peserta Didik</h6>
            <div class="row mb-3">
                <div class="col-md-3"><label class="form-label"><strong>Tingkat</strong></label><input type="text" class="form-control" value="{{ $data['grade'] ?? '' }}" readonly disabled></div>
                <div class="col-md-3"><label class="form-label"><strong>Program</strong></label><input type="text" class="form-control" value="{{ $data['program'] ?? '' }}" readonly disabled></div>
                <div class="col-md-3"><label class="form-label"><strong>Jenis Kelamin</strong></label><input type="text" class="form-control" value="{{ $data['personal']['gender'] ?? '' }}" readonly disabled></div>
                <div class="col-md-3"><label class="form-label"><strong>Agama</strong></label><input type="text" class="form-control" value="{{ $data['personal']['religion'] ?? '' }}" readonly disabled></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label"><strong>No. KKS</strong></label>
                    <input type="text" class="form-control" value="{{ $data['personal']['kks_no'] ?? '' }}" readonly disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label"><strong>Apakah Penerima KPS</strong></label>
                    <div class="d-flex gap-2 align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="show_kps_{{ $student->id }}" value="ya" {{ ($data['personal']['kps'] ?? '') == 'ya' ? 'checked' : '' }} disabled>
                            <label class="form-check-label">Ya</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="show_kps_{{ $student->id }}" value="tidak" {{ ($data['personal']['kps'] ?? '') == 'tidak' ? 'checked' : '' }} disabled>
                            <label class="form-check-label">Tidak</label>
                        </div>
                    </div>
                </div>
                @if(($data['personal']['kps'] ?? '') == 'ya')
                    <div class="col-md-4">
                        <label class="form-label"><strong>No. KPS</strong></label>
                        <input type="text" class="form-control" value="{{ $data['personal']['kps_no'] ?? '' }}" readonly disabled>
                    </div>
                @endif
            </div>

            <div class="row mb-3">
                <div class="col-md-6"><label class="form-label"><strong>Tempat Lahir</strong></label><input type="text" class="form-control" value="{{ $data['personal']['birthplace'] ?? '' }}" readonly disabled></div>
                <div class="col-md-6"><label class="form-label"><strong>Tanggal Lahir</strong></label><input type="text" id="birthdate" class="form-control" value="{{ $data['personal']['birthdate'] ?? '' }}" readonly disabled></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6"><label class="form-label"><strong>Alamat</strong></label><textarea class="form-control" readonly disabled>{{ $data['personal']['address'] ?? '' }}</textarea></div>
                <div class="col-md-3"><label class="form-label"><strong>No. HP</strong></label><input type="text" class="form-control" value="{{ $data['personal']['phone'] ?? '' }}" readonly disabled></div>
                <div class="col-md-3"><label class="form-label"><strong>Email</strong></label><input type="email" class="form-control" value="{{ $data['personal']['email'] ?? '' }}" readonly disabled></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4"><label class="form-label"><strong>Apakah Penerima KIP</strong></label>
                    <div class="d-flex gap-2 align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="show_kip_{{ $student->id }}" value="ya" {{ ($data['personal']['kip'] ?? '') == 'ya' ? 'checked' : '' }} disabled>
                            <label class="form-check-label">Ya</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="show_kip_{{ $student->id }}" value="tidak" {{ ($data['personal']['kip'] ?? '') == 'tidak' ? 'checked' : '' }} disabled>
                            <label class="form-check-label">Tidak</label>
                        </div>
                    </div>
                </div>
                @if(($data['personal']['kip'] ?? '') == 'ya')
                    <div class="col-md-4"><label class="form-label"><strong>No. KIP</strong></label><input type="text" class="form-control" value="{{ $data['personal']['kip_no'] ?? '' }}" readonly disabled></div>
                    <div class="col-md-4"><label class="form-label"><strong>Nama KIP</strong></label><input type="text" class="form-control" value="{{ $data['personal']['kip_name'] ?? '' }}" readonly disabled></div>
                @endif
            </div>

            <h6 class="text-primary">Identitas Orang Tua / Wali</h6>
            <div class="row mb-3">
                <div class="col-md-4"><label class="form-label"><strong>Ayah - Nama</strong></label><input type="text" class="form-control" value="{{ $data['parent']['dad']['name'] ?? '' }}" readonly disabled></div>
                <div class="col-md-4"><label class="form-label"><strong>Ayah - Pekerjaan</strong></label><input type="text" class="form-control" value="{{ $data['parent']['dad']['job'] ?? '' }}" readonly disabled></div>
                <div class="col-md-4"><label class="form-label"><strong>Ayah - Penghasilan</strong></label><input type="text" class="form-control" value="{{ $data['parent']['dad']['salary'] ?? '' }}" readonly disabled></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><label class="form-label"><strong>Ibu - Nama</strong></label><input type="text" class="form-control" value="{{ $data['parent']['mom']['name'] ?? '' }}" readonly disabled></div>
                <div class="col-md-4"><label class="form-label"><strong>Ibu - Pekerjaan</strong></label><input type="text" class="form-control" value="{{ $data['parent']['mom']['job'] ?? '' }}" readonly disabled></div>
                <div class="col-md-4"><label class="form-label"><strong>Ibu - Penghasilan</strong></label><input type="text" class="form-control" value="{{ $data['parent']['mom']['salary'] ?? '' }}" readonly disabled></div>
            </div>
            @if((($data['parent']['is_sub_active'] ?? '') == 'yes') || !empty($data['parent']['sub']['name'] ?? ''))
                <div class="row mb-3">
                    <div class="col-md-4"><label class="form-label"><strong>Wali - Nama</strong></label><input type="text" class="form-control" value="{{ $data['parent']['sub']['name'] ?? '' }}" readonly disabled></div>
                    <div class="col-md-4"><label class="form-label"><strong>Wali - Pekerjaan</strong></label><input type="text" class="form-control" value="{{ $data['parent']['sub']['job'] ?? '' }}" readonly disabled></div>
                    <div class="col-md-4"><label class="form-label"><strong>Wali - Penghasilan</strong></label><input type="text" class="form-control" value="{{ $data['parent']['sub']['salary'] ?? '' }}" readonly disabled></div>
                </div>
            @endif

            <h6 class="text-primary">Prestasi</h6>
            <div class="table-responsive mb-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Prestasi</th>
                            <th>Tingkat</th>
                            <th>Nama Prestasi</th>
                            <th>Tahun</th>
                            <th>Penyelenggaraan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $ach = $data['achievement'] ?? []; @endphp
                        @if(is_array($ach) && count($ach) > 0)
                            @foreach($ach as $i => $a)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><input type="text" class="form-control" value="{{ $a['type'] ?? '' }}" readonly disabled></td>
                                    <td><input type="text" class="form-control" value="{{ $a['grade'] ?? '' }}" readonly disabled></td>
                                    <td><input type="text" class="form-control" value="{{ $a['name'] ?? '' }}" readonly disabled></td>
                                    <td><input type="text" class="form-control" value="{{ $a['year'] ?? '' }}" readonly disabled></td>
                                    <td><textarea class="form-control" rows="2" readonly disabled>{{ $a['credit'] ?? '' }}</textarea></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada prestasi</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <h6 class="text-primary">Persyaratan & Referensi</h6>
            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Persyaratan yang dilampirkan</strong>
                    <div>
                        @php $req = $data['other']['requirements'] ?? []; @endphp
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="formulir" {{ in_array('formulir', $req) ? 'checked' : '' }} disabled>
                                    <label class="form-check-label">Mengisi Formulir</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="fckk" {{ in_array('fckk', $req) ? 'checked' : '' }} disabled>
                                    <label class="form-check-label">Fotokopi KK</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="fcktportu" {{ in_array('fcktportu', $req) ? 'checked' : '' }} disabled>
                                    <label class="form-check-label">Fotokopi KTP Orang Tua</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="fotowarna" {{ in_array('fotowarna', $req) ? 'checked' : '' }} disabled>
                                    <label class="form-check-label">Foto Warna 3x4</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <strong>Referensi</strong>
                    <div>
                        @php $ref = $data['other']['reference'] ?? []; @endphp
                        <div class="row mb-2">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="web" {{ in_array('web', $ref) ? 'checked' : '' }} disabled>
                                    <label class="form-check-label">Website</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="facebook" {{ in_array('facebook', $ref) ? 'checked' : '' }} disabled>
                                    <label class="form-check-label">Facebook</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="banner" {{ in_array('banner', $ref) ? 'checked' : '' }} disabled>
                                    <label class="form-check-label">Spanduk</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="brosur" {{ in_array('brosur', $ref) ? 'checked' : '' }} disabled>
                                    <label class="form-check-label">Brosur</label>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top:8px;">
                            <div class="mb-1"><strong>Nama Guru:</strong>
                                <input type="text" class="form-control" value="{{ $data['other']['reference_guru'] ?? '' }}" readonly disabled>
                            </div>
                            <div class="mb-1"><strong>Nama Teman:</strong>
                                <input type="text" class="form-control" value="{{ $data['other']['reference_teman'] ?? '' }}" readonly disabled>
                            </div>
                            <div class="mb-1"><strong>Lainnya:</strong>
                                <input type="text" class="form-control" value="{{ $data['other']['reference_lainnya'] ?? '' }}" readonly disabled>
                            </div>
                        </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('v1.students.index') }}" class="btn btn-secondary">Kembali</a>
                <a href="{{ route('v1.students.edit', $student) }}" class="btn btn-primary">Ubah Data</a>
            </div>
        </div>
    </div>
</div>
@endsection
