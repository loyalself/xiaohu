<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    /**添加回答问题的API
     * @return array
     */
    public function add()
    {
        if(!user_ins()->is_logged_in())
            return ['status'=>0,'msg'=>'请您登陆'];

        if(!rq('question_id') || !rq('content'))
            return ['status'=>0,'msg'=>'没有问题id和回答内容'];

        $ques_exists = ques_ins()->find(rq('question_id'));
        if(!$ques_exists)
            return ['status'=>0,'msg'=>'问题不存在'];

        //count看看满足这两个条件的有多少行
        $count = $this->where(['question_id'=>rq('question_id'),'user_id'=>session('user_id')])->count();
        if($count)
            return ['status'=>0,'msg'=>'您已经回答过该问题了，不能重复回答'];

        $this->content = rq('content');
        $this->question_id = rq('question_id');
        $this->user_id = session('user_id');
        return $this->save()?
            ['status'=>1,'id'=>$this->id]:
            ['status'=>0,'msg'=>'回答提交失败'];
    }

    public function change()
    {
        if(!user_ins()->is_logged_in()) return ['status'=>0,'msg'=>'请您登陆'];

        if(!rq('id'))
            return ['status'=>0,'msg'=>'需要回答内容的id'];

        if(!rq('content'))          //这里是我自己加了个条件
            return ['status'=>0,'msg'=>'请输入更改内容'];

        $answers = $this->find(rq('id'));
        if($answers->user_id != session('user_id'))
            return ['status'=>0,'msg'=>'您没有权限'];


        $answers->content = rq('content');
        return $answers->save()?
            ['status'=>1]:
            ['status'=>0,'msg'=>'更改回答失败'];
    }

    public function show_by_user_id($user_id)
    {
        $user = user_ins()->find($user_id);
        if(!$user) return error('该用户不存在');
        $res = $this->with('question')
                    ->where('user_id',$user_id)
                    ->get()
                    ->keyBy('id');
        return success($res->toArray());
    }

    /*查看回答api*/
    public function show()
    {
        if(!rq('id') && !rq('question_id') && !rq('user_id'))
            return ['status'=>0,'msg'=>'问题的id和回答该问题内容的id都需要'];

        if(rq('user_id'))
        {
            $user_id = rq('user_id') === 'self'?
                session('user_id'):
                rq('user_id');
            return $this->show_by_user_id($user_id);
        }

        /*查看单个回答*/
        if(rq('id'))
        {
            $answer = $this->with('user')
                           ->with('users')
                           ->find(rq('id'));
            if (!$answer)
                return ['status' => 0, 'msg' => '该回答不存在'];

            $answer = $this->count_vote($answer);

            return ['status' => 1, 'data' => $answer];
        }

        /*在检查回答前，查看该问题是否存在*/
        if(!ques_ins()->find(rq('question_id')))
            return ['status'=>0,'msg'=>'没有该问题'];

        /*查看同一问题下的所有回答*/
        $answers = $this->where('question_id',rq('question_id'))
                        ->get()
                        ->keyBy('id');
        return ['status'=>0,'data'=>$answers];
    }

    /*这个就是说明是由谁来回答的*/
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * 回答表与用户表多对多关联(这个回答到底有多少人投票了)
     */
    public function users()
    {
        return $this->belongsToMany('App\User')
                     ->withPivot('vote')
                     ->withTimestamps();     //=>这个的意思就是如果我们在关联表中新增了数据，这边也会跟着更新时间数据
    }

    /**
     * 那么我们怎么去用上面的那个方法呢
     */
    public function vote()
    {
        if(!user_ins()->is_logged_in())
            return ['status'=>0,'msg'=>'请您登陆'];

        if(!rq('id')|| !rq('vote'))
            return ['status'=>0,'msg'=>'回答的id和vote要有一个'];

        $answer = $this->find(rq('id'));
        if(!$answer)
            return ['status'=>0,'msg'=>'该回答不存在'];

        /*1是点赞,2是点踩 ,3是清空*/
        //$vote = rq('vote')<=1 ? 1 :2;
        $vote = rq('vote');
        if($vote !=1 && $vote!=2 && $vote!=3)
            return error('invalid vote');

        /*检查此用户是否相同回答下投过票,如果投过就删除投票*/
       $answer->users()
                    ->newPivotStatement()        //=>这个方法就是跳到中间表里，对中间表进行操作
                    ->where('user_id',session('user_id'))
                    ->where('answer_id',rq('id'))
                    ->delete();

        if($vote == 3)
            return success();

        /*在连接表中添加数据*/
        $answer->users()->attach(session('user_id'),['vote'=>$vote]);
        return ['status'=>1];
    }

    /*一对多的关系
    一个回答必定属于一个问题，
    一个问题下可以有多个回答*/
    public function question()
    {
        return $this->belongsTo('App\Question');
    }

    public function count_vote($answer)
    {
        $upvote_count = 0;
        $downvote_count = 0;
        foreach ($answer->users as $user)
        {
            if($user->pivot->vote == 1)
                $upvote_count++;
            else
                $downvote_count++;
        }
        $answer->upvote_count = $upvote_count;
        $answer->downvote_count = $downvote_count;
        return $answer;
    }
}
