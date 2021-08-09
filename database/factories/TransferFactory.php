<?php

namespace Database\Factories;

use App\Models\Transfer;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transfer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $wallets = Wallet::all()->pluck('id')->toArray();
        $to = $this->faker->randomElement($wallets);
        /// https://stackoverflow.com/a/369608
        $from = $this->faker->randomElement(array_diff($wallets, [$to]));

        return [
            'to_wallet_id' => $to,
            'from_wallet_id' => $from,
            'amount' => $this->faker->randomFloat(2, 0, 250),
            'description' => $this->faker->sentence,
            'transfer_date' => $this->faker->dateTimeBetween(date('Y-m-01'), date('Y-m-t')),
        ];
    }
}
