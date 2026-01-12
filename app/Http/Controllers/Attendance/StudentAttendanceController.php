<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Component;

class StudentAttendanceController extends Controller
{
    /**
     * Show attendance list/index
     */
    public function index(Request $request){
        $query = StudentAttendance::query()->orderBy('attendance_date', 'desc');

        // Filters
        $class = $request->query('class');
        $period = $request->query('period');
        $month = $request->query('month'); // format: YYYY-MM
        $studentClass = $request->query('student_class'); // Filter for student table

        if (!empty($class)) {
            $query->where('class', $class);
        }

        if (!empty($month) && preg_match('/^\d{4}-\d{2}$/', $month)) {
            [$year, $mon] = explode('-', $month);
            $query->whereYear('attendance_date', (int)$year)
                  ->whereMonth('attendance_date', (int)$mon);
        }

        $attendances = $query->get();

        // Filter by period (stored inside data JSON)
        if (!empty($period)) {
            $attendances = $attendances->filter(function ($attendance) use ($period) {
                $data = is_array($attendance->data) ? $attendance->data : json_decode($attendance->data, true);
                return ($data['period'] ?? null) === $period;
            })->values();
        }

        // Build classes list from Component table for filter dropdown
        $classes = [];
        $classComponent = Component::where('name', 'Class')->where('category', 'Absensi')->first();
        if ($classComponent && $classComponent->data && is_array($classComponent->data)) {
            foreach ($classComponent->data as $item) {
                if (is_array($item) && isset($item['nama']) && isset($item['tingkat'])) {
                    $classes[$item['nama']] = $item['tingkat'];
                }
            }
        }

        // Get Tahun Ajaran date range from component table
        $tahunAjaranComponent = Component::where('name', 'Tahun Ajaran')->first();
        $startDate = null;
        $endDate = null;
        
        if ($tahunAjaranComponent && $tahunAjaranComponent->data) {
            $tahunAjaranData = is_array($tahunAjaranComponent->data) ? $tahunAjaranComponent->data : json_decode($tahunAjaranComponent->data, true);
            $startDate = $tahunAjaranData['startDate'] ?? null;
            $endDate = $tahunAjaranData['endDate'] ?? null;
        }

        // Get all students with their absent history
        $studentsWithAbsent = Student::all()->map(function($student) use ($startDate, $endDate) {
            $data = is_array($student->data) ? $student->data : json_decode($student->data, true);
            $absent = $data['absent'] ?? [];
            
            // Filter absent records within Tahun Ajaran date range
            $filteredAbsent = $absent;
            if ($startDate && $endDate) {
                $filteredAbsent = array_filter($absent, function($record) use ($startDate, $endDate) {
                    $attendanceDate = $record['attendance_date'] ?? null;
                    if (!$attendanceDate) return false;
                    
                    return $attendanceDate >= $startDate && $attendanceDate <= $endDate;
                });
            }
            
            return [
                'id' => $student->id,
                'name' => $student->name,
                'class' => $data['academic']['class'] ?? '-',
                'absent_count' => count($filteredAbsent),
                'absent_details' => $filteredAbsent,
            ];
        });

        // Filter by student class if specified
        if (!empty($studentClass)) {
            $studentsWithAbsent = $studentsWithAbsent->filter(function($student) use ($studentClass) {
                return $student['class'] === $studentClass;
            })->values();
        } else {
            // Show only students with assigned classes
            $studentsWithAbsent = $studentsWithAbsent->filter(function($student) {
                return $student['class'] !== '-';
            })->values();
        }

        return view('attendance.index', [
            'attendances' => $attendances,
            'classes' => $classes,
            'selectedClass' => $class,
            'selectedPeriod' => $period,
            'selectedMonth' => $month,
            'selectedStudentClass' => $studentClass,
            'studentsWithAbsent' => $studentsWithAbsent,
        ]);
    }

    /**
     * Show attendance detail
     */
    public function show($id)
    {
        $attendance = StudentAttendance::findOrFail($id);
        $data = is_array($attendance->data) ? $attendance->data : json_decode($attendance->data, true);
        
        // Get all students for this class
        $students = Student::all()->filter(function ($student) use ($attendance, $data) {
            $studentData = is_array($student->data) ? $student->data : json_decode($student->data, true);
            
            $studentClass = $studentData['academic']['class'] ?? null;
            $studentPeriod = $studentData['academic']['period'] ?? null;
            $studentGrade = $studentData['academic']['grade'] ?? null;

            return $studentClass === $attendance->class && 
                   $studentPeriod === ($data['period'] ?? null) && 
                   $studentGrade === ($data['grade'] ?? null);
        });

        // Separate attending and absent students
        $abstainList = $data['abstain'] ?? [];
        $absentStudentNames = collect($abstainList)->pluck('student_name')->toArray();
        
        $attendingStudents = $students->filter(function($student) use ($absentStudentNames) {
            return !in_array($student->name, $absentStudentNames);
        });

        return view('attendance.detailattendancestud', [
            'attendance' => $attendance,
            'attendingStudents' => $attendingStudents,
        ]);
    }

