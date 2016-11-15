<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel='stylesheet' type="text/css" href="__PUBLIC__/css/style.css" />
<script type="text/javascript" src="__PUBLIC__/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/common.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/jquery.form.min.js"></script>
<script type="text/javascript">
	window.UEDITOR_HOME_URL = '__DATA__/ueditor/';
	window.onload = function() {
		window.UEDITOR_CONFIG.initialFrameWidth=780;
		window.UEDITOR_CONFIG.initialFrameHeight=300;
		//图片上传配置区
		window.UEDITOR_CONFIG.imageUrl = "<?php echo U(GROUP_NAME. '/Public/upload');?>" ;   //图片上传提交地址
		window.UEDITOR_CONFIG.imagePath = "" ; //图片修正地址，引用了fixedImagePath,如有特殊需求，可自行配置      
		window.UEDITOR_CONFIG.imageManagerUrl = "<?php echo U(GROUP_NAME. '/Public/getFileOfImg');?>" ;////图片在线管理的处理地址
		window.UEDITOR_CONFIG.imageManagerPath = ""; //图片在线管理修正地址  
			UE.getEditor('content');

	}
</script>
<script type="text/javascript" src="__DATA__/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="__DATA__/ueditor/ueditor.all.min.js"></script>	
</head>
<body>
<div class="main">
    <div class="pos"><?php echo ($vo["name"]); ?>
    <?php if(ACTION_NAME == "index"): ?>| <?php if(is_array($poscate)): foreach($poscate as $key=>$v): ?><a href="<?php echo U(GROUP_NAME. '/' . ucfirst($v['tablename']) .'/index', array('pid' => $v['id']));?>"><?php echo ($v["name"]); ?> &gt; </a><?php endforeach; endif; endif; ?>
    </div>
    <?php if($subcate): ?><div class="sub"><span>子栏目：</span>
        <?php if(is_array($subcate)): foreach($subcate as $key=>$v): ?><a href="<?php echo U(GROUP_NAME. '/'. ucfirst($v['tablename']) . '/index', array('pid' => $v['id']));?>"><?php echo ($v["name"]); ?></a><?php endforeach; endif; ?>
    </div><?php endif; ?>
	<div class="form">
		<form method='post' id="form_do" name="form_do" action="<?php echo U(GROUP_NAME. '/Page/indexHandle');?>">
		<dl><dt>摘要：</dt>
			<dd>
				<textarea name="description" class="tarea_default_new"><?php echo ($vo["description"]); ?></textarea>
			</dd>
		</dl>
		<dl><dt>内容：</dt><dd>
				<textarea name="content" id="content"><?php echo ($vo["content"]); ?></textarea>
			</dd>
		</dl>
		<dl>
			
		</dl>	
		</div>
		<div class="form_b">
			<input type="hidden" name="id" value="<?php echo ($pid); ?>" />
			<input type="hidden" name="pid" value="<?php echo ($pid); ?>" />
			<input type="submit" class="btn_blue" id="submit" value="提 交">
		</div>
	   </form>
	</div>


</body>
</html>