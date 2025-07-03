<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('title_accounts', function (Blueprint $table) {
            $table->increments('title_code');
            $table->string('title_account');
            $table->unsignedInteger('main_code');
            $table->unsignedInteger('heading_code');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('main_code')->references('main_code')->on('main_accounts')->onDelete('cascade');
            $table->foreign('heading_code')->references('heading_code')->on('heading_accounts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        //
    }
};
