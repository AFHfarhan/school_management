<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\Student;
use OpenSpout\Reader\Common\Creator\ReaderFactory;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;

class BulkImportController extends Controller
{
    /**
     * Import students from CSV or Excel file
     */
    public function import(Request $request)
    {
        // Check if user is admin
        $user = Auth::guard('teacher')->user();
        $role = null;
        if ($user && !empty($user->data)) {
            $ud = is_array($user->data) ? $user->data : json_decode($user->data, true);
            $role = $ud['role'] ?? null;
        }
        if ($role !== 'admin') {
            abort(403);
        }

        // Friendly handling for legacy .xls uploads
        if ($request->hasFile('excel_file')) {
            $ext = strtolower($request->file('excel_file')->getClientOriginalExtension());
            if ($ext === 'xls') {
                return Redirect::route('v1.teacher.manage')
                    ->with('import_error', 'Format .xls (Excel 97-2003) tidak didukung. Silakan konversi ke .xlsx atau .ods, atau gunakan .csv.');
            }
        }

        $request->validate([
            'excel_file' => 'required|file|mimes:csv,txt,xlsx,ods|max:10240', // max 10MB
        ], [
            'excel_file.mimes' => 'Format file tidak didukung. Gunakan .xlsx, .csv, atau .ods.',
            'excel_file.max' => 'Ukuran file terlalu besar. Maksimum 10MB.',
            'excel_file.required' => 'Silakan pilih file untuk diunggah.',
        ]);

        try {
            $file = $request->file('excel_file');
            $extension = strtolower($file->getClientOriginalExtension());

            // Determine if CSV or Excel
            if (in_array($extension, ['csv', 'txt'])) {
                return $this->importCSV($file);
            } else {
                return $this->importExcel($file);
            }
        } catch (\Exception $e) {
            return Redirect::route('v1.teacher.manage')
                ->with('import_error', 'Error saat membaca file: ' . $e->getMessage());
        }
    }

    /**
     * Import from CSV file
     */
    private function importCSV($file)
    {
        $handle = fopen($file->getPathname(), 'r');

        if (!$handle) {
            return Redirect::route('v1.teacher.manage')
                ->with('import_error', 'Tidak dapat membuka file CSV.');
        }

        // Read header row
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return Redirect::route('v1.teacher.manage')
                ->with('import_error', 'File CSV kosong atau tidak valid.');
        }

        // Clean header
        $header = array_map('trim', $header);

        $successCount = 0;
        $updateCount = 0;
        $createCount = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Map row data to associative array
            $rowData = [];
            foreach ($header as $colIndex => $columnName) {
                $value = $row[$colIndex] ?? null;
                $rowData[$columnName] = $this->sanitizeCellValue($value);
            }

