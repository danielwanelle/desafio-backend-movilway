<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pdv>
 */
class PdvFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'fantasy_name' => fake()->company(),
            'cnpj' => fake()->numerify('##.###.###/####-##'),
            'owner_name' => fake()->name(),
            'owner_phone' => fake()->numerify('(##) #####-####'),
            'sales_limit' => fake()->randomFloat(2, 1000, 10000),
            'active' => fake()->boolean(),
        ];
    }

    /**
     * Indicate that the model is active.
     *
     * @return static
     */
    public function active()
    {
        return $this->state(
            fn () => [
                'active' => true,
            ]
        );
    }
}
