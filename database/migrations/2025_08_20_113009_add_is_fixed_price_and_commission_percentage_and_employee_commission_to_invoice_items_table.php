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
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->boolean('is_fixed_price')->default(true)->after('sub_total');
            $table->unsignedInteger('commission_percentage')->nullable()->after('is_fixed_price');
            $table->decimal('employee_commission', 10, 2)->default(0)->after('commission_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['is_fixed_price','commission_percentage','employee_commission']);
        });
    }
};
