<?php

namespace App\Http\Controllers\Wallet;

use App\Feedbacks\WalletFeedback;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * This controller handles wallet view related requests
 */
class WalletViewController extends Controller
{
    /**
     * Editor view name
     *
     * @var string
     */
    private string $editorViewName = 'wallet/edit';

    /**
     * List all wallets view
     *
     * @return Factory|View|Application
     */
    public function list(): Factory|View|Application
    {
        $wallets = Auth::user()->wallets()->get()->sortBy('deleted_at');
        return view('wallet.list', compact('wallets'));
    }

    /**
     * Show the view for editing wallet
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view($this->editorViewName);
    }

    /**
     * Show the view for editing wallet
     *
     * @param string $id
     * @return View|Factory|RedirectResponse|Application
     */
    public function edit(string $id): View|Factory|RedirectResponse|Application
    {
        $wallet = Wallet::withTrashed()->find($id);

        $permissionCheck = Wallet::check($wallet);
        return $permissionCheck ?: view($this->editorViewName, compact('wallet'));
    }

    /**
     * Show details page for wallet, if user owns it
     *
     * @param string $id
     * @return View|Factory|RedirectResponse|Application
     */
    public function details(string $id): View|Factory|RedirectResponse|Application
    {
        $wallet = Wallet::withTrashed()->findOrFail($id);
        if (!Auth::user()->owns($wallet)) {
            return WalletFeedback::viewError();
        }

        return view('wallet.details', compact('wallet'));
    }
}
