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
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('transaction_id');
            $table->unsignedInteger('transaction_user_id');
            $table->unsignedInteger('transaction_source_id');
            $table->unsignedInteger('transaction_type_id');
            $table->date('transaction_date');
            $table->integer('transaction_total');
            $table->string('transaction_description');
            $table->boolean('transaction_is_cancelled')->default(false);
            $table->timestamps();

            $table->foreign('transaction_user_id')->references('user_id')->on('users');
            $table->foreign('transaction_source_id')->references('source_id')->on('sources');
            $table->foreign('transaction_type_id')->references('type_id')->on('types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
