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

    public function change()
    {
        //第一,检查用户是否登陆
        if(!user_ins()->is_logged_in())
            return ['status'=>0,'msg'=>'请您登陆'];

        //更新问题的话，要指定哪一条,这个id是之前提出问题的id
        if(!rq('id'))
            return ['status'=>0,'msg'=>'不知道文章的id'];

        $question = $this->find(rq('id'));

        if(!$question)
            return ['status'=>0,'msg'=>'这个问题不存在'];

        if($question->user_id != session('user_id'))
            return ['status'=>0,'msg'=>'您没有权限修改这个问题,因为您不是这个问题的提问者'];

        if(rq('title'))
            $question->title = rq('title');

        if(rq('desc'))
            $question->desc = rq('desc');

        return $question->save()?
            ['status'=>1]:
            ['status'=>0,'msg'=>'更新问题失败'];
    }

    public function show()
    {
        /*判断请求参数中是否有id,如果有id,就直接返回id对应的问题*/
       if(rq('id'))
           return ['staus'=>1,'data'=>$this->find(rq('id'))];

        $limit = rq('limit') ? :15;     //这个limit是让一页显示多少条问题的
        //$skip = (rq('page')?:0)* $limit;      这个是我自己写的,感觉也没问题
        $skip = (rq('page')?rq('page')-1 :0) * $limit;      //分页

        /*构建query并返回collection数据*/
        $res = $this->orderBy('created_at')
                    ->limit($limit)
                    ->skip($skip)
                    ->get()         //get里面可以给你想要显示数据的字段
                    ->keyBy('id');  //给数据一个键名

        return ['status'=>1,'data'=>$res];
    }

    public function remove()
    {
        /*检查用户有没有登陆*/
        if(!user_ins()->is_logged_in())
            return ['status'=>0,'msg'=>'请您登陆'];

        /*检查传参中有没有id*/
        if(!rq('id'))
            return ['status'=>0,'msg'=>'没有问题id'];

        $question = $this->find(rq('id'));
        if(!$question)
            return ['status'=>0,'msg'=>'没有该问题'];

        /*检查该问题是否为该用户的持有者*/
        if($question->user_id != session('user_id'))
            return ['status'=>0,'msg'=>'您没有权限'];

        return $question->delete()?
            ['status'=>1]:
            ['status'=>0,'msg'=>'删除成功'];
    }
}
