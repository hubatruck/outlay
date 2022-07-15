<?php

use App\Models\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Transaction::query()->update(['amount' => DB::raw('`amount` * 100')]);
        Schema::table('transactions', static function (Blueprint $table) {
            $table->bigInteger('amount')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('transactions', static function (Blueprint $table) {
            $table->float('amount')->default(0)->change();
        });
        Transaction::query()->update(['amount' => DB::raw('`amount` / 100')]);
    }
};
