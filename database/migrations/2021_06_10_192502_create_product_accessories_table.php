<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_accessories', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('relatable_id')->nullable();
            $table->string('relatable_type')->nullable();
            $table->integer('related_id')->nullable();
            $table->string('related_type')->nullable();
            $table->string('related_name')->nullable();
            $table->longText('related_icon_url')->nullable();
            $table->longText('related_icon_thumb_url')->nullable();
            $table->string('type')->nullable();
            $table->string('parent_transaction_type')->nullable();
            $table->string('parent_transaction_type_name')->nullable();
            $table->string('item_transaction_type')->nullable();
            $table->string('item_transaction_type_name')->nullable();
            $table->string('inclusion_type')->nullable();
            $table->string('inclusion_type_name')->nullable();
            $table->string('mode')->nullable();
            $table->string('mode_name')->nullable();
            $table->string('quantity')->nullable();
            $table->string('zero_priced')->nullable();
            $table->string('sort_order')->nullable();
            $table->integer('crms_id')->nullable(); // ? Current RMS identifier
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
        Schema::dropIfExists('product_accessories');
    }
}
