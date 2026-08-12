<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'wallet_id' => $this->faker->randomElement(Wallet::all()->pluck('id')->toArray()),
            'scope' => $this->faker->sentence,
            'amount' => $this->faker->randomFloat(2, 0, 250),
            'transaction_type_id' => $this->faker->randomElement(TransactionType::all()->pluck('id')->toArray()),
            'transaction_date' => $this->faker->dateTimeBetween(date('Y-m-01'), date('Y-m-t')),
        ];
    }
}
