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
        Schema::create('sources', function (Blueprint $table) {
            $table->increments('source_id');
            $table->string('source_name')->unique();
            $table->integer('beginning_balance');
            $table->integer('source_ending_balance');
            $table->boolean('source_is_cancelled')->default(false);
            $table->unsignedInteger('source_user_id');


            $table->foreign('source_user_id')->references('user_id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sources');
    }
};
