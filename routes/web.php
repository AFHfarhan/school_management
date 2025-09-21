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
            $data = json_decode($teacher->data, true); // Decode JSON data
            $role = $data['role'] ?? 'No role assigned'; // Access role from JSON data
            return view('teacher.dashboard', ['role' => $role]);
        })->name('dashboard');

        Route::post('add', [StudentController::class, 'store'])->name('student.add');

        Route::resource('students', StudentController::class);
        Route::resource('teacherdata', TeacherProfileController::class);

        Route::put('password', [PasswordController::class, 'update'])->name('teacher.update');

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
    });

});



Route::get('/', function () {
    return view('welcome');
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
