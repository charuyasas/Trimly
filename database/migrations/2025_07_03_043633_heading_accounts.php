<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heading_accounts', function (Blueprint $table) {
            $table->increments('heading_code');
            $table->string('heading_account');
            $table->unsignedInteger('main_code');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('main_code')->references('main_code')->on('main_accounts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        //
    }
};
