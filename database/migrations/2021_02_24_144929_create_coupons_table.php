<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('coupon');
            $table->index('coupon');
            $table->timestamp('start')->nullable();
            $table->timestamp('end')->nullable();
            $table->float('price')->unsigned()->nullable();
            $table->float('discount')->unsigned()->nullable();
            $table->text('description')->nullable();
            $table->string('status', 100)->default(config('add.page_statuses')[0]);
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
        Schema::dropIfExists('coupons');
    }
}
