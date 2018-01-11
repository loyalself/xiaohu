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
            return ['status'=>0,'msg'=>'没有获取到问题的id'];

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

    /*根据用户id查询出当前用户在问题表中所持有的数据*/
    public function show_by_user_id($user_id)
    {
        $user = user_ins()->find($user_id);
        if(!$user) return error('该用户不存在');
        $res = $this->where('user_id',$user_id)
            ->get()
            ->keyBy('id');
        //dd($res->toArray());
        return success($res->toArray());        //这返回的是用户提了多少个问题
    }

    /*查看问题API*/
    public function show()
    {
        /*判断请求参数中是否有id,如果有id,就直接返回id对应的问题*/
       if(rq('id'))
       {
           $r = $this->with('answers_with_user_info')
               ->find(rq('id'));
           return ['status'=>1,'data'=>$r];
       }

       if(rq('user_id'))
       {
           /*这里的self的写法是写在user的后面 eg: user/self  */
           $user_id = rq('user_id') === 'self'?
               session('user_id'):rq('user_id');
           return $this->show_by_user_id($user_id);
       }

        //$limit = rq('limit') ? :15;     //这个limit是让一页显示多少条问题的
        //$skip = (rq('page')?:0)* $limit;      这个是我自己写的,感觉也没问题
        //$skip = (rq('page')?rq('page')-1 :0) * $limit;      //分页

        list($limit,$skip) = paginate(rq('page'),rq('limit'));

        /*构建query并返回collection数据*/
        $res = $this->orderBy('created_at')
                    ->limit($limit)
                    ->skip($skip)
                    ->get()         //get里面可以给你想要显示数据的字段
                    ->keyBy('id');  //给数据一个键名

        return ['status'=>1,'data'=>$res];
    }

    /**删除问题API
     * @mix 首先检查用户是否登陆
     * @param id(这是问题的id,首先你要知道删除的是哪条问题)
     * @mix 检查该id有没有对应的问题,
     *       检查该问题是否为该用户的持有者
     * @return array
     */
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

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function answers()
    {
        return $this->hasMany('App\Answer');
    }

    public function answers_with_user_info()
    {
        return $this->answers()
                    ->with('user')
                    ->with('users');
    }
}
