<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentAttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'class' => $request->input('class'),
            'semester' => $request->input('semester'),
        ];

        $attendances = $this->baseQuery($filters)->get();
        $availableColumns = $this->columns();
        $defaultColumns = $this->defaultColumns();
        $stats = $this->stats($attendances);

        return view('reports.student_attendance', [
            'attendances' => $attendances,
            'filters' => $filters,
            'availableColumns' => $availableColumns,
            'defaultColumns' => $defaultColumns,
            'stats' => $stats,
        ]);
    }

    public function export(Request $request)
    {
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'class' => $request->input('class'),
            'semester' => $request->input('semester'),
        ];

        $attendances = $this->baseQuery($filters)->get();
        $selectedColumns = array_values($request->input('columns', $this->defaultColumns()));

        if (empty($selectedColumns)) {
            $selectedColumns = $this->defaultColumns();
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Absen Siswa');

        $colIndex = 1;
        foreach ($selectedColumns as $column) {
            $sheet->setCellValueByColumnAndRow($colIndex, 1, $this->columns()[$column] ?? $column);
            $colIndex++;
        }

        $row = 2;
        foreach ($attendances as $attendance) {
            $colIndex = 1;
            foreach ($selectedColumns as $column) {
                $sheet->setCellValueByColumnAndRow($colIndex, $row, $this->valueFor($attendance, $column));
                $colIndex++;
            }
            $row++;
        }

        for ($i = 1; $i < $colIndex; $i++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_Absen_Siswa_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function baseQuery(array $filters)
    {
        $query = StudentAttendance::query()->orderByDesc('attendance_date');

        if (!empty($filters['start_date'])) {
            $query->whereDate('attendance_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('attendance_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['class'])) {
            $query->where('class', $filters['class']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        return $query;
    }

    private function columns(): array
    {
        return [
            'attendance_date' => 'Tanggal Absen',
            'dayOfWeek' => 'Hari',
            'class' => 'Kelas',
            'semester' => 'Semester',
            'entry_year' => 'Tahun Masuk',
            'student_count' => 'Total Siswa',
            'present_count' => 'Total Hadir',
            'absent_count' => 'Total Tidak Hadir',
            'absent_names' => 'Nama Tidak Hadir',
            'recorded_by' => 'Dicatat Oleh',
            'recorded_at' => 'Waktu Catat',
        ];
    }

    private function defaultColumns(): array
    {
        return [
            'attendance_date',
            'class',
            'semester',
            'student_count',
            'present_count',
            'absent_count',
            'recorded_by',
        ];
    }

    private function valueFor(StudentAttendance $attendance, string $column): string
    {
        $data = is_array($attendance->data) ? $attendance->data : [];
        $absent = $data['abstain'] ?? [];
        $studentCount = (int) ($data['student_count'] ?? 0);
        $absentCount = count($absent);
        $presentCount = max($studentCount - $absentCount, 0);

        return match ($column) {
            'attendance_date' => optional($attendance->attendance_date)->format('Y-m-d') ?? '',
            'dayOfWeek' => $attendance->dayOfWeek ?? '',
            'class' => $attendance->class ?? '',
            'semester' => $attendance->semester ?? '',
            'entry_year' => $attendance->entry_year ?? '',
            'student_count' => (string) $studentCount,
            'present_count' => (string) $presentCount,
            'absent_count' => (string) $absentCount,
            'absent_names' => collect($absent)->pluck('student_name')->implode(', '),
            'recorded_by' => $data['recorded_by'] ?? '',
            'recorded_at' => $data['recorded_at'] ?? '',
            default => '',
        };
    }

    private function stats($attendances): array
    {
        $total = $attendances->count();
        $absent = 0;
        $present = 0;

        foreach ($attendances as $attendance) {
            $data = is_array($attendance->data) ? $attendance->data : [];
            $studentCount = (int) ($data['student_count'] ?? 0);
            $absentCount = count($data['abstain'] ?? []);
            $presentCount = max($studentCount - $absentCount, 0);

            $absent += $absentCount;
            $present += $presentCount;
        }

        return [
            'records' => $total,
            'present' => $present,
            'absent' => $absent,
        ];
    }
}
