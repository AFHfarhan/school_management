<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'grade' => $request->input('grade'),
            'program' => $request->input('program'),
        ];

        $students = $this->baseQuery($filters)->get();
        $availableColumns = $this->columns();
        $defaultColumns = $this->defaultColumns();

        return view('reports.students', [
            'students' => $students,
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
            'grade' => $request->input('grade'),
            'program' => $request->input('program'),
        ];

        $students = $this->baseQuery($filters)->get();
        $selectedColumns = array_values($request->input('columns', $this->defaultColumns()));

        if (empty($selectedColumns)) {
            $selectedColumns = $this->defaultColumns();
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan PPDB');

        $colIndex = 1;
        foreach ($selectedColumns as $column) {
            $sheet->setCellValueByColumnAndRow($colIndex, 1, $this->columns()[$column] ?? $column);
            $colIndex++;
        }

        $row = 2;
        foreach ($students as $student) {
            $colIndex = 1;
            foreach ($selectedColumns as $column) {
                $sheet->setCellValueByColumnAndRow($colIndex, $row, $this->valueFor($student, $column));
                $colIndex++;
            }
            $row++;
        }

        for ($i = 1; $i < $colIndex; $i++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_PPDB_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function baseQuery(array $filters)
    {
        $query = Student::query()->orderByDesc('created_at');

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        if (!empty($filters['grade'])) {
            $query->where('data->grade', $filters['grade']);
        }

        if (!empty($filters['program'])) {
            $query->where('data->program', $filters['program']);
        }

        return $query;
    }

    private function columns(): array
    {
        return [
            'name' => 'Nama Lengkap',
            'data.form_date' => 'Tanggal Formulir',
            'data.form_reg' => 'Nomor Registrasi',
            'data.grade' => 'Kelas/Grade',
            'data.program' => 'Program/Jurusan',
            'data.personal.birthplace' => 'Tempat Lahir',
            'data.personal.birthdate' => 'Tanggal Lahir',
            'data.personal.gender' => 'Jenis Kelamin',
            'data.personal.religion' => 'Agama',
            'data.personal.address' => 'Alamat',
            'data.personal.phone' => 'Telepon',
            'data.personal.email' => 'Email',
            'data.parent.dad.name' => 'Nama Ayah',
            'data.parent.mom.name' => 'Nama Ibu',
            'created_at' => 'Tanggal Dibuat',
        ];
    }

    private function defaultColumns(): array
    {
        return [
            'name',
            'data.form_date',
            'data.form_reg',
            'data.grade',
            'data.program',
            'created_at',
        ];
    }

    private function valueFor(Student $student, string $column): string
    {
        if ($column === 'name') {
            return $student->name ?? '';
        }

        if ($column === 'created_at') {
            return optional($student->created_at)->format('Y-m-d H:i:s') ?? '';
        }

        $data = is_array($student->data) ? $student->data : [];

        $path = explode('.', $column);
        $value = $data;

        foreach ($path as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return '';
            }
            $value = $value[$segment];
        }

        return is_array($value) ? json_encode($value) : (string) $value;
    }
}
