<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('belong_id')->unsigned();
            $table->foreign('belong_id')->references('id')->on('menu_names')->onDelete('cascade');
            $table->integer('parent_id')->default('0')->unsigned();
            $table->string('title', 64)->nullable();
            $table->index('title');
            $table->string('slug', 255)->nullable();
            $table->string('target', 64)->nullable();
            $table->string('item', 64)->nullable();
            $table->string('class', 64)->nullable();
            $table->string('attr', 64)->nullable();
            $table->string('status', 32)->default(config('add.page_statuses')[0]);
            $table->smallInteger('sort')->unsigned()->default('500');
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
        Schema::dropIfExists('menu');
    }
}
