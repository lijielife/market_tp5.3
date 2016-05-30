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


            <div style="margin-left:0px;"  id="mainajax"><div id="pageajax">
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
                    管理员
                </li>
            </ul><!-- .breadcrumb -->
        <!--<form action="/Admin/Index/select"  enctype="multipart/form-data" method="post">-->
            <!--<div style="position: absolute;top: 2px;margin-left:200px;line-height: 24px;color: #555;">-->
                    <!--开始时间：<input name="start" id="start" type="text" onClick="WdatePicker({startDate:'%y-%M-%d 00:00:00',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true,position:{left:187,top:-31},firstDayOfWeek:1,autoPickDate:true})"/>-->
                    <!--结束时间：<input name="end" id="end" type="text" onClick="WdatePicker({startDate:'%y-%M-%d 23:59:59',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true,position:{left:187,top:-31},firstDayOfWeek:1,autoPickDate:true})"/>-->
                    <!--<a href="<?php echo U('Index/select');?>"><button class="btn btn-sm btn-primary no-border">查询</button></a>-->
            <!--</div>-->
        <!--</form>-->
        <!--<div style="position: absolute;top: 3px;margin-left:765px;line-height: 24px;">-->
            <!--<form action="/Admin/Index/excel"  enctype="multipart/form-data" method="post">-->
                <!--<input type="hidden" name="timeStart" value="<?php echo ($timeStart); ?>">-->
                <!--<input type="hidden" name="timeEnd" value="<?php echo ($timeEnd); ?>">-->
                <!--<a href="<?php echo U('Index/excel');?>"><button class="btn btn-sm btn-primary no-border">导出客户</button></a>-->
            <!--</form>-->
        <!--</div>-->

        <div class="nav-search" id="nav-search"  style="top: 3px;">
            <a href="<?php echo U('Admin/addshow');?>"><button class="btn btn-sm btn-primary no-border">新增管理员</button></a>

        </div><!-- #nav-search -->
    </div>

    <div class="page-content">


        <div class="row">
            <div class="col-xs-12">
                <!-- PAGE CONTENT BEGINS -->

                <form action="/Admin/Admin/alldel"  enctype="multipart/form-data" method="post">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="table-header">

                        </div>

                        <div class="table-responsive">
                            <table  id="sample-table-2" class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="center" style="width: 50px;"><input type="checkbox" id="delt" name="delt"  value="delt" ></th>
                                    <th class="center" style="width: 50px;">Id</th>
                                    <th>用户名</th>
                                    <th style="width: 150px;">最后登录IP</th>
                                    <th style="width: 150px;"><i class="icon-time bigger-110 hidden-480"></i>最后登录时间</th>
                                    <th style="width: 150px;" class="hidden-480">登录次数</th>
                                    <th style="width: 150px;">创建IP</th>
                                    <th style="width: 150px;"><i class="icon-time bigger-110 hidden-480"></i>创建时间</th>
                                    <th style="width: 100px;">操作</th>
                                </tr>
                                </thead>

                                <tbody >
                                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                    <td class="center"><input type="checkbox" value="<?php echo ($vo["id"]); ?>" name="id[]"></td>
                                    <td class="center"><?php echo ($vo["id"]); ?></td>
                                    <td><a href="<?php echo U('Admin/edit',array('id'=>$vo['id']));?>"><?php echo ($vo["username"]); ?></a></td>
                                    <td><?php echo ($vo["ip"]); ?></td>
                                    <td><?php echo (date("Y-m-d H:i:s",$vo["time"])); ?></td>
                                    <td><?php echo ($vo["count"]); ?></td>
                                    <td><?php echo ($vo["create_ip"]); ?></td>
                                    <td><?php echo (date("Y-m-d H:i:s",$vo["create_time"])); ?></td>
                                    <td>
                                        <div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
                                            &nbsp;
                                            <a title="编辑" class="green" href="<?php echo U('Admin/edit',array('id'=>$vo['id']));?>">
                                                <i class="icon-pencil bigger-130"></i>
                                            </a>
                                            <a id="id-btn-dialog20"  title="删除" class="red" href="<?php echo U('Admin/del',array('id'=>$vo['id']));?>" onclick="return confirm('是否确认删除？');">
                                                <i class="icon-trash bigger-130"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
                <div>
                <a href="#" onclick="return confirm('是否确认删除？');"><button class="btn btn-sm btn-primary no-border">全部删除</button></a>
                </div>

                </form>
                <!--class no-margin-top-->
                <div class="modal-footer " style="text-align: left;margin-top: 10px;">
                    <ul class="pagination  no-margin" style="width:100%;">
                        <?php echo ($page); ?>
                    </ul>
                </div>


            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.page-content -->
</div><!-- /.main-content -->
</div></div>

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