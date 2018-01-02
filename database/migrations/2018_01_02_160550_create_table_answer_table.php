<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answer_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('关联用户表');
            $table->unsignedInteger('answer_id')->comment('关联回答表');
            $table->unsignedSmallInteger('vote')->comment('点赞或者点踩，用1或0来表示，所以用unsignedSmallInteger');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('answer_id')->references('id')->on('answers');
            $table->unique(['user_id','answer_id','vote']);     //意思是一个用户不能对一个回答既投了赞同票，也投了反对票,要么点赞，要么点踩
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answer_user');
    }
}
