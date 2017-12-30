<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',64)->comment('问题的标题');
            $table->text('desc')->nullable()->comment('问题的详细内容,即描述');
            $table->unsignedInteger('user_id');
            //$table->unsignedInteger('admin_id');        //是哪个管理员在负责审核这个问题的发布
            $table->string('status')->default('ok');    //记录这个问题的状态,即该问题涉及到黄赌毒这些没有
            $table->timestamps();

            //这句话的意思就是当前这张表的usesr_id其实是users表里id的外键
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
