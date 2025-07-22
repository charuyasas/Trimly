<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice_no')->nullable()->change();
            $table->string('token_no')->nullable()->after('id');
            $table->decimal('received_cash', 10, 2)->nullable()->after('discount_amount');
            $table->decimal('balance', 10, 2)->nullable()->after('received_cash');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('invoice_no');
        });
    }
};
