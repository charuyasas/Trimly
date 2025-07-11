<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
        Schema::create('grn_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('grn_id');
            $table->uuid('item_id');
            $table->string('item_name');
            $table->decimal('qty', 12, 2);
            $table->decimal('foc', 12, 2)->default(0);
            $table->decimal('price', 12, 2);
            $table->decimal('margin', 5, 2)->nullable();
            $table->decimal('discount', 5, 2)->nullable();
            $table->decimal('final_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            $table->foreign('grn_id')->references('id')->on('grns')->onDelete('cascade');
        });
    }

        public function down(): void
    {
        Schema::dropIfExists('grn_items');
    }
};
