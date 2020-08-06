<?php

use App\Trending;
use App\TrixImage;
use Illuminate\Database\Seeder;

class RedisCleanSeeder extends Seeder
{
    /**
     * database初始化後, 將原本存在redis裡的資料清掉
     *
     * @return void
     */
    public function run()
    {
        TrixImage::reset();
        (new Trending)->reset();
    }
}
