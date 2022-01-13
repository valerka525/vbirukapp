<?php

namespace App\Jobs;

use App\Services\ThemeBackup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RestoreThemeBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 172800;
    protected $backup;
    protected $shop;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($backup, $shop)
    {
        $this->backup = $backup;
        $this->shop = $shop;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $backup = new ThemeBackup(
            $this->backup['name'],
            null,
            null,
            $this->backup['path'],
            $this->shop,
            $this->backup['created_at']
        );
        $backup->restoreBackupFromStorage();
        /*
        $result = ($backup->restoreBackupFromStorage()) ? [
            'type' => 'success',
            'message' => __('flashes.backup_published')
        ] : [
            'type' => 'warning',
            'message' => __('flashes.went_wrong')
        ];
        */
    }
}
