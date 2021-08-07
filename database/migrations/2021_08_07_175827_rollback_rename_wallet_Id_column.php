<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RollbackRenameWalletIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['source_wallet_id']);
            DB::raw('drop index transactions_source_wallet_id_foreign on transactions;');
            $table->renameColumn('source_wallet_id', 'wallet_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('wallet_id')
                ->references('id')
                ->on('wallets')
                ->change();
            $table->unsignedBigInteger('wallet_id')->default(null)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['wallet_id']);
            DB::raw('drop index transactions_wallet_id_foreign on transactions;');
            $table->renameColumn('wallet_id', 'source_wallet_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('source_wallet_id')
                ->references('id')
                ->on('wallets')
                ->change();
            $table->unsignedBigInteger('source_wallet_id')->nullable()->default('NULL')->change();
        });
    }
}
