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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ledger_code');
            $table->string('reference_type');
            $table->string('reference_id');
            $table->decimal('credit')->default(0.00);
            $table->decimal('debit')->default(0.00);
            $table->timestamps();

            $table->foreign('ledger_code')->references('ledger_code')->on('posting_accounts')->onDelete('cascade');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
