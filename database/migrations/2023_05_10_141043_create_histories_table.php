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
        Schema::create('histories', function (Blueprint $table) {
            $table->increments('history_id');
            $table->unsignedInteger('history_transaction_id')->nullable();
            $table->integer('history_transaction_total');
            $table->integer('history_ending_balance');
            $table->integer('history_source_balance');
            $table->unsignedInteger('history_type_id');
            $table->unsignedInteger('history_source_id')->nullable();
            $table->string('action');
            $table->timestamps();

            $table->foreign('history_transaction_id')->references('transaction_id')->on('transactions');
            $table->foreign('history_type_id')->references('type_id')->on('types');
            $table->foreign('history_source_id')->references('source_id')->on('sources');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('histories');
    }
};
