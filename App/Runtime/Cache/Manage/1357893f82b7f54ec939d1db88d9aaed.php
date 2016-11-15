<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>登录后台</title> 
<script type="text/javascript" src="__PUBLIC__/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/login.js"></script>		
<link rel="stylesheet" href="__PUBLIC__/css/login.css" />
<script type="text/javascript">
    var verifyUrl = "<?php echo U(GROUP_NAME.'/Login/verify','','');?>"; 
    var URLPATHDEPR = "<?php echo C('URL_PATHINFO_DEPR');?>";
</script>
</head>	
<body>
<div class="wrapper">
	<div class="header clearfix"> 
		<div class="subLogo"><span>网站管理系统</span></div>
	</div>
	<div class="content login">
    <form action="<?php echo U(GROUP_NAME.'/Login/login');?>" method="post" name="LoginForm" onsubmit="return ChkLogin()">
    <p class="username"><input class="l_ipt" name="username"  id="username" maxlength="20" type="text" onblur="if(this.value==''){this.value='请输入用户名';this.style.color='#aaa'}" onfocus="if(this.value=='请输入用户名'){this.value='';this.style.color='#666'}" value="请输入用户名" /></p>
    <p class="password"><input class="l_ipt"  name="password" id="password" type="password" onblur="if(this.value==''){this.value='';this.style.color='#aaa'}" onfocus="if(this.value==''){this.value='';this.style.color='#666'}" value="" /></p>
 	<p class="authcode"><input class="l_ipt"  name="code" id="codeshow" maxlength="10" type="text" onblur="if(this.value==''){this.value='请输入验证码';this.style.color='#aaa'}" onfocus="if(this.value=='请输入验证码'){this.value='';this.style.color='#666'}" value="请输入验证码" />
    <span class="img">
    <img src="<?php echo U(GROUP_NAME. '/Login/verify');?>" id="code" style="cursor:pointer;" alt="看不清？点击换一张" /> <a href="javascript:void(0)" onclick="change_code('code')" >看不清</a>
    </span></p>
	 <p class="btn"><button type="submit" class="l_btn">登录</button></p>
    </form>

    </div>
	<div class="footer">
       <p>&nbsp;</p> 
		<p class="sq"><?php echo (C("cfg_powerby")); ?> </p> 
	</div>
</div> 
</body>
</html>