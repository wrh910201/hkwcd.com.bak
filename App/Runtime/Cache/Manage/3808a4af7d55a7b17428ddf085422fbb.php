<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
    <div class="pos">编辑客户组</div>
    <div class="form">
        <form method='post' id="form_do" name="form_do" action="<?php echo U(GROUP_NAME. '/Clientgroup/doEdit');?>">
            <dl>
                <dt>客户组名：</dt>
                <dd>
                    <input type="text" name="name" class="inp_one" id="name" value="<?php echo ($client_group["name"]); ?>"/>
                </dd>
            </dl>
            <dl>
                <dt>客户组英文名：</dt>
                <dd>
                    <input type="text" name="en_name" class="inp_one" id="en_name" value="<?php echo ($client_group["en_name"]); ?>"/>
                </dd>
            </dl>

            <div class="form_b">
                <input type="hidden" name="id" value="<?php echo ($client_group["id"]); ?>"/>
                <input type="submit" class="btn_blue" id="submit" value="提 交">
            </div>
        </form>
    </div>
</div>


</body>
</html>