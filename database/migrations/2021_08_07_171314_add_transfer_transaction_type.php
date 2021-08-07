<?php

use App\Models\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddTransferTransactionType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('transaction_types', function () {
            TransactionType::transactionTypeCreate('Transfer in', 'Transfer out');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_types', function () {
            TransactionType::where('name', '=', 'Transfer in')->delete();
            TransactionType::where('name', '=', 'Transfer out')->delete();
        });
    }
}
