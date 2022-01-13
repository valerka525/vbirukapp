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
    private $createdAt;

    public function __construct(
        $themeName = null,
        $themeId = null,
        $backupId = null,
        $backupPath = null,
        $shop = null,
        $createdAt = null
    ) {
        $this->themeName = $themeName;
        $this->themeId = $themeId;
        $this->backupId = $backupId;
        $this->backupPath = $backupPath;
        $this->shop = (is_null($shop)) ? Auth::user() : $shop; //
        $this->createdAt = $createdAt;
    }

    public static function autoMakeBackup()
    {
        $schedules = ThemeBackupSchedule::all();
        foreach ($schedules as $schedule) {
            if (strtotime(Carbon::now()) > (strtotime($schedule->updated_at) + 86400 * $schedule->interval)) {
                $backup = new self(
                    $schedule->theme_name, $schedule->theme_id, null, null, $schedule->user
                );
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
        $zipFileStoragePath = "$id/themes/$time.zip";
        $zipFileFullPath = storage_path() . "/app/$zipFileStoragePath";
        if (Storage::missing("$id/themes")) {
            Storage::makeDirectory("$id/themes");
        }
        $zip = new ZipArchive();
        $zip->open($zipFileFullPath, ZipArchive::CREATE);
        $theme = $shop->api()->rest(
            'GET',
            '/admin/api/' . env('SHOPIFY_API_VERSION') . '/themes/' . $this->themeId . '/assets.json')['body']['assets'];
        foreach ($theme as $key => $asset) {
            $asset = $shop->api()->rest(
                'GET',
                '/admin/api/' . env('SHOPIFY_API_VERSION') . '/themes/' . $this->themeId . '/assets.json',
                ['query' => 'asset[key]=' . $asset['key']]
            )['body']['asset'];
            if (array_key_exists('attachment', $asset->container)) {
                $assetContent = $asset['attachment'];
            } else {
                $assetContent = $asset['value'];
            }
            $zip->addFromString($asset['key'], $assetContent);
        }
        $zip->close();
        Theme::create(
            [
                'user_id' => $id,
                'name' => $this->themeName,
                'path' => $zipFileStoragePath,
            ]
        );
        if ($backupsCount >= 10) {
            $oldestBackup = $shop->themes()->first();
            $excessBackup = new self(null, null, $oldestBackup['id'], $oldestBackup['path']);
            $excessBackup->deleteBackupFromStorage();
        }
    }

    public function restoreBackupFromStorage()
    {
        $privateArchive = Storage::path($this->backupPath);
        $publicArchive = md5_file($privateArchive) . '.zip';
        Storage::copy($this->backupPath, "/public/$publicArchive");
        $url = Storage::disk('public')->url($publicArchive);
        $put = $this->shop->api()->rest(
            'POST',
            '/admin/api/' . env('SHOPIFY_API_VERSION') . '/themes.json',
            ['theme' => ['name' => "$this->themeName $this->createdAt", 'src' => $url, 'role' => 'main']]
        );
        Storage::disk('public')->delete($publicArchive);
        if (!$put['errors']) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteBackupFromStorage()
    {
        Storage::delete($this->backupPath);
        Theme::destroy($this->backupId);
    }
}
