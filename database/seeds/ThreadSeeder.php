<?php

use Illuminate\Database\Seeder;

class ThreadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // thread生成後會直接將資料匯入elasticsearch裡, 然而此時elasticsearch的相關設定尚未完成
        // 所以必須先設定好elasticsearch
        $this->call(ElasticSearchCleanSeeder::class);

        factory('App\Thread', 20)->create()->each(function ($thread) {
            factory('App\Reply', 15)->create([
                'thread_id' => $thread->id
            ]);
        });
    }
}
