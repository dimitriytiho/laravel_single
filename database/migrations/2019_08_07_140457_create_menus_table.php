<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('belong_id')->unsigned();
            $table->foreign('belong_id')->references('id')->on('menu_groups')->onDelete('cascade');
            $table->bigInteger('parent_id')->default('0')->unsigned();
            $table->string('title', 100)->nullable();
            $table->index('title');
            $table->string('slug')->nullable();
            $table->string('target', 100)->nullable();
            $table->string('item', 100)->nullable();
            $table->string('class', 100)->nullable();
            $table->string('attr', 100)->nullable();
            $table->string('status', 100)->default(config('add.page_statuses')[0]);
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
        Schema::dropIfExists('menus');
    }
}
