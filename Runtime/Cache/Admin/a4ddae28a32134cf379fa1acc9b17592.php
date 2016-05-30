<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>后台管理系统</title>

    <!-- basic styles -->
<link href="/Application/Admin/Common/assets/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="/Application/Admin/Common/assets/css/font-awesome.min.css" />
<link rel="stylesheet" href="/Application/Admin/Common/assets/css/page.css" />
<!--[if IE 7]>
<link rel="stylesheet" href="/Application/Admin/Common/assets/css/font-awesome-ie7.min.css" />
<![endif]-->
<!-- page specific plugin styles -->
<!-- fonts -->
<link rel="stylesheet" href="/Application/Admin/Common/assets/css/family.css" />
<!-- ace styles -->
<link rel="stylesheet" href="/Application/Admin/Common/assets/css/ace.min.css" />
<link rel="stylesheet" href="/Application/Admin/Common/assets/css/ace-rtl.min.css" />
<link rel="stylesheet" href="/Application/Admin/Common/assets/css/ace-skins.min.css" />
<!--[if lte IE 8]>
<link rel="stylesheet" href="/Application/Admin/Common/assets/css/ace-ie.min.css" />

<![endif]-->
<!-- inline styles related to this page -->
<!-- ace settings handler -->
<script src="/Application/Admin/Common/assets/js/ace-extra.min.js"></script>
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="/Application/Admin/Common/assets/js/html5shiv.js"></script>
<script src="/Application/Admin/Common/assets/js/respond.min.js"></script>
<![endif]-->
</head>

<body>
<div class="navbar navbar-default" id="navbar">
<script type="text/javascript">
    try{ace.settings.check('navbar' , 'fixed')}catch(e){}
</script>

<div class="navbar-container" id="navbar-container" >
<div class="navbar-header pull-left">
    <a href="<?php echo U('Index/index');?>" class="navbar-brand">
        <small>
            <i class="icon-leaf"></i>
            后台管理系统
        </small>
    </a><!-- /.brand -->
</div><!-- /.navbar-header -->

<div class="navbar-header pull-right" role="navigation">
    <ul class="nav ace-nav">

        <li class="light-blue">
            <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                <img class="nav-user-photo" style="width: 36px;height: 36px;" src="/Application/Admin/Common/assets/avatars/user.jpg" alt="Jason's Photo" />
                                        <span class="user-info">
                                            <small>欢迎光临,</small>
                                            <?php
 session_start(); $username=$_SESSION['username']; echo $username; ?>
                                        </span>

                <i class="icon-caret-down"></i>
            </a>

            <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                <li>
                    <a href="#">
                        <i class="icon-cog"></i>
                        设置
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="icon-user"></i>
                        个人资料
                    </a>
                </li>

                <li class="divider"></li>

                <li>
                    <a href="<?php echo U('Login/logout');?>">
                        <i class="icon-off"></i>
                        退出
                    </a>
                </li>
            </ul>
        </li>
    </ul><!-- /.ace-nav -->
</div><!-- /.navbar-header -->
</div><!-- /.container -->
</div>
<div class="main-container" id="main-container">
    <script type="text/javascript">
        try{ace.settings.check('main-container' , 'fixed')}catch(e){}
    </script>
    <div class="main-container-inner">
        <a class="menu-toggler" id="menu-toggler" href="#">
            <span class="menu-text"></span>
        </a>

        <div><div class="sidebar" id="sidebar">
<script type="text/javascript">
    try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
</script>

<div class="sidebar-shortcuts" id="sidebar-shortcuts">
    <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
        <button class="btn btn-success">
            <i class="icon-signal"></i>
        </button>

        <button class="btn btn-info">
            <i class="icon-pencil"></i>
        </button>

        <button class="btn btn-warning">
            <i class="icon-group"></i>
        </button>

        <button class="btn btn-danger">
            <i class="icon-cogs"></i>
        </button>
    </div>

    <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
        <span class="btn btn-success"></span>

        <span class="btn btn-info"></span>

        <span class="btn btn-warning"></span>

        <span class="btn btn-danger"></span>
    </div>
</div><!-- #sidebar-shortcuts -->
<ul class="nav nav-list">
    <!--class="active"-->
    <li class="<?php echo ($capital); ?>">
        <a href="<?php echo U('Capital/index');?>" class="dropdown-toggle"><i class="icon-edit"></i><span class="menu-text"> 模拟资金</span></a>
    </li>
    <li class="<?php echo ($active); ?>">
        <a href="<?php echo U('Active/index');?>" class="dropdown-toggle"><i class="icon-edit"></i><span class="menu-text"> 活跃用户</span></a>
    </li>
    <li class="<?php echo ($allowip); ?>">
        <a href="<?php echo U('Allowip/index');?>" class="dropdown-toggle"><i class="icon-edit"></i><span class="menu-text"> 允许操作ip</span></a>
    </li>
    <li class="<?php echo ($admin); ?>">
        <a href="<?php echo U('Admin/index');?>" class="dropdown-toggle"><i class="icon-edit"></i><span class="menu-text"> 管理员</span></a>
    </li>

</ul><!-- /.nav-list -->

<div class="sidebar-collapse" id="sidebar-collapse" onclick="adb();">
    <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
</div>

<script type="text/javascript">
    try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
</script>
</div>




