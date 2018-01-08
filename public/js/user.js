;(function()
{
    'use strict';

    angular.module('user',[
        'answer'
    ])
        .service('UserService',[
            '$state',
            '$http',
            function($state,$http){
                var me = this;
                me.signup_data = {};
                me.login_data = {};

                me.read = function(param){
                    return $http.post('api/user/read',param)
                     .then(function(r){
                         console.log('r',r)
                    })
                }

                me.signup = function()
                {
                    //console.log(9999);
                    $http.post('api/signup',me.signup_data)
                        .then(function(r)
                        {
                            if(r.data.status)
                            {
                                me.signup_data = {};        /*注册成功将数据清空*/
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

        .controller('SignupController',[
            '$scope',
            'UserService',
            function($scope,UserService){
                $scope.User = UserService;

                $scope.$watch(function()
                {
                    return UserService.signup_data;     /*返回你要监控的内容*/
                },function(n,o)
                {
                    if(n.username != o.username)
                        UserService.username_exists();
                },true)
            }])

        .controller('LoginController',[
            '$scope',
            'UserService',
            function($scope,UserService){
                $scope.User = UserService;
            }])

        .controller('UserController',[
            '$scope',
            '$stateParams',
            'AnswerService',
            'UserService',
            function($scope,$stateParams,UserService)
            {
                $scope.User = UserService;
                console.log('$stateParams',$stateParams);
                UserService.read($stateParams);
                AnswerService.read({user_id:$stateParams.id})
                    .then(function(r){
                        if(r)
                            UserService.his_answers = r;
                    })

            }])


})();