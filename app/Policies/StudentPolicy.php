<?php

namespace App\Policies;

use App\Models\Student;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class StudentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any students.
     */
    public function viewAny($user = null): bool
    {
        return Auth::guard('teacher')->check();
    }

    /**
     * Determine whether the user can view the student.
     */
    public function view($user = null, Student $student): bool
    {
        return Auth::guard('teacher')->check();
    }

    /**
     * Determine whether the user can create students.
     */
    public function create($user = null): bool
    {
        return Auth::guard('teacher')->check();
    }

    /**
     * Determine whether the user can update the student.
     */
    public function update($user = null, Student $student): bool
    {
        return Auth::guard('teacher')->check();
    }

    /**
     * Determine whether the user can delete the student.
     */
    public function delete($user = null, Student $student): bool
    {
        return Auth::guard('teacher')->check();
    }
}
