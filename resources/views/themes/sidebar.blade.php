<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('v1.dashboard')}}">
        <!-- <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div> -->
        <div class="sidebar-brand-text mx-3">SMK SUKAMAKMUR</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('v1.dashboard')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Menu Admin
    </div>


    <!-- Nav Item - Menu Pendaftaran Siswa Baru -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-table"></i>
            <span>Pendaftaran Siswa</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('v1.students.create')}}">Pendaftaran Siswa Baru</a>
                <a class="collapse-item" href="{{ route('v1.students.index')}}">Daftar Siswa</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Menu Pembayaran dan pembiayaan -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePayment"
            aria-expanded="true" aria-controls="collapsePayment">
            <i class="fas fa-fw fa-table"></i>
            <span>Pembayaran dan Pembiayaan</span>
        </a>
        <div id="collapsePayment" class="collapse" aria-labelledby="headingPayment"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="/buat_pembayaran_baru">Pembayaran Baru</a>
                <a class="collapse-item" href="/daftar_pembayaran_pembiayaan">Daftar Pembayaran</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Menu Pembayaran dan pembiayaan -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
            aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-table"></i>
            <span>Absensi</span>
        </a>
        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="/tambah_absensi_guru">Isi Absen Mengajar</a>
                <a class="collapse-item" href="/tambah_absensi">Isi Absen Siswa</a>
                <a class="collapse-item" href="/daftar_absensi">Daftar Absen</a>
            </div>
        </div>
    </li>
    @php
        $teacher = Auth::guard('teacher')->user();
        $role = null;
        if ($teacher && !empty($teacher->data)) {
            $tdata = json_decode($teacher->data, true);
            $role = $tdata['role'] ?? null;
        }
    @endphp

    @if($role === 'admin')

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Menu Super Admin
    </div>
    
    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
            aria-expanded="true" aria-controls="collapsePages">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Pengaturan</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="login.html">Kelola Admin</a>
                <a class="collapse-item" href="register.html">Kelola Data Sekolah</a>
            </div>
        </div>
    </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->