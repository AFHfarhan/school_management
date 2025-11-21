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
                            <input type="text" class="form-control" placeholder="Pilih Tanggal" name="data[register_date]" id="registerdate" value="{{ old('data[register_date]') }}" aria-label="Tanggal">
                        </div>
                        <div class="row" style="margin-top:20px;">
                            <div class="col-lg-6">
                                <label for="inputTingkat" class="form-label">Tingkat</label>
                                <input type="text" class="form-control" name="data[grade]" inputmode="numeric" oninput="this.value = this.value.replace(/[^\d]/g,'')"  pattern="\d*"  maxlength="2" autocomplete="off" value="{{ old('data[grade]') }}" id="inputTingkat">
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
                                    <input type="text" class="form-control" placeholder="Pilih Tanggal" name="data[personal][birth_date]" id="birthdate" value="{{ old('data[personal][birth_date]') }}" aria-label="Tanggal Lahir">
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
                        <input type="text" class="form-control" name="data[personal][kks_no]" inputmode="numeric" oninput="this.value = this.value.replace(/[^\d]/g,'')"  pattern="\d*"  maxlength="15" autocomplete="off" value="{{ old('data[personal][kks_no]') }}" id="inputKks" >

                        <label for="radiobuttonKPS" class="form-label" style="margin-top:20px;">Apakah Penerima KPS </label><span style="display:inline-block; margin-left:50px;font-weight: 200;font-size: small;font-style: italic;">*KPS : Kartu Perlindungan Sosial</span>
                        <div id="radiobuttonKPS" class="row form-check">
                            <div class="form-check col-md-6" id="rbshowKps">
                                <input class="form-check-input " type="radio" name="data[personal][kps]" value="ya" id="rbKpsYa">
                                <label class="form-check-label " for="rbKpsYa">
                                    Ya
                                </label>
                            </div>
                            <div class="form-check  col-md-6" >
                                <input class="form-check-input " type="radio" name="data[personal][kps]" value="tidak" id="rbKpsTidak" checked>
                                <label class="form-check-label " for="rbKpsTidak">
                                    Tidak
                                </label>
                            </div>
                        </div>

                        <div id="kpsField">
                            <label for="inputKps" class="form-label" style="margin-top:20px;">No.KPS</label>
                            <input type="text" class="form-control" name="data[personal][kps_no]" inputmode="numeric" oninput="this.value = this.value.replace(/[^\d]/g,'')"  pattern="\d*"  maxlength="15" autocomplete="off" value="{{ old('data[personal][kps_no]') }}" id="inputKps" >
                        </div>

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

                        <div id="kipField">
                            <label for="inputNoKip" class="form-label" style="margin-top:20px;">No.KIP</label>
                            <input type="text" class="form-control" name="data[personal][kip_no]" inputmode="numeric" oninput="this.value = this.value.replace(/[^\d]/g,'')"  pattern="\d*"  maxlength="15" autocomplete="off" value="{{ old('data[personal][kip_no]') }}" id="inputNoKip" >

                            <label for="inputNamaKip" class="form-label" style="margin-top:20px;">Nama KIP</label>
                            <input type="text" class="form-control" name="data[personal][kip_name]" value="{{ old('data[personal][kip_name]') }}" id="inputNamaKip" >
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="inputSekolahAsal" class="form-label">Nama Sekolah Asal</label>
                        <input type="text" class="form-control" name="data[personal][school_prev]" value="{{ old('data[personal][school_prev]') }}" id="inputSekolahAsal" >

                        <label for="inputTempatTinggal" class="form-label" style="margin-top:20px;">Alamat Tempat Tinggal</label>
                        <textarea class="form-control" placeholder="" name="data[personal][address]" value="{{ old('data[personal][address]') }}" id="inputTempatTinggal"></textarea>

                        <label for="inputNoTelp" class="form-label" style="margin-top:20px;">No Telp Rumah</label>
                        <input type="text" class="form-control" name="data[personal][homephone]" inputmode="numeric" oninput="this.value = this.value.replace(/[^\d]/g,'')"  pattern="\d*"  maxlength="13" autocomplete="off" value="{{ old('data[personal][homephone]') }}" id="inputNoTelp" >

                        <label for="inputNoHp" class="form-label" style="margin-top:20px;">No. HP</label>
                        <input type="text" class="form-control" name="data[personal][phone]" inputmode="numeric" oninput="this.value = this.value.replace(/[^\d]/g,'')"  pattern="\d*"  maxlength="13" autocomplete="off" value="{{ old('data[personal][phone]') }}" id="inputNoHp" >

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
                        <input type="text" class="form-control" name="data[parent][dad][birthdate]" value="{{ old('data[parent][dad][birthdate]') }}" placeholder="Pilih Tanggal" id="inputTLAyah">

                        <label for="inputPendidikanAyah" class="form-label" style="margin-top:20px;">Pendidikan</label>
                        <input type="text" class="form-control" name="data[parent][dad][education]" value="{{ old('data[parent][dad][education]') }}" id="inputPendidikanAyah">
                    </div>
                    <div class="col-lg-6">
                        <label for="inputPekerjaanAyah" class="form-label">Pekerjaan</label>
                        <input type="text" class="form-control" name="data[parent][dad][job]" value="{{ old('data[parent][dad][job]') }}" id="inputPekerjaanAyah">

                        <label for="inputPBAyah" class="form-label" style="margin-top:20px;">Penghasilan Bulanan</label>
                        <input type="text" class="form-control" name="data[parent][dad][salary]" id="inputPBAyah">

                    </div>
                </div>
                <br><br>
                <div class="row">
                    <div class="col-lg-6">
                        <label for="inputNamaIbu" class="form-label">Nama Ibu</label>
                        <input type="text" class="form-control" name="data[parent][mom][name]" value="{{ old('data[parent][mom][name]') }}" id="inputNamaIbu">

                        <label for="inputTLIbu" class="form-label" style="margin-top:20px;">Tanggal Lahir</label>
                        <input type="text" class="form-control" name="data[parent][mom][birthdate]" value="{{ old('data[parent][mom][birthdate]') }}" placeholder="Pilih Tanggal" id="inputTLIbu">

                        <label for="inputPendidikanIbu" class="form-label" style="margin-top:20px;">Pendidikan</label>
                        <input type="text" class="form-control" name="data[parent][mom][education]" value="{{ old('data[parent][mom][nameducatione]') }}" id="inputPendidikanIbu">
                    </div>
                    <div class="col-lg-6">
                        <label for="inputPekerjaanIbu" class="form-label">Pekerjaan</label>
                        <input type="text" class="form-control" name="data[parent][mom][job]" value="{{ old('data[parent][mom][job]') }}" id="inputPekerjaanIbu">

                        <label for="inputPBIbu" class="form-label" style="margin-top:20px;">Penghasilan Bulanan</label>
                        <input type="text" class="form-control" name="data[parent][mom][salary]" id="inputPBIbu">

                    </div>
                </div>
                <hr></hr>
                <div class="row">
                    <div class="col-lg-6">
                        <h7 class="m-0 font-weight-bold text-primary">Identitas Wali</h7>
                    </div>
                    <div class="col-lg-6" style="text-align:left;">
                        <input class="form-check-input" type="checkbox" name="data[parent][is_sub_active]" value="yes" id="toggleOptional" aria-controls="optionalFields" aria-expanded="true" >
                        <label class="form-check-label" for="toggleOptional"><strong>Ada</strong></label>
                    </div>
                </div>
                <hr></hr> 
                <div class="row" id="optionalFields">
                    <div class="col-lg-6">
                        <label for="inputNamaWali" class="form-label">Nama Wali</label>
                        <input type="text" class="form-control" name="data[parent][sub][name]" value="{{ old('data[parent][sub][name]') }}" id="inputNamaWali">

                        <label for="inputTLWali" class="form-label" style="margin-top:20px;">Tanggal Lahir</label>
                        <input type="text" class="form-control" name="data[parent][sub][birthdate]" value="{{ old('data[parent][sub][birthdate]') }}" placeholder="Pilih Tanggal" id="inputTLWali">

                        <label for="inputPendidikanWali" class="form-label" style="margin-top:20px;">Pendidikan</label>
                        <input type="text" class="form-control" name="data[parent][sub][education]" value="{{ old('data[parent][sub][education]') }}" id="inputPendidikanWali">
                    </div>
                    <div class="col-lg-6">
                        <label for="inputPekerjaanWali" class="form-label">Pekerjaan</label>
                        <input type="text" class="form-control" name="data[parent][sub][job]" id="inputPekerjaanWali">

                        <label for="inputPBWali" class="form-label" style="margin-top:20px;">Penghasilan Bulanan</label>
                        <input type="text" class="form-control" name="data[parent][sub][salary]" id="inputPBWali">

                    </div>
                </div>
                <hr></hr>
                <h7 class="m-0 font-weight-bold text-primary">Data Periodik (WAJIB DIISI)</h7>
                <hr></hr>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0" id="itemsTable">
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
                            <tbody id="tableBody">
                                <tr class="data-row">
                                    <td class="row-index"></td>
                                    <td><input type="text" class="form-control" name="data[achievement][0][type]" id="jenisPrestasi"></td>
                                    <td><input type="text" class="form-control" name="data[achievement][0][grade]" id="tingkatPrestasi"></td>
                                    <td><input type="text" class="form-control" name="data[achievement][0][name]" id="namaPrestasi"></td>
                                    <td><input type="text" class="form-control" name="data[achievement][0][year]" inputmode="numeric" oninput="this.value = this.value.replace(/[^\d]/g,'')"  pattern="\d*"  maxlength="4" autocomplete="off" id="tahunPrestasi"></td>
                                    <td><textarea class="form-control" id="penyelenggaraPrestasi" name="data[achievement][0][credit]" rows="2" cols="5">{{ old('data[achievement][credit]') }}</textarea>
                                    <button type="button" class="btn btn-sm btn-danger remove-row">Remove</button></td>
                                </tr>
                                <!-- Hidden template row for cloning -->
                                <template id="rowTemplate">
                                <tr class="data-row">
                                    <td class="row-index"></td>
                                    <td><input type="text" class="form-control" data-field="type"></td>
                                    <td><input type="text" class="form-control" data-field="grade"></td>
                                    <td><input type="text" class="form-control" data-field="name"></td>
                                    <td><input type="text" class="form-control" inputmode="numeric" oninput="this.value = this.value.replace(/[^\d]/g,'')"  pattern="\d*"  maxlength="13" autocomplete="off" data-field="year"></td>
                                    <td><textarea class="form-control" data-field="credit" rows="2" cols="5"></textarea>
                                    <button type="button" class="btn btn-sm btn-danger remove-row">Remove</button></td>
                                </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <button type="button" id="addRow" class="btn btn-sm btn-success">Add row</button>
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
                                </label> <input type="text" class="form-control" id="inputGuruRef" name="data[other][reference_guru]" value="">
                            </div>
                            <div class="form-check  col-md-8">
                                <input class="form-check-input " type="checkbox" name="data[other][reference][]" value="teman" id="cbdrf6">
                                <label class="form-check-label " for="cbdrf6">
                                    Teman
                                </label><input type="text" class="form-control" id="inputTemanRef" name="data[other][reference_teman]" value="">
                            </div>
                            <div class="form-check  col-md-8">
                                <input class="form-check-input " type="checkbox" name="data[other][reference][]" value="lainnya" id="cbdrf7">
                                <label class="form-check-label " for="cbdrf7">
                                    Lainnya
                                </label>
                                <input type="text" class="form-control" id="inputLainnyaRef" name="data[other][reference_lainnya]" value="">
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
<script>
  document.addEventListener('DOMContentLoaded', function () {
    flatpickr("#registerdate", { dateFormat: "d/m/Y", allowInput: true });
    flatpickr("#birthdate", { dateFormat: "d/m/Y", allowInput: true });
    flatpickr("#inputTLAyah", { dateFormat: "d/m/Y", allowInput: true });
    flatpickr("#inputTLIbu", { dateFormat: "d/m/Y", allowInput: true });
    flatpickr("#inputTLWali", { dateFormat: "d/m/Y", allowInput: true });

    // Toggle logic
    const toggle = document.getElementById('toggleOptional');
    const container = document.getElementById('optionalFields');

    // Utility: find form controls inside container
    function formControlsIn(element) {
        return Array.from(element.querySelectorAll('input, select, textarea, button'))
        .filter(el => el.type !== 'hidden');
    }

    // Hide or show and manage disabled state and validation classes
    function setVisibility(show) {
        container.style.display = show ? '' : 'none';
        container.setAttribute('aria-hidden', show ? 'false' : 'true');

        formControlsIn(container).forEach(control => {
        if (show) {
            control.disabled = false;
            // restore aria-invalid if it had validation (leave classes intact)
        } else {
            control.disabled = true;
            // remove client-side validation styling to avoid confusing appearance
            control.classList.remove('is-invalid');
        }
        });

        toggle.setAttribute('aria-expanded', show ? 'true' : 'false');
    }

    // Initialize using current toggle state
    setVisibility(toggle.checked);

    // Remember values when hiding (so they are preserved when shown again)
    const preserved = new Map();
    function preserveValues() {
        formControlsIn(container).forEach(control => {
        preserved.set(control.name || control.id, control.value);
        });
    }
    function restoreValues() {
        formControlsIn(container).forEach(control => {
        const key = control.name || control.id;
        if (preserved.has(key)) control.value = preserved.get(key);
        });
    }

    // Handle change event
    toggle.addEventListener('change', function () {
        if (!this.checked) {
        preserveValues();
        setVisibility(false);
        } else {
        restoreValues();
        setVisibility(true);
        }
    });

    // If the container should be hidden by default based on server validation errors,
    // keep it visible so users see errors. Hide only if no validation errors exist.
    (function conditionalInitialHide() {
        const hasServerErrors = container.querySelectorAll('.is-invalid').length > 0;
        if (!hasServerErrors) {
        // If the toggle is unchecked (user wants hidden), ensure container is hidden
        if (!toggle.checked) setVisibility(false);
        } else {
        // make sure toggle reflects visible state when server validation found errors
        toggle.checked = true;
        setVisibility(true);
        }
    })();
  });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radioYes = document.getElementById('rbKpsYa');
        const radioNo = document.getElementById('rbKpsTidak');
        const container = document.getElementById('kpsField');
        const radioKipYes = document.getElementById('rbKipYa');
        const radioKipNo = document.getElementById('rbKipTidak');
        const containerKip = document.getElementById('kipField');
        const radioGuru = document.getElementById('cbdrf5');
        const radioTeman = document.getElementById('cbdrf6');
        const radioLainnya = document.getElementById('cbdrf7');
        const containerGuru = document.getElementById('inputGuruRef');
        const containerTeman = document.getElementById('inputTemanRef');
        const containerLainnya = document.getElementById('inputLainnyaRef');

        // Helpers
        function controls(el) {
            return Array.from(el.querySelectorAll('input, select, textarea')).filter(i => i.type !== 'hidden');
        }

        function setGroupVisible(group, visible) {
            group.style.display = visible ? '' : 'none';
            group.setAttribute('aria-hidden', visible ? 'false' : 'true');
            controls(group).forEach(c => {
            c.disabled = !visible;
            if (!visible) c.classList.remove('is-invalid');
            });
        }

        // Preserve/restore maps
        const preserved = {
            A: new Map(),
            B: new Map()
        };

        function preserve(group, map) {
            controls(group).forEach(c => map.set(c.name || c.id, c.value));
        }
        function restore(group, map) {
            controls(group).forEach(c => {
            const key = c.name || c.id;
            if (map.has(key)) c.value = map.get(key);
            });
        }

        // Initialize based on old values or validation errors
        (function init() {
            const errsA = container.querySelectorAll('.is-invalid').length > 0;
            const errsB = containerKip.querySelectorAll('.is-invalid').length > 0;
            const errsC = containerGuru.querySelectorAll('.is-invalid').length > 0;
            const errsD = containerTeman.querySelectorAll('.is-invalid').length > 0;
            const errsE = containerLainnya.querySelectorAll('.is-invalid').length > 0;

            // If server validation has errors in a group, force it visible and set its radio to Yes
            if (errsA) { radioYes.checked = true; setGroupVisible(container, true); } 
            else setGroupVisible(container, radioYes.checked);

            if (errsB) { radioKipYes.checked = true; setGroupVisible(containerKip, true); } 
            else setGroupVisible(containerKip, radioKipYes.checked);

            if (errsC) { radioGuru.checked = true; setGroupVisible(containerGuru, true); } 
            else setGroupVisible(containerGuru, radioGuru.checked);

            if (errsD) { radioTeman.checked = true; setGroupVisible(containerTeman, true); } 
            else setGroupVisible(containerTeman, radioTeman.checked);

            if (errsE) { radioLainnya.checked = true; setGroupVisible(containerLainnya, true); } 
            else setGroupVisible(containerLainnya, radioLainnya.checked);

            // If neither radio was set (first load), you can default to hidden both; current code respects checked states
        })();

        // Radio handlers (independent)
        radioYes.addEventListener('change', function () {
            if (this.checked) {
            restore(container, preserved.A);
            setGroupVisible(container, true);
            }
        });
        radioNo.addEventListener('change', function () {
            if (this.checked) {
            preserve(container, preserved.A);
            setGroupVisible(container, false);
            }
        });

        radioKipYes.addEventListener('change', function () {
            if (this.checked) {
            restore(containerKip, preserved.B);
            setGroupVisible(containerKip, true);
            }
        });
        radioKipNo.addEventListener('change', function () {
            if (this.checked) {
            preserve(containerKip, preserved.B);
            setGroupVisible(containerKip, false);
            }
        });

        radioGuru.addEventListener('change', function () {
            if (this.checked) {
            restore(containerGuru, preserved.B);
            setGroupVisible(containerGuru, true);
            }else{
            preserve(containerGuru, preserved.B);
            setGroupVisible(containerGuru, false);
            }
        });
        radioTeman.addEventListener('change', function () {
            if (this.checked) {
            restore(containerTeman, preserved.B);
            setGroupVisible(containerTeman, true);
            }else{
            preserve(containerTeman, preserved.B);
            setGroupVisible(containerTeman, false);
            }
        });
        radioLainnya.addEventListener('change', function () {
            if (this.checked) {
            restore(containerLainnya, preserved.B);
            setGroupVisible(containerLainnya, true);
            }else{
            preserve(containerLainnya, preserved.B);
            setGroupVisible(containerLainnya, false);
            }
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const tbody = document.getElementById('tableBody');
  const tpl = document.getElementById('rowTemplate').content;
  // Re-index rows so names become data[achievement][0][type], data[achievement][1][...], ...
  function reindex() {
    const rows = Array.from(tbody.querySelectorAll('tr.data-row'));
    rows.forEach((tr, i) => {
      // visible index
      const idxCell = tr.querySelector('.row-index');
      if (idxCell) idxCell.textContent = i + 1;
      // set input names using data-field attributes or detect existing names
      Array.from(tr.querySelectorAll('input, textarea, select')).forEach(el => {
        const field = el.dataset.field || ((el.name || '').match(/\[([^\]]+)\]$/) ? (el.name.match(/\[([^\]]+)\]$/)[1]) : null);
        if (field) el.name = `data[achievement][${i}][${field}]`;
        // remove IDs duplicated from template
        if (el.id) el.removeAttribute('id');
      });
    });
  }

  // add a new empty row
  function addRow(values = {}) {
    const frag = document.importNode(tpl, true);
    const tr = frag.querySelector('tr');
    // populate if values provided
    Array.from(tr.querySelectorAll('[data-field]')).forEach(el => {
      const f = el.dataset.field;
      if (values[f] !== undefined) {
        if (el.tagName.toLowerCase() === 'textarea') el.textContent = values[f];
        else el.value = values[f];
      }
    });
    tbody.appendChild(tr);
    reindex();
  }

  // remove handler (works for dynamically added rows)
  tbody.addEventListener('click', function (e) {
    if (e.target.matches('.remove-row')) {
      const tr = e.target.closest('tr.data-row');
      if (!tr) return;
      tr.remove();
      // ensure at least one row exists if desired; otherwise reindex will create empty names array
      if (tbody.querySelectorAll('tr.data-row').length === 0) addRow();
      reindex();
    }
  });

  // If you have a separate Add button, wire it up:
  const addBtn = document.getElementById('addRow'); // adjust selector if different
  if (addBtn) addBtn.addEventListener('click', () => addRow());

  // initial reindex (for old() rows)
  reindex();
});
</script>

<script>
    document.getElementById('inputPBAyah').addEventListener('input', function (e) {
  let value = e.target.value.replace(/[^0-9]/g, '');
  e.target.value = new Intl.NumberFormat('id-ID').format(value);
});
document.getElementById('inputPBIbu').addEventListener('input', function (e) {
  let value = e.target.value.replace(/[^0-9]/g, '');
  e.target.value = new Intl.NumberFormat('id-ID').format(value);
});
document.getElementById('inputPBWali').addEventListener('input', function (e) {
  let value = e.target.value.replace(/[^0-9]/g, '');
  e.target.value = new Intl.NumberFormat('id-ID').format(value);
});
</script>
@endsection