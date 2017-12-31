<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    public function add()
    {
        if(!user_ins()->is_logged_in())
            return ['status'=>0,'msg'=>'请您登陆'];

        if(!rq('content'))
            return ['status'=>0,'msg'=>'评论不能为空'];

        /*在这里你就要想用户是给问题评论还是答案评论还是给评论评论*
        /*同时一个评论不能既给问题也给回答*/
        if(
            (!rq('question_id') && !rq('answer_id')) ||         //
            (rq('question_id') && rq('answer_id'))  //两个条件都要
          )
            return ['status'=>0,'msg'=>'需要问题的id和回答内容的id'];

        if(rq('question_id'))
        {
            $ques = ques_ins()->find(rq('question_id'));
            if(!$ques)  return ['stauts'=>0,'msg'=>'该问题不存在'];
            $this->question_id = rq('question_id');
        }else
        {
            $answer = answer_ins()->find(rq('answer_id'));
            if(!$answer)  return ['stauts'=>0,'msg'=>'该回答不存在'];
            $this->answer_id = rq('answer_id');
        }

        if(rq('reply_to'))
        {
            $target = $this->find('reply_to');
            if(!$target)  return ['stauts'=>0,'msg'=>'target comment not exists'];
            $this->reply_to = rq('reply_to');
        }
        $this->content = rq('content');
        $this->user_id = session('user_id');
        return $this->save()?
            ['status'=>1]:
            ['status'=>0,'msg'=>'添加评论失败'];
    }
}
