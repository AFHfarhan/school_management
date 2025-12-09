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

        // Get all teachers that are not deleted (isActive != 0) and not admin
        try {
            $teachers = Teacher::all()->filter(function ($t) {
                $td = is_array($t->data) ? $t->data : json_decode($t->data, true) ?? [];
                $isActive = $td['isActive'] ?? 1;
                $role = $td['role'] ?? null;
                // Show only if isActive != 0 and role != admin
                return $isActive != 0 && $role !== 'admin';
            })->values();
        } catch (\Throwable $e) {
            $teachers = [];
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

    /**
     * Show edit form for a teacher
     */
    public function edit(Teacher $teacher)
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

        // Ensure data is array
        if (!is_array($teacher->data)) {
            $teacher->data = json_decode($teacher->data, true) ?? [];
        }

        return view('teacher.superadmin.editsuperadmin', compact('teacher'));
    }

    /**
     * Update a teacher
     */
    public function update(Request $request, Teacher $teacher)
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
            'email' => 'required|email|unique:teachers,email,' . $teacher->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|in:guru,tata_usaha,ka_tata_usaha,kepala_sekolah,kesiswaan',
        ]);

        // Update basic fields
        $teacher->name = $validated['name'];
        $teacher->email = $validated['email'];
        
        // Update password if provided
        if ($validated['password']) {
            $teacher->password = $validated['password'];
        }

        // Update data (JSON) with new role
        $currentData = is_array($teacher->data) ? $teacher->data : json_decode($teacher->data, true) ?? [];
        $currentData['role'] = $validated['role'];
        $currentData['updated_at'] = now()->toDateTimeString();
        $teacher->data = $currentData;

        $teacher->save();

        return Redirect::route('v1.teacher.manage')->with('success', 'Teacher updated successfully.');
    }

    /**
     * Deactivate a teacher (set isActive to 2)
     */
    public function deactivate(Teacher $teacher)
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

        // Update data to set isActive = 2
        $currentData = is_array($teacher->data) ? $teacher->data : json_decode($teacher->data, true) ?? [];
        $currentData['isActive'] = 2;
        $currentData['deactivated_at'] = now()->toDateTimeString();
        $teacher->data = $currentData;
        $teacher->save();

        return Redirect::route('v1.teacher.manage')->with('success', 'Teacher deactivated successfully.');
    }

    /**
     * Delete a teacher (set isActive to 0)
     */
    public function delete(Teacher $teacher)
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

        // Update data to set isActive = 0 (soft delete)
        $currentData = is_array($teacher->data) ? $teacher->data : json_decode($teacher->data, true) ?? [];
        $currentData['isActive'] = 0;
        $currentData['deleted_at'] = now()->toDateTimeString();
        $teacher->data = $currentData;
        $teacher->save();

        return Redirect::route('v1.teacher.manage')->with('success', 'Teacher deleted successfully.');
    }

    /**
     * Reactivate a teacher (set isActive to 1)
     */
    public function reactivate(Teacher $teacher)
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

        // Update data to set isActive = 1 (reactivate)
        $currentData = is_array($teacher->data) ? $teacher->data : json_decode($teacher->data, true) ?? [];
        $currentData['isActive'] = 1;
        $currentData['reactivated_at'] = now()->toDateTimeString();
        unset($currentData['deactivated_at']);
        $teacher->data = $currentData;
        $teacher->save();

        return Redirect::route('v1.teacher.manage')->with('success', 'Teacher reactivated successfully.');
    }
}
