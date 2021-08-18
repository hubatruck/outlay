<?php

namespace App\DataHandlers;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

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
     * @param Collection|array|null $data
     * @return ByWalletDataHandler
     */
    protected static function newInstance($data = null): ByWalletDataHandler
    {
        return new ByWalletDataHandler($data);
    }
}
