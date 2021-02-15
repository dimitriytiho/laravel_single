<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColorProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('color_product', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('color_id')->unsigned();
            $table->bigInteger('product_id')->unsigned();
            $table->foreign('color_id')->references('id')->on('colors');
            $table->foreign('product_id')->references('id')->on('products');
            $table->string('title');
            $table->string('status', 100)->default(config('add.page_statuses')[0] ?? 'inactive');
            $table->float('price')->unsigned()->nullable();
            $table->float('old_price')->unsigned()->nullable();
            $table->float('discount')->unsigned()->nullable();
            $table->smallInteger('sort')->unsigned()->default('500');
            $table->string('article')->nullable();
            $table->string('size')->nullable();
            $table->string('weight')->nullable();
            $table->text('description')->nullable();
            $table->text('body')->nullable();
            $table->string('img')->nullable()->default(config('admin.imgProductDefault'));
            $table->bigInteger('popular')->unsigned()->default('0');
            $table->string('labels')->nullable(); // Через запятую записывать id лэйблов
            $table->softDeletes();
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
        Schema::dropIfExists('color_product');
    }
}
