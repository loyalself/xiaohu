;(function()
{
    'use strict';
    angular.module('answer',[])

        .service('AnswerService',[
            '$http',
            '$state',
            function($http,$state){
                var me = this;
                me.data = {};
                me.answer_form = {};
                /**
                 * 统计票数
                 * @param answers  array  用于统计票数的数据
                 * 此数据可以是问题也可以是回答
                 * 如果是问题,将会跳过统计
                 * @returns {*}
                 */
                me.count_vote = function(answers){
                    /**迭代所有的数据*/
                    for(var i=0;i<answers.length;i++){
                        /*封装单个数据*/
                        var votes,item = answers[i];
                        /*如果不是回答也没有users元素说明本条不是回答或回答没有任何票数*/
                        if(!item['question_id'])
                            continue;

                        me.data[item.id] = item;

                        if(!item['users'])
                            continue;

                        /*每条回答的默认赞同票和反对票都为0*/
                        item.upvote_count = 0;
                        item.downvote_count = 0;

                        /*users是所有投票用户的用户信息*/
                        votes = item['users'];
                        if(votes)
                        for(var j = 0;j < votes.length;j++){
                            var v = votes[j];

                        /*获取pivot元素中的用户投票信息
                        * 如果是1将增加一赞同票
                        * 如果是2将增加一反对票*/
                        if(v['pivot'].vote === 1)
                            item.upvote_count++;
                        if(v['pivot'].vote === 2)
                             item.downvote_count++;
                        }
                    }
                    return answers;
                }

                /*添加回答或编辑之前的回答*/
                me.add_or_update = function(question_id)
                {
                    if(!question_id)
                    {
                        console.error('没有问题的id,怎么回答啊');
                        return;
                    }
                    me.answer_form.question_id = question_id;
                    if(me.answer_form.id)
                        $http.post('/api/answer/change',me.answer_form)
                            .then(function(r){
                                if(r.data.status)
                                    me.answer_form = {};
                                    $state.reload();
                                    console.log('更新回答成功')
                            })
                    else
                        $http.post('/api/answer/add',me.answer_form)
                            .then(function(r){
                                if(r.data.status)
                                    me.answer_form = {};
                                    $state.reload();
                                    console.log('添加回答成功')
                })
                }

                /*删除回答*/
                me.delete = function(id)
                {
                    if(!id)
                    {
                        console.error('没有回答的id,怎么删除啊');
                        return;
                    }
                    $http.post('/api/answer/remove',{id:id})
                        .then(function(r)
                        {
                            if(r.data.status)
                                console.log('删除回答成功');
                            $state.reload();
                        })
                }

                /*点赞或点踩方法*/
                me.vote = function(conf)
                {
                    if(!conf.id || !conf.vote)
                    {
                        console.log('id and vote are required');
                        return;
                    }

                    var answer = me.data[conf.id],
                         users = answer.users;

                    if(answer.user_id == his.id)
                    {
                        console.log('您不能对自己点赞啊!嘻嘻');
                        return false;
                    }


                    /*判断当前用户是否已经投过相同的票*/
                     for(var i = 0;i < users.length;i++)
                     {
                         if(users[i].id == his.id && conf.vote == users[i].pivot.vote)
                             conf.vote = 3;
                     }

                    return $http.post('api/answer/vote',conf)
                        .then(function(r)
                        {
                            if(r.data.status)
                                return true;
                            else if(r.data.msg === '请您登陆')
                                $state.go('login')
                            else
                                return false;
                        },function(){
                            return false;
                        })
                }

                /*更新数据*/
                me.update_data = function(id)
                {
                    return $http.post('api/answer/show',{id:id})
                        .then(function(r){
                            me.data[id] = r.data.data;
                        })
                }

                me.read = function(params)
                {
                    return $http.post('api/answer/show',params)
                        .then (function(r){
                            if(r.data.status)
                            {
                                me.data = angular.merge({},me.data,r.data.data);
                                return r.data.data;
                            }
                            return false;
                        })
                }

                /*添加评论*/
                me.add_comment = function()
                {
                    return $http.post('/api/comment/add',me.new_comment)
                        .then(function(r){
                            console.log(r);
                            if(r.data.status)
                                return true;
                            return false;
                        })
                }
        }])

        .directive('commentBlock',[
            '$http',
            'AnswerService',
            function($http,AnswerService)
            {
              var o = {};
              o.templateUrl = 'comment.tpl';
              o.scope = {
                  answer_id: '=answerId'
              }
              /*这个link的作用就是当发现页面中有comment-block时,就会去执行下面link这个方法*/
              o.link = function(sco,ele,attr)
              {
                  sco.Answer = AnswerService;
                  sco._ = {};
                  sco.data = {};
                  sco.helper = helper;

                  function get_comment_list()
                  {
                      $http.post('/api/comment/show',{answer_id:sco.answer_id})
                          .then(function(r){
                              if(r.data.status)
                                  sco.data = angular.merge({},sco.data,r.data.data);
                          })
                  }

                  if(sco.answer_id)
                        get_comment_list();

                  sco._.add_comment = function()
                  {
                      AnswerService.new_comment.answer_id = sco.answer_id;
                      AnswerService.add_comment()
                          .then(function(r){
                             if(r)
                             {
                                 AnswerService.new_comment = {};
                                 get_comment_list();
                             }
                          })
                  }
              }
              return o;
            }])
})();