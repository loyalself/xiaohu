;(function()
{
    'use strict';

    angular.module('question',[])

        .service('QuestionService',[
            '$http',
            '$state',
            'AnswerService',
            function($http,$state,AnswerService)
            {
                var me = this;
                me.new_question = {};
                me.data = {};

                me.update = function()
                {
                    if(!me.current_question.title)
                    {
                        return;
                        console.error('没有问题的标题呀');
                    }
                    return $http.post('/api/question/change',me.current_question)
                        .then(function(r){
                            if(r.data.status)
                                me.show_update_form = false;
                        })
                }

                me.go_add_question = function()
                {
                    $state.go('question.add');
                }

                /*查看问题*/
                me.read = function(params){
                    return $http.post('api/question/show',params)
                        .then(function(r){
                            if(r.data.status) {
                                if (params.id) {
                                    me.data[params.id] = me.current_question = r.data.data;
                                    me.its_answers = me.current_question.answers_with_user_info;
                                    me.its_answers = AnswerService.count_vote(me.its_answers);
                                }
                                else {
                                    me.data = angular.merge({}, me.data, r.data.data);
                                }
                                return r.data.data;
                            }
                            return false;
                        })

                }
                /*类似于这种me.xxx = function(){},是新建方法
                * me.vote:点赞*/
                me.vote = function(conf)
                {
                    /*调用核心点赞功能*/
                   var $r = AnswerService.vote(conf)
                       if($r)
                         $r.then(function(r){
                            if(r)
                                me.update_answer(conf.id);
                        })
                }

                me.update_answer = function(answer_id)
                {
                    $http.post('/api/answer/show',{id:answer_id})
                        .then(function(r)
                        {
                            if(r.data.status)
                            {
                                for(var i=0;i < me.its_answers.length;i++)
                                {
                                    var answer = me.its_answers[i];
                                    if(answer.id == answer_id)
                                    {
                                        console.log('r.data.data',r.data.data);
                                        me.its_answers[i] = r.data.data;
                                        AnswerService.data[answer_id] = r.data.data;
                                    }
                                }
                            }

                        })
                }

                me.add = function()
                {
                    if(!me.new_question.title)
                        return;

                    $http.post('/api/question/add',me.new_question)
                        .then(function(r)
                        {
                            if(r.data.status)
                            {
                                me.new_question = {};
                                $state.go('home');
                            }
                        },function(e)
                        {

                        })
                }
            }
        ])

        .controller('QuestionController',[
            '$scope',
            'QuestionService',
            function($scope,QuestionService)
            {
                $scope.Question = QuestionService;
            }
        ])

        .controller('QuestionAddController',[
            '$scope',
            '$state',
            'QuestionService',
            function($scope,$state,QuestionService)
            {
                if(!his.id)
                    $state.go('login');
            }
        ])

        .controller('QuestionDetailController',[
            '$scope',
            '$stateParams',     // 参数
            'AnswerService',
            'QuestionService',
            function($scope,$stateParams,AnswerService,QuestionService)
            {
                $scope.Answer = AnswerService;
                QuestionService.read($stateParams);
                //console.log('$stateParams',$stateParams);
                if($stateParams.answer_id)
                    QuestionService.current_answer_id = $stateParams.answer_id;
                else
                    QuestionService.current_answer_id = null;
            }
        ])
})();