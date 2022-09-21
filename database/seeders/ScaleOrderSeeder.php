<?php

namespace Database\Seeders;

use App\Models\ScaleOrder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScaleOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ScaleOrder::create(['name' => '極早期失智症量表 (AD8)']);
        ScaleOrder::create(['name' => '老人憂鬱量表 (GDS-15) Geriatric Depression Scale']);
        ScaleOrder::create(['name' => '長者功能評估量表 (ICOPE) Integrated Care for Older People']);
    }
}
