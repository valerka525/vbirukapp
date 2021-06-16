<?php

namespace App\Services;

use App\Theme;
use App\ThemeBackupSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ThemeBackup
{
    private $themeName;
    private $themeId;
    private $backupId;
    private $backupPath;
    private $shop;

    public function __construct($themeName = null, $themeId = null, $backupId = null, $backupPath = null, $shop = null)
    {
        $this->themeName = $themeName;
        $this->themeId = $themeId;
        $this->backupId = $backupId;
        $this->backupPath = $backupPath;
        if (is_null($shop)) {
            $this->shop = Auth::user();
        }
    }

    public static function autoMakeBackup()
    {
        $schedules = ThemeBackupSchedule::all();
        foreach ($schedules as $schedule) {
            if (strtotime(Carbon::now()) > (strtotime($schedule->updated_at) + 86400 * $schedule->interval)) {
                $backup = new ThemeBackup($schedule->theme_name, $schedule->theme_id, null,
                    null, $schedule->user);
                $backup->saveBackupToStorage();
                $schedule->touch();
            }
        }
    }

    public function saveBackupToStorage()
    {
        $shop = $this->shop;
        $id = $shop->id;
        $backupsCount = $shop->themes()->count();
        $time = time();
        $theme = $shop->api()->rest('GET', '/admin/api/' . env('SHOPIFY_API_VERSION') . '/themes/' . $this->themeId
            . '/assets.json')['body']['assets'];
        $zipFileStoragePath = "$id/themes/$time.zip";
        $zipFileFullPath = storage_path() . "/app/$zipFileStoragePath";
        $zip = new ZipArchive;
        $zip->open($zipFileFullPath, ZipArchive::CREATE);
        foreach ($theme as $key => $asset) {
            $asset = $shop->api()->rest('GET', '/admin/api/' . env('SHOPIFY_API_VERSION') . '/themes/'
                . $this->themeId . '/assets.json', ['query' =>
                'asset[key]=' . $asset['key']])['body']['asset'];
            if (array_key_exists('attachment', $asset->container)) {
                $assetContent = $asset['attachment'];
            } else {
                $assetContent = $asset['value'];
            }
            $zip->addFromString($asset['key'], $assetContent);
        }
        $zip->close();
        // chmod($zipFileFullPath, 33152);
        Theme::create([
            'user_id' => $id,
            'name' => $this->themeName,
            'path' => $zipFileStoragePath,
        ]);
        if ($backupsCount >= 10) {
            $oldestBackup = $shop->themes()->first();
            $excessBackup = new self(null, null, $oldestBackup['id'], $oldestBackup['
            path']);
            $excessBackup->deleteBackupFromStorage();
        }
    }

    public function restoreBackupFromStorage()
    {
        /* После теста на сервере нужно будет реализовать генерацию URL для приватных файлов так как архив не будет
        отдаваться шопифаю */
        $archive = Storage::url($this->backupPath);
        // Временное решение для теста публикации бекапа на локальном окружении
        $archive = str_replace('vbirukapp', 'b5a1a13d570f.ngrok.io', $archive);
        $put = $this->shop->api()->rest('POST', '/admin/api/' . env('SHOPIFY_API_VERSION') . '/themes.json',
            ['theme' => ['name' => $this->themeName, 'src' => $archive, 'role' => 'main']]);
        if (!$put['errors']) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteBackupFromStorage()
    {
        Storage::delete($this->backupPath);
        Theme::where('id', $this->backupId)->delete();
    }
}
