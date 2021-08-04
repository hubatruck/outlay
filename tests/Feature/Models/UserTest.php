<?php

namespace Tests\Feature\Models;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check hasWallet function
     */
    public function test_user_has_wallet_function(): void
    {
        $user = User::factory()->create();

        self::assertFalse($user->hasWallet());

        $wallet = $this->createWalletFor($user);
        $user->refresh();
        self::assertTrue($user->hasWallet());

        /// hidden wallet
        $wallet->delete();
        $user->refresh();
        self::assertTrue($user->hasWallet());

        /// no wallet at all
        $wallet->forceDelete();
        $user->refresh();
        self::assertFalse($user->hasWallet());
    }

    private function createWalletFor(User $user): Wallet
    {
        return Wallet::factory()->create(['user_id' => $user->id]);
    }

    /**
     * Check hasAnyActiveWallet function
     */
    public function test_has_any_active_wallet_function(): void
    {
        $user = User::factory()->create();

        self::assertFalse($user->hasAnyActiveWallet());

        $wallet = $this->createWalletFor($user);
        $user->refresh();
        self::assertTrue($user->hasAnyActiveWallet());

        $wallet->delete();
        $user->refresh();
        self::assertFalse($user->hasAnyActiveWallet());

        $wallet->forceDelete();
        $user->refresh();
        self::assertFalse($user->hasAnyActiveWallet());
    }

    /**
     * Check activeWallets function
     */
    public function test_active_wallets_function(): void
    {
        $user = User::factory()->create();

        self::assertEmpty($user->activeWallets());

        $wallet = $this->createWalletFor($user);
        $user->refresh();
        self::assertNotEmpty($user->activeWallets());

        $wallet->delete();
        $user->refresh();
        self::assertEmpty($user->activeWallets());

        $wallet->forceDelete();
        $user->refresh();
        self::assertEmpty($user->activeWallets());
    }

    /**
     * Check hasTransactions function
     */
    public function test_has_transactions_function(): void
    {
        $user = User::factory()->create();

        self::assertFalse($user->hasTransactions());

        $wallet = $this->createWalletFor($user);
        $transaction = Transaction::factory()->create(['wallet_id' => $wallet->id]);

        $user->refresh();
        self::assertTrue($user->hasTransactions());

        $transaction->delete();
        $user->refresh();
        self::assertFalse($user->hasTransactions());
    }

    /**
     * Test owns function with owned models
     */
    public function test_owns_function_owned(): void
    {
        $user = User::factory()->create();

        $wallet = $this->createWalletFor($user);

        /// wallet that is owned by the user
        self::assertTrue($user->owns($wallet));

        /// owned transaction
        $transaction = Transaction::factory()->create(['wallet_id' => $wallet->id]);
        $user->refresh();
        self::assertTrue($user->owns($transaction));
    }

    /**
     * Test owns function with not owned, but correct type model
     */
    public function test_owns_function_not_owned(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $user2Wallet = $this->createWalletFor($user2);

        /// not their wallet
        $user->refresh();
        self::assertFalse($user->owns($user2Wallet));

        /// not their transaction
        $user2Transaction = Transaction::factory()->create(['wallet_id' => $user2Wallet->id]);
        $user->refresh();
        self::assertFalse($user->owns($user2Transaction));
    }

    /**
     * Test owns function with not allowed types
     */
    public function test_owns_function_not_allowed_type():void{
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        self::assertFalse($user->owns($user2));
    }
}
