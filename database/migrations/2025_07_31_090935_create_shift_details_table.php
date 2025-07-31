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
        Schema::create('shift_details', function (Blueprint $table) {
            $table->uuid('id');
            $table->unsignedBigInteger('user_id');
            $table->id('shift_id')->primary();
            $table->boolean('status')->default(true);
            $table->dateTime('shift_in_time');
            $table->decimal('opening_cash_in_hand', 10, 2)->default(0.00);
            $table->dateTime('shift_off_time')->nullable();
            $table->decimal('day_end_cash_in_hand', 10, 2)->default(0.00);
            $table->decimal('total_daily_sales', 10, 2)->default(0.00);
            $table->decimal('total_daily_expenses', 10, 2)->default(0.00);
            $table->decimal('cash_shortage', 10, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_details');
    }
};
