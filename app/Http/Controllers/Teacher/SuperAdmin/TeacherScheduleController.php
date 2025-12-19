<?php

namespace App\Http\Controllers\Teacher\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeacherSchedule;
use App\Models\Component;
use App\Models\Teacher;

class TeacherScheduleController extends Controller
{
    public function manage(Request $request)
    {
        $editId = $request->query('id');
        $editing = null;
        if ($editId) {
            $editing = TeacherSchedule::find($editId);
        }

        // Tahun Ajaran from component table
        $tahunAjaranName = '';
        $startDate = '';
        $endDate = '';
        $comp = Component::where('name', 'Tahun Ajaran')->first();
        if ($comp && $comp->data) {
            $data = is_array($comp->data) ? $comp->data : json_decode($comp->data, true);
            $tahunAjaranName = $data['nama'] ?? '';
            $startDate = $data['startDate'] ?? '';
            $endDate = $data['endDate'] ?? '';
        }

        $schedules = TeacherSchedule::orderByDesc('created_at')->get();

        // Teachers with role 'guru'
        $teachers = Teacher::all()->filter(function($t){
            $data = is_array($t->data) ? $t->data : json_decode($t->data, true);
            return ($data['role'] ?? null) === 'guru';
        })->map(function($t){
            return [ 'id' => $t->id, 'name' => $t->name ];
        })->values();

        // Get classes from component table
        $classes = [];
        $classComp = Component::where('name', 'Class')->first();
        
        if ($classComp && $classComp->data) {
            $classData = is_array($classComp->data) ? $classComp->data : json_decode($classComp->data, true);
            
            // Debug: Log the structure
            \Log::info('Class Component Data:', ['data' => $classData]);
            
            // Try different possible structures
            if (isset($classData['nama']) && is_array($classData['nama'])) {
                $classes = $classData['nama'];
            } elseif (is_array($classData) && isset($classData[0])) {
                // If it's directly an array of class names
                $classes = $classData;
            } elseif (isset($classData['classes'])) {
                $classes = $classData['classes'];
            } elseif (isset($classData['class'])) {
                $classes = $classData['class'];
            }
            
            \Log::info('Extracted Classes:', ['classes' => $classes]);
        }

        return view('teacher.superadmin.manageteacherschedule', [
            'schedules' => $schedules,
            'editing' => $editing,
            'tahunAjaranName' => $tahunAjaranName,
            'defaultStartDate' => $startDate,
            'defaultEndDate' => $endDate,
            'teachers' => $teachers,
            'classes' => $classes,
        ]);
    }

    public function upsert(Request $request)
    {
        $validated = $request->validate([
            'guru' => 'required|string|max:255',
            'id' => 'nullable|integer|exists:teacher_schedules,id',
            'tahun_ajaran' => 'required|string|max:255',
            'semester' => 'required|string|in:Semester Ganjil,Semester Genap',
            'startDate' => 'required|date_format:Y-m-d',
            'endDate' => 'required|date_format:Y-m-d|after_or_equal:startDate',
            'data' => 'required|string', // JSON string
        ]);

        // decode data to ensure valid JSON structure
        $decoded = json_decode($validated['data'], true);
        if (!is_array($decoded) || !isset($decoded['schedule']) || !is_array($decoded['schedule'])) {
            return back()->with('error', 'Struktur data jadwal tidak valid.');
        }

        // Upsert by id if provided, otherwise by guru + tahun_ajaran + semester
        if (!empty($validated['id'])) {
            $schedule = TeacherSchedule::findOrFail($validated['id']);
        } else {
            $schedule = TeacherSchedule::firstOrNew([
                'guru' => $validated['guru'],
                'tahun_ajaran' => $validated['tahun_ajaran'],
                'semester' => $validated['semester'],
            ]);
        }

        $schedule->guru = $validated['guru'];
        $schedule->tahun_ajaran = $validated['tahun_ajaran'];
        $schedule->semester = $validated['semester'];
        $schedule->startDate = $validated['startDate'];
        $schedule->endDate = $validated['endDate'];
        $schedule->data = $decoded;
        $schedule->save();

        return redirect()->route('v1.teacher.schedule.manage')
            ->with('success', 'Jadwal guru berhasil disimpan.');
    }
}
