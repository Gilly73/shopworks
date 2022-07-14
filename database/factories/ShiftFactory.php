<?php

namespace Database\Factories;

use App\Models\Rota;
use App\Models\Shift;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftFactory extends Factory

{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Shift::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $start_time = $this->faker->dateTimeBetween('last Monday','last Monday +7 days');
        $end_time = $this->faker->dateTimeBetween($start_time, $start_time->format('Y-m-d H:i:s').' +2 hours');
        return [
            'rota_id' => Rota::factory()->create()->id,
            'staff_id' => Staff::factory()->create()->id,
            'start_time' => $start_time,
            'end_time' => $end_time
        ];
    }
}
