<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_wallet_id');
            $table->foreign('from_wallet_id')->references('id')->on('wallets');
            $table->unsignedBigInteger('to_wallet_id');
            $table->foreign('to_wallet_id')->references('id')->on('wallets');
            $table->float('amount');
            $table->string('description')->nullable();
            $table->dateTime('transfer_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
}
