<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAppUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_users', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('first_name',255);
            $table->string('nik_name',255);
            $table->string('password',255);
            $table->string('sex',3);
            $table->string('age',10);
            $table->string('status',1);
            $table->string('imei',255);
            $table->string('ads_disabled',1);
            $table->string('image',255);
            $table->integer('rating')->default(0);
            $table->string('refresh_token')->default(null);
            $table->timestamp("date_update_rating");
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
        Schema::drop('app_users');
    }
}
