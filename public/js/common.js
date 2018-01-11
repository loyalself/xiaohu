;(function()
{
    'use strict';
    angular.module('common',[])

        .service('TimelineService',[
            '$http',
            'AnswerService',
            function($http,AnswerService)
            {
                var me = this;
                me.data = [];
                me.current_page = 1;
                me.no_more_data = false;

                /*获取首页数据*/
                me.get = function(conf)
                {
                    if(me.pending || me.no_more_data ) return;

                    me.pending = true;

                    conf = conf || {page:me.current_page}

                    $http.post('/api/timeline',conf)
                        .then(function(r)
                        {
                            if(r.data.status)
                            {
                                if(r.data.data.length){
                                    me.data = me.data.concat(r.data.data);
                                    /*统计每一条回答的票数*/
                                    me.data = AnswerService.count_vote(me.data);
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

                /*在时间线中投票*/
                me.vote = function(conf)
                {
                    /*调用核心投票功能*/
                   var $r = AnswerService.vote(conf)
                       if($r)
                        /*如果投票成功就更新AnswerService中的数据*/
                        $r.then(function(r)
                        {
                            if(r)
                                AnswerService.update_data(conf.id);
                        })
                }

                me.reset_state = function()
                {
                    me.data = [];
                    me.current_page = 1;
                    me.no_more_data = 0;
                }
            }])

        .controller('HomeController',[          /*每次注册新控制器的时候,都会提示该控制器没有注册,删除一下缓存就可以了*/
            '$scope',
            'TimelineService',
            'AnswerService',
            function($scope,TimelineService,AnswerService)
            {
                var $win;
                $scope.Timeline = TimelineService;
                TimelineService.reset_state();
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

                /*监控回答数据的变化,如果监控数据有变化同时更新其他模块中的回答数据*/
                $scope.$watch(function()
                {
                    return AnswerService.data;
                },function(new_data,old_data)
                {
                   var timeline_data = TimelineService.data;
                   for(var k in new_data)
                   {
                       /*更新时间线中的回答数据*/
                       for(var i = 0;i < timeline_data.length;i++)
                       {
                            if(k == timeline_data[i].id)
                            {
                                timeline_data[i] = new_data[k];
                            }
                       }
                   }

                   TimelineService.data = AnswerService.count_vote(TimelineService.data)
                },true)
            }])
})();