</div>


            <div style="margin-left:0px;"  id="mainajax">
    <div class="main-content">
    <div class="breadcrumbs" id="breadcrumbs">
        <script type="text/javascript">
            try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
        </script>

        <ul class="breadcrumb">
            <li>
                <i class="icon-home home-icon"></i>
                首页
            </li>

            <li>
                允许IP添加
            </li>
        </ul><!-- .breadcrumb -->

        <div class="nav-search" id="nav-search" style="top: 3px;">
            <a href="<?php echo U('Allowip/index');?>"><button class="btn btn-sm btn-primary no-border">返回列表</button></a>
        </div><!-- #nav-search -->
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-xs-12">
                <!-- PAGE CONTENT BEGINS -->
                <div class="row">
                    <div class="col-xs-12">
                        <div class="table-header">

                        </div>
                        <div class="table-responsive">
                            <form action="/Admin/Allowip/add"  enctype="multipart/form-data" method="post">
                                <table id="sample-table-2" class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th class="center" style="width: 200px;">栏目</th>
                                        <th>详情</th>
                                    </tr>
                                    </thead>

                                    <tbody style="font-family:'Microsoft YaHei';">
                                    <tr>
                                        <td class="center"><label style="height: 15px;">允许ip</label></td>
                                        <td><input name="ip" type="text" class="col-xs-10 col-sm-3"></td>
                                    </tr>

                                    <tr>
                                        <td class="center"></td>
                                        <td>
                                            <button style="float:right;margin-right:5%;height: 30px;width: 55px;" class="btn btn-app btn-grey btn-xs radius-4"><i style="font-size:16px;width:35px;text-align: left;padding-left: 5px;margin-top: -5px;" class="icon-save bigger-160">保存</i></button>
                                        </td>
                                    </tr>
                                    </tbody>

                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.page-content -->
</div><!-- /.main-content -->
</div>

    </div><!-- /.main-container-inner -->

    <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
        <i class="icon-double-angle-up icon-only bigger-110"></i>
    </a>

</div><!-- /.main-container -->
</body>
<script type="text/javascript" src="/Public/ckeditor/ckeditor.js"></script>
<!--<script type="text/javascript">CKEDITOR.replace( 'editor1', {allowedContent:true} );</script>-->
<script>
    function ckf(){
        CKEDITOR.replace( 'editor1', {allowedContent:true} );
    }
</script>
<!-- basic scripts -->

<!--[if !IE]> -->

<script src="/Application/Admin/Common/assets/js/jquery-2.0.3.min.js"></script>

<!-- <![endif]-->

<!--[if IE]>
<script src="/Application/Admin/Common/assets/js/jquery-1.10.2.min.js"></script>
<![endif]-->

<!--[if !IE]> -->

<script type="text/javascript">
    window.jQuery || document.write("<script src='/Application/Admin/Common/assets/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
</script>

<!-- <![endif]-->

<!--[if IE]>
<script type="text/javascript">
    window.jQuery || document.write("<script src='/Application/Admin/Common/assets/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
</script>
<![endif]-->

<script type="text/javascript">
    if("ontouchend" in document) document.write("<script src='/Application/Admin/Common/assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
</script>
<script src="/Application/Admin/Common/assets/js/bootstrap.min.js"></script>
<script src="/Application/Admin/Common/assets/js/typeahead-bs2.min.js"></script>

<!-- page specific plugin scripts -->

<!--<script src="/Application/Admin/Common/assets/js/jquery.dataTables.min.js"></script>
<script src="/Application/Admin/Common/assets/js/jquery.dataTables.bootstrap.js"></script>-->

<!-- ace scripts -->

<script src="/Application/Admin/Common/assets/js/ace-elements.min.js"></script>
<script src="/Application/Admin/Common/assets/js/ace.min.js"></script>

<!-- inline scripts related to this page -->

<script type="text/javascript">
    jQuery(function($) {
        var oTable1 = $('#sample-table-2').dataTable( {
            "aoColumns": [
                { "bSortable": false },
                null, null,null, null, null,
                { "bSortable": false }
            ] } );


        $('table th input:checkbox').on('click' , function(){
            var that = this;
            $(this).closest('table').find('tr > td:first-child input:checkbox')
                    .each(function(){
                        this.checked = that.checked;
                        $(this).closest('tr').toggleClass('selected');
                    });

        });


        $('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});
        function tooltip_placement(context, source) {
            var $source = $(source);
            var $parent = $source.closest('table')
            var off1 = $parent.offset();
            var w1 = $parent.width();

            var off2 = $source.offset();
            var w2 = $source.width();

            if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
            return 'left';
        }
    })
</script>
<!--<div style="display:none"><script src='/Application/Admin/Common/assets/js/foot.js' language='JavaScript' charset='gb2312'></script></div>-->
<script src="/Application/Admin/Common/assets/js/ajax/ajax.js"></script>
<script src="/Public/date/WdatePicker.js"></script>
<script type="text/javascript">
    var checkall=document.getElementsByName("id[]");
     $('#delt').click(function(){
         if(!this.checked){
             for(var $i=0;$i<checkall.length;$i++){
                     checkall[$i].checked=false;
             }
         }else{
             for(var $i=0;$i<checkall.length;$i++){
                     checkall[$i].checked=true;
             }
         }
     });
    $(document).ready(function(){
        $(".flip").click(function(){
            $(".panel").slideToggle("slow");
        });
    });
    function display(id){
        //$(".nodisplay"+id).slideToggle(1);
        $(".nodisplay"+id).toggle(100);
        //$(".nodisplay10").css('display','inline');

    }
</script>
</html>