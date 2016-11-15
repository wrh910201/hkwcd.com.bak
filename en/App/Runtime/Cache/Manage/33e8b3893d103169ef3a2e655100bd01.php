<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理中心</title>
<link rel='stylesheet' type="text/css" href="__PUBLIC__/css/style.css" />
<script type="text/javascript" src="__PUBLIC__/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/common.js"></script>
 <script language="JavaScript">
    <!--
    var URL = '__URL__';
    var APP	 = '__APP__';
    var SELF='__SELF__';
    var PUBLIC='__PUBLIC__';
    //-->
</script>
<style type="text/css"> 
	html{_overflow-y:scroll}
</style>
</head>
<body> 
<div class="main"  > 
  <div class="pos">网站基本信息</div>
  	<div class="form">
     <div class="h3">我的个人信息</div>
		<dl> 
			<dd>
				您好，<?php echo (session('yang_adm_username')); ?><br/>
				<div class="clear"></div>
				上次登录时间：<?php echo (session('yang_adm_logintime')); ?><br/>
                <div class="clear"></div>
				上次登录IP：<?php echo (session('yang_adm_loginip')); ?> <br/>
			</dd> 
		</dl> 
         <div class="h3">系统基本信息</div>
		<dl> 
			<dd> 
                网站名称： <?php echo (C("cfg_webname")); ?><br />
                网站域名： <?php echo (C("cfg_weburl")); ?>  <br /> 
                联系电话： <?php echo (C("cfg_phone")); ?> <br />
                客户QQ： <?php echo (C("cfg_qq")); ?><br />
                电子邮箱： <?php echo (C("cfg_email")); ?><br />
                程序版本：HXCMS V<?php echo ($yycms_info["HXCMS_VER"]); ?> [<?php echo ($yycms_info["HXCMS_TIME"]); ?>] <br />
			</dd> 
		</dl>  
       <div class="h3">服务器信息</div>
		<dl> 
			<dd> 
	            操作系统：<?php echo ($os); ?> <br />
	            服务器软件：<?php echo ($software); ?><br />
	            MySQL 版本：<?php echo ($mysql_ver); ?><br /> 
			</dd> 
		</dl>  
   </div> 
</div>
</body>
</html>