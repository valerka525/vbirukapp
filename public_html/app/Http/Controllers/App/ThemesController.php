<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Services\MakeThemeBackup;
use App\Theme;
use App\ThemeBackupSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ThemesController extends Controller
{
    protected static function home()
    {
        $shop = Auth::user();
        $backups = $shop->themes;
        $schedules = $shop->schedules;
        $themes = $shop->api()->rest('GET', '/admin/api/' . env('SHOPIFY_API_VERSION') . '/themes.json')['body']['themes'];
        return view('home.home',
            [
                'themes' => $themes,
                'backups' => $backups,
                'schedules' => $schedules,
            ]);
    }
    protected static function makeBackup($themeId, $themeName)
    {
        $shop = Auth::user();
        $backup = new MakeThemeBackup($shop, $themeId, $themeName);
        $backup->saveBackupToStorage();
        return redirect()->route('home')->with('success', 'Backup has been created!')->with('show', 'backups');
    }

    protected static function restoreBackup(Theme $backup){
        $shop = Auth::user();
        /* После теста на сервере нужно будет реализовать генерацию URL для приватных файлов так как архив не будет
        отдаваться шопифаю */
        $archive = Storage::url($backup['path']);
        // Временное решение для теста публикации бекапа на локальном энвиронменте
        $archive = str_replace('vbirukapp', 'd1dab4690de6.ngrok.io', $archive);
        $shop->api()->rest('POST', '/admin/api/' . env('SHOPIFY_API_VERSION') . '/themes.json', ['theme' =>
            ['name' => "$backup[name] $backup[created_at]", 'src' => $archive, 'role' => 'main']]);
        return redirect()->route('home')->with('success', 'Backup has been published to your store!')->with('show', 'themes');
    }

    protected static function themeDelete(Theme $backup)
    {
        Storage::delete($backup['path']);
        Theme::where('id', $backup['id'])->delete();
        return redirect()->route('home')->with('warning', 'Backup has been deleted!')->with('show', 'backups');
    }

    protected static function addSchedule(Request $request){
        $id = Auth::user()->id;
        $theme = preg_split("#/#", $request['theme']);
        ThemeBackupSchedule::create([
            'user_id' => $id,
            'theme_id' => $theme[0],
            'theme_name' => $theme[1],
            'interval' => $request['interval'],
        ]);
        return redirect()->route('home')->with('success', 'Schedule has been created!')->with('show', 'schedules');
    }
    protected static function deleteSchedule($id){
        ThemeBackupSchedule::where('id', $id)->delete();
        return redirect()->route('home')->with('warning', 'Schedule has been deleted!')->with('show', 'schedules');
    }
}
