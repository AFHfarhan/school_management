<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\LogEntry;
use Illuminate\Support\Facades\Auth;

class PersistLogJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $level;
    public $message;
    public $context;

    /**
     * Create a new job instance.
     */
    public function __construct($level, $message, $context = [])
    {
        $this->level = $level;
        $this->message = $message;
        $this->context = $context;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = Auth::check() ? Auth::id() : null;
        LogEntry::create([
            'level' => $this->level,
            'channel' => 'app',
            'message' => $this->message,
            'context' => $this->context,
            'user_id' => $user,
            'ip' => $this->context['ip'] ?? request()->ip() ?? null,
            'path' => $this->context['path'] ?? request()->path() ?? null,
            'logged_at' => now(),
        ]);
    }
}
