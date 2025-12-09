<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Transaction;
use App\Models\Component;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions.
     */
    public function index()
    {
        $types = $this->getTransactionTypes();
        $transactions = Transaction::orderBy('created_at', 'desc')->limit(200)->get();
        return view('transaction.index', compact('transactions', 'types'));
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create(Request $request)
    {
        $types = $this->getTransactionTypes();
        $preselectedType = $request->query('type') ?: null;
        
        // Get transaction title from query parameter or fetch from component
        $transactionTitle = $this->getTransactionTitle($preselectedType);
        

        return view('transaction.create', compact('types', 'preselectedType', 'transactionTitle'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'data.payment_date' => 'required|string|max:255',
            'data.amount' => 'required|string|max:255',
            'data.amount_terbilang' => 'nullable|string|max:255',
            'data.payer' => 'required|string|max:255',
            'data.recipient' => 'required|string|max:255',
            'data.notes' => 'nullable|string',
        ];

        dd($request->all());

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $category = $request->input('category');
        $data = $request->input('data', []);
        
        // Add status and payment_date to data array
        $data['status'] = 'pending';

        $createdBy = Auth::guard('teacher')->id() ?? Auth::id() ?? null;

        $transaction = Transaction::create([
            'name' => $name,
            'data' => $data,
            'category' => $category,
            'created_by' => $createdBy,
            'updated_by' => null,
        ]);

        return redirect()->route('v1.transaction.show', $transaction->id)->with('success', 'Transaction created successfully');
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction)
    {
        return view('transaction.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction)
    {
        $types = $this->getTransactionTypes();
        return view('transaction.edit', compact('transaction', 'types'));
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'data.payment_date' => 'required|string',
            'data.amount' => 'required|string|max:255',
            'data.amount_terbilang' => 'nullable|string|max:255',
            'data.payer' => 'required|string|max:255',
            'data.recipient' => 'required|string|max:255',
            'data.notes' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->input('data', []);
        
        // Add status and payment_date to data array
        $data['status'] = $request->input('status', 'pending');

        $transaction->update([
            'name' => $request->input('name'),
            'category' => $request->input('category'),
            'data' => $data,
            'updated_by' => Auth::guard('teacher')->id() ?? Auth::id() ?? null,
        ]);

        return redirect()->route('v1.transaction.show', $transaction->id)->with('success', 'Transaction updated successfully');
    }

    /**
     * Get transaction types from component table
     */
    private function getTransactionTypes()
    {
        $component = Component::where('name', 'Title Formulir Pembayaran')->first();
        
        if ($component && !empty($component->data)) {
            $data = is_array($component->data) ? $component->data : json_decode($component->data, true);
            
            // If data is array of values, convert to key-value pairs
            if ($data && is_array($data)) {
                $types = [];
                foreach ($data as $item) {
                    // If item is an array with 'type' key
                    if (is_array($item) && isset($item['type']) && is_string($item['type'])) {
                        $type = $item['type'];
                        $types[strtolower($type)] = strtoupper($type);
                    }
                    // If item is a string
                    elseif (is_string($item)) {
                        $types[strtolower($item)] = strtoupper($item);
                    }
                }
                
                
                
                // Return types if not empty, otherwise fallback
                if (!empty($types)) {
                    return $types;
                }
            }
        }
        
        // Fallback to default types if component not found
        return ['ppdb' => '1', 'spp' => '2', 'other' => 'Other'];
    }

    /**
     * Get transaction title from component based on type
     */
    private function getTransactionTitle($type)
    {
        if (!$type) {
            return null;
        }
        
        // Get all components with category 'formulir pembayaran'
        $components = Component::where('name', 'Title Formulir Pembayaran')->get();
        
        foreach ($components as $component) {
            if (empty($component->data)) {
                continue;
            }
            
            $data = is_array($component->data) ? $component->data : json_decode($component->data, true);
            // If data is array, loop through items looking for matching type
            if (is_array($data)) {
                foreach ($data as $row) {
                    // Check if row is an array and has type key
                    if (is_array($row) && isset($row['type']) && isset($row['title'])) {
                        if ($row['type'] === $type) {
                            return $row['title'];
                        }
                    }
                }
            }
        }
        
        return null;
    }
}