    /**
     * Show attendance form
     */
    public function create()
    {
        // Get class data from component table
        $classComponent = Component::where('name', 'Class')
                                    ->where('category', 'Absensi')
                                    ->first();
        
        $classes = [];
        if ($classComponent && $classComponent->data) {
            $namaData = $classComponent->data;
            // Loop through array of class objects
            if (is_array($namaData)) {
                foreach ($namaData as $item) {
                    if (is_array($item) && isset($item['nama']) && isset($item['tingkat'])) {
                        // Use 'tingkat' as key and 'nama' as value
                        $classes[$item['nama']] = $item['tingkat'];
                    }
                }
            }
        }

        return view('attendance.attendancestudent', [
            'classes' => $classes,
        ]);
    }

    /**
     * Search students by class, period, and grade
     */
    public function search(Request $request)
    {
        $class = $request->query('class');
        $period = $request->query('period');
        $grade = $request->query('grade');

        // Get class data from component table
        $classComponent = Component::where('name', 'Class')
                                    ->first();
        $classes = [];
        
        if ($classComponent && $classComponent->data) {
            $namaData = $classComponent->data;
            // Loop through array of class objects
            if (is_array($namaData)) {
                foreach ($namaData as $item) {
                    if (is_array($item) && isset($item['nama']) && isset($item['tingkat'])) {
                        // Use 'tingkat' as key and 'nama' as value
                        $classes[$item['nama']] = $item['tingkat'];
                    }
                }
            }
        }

        

        // Get all students and filter by matching criteria
        $students = Student::all()->filter(function ($student) use ($class, $period, $grade) {
            $data = is_array($student->data) ? $student->data : json_decode($student->data, true);
            
            $studentClass = $data['academic']['class'] ?? null;
            $studentPeriod = $data['academic']['period'] ?? null;
            $studentGrade = $data['academic']['grade'] ?? null;

            return $studentClass === $class && 
                   $studentPeriod === $period && 
                   $studentGrade === $grade;
        });

        return view('attendance.attendancestudent', [
            'students' => $students,
            'classes' => $classes,
            'class' => $class,
            'period' => $period,
            'grade' => $grade,
        ]);
    }

