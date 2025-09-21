@extends('layouts.master')
@section('page_title', 'Pembayaran PPDB SMK SUKAMAKMUR')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Pembayaran PPDB SMK SUKAMAKMUR</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Pembayaran PPDB</h6>
        </div>
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-lg-6">
                        <label for="inputNoPembayaran" class="form-label">No.</label>
                        <input type="text" class="form-control" id="inputNoPembayaran">
                    </div>
                    <div class="col-lg-6">
                        <label for="inputTglPembayaran" class="form-label">Tanggal</label>
                        <input type="text" class="form-control" id="inputTglPembayaran">
                    </div>
                </div>
                <br>
                <hr>
                <hr>
                <br>
                <div class="row">
                    <div class="col-lg-12">
                        <label for="inputTerimaDari" class="form-label">Terima Dari </label>
                        <input type="text" class="form-control" id="inputTerimaDari">
                    </div>
                </div>
                <div class="row" style="margin-top:20px;">
                    <div class="col-lg-6">
                        <label for="inputTerbilang" class="form-label">Terbilang</label>
                        <input type="text" class="form-control" id="inputTerbilang">
                    </div>
                    <div class="col-lg-6">
                        <label for="inputNominal" class="form-label">Nominal</label>
                        <input type="text" class="form-control" id="inputNominal">
                    </div>
                </div>
                <div class="row" style="margin-top:20px;">
                    <div class="col-lg-12">
                        <label for="inputUntukPembayaran" class="form-label">Untuk Pembayaran</label>
                        <textarea class="form-control" id="inputUntukPembayaran" rows="3" cols="5"></textarea>
                    </div>
                </div>
                <div class="row" style="margin-top:20px;">
                    <button type="submit" class="btn btn-primary mb-3 form-control">Kirim</button>
                    <button type="submit" class="btn btn-danger mb-3 form-control">Batal</button>
                </div>
            </form>
        </<div>
    </div>

</div>
@endsection