<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveWalletBalanceColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('wallets', static function (Blueprint $table) {
            $table->dropColumn('balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('wallets', static function (Blueprint $table) {
            $table->float('balance')->after('notes')->default(0);
        });
    }
}
