<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
         Schema::create('main_accounts', function (Blueprint $table) {
            $table->increments('main_code');
            $table->string('main_account');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        //
    }
};
