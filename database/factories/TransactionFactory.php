<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'wallet_id' => $this->faker->numberBetween(1, 10),
            'scope' => $this->faker->sentence,
            'amount' => $this->faker->randomFloat(2, 0, 250),
            'transaction_type_id' => $this->faker->numberBetween(1, 2)
        ];
    }
}
