<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Jobs\PersistLogJob;

class LogController extends Controller
{
    public function info(string $message, array $context = [])
    {
        Log::info($message, $context);
        $this->dispatchToDb('info', $message, $context);
    }

    public function warning(string $message, array $context = [])
    {
        Log::warning($message, $context);
        $this->dispatchToDb('warning', $message, $context);
    }

    public function error(string $message, array $context = [])
    {
        Log::error($message, $context);
        $this->dispatchToDb('error', $message, $context);
    }

    protected function dispatchToDb(string $level, string $message, array $context)
    {
        PersistLogJob::dispatch($level, $message, $context);
    }
}
