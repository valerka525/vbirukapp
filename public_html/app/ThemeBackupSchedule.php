<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ThemeBackupSchedule extends Model
{
    protected $fillable = ['user_id', 'theme_id', 'theme_name', 'interval'];

    public function user(): \Illuminate\Database\Eloquent\Relations\belongsTo
    {
        return $this->belongsTo(User::class);
    }
}
