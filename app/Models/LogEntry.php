<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogEntry extends Model
{
    protected $table = 'logs';

    protected $fillable = [
        'level', 'channel', 'message', 'context', 'user_id', 'ip', 'path', 'logged_at'
    ];

    protected $casts = [
        'context' => 'array',
        'logged_at' => 'datetime',
    ];
}
