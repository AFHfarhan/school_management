<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Teacher\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Teacher\ProfileController as TeacherProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\ProfileController as StudentController;


Route::prefix('v1')->name('v1.')->group(function () {
    
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);
    });
    
    // Auth Routes
    Route::middleware('auth:teacher')->group(function () {
        Route::get('/dashboard', function () {
            $teacher = Auth::guard('teacher')->user();
            $data = is_array($teacher->data) ? $teacher->data : json_decode($teacher->data, true);
            $role = $data['role'] ?? 'No role assigned'; // Access role from JSON data
            return view('teacher.dashboard', ['role' => $role]);
        })->name('dashboard');

        // Transactions page handled by controller
        Route::get('transactions', [\App\Http\Controllers\Transaction\TransactionController::class, 'index'])
            ->name('transaction.index');

        // Show create form (optionally with ?type=... to preselect)
        Route::get('transactions/create', [\App\Http\Controllers\Transaction\TransactionController::class, 'create'])
            ->name('transaction.create');

        // Store transaction (create)
        Route::post('transactions', [\App\Http\Controllers\Transaction\TransactionController::class, 'store'])
            ->name('transaction.store');

        // Show transaction detail
        Route::get('transactions/{transaction}', [\App\Http\Controllers\Transaction\TransactionController::class, 'show'])
            ->name('transaction.show');

        // Edit transaction form
        Route::get('transactions/{transaction}/edit', [\App\Http\Controllers\Transaction\TransactionController::class, 'edit'])
            ->name('transaction.edit');

        // Update transaction
        Route::put('transactions/{transaction}', [\App\Http\Controllers\Transaction\TransactionController::class, 'update'])
            ->name('transaction.update');

        // Update payment status form
        Route::get('transactions/{transaction}/update-payment', [\App\Http\Controllers\Transaction\TransactionController::class, 'updatePaymentForm'])
            ->name('transaction.updatePayment');

        // Cancel payment
        Route::post('transactions/{transaction}/cancel-payment', [\App\Http\Controllers\Transaction\TransactionController::class, 'cancelPayment'])
            ->name('transaction.cancelPayment');

        // Teacher attendance management (must be before student attendance routes with {id} parameter)
        Route::get('attendance/teacher', [\App\Http\Controllers\Attendance\TeacherAttendanceController::class, 'index'])
            ->name('attendance.teacher.index');
        Route::post('attendance/teacher', [\App\Http\Controllers\Attendance\TeacherAttendanceController::class, 'store'])
            ->name('attendance.teacher.store');
        Route::get('attendance/teacher/history', [\App\Http\Controllers\Attendance\TeacherAttendanceController::class, 'history'])
            ->name('attendance.teacher.history');
        Route::get('attendance/teacher/detail/{id}', [\App\Http\Controllers\Attendance\TeacherAttendanceController::class, 'detail'])
            ->name('attendance.teacher.detail');

        // Student attendance routes
        Route::get('attendance', [\App\Http\Controllers\Attendance\StudentAttendanceController::class, 'index'])
            ->name('attendance.index');
        Route::get('attendance/create', [\App\Http\Controllers\Attendance\StudentAttendanceController::class, 'create'])
            ->name('attendance.create');
        Route::get('attendance/search', [\App\Http\Controllers\Attendance\StudentAttendanceController::class, 'search'])
            ->name('attendance.search');
        Route::get('attendance/filter-students', [\App\Http\Controllers\Attendance\StudentAttendanceController::class, 'filterStudents'])
            ->name('attendance.filterStudents');
        Route::get('attendance/student/{id}', [\App\Http\Controllers\Attendance\StudentAttendanceController::class, 'studentDetail'])
            ->name('attendance.studentDetail');
        Route::get('attendance/download-warning-letter', [\App\Http\Controllers\Attendance\StudentAttendanceController::class, 'downloadWarningLetter'])
            ->name('attendance.downloadWarningLetter');
        Route::get('attendance/{id}', [\App\Http\Controllers\Attendance\StudentAttendanceController::class, 'show'])
            ->name('attendance.show');
        Route::get('attendance/{id}/edit', [\App\Http\Controllers\Attendance\StudentAttendanceController::class, 'edit'])
            ->name('attendance.edit');
        Route::put('attendance/{id}', [\App\Http\Controllers\Attendance\StudentAttendanceController::class, 'update'])
            ->name('attendance.update');
        Route::post('attendance', [\App\Http\Controllers\Attendance\StudentAttendanceController::class, 'store'])
            ->name('attendance.store');

        Route::post('add', [StudentController::class, 'store'])->name('student.add');
        Route::post('teachers', [\App\Http\Controllers\Teacher\SuperAdmin\TeacherManagementController::class, 'store'])->name('teacher.store');
        Route::get('teachers/manage', [\App\Http\Controllers\Teacher\SuperAdmin\TeacherManagementController::class, 'index'])->name('teacher.manage');

        // Component management (superadmin)
        Route::get('components/manage', [\App\Http\Controllers\Teacher\SuperAdmin\ComponentController::class, 'index'])
            ->name('component.manage');
        Route::post('components', [\App\Http\Controllers\Teacher\SuperAdmin\ComponentController::class, 'store'])
            ->name('component.store');
        Route::get('components/mandatory/{name}', [\App\Http\Controllers\Teacher\SuperAdmin\ComponentController::class, 'getMandatoryComponent'])
            ->name('component.mandatory.get');
        Route::get('components/{component}/edit', [\App\Http\Controllers\Teacher\SuperAdmin\ComponentController::class, 'edit'])
            ->name('component.edit');
        Route::put('components/{component}', [\App\Http\Controllers\Teacher\SuperAdmin\ComponentController::class, 'update'])
            ->name('component.update');
        Route::delete('components/{component}', [\App\Http\Controllers\Teacher\SuperAdmin\ComponentController::class, 'destroy'])
            ->name('component.destroy');

        // Teacher schedule management (superadmin)
        Route::get('teachers/schedule/manage', [\App\Http\Controllers\Teacher\SuperAdmin\TeacherScheduleController::class, 'manage'])
            ->name('teacher.schedule.manage');
        Route::post('teachers/schedule/upsert', [\App\Http\Controllers\Teacher\SuperAdmin\TeacherScheduleController::class, 'upsert'])
            ->name('teacher.schedule.upsert');

        // Teacher management (superadmin)
        Route::get('teachers/edit/{teacher}', [\App\Http\Controllers\Teacher\SuperAdmin\TeacherManagementController::class, 'edit'])
            ->name('teacher.edit');
        Route::put('teachers/{teacher}', [\App\Http\Controllers\Teacher\SuperAdmin\TeacherManagementController::class, 'update'])
            ->name('teacher.update');
        Route::patch('teachers/{teacher}/deactivate', [\App\Http\Controllers\Teacher\SuperAdmin\TeacherManagementController::class, 'deactivate'])
            ->name('teacher.deactivate');
        Route::patch('teachers/{teacher}/reactivate', [\App\Http\Controllers\Teacher\SuperAdmin\TeacherManagementController::class, 'reactivate'])
            ->name('teacher.reactivate');
        Route::patch('teachers/{teacher}/delete', [\App\Http\Controllers\Teacher\SuperAdmin\TeacherManagementController::class, 'delete'])
            ->name('teacher.delete');

        Route::resource('students', StudentController::class);
        Route::resource('teacherdata', TeacherProfileController::class);

        Route::put('password', [PasswordController::class, 'update'])->name('teacher.update');

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
    });

});

// Provide a global named logout route so views that call route('logout') resolve.
// This route uses the teacher guard logout controller and requires auth:teacher.
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:teacher')
    ->name('logout');

Route::get('/', function () {
    return redirect()->route('v1.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// require __DIR__.'/auth.php';
