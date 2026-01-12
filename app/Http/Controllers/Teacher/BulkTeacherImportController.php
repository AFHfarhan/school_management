<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Models\Teacher;
use OpenSpout\Reader\Common\Creator\ReaderFactory;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;

class BulkTeacherImportController extends Controller
{
    /**
     * Import teachers from CSV or Excel file
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

            $result = $this->processTeacherRow($rowData, $rowNumber, $errors);
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

                $result = $this->processTeacherRow($mappedData, $rowNumber, $errors);
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
     * Process a single teacher row
     */
    private function processTeacherRow($rowData, $rowNumber, &$errors)
    {
        try {
            // Validate required fields
            if (empty($rowData['name'])) {
                $errors[] = "Baris $rowNumber: Nama guru wajib diisi.";
                return ['success' => false, 'updated' => false];
            }

            if (empty($rowData['email'])) {
                $errors[] = "Baris $rowNumber: Email guru wajib diisi.";
                return ['success' => false, 'updated' => false];
            }

            // Validate email format
            if (!filter_var($rowData['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Baris $rowNumber: Format email tidak valid.";
                return ['success' => false, 'updated' => false];
            }

            // Build the JSON data structure
            $data = [
                'role' => $rowData['role'] ?? 'guru',
                'latest_login' => null,
                'isActive' => 1,
            ];

            // Handle password - use default if empty
            $password = !empty($rowData['password']) ? $rowData['password'] : 'password123';
            $hashedPassword = Hash::make($password);

            // Check if teacher exists with same email
            $existingTeacher = Teacher::where('email', $rowData['email'])->first();

            if ($existingTeacher) {
                // Update existing teacher
                $existingTeacher->name = $rowData['name'];
                $existingTeacher->password = $hashedPassword;
                $existingTeacher->data = $data;
                $existingTeacher->save();
                return ['success' => true, 'updated' => true];
            } else {
                // Create new teacher
                Teacher::create([
                    'name' => $rowData['name'],
                    'email' => $rowData['email'],
                    'password' => $hashedPassword,
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
        $successMessage = "Berhasil memproses $successCount akun guru";
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
                ->with('teacher_import_success', $successMessage);
        } elseif ($successCount > 0 && !empty($errors)) {
            return Redirect::route('v1.teacher.manage')
                ->with('teacher_import_success', $successMessage)
                ->with('teacher_import_errors', $errors);
        } else {
            return Redirect::route('v1.teacher.manage')
                ->with('teacher_import_error', 'Tidak ada akun guru yang berhasil diimport.')
                ->with('teacher_import_errors', $errors);
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
     * Download CSV template for bulk teacher import
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
            'email',
            'password',
            'role',
        ];

        // Example data
        $exampleData = [
            'Ahmad Fauzi',
            'ahmad.fauzi@sekolah.com',
            'password123',
            'guru',
        ];

        $fileName = 'template_import_guru_' . date('Y-m-d') . '.csv';

        // Create CSV file
        $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'template_guru_' . uniqid() . '.csv';
        
        $handle = fopen($tempFile, 'w');
        
        // Write header row
        fputcsv($handle, $headers);
        
        // Write example data row
        fputcsv($handle, $exampleData);
        
        fclose($handle);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
