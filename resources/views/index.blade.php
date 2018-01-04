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
            <input type="text" name="" class="">
        </div>
    </div>
    <div class="fr">
        <a ui-sref="home" class="navbar-item">首页</a>
        <a ui-sref="login" class="navbar-item">登陆</a>
        <a ui-sref="signup" class="navbar-item">注册</a>
    </div>
    </div>
</div>

<div class="page">
    <div ui-view></div>
</div>
</body>

<script type="text/ng-template" id="home.tpl">
    <div class="home container">
        首页
        sfdhfdjdgjkdgjdhjs
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
                          ng-model="User.signup_data.username"
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
               <button type="submit" ng-disabled="signup_form.$invalid">注册</button>
           </form>
       </div>
    </div>
</script>

<script type="text/ng-template" id="login.tpl">
    <div class="home container">
        登陆
        sfdhfdjdgjkdgjdhjs
    </div>
</script>
</html>