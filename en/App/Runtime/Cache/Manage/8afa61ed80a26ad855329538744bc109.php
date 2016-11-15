<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
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
</head>
<body>
<div class="main">
    <div class="pos"><?php echo ($type); ?>
    </div>
    <div class="operate">
        <div class="left">
            <input type="button" onclick="goUrl('<?php echo U(GROUP_NAME. '/Block/add');?>')" class="btn_blue" value="添加内容">
            <input type="button" onclick="doConfirmBatch('<?php echo U(GROUP_NAME.'/Block/del', array('batchFlag' => 1));?>', '确实要删除选择项吗？')" class="btn_blue" value="删除">      
        </div>
    </div>
    <div class="list">    
    <form action="<?php echo U(GROUP_NAME.'/Block/delAll');?>" method="post" id="form_do" name="form_do">
        <table width="100%">
            <tr>
                <th><input type="checkbox" id="check"></th>
                <th>编号</th>
                <th>名称[说明]</th>
                <th>类型</th>
                <th>模板调用代码</th>
                <th>操作</th>
            </tr>
			<?php if(is_array($vlist)): foreach($vlist as $key=>$v): ?><tr>
                <td><input type="checkbox" name="key[]" value="<?php echo ($v["id"]); ?>"></td>
                <td><?php echo ($v["id"]); ?></td>
                <td class="aleft"><?php echo ($v["name"]); ?> [<?php echo (str2sub($v["remark"],10)); ?>]</td>
                <td><?php echo (getvalueofitem('blocktype',$v["blocktype"])); ?></td>
                <td class="aleft">&lt;yang:block name='<?php echo ($v["name"]); ?>' /&gt; </td>
                <td>
                    <a href="<?php echo U(GROUP_NAME. '/Block/edit',array('id' => $v['id']), '');?>">编辑</a>
                    <a href="javascript:void(0);" onclick="doConfirm('<?php echo U(GROUP_NAME. '/Block/del',array('id' => $v['id']), '');?>', '确实要删除选择项吗？')">删除</a>
				</td>
            </tr><?php endforeach; endif; ?>
        </table>
        <div class="th" style="clear: both;"><?php echo ($page); ?></div>
    </form>
    </div>
</div>
</body>
</html>