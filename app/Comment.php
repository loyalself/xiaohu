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
        /*同时一个评论不能既给问题也给回答
        判断:如果既没有问题的id和没有回答内容的id   或者 两者都有，都返回错误；你有且只能有一个
        */
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

        if(rq('reply_to'))          //这里的reply_to的值就是评论的id,其实就是针对自己再去评论
        {
            $target = $this->find(rq('reply_to'));
            if(!$target)  return ['stauts'=>0,'msg'=>'target comment not exists'];
            if($target->user_id == session('user_id'))
                return ['status'=>0,'msg'=>'您不能对自己进行评论呢'];
            $this->reply_to = rq('reply_to');
        }
        $this->content = rq('content');
        $this->user_id = session('user_id');
        return $this->save()?
            ['status'=>1]:
            ['status'=>0,'msg'=>'添加评论失败'];
    }

    /**
     * 查看评论的API
     * 1.一种是查看问题的评论
     * 2.一种是查看答案的评论
     */
    public function show()
    {
        if(!rq('question_id') && !rq('answer_id'))
            return ['status'=>0,'msg'=>'问题的id和回答内容的id要有一个'];

        if(rq('question_id'))
        {
            $question = ques_ins()->find(rq('question_id'));
            if(!$question) return ['status'=>0,'msg'=>'没有该问题'];
            $data = $this->where('question_id',rq('question_id'))
                         ->with('user')
                         ->get();
        }else
        {
            $answer = ques_ins()->find(rq('answer_id'));
            if(!$answer) return ['status'=>0,'msg'=>'没有该回答'];
            $data = $this->with('user')
                         ->where('answer_id',rq('answer_id'))
                         ->get();
        }
        return ['status'=>1,'data'=>$data->keyBy('id')];
    }

    public function remove()
    {
        if(!user_ins()->is_logged_in())
            return ['status'=>0,'msg'=>'请您登陆'];

        if(!rq('id'))
            return ['status'=>0,'msg'=>'评论id没有'];

        $comments = $this->find(rq('id'));
        if(!$comments)
            return ['status'=>0,'msg'=>'没有该评论'];
        if($comments->user_id != session('user_id'))
            return ['status'=>0,'msg'=>'您没有权限'];

        $this->where('reply_to',rq('id'))->delete();    //先删除此评论下的所有回复
        return $comments->delete()?
            ['status'=>1]:
            ['status'=>0,'msg'=>'删除失败'];
    }

    /**
     * 一个用户可以拥有多条评论
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
