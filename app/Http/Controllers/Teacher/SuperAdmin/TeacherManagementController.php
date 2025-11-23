<?php

namespace App\Http\Controllers\Teacher\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\Teacher;

class TeacherManagementController extends Controller
{
    /**
     * Show form to create a teacher and list existing teachers.
     */
    public function index()
    {
        $user = Auth::guard('teacher')->user();
        // only allow admin role
        $role = null;
        if ($user && !empty($user->data)) {
            $ud = is_array($user->data) ? $user->data : json_decode($user->data, true);
            $role = $ud['role'] ?? null;
        }
        if ($role !== 'admin') {
            abort(403);
        }

        // list teachers except those with data->role == 'admin'
        // Use a JSON where clause to filter at DB level when possible
        try {
            $teachers = Teacher::where('data->role', '<>', 'admin')->get();
        } catch (\Throwable $e) {
            // Fallback: fetch all and filter in memory if DB doesn't support JSON operator
            $teachers = Teacher::all()->filter(function ($t) {
                $td = $t->data ?? [];
                return ($td['role'] ?? null) !== 'admin';
            })->values();
        }

        return view('teacher.superadmin.createteacher', compact('teachers'));
    }

    /**
     * Store a new teacher.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('teacher')->user();
        // only allow admin role
        $role = null;
        if ($user && !empty($user->data)) {
            $ud = is_array($user->data) ? $user->data : json_decode($user->data, true);
            $role = $ud['role'] ?? null;
        }
        if ($role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|in:guru,tata_usaha,ka_tata_usaha,kepala_sekolah,kesiswaan',
        ]);

        // Default password when none provided
        $password = $validated['password'] ?? 'password123';

        $data = [
            'role' => $validated['role'] ?? 'guru',
            'latest_login' => now()->toDateTimeString(),
            'isActive' => 1,
        ];

        $teacher = Teacher::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $password,
            'data' => $data,
        ]);

        return Redirect::route('v1.teacher.manage')->with('success', 'Teacher created.');
    }
}
