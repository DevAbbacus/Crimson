<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // * Attributes
            $table->string('name');
            $table->string('buffer_percent')->nullable();
            $table->string('replacement_charge')->nullable();
            $table->string('weight')->nullable();
            $table->string('barcode')->nullable();
            $table->longText('description')->nullable();
            $table->double('purchase_price')->nullable();
            $table->double('sub_rental_price')->nullable();
            $table->boolean('active');
            $table->boolean('accessory_only');
            $table->boolean('discountable');
            $table->boolean('system')->default(false);
            $table->json('tag_list')->nullable();
            $table->json('custom_fields')->nullable();

            // * Enums
            $table->integer('allowed_stock_type');
            $table->integer('stock_method');
            $table->integer('post_rent_unavailability')->nullable();

            // * Relationships
            $table->unsignedInteger('product_group_id')->nullable();
            $table->unsignedInteger('user_id');

            // * Remote ids
            $table->unsignedInteger('crms_id');
            
            // * Remote required
            $table->unsignedInteger('sale_revenue_group_id');
            $table->unsignedInteger('purchase_cost_group_id');

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
        Schema::dropIfExists('products');
    }
}
