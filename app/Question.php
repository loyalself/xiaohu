<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /**
     * 创建问题的API
     * 1.检测用户是否登陆
     * 2.登陆成功后，要提交标题
     * @return array
     */
    public function add()
    {
        //dd(rq());         什么参数都没有，就是一个空数组
        /*首先检测用户登陆没有，没有登陆不能发布问题*/
        if(!user_ins()->is_logged_in())
           return ['status'=>0,'msg'=>'请您登陆'];

        if(!rq('title'))
            return ['status'=>0,'msg'=>'请输入标题'];

        $this->title = rq('title');
        $this->user_id = session('user_id');
        if(rq('desc')) $this->desc = rq('desc');

        return $this->save() ?
             ['status'=>1,'id'=>$this->id]:
             ['status'=>0,'msg'=>'问题提交失败'];
    }
}
