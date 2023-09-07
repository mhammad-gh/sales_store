<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellBuysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sell__buys', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('usersell_id')->unsigned()->nullable();
            $table->foreign('usersell_id')->references('id')->on('users')->onDelete('set null');
            $table->bigInteger('userbuy_id')->unsigned()->nullable();
            $table->foreign('userbuy_id')->references('id')->on('users')->onDelete('set null');
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->index('id');
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
        Schema::dropIfExists('sell__buys');
    }
}
