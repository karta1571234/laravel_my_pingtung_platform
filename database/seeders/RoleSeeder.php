<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'cheif_admin']);    //主管理員
        Role::create(['name' => 'bureau_admin']);   //局長
        Role::create(['name' => 'bureau_user']);    //衛生局一般使用者(沒用到)
        Role::create(['name' => 'director_admin']); //所長
        Role::create(['name' => 'director_user']);  //衛生所一般使用者(社工)
        Role::create(['name' => 'user']);  //長者
    }
}
