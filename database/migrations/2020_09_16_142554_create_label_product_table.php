<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLabelProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('label_product', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('label_id')->unsigned();
            $table->bigInteger('product_id')->unsigned();
            $table->foreign('label_id')->references('id')->on('labels');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('label_product');
    }
}