    /**
     * Store attendance records
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class' => 'required|string',
            'period' => 'required|string',
            'grade' => 'required|string',
            'attendance_date' => 'required|date',
            'dayOfWeek' => 'required|string',
            'semester' => 'required|string',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:hadir,sakit,izin,alpa',
            'notes' => 'sometimes|array',
            'notes.*' => 'nullable|string|max:255',
        ]);

        try {
            // Check if attendance record already exists for this date, class, and semester
            $existingAttendance = StudentAttendance::where('attendance_date', $validated['attendance_date'])
                ->where('class', $validated['class'])
                ->where('semester', $validated['semester'])
                ->first();

            if ($existingAttendance) {
                return redirect()
                    ->back()
                    ->with('error', 'Absensi untuk kelas ' . $validated['class'] . ' pada tanggal ' . date('d/m/Y', strtotime($validated['attendance_date'])) . ' semester ' . $validated['semester'] . ' sudah ada. Gunakan fitur edit untuk mengubahnya.');
            }

            // Collect all abstain records (sakit, izin, alpa)
            $abstainRecords = [];
            $allHadir = true;
            $studentCount = 0;

            // First pass: collect abstain records and check if all hadir
            $notes = $request->input('notes', []);
            foreach ($validated['attendance'] as $studentId => $status) {
                $studentCount++;
                if ($status !== 'hadir') {
                    $allHadir = false;
                    $student = Student::find($studentId);
                    if ($student) {
                        $abstainRecords[] = [
                            'student_name' => $student->name,
                            'status' => $status,
                            'note' => $notes[$studentId] ?? null,
                        ];
                    }
                }
            }

            // Build attendance data array with all students
            $attendanceData = [
                'grade' => $validated['grade'],
                'period' => $validated['period'],
                'recorded_at' => now()->format('Y-m-d H:i:s'),
                'recorded_by' => auth('teacher')->user()->name ?? 'System',
                'student_count' => $studentCount,
            ];

            // Add status field if all students have hadir status
            if ($allHadir) {
                $attendanceData['status'] = 'hadir';
            }

            // Add abstain array if there are non-hadir records
            if (!empty($abstainRecords)) {
                $attendanceData['abstain'] = $abstainRecords;
            }

            // Create single attendance record for the entire class
            StudentAttendance::create([
                'student_id' => 0,
                'student_name' => $validated['class'],
                'class' => $validated['class'],
                'semester' => $validated['semester'],
                'entry_year' => date('Y'),
                'dayOfWeek' => $validated['dayOfWeek'],
                'attendance_date' => $validated['attendance_date'],
                'data' => $attendanceData,
            ]);

            // Store absent student data to students table
            foreach ($validated['attendance'] as $studentId => $status) {
                if ($status !== 'hadir') {
                    $student = Student::find($studentId);
                    if ($student) {
                        $studentData = is_array($student->data) ? $student->data : json_decode($student->data, true);
                        
                        // Initialize absent array if not exists
                        if (!isset($studentData['absent'])) {
                            $studentData['absent'] = [];
                        }
                        
                        // Add new absent record
                        $studentData['absent'][] = [
                            'status' => $status,
                            'attendance_date' => $validated['attendance_date'],
                            'note' => $notes[$studentId] ?? null,
                        ];
                        
                        // Update student data
                        $student->update([
                            'data' => $studentData,
                        ]);
                    }
                }
            }

            return redirect()
                ->route('v1.attendance.create')
                ->with('success', 'Absensi berhasil disimpan untuk kelas ' . $validated['class'] . ' (' . $studentCount . ' siswa).');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan absensi: ' . $e->getMessage());
        }
    }

    /**
     * Show edit attendance form
     */
    public function edit($id)
    {
        $attendance = StudentAttendance::findOrFail($id);
        $data = is_array($attendance->data) ? $attendance->data : json_decode($attendance->data, true);
        
        // Get students for this class
        $students = Student::all()->filter(function ($student) use ($attendance, $data) {
            $studentData = is_array($student->data) ? $student->data : json_decode($student->data, true);
            
            $studentClass = $studentData['academic']['class'] ?? null;
            $studentPeriod = $studentData['academic']['period'] ?? null;
            $studentGrade = $studentData['academic']['grade'] ?? null;

            return $studentClass === $attendance->class && 
                   $studentPeriod === ($data['period'] ?? null) && 
                   $studentGrade === ($data['grade'] ?? null);
        });

        return view('attendance.editattendancestud', [
            'attendance' => $attendance,
            'students' => $students,
        ]);
    }

    /**
     * Filter students by class
     */
    public function filterStudents(Request $request)
    {
        $class = $request->query('class');

        // Get all students with their absent history
        $studentsWithAbsent = Student::all()->map(function($student) {
            $data = is_array($student->data) ? $student->data : json_decode($student->data, true);
            $absent = $data['absent'] ?? [];
            
            return [
                'id' => $student->id,
                'name' => $student->name,
                'class' => $data['academic']['class'] ?? '-',
                'absent_count' => count($absent),
                'absent_details' => $absent,
            ];
        });

        // Filter by class if specified
        if (!empty($class)) {
            $studentsWithAbsent = $studentsWithAbsent->filter(function($student) use ($class) {
                return $student['class'] === $class;
            })->values();
        } else {
            // Show only students with assigned classes
            $studentsWithAbsent = $studentsWithAbsent->filter(function($student) {
                return $student['class'] !== '-';
            })->values();
        }

        return response()->json([
            'success' => true,
            'data' => $studentsWithAbsent,
        ]);
    }

