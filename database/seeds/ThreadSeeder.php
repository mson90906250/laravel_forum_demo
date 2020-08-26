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
        factory('App\Thread', 20)->create()->each(function ($thread) {
            factory('App\Reply', 15)->create([
                'thread_id' => $thread->id
            ]);
        });
    }
}
