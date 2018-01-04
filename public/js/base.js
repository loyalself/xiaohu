;(function(){
    'use strict';

    angular.module('xiaohu',[
        'ui.router',
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
                    templateUrl:'home.tpl'
                })

                .state('signup',{
                    url:'/signup',
                    templateUrl:'signup.tpl'
                })

                .state('login',{
                    url:'/login',
                    templateUrl:'login.tpl'
                })
        }])

        .service('UserService',[
            '$state',
            '$http',
            function($state,$http){
                var me = this;
                me.signup_data = {};
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
})();