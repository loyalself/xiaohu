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

    /*查看回答*/
    public function show()
    {
        if(!rq('id') && !rq('question_id'))
            return ['status'=>0,'msg'=>'问题的id和回答该问题内容的id都需要'];

        /*查看单个回答*/
        if(rq('id'))
        {
            $answer = $this->find(rq('id'));
            if (!$answer)
                return ['status' => 0, 'msg' => '该回答不存在'];

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



}
