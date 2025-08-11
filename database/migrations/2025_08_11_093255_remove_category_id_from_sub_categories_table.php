<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }

    public function down(): void {
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->uuid('category_id')->after('id');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }
};

