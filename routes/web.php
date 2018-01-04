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

function success($data_to_merge =[])     //正确返回正确信息
{
    $data = ['status'=>1,'data'=>[]];
    if($data_to_merge)              //这一步的作用是如果想要返回数据,但是有时返回的数据对应的字段不同的做法
        $data['data'] = array_merge($data['data'], $data_to_merge);
    return $data;
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

Route::any('api/user_information',function(){       //个人信息
    return user_ins()->user_information();
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

Route::any('api/answer/show',function(){                //查看回答
    return answer_ins()->show();
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