<div ng-controller="HomeController" class="home card container">
    <h1>最新动态</h1>
    <div class="hr"></div>
    <div class="item-set">
        <div ng-repeat="item in Timeline.data" class="feed item clearfix">
            <div ng-if="item.question_id" class="vote">
                <div class="up">[:item.upvote_count:]</div>
                <div class="down">踩</div>
            </div>
            <div class="feed-item-content">
                <div ng-if="item.question_id" class="content-act">用户[:item.user.username:]添加了回答</div>
                <div ng-if="!item.question_id" class="content-act">用户[:item.user.username:]添加了提问</div>
                <div class="title">[:item.title:]</div>
                <div class="content-owner">用户[:item.user.username:]
                    <span class="desc">我的小名叫花花</span>
                </div>
                <div class="content-main">
                    [:item.desc:]
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