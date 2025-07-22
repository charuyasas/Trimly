<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('grns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('grn_number')->unique();
            $table->date('grn_date');
            $table->uuid('supplier_id');
            $table->string('supplier_invoice_number');
            $table->string('grn_type'); // Profit Margin / Discount Based
            $table->text('note')->nullable();
            $table->decimal('total_before_discount', 12, 2)->default(0);
            $table->decimal('total_foc', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->boolean('is_percentage')->default(false);
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('grns');
    }
};
