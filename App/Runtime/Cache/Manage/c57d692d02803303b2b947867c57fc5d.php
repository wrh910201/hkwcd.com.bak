<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel='stylesheet' type="text/css" href="__PUBLIC__/css/style.css" />
<script type="text/javascript" src="__PUBLIC__/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/common.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/jquery.form.min.js"></script>
</head>
<body>
<div class="main">
    <div class="pos"><?php echo ($type); ?></div>
	<div class="form">
		<form method='post' id="form_do" name="form_do" action="<?php echo U(GROUP_NAME. '/Country/add');?>">
		<dl>
			<dt>中文名称：</dt>
			<dd>
				<input type="text" name="name" class="inp_one" style="width:400px"  /> 
			</dd>  
		</dl> 
		<dl>
			<dt>英文名称：</dt>
			<dd>
				<input type="text" name="ename" class="inp_one" style="width:400px"  />
			</dd>
		</dl>
		<dl>
			<dt>上级：</dt>
			<dd>
				<?php echo ($pname); ?>
			</dd>
		</dl>	
		<dl>
			<dt> 排序：</dt>
			<dd>
				<input type="text" name="sort" class="inp_one"  />
			</dd>
		</dl>
		<div class="form_b">	
			<input type="hidden" name="id" value="<?php echo ($id); ?>" />
			<input type="hidden" name="pid" value="<?php echo ($pid); ?>" />		
			<input type="submit" class="btn_blue" id="submit" value="提 交">
		</div>
	   </form>
	</div>
</div>


</body>
</html>