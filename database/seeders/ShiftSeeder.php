<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Shift::factory()->count(1)->create();
        $shifts = [[
            'rota_id' => 1,
            'staff_id' => 1,
            'start_time' => '2022-05-19 09:00:00',
            'end_time' => '2022-05-19 10:00:00'
        ],[
            'rota_id' => 1,
            'staff_id' => 2,
            'start_time' => '2022-05-19 10:00:01',
            'end_time' => '2022-05-19 11:00:00'
        ],[
            'rota_id' => 1,
            'staff_id' => 3,
            'start_time' => '2022-05-19 10:00:01',
            'end_time' => '2022-05-19 13:00:00'
        ],[
            'rota_id' => 1,
            'staff_id' => 4,
            'start_time' => '2022-05-19 13:00:01',
            'end_time' => '2022-05-19 17:00:00'
        ]];

        DB::table('shifts')->insert($shifts);
    }
}
