<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>跳转提示</title>
<!-- Bootstrap core CSS -->
<link href="__STATIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<!-- <link href="__CSS__/bootstrap.css" rel="stylesheet"> -->
<style type="text/css">
*{ padding: 0; margin: 0; }
body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; padding: 0px;margin: 0px;overflow-x: hidden;}
.alert-warp{
	padding: 50px;
}
.alert-error {
  color: white;
  border-color: #eed3d7;
  background-color: #FF6666;
}
.alert-success {
    color: #468847;
    background-color: #CCFF99;
    border-color: #eed3d7;
}
.alert-title{
	text-align: center;
	font-size: 48px;
}
.alert-body{
	padding-top: 20px;
	text-align: center;
	font-size: 22px;
}
.alert-footer{
	padding-top: 20px;
	font-size: 12px;
}
</style>
</head>
<body>
<div class="row alert-warp">
  <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
  	<?php if(isset($message)) {?>
		<div class="panel panel-default alert-success">
		  <div class="panel-body">
		    <div class="alert-title"><span class="glyphicon glyphicon-ok"></span></div>
		    <div class="alert-body"><?php echo($message); ?></div>
  	<?php }else{?>
		<div class="panel panel-default alert-error">
		  <div class="panel-body">
		    <div class="alert-title"><span class="glyphicon glyphicon-remove"></span></div>
		    <div class="alert-body"><?php echo($error); ?></div>
	<?php }?>
			<div class="alert-footer">
				页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?>
			</div>
		  </div>
		</div>
  </div>
</div>

<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>
