<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_no')->unique();
            $table->uuid('employee_no');
            $table->uuid('customer_no');
            $table->decimal('grand_total', 10, 2);
            $table->integer('discount_percentage')->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->tinyInteger('status');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('employee_no')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('customer_no')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
