<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
    <div class="pos"><?php echo ($type); ?></div>
    <div class="operate">
        <div class="left">
            <input type="button" onclick="goUrl('<?php echo U(GROUP_NAME.'/Region/add');?>')" class="btn_blue" value="添加地区">
            <input type="button" onclick="doConfirmBatch('<?php echo U(GROUP_NAME.'/Region/del', array('batchFlag' => 1));?>', '确实要删除选择项吗？')" class="btn_blue" value="删除">
        </div>
    </div>
</div>
<div class="list">
    <form action="<?php echo U(GROUP_NAME.'/Region/sort', array('pid' => $pid));?>" method="post" id="form_do" name="form_do">
        <table width="100%">
            <tr>
                <th><input type="checkbox" id="check"></th>
                <th>编号</th>
                <th>地区别名</th>
                <!--<th>排序</th>-->
                <th>操作</th>
            </tr>
            <?php if(is_array($region_list)): foreach($region_list as $key=>$v): ?><tr>
                    <td><input type="checkbox" name="key[]" value="<?php echo ($v["id"]); ?>"></td>
                    <td><?php echo ($v["id"]); ?></td>
                    <td><?php echo ($v["alias"]); ?></td>
                    <td>
                        <a href="<?php echo U(GROUP_NAME.'/Region/edit',array('id' => $v['id']));?>">详情</a>
                        <a href="javascript:void(0);" onclick="doConfirm('<?php echo U(GROUP_NAME. '/Region/del', array('id' => $v['id']));?>', '确实要删除选择项吗？')">删除</a>
                    </td>
                </tr><?php endforeach; endif; ?>
        </table>
        <div class="th" style="clear: both;"><?php echo ($page); ?></div>
    </form>
</div>
</div>
</body>
</html>