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
        Schema::table('users', function(Blueprint $table){
            $table->renameColumn('fullname', 'firstname');
            $table->string('middlename')->after('fullname')->nullable();
            $table->string('lastname')->after('middlename');
            $table->string('nationality')->after('phone_number');
            $table->string('sex')->after('nationality');
            $table->enum('marital_status', ['married', 'single', 'divorced'])->after('sex')->nullable();
            $table->date('date_of_birth')->after('marital_status')->nullable($value = true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table){
            $table->renameColumn('firstname','fullname');
            $table->dropColumn('middlename');
            $table->dropColumn('lastname');
            $table->dropColumn('nationality');
            $table->dropColumn('sex');
            $table->dropColumn('marital_status');
            $table->dropColumn('date_of_birth');
        });
    }
};
