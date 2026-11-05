<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestCron extends Command
{
    protected $signature = 'cron:test';
    protected $description = 'Simple cron test';

    public function handle()
    {
        Log::info('Test cron executed at ' . now());
        $this->info('Test cron executed');
    }
}
