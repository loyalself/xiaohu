<!doctype html>
<html lang="zh" ng-app="xiaohu">
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
</head>
<body>
<div class="navbar clearfix">
    <div class="container">
    <div class="fl">
        <div class="navbar-item brand">晓乎</div>
        <div class="navbar-item">
            <form ng-submit="Question.go_add_question()" id="quick_ask" ng-controller="QuestionAddController">
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
</body>

<script type="text/ng-template" id="home.tpl">
    <div ng-controller="HomeController" class="home card container">
       <h1>最新动态</h1>
        <div class="hr"></div>
        <div class="item-set">
                <div ng-repeat="item in Timeline.data" class="item">
                    <div class="vote"></div>
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
        </div>
    </div>
</script>

<script type="text/ng-template" id="signup.tpl">
    <div ng-controller="SignupController" class="signup container">
       <div class="card">
           <h1>注册</h1>
           [:User.signup_data:]
           <form name="signup_form" ng-submit="User.signup()">
              <div class="input-group">
                  <label>用户名:</label>
                  <input name="username" type="text" ng-model="User.signup_data.username"
                          ng-minlength="3"
                          ng-maxlength="24"
                          ng-model-options="{debounce:500}"     {{--500ms检查一次用户输入的用户名--}}
                          required  >
                  <div ng-if="signup_form.username.$touched" class="input-error-set">
                      <div ng-if="signup_form.username.$error.required">用户名为必填项</div>
                      <div ng-if="signup_form.username.$error.maxlength ||
                                   signup_form.username.$error.minlength ">用户名长度需在3-24为之间</div>
                      <div ng-if="User.signup_username_exists ">用户名已存在</div>
                  </div>
              </div>

               <div class="input-group">
                   <label>密码:</label>
                   <input name="password" type="password" ng-model="User.signup_data.password"
                           ng-minlength="4"
                           ng-maxlength="255"
                            required>
                   <div ng-if="signup_form.password.$touched" class="input-error-set">
                       <div ng-if="signup_form.password.$error.required">密码为必填项</div>
                       <div ng-if="signup_form.password.$error.maxlength ||
                                    signup_form.password.$error.minlength">密码长度在4-255位之间</div>
                   </div>
               </div>
               <button class="primary" type="submit" ng-disabled="signup_form.$invalid">注册</button>
           </form>
       </div>
    </div>
</script>

<script type="text/ng-template" id="login.tpl">
    <div ng-controller="LoginController" class="login container">
        <div class="card">
            <h1>登陆</h1>
            <form name="login_form" ng-submit="User.login()">
                <div class="input-group">
                    <label>用户名</label>
                    <input type="text"
                            name="username"
                            ng-model="User.login_data.username"
                           required>
                </div>

                <div class="input-group">
                    <label>密码</label>
                    <input type="password"
                           name="password"
                           ng-model="User.login_data.password"
                           required>
                </div>
                <div ng-if="User.login_failed " class="input-error-set">
                    用户名或密码错误
                </div>

                <div class="input-group">
                    <button type="submit"
                            class="primary"
                            ng-disabled="login_form.username.$error.required ||
                            login_form.password.$error.required">登陆</button>
                </div>
            </form>
        </div>
    </div>
</script>

<script type="text/ng-template" id="question.add.tpl">
    <div ng-controller="QuestionAddController" class="question-add container">
        <div class="card">
            <form name="question_add_form" ng-submit="Question.add()">
                <div class="input-group">
                    <label>问题标题</label>
                    <input type="text"
                            name="title"
                            ng-minlength="5"
                            ng-maxlength="255"
                            ng-model="Question.new_question.title"
                            required>
                </div>
                <div class="input-group">
                    <label>问题描述</label>
                    <textarea type="text"
                               name="desc"
                               ng-model="Question.new_question.desc">
                    </textarea>
                </div>
                <div class="input-group">
                    <button ng-disabled="question_add_form.$invalid"
                             class="primary"
                             type="submit">提交</button>
                </div>
            </form>
        </div>
    </div>
</script>

</html>