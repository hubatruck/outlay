<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Wallet;
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
        $source_wallet = $this->faker
            ->randomElement(Wallet::all()->pluck('id')->toArray());
        $dest_wallet = $this->faker
            ->randomElement(Wallet::whereNotIn('id', [$source_wallet])->pluck('id')->toArray());
        $dest_wallet = $this->faker->randomElement([$dest_wallet, null]);
        $type = $dest_wallet === null
            ? $this->faker->randomElement(TransactionType::whereNotIn('id', [3, 4])->pluck('id')->toArray())
            : 3;

        return [
            'wallet_id' => $source_wallet,
            'destination_wallet_id' => $dest_wallet,
            'scope' => $this->faker->sentence,
            'amount' => $this->faker->randomFloat(2, 0, 250),
            'transaction_type_id' => $type,
            'transaction_date' => $this->faker->dateTimeBetween(date('Y-m-01'), date('Y-m-t')),
        ];
    }
}
