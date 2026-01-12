<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\TeacherAttendance;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TeacherAttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'teacher_name' => $request->input('teacher_name'),
            'semester' => $request->input('semester'),
        ];

        $attendances = $this->baseQuery($filters)->get();
        $availableColumns = $this->columns();
        $defaultColumns = $this->defaultColumns();

        return view('reports.teacher_attendance', [
            'attendances' => $attendances,
            'filters' => $filters,
            'availableColumns' => $availableColumns,
            'defaultColumns' => $defaultColumns,
        ]);
    }

    public function export(Request $request)
    {
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'teacher_name' => $request->input('teacher_name'),
            'semester' => $request->input('semester'),
        ];

        $attendances = $this->baseQuery($filters)->get();
        $selectedColumns = array_values($request->input('columns', $this->defaultColumns()));

        if (empty($selectedColumns)) {
            $selectedColumns = $this->defaultColumns();
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Absen Guru');

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
        $fileName = 'Laporan_Absen_Guru_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function baseQuery(array $filters)
    {
        $query = TeacherAttendance::query()->orderByDesc('attendance_date');

        if (!empty($filters['start_date'])) {
            $query->whereDate('attendance_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('attendance_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['teacher_name'])) {
            $query->where('teacher_name', 'like', '%' . $filters['teacher_name'] . '%');
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        return $query;
    }

    private function columns(): array
    {
        return [
            'teacher_name' => 'Nama Guru',
            'attendance_date' => 'Tanggal Absen',
            'tahun_ajaran' => 'Tahun Ajaran',
            'semester' => 'Semester',
            'attend_status' => 'Status Kehadiran',
            'attend_notes' => 'Catatan',
            'total_classes' => 'Jumlah Jadwal',
            'recorded_by' => 'Dicatat Oleh',
            'recorded_at' => 'Waktu Catat',
        ];
    }

    private function defaultColumns(): array
    {
        return [
            'teacher_name',
            'attendance_date',
            'semester',
            'attend_status',
            'total_classes',
            'recorded_by',
        ];
    }

    private function valueFor(TeacherAttendance $attendance, string $column): string
    {
        $data = is_array($attendance->data) ? $attendance->data : [];
        $schedule = $data['schedule'] ?? [];

        return match ($column) {
            'teacher_name' => $attendance->teacher_name ?? '',
            'attendance_date' => optional($attendance->attendance_date)->format('Y-m-d') ?? '',
            'tahun_ajaran' => $attendance->tahun_ajaran ?? '',
            'semester' => $attendance->semester ?? '',
            'attend_status' => $data['attend_status'] ?? '',
            'attend_notes' => $data['attend_notes'] ?? '',
            'total_classes' => (string) (is_array($schedule) ? count($schedule) : 0),
            'recorded_by' => $data['recorded_by'] ?? '',
            'recorded_at' => $data['recorded_at'] ?? '',
            default => '',
        };
    }
}
