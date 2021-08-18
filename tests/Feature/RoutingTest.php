<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check if '/' route is redirected to /home
     *
     * @return void
     */
    public function test_redirect_root_to_home(): void
    {
        $this->get('/')->assertStatus(302);
    }

    /**
     * Check if not found handling is working
     */
    public function test_not_found(): void
    {

        $this->get('/pretty_sure_does_not_exist')->assertNotFound();
    }

    /**
     * Check if user is redirected to login page if is guest
     */
    public function test_home_redirecting_to_login_when_not_authenticated(): void
    {
        $this->get(route('dashboard'))->assertStatus(302);
        $this->assertGuest();
    }

    /**
     * Check home routing when user is authenticated
     */
    public function test_home_when_authenticated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard'))->assertOk();

        $this->assertAuthenticatedAs($user);
    }

    /**
     * Check logging out
     */
    public function test_logging_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertStatus(302);
        $this->assertGuest();
    }

    /**
     * Check viewing transactions
     */
    public function test_view_transactions(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/transactions')->assertOk();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Check viewing wallets
     */
    public function test_view_wallets(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/wallets')->assertOk();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Check wallet creation view
     */
    public function test_create_wallet_view(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/wallets/create')->assertOk();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * View a wallet's details that does not exist.
     */
    public function test_create_wallet_details_view_non_existent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/wallets/9999/details')->assertStatus(404);
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Edit view for a wallet that does not exist
     */
    public function test_create_wallet_edit_view_non_existent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/wallets/9999/edit')->assertStatus(302);
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Create transactions without wallets
     */
    public function test_create_transaction_view_with_no_wallet(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/transactions/create')->assertStatus(302);
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Edit transaction that does not exist
     */
    public function test_edit_transaction_view_non_existent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/transactions/9999/edit')->assertStatus(302);
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Delete transaction that does not exist
     */
    public function test_delete_transaction_non_existent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/transactions/9999/delete')->assertStatus(302);
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Post new transaction
     */
    public function test_post_new_transaction_with_empty_data(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/transactions/create')->assertStatus(302);
        $response->assertSessionHasErrors();
    }
}
