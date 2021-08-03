<?php

namespace Database\Factories;

use App\Models\Ad;
use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid(),
            'species' => fn () => collect(['cat', 'dog'])->random(),
            'name' => $this->faker->name(),
            'status' => $this->faker->boolean(),
            'ad_id' => fn () => Ad::all()->random()->id,
        ];
    }
}
