@extends('layouts.master')
@section('page_title', 'Absen Baru Guru')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row">
                <h6 class="m-0 font-weight-bold text-primary" >Absensi Guru Tanggal <span class="m-0 " id="judulTglAbsenGuru"></span> </h6>
            </div>
        </div>
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-lg-6">
                        <label for="inputNamaGuru" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="inputNamaGuru">

                        <label for="inputMapel" class="form-label" style="margin-top:20px;">Mata Pelajaran</label>
                        <input type="text" class="form-control" id="inputMapel">

                        <label for="inputNamaGuru" class="form-label" style="margin-top:20px;">Kelas</label>
                        <input type="text" class="form-control" id="inputKelas">
                    </div>
                    <div class="col-lg-6">
                        <label for="inputTanggalAbsen" class="form-label">Tanggal</label>
                        <input type="text" class="form-control" id="inputTanggalAbsen">

                        <label for="inputJamPel" class="form-label" style="margin-top:20px;">Jam Pelajaran</label>
                        <input type="text" class="form-control" id="inputJamPel">

                        <label for="inputStatusAbsen" class="form-label" style="margin-top:20px;">Status</label>
                        <input type="text" class="form-control" id="inputStatusAbsen">
                    </div>
                </div>
                <div class="row" style="margin-top:20px;">
                    <button type="submit" class="btn btn-primary mb-3 form-control">Kirim</button>
                    <button type="submit" class="btn btn-danger mb-3 form-control">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
  // Get current date and time
  n =  new Date();
  y = n.getFullYear();
  m = n.getMonth() + 1;
  d = n.getDate();

  // Insert date and time into HTML
//   document.getElementById("judulTglAbsenGuru").innerHTML = datetime;
  document.getElementById("judulTglAbsenGuru").innerHTML = d + "/" + m + "/" + y;
</script>
@endsection