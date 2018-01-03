<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique()->comment('用户名');
            $table->string('email')->unique()->nullable()->comment('邮箱');
            $table->string('phone')->unique()->nullable()->comment('电话号码');
            $table->text('avatar_url')->nullable()->comment('用户头像,存的是是一个url地址');
            $table->string('password');
            $table->text('intro')->nullable();
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
        Schema::dropIfExists('users');
    }
}
