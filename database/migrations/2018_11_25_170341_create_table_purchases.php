<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePurchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid');
            $table->string('token',255);
            $table->integer('consumptionState');
            $table->string('developerPayload',255);
            $table->string('kind',255);
            $table->string('orderId',255);
            $table->integer('purchaseState');
            $table->timestamp('purchaseTimeMillis');
            $table->integer('purchaseType');
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
        Schema::drop('purchases');
    }
}
