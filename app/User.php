<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Request;

class User extends Model
{
    //用户注册
    public function singup()
    {
        //dd(Request::has('username'));       //判断前端有没有传入这个参数
       // dd(Request::has('age'));
        //dd(Request::all());

        /*用户注册验证
        $username = Request::get('username');   //有值获取到值，没值为null
        $password = Request::get('password');
        if(!($username && $password))           //获取前端传来的信息
        {
            return ['status'=>0,'msg'=>'用户名和密码不能为空'];
        }*/
        $has_username_and_password = $this->has_username_and_password();
        if(!$has_username_and_password)
            return ['status'=>0,'msg'=>'用户名和密码均不能为空'];
        $username = $has_username_and_password[0];
        $password = $has_username_and_password[1];

        //检验用户名是否唯一
        $username_exists = $this->where('username',$username)->exists();
        //dd($username_exists);       //若存在返回true布尔值;
        if($username_exists)
           return ['status'=>0,'msg'=>'用户名已经存在'];

        //加密密码
        $hashed_password = Hash::make($password);      //这是Laravel自带的,其实也相当于bcrypt
        //dd($password);

        //将数据存入数据库
        $this->username = $username;
        $this->password = $hashed_password;
        if($this->save())
            return ['status'=>1,'id'=>$this->id];
        else
            return ['status'=>0,'msg'=>'db insert failed'];
    }

    public function login()
    {
        //dd(session()->all());     不管你登陆没登陆，都会返回一个token给你
        $has_username_and_password = $this->has_username_and_password();
        if(!$has_username_and_password)
            return ['status'=>0,'msg'=>'用户名和密码不能为空'];
        $username = $has_username_and_password[0];
        $password = $has_username_and_password[1];

        $users = $this->where('username',$username)->first();
        if(!$users)
            return ['status'=>0,'msg'=>'该用户名不存在'];

        $hashed_password = $users->password;
        if(!(Hash::check($password,$hashed_password)))
            return ['status'=>0,'msg'=>'密码错误'];

        //登陆成功，将用户的数据存入session
        session()->put('username',$users->username);
        session()->put('user_id',$users->id);
        return ['status'=>1,'id'=>$users->id];
    }

    public function has_username_and_password()
    {
       // $username = Request::get('username');
       // $password = Request::get('password');
        $username = rq('username');
        $password = rq('password');
        if($username&&$password)
            return [$username,$password];
        else
            return false;
    }

    /*检测用户是否登陆*/
    public function is_logged_in()
    {
        //这里如果session有值,直接返回user_id，如果没有直接返回false(这里的三目运算符看看)
        return session('user_id')? : false;
    }

    /*登出API*/
    public function loggout()
    {
        //将我们所有的session的值清空
        //session()->flush();
        //session()->put('username',null);
        //session()->put('user_id',null);
        session()->forget('username');
        session()->forget('user_id');
       // return redirect('/');  退出登陆返回到首页
        return ['status'=>1];
        //dd(session()->all());
    }

    public function answers()
    {
        return $this->belongsToMany('App\Answer')
                    ->withPivot('vote')
                    ->withTimestamps();
    }
}
