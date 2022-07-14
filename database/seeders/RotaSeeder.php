<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Rota;

class RotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rota::factory()->count(1)->create();
    }
}