            $result = $this->processStudentRow($rowData, $rowNumber, $errors);
            if ($result['success']) {
                $successCount++;
                if ($result['updated']) {
                    $updateCount++;
                } else {
                    $createCount++;
                }
            }
        }

        fclose($handle);

        return $this->buildResponse($successCount, $createCount, $updateCount, $errors);
    }

    /**
     * Import from Excel/ODS file (.xlsx, .ods)
     */
    private function importExcel($file)
    {
        // Create reader using original filename (for correct extension detection),
        // but open using the temporary file path.
        $reader = ReaderFactory::createFromFile($file->getClientOriginalName());
        $reader->open($file->getPathname());

        $header = null;
        $successCount = 0;
        $updateCount = 0;
        $createCount = 0;
        $errors = [];
        $rowNumber = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rowNumber++;
                $rowData = $row->toArray();

                // First row is header
                if ($rowNumber === 1) {
                    $header = array_map('trim', $rowData);
                    continue;
                }

                // Skip empty rows
                if (empty(array_filter($rowData))) {
                    continue;
                }

                // Map row data to associative array
                $mappedData = [];
                foreach ($header as $colIndex => $columnName) {
                    $value = $rowData[$colIndex] ?? null;
                    $mappedData[$columnName] = $this->sanitizeCellValue($value);
                }

                $result = $this->processStudentRow($mappedData, $rowNumber, $errors);
                if ($result['success']) {
                    $successCount++;
                    if ($result['updated']) {
                        $updateCount++;
                    } else {
                        $createCount++;
                    }
                }
            }
        }

        $reader->close();

        return $this->buildResponse($successCount, $createCount, $updateCount, $errors);
    }

    /**
     * Process a single student row
     */
    private function processStudentRow($rowData, $rowNumber, &$errors)
    {
        try {
            // Validate required field
            if (empty($rowData['name'])) {
                $errors[] = "Baris $rowNumber: Nama siswa wajib diisi.";
                return ['success' => false, 'updated' => false];
            }

            // Build the JSON data structure
            $data = [
                'age' => !empty($rowData['age']) ? (int)$rowData['age'] : null,
                'gender' => $rowData['gender'] ?? null,
                'contact' => [
                    'phone' => $rowData['phone'] ?? null,
                    'email' => $rowData['email'] ?? null,
                    'address' => $rowData['address'] ?? null,
                    'emergency_contact' => $rowData['emergency_contact'] ?? null,
                    'parent_name' => $rowData['parent_name'] ?? null,
                ],
                'academic' => [
                    'grade' => $rowData['grade'] ?? null,
                    'class' => $rowData['class'] ?? null,
                    'student_id' => $rowData['student_id'] ?? null,
                    'major' => $rowData['major'] ?? null,
                    'entry_year' => !empty($rowData['entry_year']) ? (int)$rowData['entry_year'] : null,
                    'period' => $rowData['period'] ?? null,
                ],
                'biodata' => [
                    'nisn' => $rowData['nisn'] ?? null,
                    'nik' => $rowData['nik'] ?? null,
                    'birth_place' => $rowData['birth_place'] ?? null,
                    'birth_date' => $rowData['birth_date'] ?? null,
                    'religion' => $rowData['religion'] ?? null,
                    'blood_type' => $rowData['blood_type'] ?? null,
                    'height' => !empty($rowData['height']) ? (int)$rowData['height'] : null,
                    'weight' => !empty($rowData['weight']) ? (int)$rowData['weight'] : null,
                    'hobbies' => $this->parseArrayField($rowData['hobbies'] ?? null),
                    'achievements' => $this->parseArrayField($rowData['achievements'] ?? null),
                    'absent' => [],
                ],
            ];

            // Check if student exists with same name, gender, phone, and email
            $existingStudent = null;
            $phone = $rowData['phone'] ?? null;
            $gender = $rowData['gender'] ?? null;
            $email = $rowData['email'] ?? null;

            if ($phone && $gender && $email) {
                // Find students with matching name
                $students = Student::where('name', $rowData['name'])->get();

                foreach ($students as $student) {
                    $studentData = is_array($student->data) ? $student->data : json_decode($student->data, true) ?? [];
                    $studentPhone = $studentData['contact']['phone'] ?? null;
                    $studentGender = $studentData['gender'] ?? null;
                    $studentEmail = $studentData['contact']['email'] ?? null;

                    // Match if name, gender, phone, and email all match
                    if ($studentPhone === $phone && $studentGender === $gender && $studentEmail === $email) {
                        $existingStudent = $student;
                        break;
                    }
                }
            }

            if ($existingStudent) {
                // Update existing student
                $existingStudent->name = $rowData['name'];
                $existingStudent->data = $data;
                $existingStudent->save();
                return ['success' => true, 'updated' => true];
            } else {
                // Create new student
                Student::create([
                    'name' => $rowData['name'],
                    'data' => $data,
                ]);
                return ['success' => true, 'updated' => false];
            }

        } catch (\Exception $e) {
            $errors[] = "Baris $rowNumber: " . $e->getMessage();
            return ['success' => false, 'updated' => false];
        }
    }

    /**
     * Build response with success/error messages
     */
    private function buildResponse($successCount, $createCount, $updateCount, $errors)
    {
        // Build success message
        $successMessage = "Berhasil memproses $successCount siswa";
        if ($createCount > 0 && $updateCount > 0) {
            $successMessage .= " ($createCount baru, $updateCount diperbarui)";
        } elseif ($createCount > 0) {
            $successMessage .= " (semua baru)";
        } elseif ($updateCount > 0) {
            $successMessage .= " (semua diperbarui)";
        }
        $successMessage .= ".";

        if ($successCount > 0 && empty($errors)) {
            return Redirect::route('v1.teacher.manage')
                ->with('import_success', $successMessage);
        } elseif ($successCount > 0 && !empty($errors)) {
            return Redirect::route('v1.teacher.manage')
                ->with('import_success', $successMessage)
                ->with('import_errors', $errors);
        } else {
            return Redirect::route('v1.teacher.manage')
                ->with('import_error', 'Tidak ada siswa yang berhasil diimport.')
                ->with('import_errors', $errors);
        }
    }

    /**
     * Sanitize cell value to string, handling DateTimeImmutable and other types
     */
    private function sanitizeCellValue($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Handle DateTimeImmutable objects from Excel date cells
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        // Convert to string and trim
        $stringValue = trim((string)$value);
        
        return $stringValue !== '' ? $stringValue : null;
    }

    /**
     * Parse comma-separated string into array (for hobbies, achievements)
     */
    private function parseArrayField($value)
    {
        if (empty($value)) {
            return [];
        }

        if (is_string($value)) {
            // Split by semicolon to avoid conflicts with commas in the CSV
            // Users should use semicolon to separate array items
            return array_filter(array_map('trim', explode(';', $value)));
        }

        return [];
    }

    /**
        * Download CSV template for bulk import
     */
    public function downloadTemplate()
    {
        // Check if user is admin
        $user = Auth::guard('teacher')->user();
        $role = null;
        if ($user && !empty($user->data)) {
            $ud = is_array($user->data) ? $user->data : json_decode($user->data, true);
            $role = $ud['role'] ?? null;
        }
        if ($role !== 'admin') {
            abort(403);
        }

        // Define headers
        $headers = [
            'name',
            'age',
            'gender',
            'phone',
            'email',
            'address',
            'emergency_contact',
            'parent_name',
            'grade',
            'class',
            'student_id',
            'major',
            'entry_year',
            'period',
            'nisn',
            'nik',
            'birth_place',
            'birth_date',
            'religion',
            'blood_type',
            'height',
            'weight',
            'hobbies',
            'achievements',
        ];

        // Example data
        $exampleData = [
            'Siti Nurhaliza',
            15,
            'female',
            '082345678901',
            'siti.nurhaliza@student.com',
            'Jl. Sudirman No. 45, Jakarta Selatan',
            '082345678902',
            'Ibu Haliza',
            '10',
            'X TKJ 1',
            'SMK2025002',
            'Teknik Komputer dan Jaringan',
            2025,
            'Semester Ganjil',
            '0051234568',
            '3175012345670002',
            'Bandung',
            '2009-08-20',
            'Islam',
            'B',
            158,
            48,
            'Design;Photography;Music',
            'Juara 2 Design Competition 2024',
        ];

        $fileName = 'template_import_siswa_' . date('Y-m-d') . '.csv';

        // Create CSV file
        $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'template_siswa_' . uniqid() . '.csv';
        
        $handle = fopen($tempFile, 'w');
        
        // Write header row
        fputcsv($handle, $headers);
        
        // Write example data row
        fputcsv($handle, $exampleData);
        
        fclose($handle);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
