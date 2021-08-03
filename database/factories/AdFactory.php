<?php

namespace Database\Factories;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ad::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid(),
            'campaign_id' => $this->faker->randomNumber(9, true),
            'ad_set_id' => $this->faker->randomNumber(9, true),
            'ad_id' => $this->faker->randomNumber(9, true),
            'creative_id' => $this->faker->randomNumber(9, true),
            'budget' => $this->faker->randomFloat(2, 5, 20),
            'results' => $this->faker->randomNumber(5),
            'reach' => $this->faker->randomNumber(5),
            'impressions' => $this->faker->randomNumber(5),
            'cost_per_result' => $this->faker->randomFloat(2, 0, 2),
            'amount_spent' => $this->faker->randomFloat(2, 5, 20),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'user_id' => fn () => User::all()->random()->id,
        ];
    }
}
