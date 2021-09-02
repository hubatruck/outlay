<?php

namespace App\DataHandlers;

use App\Models\Wallet;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\ArrayShape;

class ByWalletDataHandler extends ChartDataHandler
{
    /**
     * Map incoming and outgoing transfers to wallet names
     *
     * @param Collection $incoming
     * @param Collection $outgoing
     * @return ByWalletDataHandler
     */
    public static function mapTransfersToWallets(Collection $incoming, Collection $outgoing): ByWalletDataHandler
    {
        $data = [];
        foreach ($incoming as $in) {
            $key = self::formatWalletName($in->fromWallet);
            $data[$key['name']] = [
                'in' => $in->amount,
                'out' => 0.00,
            ];
        }

        foreach ($outgoing as $out) {
            $key = self::formatWalletName($out->toWallet);
            if (array_key_exists($key['name'], $data)) {
                $data[$key['name']]['out'] = $out->amount;
            } else {
                $data[$key['name']] = [
                    'out' => $out->amount,
                    'in' => 0.00,
                ];
            }
        }
        return new ByWalletDataHandler($data);
    }

    /**
     * Append owner's name next to wallets not belonging to the user
     *
     * @param Wallet $wallet
     * @return array
     */
    private static function formatWalletName(Wallet $wallet): array
    {
        if (Auth::user()->owns($wallet)) {
            return ['name' => $wallet->name];
        }
        return [
            'name' => $wallet->name . ' (' . $wallet->user->name . ')',
        ];
    }

    /**
     * @param array|Collection|null $data
     * @param CarbonPeriod|null $range
     * @return ByWalletDataHandler
     */
    protected static function newInstance(array|Collection $data = null, CarbonPeriod $range = null): ByWalletDataHandler
    {
        return new ByWalletDataHandler($data, $range);
    }
}
