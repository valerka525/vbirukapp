<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Services\ThemeBackup;
use App\Services\ThemeBackupScheduler;
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
        $schedule = new ThemeBackupScheduler($request['interval'], $request['theme']);
        $result = ($schedule->addSchedule()) ? ['type' => 'success', 'message' =>
            __('flashes.schedule_created')] : ['type' => 'warning', 'message' => __('flashes.schedules_limit')];
        return redirect()->route('home')->with($result['type'], $result['message'])->with('show', 'schedules');
    }

    protected static function deleteSchedule($id)
    {
        $schedule = new ThemeBackupScheduler(null, null, $id);
        $schedule->deleteSchedule();
        return redirect()->route('home')->with('warning', __('flashes.schedule_deleted'))->
        with('show', 'schedules');
    }
}
