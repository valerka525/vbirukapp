<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Services\ThemeBackup;
use App\Theme;
use App\ThemeBackupSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThemesController extends Controller
{
    protected static function home()
    {
        $shop = Auth::user();
        $backups = $shop->themes;
        $schedules = $shop->schedules;
        $themes = $shop->api()->rest('GET', '/admin/api/' . env('SHOPIFY_API_VERSION') . '/themes.json')['body']
        ['themes'];
        return view('home.home',
            [
                'themes' => $themes,
                'backups' => $backups,
                'schedules' => $schedules,
            ]);
    }

    protected static function makeBackup($themeId, $themeName)
    {
        $backup = new ThemeBackup($themeName, $themeId);
        $backup->saveBackupToStorage();
        return redirect()->route('home')->with('success',
            __('flashes.backup_created'))->with('show', 'backups');
    }

    protected static function restoreBackup(Theme $backup)
    {
        $backup = new ThemeBackup($backup['name'], null, null, $backup['path']);
        $result = ($backup->restoreBackupFromStorage()) ? ['type' => 'success', 'message' =>
            __('flashes.backup_published')] : ['type' => 'warning', 'message' => __('flashes.went_wrong')];
        return redirect()->route('home')->with($result['type'], $result['message'])->with('show', 'themes');
    }

    protected static function deleteBackup(Theme $backup)
    {
        $backup = new ThemeBackup(null, null, $backup['id'], $backup['path'], null);
        $backup->deleteBackupFromStorage();
        return redirect()->route('home')->with('warning',
            __('flashes.backup_deleted'))->with('show', 'backups');
    }

    protected static function addSchedule(Request $request)
    {
        $shop = Auth::user();
        $schedulesCount = $shop->schedules()->count();
        if ($schedulesCount >= 3) {
            return redirect()->route('home')->with('warning', __('flashes.schedules_limit'))->with('show', 'schedules');
        }
        $user_id = $shop->id;
        $theme = preg_split("#/#", $request['theme']);
        ThemeBackupSchedule::create([
            'user_id' => $user_id,
            'theme_id' => $theme[0],
            'theme_name' => $theme[1],
            'interval' => $request['interval'],
        ]);
        return redirect()->route('home')->with('success', __('flashes.schedule_created'))->with('show', 'schedules');
    }

    protected static function deleteSchedule($id)
    {
        ThemeBackupSchedule::where('id', $id)->delete();
        return redirect()->route('home')->with('warning', __('flashes.schedule_deleted'))->with('show', 'schedules');
    }
}

/*
Вынести шедулы из контроллера в сервис
?vue
 */
