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
}
