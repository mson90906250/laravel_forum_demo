<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class ElasticSearchCleanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 重新將資料匯入elasticsearch
        Artisan::call("elastic:migrate");
        Artisan::call("scout:import 'App\\\Thread'");
    }
}
