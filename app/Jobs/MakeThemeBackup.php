<?php

namespace App\Jobs;

use App\Services\ThemeBackup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MakeThemeBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 172800;
    protected $themeName;
    protected $themeId;
    protected $shop;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($themeName, $themeId, $shop)
    {
        $this->themeName = $themeName;
        $this->themeId = $themeId;
        $this->shop = $shop;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $backup = new ThemeBackup($this->themeName, $this->themeId, null, null, $this->shop);
        $backup->saveBackupToStorage();
    }
}
