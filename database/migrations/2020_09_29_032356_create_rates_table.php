<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->id();

            // * Attributes
            $table->string('price');
            $table->integer('transaction_type');
            $table->integer('rate_definition_id')->nullable();
            $table->string('start_at')->nullable();
            $table->string('end_at')->nullable();

            // * Relationships
            $table->unsignedInteger('product_id');

            // * Remote ids
            $table->integer('store_id');
            $table->unsignedInteger('crms_id');

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
        Schema::dropIfExists('rates');
    }
}
