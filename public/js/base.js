;(function(){
    'use strict';

    angular.module('xiaohu',[
        'ui.router',
        'common',
        'user',
        'question',
        'answer'
        ])
        .config([
                '$interpolateProvider',
                '$stateProvider',
                '$urlRouterProvider',
                function ($interpolateProvider,
                           $stateProvider,
                           $urlRouterProvider)
        {
            $interpolateProvider.startSymbol('[:');
            $interpolateProvider.endSymbol(':]');

           $urlRouterProvider.otherwise('/home');

           $stateProvider
               .state('home',{
                    url:'/home',
                    templateUrl:'/tpl/page/home'
                })

                .state('signup',{
                    url:'/signup',
                    templateUrl:'/tpl/page/signup'
                })

                .state('login',{
                    url:'/login',
                    templateUrl:'/tpl/page/login'
                })

               .state('question',{
                   abstract:true,
                   url:'/question',
                   template:'<div ui-view></div>'
               })

               .state('question.add',{
                   url:'/add',
                   templateUrl:'/tpl/page/question_add'
               })
        }])

       /* .service('UserService',[
            '$state',
            '$http',
            function($state,$http){
                var me = this;
                me.signup_data = {};
                me.login_data = {};

                me.signup = function()
                {
                    //console.log(9999);
                    $http.post('api/signup',me.signup_data)
                        .then(function(r)
                        {
                            if(r.data.status)
                            {
                                me.signup_data = {};        /!*注册成功将数据清空*!/
                                $state.go('login');
                            }
                        },function(e)
                        {

                        })
                }

                me.login = function(){
                    $http.post('/api/login',me.login_data)
                        .then(function(r)
                        {
                            if(r.data.status)   //如果登陆成功，就会跳转到首页，并且刷新页面
                            {
                                location.href = '/';
                            }else
                            {
                                me.login_failed = true;
                            }
                        },function()
                        {

                        })
                }

                me.username_exists = function(){
                    $http.post('/api/user/exist', {username: me.signup_data.username})
                        .then(function(r)
                        {
                           if(r.data.status && r.data.data.count)
                               me.signup_username_exists = true;
                           else
                               me.signup_username_exists = false;
                        },function()
                        {
                            console.log('e',e);
                        })
                    }

        }])
*/
       /* .controller('SignupController',[
            '$scope',
            'UserService',
            function($scope,UserService){
                $scope.User = UserService;

                $scope.$watch(function()
                {
                    return UserService.signup_data;     /!*返回你要监控的内容*!/
                },function(n,o)
                {
                    if(n.username != o.username)
                     UserService.username_exists();
                },true)
            }])*/

        /*.controller('LoginController',[
            '$scope',
            'UserService',
            function($scope,UserService){
                $scope.User = UserService;
            }])*/

        /*.service('QuestionService',[
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
        ])*/
       /* .controller('QuestionAddController',[
            '$scope',
            'QuestionService',
            function($scope,QuestionService)
            {
                $scope.Question = QuestionService;
            }
        ])*/

        /*.service('TimelineService',[
            '$http',
            function($http)
            {
                var me = this;
                me.data = [];
                me.current_page = 1;
                me.get = function(conf)
                {
                    if(me.pending) return;

                    me.pending = true;

                    conf = conf || {page:me.current_page}

                    $http.post('/api/timeline',conf)
                        .then(function(r)
                        {
                            if(r.data.status)
                            {
                                if(r.data.data.length){
                                    me.data = me.data.concat(r.data.data);
                                    me.current_page++;
                                }
                                else{
                                    me.no_more_data = true;
                                }
                            }
                            else
                                console.log('network error')
                        },function(){
                            console.log('network error')
                        })
                        .finally(function(){
                            me.pending = false;
                        })
                }
            }])*/

       /* .controller('HomeController',[          /!*每次注册控制器的时候,都会提示该控制器没有注册,删除一下缓存就可以了*!/
            '$scope',
            'TimelineService',
            function($scope,TimelineService)
            {
                var $win;
                $scope.Timeline = TimelineService;
                TimelineService.get();

                $win = $(window);
                $win.on('scroll',function()
                {
                   if($win.scrollTop()-($(document).height()-$win.height()) > -30)
                   {
                       //console.log(1)     当满足上面的那个条件之后，才会console.log
                       TimelineService.get();   //即满足条件之后就加载数据
                   }
                })
            }
        ])*/

})();