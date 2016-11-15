<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel='stylesheet' type="text/css" href="__PUBLIC__/css/style.css" />
<script type="text/javascript" src="__PUBLIC__/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/common.js"></script>
<style type="text/css">
dd.desc{width: 150px; height: 30px; padding: 0px 10px;}
</style>
</head>
<body>
<div class="main">
    <div class="pos">网站设置</div>
	<div class="form">
		<form method='post' id="form_do" name="form_do" action="<?php echo U(GROUP_NAME.'/System/site');?>">
			<div class="h3">系统基本设置（注意：如果你不是专业人员请勿改动，只有开放文件的读写权限才能修改）</div>
        <dl>
			<dt> 网站名称：</dt>
			<dd>
				<input type="text" name="cfg_webname" class="inp_one inp_w250" value="<?php echo (C("cfg_webname")); ?>" />
			</dd>			
			<dd class="desc"></dd>
			<dd>cfg_webname</dd>
		</dl>
		<dl>
			<dt> 网站主页：</dt>
			<dd>
				<input type="text" name="cfg_weburl" class="inp_one inp_w250" value="<?php echo (C("cfg_weburl")); ?>" />
			</dd>
			<dd class="desc"> </dd>
			<dd>cfg_weburl</dd>
		</dl>	
        <dl>
			<dt> CDN：</dt>
			<dd>
				<input type="text" name="cfg_cdn" class="inp_one inp_w250" value="<?php echo (C("cfg_cdn")); ?>" />
			</dd>
			<dd class="desc">不要加入/ </dd>
			<dd>cfg_cdn</dd>
		</dl>	
		<dl>
			<dt> 网站标题：</dt>
			<dd>
				<input type="text" name="cfg_webtitle" class="inp_one inp_w250" value="<?php echo (C("cfg_webtitle")); ?>" />
			</dd>
			<dd class="desc">站点首页title的设置</dd>
			<dd>cfg_webtitle</dd>
		</dl>	
		<dl>
			<dt> 模板默认风格：</dt>
			<dd>
				<select name="cfg_themestyle" class="inp_one inp_w250">
					<?php if(is_array($styleDirList)): foreach($styleDirList as $key=>$v): ?><option value="<?php echo ($v); ?>" <?php if(C("cfg_themestyle")== $v): ?>selected="selected"<?php endif; ?>><?php echo ($v); ?></option><?php endforeach; endif; ?>
				</select>
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_themestyle</dd>
		</dl>
		<dl>
			<dt> 站点关键词：</dt>
			<dd>
				<input type="text" name="cfg_keywords" class="inp_one inp_w250" value="<?php echo (C("cfg_keywords")); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_keywords</dd>
		</dl>
		<dl>
			<dt> 站点描述：</dt>
			<dd>
				<textarea name="cfg_description" class="tarea_default"><?php echo (C("cfg_description")); ?></textarea><br/>
				cfg_description
			</dd>
		</dl>	
		<dl>
			<dt> 网站版权信息：</dt>
			<dd>
				<textarea name="cfg_powerby" class="tarea_default"><?php echo (C("cfg_powerby")); ?></textarea><br/>
				cfg_powerby
			</dd>
		</dl>
        <dl>
			<dt> 网站统计代码：</dt>
			<dd>
				<textarea name="cfg_countcode" class="tarea_default"><?php echo (C("cfg_countcode")); ?></textarea><br/>
				cfg_countcode
			</dd>
		</dl>  
        <dl>
			<dt> 网站分享代码：</dt>
			<dd>
				<textarea name="cfg_sharecode" class="tarea_default"><?php echo (C("cfg_sharecode")); ?></textarea><br/>
				cfg_sharecode
			</dd>
		</dl> 

       <dl>
			<dt> 商务通代码：</dt>
			<dd>
				<textarea name="cfg_swturl" class="tarea_default"><?php echo (C("cfg_swturl")); ?></textarea><br/>
				cfg_swturl
			</dd>
		</dl>

		<dl>
			<dt> 网站备案号：</dt>
			<dd>
				<input type="text" name="cfg_beian" class="inp_one inp_w250" value="<?php echo (C("cfg_beian")); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_beian</dd>
		</dl>
         
		<dl>
			<dt>联系地址：</dt>
			<dd>
				<input type="text" name="cfg_address" class="inp_one inp_w250" value="<?php echo (C("cfg_address")); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_address</dd>
		</dl>

		<dl>
			<dt>联系电话：</dt>
			<dd>
				<input type="text" name="cfg_phone" class="inp_one inp_w250" value="<?php echo (C("cfg_phone")); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_phone</dd>
		</dl>
			
		 <dl>
			<dt>客服QQ：</dt>
			<dd>
				<input type="text" name="cfg_qq" class="inp_one inp_w250" value="<?php echo (C("cfg_qq")); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_qq</dd>
		</dl>	
		<dl>
			<dt>客服邮箱：</dt>
			<dd>
				<input type="text" name="cfg_email" class="inp_one inp_w250" value="<?php echo (C("cfg_email")); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_email</dd>
		</dl>
		
		<dl>
			<dt>cookie加密码：</dt>
			<dd>
				<input type="text" name="cfg_cookie_encode" class="inp_one inp_w250" value="<?php echo (C("cfg_cookie_encode")); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_cookie_encode</dd>
		</dl>

    <div class="h3">站点状态</div>		
		<dl>
			<dt>关闭网站：</dt>
			<dd>
				<input type="radio" name="cfg_website_close" value="1" <?php if(C('cfg_website_close') == 1): ?>checked="checked"<?php endif; ?>>禁止访问 
				<input type="radio" name="cfg_website_close" value="0" <?php if(C('cfg_website_close') == 0): ?>checked="checked"<?php endif; ?>>允许访问
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_website_close</dd>
		</dl>
		<dl>
			<dt>关站提示：</dt>
			<dd>
				<textarea name="cfg_website_close_info" class="tarea_default"><?php echo (C("cfg_website_close_info")); ?></textarea><br/>
				cfg_website_close_info
			</dd>
		</dl>

		<div class="h3">手机访问</div>
		<dl>
			<dt>自动跳转：</dt>
			<dd>
				<input type="radio" name="cfg_mobile_auto" value="1" <?php if(C('cfg_mobile_auto') == 1): ?>checked="checked"<?php endif; ?>>开启 
				<input type="radio" name="cfg_mobile_auto" value="0" <?php if(C('cfg_mobile_auto') == 0): ?>checked="checked"<?php endif; ?>>关闭
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_mobile_auto</dd>
		</dl>


		<div class="h3">验证码设置</div>
		<dl>
			<dt>开启验证码：</dt>
			<dd>
				<input name="cfg_verify_register" type="checkbox" id="VerifyOn1" value="1" <?php if(C('cfg_verify_register') == 1): ?>checked="checked"<?php endif; ?>>
				<label for="VerifyOn1">会员注册 </label>

				<input name="cfg_verify_login" type="checkbox" id="VerifyOn2" value="1" <?php if(C('cfg_verify_login') == 1): ?>checked="checked"<?php endif; ?>>
				<label for="VerifyOn2">会员登录 </label>

				<input name="cfg_verify_guestbook" type="checkbox" id="VerifyOn3" value="1" <?php if(C('cfg_verify_guestbook') == 1): ?>checked="checked"<?php endif; ?>>
				<label for="VerifyOn3">留言板 </label>

				<input name="cfg_verify_review" type="checkbox" id="VerifyOn4" value="1" <?php if(C('cfg_verify_review') == 1): ?>checked="checked"<?php endif; ?>>
				<label for="VerifyOn4">文章评论 </label>

				
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_verify_XXX</dd>
		</dl>


		<div class="h3">STMP发送邮件配置</div>

		<dl>
			<dt>发件邮箱地址：</dt>
			<dd>
				<input type="text" name="cfg_email_from" class="inp_one inp_w250" value="<?php echo (C("cfg_email_from")); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_email_from</dd>
		</dl>	
		<dl>
			<dt>发件人名称：</dt>
			<dd>
				<input type="text" name="cfg_email_from_name" class="inp_one inp_w250" value="<?php echo (C("cfg_email_from_name")); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_email_from_name</dd>
		</dl>

		<dl>
			<dt>STMP服务器：</dt>
			<dd>
				<input type="text" name="cfg_email_host" class="inp_one inp_w250" value="<?php echo (C("cfg_email_host")); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_email_host</dd>
		</dl>
		<dl>
			<dt>服务器端口：</dt>
			<dd>
				<input type="text" name="cfg_email_port" class="inp_one inp_w250" value="<?php echo (C("cfg_email_port")); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_email_port</dd>
		</dl>
		<dl>
			<dt>邮箱帐号：</dt>
			<dd>
				<input type="text" name="cfg_email_loginname" class="inp_one inp_w250" value="<?php echo (C("cfg_email_loginname")); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_email_loginname</dd>
		</dl>
		<dl>
			<dt>邮箱密码：</dt>
			<dd>
				<input type="password" name="cfg_email_password" class="inp_one inp_w250" value="<?php echo (C("cfg_email_password")); ?>" />
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_email_password</dd>
		</dl>
		<dl>
			<dt>禁用词语：</dt>
			<dd>
				<textarea name="cfg_badword" class="tarea_default"><?php echo (C("cfg_badword")); ?></textarea><br/>
				cfg_badword
			</dd>
		</dl> 
        <dl>
			<dt>允许发送邮件：</dt>
			<dd>
				<input type="radio" name="cfg_feedback_email" value="1" <?php if(C('cfg_feedback_email') == 1): ?>checked="checked"<?php endif; ?>>是 
				<input type="radio" name="cfg_feedback_email" value="0" <?php if(C('cfg_feedback_email') == 0): ?>checked="checked"<?php endif; ?>>否 
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_feedback_email</dd>
		</dl>
        <dl>
			<dt>允许匿名评论：</dt>
			<dd>
				<input type="radio" name="cfg_feedback_guest" value="1" <?php if(C('cfg_feedback_guest') == 1): ?>checked="checked"<?php endif; ?>>是 
				<input type="radio" name="cfg_feedback_guest" value="0" <?php if(C('cfg_feedback_guest') == 0): ?>checked="checked"<?php endif; ?>>否 
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_feedback_guest</dd>
		</dl>

		<div class="h3">会员设置</div>
		<dl>
			<dt>禁止使用的昵称：</dt>
			<dd>
				<textarea name="cfg_member_notallow"  class="tarea_default"><?php echo (C("cfg_member_notallow")); ?></textarea><br/>
				cfg_member_notallow
			</dd>
		</dl>
		<dl>
			<dt>邮件验证：</dt>
			<dd>
				<input type="radio" name="cfg_member_verifyemail" value="1" <?php if(C('cfg_member_verifyemail') == 1): ?>checked="checked"<?php endif; ?>>开启 
				<input type="radio" name="cfg_member_verifyemail" value="0" <?php if(C('cfg_member_verifyemail') == 0): ?>checked="checked"<?php endif; ?>>关闭
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_member_verifyemail</dd>
		</dl>
		<div class="h3">附件设置</div>
		<dl>
			<dt>允许上传大小：</dt>
			<dd>
				<input type="text" name="cfg_upload_maxsize" class="inp_one inp_w250" value="<?php echo (C("cfg_upload_maxsize")); ?>" />
			</dd>
			<dd class="desc">KB</dd>
			<dd>cfg_upload_maxsize</dd>
		</dl>
		<dl>
			<dt>缩略图组尺寸：</dt>
			<dd>
				<input type="text" name="cfg_imgthumb_size" class="inp_one inp_w250" value="<?php echo (C("cfg_imgthumb_size")); ?>" />
			</dd>
			<dd class="desc">长X高,如60X60,50X100</dd>
			<dd>cfg_imgthumb_size</dd>
		</dl>
		<dl>
			<dt>缩略图生成方式：</dt>
			<dd>
				<input type="radio" name="cfg_imgthumb_type" value="1" <?php if(C('cfg_imgthumb_type') == 1): ?>checked="checked"<?php endif; ?>>设置大小截取 
				<input type="radio" name="cfg_imgthumb_type" value="0" <?php if(C('cfg_imgthumb_type') == 0): ?>checked="checked"<?php endif; ?>>原图等比例缩略
			</dd>
			<dd class="desc"></dd>
			<dd>cfg_imgthumb_type</dd>
		</dl>

		<dl>
			<dt>固宽缩略图组长度：</dt>
			<dd>
				<input type="text" name="cfg_imgthumb_width" class="inp_one inp_w250" value="<?php echo (C("cfg_imgthumb_width")); ?>" />
			</dd>
			<dd class="desc">固定宽度等比缩略,如60,50</dd>
			<dd>cfg_imgthumb_width</dd>
		</dl>

		</div>
		<div class="form_b">
			<input type="submit" class="btn_blue" id="submit" value="提 交">
		</div>
	   </form>
	</div>


</body>
</html>