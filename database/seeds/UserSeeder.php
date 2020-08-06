<?php

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 加入一個admin
        User::create([
            'name' => 'admin',
            'email' => 'admin@test.com',
            'email_verified_at' => Carbon::now()->toDateTime(),
            'password' => Hash::make(123123123)
        ]);
    }
}
