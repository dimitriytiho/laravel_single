<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModifiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modifiers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_id')->unsigned();
            $table->foreign('parent_id')->references('id')->on('modifier_groups');
            $table->string('title', 100);
            $table->index('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->float('price')->nullable();
            $table->enum('default', ['0', '1'])->default('0');
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
        Schema::dropIfExists('modifiers');
    }
}
