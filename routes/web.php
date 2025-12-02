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

        Route::post('add', [StudentController::class, 'store'])->name('student.add');
        Route::post('teachers', [\App\Http\Controllers\Teacher\SuperAdmin\TeacherManagementController::class, 'store'])->name('teacher.store');
        Route::get('teachers/manage', [\App\Http\Controllers\Teacher\SuperAdmin\TeacherManagementController::class, 'index'])->name('teacher.manage');

        // Component management (superadmin)
        Route::get('components/manage', [\App\Http\Controllers\Teacher\SuperAdmin\ComponentController::class, 'index'])
            ->name('component.manage');
        Route::post('components', [\App\Http\Controllers\Teacher\SuperAdmin\ComponentController::class, 'store'])
            ->name('component.store');
        Route::get('components/{component}/edit', [\App\Http\Controllers\Teacher\SuperAdmin\ComponentController::class, 'edit'])
            ->name('component.edit');
        Route::put('components/{component}', [\App\Http\Controllers\Teacher\SuperAdmin\ComponentController::class, 'update'])
            ->name('component.update');
        Route::delete('components/{component}', [\App\Http\Controllers\Teacher\SuperAdmin\ComponentController::class, 'destroy'])
            ->name('component.destroy');

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
