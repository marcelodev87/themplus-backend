<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BackupLaravelLog extends Command
{
    protected $signature = 'log:backup';
    protected $description = 'Cria backup do laravel.log com data e limpa o arquivo original';

    public function handle()
    {
        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            $this->error('laravel.log não encontrado.');
            return Command::FAILURE;
        }

        $date = Carbon::now()->format('Y-m-d');
        $backupPath = storage_path("logs/{$date}.log");

        File::copy($logPath, $backupPath);

        File::put($logPath, '');

        $this->info("Backup criado em: {$backupPath}");
        $this->info("laravel.log foi limpo.");

        return Command::SUCCESS;
    }
}
