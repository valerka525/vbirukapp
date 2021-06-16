<?php

namespace App\Services;

use App\ThemeBackupSchedule;
use Illuminate\Support\Facades\Auth;

class ThemeBackupScheduler
{
    private $interval;
    private $scheduledTheme;
    private $scheduleId;

    public function __construct($interval = null, $scheduledTheme = null, $scheduleId = null)
    {
        $this->interval = $interval;
        $this->scheduledTheme = $scheduledTheme;
        $this->scheduleId = $scheduleId;
    }

    public function addSchedule()
    {
        $shop = Auth::user();
        $schedulesCount = $shop->schedules()->count();
        if ($schedulesCount >= 3) {
            return false;
        }
        $theme = preg_split("#/#", $this->scheduledTheme);
        ThemeBackupSchedule::create([
            'user_id' => $shop->id,
            'theme_id' => $theme[0],
            'theme_name' => $theme[1],
            'interval' => $this->interval,
        ]);
        return true;
    }

    public function deleteSchedule()
    {
        ThemeBackupSchedule::where('id', $this->scheduleId)->delete();
    }
}
