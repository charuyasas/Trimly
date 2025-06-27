<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->string('employee_no');
            $table->string('customer_no');
            $table->decimal('grand_total', 10, 2);
            $table->integer('discount_percentage')->default(0);
            $table->integer('discount_amount')->default(0); 
            $table->tinyInteger('status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
