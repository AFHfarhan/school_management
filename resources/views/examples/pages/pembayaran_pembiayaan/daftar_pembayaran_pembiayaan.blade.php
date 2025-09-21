@extends('layouts.master')
@section('page_title', 'Daftar Pembayaran PPDB SMK SUKAMAKMUR')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Riwayat Pembayaran</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row">
                <div class="col-lg-6">
                    <h6 class="m-0 font-weight-bold text-primary">Data Riwayat Pembayaran PPDB SMK SUKAMAKMUR</h6>
                </div>
                <div class="col-lg-6" style="text-align: right;">
                    <a href="#" class="btn btn-primary btn-icon-split">
                        <span class="icon text-white-50">
                            <i class="fa fa-plus-circle"></i>
                        </span>
                        <span class="text">Lakukan Pembayaran</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTablePembayaran" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No Pembayaran</th>
                            <th>Nama</th>
                            <th>Untuk Pembayaran</th>
                            <th>Tanggal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Tiger Nixon</td>
                            <td>System Architect</td>
                            <td>System Architect</td>
                            <td>2011/04/25</td>
                            <td><a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-download fa-sm text-white-50"></i> Print Kwitansi</a></td>
                        </tr>
                        <tr>
                            <td>Garrett Winters</td>
                            <td>Accountant</td>
                            <td>Accountant</td>
                            <td>2011/07/25</td>
                            <td><a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-download fa-sm text-white-50"></i> Print Kwitansi</a></td>
                        </tr>
                        <tr>
                            <td>Ashton Cox</td>
                            <td>Junior Technical Author</td>
                            <td>Junior Technical Author</td>
                            <td>2009/01/12</td>
                            <td><a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-download fa-sm text-white-50"></i> Print Kwitansi</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection