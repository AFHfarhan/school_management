<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction_table';

    protected $fillable = [
        'name',
        'data',
        'additional_data',
        'category',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'data' => 'array',
        'additional_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $timestamps = true;

    /**
     * Get the teacher who created this transaction.
     */
    public function creator()
    {
        return $this->belongsTo(Teacher::class, 'created_by');
    }

    /**
     * Get the teacher who last updated this transaction.
     */
    public function updater()
    {
        return $this->belongsTo(Teacher::class, 'updated_by');
    }
}
