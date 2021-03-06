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
function paginate($page=1,$limit=16)
{
    $limit = $limit ? : 16;
    $skip = ($page ? $page-1 :0) * $limit;
    return [$limit,$skip];
}

function error($msg=null)          //返回错误信息,给一个默认值null的目的是如果有人不想要这个错误信息呢
{
    return ['status'=>0,'msg'=>$msg];
}

function success($data_to_add =[])     //正确返回正确信息
{
    $data = ['status'=>1,'data'=>[]];
    if($data_to_add)
        $data['data'] =  $data_to_add;
    return $data;
}

/*判断用户是否登陆*/
function is_logged_in()
{
    //这里如果session有值,直接返回user_id，如果没有直接返回false(这里的三目运算符看看)
    return session('user_id')? : false;
}

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
    //return view('welcome');
    return view('index');
});

Route::any('api/signup',function(){         //注册
    //$user = new App\User;
    return user_ins()->singup();
});

Route::any('api/login',function(){          //登陆
    return user_ins()->login();
});

Route::any('api/user/read',function(){       //个人信息
    return user_ins()->read();
});

Route::any('api/logout',function(){        //登出
    return user_ins()->loggout();
});



Route::any('api/checklogin',function(){
    dd(user_ins()->is_logged_in());
});

Route::any('api/question/add',function(){         //增加问题
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

Route::any('api/answer/show',function(){                //查看回答
    return answer_ins()->show();
});

Route::any('api/answer/remove',function(){
    return answer_ins()->remove();
});

Route::any('api/answer/vote',function(){
    return answer_ins()->vote();
});


Route::any('api/comment/add',function(){                //添加评论
    return comment_ins()->add();
});

Route::any('api/comment/show',function(){               //查看评论
    return comment_ins()->show();
});

Route::any('api/comment/remove',function(){             //删除评论
    return comment_ins()->remove();
});

Route::any('api/timeline','CommonController@timeline');     //通用,专栏API

Route::any('api/user/change_password',function(){           //修改密码
    return user_ins()->change_password();
});
Route::any('api/user/reset_password',function(){
    return user_ins()->reset_password();
});
Route::any('api/user/validate_reset_pasword',function(){
    return user_ins()->validate_reset_pasword();
});

Route::any('api/user/exist',function(){
    return user_ins()->exist();
});

Route::get('tpl/page/home',function(){
    return view('page.home');
});
Route::get('tpl/page/signup',function(){
    return view('page.signup');
});
Route::get('tpl/page/login',function(){
    return view('page.login');
});
Route::get('tpl/page/question_add',function(){
    return view('page.question_add');
});
Route::get('tpl/page/user',function(){
    return view('page.user');
});
Route::get('tpl/page/question_detail',function(){
    return view('page.question_detail');
});