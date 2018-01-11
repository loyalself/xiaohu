<!doctype html>
<html ng-controller="BaseController" lang="zh" ng-app="xiaohu" user-id="{{session('user_id')}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>晓乎首页</title>
    <link rel="stylesheet" href="/node_modules/normalize-css/normalize.css">
    <link rel="stylesheet" href="/css/base.css"> {{--这个是自己写的--}}
    <script src="/node_modules/jquery/dist/jquery.js"></script>
    <script src="/node_modules/angular/angular.js"></script>
    <script src="/node_modules/angular-ui-router/release/angular-ui-router.js"></script>
    <script src="/js/base.js"></script>
    <script src="/js/common.js"></script>
    <script src="/js/user.js"></script>
    <script src="/js/question.js"></script>
    <script src="/js/answer.js"></script>
</head>
<body>
<div class="navbar clearfix">
    <div class="container">
    <div class="fl">
        <div ui-sref="home" class="navbar-item brand">晓乎</div>
        <div class="navbar-item">
            <form ng-submit="Question.go_add_question()"
                  id="quick_ask"
                  ng-controller="QuestionController">
                <div class="navbar-item">
                  <input type="text"  ng-model="Question.new_question.title">
                </div>
                <div class="navbar-item">
                    <button type="submit">提问</button>
                </div>
            </form>
        </div>
    </div>
    <div class="fr">
        <a ui-sref="home" class="navbar-item">首页</a>
        @if(is_logged_in())
            <a ui-sref="login" class="navabr-item">{{session('username')}}</a>
            <a href="{{url('api/logout')}}" class="navabr-item">登出</a>
        @else
            <a ui-sref="login" class="navbar-item">登陆</a>
            <a ui-sref="signup" class="navbar-item">注册</a>
        @endif
    </div>
    </div>
</div>

<div class="page">
    <div ui-view></div>
</div>
<script type="text/ng-template" id="comment.tpl">
    <div class="comment-block">
        <div class="hr"></div>
        <div class="comment-item-set">
            <div class="rect"></div>
            <div class="gray tac well" ng-if="!helper.obj_length(data)">暂无评论</div>
            <div ng-if="helper.obj_length(data)"
                 ng-repeat="item in data"
                 class="comment-item clearfix">
                <div class="user">[:item.user.username:]:</div>
                <div class="comment-content">
                   [:item.content:]
                </div>
            </div>
   {{--         <div class="comment-item clearfix">
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
            </div>--}}
        </div>
        <div class="input-group">
           <form ng-submit="_.add_comment()" class="comment_form">
               <input type="text"
                       placeholder="说点什么..."
                       ng-model="Answer.new_comment.content">
               <button class="primary" type="submit">评论</button>
           </form>
        </div>
    </div>
</script>
</body>
{{--
<script type="text/ng-template" id="home.tpl">
        当没有这个tpl的时候，就会去服务器找这个tpl
</script>
--}}
</html>