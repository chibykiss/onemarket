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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('shop_number');
            $table->string('shop_address');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('gotten_via')->nullable();
            $table->unsignedBigInteger('guarantor')->nullable();
            $table->integer('known_for')->nullable();
            $table->string('company_name')->nullable();
            $table->boolean('guranteed')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('owner_id')->references('id')->on('owners');
            $table->foreign('guarantor')->references('id')->on('owners');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
    }
};
