<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableThemeBackupSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'theme_backup_schedules',
            function (Blueprint $table) {
                $table->string('theme_name')->after('theme_id');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'theme_backup_schedules',
            function (Blueprint $table) {
                $table->dropColumn('theme_name');
            }
        );
    }
}
