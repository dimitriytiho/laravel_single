<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortfoliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->index('title');
            $table->string('slug')->unique();
            $table->string('status', 100)->default(config('add.page_statuses')[0] ?? 'inactive');
            $table->smallInteger('sort')->unsigned()->default('500');
            $table->text('description')->nullable();
            $table->text('body')->nullable();
            $table->text('work')->nullable();
            $table->string('client')->nullable();
            $table->string('term')->nullable();
            $table->string('preview')->nullable();
            $table->string('img')->nullable();
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
        Schema::dropIfExists('portfolios');
    }
}
