;(function()
{
    'use strict';

    angular.module('question',[])

        .service('QuestionService',[
            '$http',
            '$state',
            function($http,$state)
            {
                var me = this;
                me.new_question = {};
                me.go_add_question = function()
                {
                    $state.go('question.add');
                }

                /*查看问题*/
                me.read = function(params)
                {
                    return $http.post('/api/question/show',params)
                        .then (function(r){
                            if(r.data.status)
                            {
                                if(params.id)
                                {
                                    console.log('r',r);
                                    me.data[params.id] = me.current_question = r.data.data;
                                }else
                                {
                                    me.data = angular.merge({},me.data,r.data.data);
                                }
                                return r.data.data;
                            }
                            return false;
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
            'QuestionService',
            function($scope,QuestionService)
            {

            }
        ])

        .controller('QuestionDetailController',[
            '$scope',
            '$stateParams',
            'QuestionService',
            function($scope,$stateParams,QuestionService)
            {
                QuestionService.read($stateParams);
            }
        ])
})();