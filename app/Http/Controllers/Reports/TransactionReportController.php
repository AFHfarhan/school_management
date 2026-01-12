<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TransactionReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'category' => $request->input('category'),
            'status' => $request->input('status'),
        ];

        $transactions = $this->baseQuery($filters)->get();
        $availableColumns = $this->columns();
        $defaultColumns = $this->defaultColumns();

        $totals = $this->calculateTotals($transactions);

        return view('reports.transactions', [
            'transactions' => $transactions,
            'filters' => $filters,
            'availableColumns' => $availableColumns,
            'defaultColumns' => $defaultColumns,
            'totals' => $totals,
        ]);
    }

    public function export(Request $request)
    {
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'category' => $request->input('category'),
            'status' => $request->input('status'),
        ];

        $transactions = $this->baseQuery($filters)->get();
        $selectedColumns = array_values($request->input('columns', $this->defaultColumns()));

        if (empty($selectedColumns)) {
            $selectedColumns = $this->defaultColumns();
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pembayaran');

        $colIndex = 1;
        foreach ($selectedColumns as $column) {
            $sheet->setCellValueByColumnAndRow($colIndex, 1, $this->columns()[$column] ?? $column);
            $colIndex++;
        }

        $row = 2;
        foreach ($transactions as $transaction) {
            $colIndex = 1;
            foreach ($selectedColumns as $column) {
                $sheet->setCellValueByColumnAndRow($colIndex, $row, $this->valueFor($transaction, $column));
                $colIndex++;
            }
            $row++;
        }

        for ($i = 1; $i < $colIndex; $i++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_Pembayaran_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function baseQuery(array $filters)
    {
        $query = Transaction::query()->orderByDesc('created_at');

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['status'])) {
            $query->where('data->status', $filters['status']);
        }

        return $query;
    }

    private function columns(): array
    {
        return [
            'name' => 'Judul Transaksi',
            'category' => 'Kategori',
            'data.amount' => 'Total Pembayaran',
            'data.payer' => 'Pembayar',
            'data.recipient' => 'Penerima',
            'data.payment_date' => 'Tanggal Bayar',
            'data.status' => 'Status',
            'data.notes' => 'Catatan',
            'created_at' => 'Tanggal Dibuat',
            'updated_at' => 'Terakhir Diubah',
        ];
    }

    private function defaultColumns(): array
    {
        return [
            'name',
            'category',
            'data.amount',
            'data.payer',
            'data.payment_date',
            'data.status',
            'created_at',
        ];
    }

    private function valueFor(Transaction $transaction, string $column): string
    {
        if ($column === 'name') {
            return $transaction->name ?? '';
        }

        if ($column === 'category') {
            return $transaction->category ?? '';
        }

        if (in_array($column, ['created_at', 'updated_at'], true)) {
            return optional($transaction->{$column})->format('Y-m-d H:i:s') ?? '';
        }

        $data = is_array($transaction->data) ? $transaction->data : [];
        $path = explode('.', $column);
        $value = $data;

        foreach ($path as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return '';
            }
            $value = $value[$segment];
        }

        if (is_numeric($value) && $path[1] === 'amount') {
            return (string) $value;
        }

        return is_array($value) ? json_encode($value) : (string) $value;
    }

    private function calculateTotals($transactions): array
    {
        $totalAmount = 0;
        $paidAmount = 0;

        foreach ($transactions as $transaction) {
            $data = is_array($transaction->data) ? $transaction->data : [];
            $amount = (float) ($data['amount'] ?? 0);
            $totalAmount += $amount;

            if (($data['status'] ?? 'pending') === 'paid') {
                $paidAmount += $amount;
            }
        }

        return [
            'total' => $totalAmount,
            'paid' => $paidAmount,
        ];
    }
}
