<?php

namespace Database\Factories;

use App\Models\Pdv;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'pdv_id' => Pdv::factory(),
            'products' => [
                [
                    'id' => 1,
                    'valor' => 1000,
                    'descricao' => 'Product 1',
                ],
                [
                    'id' => 2,
                    'valor' => 2000,
                    'descricao' => 'Product 2',
                ],
                [
                    'id' => 3,
                    'valor' => 3000,
                    'descricao' => 'Product 3',
                ],
            ],
            'value' => $this->faker->randomFloat(2, 1000, 10000),
            'cancel_reason' => $this->faker->words(3, true),
            'status' => $this->faker->randomElement(
                [
                    Sale::STATUS_PAYMENT_PENDING,
                    Sale::STATUS_PAID,
                    Sale::STATUS_REJECTED,
                    Sale::STATUS_CANCELED,
                ]
            ),
        ];
    }

    /**
     * Indicate that the model is active.
     *
     * @return static
     */
    public function pending()
    {
        return $this->state(
            fn () => [
                'cancel_reason' => null,
                'status' => Sale::STATUS_PAYMENT_PENDING,
            ]
        );
    }
}
