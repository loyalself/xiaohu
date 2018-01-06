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
            }])

        .controller('HomeController',[          /*每次注册控制器的时候,都会提示该控制器没有注册,删除一下缓存就可以了*/
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
        ])
})();