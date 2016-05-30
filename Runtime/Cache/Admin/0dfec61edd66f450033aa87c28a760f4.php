<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>登陆</title>
<!-- basic styles -->
<link href="/market/Application/Admin/Common/assets/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="/market/Application/Admin/Common/assets/css/font-awesome.min.css" />
<link rel="stylesheet" href="/market/Application/Admin/Common/assets/css/page.css" />
<!--[if IE 7]>
<link rel="stylesheet" href="/market/Application/Admin/Common/assets/css/font-awesome-ie7.min.css" />
<![endif]-->
<!-- page specific plugin styles -->
<!-- fonts -->
<link rel="stylesheet" href="/market/Application/Admin/Common/assets/css/family.css" />
<!-- ace styles -->
<link rel="stylesheet" href="/market/Application/Admin/Common/assets/css/ace.min.css" />
<link rel="stylesheet" href="/market/Application/Admin/Common/assets/css/ace-rtl.min.css" />
<link rel="stylesheet" href="/market/Application/Admin/Common/assets/css/ace-skins.min.css" />
<!--[if lte IE 8]>
<link rel="stylesheet" href="/market/Application/Admin/Common/assets/css/ace-ie.min.css" />

<![endif]-->
<!-- inline styles related to this page -->
<!-- ace settings handler -->
<script src="/market/Application/Admin/Common/assets/js/ace-extra.min.js"></script>
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="/market/Application/Admin/Common/assets/js/html5shiv.js"></script>
<script src="/market/Application/Admin/Common/assets/js/respond.min.js"></script>
<![endif]-->
</head>

<body class="login-layout">
<div class="main-container">
<div class="main-content">
<div class="row">
<div class="col-sm-10 col-sm-offset-1">
<div class="login-container">
<div class="center">
    <h1>
        <i class="icon-leaf green"></i>
        <span class="red">后台</span>
        <span class="white">管理系统</span>
    </h1>
    <h4 class="blue">&nbsp;</h4>
</div>

<div class="space-6"></div>

<div class="position-relative">
    <div id="login-box" class="login-box visible widget-box no-border">
        <div class="widget-body">
            <div class="widget-main">
                <h4 class="header blue lighter bigger">
                    <i class="icon-coffee green"></i>
                   请输入你的账户信息
                </h4>

                <div class="space-6"></div>

                <form action="<?php echo U('Login/login');?>"  enctype="multipart/form-data" method="post">
                        <label class="block clearfix" for="username">
                            <span class="block input-icon input-icon-right">
                                <input name="username" id="username" type="text" class="form-control" placeholder="Username" />
                                <i class="icon-user"></i>
                            </span>
                        </label>

                        <label class="block clearfix" for="password">
                            <span class="block input-icon input-icon-right">
                                <input name="password" id="password" type="password" class="form-control" placeholder="Password" />
                                <i class="icon-lock"></i>
                            </span>
                        </label>
                        <label>
                            <span>验证码：</span><input style="width: 235px;" type="text" name="verify" >
                        </label>

                        <div><img id="verify" alt="验证码" onClick="show()" src="<?php echo U('Login/verify');?>">
                            <label style="margin-left: 50px;" class="inline">
                                <input type="checkbox" value="year" name="year" class="ace">
                                <span class="lbl"> 记住我</span>
                            </label>
                        </div>
                        <div style="margin: 5px 0px;">

                        </div>

                        <div class="clearfix">
                            <!--<label class="inline">
                                <input type="checkbox" class="ace" />
                                <span class="lbl"> 记住我</span>
                            </label>-->
                            <!--<a href="javascript:show()">看不清楚</a>-->
                            <button onclick="show()" type="button" class="width-35 btn btn-purple btn-sm no-border">
                                <i class="icon-refresh bigger-100"></i>
                                看不清楚
                            </button>
                            <button class="width-35 pull-right btn btn-sm btn-primary">
                                <i class="icon-key"></i>
                                登陆
                            </button>
                        </div>
                        <div class="space-4"></div>
                </form>

                <div class="social-or-login center">
                    <span class="bigger-110">第三方登陆</span>
                </div>

                <div class="social-login center">
                    <a class="btn btn-primary">
                        <i class="icon-facebook"></i>
                    </a>

                    <a class="btn btn-info">
                        <i class="icon-twitter"></i>
                    </a>

                    <a class="btn btn-danger">
                        <i class="icon-google-plus"></i>
                    </a>
                </div>
            </div><!-- /widget-main -->

            <div class="toolbar clearfix">
                <div>
                    <a href="#" onclick="show_box('forgot-box'); return false;" class="forgot-password-link">
                        <i class="icon-arrow-left"></i>
                        <!--I forgot my password-->
                    </a>
                </div>

                <div>
                    <a href="#" onclick="show_box('signup-box'); return false;" class="user-signup-link">
                        <!--I want to register-->
                        <i class="icon-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div><!-- /widget-body -->
    </div><!-- /login-box -->
        </div><!-- /widget-body -->
    </div><!-- /signup-box -->
</div><!-- /position-relative -->
</div>
</div><!-- /.col -->
</div><!-- /.row -->
</div>
</div><!-- /.main-container -->


</body>

<script>
    function show(){
        document.getElementById("verify").src="/market/Admin/Login/verify/random"+Math.random();

    }
</script>
</html>