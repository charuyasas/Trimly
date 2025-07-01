<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('invoice_id');
            $table->string('item_id');
            $table->string('item_description');
            $table->integer('quantity')->default(1);
            $table->decimal('amount', 10, 2);
            $table->integer('discount_percentage')->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('sub_total', 10, 2);
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
