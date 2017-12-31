<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content')->comment('评论的内容');
            $table->unsignedInteger('user_id')->comment('哪个人评论的');
            $table->unsignedInteger('question_id')->nullable()->comment('我们不知道用户是评论了问题还是评论了回单内容,所以允许为空');
            $table->unsignedInteger('answer_id')->nullable();
            $table->unsignedInteger('reply_to')->nullable()->comment('用户也有可能评论了这条评论');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('question_id')->references('id')->on('questions');
            $table->foreign('answer_id')->references('id')->on('answers');
            $table->foreign('reply_to')->references('id')->on('comments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
