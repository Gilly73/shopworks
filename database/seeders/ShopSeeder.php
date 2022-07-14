<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
/*use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;*/
use App\Models\Shop;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*DB::table('shops')->insert([
            'name' => Str::random(10)
        ]);*/
        Shop::factory()->count(1)->create();

    }
}
