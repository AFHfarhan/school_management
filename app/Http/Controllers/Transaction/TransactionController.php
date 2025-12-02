<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Transaction;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions.
     */
    public function index()
    {
        $types = ['ppdb' => 'PPDB', 'spp' => 'SPP', 'other' => 'Other'];
        $transactions = Transaction::orderBy('created_at', 'desc')->limit(200)->get();
        return view('transaction.index', compact('transactions', 'types'));
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create(Request $request)
    {
        $types = ['ppdb' => 'PPDB', 'spp' => 'SPP', 'other' => 'Other'];
        $preselectedType = $request->query('type') ?: null;
        // ensure preselected is valid
        if ($preselectedType && ! array_key_exists($preselectedType, $types)) {
            $preselectedType = null;
        }
        return view('transaction.create', compact('types', 'preselectedType'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'category' => 'nullable|string|max:100',
            'status' => 'nullable|in:pending,paid,failed',
            'payment_date' => 'nullable|date',
            'data.amount' => 'nullable|numeric',
            'data.payer' => 'nullable|string|max:255',
            'additional_data' => 'nullable|string',
            'data.notes' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $name = $request->input('name');
        $type = $request->input('type');
        $category = $request->input('category');
        $status = $request->input('status') ?: 'pending';
        $paymentDate = $request->input('payment_date');
        $data = $request->input('data', []);
        $additional = $request->input('additional_data');

        $createdBy = Auth::guard('teacher')->id() ?? Auth::id() ?? null;

        // normalize additional_data: if valid JSON string, decode, otherwise store as array with note
        $additionalData = null;
        if ($additional !== null) {
            if (is_string($additional)) {
                $decoded = json_decode($additional, true);
                $additionalData = $decoded === null ? ['note' => $additional] : $decoded;
            } else {
                $additionalData = $additional;
            }
        }

        $transaction = Transaction::create([
            'name' => $name,
            'data' => $data,
            'additional_data' => $additionalData,
            'category' => $category,
            'created_by' => $createdBy,
            'updated_by' => null,
        ]);

        return redirect()->route('v1.transaction.index')->with('success', 'Transaction created');
    }
}
