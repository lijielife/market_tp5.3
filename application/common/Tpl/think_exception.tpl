<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>系统发生错误</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
html{ overflow-y: scroll; }
body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }
img{ border: 0; }
.error{ padding: 24px 48px; }
h1{ font-size: 32px; line-height: 48px; }
.error .content{ padding-top: 10px}
.error .info{ margin-bottom: 12px; }
.error .info .title{ margin-bottom: 3px; }
.error .info .title h3{ color: #000; font-weight: 700; font-size: 16px; }
.error .info .text{ line-height: 24px; }
.error-tips{
	color: #666;
	width: 450px;
	margin: 30px auto 0;
}
.error-tips dt{
	margin-left: -20px;
	font-size: 16px;
	line-height: 50px;
}
</style>
</head>
<body>
<div class="error">
	<div style="text-align: center;padding-top: 100px;"><img src="/Public/Images/404.png" alt=""></div>
<?php if(!constant("APP_DEBUG")) {?>
	<dl class="error-tips">
		<dt>没有发现你要找的页面, 经砖家仔细研究结果如下:</dt>
		<dd>
			<ul>
				<li>贵玉手输入地址时可能存在键入错误</li>
				<li>小蜗牛把页面落家里忘记带了</li>
				<li>电信网通那头接口生锈了</li>
			</ul>
		</dd>
	</dl>

<?php }else{ ?>
	<h1><?php echo strip_tags($e['message']);?></h1>
<?php }?>
<div class="content">
<?php if(isset($e['file'])) {?>
	<div class="info">
		<div class="title">
			<h3>错误位置</h3>
		</div>
		<div class="text">
			<p>FILE: <?php echo $e['file'] ;?> &#12288;LINE: <?php echo $e['line'];?></p>
		</div>
	</div>
<?php }?>
<?php if(isset($e['trace'])) {?>
	<div class="info">
		<div class="title">
			<h3>TRACE</h3>
		</div>
		<div class="text">
			<p><?php echo nl2br($e['trace']);?></p>
		</div>
	</div>
<?php }?>
</div>
</div>
</body>
</html>
