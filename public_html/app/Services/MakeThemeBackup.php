<?php

namespace App\Services;

use App\Theme;
use App\ThemeBackupSchedule;
use Carbon\Carbon;
use ZipArchive;

class MakeThemeBackup
{
    public $shop;
    public $themeId;
    public $themeName;

    public function __construct($shop, $themeId, $themeName)
    {
        $this->shop = $shop;
        $this->themeId = $themeId;
        $this->themeName = $themeName;
    }

    public static function autoMakeBackup(){
        $schedules = ThemeBackupSchedule::all();
        foreach ($schedules as $schedule) {
            if (strtotime(Carbon::now()) > (strtotime($schedule->updated_at) + 86400 * $schedule->interval)){
                $backup = new MakeThemeBackup($schedule->user, $schedule->theme_id, $schedule->theme_name);
                $backup->saveBackupToStorage();
                $schedule->touch();
            }
        }
    }

    public function saveBackupToStorage(){
        $shop = $this->shop;
        $id = $shop->id;
        $time = time();
        $theme = $shop->api()->rest('GET', '/admin/api/' . env('SHOPIFY_API_VERSION') . '/themes/' . $this->themeId . '/assets.json')['body']['assets'];
        $zipFileStoragePath = "$id/themes/$time.zip";
        $zipFileFullPath = storage_path() . "/app/$zipFileStoragePath";
        $zip = new ZipArchive;
        $zip->open($zipFileFullPath, ZipArchive::CREATE);
        foreach ($theme as $key => $asset) {
            $asset = $shop->api()->rest('GET', '/admin/api/' . env('SHOPIFY_API_VERSION') . '/themes/' . $this->themeId . '/assets.json', ['query' =>
                'asset[key]=' . $asset['key']])['body']['asset'];
            if (array_key_exists('attachment', $asset->container)){
                $assetContent = $asset['attachment'];
            } else {
                $assetContent = $asset['value']; }
                $zip->addFromString($asset['key'], $assetContent);
        }
        $zip->close();
        // chmod($zipFileFullPath, 33152);
        Theme::create([
            'user_id' => $id,
            'name' => $this->themeName,
            'path' => $zipFileStoragePath,
        ]);
    }
}
