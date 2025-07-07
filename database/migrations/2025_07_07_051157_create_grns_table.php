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
            $table->foreignUuid('supplier_id')->constrained()->onDelete('cascade');
            $table->string('supplier_invoice_number');
            $table->string('grn_type'); // 'Profit Margin' or 'Discount Based'
            $table->string('store_location')->nullable();
            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('grns');
    }
};
