<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDestinationWalletIdColumnForTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('destination_wallet_id')->after('source_wallet_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('destination_wallet_id')
                ->references('id')
                ->on('wallets')
                ->change();
            $table->unsignedBigInteger('destination_wallet_id')->nullable()->default('NULL')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down():void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['destination_wallet_id']);
            DB::raw('drop index transactions_destination_wallet_id_foreign on transactions;');
            $table->dropColumn(['destination_wallet_id']);
        });
    }
}
