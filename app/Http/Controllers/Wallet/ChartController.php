<?php

namespace App\Http\Controllers\Wallet;

use App\Charts\DailyBalanceChart;
use App\Charts\DailyTransactionsChart;
use App\Charts\DailyTransfersChart;
use App\Charts\TransactionsByTypeChart;
use App\Charts\TransfersByTypeChart;
use App\Charts\TransfersByWalletChart;
use App\Feedbacks\WalletFeedback;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\Exceptions\InvalidFormatException;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChartController extends Controller
{
    /**
     * Get charts for a specified wallet in a date range
     *
     * @param Request $request
     * @param string $id
     * @return RedirectResponse|string
     */
    public function getFor(Request $request, string $id): string|RedirectResponse
    {
        $wallet = Wallet::withTrashed()->findOrFail($id);
        if (!Auth::user()->owns($wallet)) {
            return WalletFeedback::viewError();
        }

        $range = $this->parseRange($request);
        $charts = $this->initCharts($wallet, $range);

        return view(
            'wallet.charts',
            array_merge(compact('wallet'), $charts)
        )->render();
    }

    /**
     * Parse the date range for charts from the request
     *
     * @param Request $request
     * @return CarbonPeriod
     */
    private function parseRange(Request $request): CarbonPeriod
    {
        $fallback = defaultChartRange();
        if ($request->has('range')) {
            $rawRange = explode(' - ', $request->get('range'));

            /// Single day selected workaround
            if (sizeof($rawRange) !== 2) {
                try {
                    $rawRange[0] = Carbon::parse($rawRange[0])->startOfDay();
                    $rawRange[1] = $rawRange[0]->endOfDay();
                } catch (InvalidFormatException) {
                    $rawRange[1] = currentDayOfTheMonth();
                    $rawRange[0] = $rawRange[1];
                }
            }

            /// In case of correct range format (xx - yy), but un-parseable data
            try {
                $range = CarbonPeriod::create(($rawRange[0]), ($rawRange[1]));
            } catch (Exception) {
                $range = $fallback;
            }
        }
        return $range ?? $fallback;
    }

    /**
     * @param Wallet $wallet
     * @param CarbonPeriod $range
     * @return array
     */
    private function initCharts(Wallet $wallet, CarbonPeriod $range): array
    {
        $balanceDailyChart = (new DailyBalanceChart(new LarapexChart(), $range))->build($wallet);
        $transactionDailyChart = (new DailyTransactionsChart(new LarapexChart(), $range))->build($wallet);
        $transactionTypeChart = (new TransactionsByTypeChart(new LarapexChart(), $range))->build($wallet);
        $transferDailyChart = (new DailyTransfersChart(new LarapexChart(), $range))->build($wallet);
        $transferTypeChart = (new TransfersByTypeChart(new LarapexChart(), $range))->build($wallet);
        $transferWalletChart = (new TransfersByWalletChart(new LarapexChart(), $range))->build($wallet);

        return compact(
            'balanceDailyChart',
            'transactionDailyChart',
            'transactionTypeChart',
            'transferDailyChart',
            'transferTypeChart',
            'transferWalletChart',
            'wallet'
        );
    }
}
