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

function answer_ins()
{
    return new App\Answer();
}

function comment_ins()
{
    return new App\Comment();
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

Route::get('api/singup',function(){         //注册
    //$user = new App\User;
    return user_ins()->singup();
});

Route::any('api/login',function(){          //登陆
    return user_ins()->login();
});

Route::any('api/loggout',function(){        //登出
    return user_ins()->loggout();
});

Route::any('api/checklogin',function(){
    dd(user_ins()->is_logged_in());
});

Route::any('api/question/addques',function(){         //增加问题
    return ques_ins()->add();
});

Route::any('api/question/change',function(){           //更改问题
    return ques_ins()->change();
});

Route::any('api/question/show',function(){              //查看问题
    return ques_ins()->show();
});

Route::any('api/question/remove',function(){            //删除问题
    return ques_ins()->remove();
});

Route::any('api/answer/add',function(){                 //回答问题
    return answer_ins()->add();
});

Route::any('api/answer/change',function(){              //更新回答
    return answer_ins()->change();
});

Route::any('api/answer/show',function(){
    return answer_ins()->show();
});

Route::any('api/comment/add',function(){
    return comment_ins()->add();
});