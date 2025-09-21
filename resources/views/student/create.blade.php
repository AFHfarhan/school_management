@extends('themes.master')
@section('page_title', 'Pendaftaran Siswa Baru')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Pendaftaran Siswa Baru</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Peserta Didik</h6>
        </div>
        <div class="card-body">
            <form id="studentForm" action="{{ route('v1.student.add') }}" method="POST" >
                @csrf
                <!-- row header -->
                <div class="row">
                    <div class="col-lg-6">
                        <label for="inputTanggal" class="form-label">Tanggal</label>
                        <div class="input-group" id="inputTanggal">
                            <input type="text" class="form-control" placeholder="Tanggal" name="formgruptanggal" id="formgruptanggal" value="{{ old('formgruptanggal') }}" aria-label="Tanggal">
                            <span class="input-group-text form-control" style="display:inline-block;">/</span>
                            <input type="text" class="form-control" placeholder="Bulan" name="formgrupbulan" id="formgrupbulan"  value="{{ old('formgrupbulan') }}" aria-label="Bulan">
                            <span class="input-group-text form-control" style="display:inline-block;">/</span>
                            <input type="text" class="form-control" placeholder="Tahun" name="formgruptahun" id="formgruptahun"  value="{{ old('formgruptahun') }}" aria-label="Tahun">
                        </div>
                        <div class="row" style="margin-top:20px;">
                            <div class="col-lg-6">
                                <label for="inputTingkat" class="form-label">Tingkat</label>
                                <input type="text" class="form-control" name="data[grade]" value="{{ old('data[grade]') }}" id="inputTingkat">
                            </div>
                            <div class="col-lg-6">
                                <label for="inputProgram" class="form-label">Program</label>
                                <input type="text" class="form-control" name="data[program]" value="{{ old('data[program]') }}" id="inputProgram">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                            <label for="inputReg" class="form-label">REG</label>
                        <div class="input-group mb-3" id="inputReg">
                            <input type="text" class="form-control" placeholder="Reg" name="formgrupReg1" value="{{ old('formgrupReg1') }}" id="formgrupReg1" aria-label="Reg1">
                            <span class="input-group-text form-control" style="display:inline-block;">/</span>
                            <input type="text" class="form-control" placeholder="Reg" name="formgrupReg2" value="{{ old('formgrupReg2') }}" id="formgrupReg2" aria-label="Reg2">
                            <span class="input-group-text form-control" style="display:inline-block;">/</span>
                            <input type="text" class="form-control" placeholder="Reg" name="formgrupReg3" value="{{ old('formgrupReg3') }}" id="formgrupReg3" aria-label="Reg3">
                        </div>
                    </div>
                </div>
                <hr></hr>
                <h7 class="m-0 font-weight-bold text-primary">Identitas Peserta Didik (WAJIB DI ISI)</h7>
                <hr></hr>     
                <!-- row biodata -->
                <div class="row">
                    <div class="col-lg-6" >
                        <label for="inputNama" class="form-label">Nama</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" id="inputNama" >

                        <label for="inputJenisKelamin" class="form-label" style="margin-top:20px;">Jenis Kelamin</label>
                        <select class="form-select form-select-lg form-control" name="data[personal][gender]" value="{{ old('data[personal][gender]') }}" id="inputJenisKelamin"  aria-label="Pilih Jenis Kelamin">
                            <option selected disabled>Pilih salah satu</option>
                            <option value="laki">Laki - laki</option>
                            <option value="perempuan">Perempuan</option>
                        </select>

                        <label for="inputTTL" class="form-label" style="margin-top:20px;">Tempat, Tanggal Lahir</label>
                        <div class="input-group" id="inputTTL">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="data[personal][birthplace]" value="{{ old('data[personal][birthplace]') }}" id="formgrupTTLTempat" >
                                </div>
                                <div class="input-group col-md-8">
                                    <input type="text" class="form-control" placeholder="tgl" name="formgrupTTLTanggal" value="{{ old('formgrupTTLTanggal') }}" id="formgrupTTLTanggal" aria-label="Tanggal">
                                    <span class="input-group-text form-control" style="display:inline-block;">/</span>
                                    <input type="text" class="form-control" placeholder="bln" name="formgrupTTLBulan" value="{{ old('formgrupTTLBulan') }}" id="formgrupTTLBulan" aria-label="Bulan">
                                    <span class="input-group-text form-control" style="display:inline-block;">/</span>
                                    <input type="text" class="form-control" placeholder="thn" name="formgrupTTLTahun" value="{{ old('formgrupTTLTahun') }}" id="formgrupTTLTahun" aria-label="Tahun">    
                                </div>
                            </div>
                        </div>

                        <label for="inputAgama" class="form-label" style="margin-top:20px;">Agama</label>
                        <select class="form-select form-select-lg form-control" name="data[personal][religion]" value="{{ old('data[personal][religion]') }}" id="inputAgama"  aria-label="Pilih Agama">
                            <option selected disabled>Pilih salah satu</option>
                            <option value="islam">Islam</option>
                            <option value="kristen">Kristen</option>
                            <option value="katholik">Katholik</option>
                            <option value="hindu">Hindu</option>
                            <option value="budha">Budha</option>
                            <option value="konguchu">Konguchu</option>
                        </select>

                        <label for="inputKks" class="form-label" style="margin-top:20px;">No.KKS</label><span style="display:inline-block; margin-left:50px;font-weight: 200;font-size: small;font-style: italic;">*KKS : Kartu Keluarga Sejahtera</span>
                        <input type="text" class="form-control" name="data[personal][kks_no]" value="{{ old('data[personal][kks_no]') }}" id="inputKks" >

                        <label for="radiobuttonKPS" class="form-label" style="margin-top:20px;">Apakah Penerima KPS </label><span style="display:inline-block; margin-left:50px;font-weight: 200;font-size: small;font-style: italic;">*KPS : Kartu Perlindungan Sosial</span>
                        <div id="radiobuttonKPS" class="row form-check">
                            <div class="form-check col-md-6">
                                <input class="form-check-input " type="radio" name="data[personal][kps]" value="ya" id="rbKpsYa">
                                <label class="form-check-label " for="rbKpsYa">
                                    Ya
                                </label>
                            </div>
                            <div class="form-check  col-md-6">
                                <input class="form-check-input " type="radio" name="data[personal][kps]" value="tidak" id="rbKpsTidak" checked>
                                <label class="form-check-label " for="rbKpsTidak">
                                    Tidak
                                </label>
                            </div>
                        </div>

                        <label for="inputKps" class="form-label" style="margin-top:20px;">No.KPS</label>
                        <input type="text" class="form-control" name="data[personal][kps_no]" value="{{ old('data[personal][kps_no]') }}" id="inputKps" >

                        <label for="radiobuttonKIP" class="form-label" style="margin-top:20px;">Apakah Penerima KIP</label><span style="display:inline-block; margin-left:50px;font-weight: 200;font-size: small;font-style: italic;">*KIP : Kartu Indonesia Pintar</span>
                        <div id="radiobuttonKIP" class="row form-check">
                            <div class="form-check col-md-6">
                                <input class="form-check-input " type="radio" name="data[personal][kip]" value="ya" id="rbKipYa">
                                <label class="form-check-label " for="rbKipYa">
                                    Ya
                                </label>
                            </div>
                            <div class="form-check  col-md-6">
                                <input class="form-check-input " type="radio" name="data[personal][kip]" value="tidak" id="rbKipTidak" checked>
                                <label class="form-check-label " for="rbKipTidak">
                                    Tidak
                                </label>
                            </div>
                        </div>

                        <label for="inputNoKip" class="form-label" style="margin-top:20px;">No.KIP</label>
                        <input type="text" class="form-control" name="data[personal][kip_no]" value="{{ old('data[personal][kip_no]') }}" id="inputNoKip" >

                        <label for="inputNamaKip" class="form-label" style="margin-top:20px;">Nama KIP</label>
                        <input type="text" class="form-control" name="data[personal][kip_name]" value="{{ old('data[personal][kip_name]') }}" id="inputNamaKip" >

                    </div>
                    <div class="col-lg-6">
                        <label for="inputSekolahAsal" class="form-label">Nama Sekolah Asal</label>
                        <input type="text" class="form-control" name="data[personal][school_prev]" value="{{ old('data[personal][school_prev]') }}" id="inputSekolahAsal" >

                        <label for="inputTempatTinggal" class="form-label" style="margin-top:20px;">Alamat Tempat Tinggal</label>
                        <textarea class="form-control" placeholder="" name="data[personal][address]" value="{{ old('data[personal][address]') }}" id="inputTempatTinggal"></textarea>

                        <label for="inputNoTelp" class="form-label" style="margin-top:20px;">No Telp Rumah</label>
                        <input type="text" class="form-control" name="data[personal][homephone]" value="{{ old('data[personal][homephone]') }}" id="inputNoTelp" >

                        <label for="inputNoHp" class="form-label" style="margin-top:20px;">No. HP</label>
                        <input type="text" class="form-control" name="data[personal][phone]" value="{{ old('data[personal][phone]') }}" id="inputNoHp" >

                        <label for="inputEmail" class="form-label" style="margin-top:20px;">Email Pribadi</label>
                        <input type="email" class="form-control" name="data[personal][email]" value="{{ old('data[personal][email]') }}" id="inputEmail" >
                    </div>
                </div>
                <hr></hr>
                <h7 class="m-0 font-weight-bold text-primary">Identitas Ayah dan Ibu Kandung (WAJIB DI ISI)</h7>
                <hr></hr>     
                <!-- row biodata orang tua-->
                <div class="row">
                    <div class="col-lg-6">
                        <label for="inputNamaAyah" class="form-label">Nama Ayah</label>
                        <input type="text" class="form-control" name="data[parent][dad][name]" value="{{ old('data[parent][dad][name]') }}" id="inputNamaAyah">

                        <label for="inputTLAyah" class="form-label" style="margin-top:20px;">Tanggal Lahir</label>
                        <input type="text" class="form-control" name="data[parent][dad][birthdate]" value="{{ old('data[parent][dad][birthdate]') }}" id="inputTLAyah">

                        <label for="inputPendidikanAyah" class="form-label" style="margin-top:20px;">Pendidikan</label>
                        <input type="text" class="form-control" name="data[parent][dad][education]" value="{{ old('data[parent][dad][education]') }}" id="inputPendidikanAyah">
                    </div>
                    <div class="col-lg-6">
                        <label for="inputPekerjaanAyah" class="form-label">Pekerjaan</label>
                        <input type="text" class="form-control" name="data[parent][dad][job]" value="{{ old('data[parent][dad][job]') }}" id="inputPekerjaanAyah">

                        <label for="inputPBAyah" class="form-label" style="margin-top:20px;">Penghasilan Bulanan</label>
                        <input type="text" class="form-control" name="data[parent][dad][salary]" value="{{ old('data[parent][dad][salary]') }}" id="inputPBAyah">

                    </div>
                </div>
                <br><br>
                <div class="row">
                    <div class="col-lg-6">
                        <label for="inputNamaIbu" class="form-label">Nama Ibu</label>
                        <input type="text" class="form-control" name="data[parent][mom][name]" value="{{ old('data[parent][mom][name]') }}" id="inputNamaIbu">

                        <label for="inputTLIbu" class="form-label" style="margin-top:20px;">Tanggal Lahir</label>
                        <input type="text" class="form-control" name="data[parent][mom][birthdate]" value="{{ old('data[parent][mom][birthdate]') }}" id="inputTLIbu">

                        <label for="inputPendidikanIbu" class="form-label" style="margin-top:20px;">Pendidikan</label>
                        <input type="text" class="form-control" name="data[parent][mom][education]" value="{{ old('data[parent][mom][nameducatione]') }}" id="inputPendidikanIbu">
                    </div>
                    <div class="col-lg-6">
                        <label for="inputPekerjaanIbu" class="form-label">Pekerjaan</label>
                        <input type="text" class="form-control" name="data[parent][mom][job]" value="{{ old('data[parent][mom][job]') }}" id="inputPekerjaanIbu">

                        <label for="inputPBIbu" class="form-label" style="margin-top:20px;">Penghasilan Bulanan</label>
                        <input type="text" class="form-control" name="data[parent][mom][salary]" value="{{ old('data[parent][mom][salary]') }}" id="inputPBIbu">

                    </div>
                </div>
                <hr></hr>
                <h7 class="m-0 font-weight-bold text-primary">Identitas Wali</h7>
                <hr></hr> 
                <div class="row">
                    <div class="col-lg-6">
                        <label for="inputNamaWali" class="form-label">Nama Wali</label>
                        <input type="text" class="form-control" name="data[parent][sub][name]" value="{{ old('data[parent][sub][name]') }}" id="inputNamaWali">

                        <label for="inputTLWali" class="form-label" style="margin-top:20px;">Tanggal Lahir</label>
                        <input type="text" class="form-control" name="data[parent][sub][birthdate]" value="{{ old('data[parent][sub][birthdate]') }}" id="inputTLWali">

                        <label for="inputPendidikanWali" class="form-label" style="margin-top:20px;">Pendidikan</label>
                        <input type="text" class="form-control" name="data[parent][sub][education]" value="{{ old('data[parent][sub][education]') }}" id="inputPendidikanWali">
                    </div>
                    <div class="col-lg-6">
                        <label for="inputPekerjaanWali" class="form-label">Pekerjaan</label>
                        <input type="text" class="form-control" name="data[parent][sub][job]" id="inputPekerjaanWali">

                        <label for="inputPBWali" class="form-label" style="margin-top:20px;">Penghasilan Bulanan</label>
                        <input type="text" class="form-control" name="data[parent][sub][salary]" value="{{ old('data[parent][sub][salary]') }}" id="inputPBWali">

                    </div>
                </div>
                <hr></hr>
                <h7 class="m-0 font-weight-bold text-primary">Data Periodik (WAJIB DIISI)</h7>
                <hr></hr>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Jenis Prestasi</th>
                                    <th>Tingkat</th>
                                    <th>Nama Prestasi</th>
                                    <th>Tahun</th>
                                    <th>Penyelenggaraan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" class="form-control" name="data[achievement][type]" id="jenisPrestasi1"></td>
                                    <td><input type="text" class="form-control" name="data[achievement][grade]" id="tingkatPrestasi1"></td>
                                    <td><input type="text" class="form-control" name="data[achievement][name]" id="namaPrestasi1"></td>
                                    <td><input type="text" class="form-control" name="data[achievement][year]" id="tahunPrestasi1"></td>
                                    <td><textarea class="form-control" id="penyelenggaraPrestasi1" name="data[achievement][credit]" value="{{ old('data[achievement][credit]') }}" rows="2" cols="5"></textarea></td>
                                </tr>
                                <tr>
                                <td><input type="text" class="form-control" name="jenisPrestasi2" id="jenisPrestasi2"></td>
                                    <td><input type="text" class="form-control" name="tingkatPrestasi2" id="tingkatPrestasi2"></td>
                                    <td><input type="text" class="form-control" name="namaPrestasi2" id="namaPrestasi2"></td>
                                    <td><input type="text" class="form-control" name="tahunPrestasi2" id="tahunPrestasi2"></td>
                                    <td><textarea class="form-control" name="penyelenggaraPrestasi2" id="penyelenggaraPrestasi2" rows="2" cols="5"></textarea></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="jenisPrestasi3" id="jenisPrestasi3"></td>
                                    <td><input type="text" class="form-control" name="tingkatPrestasi3" id="tingkatPrestasi3"></td>
                                    <td><input type="text" class="form-control" name="namaPrestasi3" id="namaPrestasi3"></td>
                                    <td><input type="text" class="form-control" name="tahunPrestasi3" id="tahunPrestasi3"></td>
                                    <td><textarea class="form-control" name="penyelenggaraPrestasi3" id="penyelenggaraPrestasi3" rows="2" cols="5"></textarea></td>
                                </tr>
                                <tr>
                                    <td><input type="text" class="form-control" name="jenisPrestasi4" id="jenisPrestasi4"></td>
                                    <td><input type="text" class="form-control" name="tingkatPrestasi4" id="tingkatPrestasi4"></td>
                                    <td><input type="text" class="form-control" name="namaPrestasi4" id="namaPrestasi4"></td>
                                    <td><input type="text" class="form-control" name="tahunPrestasi4" id="tahunPrestasi4"></td>
                                    <td><textarea class="form-control" name="penyelenggaraPrestasi4" id="penyelenggaraPrestasi4" rows="2" cols="5"></textarea></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr></hr>
                <h7 class="m-0 font-weight-bold text-primary">Lain - Lain</h7>
                <hr></hr> 
                <div class="row">
                    <div class="col-lg-6">
                        <label for="checkboxPersyaratan" class="form-label" style="margin-top:20px;">Persyaratan Pendaftaran : </label>
                        <div id="checkboxPersyaratan" class="row form-check">
                            <div class="form-check col-md-6">
                                <input class="form-check-input " type="checkbox"  name="data[other][requirements][]" value="formulir" id="cbdft1">
                                <label class="form-check-label " for="cbdft1">
                                    1. Mengisi Formulir
                                </label>
                            </div>
                            <div class="form-check  col-md-6">
                                <input class="form-check-input " type="checkbox"  name="data[other][requirements][]" value="fckk" id="cbdft2">
                                <label class="form-check-label " for="cbdft2">
                                    2. Fotokopi KK 
                                </label>
                            </div>
                            <div class="form-check  col-md-6">
                                <input class="form-check-input " type="checkbox"  name="data[other][requirements][]" value="fcktportu" id="cbdft3">
                                <label class="form-check-label " for="cbdft3">
                                    3. Fotokopi KTP Orang Tua
                                </label>
                            </div>
                            <div class="form-check  col-md-8">
                                <input class="form-check-input " type="checkbox"  name="data[other][requirements][]" value="fotowarna" id="cbdft4">
                                <label class="form-check-label " for="cbdft4">
                                    4. Foto Warna 3x4, 4 Lembar
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="checkboxReferensi" class="form-label" style="margin-top:20px;">Referensi Dari : </label>
                        <div id="checkboxReferensi" class="row form-check">
                            <div class="form-check col-md-6">
                                <input class="form-check-input " type="checkbox" name="data[other][reference][]" value="web" id="cbdrf1">
                                <label class="form-check-label " for="cbdrf1">
                                    Website
                                </label>
                            </div>
                            <div class="form-check  col-md-6">
                                <input class="form-check-input " type="checkbox" name="data[other][reference][]" value="facebook" id="cbdrf2">
                                <label class="form-check-label " for="cbdrf2">
                                    Facebook
                                </label>
                            </div>
                            <div class="form-check  col-md-6">
                                <input class="form-check-input " type="checkbox" name="data[other][reference][]" value="banner" id="cbdrf3">
                                <label class="form-check-label " for="cbdrf3">
                                    Spanduk
                                </label>
                            </div>
                            <div class="form-check  col-md-6">
                                <input class="form-check-input " type="checkbox" name="data[other][reference][]" value="brosur" id="cbdrf4">
                                <label class="form-check-label " for="cbdrf4">
                                    Brosur
                                </label>
                            </div>
                            <div class="form-check  col-md-8">
                                <input class="form-check-input " type="checkbox" name="data[other][reference][]" value="guru" id="cbdrf5">
                                <label class="form-check-label " for="cbdrf5">
                                    Guru
                                </label> <input type="text" class="form-control" id="inputGuruRef" name="inputGuruRef" value="">
                            </div>
                            <div class="form-check  col-md-8">
                                <input class="form-check-input " type="checkbox" name="data[other][reference][]" value="teman" id="cbdrf6">
                                <label class="form-check-label " for="cbdrf6">
                                    Teman
                                </label><input type="text" class="form-control" id="inputTemanRef" name="inputTemanRef" value="">
                            </div>
                            <div class="form-check  col-md-6">
                                <input class="form-check-input " type="checkbox" name="data[other][reference][]" value="lainnya" id="cbdrf7">
                                <label class="form-check-label " for="cbdrf7">
                                    Lainnya
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr></hr>
                <div class="row">
                    <button type="submit" class="btn btn-primary mb-3 form-control">Kirim</button>
                    <button type="button" id="clearForm" class="btn btn-danger mb-3 form-control">Clear</button>
                </div>
                <hr></hr> 
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('clearForm').addEventListener('click', function () {
        // Reset the form to its default state
        document.getElementById('studentForm').reset();
    });
</script>
@endsection