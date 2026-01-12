<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    use HasFactory;

    protected $table = 'teacher_attendance';

    protected $fillable = [
        'teacher_name',
        'attendance_date',
        'tahun_ajaran',
        'semester',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
        'attendance_date' => 'date',
    ];

    /**
     * Get teacher schedule for specific date
     */
    public function getScheduleForDate()
    {
        $data = is_array($this->data) ? $this->data : [];
        return $data['schedule'] ?? [];
    }

    /**
     * Get recorded info
     */
    public function getRecordedInfo()
    {
        $data = is_array($this->data) ? $this->data : [];
        return [
            'recorded_at' => $data['recorded_at'] ?? null,
            'recorded_by' => $data['recorded_by'] ?? null,
        ];
    }
}
