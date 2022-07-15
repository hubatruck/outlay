<?php

use App\Models\Transfer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // migrate data so does not get chopped
        Transfer::query()->update(['amount' => DB::raw('`amount` * 100')]);
        Schema::table('transfers', static function (Blueprint $table) {
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
        Schema::table('transfers', static function (Blueprint $table) {
            $table->float('amount')->default(0)->change();
        });
        // restore data to former representation
        Transfer::query()->update(['amount' => DB::raw('`amount` / 100')]);
    }
};
