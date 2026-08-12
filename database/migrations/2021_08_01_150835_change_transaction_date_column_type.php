<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTransactionDateColumnType extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', static function (Blueprint $table) {
            $table->dateTime('transaction_date')->default('CURRENT_TIMESTAMP')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', static function (Blueprint $table) {
            $table->date('transaction_date')->after('transaction_type_id')->default(null)->nullable(true)->change();
        });
    }
}
