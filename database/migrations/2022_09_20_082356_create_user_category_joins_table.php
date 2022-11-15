<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_category_joins', function (Blueprint $table) {
           $table->id();
           $table->unsignedBigInteger('user_id');
           $table->unsignedBigInteger('UserCategory_id');
           $table->foreign('user_id')->references('id')->on('users');
           $table->foreign('UserCategory_id')->references('id')->on('user_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_category_joins');
    }
};
