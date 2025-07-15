<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posting_accounts', function (Blueprint $table) {
            $table->increments('posting_code');
            $table->string('posting_account');
            $table->unsignedInteger('main_code');
            $table->unsignedInteger('heading_code');
            $table->unsignedInteger('title_code');
            $table->string('ledger_code')->unique()->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('main_code')->references('main_code')->on('main_accounts')->onDelete('cascade');
            $table->foreign('heading_code')->references('heading_code')->on('heading_accounts')->onDelete('cascade');
            $table->foreign('title_code')->references('title_code')->on('title_accounts')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE posting_accounts AUTO_INCREMENT = 1000');
    }

    public function down(): void
    {
        //
    }
};
