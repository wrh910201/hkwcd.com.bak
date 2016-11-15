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
    <div class="pos"><?php echo ($type); ?>
    </div>
    <div class="operate">
        <div class="left">
            <input type="button" onclick="goUrl('<?php echo U(GROUP_NAME. '/Client/add');?>')" class="btn_blue" value="添加会员">
            <input type="button" onclick="doConfirmBatch('<?php echo U(GROUP_NAME.'/Client/del', array('batchFlag' => 1));?>', '确实要删除选择项吗？')" class="btn_blue" value="删除">
        </div>
        <?php if(ACTION_NAME == "index"): ?><div class="left_pad">
                <form method="post" action="<?php echo U(GROUP_NAME. '/Client/index');?>">
                    <input type="text" name="keyword" title="关键字" class="inp_default" value="<?php echo ($keyword); ?>">
                    <input type="submit" class="btn_blue" value="查  询">
                </form>
            </div><?php endif; ?>
    </div>
    <div class="list">
        <form action="<?php echo U(GROUP_NAME.'/Client/multiDel');?>" method="post" id="form_do" name="form_do">
            <table width="100%">
                <tr>
                    <th><input type="checkbox" id="check"></th>
                    <th>用户名</th>
                    <th>姓名</th>
                    <th>分组</th>
                    <th>公司名称</th>
                    <th>登录时间</th>
                    <th>登录ip</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                <?php if(is_array($client_list)): foreach($client_list as $key=>$v): ?><tr>
                        <td><input type="checkbox" name="key[]" value="<?php echo ($v["id"]); ?>"></td>
                        <td><?php echo ($v["username"]); ?></td>
                        <td><?php echo ($v["full_name"]); ?></td>
                        <td><?php echo ($group_list[$v['group_id']]); ?></td>
                        <td><?php echo ($v["company"]); ?></td>
                        <td><?php echo ($v["last_login"]); ?></td>
                        <td><?php echo ($v["last_ip"]); ?></td>
                        <td><?php if($v['is_locked']): ?>锁定<?php else: ?>正常<?php endif; ?></td>
                        <td>
                            <a href="<?php echo U(GROUP_NAME. '/Client/edit',array('id' => $v['id']), '');?>">详情</a>
                            <a href="<?php echo U(GROUP_NAME. '/Client/price',array('id' => $v['id']), '');?>">查看价格表</a>
                            <a href="javascript:void(0);" onclick="doConfirm('<?php echo U(GROUP_NAME. '/Client/lock',array('id' => $v['id']), '');?>', '确实要<?php if($v['is_locked']): ?>解锁<?php else: ?>锁定<?php endif; ?>选择项吗？')"><?php if($v['is_locked']): ?>解锁<?php else: ?>锁定<?php endif; ?></a>
                            <a href="javascript:void(0);" onclick="doConfirm('<?php echo U(GROUP_NAME. '/Client/del',array('id' => $v['id']), '');?>', '确实要删除选择项吗？')">删除</a>
                            <a href="javascript:void(0);" onclick="doConfirm('<?php echo U(GROUP_NAME. '/Client/password',array('id' => $v['id']), '');?>', '确实对选择项重置密码吗？')">重置密码</a>
                            <?php if($v['real_password']): ?><a href="javascript:void(0);" onclick="alert('<?php echo ($v["real_password"]); ?>')">查看密码</a><?php endif; ?>
                        </td>
                    </tr><?php endforeach; endif; ?>
            </table>
            <div class="th" style="clear: both;"><?php echo ($page); ?></div>
        </form>
    </div>
</div>
</body>
</html>