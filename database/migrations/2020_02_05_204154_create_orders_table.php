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

            // Скидки
            $table->float('discount')->unsigned()->nullable();
            $table->string('discount_code')->nullable();
            $table->float('discount_score')->unsigned()->default('0');

            // Доставка
            $table->string('delivery')->nullable();
            $table->float('delivery_sum')->unsigned()->nullable();

            // Оплата
            $table->string('bank_id')->nullable();
            $table->string('payment')->default(config('shop.payment')[0]['title'] ?? 'cash_courier');
            $table->enum('paid', ['0', '1'])->default('0');

            $table->smallInteger('qty')->unsigned()->nullable();
            $table->float('sum')->unsigned()->nullable();
            $table->string('user_source', 100)->nullable();
            $table->text('user_utm')->nullable();
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
