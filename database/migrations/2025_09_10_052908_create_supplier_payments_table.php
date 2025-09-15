<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('supplier_id');
            $table->enum('payment_type', ['cash', 'bank']);
            $table->json('payments')->nullable();
            $table->decimal('amount', 12, 2);
            $table->uuid('bank_id')->nullable();
            $table->string('bank_slip_no')->nullable();
            $table->date('date');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');

            $table->index(['supplier_id', 'payment_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
