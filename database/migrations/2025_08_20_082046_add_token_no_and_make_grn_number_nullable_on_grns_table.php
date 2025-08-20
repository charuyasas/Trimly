<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('grns', function (Blueprint $table) {
            $table->string('token_no')->after('id')->unique();
            $table->string('grn_number')->nullable()->change();
        });
    }

    public function down(): void {
        Schema::table('grns', function (Blueprint $table) {
            $table->dropUnique(['token_no']);
            $table->dropColumn('token_no');
            $table->string('grn_number')->nullable(false)->change();
        });
    }
};
