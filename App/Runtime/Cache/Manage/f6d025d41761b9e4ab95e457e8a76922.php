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
     var APP = '__APP__';
     var SELF = '__SELF__';
     var PUBLIC = '__PUBLIC__';
        //-->
        </script>
</head>
<body>
<div class="main">
    <div class="pos"><?php echo ($type); ?> 
    </div> 
    <div class="operate">
        <div class="left"> 
                <input type="button" onclick="goUrl('<?php echo U(GROUP_NAME. '/Express/add', array('pid'=>$pid));?>')" class="btn_blue" value="添加运单">
                <input type="button" onclick="doConfirmBatch('<?php echo U(GROUP_NAME.'/Express/del', array('batchFlag' => 1,'pid' => $pid));?>', '确实要删除选择项吗？')" class="btn_blue" value="删除">
        </div>
        <?php if(ACTION_NAME == "index"): ?><div class="left_pad">
            <form method="post" action="<?php echo U(GROUP_NAME. '/Express/index');?>">
                <input type="text" name="keyword" title="关键字" placeholder="请输入运单号" class="inp_default" value="<?php echo ($keyword); ?>"> 
                <input type="submit" class="btn_blue" value="查  询">
            </form>
        </div><?php endif; ?>
    </div>
    <div class="list">    
    <form action="<?php echo U(GROUP_NAME.'/Express/del');?>" method="post" id="form_do" name="form_do">
        <table width="100%">
            <tr>
                <th><input type="checkbox" id="check"></th>
                <th>运单号</th>
                <th>快递类型</th>
                <th>邮编号</th>
                <th>目的国家/地区</th>
                <th>省/城市</th>
                <th>取件日期</th>
                <th>接口查询</th> 
                <th>操作</th>
            </tr>
			<?php if(is_array($vlist)): foreach($vlist as $key=>$v): ?><tr>
                <td><input type="checkbox" name="key[]" value="<?php echo ($v["id"]); ?>"></td> 
                <td><?php echo ($v["dda"]); ?></td> 
                <td><?php echo ($v["leix"]); ?></td> 
                <td><?php echo ($v["ckh"]); ?></td>
                <td><?php echo ($v["gjj"]); ?></td>
                <td><?php echo ($v["lmdd"]); ?></td>
                <td><?php echo ($v["qjrq"]); ?></td>
                <td><?php if($v["status"] == "1"): ?>是<?php else: ?>否<?php endif; ?></td>
                <td> 
                <a href="<?php echo U(GROUP_NAME. '/Expressdetail/index',array('fkid' => $v['id']));?>">运单进展</a> 
                <a href="<?php echo U(GROUP_NAME. '/Express/edit',array('id' => $v['id']));?>">编辑</a>
                <a href="javascript:void(0);" onclick="doConfirm('<?php echo U(GROUP_NAME. '/Express/del',array('id' => $v['id']));?>', '确实要删除选择项吗？')" >删除</a>
            
				</td>
            </tr><?php endforeach; endif; ?>
        </table>
        <div class="th" style="clear: both;"><?php echo ($page); ?></div>
    </form>
    </div>
</div>
</body>
</html>