    /**
     * Show detailed absent history for a student (Tahun Ajaran filtered)
     */
    public function studentDetail($id)
    {
        $student = Student::findOrFail($id);
        $studentData = is_array($student->data) ? $student->data : json_decode($student->data, true);

        // Tahun Ajaran range
        $tahunAjaranComponent = Component::where('name', 'Tahun Ajaran')->first();
        $startDate = null;
        $endDate = null;
        if ($tahunAjaranComponent && $tahunAjaranComponent->data) {
            $tahunAjaranData = is_array($tahunAjaranComponent->data) ? $tahunAjaranComponent->data : json_decode($tahunAjaranComponent->data, true);
            $startDate = $tahunAjaranData['startDate'] ?? null;
            $endDate = $tahunAjaranData['endDate'] ?? null;
        }

        $absent = $studentData['absent'] ?? [];
        if ($startDate && $endDate) {
            $absent = array_values(array_filter($absent, function($record) use ($startDate, $endDate) {
                $attendanceDate = $record['attendance_date'] ?? null;
                if (!$attendanceDate) return false;
                return $attendanceDate >= $startDate && $attendanceDate <= $endDate;
            }));
        }

        return view('attendance.detailattendancestud', [
            'studentDetail' => true,
            'student' => $student,
            'absentDetails' => $absent,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Update attendance record
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'class' => 'required|string',
            'period' => 'required|string',
            'grade' => 'required|string',
            'attendance_date' => 'required|date',
            'dayOfWeek' => 'required|string',
            'semester' => 'required|string',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:hadir,sakit,izin,alpa',
            'notes' => 'sometimes|array',
            'notes.*' => 'nullable|string|max:255',
        ]);

        try {
            $attendance = StudentAttendance::findOrFail($id);
            // Keep original attendance date to correctly update student records if the date changes
            $oldDate = $attendance->attendance_date instanceof \Carbon\Carbon
                ? $attendance->attendance_date->format('Y-m-d')
                : (string) $attendance->attendance_date;
            $newDate = $validated['attendance_date'];

            $oldData = is_array($attendance->data) ? $attendance->data : json_decode($attendance->data, true);
            $oldAbstainList = $oldData['abstain'] ?? [];

            // Collect all abstain records (sakit, izin, alpa)
            $abstainRecords = [];
            $allHadir = true;
            $studentCount = 0;

            // First pass: collect abstain records and check if all hadir
            $notes = $request->input('notes', []);
            foreach ($validated['attendance'] as $studentId => $status) {
                $studentCount++;
                if ($status !== 'hadir') {
                    $allHadir = false;
                    $student = Student::find($studentId);
                    if ($student) {
                         $abstainRecords[] = [
                            'student_name' => $student->name,
                            'status' => $status,
                            'note' => $notes[$studentId] ?? null,
                        ];
                    }
                }
            }

            // Build updated attendance data array
            $attendanceData = [
                'grade' => $validated['grade'],
                'period' => $validated['period'],
                'recorded_at' => $oldData['recorded_at'] ?? now()->format('Y-m-d H:i:s'),
                'recorded_by' => $oldData['recorded_by'] ?? (auth('teacher')->user()->name ?? 'System'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
                'updated_by' => auth('teacher')->user()->name ?? 'System',
                'student_count' => $studentCount,
            ];

            // Add status field if all students have hadir status
            if ($allHadir) {
                $attendanceData['status'] = 'hadir';
            }

            // Add abstain array if there are non-hadir records
            if (!empty($abstainRecords)) {
                $attendanceData['abstain'] = $abstainRecords;
            }

            // Update attendance record
            $attendance->update([
                'class' => $validated['class'],
                'semester' => $validated['semester'],
                'dayOfWeek' => $validated['dayOfWeek'],
                'attendance_date' => $validated['attendance_date'],
                'data' => $attendanceData,
            ]);

            // Update student absent records (students.data.absent)
            foreach ($validated['attendance'] as $studentId => $newStatus) {
                $student = Student::find($studentId);
                if (!$student) continue;

                $studentData = is_array($student->data) ? $student->data : json_decode($student->data, true);
                
                // Find if student had old absent record
                $oldStatus = 'hadir';
                foreach ($oldAbstainList as $oldAbstain) {
                    if ($oldAbstain['student_name'] === $student->name) {
                        $oldStatus = $oldAbstain['status'];
                        break;
                    }
                }

                // Initialize absent array if not exists
                if (!isset($studentData['absent'])) {
                    $studentData['absent'] = [];
                }

                // If status changed from hadir to absent, add new record
                if ($oldStatus === 'hadir' && $newStatus !== 'hadir') {
                    $studentData['absent'][] = [
                        'status' => $newStatus,
                        'attendance_date' => $newDate,
                        'note' => ($request->input('notes', [])[$studentId] ?? null),
                    ];
                }
                // If status changed from absent to hadir, remove the record
                elseif ($oldStatus !== 'hadir' && $newStatus === 'hadir') {
                    // Remove record matching the OLD attendance date in case the date was changed in this update
                    $studentData['absent'] = array_values(array_filter($studentData['absent'], function($record) use ($oldDate) {
                        return ($record['attendance_date'] ?? null) !== $oldDate;
                    }));
                }
                // If status changed from one absent type to another, update the record
                elseif ($oldStatus !== 'hadir' && $newStatus !== 'hadir') {
                    foreach ($studentData['absent'] as &$record) {
                        // Find by OLD date and then update to NEW date to keep consistency if date changed
                        if (($record['attendance_date'] ?? null) === $oldDate) {
                            $record['status'] = $newStatus;
                            $record['attendance_date'] = $newDate;
                            $record['note'] = ($request->input('notes', [])[$studentId] ?? ($record['note'] ?? null));
                            break;
                        }
                    }
                }

                // Update student data
                $student->update([
                    'data' => $studentData,
                ]);
            }

            return redirect()
                ->route('v1.attendance.show', $id)
                ->with('success', 'Absensi berhasil diperbarui untuk kelas ' . $validated['class'] . ' (' . $studentCount . ' siswa).');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui absensi: ' . $e->getMessage());
        }
    }

    /**
     * Download warning letter based on absent count
     */
    public function downloadWarningLetter(Request $request)
    {
        $studentId = $request->query('studentId');
        $type = $request->query('type'); // sp1, sp2, sp_ortu

        $student = Student::findOrFail($studentId);
        $studentData = is_array($student->data) ? $student->data : json_decode($student->data, true);

        // Get Tahun Ajaran range
        $tahunAjaranComponent = Component::where('name', 'Tahun Ajaran')->first();
        $startDate = null;
        $endDate = null;
        if ($tahunAjaranComponent && $tahunAjaranComponent->data) {
            $tahunAjaranData = is_array($tahunAjaranComponent->data) ? $tahunAjaranComponent->data : json_decode($tahunAjaranComponent->data, true);
            $startDate = $tahunAjaranData['startDate'] ?? null;
            $endDate = $tahunAjaranData['endDate'] ?? null;
        }

        // Get absent count within Tahun Ajaran range
        $absent = $studentData['absent'] ?? [];
        if ($startDate && $endDate) {
            $absent = array_values(array_filter($absent, function($record) use ($startDate, $endDate) {
                $attendanceDate = $record['attendance_date'] ?? null;
                if (!$attendanceDate) return false;
                return $attendanceDate >= $startDate && $attendanceDate <= $endDate;
            }));
        }
        $absentCount = count($absent);

        // Get warning letter template from Component table
        $letterName = '';
        switch($type) {
            case 'sp1':
                $letterName = 'Surat Peringatan 1';
                if ($absentCount < 5 || $absentCount > 9) {
                    return redirect()->back()->with('error', 'Jumlah ketidakhadiran tidak memenuhi kriteria untuk Surat Peringatan 1 (5-9 hari).');
                }
                break;
            case 'sp2':
                $letterName = 'Surat Peringatan 2';
                if ($absentCount < 10 || $absentCount > 14) {
                    return redirect()->back()->with('error', 'Jumlah ketidakhadiran tidak memenuhi kriteria untuk Surat Peringatan 2 (10-14 hari).');
                }
                break;
            case 'sp_ortu':
                $letterName = 'Surat Pemanggilan Orang Tua';
                if ($absentCount < 15) {
                    return redirect()->back()->with('error', 'Jumlah ketidakhadiran tidak memenuhi kriteria untuk Surat Pemanggilan Orang Tua (>=15 hari).');
                }
                break;
            default:
                return redirect()->back()->with('error', 'Tipe surat tidak valid.');
        }

        // Get letter template from component table
        $letterComponent = Component::where('name', $letterName)
            ->where('category', 'mandatory')
            ->first();

        if (!$letterComponent) {
            return redirect()->back()->with('error', 'Template surat ' . $letterName . ' tidak ditemukan. Silakan hubungi administrator.');
        }

        $letterData = is_array($letterComponent->data) ? $letterComponent->data : json_decode($letterComponent->data, true);
        $filePath = $letterData['uploads'] ?? null;

        if (!$filePath || !file_exists(public_path($filePath))) {
            return redirect()->back()->with('error', 'File surat ' . $letterName . ' tidak ditemukan. Silakan hubungi administrator untuk mengupload template.');
        }

        // Calculate start and end dates of absent period
        $absentStartDate = null;
        $absentEndDate = null;
        if (!empty($absent)) {
            // Sort absent records by date
            usort($absent, function($a, $b) {
                return strcmp($a['attendance_date'] ?? '', $b['attendance_date'] ?? '');
            });
            
            $absentStartDate = $absent[0]['attendance_date'] ?? null;
            $absentEndDate = $absent[count($absent) - 1]['attendance_date'] ?? null;
        }

        // Prepare replacement data
        $replacements = [
            '{{student_name}}' => $student->name,
            '{{nama_siswa}}' => $student->name,
            '{{STUDENT_NAME}}' => strtoupper($student->name),
            '{{NAMA_SISWA}}' => strtoupper($student->name),
            
            '{{date}}' => date('d F Y'),
            '{{tanggal}}' => date('d F Y'),
            '{{DATE}}' => strtoupper(date('d F Y')),
            '{{TANGGAL}}' => strtoupper(date('d F Y')),
            
            '{{start_date}}' => $absentStartDate ? date('d F Y', strtotime($absentStartDate)) : '-',
            '{{tanggal_mulai}}' => $absentStartDate ? date('d F Y', strtotime($absentStartDate)) : '-',
            '{{START_DATE}}' => $absentStartDate ? strtoupper(date('d F Y', strtotime($absentStartDate))) : '-',
            '{{TANGGAL_MULAI}}' => $absentStartDate ? strtoupper(date('d F Y', strtotime($absentStartDate))) : '-',
            
            '{{end_date}}' => $absentEndDate ? date('d F Y', strtotime($absentEndDate)) : '-',
            '{{tanggal_akhir}}' => $absentEndDate ? date('d F Y', strtotime($absentEndDate)) : '-',
            '{{END_DATE}}' => $absentEndDate ? strtoupper(date('d F Y', strtotime($absentEndDate))) : '-',
            '{{TANGGAL_AKHIR}}' => $absentEndDate ? strtoupper(date('d F Y', strtotime($absentEndDate))) : '-',
            
            '{{absent_count}}' => $absentCount,
            '{{jumlah_absen}}' => $absentCount,
            '{{ABSENT_COUNT}}' => $absentCount,
            '{{JUMLAH_ABSEN}}' => $absentCount,
            
            '{{class}}' => $studentData['academic']['class'] ?? '-',
            '{{kelas}}' => $studentData['academic']['class'] ?? '-',
            '{{CLASS}}' => strtoupper($studentData['academic']['class'] ?? '-'),
            '{{KELAS}}' => strtoupper($studentData['academic']['class'] ?? '-'),
            
            '{{parent_name}}' => $studentData['contact']['parent_name'] ?? '-',
            '{{nama_orang_tua}}' => $studentData['contact']['parent_name'] ?? '-',
            '{{PARENT_NAME}}' => strtoupper($studentData['contact']['parent_name'] ?? '-'),
            '{{NAMA_ORANG_TUA}}' => strtoupper($studentData['contact']['parent_name'] ?? '-'),
        ];

        // Process the template file based on extension
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $outputFileName = $type . '_' . str_replace(' ', '_', $student->name) . '_' . date('Y-m-d') . '.' . $extension;
        
        try {
            // Ensure temp directory exists
            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                if (!@mkdir($tempDir, 0755, true)) {
                    return redirect()->back()->with('error', 'Tidak dapat membuat direktori temp. Hubungi administrator.');
                }
            }
            
            $tempFile = $tempDir . DIRECTORY_SEPARATOR . $outputFileName;
            
            if ($extension === 'docx') {
                // Handle DOCX files using PHPWord; if not available, fall back to raw template download
                if (!class_exists('\\PhpOffice\\PhpWord\\TemplateProcessor')) {
                    return response()->download(public_path($filePath), $outputFileName);
                }
                
                try {
                    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path($filePath));
                    // Set values for each placeholder
                    foreach ($replacements as $placeholder => $value) {
                        $cleanPlaceholder = str_replace(['{{', '}}'], '', $placeholder);
                        try {
                            $templateProcessor->setValue($cleanPlaceholder, (string)$value);
                        } catch (\Exception $e) {
                            // Placeholder might not exist in template, continue
                        }
                    }
                    $templateProcessor->saveAs($tempFile);
                    if (!file_exists($tempFile)) {
                        // Fallback to raw template download
                        return response()->download(public_path($filePath), $outputFileName);
                    }
                    return response()->download($tempFile, $outputFileName)->deleteFileAfterSend(true);
                } catch (\Exception $e) {
                    // Any processing failure: download original template
                    \Log::warning('DOCX processing failed, falling back to raw download', ['error' => $e->getMessage()]);
                    return response()->download(public_path($filePath), $outputFileName);
                }
            } else {
                // For other file types (txt, rtf, html, etc.), use simple string replacement
                $content = file_get_contents(public_path($filePath));
                
                if ($content === false) {
                    // As a last resort, try downloading the original template
                    return response()->download(public_path($filePath), $outputFileName);
                }
                
                foreach ($replacements as $placeholder => $value) {
                    $content = str_replace($placeholder, (string)$value, $content);
                }
                
                // Write to temp file
                $bytesWritten = file_put_contents($tempFile, $content);
                if ($bytesWritten === false) {
                    // If writing fails, stream the content directly as a download
                    $mime = $extension === 'rtf' ? 'application/rtf' : ($extension === 'html' || $extension === 'htm' ? 'text/html' : 'text/plain');
                    return response($content)
                        ->header('Content-Type', $mime)
                        ->header('Content-Disposition', 'attachment; filename="' . $outputFileName . '"');
                }
                
                // Verify file was created
                if (!file_exists($tempFile)) {
                    // Stream content if file cannot be found for any reason
                    $mime = $extension === 'rtf' ? 'application/rtf' : ($extension === 'html' || $extension === 'htm' ? 'text/html' : 'text/plain');
                    return response($content)
                        ->header('Content-Type', $mime)
                        ->header('Content-Disposition', 'attachment; filename="' . $outputFileName . '"');
                }
                
                return response()->download($tempFile, $outputFileName)->deleteFileAfterSend(true);
            }
        } catch (\Exception $e) {
            \Log::error('Download letter error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Gagal memproses template surat: ' . $e->getMessage());
        }
    }

    /**
     * Preview warning letter with placeholders filled before download
     */
    public function previewWarningLetter(Request $request)
    {
        $studentId = $request->query('studentId');
        $type = $request->query('type'); // sp1, sp2, sp_ortu

        $student = Student::findOrFail($studentId);
        $studentData = is_array($student->data) ? $student->data : json_decode($student->data, true);

        // Get Tahun Ajaran range
        $tahunAjaranComponent = Component::where('name', 'Tahun Ajaran')->first();
        $startDate = null;
        $endDate = null;
        if ($tahunAjaranComponent && $tahunAjaranComponent->data) {
            $tahunAjaranData = is_array($tahunAjaranComponent->data) ? $tahunAjaranComponent->data : json_decode($tahunAjaranComponent->data, true);
            $startDate = $tahunAjaranData['startDate'] ?? null;
            $endDate = $tahunAjaranData['endDate'] ?? null;
        }

        // Get absent count within Tahun Ajaran range
        $absent = $studentData['absent'] ?? [];
        if ($startDate && $endDate) {
            $absent = array_values(array_filter($absent, function($record) use ($startDate, $endDate) {
                $attendanceDate = $record['attendance_date'] ?? null;
                if (!$attendanceDate) return false;
                return $attendanceDate >= $startDate && $attendanceDate <= $endDate;
            }));
        }
        $absentCount = count($absent);

        // Determine letter name and validate thresholds
        $letterName = '';
        switch($type) {
            case 'sp1':
                $letterName = 'Surat Peringatan 1';
                if ($absentCount < 5 || $absentCount > 9) {
                    return redirect()->back()->with('error', 'Jumlah ketidakhadiran tidak memenuhi kriteria untuk Surat Peringatan 1 (5-9 hari).');
                }
                break;
            case 'sp2':
                $letterName = 'Surat Peringatan 2';
                if ($absentCount < 10 || $absentCount > 14) {
                    return redirect()->back()->with('error', 'Jumlah ketidakhadiran tidak memenuhi kriteria untuk Surat Peringatan 2 (10-14 hari).');
                }
                break;
            case 'sp_ortu':
                $letterName = 'Surat Pemanggilan Orang Tua';
                if ($absentCount < 15) {
                    return redirect()->back()->with('error', 'Jumlah ketidakhadiran tidak memenuhi kriteria untuk Surat Pemanggilan Orang Tua (>=15 hari).');
                }
                break;
            default:
                return redirect()->back()->with('error', 'Tipe surat tidak valid.');
        }

        // Get letter template from component table
        $letterComponent = Component::where('name', $letterName)
            ->where('category', 'mandatory')
            ->first();

        if (!$letterComponent) {
            return redirect()->back()->with('error', 'Template surat ' . $letterName . ' tidak ditemukan.');
        }

        $letterData = is_array($letterComponent->data) ? $letterComponent->data : json_decode($letterComponent->data, true);
        $filePath = $letterData['uploads'] ?? null;

        if (!$filePath || !file_exists(public_path($filePath))) {
            return redirect()->back()->with('error', 'File surat ' . $letterName . ' tidak ditemukan.');
        }

        // Calculate start and end dates of absent period
        $absentStartDate = null;
        $absentEndDate = null;
        if (!empty($absent)) {
            usort($absent, function($a, $b) {
                return strcmp($a['attendance_date'] ?? '', $b['attendance_date'] ?? '');
            });
            $absentStartDate = $absent[0]['attendance_date'] ?? null;
            $absentEndDate = $absent[count($absent) - 1]['attendance_date'] ?? null;
        }

        // Prepare replacements
        $replacements = [
            '{{student_name}}' => $student->name,
            '{{nama_siswa}}' => $student->name,
            '{{STUDENT_NAME}}' => strtoupper($student->name),
            '{{NAMA_SISWA}}' => strtoupper($student->name),

            '{{date}}' => date('d F Y'),
            '{{tanggal}}' => date('d F Y'),
            '{{DATE}}' => strtoupper(date('d F Y')),
            '{{TANGGAL}}' => strtoupper(date('d F Y')),

            '{{start_date}}' => $absentStartDate ? date('d F Y', strtotime($absentStartDate)) : '-',
            '{{tanggal_mulai}}' => $absentStartDate ? date('d F Y', strtotime($absentStartDate)) : '-',
            '{{START_DATE}}' => $absentStartDate ? strtoupper(date('d F Y', strtotime($absentStartDate))) : '-',
            '{{TANGGAL_MULAI}}' => $absentStartDate ? strtoupper(date('d F Y', strtotime($absentStartDate))) : '-',

            '{{end_date}}' => $absentEndDate ? date('d F Y', strtotime($absentEndDate)) : '-',
            '{{tanggal_akhir}}' => $absentEndDate ? date('d F Y', strtotime($absentEndDate)) : '-',
            '{{END_DATE}}' => $absentEndDate ? strtoupper(date('d F Y', strtotime($absentEndDate))) : '-',
            '{{TANGGAL_AKHIR}}' => $absentEndDate ? strtoupper(date('d F Y', strtotime($absentEndDate))) : '-',

            '{{absent_count}}' => $absentCount,
            '{{jumlah_absen}}' => $absentCount,
            '{{ABSENT_COUNT}}' => $absentCount,
            '{{JUMLAH_ABSEN}}' => $absentCount,

            '{{class}}' => $studentData['academic']['class'] ?? '-',
            '{{kelas}}' => $studentData['academic']['class'] ?? '-',
            '{{CLASS}}' => strtoupper($studentData['academic']['class'] ?? '-'),
            '{{KELAS}}' => strtoupper($studentData['academic']['class'] ?? '-'),

            '{{parent_name}}' => $studentData['contact']['parent_name'] ?? '-',
            '{{nama_orang_tua}}' => $studentData['contact']['parent_name'] ?? '-',
            '{{PARENT_NAME}}' => strtoupper($studentData['contact']['parent_name'] ?? '-'),
            '{{NAMA_ORANG_TUA}}' => strtoupper($studentData['contact']['parent_name'] ?? '-'),
        ];

        // Decide preview strategy by file extension
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $html = null;
        $text = null;
        $docxPreviewUnsupported = false;

        try {
            if (in_array($extension, ['html', 'htm'])) {
                $content = file_get_contents(public_path($filePath));
                foreach ($replacements as $placeholder => $value) {
                    $content = str_replace($placeholder, e((string)$value), $content);
                }
                // HTML content may already contain markup; avoid double-escaping
                // Since we used e() in replacement values, we can safely render the HTML template structure
                $html = $content;
            } elseif (in_array($extension, ['txt', 'rtf'])) {
                $content = file_get_contents(public_path($filePath));
                foreach ($replacements as $placeholder => $value) {
                    $content = str_replace($placeholder, (string)$value, $content);
                }
                $text = $content;
            } elseif ($extension === 'docx') {
                // DOCX preview not supported; inform user and provide download action
                $docxPreviewUnsupported = true;
            } else {
                // Default to text preview
                $content = file_get_contents(public_path($filePath));
                foreach ($replacements as $placeholder => $value) {
                    $content = str_replace($placeholder, (string)$value, $content);
                }
                $text = $content;
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat preview: ' . $e->getMessage());
        }

        $downloadUrl = route('v1.attendance.downloadWarningLetter', ['studentId' => $studentId, 'type' => $type]);

        return view('attendance.showattendanceletter', [
            'student' => $student,
            'type' => $type,
            'letterName' => $letterName,
            'html' => $html,
            'text' => $text,
            'docxPreviewUnsupported' => $docxPreviewUnsupported,
            'replacements' => $replacements,
            'downloadUrl' => $downloadUrl,
        ]);
    }
}
