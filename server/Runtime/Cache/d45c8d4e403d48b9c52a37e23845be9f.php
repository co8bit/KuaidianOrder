<?php if (!defined('THINK_PATH')) exit();?><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>快点订餐系统——后台</title>
<!-- Bootstrap -->   <link href="__PUBLIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">

   <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }

    </style>
<script>
function fleshVerify(){ 
    //重载验证码
    var time = new Date().getTime();
        document.getElementById('verifyImg').src= '__APP__/Public/verify/'+time;
}
</script>
</head>
<html>

<body>

    <div class="container">

      <form class="form-signin" id="login" name="login" method="post" action="<?php echo U('User/toLogin');?>" >
        <h2 class="form-signin-heading">登录</h2>
        <input type="text" class="input-block-level" placeholder="用户名" name="userName">
        <input type="password" class="input-block-level" placeholder="密码" name="userPassword">
        <input type="text" class="input-block-level" placeholder="验证码" name="yzm">
        <div class="row">
        	<img id="verifyImg" class="span2" src='<?php echo U("Public/verify");?>' onclick="fleshVerify();"/>
        	<div class="span2">请输入左侧的验证码。<br><b>注意：区分大小写</b></div>
        </div>
        <br>
        <button class="btn btn-large btn-primary" type="submit">登录</button>
      </form>

    </div> <!-- /container --> 

    <!-- Bootstrap -->    <script src="__PUBLIC__/bootstrap/js/bootstrap.min.js"></script>

</body>

</html>