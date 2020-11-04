<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('note')->nullable(); // Для комментариев администратора
            $table->string('status', 100)->default(config('admin.order_statuses')[0]);
            $table->text('message')->nullable(); // Сообщение от пользователя
            $table->string('delivery', 100)->nullable();
            $table->float('delivery_sum')->nullable();
            $table->float('discount')->nullable();
            $table->string('discount_code')->nullable();
            $table->smallInteger('qty')->nullable();
            $table->float('sum')->nullable();
            $table->string('ip', 100)->nullable();
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
        Schema::dropIfExists('orders');
    }
}
