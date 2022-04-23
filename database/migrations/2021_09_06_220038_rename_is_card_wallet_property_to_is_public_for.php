<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameIsCardWalletPropertyToIsPublicFor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('wallets', static function (Blueprint $table) {
            $table->renameColumn('is_card', 'is_public');
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
            $table->renameColumn('is_public', 'is_card');
        });
    }
}
