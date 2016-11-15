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
    <?php if(ACTION_NAME == "index"): ?>| <?php if(is_array($poscate)): foreach($poscate as $key=>$v): ?><a href="<?php echo U(GROUP_NAME. '/' . ucfirst($v['tablename']) .'/index', array('pid' => $v['id']));?>"><?php echo ($v["name"]); ?> &gt; </a><?php endforeach; endif; endif; ?>
    </div>
    <?php if($subcate): ?><div class="sub"><span>子栏目：</span>
        <?php if(is_array($subcate)): foreach($subcate as $key=>$v): ?><a href="<?php echo U(GROUP_NAME. '/'. ucfirst($v['tablename']) . '/index', array('pid' => $v['id']));?>"><?php echo ($v["name"]); ?></a><?php endforeach; endif; ?>
    </div><?php endif; ?>
    <div class="operate">
        <div class="left">
            <?php if(ACTION_NAME == "index"): ?><input type="button" onclick="goUrl('<?php echo U(GROUP_NAME. '/Article/add', array('pid'=>$pid));?>')" class="btn_blue" value="添加文章">
                <input type="button" onclick="doConfirmBatch('<?php echo U(GROUP_NAME.'/Article/del', array('batchFlag' => 1,'pid' => $pid));?>', '确实要删除选择项吗？')" class="btn_blue" value="删除">
                <input type="button" onclick="goUrl('<?php echo U(GROUP_NAME.'/Article/trach', array('pid' => $pid));?>')" class="btn_green" value="回收站">
            <?php else: ?>
                <input type="button" onclick="goUrl('<?php echo U(GROUP_NAME. '/Article/index', array('pid'=>$pid));?>')" class="btn_blue" value="返回">
                <input type="button" onclick="doGoBatch('<?php echo U(GROUP_NAME.'/Article/restore', array('batchFlag' => 1, 'pid' => $pid));?>')" class="btn_green" value="还原">
                <input type="button" onclick="doConfirmBatch('<?php echo U(GROUP_NAME.'/Article/clear', array('batchFlag' => 1, 'pid' => $pid));?>', '确实要彻底删除选择项吗？')" class="btn_orange" value="彻底删除"><?php endif; ?>


            
        </div>
        <?php if(ACTION_NAME == "index"): ?><div class="left_pad">
            <form method="post" action="<?php echo U(GROUP_NAME. '/Article/index');?>">
                <input type="text" name="keyword" title="关键字" class="inp_default" value="<?php echo ($keyword); ?>">
                <input type="hidden" name="pid" value="<?php echo ($pid); ?>">
                <input type="submit" class="btn_blue" value="查  询">
            </form>
        </div><?php endif; ?>
    </div>
    <div class="list">    
    <form action="<?php echo U(GROUP_NAME.'/Article/del');?>" method="post" id="form_do" name="form_do">
        <table width="100%">
            <tr>
                <th><input type="checkbox" id="check"></th>
                <th>编号</th>
                <th>标题</th>
                <th>分类</th>
                <th>点击次数</th>
                <th>发布时间</th>
                <th>操作</th>
            </tr>
			<?php if(is_array($vlist)): foreach($vlist as $key=>$v): ?><tr>
                <td><input type="checkbox" name="key[]" value="<?php echo ($v["id"]); ?>"></td>
                <td><?php echo ($v["id"]); ?></td>
                <td class="aleft" style="color:<?php echo ($v["color"]); ?>"><?php echo ($v["title"]); if($v["flag"] > 0): ?><span style="color:#079B04;">[<?php echo (flag2str($v["flag"])); ?>]</span><?php endif; ?></td>
                <td><?php echo ($v["catename"]); ?></td>
                <td><?php echo ($v["click"]); ?></td>
                <td><?php echo (date('Y-m-d H:i:s', $v["publishtime"])); ?></td>
                <td>
                <?php if(ACTION_NAME == "index"): if(($v["flag"] & B_JUMP) && !empty($v['jumpurl']) ): ?><a href="<?php echo (golinkencode($v["jumpurl"])); ?>" target="_blank">查看</a>
                <?php else: ?>
                <a href="<?php echo U(C('DEFAULT_GROUP'). '/Show/article',array('id' => $v['id']), '').'#'.rand(1000,time());?>" target="_blank">查看</a><?php endif; ?>
                <a href="<?php echo U(GROUP_NAME. '/Article/edit',array('id' => $v['id'],'pid' => $pid), '');?>">编辑</a>
                <a href="javascript:void(0);" onclick="doConfirm('<?php echo U(GROUP_NAME. '/Article/del',array('id' => $v['id'], 'pid' => $pid), '');?>', '确实要删除选择项吗？')" >删除</a>
                <?php else: ?>
                <a href="<?php echo U(GROUP_NAME. '/Article/restore',array('id' => $v['id'], 'pid' => $pid), '');?>">还原</a>
                <a href="javascript:void(0);" onclick="doConfirm('<?php echo U(GROUP_NAME. '/Article/clear',array('id' => $v['id'], 'pid' => $pid), '');?>', '确实要删除选择项吗？')">彻底删除</a><?php endif; ?>
				</td>
            </tr><?php endforeach; endif; ?>
        </table>
        <div class="th" style="clear: both;"><?php echo ($page); ?></div>
    </form>
    </div>
</div>
</body>
</html>