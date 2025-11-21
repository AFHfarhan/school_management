<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PruneLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-logs {days=120}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete logs older than specified days (default: 120 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->argument('days');
        $cutoff = Carbon::now()->subDays($days);
        $deleted = LogEntry::where('logged_at', '<', $cutoff)->delete();
        $this->info("Deleted {$deleted} log entries older than {$days} days.");
    }
}
