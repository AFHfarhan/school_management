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
                                    <a href="{{ route('v1.students.edit', $student) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <form action="{{ route('v1.students.destroy', $student->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
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
@endsection

