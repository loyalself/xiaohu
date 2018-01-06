;(function()
{
    'use strict';
    angular.module('answer',[])

        .service('AnswerService',[
            '$http',
            function($http){
                var me = this;
                me.count_vote = function(answers){
                    for(var i=0;i<answers.legnth;i++){
                        var votes,item = answers[i];
                        if(!item['question_id'] || item['pivot']) continue;
                        item.upvote_count = 0;
                        item.downvote_count = 0;
                        votes = item['users'];
                        if(votes)
                        for(var j = 0;j < votes.length;j++){
                            var v = votes[j];
                        if(v['pivot'].vote === 1)
                            item.upvote_count++;
                        if(v['pivot'].vote === 2)
                             item.downvote_count++;
                        }
                    }
                    return answers;
                }
            }
        ])
})();