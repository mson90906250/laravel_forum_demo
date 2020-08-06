<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Migrations\Migration;

/**
 * 此migration專門用來reset elasticsearch裡的資料
 * 如果未來發生錯誤, 原因有可能是rollback的順序出錯
 * 必須確保此migration比create_migration_table還要早執行
 */
class RollbackElastic extends Migration
{
    public function down()
    {
        Artisan::call('elastic:migrate:rollback');
    }
}
