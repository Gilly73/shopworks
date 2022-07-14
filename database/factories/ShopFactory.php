<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopFactory extends Factory

{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Shop::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        /*The definition method returns the default set of attribute values that
        should be applied when creating a model using the factory.*/
        return [
            'name' => $this->faker->firstNameFemale(),
            'active' => $this->faker->numberBetween(0,1)
        ];
    }

    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => 1,
            ];
        });
    }
}
