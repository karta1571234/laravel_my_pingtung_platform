<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        //kevin admin
        User::factory()->create([
            'name' => 'Kevin',
            'gender' => '男',
            'birth' => '2000-01-01',
            'ID_num' => 'E123456789',
            'email' => 'test@example.com',
            'password' => Hash::make('abcd1234'),
        ]);
        User::factory(10)->create();
    }
}
