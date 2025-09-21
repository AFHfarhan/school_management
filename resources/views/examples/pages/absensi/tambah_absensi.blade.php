@extends('layouts.master')
@section('page_title', 'Absen baru Siswa')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Absensi Siswa Tanggal <span class="m-0" id="judulTglAbsenSiswa"></span></h6>
        </div>
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-lg-6">
                        <label for="inputTingkatAbsen" class="form-label">Tingkat</label>
                        <input type="text" class="form-control" id="inputTingkatAbsen">

                        <label for="inputKelasAbsen" class="form-label" style="margin-top:20px;">Kelas</label>
                        <input type="text" class="form-control" id="inputKelasAbsen">
                    </div>
                    <div class="col=lg-6">
                        <label for="inputMapelAbsen" class="form-label">Mata Pelajaran</label>
                        <input type="text" class="form-control" id="inputMapelAbsen">

                        <label for="inputMapelAbsen" class="form-label" style="margin-top:20px;visibility: hidden;" >Cari</label>
                        <button type="submit" class="btn btn-warning mb-3 form-control" >Cari</button>
                    </div>


                </div>
                <div class="row">
                    
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
  document.getElementById("judulTglAbsenSiswa").innerHTML = d + "/" + m + "/" + y;
</script>
@endsection