<?php

namespace Database\Factories;

use App\Models\Rota;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class RotaFactory extends Factory

{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rota::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'shop_id' => Shop::factory()->create()->id,
            'week_commence_date' => '2022-05-19'
        ];
    }
}
