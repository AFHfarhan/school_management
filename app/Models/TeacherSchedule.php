<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSchedule extends Model
{
    protected $table = 'teacher_schedules';

    protected $fillable = [
        'guru',
        'tahun_ajaran',
        'semester',
        'startDate',
        'endDate',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
