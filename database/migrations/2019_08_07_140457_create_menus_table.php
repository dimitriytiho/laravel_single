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
            $table->string('title')->nullable();
            $table->index('title');
            $table->string('slug')->nullable();
            $table->string('target')->nullable();
            $table->string('item')->nullable();
            $table->string('class')->nullable();
            $table->string('attr')->nullable();
            $table->string('status', 100)->default(config('add.page_statuses')[0] ?? 'inactive');
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
