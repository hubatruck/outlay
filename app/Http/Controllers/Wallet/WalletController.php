<?php

namespace App\Http\Controllers\Wallet;

use App\Charts\MonthlyChartByDay;
use App\Charts\MonthlyChartByTransactionType;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WalletController extends Controller
{
    /**
     * Editor view name
     *
     * @var string
     */
    private $editorViewName = 'wallet/edit';

    /**
     * Show the view for editing wallet
     *
     * @return View
     */
    public function createView(): View
    {
        return view($this->editorViewName);
    }

    /**
     * Show the view for editing wallet
     *
     * @param string $id
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function editView(string $id)
    {
        $wallet = Wallet::withTrashed()->find($id);

        $permissionCheck = $this->checkWallet($wallet);
        return $permissionCheck ?: view($this->editorViewName, compact('wallet'));
    }

    /**
     * Check if the provided wallet is wallet or if the user owns the wallet
     *
     * @param Wallet $wallet
     * @return RedirectResponse|null
     */
    private function checkWallet(Wallet $wallet = null)
    {
        if ($wallet === null) {
            return $this->walletDoesNotExist();
        }
        if (!Auth::user()->owns($wallet)) {
            return $this->cannotEditWallet();
        }
        return null;
    }

    /**
     * Redirect user to wallet list if wallet does not exist
     *
     * @return RedirectResponse
     */
    private function walletDoesNotExist(): RedirectResponse
    {
        return redirect()
            ->route('wallet.view.all')
            ->with([
                'message' => __('Error') . ': ' . __('Wallet does not exist.'),
                'status' => 'danger',
            ]);
    }

    /**
     * Redirect user to wallet list, if user cannot edit wallet
     *
     * @return RedirectResponse
     */
    private function cannotEditWallet(): RedirectResponse
    {
        return redirect()
            ->route('wallet.view.all')
            ->with([
                'message' => __('Error') . ': ' . __('You cannot edit this wallet.'),
                'status' => 'danger',
            ]);
    }

    /**
     * Show details page for wallet, if user owns it
     *
     * @param string $id
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse|Redirector
     */
    public function detailsView(string $id)
    {
        $dailyChart = (new MonthlyChartByDay(new LarapexChart()))->build($id);
        $typeChart = (new MonthlyChartByTransactionType(new LarapexChart()))->build($id);
        $wallet = Wallet::withTrashed()->findOrFail($id);

        if (!Auth::user()->owns($wallet)) {
            return redirect(route('wallet.view.all'))
                ->with([
                    'message' => __('Error') . ': ' . __('You cannot view this wallet.'),
                    'status' => 'danger',
                ]);
        }

        return view('wallet.details', compact('dailyChart', 'typeChart', 'wallet'));
    }

    /**
     * Save a new wallet
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function storeWallet(Request $request): RedirectResponse
    {
        $newWalletData = $this->validateRequest($request);
        $newWalletData['user_id'] = Auth::user()->id;
        Wallet::create($newWalletData);

        return $this->redirectSuccess();
    }

    /**
     * Validate request data
     *
     * @param Request $request
     * @param bool $isNewModelInstance
     * @return array
     */
    public function validateRequest(Request $request, bool $isNewModelInstance = true): array
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'notes' => 'nullable|string',
            'balance' => ($isNewModelInstance ? '' : 'nullable|') . 'numeric|max:999999.99',
            'is_card' => 'nullable',
        ]);

        $data['is_card'] = isset($data['is_card']);
        return $data;
    }

    /**
     * Redirect user to wallet list with success message
     *
     * @param string $successMethod
     * @return RedirectResponse
     */
    private function redirectSuccess(string $successMethod = 'created', string $url = null): RedirectResponse
    {
        return redirect($url ?? route('wallet.view.all'))
            ->with([
                'message' => __(
                    'Wallet :action successfully.', [
                        'action' => __($successMethod)
                    ]
                ),
                'status' => 'success',
            ]);
    }

    /**
     * Update a wallet
     *
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     */
    public function updateWallet(Request $request, string $id): RedirectResponse
    {
        $validated = $this->validateRequest($request, false);

        $wallet = Wallet::withTrashed()->find($id);

        $permissionCheck = $this->checkWallet($wallet);
        if ($permissionCheck !== null) {
            return $permissionCheck;
        }

        $wallet->fill($validated);
        $wallet->save();
        return $this->redirectSuccess('updated', route('wallet.view.details', ['id' => $id]));
    }

    /**
     * Delete a wallet if it does not have transactions tied to it
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function deleteWallet(string $id): RedirectResponse
    {
        $wallet = Wallet::withTrashed()->find($id);

        $permissionCheck = $this->checkWallet($wallet);
        if ($permissionCheck !== null) {
            return $permissionCheck;
        }

        if (count($wallet->transactions)) {
            return redirect(previousUrlOr(route('wallet.view.details', ['id' => $wallet->id])))
                ->with([
                    'message' => __('Error') . ': ' . __('Wallet has transactions linked to it. Cannot be deleted.'),
                    'status' => 'danger',
                ]);
        }

        $wallet->forceDelete();
        return $this->redirectSuccess('deleted');
    }

    /**
     * Toggle the trashed/active status of a wallet
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function toggleHidden(string $id): RedirectResponse
    {
        $wallet = Wallet::withTrashed()->find($id);

        $permissionCheck = $this->checkWallet($wallet);
        if ($permissionCheck !== null) {
            return $permissionCheck;
        }

        if ($wallet->trashed()) {
            $action = 'restored';
            $wallet->restore();
        } else {
            $action = 'hidden';
            $wallet->delete();
        }

        return $this->redirectSuccess(
            $action,
            previousUrlOr(route('wallet.view.details', ['id' => $id]))
        );
    }
}
