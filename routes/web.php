<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
function user_ins()
{
    //实例化User
    return new App\User();
}

function ques_ins()
{
    return new App\Question();
}

function rq($key=null,$default=null)
{
    if(!$key)
        return Request::all();      //如果用户没有传参数进来，我们就获取它前端的所有数据
    return Request::get($key,$default);      //如果有，我们就获取到它的key
}


Route::get('/', function () {
    return view('welcome');
});

Route::get('api/user',function(){
    //$user = new App\User;
    return user_ins()->singup();
});

Route::any('api/login',function(){
    return user_ins()->login();
});

Route::any('api/loggout',function(){
    return user_ins()->loggout();
});

Route::any('api/checklogin',function(){
    dd(user_ins()->is_logged_in());
});

Route::any('api/create/addques',function(){
    return ques_ins()->add();
});