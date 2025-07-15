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
        Schema::create('stock_sheets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('item_code');
            $table->string('ledger_code');
            $table->string('description');
            $table->integer('credit')->default(0);
            $table->integer('debit')->default(0);
            $table->timestamps();

            $table->foreign('item_code')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('ledger_code')->references('ledger_code')->on('posting_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_sheet');
    }
};
