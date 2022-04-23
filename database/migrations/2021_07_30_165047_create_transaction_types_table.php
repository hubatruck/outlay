<?php

use App\Models\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('transaction_types', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        $this->transactionTypeCreate('Income', 'Expense');
    }

    private function transactionTypeCreate(string ...$types): void
    {
        foreach ($types as $type) {
            $tType = new TransactionType();
            $tType->setAttribute('name', $type);
            $tType->save();
        }
    }

    /// https://stackoverflow.com/a/53872157

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_types');
    }
}
