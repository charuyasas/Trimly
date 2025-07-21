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
        Schema::create('sidebar_links', function (Blueprint $table) {
            $table->id();
            $table->string('permission_name'); // Must match Spatie permission name
            $table->string('display_name');    // Shown in sidebar
            $table->string('url');             // Route or URL
            $table->string('icon_path')->nullable(); // Path to icon
            $table->unsignedBigInteger('parent_id')->nullable(); // For nested menus
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sidebar_links');
    }
};
