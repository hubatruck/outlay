<?php

namespace Tests\Feature\Http\Controllers\Wallet;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * List wallets view
     */
    public function test_list_wallet_route_displays_the_wallet_list(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->get(route('wallet.view.all'));

        $response->assertViewIs('wallet.list');
        $response->assertOk();
    }

    /**
     * Create view of a wallet
     */
    public function test_create_wallet_route_displays_the_wallet_edit_view(): void
    {
        $user = User::factory()->create();
        $this->followingRedirects();
        $response = $this->actingAs($user)
            ->get(route('wallet.view.create'));

        $response->assertViewIs('wallet.edit');
        $response->assertOk();
    }

    /**
     * Edit view non-existent wallet
     */
    public function test_edit_wallet_route_trying_to_edit_nonexistent_wallet(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->get(route('wallet.view.update', ['id' => '9999']));

        $response->assertRedirect(route('wallet.view.all'));
        $response->assertSessionHas(['status' => 'danger']);
    }

    /**
     * Edit view other user's wallet
     */
    public function test_edit_wallet_route_trying_to_edit_other_users_wallet(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $wallet = $this->createWalletFor($otherUser);

        $response = $this->actingAs($user)
            ->get(route('wallet.view.update', ['id' => (string) $wallet->id]));

        $response->assertRedirect(route('wallet.view.all'));
        $response->assertSessionHasAll(['status' => 'danger']);
    }

    /**
     * Create a wallet belonging to the provided user
     * @param User $user
     * @return Wallet
     */
    private function createWalletFor(Model $user): Wallet
    {
        return Wallet::factory()->create(['user_id' => (string) $user->id]);
    }

    /**
     * Edit view for existing wallet
     */
    public function test_edit_wallet_loads_the_wallet_editing_view_for_existing_wallet(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWalletFor($user);

        $response = $this->actingAs($user)
            ->get(route('wallet.view.update', ['id' => (string) $wallet->user_id]));

        $response->assertViewIs('wallet.edit');
        $response->assertOk();
    }

    /**
     * Create wallet with valid data
     */
    public function test_save_valid_wallet(): void
    {
        $user = User::factory()->create();
        $wallet = $this->rawWallet($user);

        $response = $this->actingAs($user)
            ->post(route('wallet.data.create'), $wallet);
        $saved = Wallet::find(1);

        self::assertEquals($saved->user_id, $user->id);
        self::assertEquals($saved->name, $wallet['name']);
        $response->assertRedirect(route('wallet.view.all'));
        $response->assertSessionHasNoErrors();
    }

    /**
     * Create a wallet to belonging to the passed user and get the raw attributes
     * @param User $user
     * @return array
     */
    private function rawWallet(User $user): array
    {
        return Wallet::factory(['user_id' => (string) $user->id])->raw();
    }

    /**
     * Create wallet with no data submitted
     */
    public function test_save_wallet_validate_with_no_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('wallet.data.create'), []);

        $response->assertSessionHasErrors(['name']);
        $response->assertRedirect();
    }

    /**
     * Create wallet with too long name
     * @throws Exception
     */
    public function test_save_wallet_validate_too_long_name(): void
    {
        $user = User::factory()->create();
        $wallet = $this->rawWallet($user);
        $wallet['name'] = bin2hex(random_bytes(1000));

        $response = $this->actingAs($user)
            ->post(route('wallet.data.create'), $wallet);

        $response->assertSessionHasErrors(['name']);
        $response->assertRedirect();
    }

    /**
     * Create wallet with non-numeric balance value
     */
    public function test_save_wallet_validate_non_numeric_balance(): void
    {
        $user = User::factory()->create();
        $wallet = $this->rawWallet($user);
        $wallet['balance'] = 'definitely not a number';

        $response = $this->actingAs($user)
            ->post(route('wallet.data.create'), $wallet);

        $response->assertSessionHasErrors(['balance']);
        $response->assertRedirect();
    }

    /**
     * Create wallet with too big balance
     */
    public function test_save_wallet_validate_too_big_balance(): void
    {
        $user = User::factory()->create();
        $wallet = $this->rawWallet($user);
        $wallet['balance'] = 99999999;

        $response = $this->actingAs($user)
            ->post(route('wallet.data.create'), $wallet);

        $response->assertSessionHasErrors(['balance']);
        $response->assertRedirect();
    }

    /**
     * Update wallet with empty data
     */
    public function test_update_wallet_validate_empty(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWalletFor($user);

        $response = $this->actingAs($user)
            ->post(route('wallet.data.update', [
                'id' => (string) $wallet->id,
            ]), []);

        $response->assertSessionHasErrors(['name']);
        $response->assertRedirect();
    }

    /**
     * Update wallet with too long name
     * @throws Exception
     */
    public function test_update_wallet_validate_too_long_name(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWalletFor($user);

        $response = $this->actingAs($user)
            ->post(route('wallet.data.update', [
                'id' => (string) $wallet->id,
            ]), ['name' => bin2hex(random_bytes(1000))]);

        $response->assertSessionHasErrors(['name']);
        $response->assertRedirect();
    }

    /**
     * Update wallet with non-numeric balance value
     */
    public function test_update_wallet_validate_non_numeric_balance(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWalletFor($user);

        $response = $this->actingAs($user)
            ->post(route('wallet.data.update', [
                'id' => (string) $wallet->id,
            ]), ['balance' => 'definitely not a number']);

        $response->assertSessionHasErrors(['balance']);
        $response->assertRedirect();
    }

    /**
     * Update wallet with too big balance value
     */
    public function test_update_wallet_validate_too_big_balance(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWalletFor($user);

        $response = $this->actingAs($user)
            ->post(route('wallet.data.update', [
                'id' => (string) $wallet->id,
            ]), ['balance' => 9999999999]);

        $response->assertSessionHasErrors(['balance']);
        $response->assertRedirect();
    }

    /**
     * Update wallet that does not exist
     */
    public function test_update_wallet_non_existent_wallet(): void
    {
        $user = User::factory()->create();
        $wallet = $this->rawWallet($user);
        $response = $this->actingAs($user)
            ->post(route('wallet.data.update', [
                'id' => 9999,
            ]), $wallet);

        $response->assertSessionHas(['status' => 'danger']);
        $response->assertRedirect(route('wallet.view.all'));
    }

    /**
     * Update wallet belonging to other user
     */
    public function test_update_wallet_trying_to_update_other_users_wallet(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $wallet = $this->createWalletFor($otherUser);

        $response = $this->actingAs($user)
            ->post(route('wallet.data.update', [
                'id' => (string) $wallet->id,
            ]), $wallet->attributesToArray());

        $response->assertRedirect(route('wallet.view.all'));
        $response->assertSessionHasAll(['status' => 'danger']);
    }

    /**
     * Update wallet with all correct data
     */
    public function test_update_wallet_correct_data(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWalletFor($user);
        $wallet2 = $this->createWalletFor($user);
        $response = $this->actingAs($user)
            ->post(route('wallet.data.update', [
                'id' => $wallet->id,
            ]), $wallet2->attributesToArray());

        $saved = Wallet::find(1);

        $shared_data_keys = ['name', 'user_id', 'notes'];
        self::assertEquals($wallet2->only($shared_data_keys), $saved->only($shared_data_keys));

        $response->assertSessionHas(['status' => 'success']);
        $response->assertRedirect(route('wallet.view.details', ['id' => $wallet->id]));
    }

    /**
     * Delete wallet that does not exist
     */
    public function test_delete_wallet_non_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('wallet.manage.delete', ['id' => 1]));

        $response->assertRedirect(route('wallet.view.all'));
        $response->assertSessionHas(['status' => 'danger']);
    }

    /**
     * Delete wallet that belongs to other user
     */
    public function test_delete_wallet_not_users(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $wallet = $this->createWalletFor($user2);

        $response = $this->actingAs($user)->get(route('wallet.manage.delete', ['id' => $wallet->id]));

        $deleted = Wallet::find($wallet->id);
        self::assertNotNull($deleted);

        $response->assertRedirect(route('wallet.view.all'));
        $response->assertSessionHas(['status' => 'danger']);
    }

    /**
     * Delete wallet that has got transactions
     */
    public function test_delete_wallet_has_transactions(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWalletFor($user);
        Transaction::factory()->create(['wallet_id' => (string) $wallet->id]);

        $response = $this->actingAs($user)
            ->from(route('wallet.view.details', ['id' => $wallet->id]))
            ->get(route('wallet.manage.delete', ['id' => $wallet->id]));

        $deleted = Wallet::find($wallet->id);
        self::assertNotNull($deleted);
        $response->assertSessionHas(['status' => 'danger']);
        $response->assertLocation(route('wallet.view.details', ['id' => $wallet->id]));

        $response = $this->actingAs($user)
            ->from(route('wallet.view.all'))
            ->get(route('wallet.manage.delete', ['id' => $wallet->id]));

        $deleted = Wallet::find($wallet->id);
        self::assertNotNull($deleted);

        $response->assertSessionHas(['status' => 'danger']);
        $response->assertLocation(route('wallet.view.all'));
    }

    /**
     * Delete wallet
     */
    public function test_delete_wallet_successfully(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWalletFor($user);

        $response = $this->actingAs($user)->get(route('wallet.manage.delete', ['id' => $wallet->id]));

        $deleted = Wallet::find($wallet->id);
        self::assertNull($deleted);

        $response->assertLocation(route('wallet.view.all'));
        $response->assertSessionHas(['status' => 'success']);
    }

    /**
     * Hide wallet that does not exist
     */
    public function test_toggle_hidden_wallet_non_existent(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('wallet.manage.toggle_hidden', ['id' => 1]));

        $response->assertLocation(route('wallet.view.all'));
        $response->assertSessionHas(['status' => 'danger']);
    }

    /**
     * Hide other user's wallet
     */
    public function test_toggle_hidden_wallet_not_users(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $wallet = $this->createWalletFor($user2);

        $response = $this->actingAs($user)->get(route('wallet.manage.toggle_hidden', ['id' => $wallet->id]));

        $wallet->refresh();
        self::assertFalse($wallet->trashed());

        $response->assertLocation(route('wallet.view.all'));
        $response->assertSessionHas(['status' => 'danger']);
    }

    /**
     * Hide wallet with transactions
     */
    public function test_toggle_hidden_wallet_has_transactions(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWalletFor($user);
        Transaction::factory()->create(['wallet_id' => (string) $wallet->id]);

        $response = $this->actingAs($user)
            ->from(route('wallet.view.all'))
            ->get(route('wallet.manage.toggle_hidden', ['id' => $wallet->id]));

        $wallet->refresh();
        self::assertTrue($wallet->trashed());

        $response->assertLocation(route('wallet.view.all'));
        $response->assertSessionHas(['status' => 'success']);

        /// checking redirect
        $wallet->delete();
        $response = $this->actingAs($user)
            ->from(route('login'))
            ->get(route('wallet.manage.toggle_hidden', ['id' => $wallet->id]));

        $wallet->refresh();
        self::assertFalse($wallet->trashed());

        $response->assertLocation(route('wallet.view.details', ['id' => $wallet->id]));
        $response->assertSessionHas(['status' => 'success']);
    }

    /**
     * Hide an active wallet
     */
    public function test_toggle_hidden_wallet_hide(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWalletFor($user);

        $response = $this->actingAs($user)
            ->from(route('wallet.view.all'))
            ->get(route('wallet.manage.toggle_hidden', ['id' => $wallet->id]));

        $wallet->refresh();
        self::assertTrue($wallet->trashed());

        $response->assertLocation(route('wallet.view.all'));
        $response->assertSessionHas(['status' => 'success']);

        /// checking redirect
        $wallet->delete();
        $response = $this->actingAs($user)
            ->from(route('login'))
            ->get(route('wallet.manage.toggle_hidden', ['id' => $wallet->id]));

        $wallet->refresh();
        self::assertFalse($wallet->trashed());

        $response->assertLocation(route('wallet.view.details', ['id' => $wallet->id]));
        $response->assertSessionHas(['status' => 'success']);
    }

    /**
     * Activate a hidden wallet
     */
    public function test_toggle_hidden_wallet_activate(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWalletFor($user);
        $wallet->delete();

        $response = $this->actingAs($user)
            ->from(route('wallet.view.all'))
            ->get(route('wallet.manage.toggle_hidden', ['id' => $wallet->id]));

        $wallet->refresh();
        self::assertFalse($wallet->trashed());

        $response->assertLocation(route('wallet.view.all'));
        $response->assertSessionHas(['status' => 'success']);

        /// checking redirect
        $wallet->delete();
        $response = $this->actingAs($user)
            ->from(route('login'))
            ->get(route('wallet.manage.toggle_hidden', ['id' => $wallet->id]));

        $wallet->refresh();
        self::assertFalse($wallet->trashed());

        $response->assertLocation(route('wallet.view.details', ['id' => $wallet->id]));
        $response->assertSessionHas(['status' => 'success']);
    }
}
