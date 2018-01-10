<div ng-controller="HomeController" class="home card container">
    <h1>最新动态</h1>
    <div class="hr"></div>
    <div class="item-set">
        <div ng-repeat="item in Timeline.data track by $index" class="feed item clearfix">
            <div ng-if="item.question_id" class="vote">
                <div ng-click="Timeline.vote({id:item.id,vote:1})" class="up">赞[: item.upvote_count :]</div>
                <div ng-click="Timeline.vote({id:item.id,vote:2})" class="down">踩[: item.downvote_count :]</div>
            </div>
            <div class="feed-item-content">
                <div ng-if="item.question_id" class="content-act">用户[:item.user.username:]添加了回答</div>
                <div ng-if="!item.question_id" class="content-act">用户[:item.user.username:]添加了提问</div>
                <div ng-if="item.question_id" ui-sref="question.detail({id:item.question.id})" class="title">
                    [:item.question.title:]
                </div>
                <div ui-sref="question.detail({id:item.id})" class="title">[:item.title:]</div>
                <div class="content-owner">用户[:item.user.username:]
                    <span class="desc">我的小名叫花花</span>
                </div>
                <div ng-if="item.question_id" class="content-main">
                    [:item.content:]
                    <div class="gray">
                        {{--下面这个链接就是问题加专门的回答跳转链接
                            参数就是跟base.js里面是一样的, id?answer_id  --}}
                       <a ui-sref="question.detail({id:item.question_id,answer_id:item.id})">
                            [:item.updated_at:]
                        </a>
                    </div>
                </div>

                <div class="action-set">
                    <div class="comment">评论</div>
                </div>
                <div class="comment-block">
                    <div class="hr"></div>
                    <div class="comment-item-set">
                        <div class="rect"></div>
                        <div class="comment-item clearfix">
                            <div class="user">黎明</div>
                            <div class="comment-content">
                                sgfdsl;hklsdfhk'lfskh'fksfsagddghd
                                shdshsafsagsadfsafgsgfsafsafsafsafasgas
                                sagsagsagasgsagsagasdgsdgfjkyhkyk
                                dsfgdsghdshg
                            </div>
                        </div>
                        <div class="comment-item clearfix">
                            <div class="user">黎明</div>
                            <div class="comment-content">
                                sgfdsl;hklsdfhk'lfskh'fksfsagddghd
                                shdshsafsagsadfsafgsgfsafsafsafsafasgas
                                sagsagsagasgsagsagasdgsdgfjkyhkyk
                                dsfgdsghdshg
                            </div>
                        </div>
                        <div class="comment-item clearfix">
                            <div class="user">黎明</div>
                            <div class="comment-content">
                                sgfdsl;hklsdfhk'lfskh'fksfsagddghd
                                shdshsafsagsadfsafgsgfsafsafsafsafasgas
                                sagsagsagasgsagsagasdgsdgfjkyhkyk
                                dsfgdsghdshg
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hr"></div>
        </div>
        <div ng-if="Timeline.pending" class="tac">加载中...</div>
        <div ng-if="Timeline.no_more_data" class="tac">没有更多数据啦</div>
    </div>
</div>