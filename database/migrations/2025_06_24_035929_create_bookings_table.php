<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('customer_id');
            $table->uuid('employee_id');
            $table->uuid('service_id');

            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');

            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('service_id')->references('id')->on('services');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
