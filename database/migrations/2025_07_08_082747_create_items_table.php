<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('description');
            $table->string('rack_location')->nullable();
            $table->uuid('supplier_id');
            $table->uuid('category_id');
            $table->uuid('sub_category_id')->nullable();
            $table->enum('measure_unit', ['Kg', 'g', 'unit', 'l', 'ml'])->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('list_price', 10, 2)->nullable();
            $table->decimal('retail_price', 10, 2)->nullable();
            $table->decimal('wholesale_price', 10, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('items');
    }
};
