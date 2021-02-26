<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->index('title');
            $table->string('slug')->unique();
            $table->timestamp('start')->nullable();
            $table->timestamp('end')->nullable();
            $table->string('type')->default(config('shop.promo_type')[0] ?? 'all_products');
            $table->float('price')->unsigned()->nullable();
            $table->float('old_price')->unsigned()->nullable();
            $table->float('discount')->unsigned()->nullable();
            $table->text('description')->nullable();
            $table->text('body')->nullable();
            $table->string('status', 100)->default(config('add.page_statuses')[0]);
            $table->smallInteger('sort')->unsigned()->default('500');
            $table->string('img')->nullable()->default(config('admin.imgProductDefault'));
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
        Schema::dropIfExists('promos');
    }
}
