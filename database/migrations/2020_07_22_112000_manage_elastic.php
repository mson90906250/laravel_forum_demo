<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Migrations\Migration;


class ManageElastic extends Migration
{
    /**
     * 將資料匯入elasticsearch
     */
    public function up()
    {
        Artisan::call("elastic:migrate");
        Artisan::call("scout:import 'App\\\Thread'");
    }

    /**
     * 此migration專門用來reset elasticsearch裡的資料
     * 如果未來發生錯誤, 原因有可能是rollback的順序出錯
     * 必須確保在Rollback時 此migration 比 create_migration_table (babenkoivan/elastic-migrations套件的migration) 還要早執行
     */
    public function down()
    {
        Artisan::call('elastic:migrate:rollback');
    }
}
