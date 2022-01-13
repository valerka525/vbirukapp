<?php

namespace App\Jobs;

use App\Services\ThemeBackup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteThemeBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 172800;
    protected $backup;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($backup)
    {
        $this->backup = $backup;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $backup = new ThemeBackup(null, null, $this->backup['id'], $this->backup['path']);
        $backup->deleteBackupFromStorage();
    }
}
