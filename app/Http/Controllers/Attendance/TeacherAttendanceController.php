<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeacherAttendance;
use App\Models\TeacherSchedule;
use App\Models\Component;
use App\Models\Teacher;
use Carbon\Carbon;

class TeacherAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->input('date', now()->format('Y-m-d'));
        $selectedGuru = $request->input('guru', auth()->user()->name);
        $teacherName = $selectedGuru;

        // Get list of teachers with role 'guru' for dropdown
        $teachers = Teacher::all()->filter(function($teacher) {
            $data = is_array($teacher->data) ? $teacher->data : json_decode($teacher->data, true);
            return isset($data['role']) && $data['role'] === 'guru';
        })->pluck('name', 'name');

        // Get Tahun Ajaran and Semester
        $tahunAjaran = '';
        $semester = '';
        $comp = Component::where('name', 'Tahun Ajaran')->first();
        if ($comp && $comp->data) {
            $data = is_array($comp->data) ? $comp->data : json_decode($comp->data, true);
            $tahunAjaran = $data['nama'] ?? '';
        }
        
        // Determine semester based on date (simple logic, adjust as needed)
        $month = Carbon::parse($selectedDate)->month;
        $semester = ($month >= 7 && $month <= 12) ? 'Semester Ganjil' : 'Semester Genap';

        // Get teacher's schedule for this tahun ajaran and semester
        $schedule = TeacherSchedule::where('guru', $teacherName)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->first();

        // Get day name for selected date
        $dayName = strtolower(Carbon::parse($selectedDate)->locale('id')->dayName);
        
        // Filter schedule by selected day
        $daySchedule = [];
        if ($schedule && $schedule->data) {
            $scheduleData = is_array($schedule->data) ? $schedule->data : json_decode($schedule->data, true);
            $allSchedule = $scheduleData['schedule'] ?? [];
            
            foreach ($allSchedule as $item) {
                if (strtolower($item['hari'] ?? '') === $dayName) {
                    $daySchedule[] = $item;
                }
            }
        }

        // Check if attendance already exists for this date
        $existingAttendance = TeacherAttendance::where('teacher_name', $teacherName)
            ->where('attendance_date', $selectedDate)
            ->first();

        return view('attendance.attendanceteacher', [
            'selectedDate' => $selectedDate,
            'teacherName' => $teacherName,
            'selectedGuru' => $selectedGuru,
            'teachers' => $teachers,
            'tahunAjaran' => $tahunAjaran,
            'semester' => $semester,
            'daySchedule' => $daySchedule,
            'existingAttendance' => $existingAttendance,
            'dayName' => ucfirst($dayName),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_name' => 'required|string',
            'attendance_date' => 'required|date',
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|string',
            'schedule' => 'nullable|array',
            'attend_status' => 'nullable|string|in:hadir,izin,sakit,alpha',
            'attend_notes' => 'nullable|string',
        ]);

        $teacherName = $validated['teacher_name'];
        $recordedBy = auth()->user()->name;

        // If teacher has schedule (teaching subjects), automatically set attend_status to 'hadir' and notes to null
        $hasSchedule = !empty($validated['schedule']) && is_array($validated['schedule']);
        
        if ($hasSchedule) {
            $attendStatus = 'hadir';
            $attendNotes = null;
        } else {
            // For days without schedule, use the provided status
            $attendStatus = $validated['attend_status'] ?? null;
            // Set attend_notes to null if status is hadir
            $attendNotes = $attendStatus === 'hadir' ? null : ($validated['attend_notes'] ?? null);
        }

        // Build attendance data structure
        $attendanceData = [
            'schedule' => $validated['schedule'] ?? null,
            'attend_status' => $attendStatus,
            'attend_notes' => $attendNotes,
            'recorded_at' => now()->toDateTimeString(),
            'recorded_by' => $recordedBy,
        ];

        // Update or create attendance
        TeacherAttendance::updateOrCreate(
            [
                'teacher_name' => $teacherName,
                'attendance_date' => $validated['attendance_date'],
            ],
            [
                'tahun_ajaran' => $validated['tahun_ajaran'],
                'semester' => $validated['semester'],
                'data' => $attendanceData,
            ]
        );

        return redirect()->route('v1.attendance.teacher.index', ['date' => $validated['attendance_date'], 'guru' => $teacherName])
            ->with('success', 'Absensi berhasil disimpan.');
    }

    public function history(Request $request)
    {
        $currentUser = auth()->user();
        $teacherName = $currentUser->name;
        
        // Check if user is admin or guru_piket
        $userData = is_array($currentUser->data) ? $currentUser->data : json_decode($currentUser->data, true);
        $userRole = $userData['role'] ?? '';
        $isAdmin = in_array($userRole, ['super_admin', 'admin', 'guru_piket']);
        
        // Get attendance records based on role
        if ($isAdmin) {
            // Admin can see all attendance or filter by teacher
            $query = TeacherAttendance::query();
            
            if ($request->has('teacher') && $request->input('teacher') !== '') {
                $query->where('teacher_name', $request->input('teacher'));
            }
            
            $attendances = $query->orderByDesc('attendance_date')->paginate(20);
            
            // Get teachers list for filter
            $teachers = Teacher::all()->filter(function($teacher) {
                $data = is_array($teacher->data) ? $teacher->data : json_decode($teacher->data, true);
                return isset($data['role']) && $data['role'] === 'guru';
            })->pluck('name', 'name');
        } else {
            // Regular teacher can only see their own attendance
            $attendances = TeacherAttendance::where('teacher_name', $teacherName)
                ->orderByDesc('attendance_date')
                ->paginate(20);
            $teachers = collect();
        }

        return view('attendance.teacherattendancehistory', [
            'attendances' => $attendances,
            'isAdmin' => $isAdmin,
            'teachers' => $teachers,
            'selectedTeacher' => $request->input('teacher', ''),
        ]);
    }

    public function detail($id)
    {
        $attendance = TeacherAttendance::findOrFail($id);
        
        // Check if user is admin or guru_piket
        $currentUser = auth()->user();
        $userData = is_array($currentUser->data) ? $currentUser->data : json_decode($currentUser->data, true);
        $userRole = $userData['role'] ?? '';
        $isAdmin = in_array($userRole, ['super_admin', 'admin', 'guru_piket']);
        
        // Ensure user can see their own attendance or is admin
        if (!$isAdmin && $attendance->teacher_name !== $currentUser->name) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $attendance
        ]);
    }
}
