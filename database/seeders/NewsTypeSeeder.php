<?php

namespace Database\Seeders;

use App\Models\NewsType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NewsType::create(['type' => '最新消息']);
        NewsType::create(['type' => '活動消息']);
    }
}
