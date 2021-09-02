<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accessories', function (Blueprint $table) {

            $table->id();

            // * Attributtes
            $table->integer('sort_order')->nullable();
            $table->string('quantity')->nullable();
            $table->boolean('zero_priced')->default(false);

            // * Enums
            $table->integer('inclusion_type')->nullable();
            $table->integer('parent_transaction_type')->nullable();
            $table->integer('item_transaction_type')->nullable();
            $table->integer('mode')->nullable();

            // * Relations
            $table->unsignedInteger('relatable_id'); // ? Parent Product
            $table->unsignedInteger('related_id'); // ? Child Product

            // * Remote ids
            $table->integer('crms_id'); // ? Current RMS identifier

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
        Schema::dropIfExists('accessories');
    }
}
