<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModifierGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modifier_groups', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->index('title');
            $table->string('slug')->unique();
            $table->string('type', 100)->default(config('shop.modifier_type')[0]);
            $table->string('function', 100)->default(config('shop.function')[0]);
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
        Schema::dropIfExists('modifier_groups');
    }
}
