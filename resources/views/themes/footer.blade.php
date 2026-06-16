<!-- Footer -->
@php
    $componentService = app(\App\Services\ComponentService::class);
    $schoolName = $componentService->getSchoolProfileValue('school_name', 'SMK SUKAMAKMUR');
@endphp
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; {{ $schoolName }} {{ now()->year }}</span>
        </div>
    </div>
</footer>
<!-- End of Footer -->