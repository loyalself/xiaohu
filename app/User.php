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

    /**
     * 返回用户的信息
     * @return array
     */
    public function user_information()
    {
        if(!rq('id'))
            return error('没有用户id');

        $get = ['id','username','avatar_url','intro'];      //可以公开的字段
        /*find()第一个参数是id，后面再跟上你想要的字段*/
        $user = $this->find(rq('id'),$get);
        $data = $user->toArray();
        $answer_count = answer_ins()->where('user_id',rq('id'))->count();
        $question_count = ques_ins()->where('user_id',rq('id'))->count();
        $data['answer_count'] = $answer_count;
        $data['question_count'] = $question_count;
        return success($data);
        //$answer_count = $user->answers()->count();  //该用户回答问题的数量,这是通过模型关联的方法进行查询
        //$question_count = $user->questions()->count(); //该用户提过问题的数量(在这之前，并没有创建过用户与问题之间的关联表)
        //dd($answer_count,$question_count);
    }
    /**
     * 用户登陆API
     * @return array
     */
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
        //return ['status'=>1,'id'=>$users->id];
        //return success(['ggg'=>66699]);  这个是测试用的，看能不能返回数据
        return success(['id'=>$users->id]);
    }

    /**
     * 判断用户有没有传入用户名和密码
     * @return array|bool
     */
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
        //return session('user_id')? : false;
        return is_logged_in();
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

    public function questions()
    {
        return $this->belongsToMany('App\Question')
            ->withPivot('vote')
            ->withTimestamps();
    }

    /**
     * 修改密码
     */
    public function change_password()
    {
        /*验证是否登陆*/
        if(!$this->is_logged_in())
           // return ['status'=>0,'msg'=>'请您登陆'];
            return error('请您登陆666');

        if(!rq('old_password')|| !rq('new_password'))   //这种非或的写法就是两个条件都必须满足
            return ['status'=>0,'msg'=>'请输入旧密码和新密码'];

        /*将用户信息查找出来*/
        $user = $this->find(session('user_id'));

        /*检查用户输入的旧密码是否正确*/
        if( !Hash::check(rq('old_password'),$user->password))
            return ['status'=>0,'msg'=>'旧密码错误'];

        $user->password = bcrypt(rq('new_password'));

        return $user->save()?
            ['status'=>1]:
            ['status'=>0,'msg'=>'用户修改密码失败'];

    }

    /**
     * 找回密码
     */
    public function reset_password()
    {
       if($this->is_robot())
           return error('短信发送频繁');

        if(!rq('phone'))
            return error('请输入电话号码');

        $user = $this->where('phone',rq('phone'))->first();
        if(!$user)
            return error('该电话号码不存在');
        /*生成验证码*/
        $captcha = $this->generate_captcha();
        /*将生成的验证码存入到数据库,如果保存成功,就发送短信*/
        $user->phone_captcha = $captcha;
        if($user->save())
        {
            $this->send_sms();      //发送短信
            /*为下一次机器人调用做准备*/
            $this->update_robot_time();
            return success();
        }else
        {
            return error('数据插入失败');
        }
    }

    /**
     * 1.验证验证码机制，如果验证成功了，就让他重置密码
     * 2.验证频繁,有可能是在暴力破解
     */
    public function validate_reset_pasword()
    {
        if($this->is_robot(3))
            return error('短信发送频繁');

        if(!rq('phone')|| !rq('phone_captcha') || !rq('new_password'))
            return error('电话号码、验证码和密码都不能为空');

        /*根据用户传进来的电话号码和验证码去数据库进行比对，看有没有该用户*/
        $user = $this->where(['phone'=>rq('phone'),'phone_captcha'=>rq('phone_captcha')])
                     ->first();
        if(!$user)
            return error('电话号码或者验证码错误');

        /*加密新密码*/
        $user->password = bcrypt(rq('new_password'));
        $this->update_robot_time();
        return $user->save()? success():error('重置密码失败');
    }
    /**
     * 生成验证码
     */
    public function generate_captcha()
    {
        return rand(1000,9000);
    }
    /**
     * 发送短信验证码
     */
    public function send_sms()
    {
        return true;
    }
    /*检查是否是机器人,也就是有人暴力使用一些手段频繁发送短信验证码*/
    public function is_robot($time=10)
    {
        /*如果session中没有last_action_time,说明短信接口从未调用过*/
        if(!session('last_action_time'))
            return false;

        $current_time = time();
        $last_active_time = session('last_action_time');
        $elapsed = $current_time - $last_active_time;
        return !($elapsed > $time);
    }
    /*更新机器人行为时间*/
    public function update_robot_time()
    {
        session()->put('last_action_time',time());
    }

    /*检查用户名是否存在*/
    public function exist()
    {
        return success(['count'=>$this->where(rq())->count()]);
    }
}
