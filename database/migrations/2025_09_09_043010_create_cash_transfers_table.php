<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cash_transfers', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('credit_account');
            $table->string('debit_account');
            $table->string('description');
            $table->decimal('amount')->default(0.00);
            $table->unsignedBigInteger('user_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('credit_account')->references('ledger_code')->on('posting_accounts');
            $table->foreign('debit_account')->references('ledger_code')->on('posting_accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transfer');
    }
};
