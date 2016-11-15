<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel='stylesheet' type="text/css" href="__PUBLIC__/css/style.css" />
<script type="text/javascript" src="__PUBLIC__/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/common.js"></script>
<script type="text/javascript" src="/Public/wcd56/usercenter/layer/layer.js"></script>
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
            <input type="button" onclick="goUrl('<?php echo U(GROUP_NAME. '/Country/add', array('pid' => $pid));?>')" class="btn_blue" value="添加">
        <input type="button" onclick="doConfirmBatch('<?php echo U(GROUP_NAME.'/Country/del', array('batchFlag' => 1,'pid' => $pid));?>', '确实要删除选择项吗？')" class="btn_blue" value="删除">
        <input type="button" onclick="document.getElementById('form_do').submit();" class="btn_blue" value="更新排序"></div>
        <?php if($pid > 0): ?><input type="button" onclick="goUrl('<?php echo U(GROUP_NAME. '/Country/index', array('pid' => 0));?>')" class="btn_blue" value="返回顶级">
        <?php else: ?>
        <input type="button" onclick="goUrl('<?php echo U(GROUP_NAME. '/Country/createJsOfCountry', array('pid' => 0));?>')" class="btn_blue" value="生成JS">
        <input onclick="select_region();" type="button"  class="btn_blue" value="添加到地区"><?php endif; ?>
		<?php if(ACTION_NAME == "index"): ?><div class="left_pad">
            <form method="post" action="<?php echo U(GROUP_NAME. '/Country/index');?>">
                <input type="text" name="keyword" title="关键字" placeholder="请输入中文名" class="inp_default" value="<?php echo ($keyword); ?>"> 
                <input type="submit" class="btn_blue" value="查  询">
            </form>
        </div><?php endif; ?>
        </div>
    </div>
    <div class="list">    
    <form action="<?php echo U(GROUP_NAME.'/Country/sort', array('pid' => $pid));?>" method="post" id="form_do" name="form_do">
        <table width="100%">
            <tr>
                <th><input type="checkbox" id="check"></th>
                <th>编号</th>
                <th>中文名</th> 
                <th>英文名</th>
                <th>排序</th>
                <th>操作</th>
            </tr>
			<?php if(is_array($vlist)): foreach($vlist as $key=>$v): ?><tr>
                <td><input type="checkbox" name="key[]" value="<?php echo ($v["id"]); ?>"></td>
                <td><?php echo ($v["id"]); ?></td>
                <td class="aleft"><a href="<?php echo U(GROUP_NAME.'/Country/index',array('pid' => $v['id']));?>"><?php echo ($v["name"]); ?></a></td> 
				<td><?php echo ($v["ename"]); ?></td>
                <td><input type="text" style=" width:40px;" name="<?php echo ($v["id"]); ?>" value="<?php echo ($v["sort"]); ?>" /></td>
                <td>
                <a href="<?php echo U(GROUP_NAME.'/Country/index',array('pid' => $v['id']));?>">添加城市</a>
                <a href="<?php echo U(GROUP_NAME.'/Country/indexzip',array('pid' => $v['id']));?>">添加邮编</a>
                <a href="<?php echo U(GROUP_NAME.'/Country/edit',array('id' => $v['id'], 'pid' => $v['pid']));?>">修改</a>
                <a href="javascript:void(0);" onclick="doConfirm('<?php echo U(GROUP_NAME. '/Country/del', array('id' => $v['id'], 'pid' => $v['pid']));?>', '确实要删除选择项吗？')">删除</a>
				</td>
            </tr><?php endforeach; endif; ?>
        </table>
        <div class="th" style="clear: both;"><?php echo ($page); ?></div>
    </form>
    </div>
</div>
<div id="select_region_form">
    <div class="form">
        <form method='post' id="" name="form_do" action="return false">
            <dl style="text-align: center">
                <dt style="width: 100px">自定义地区：</dt>
                <dd>
                    <select name="region_id">
                        <?php if(is_array($region_list)): foreach($region_list as $key=>$v): ?><option value="<?php echo ($v["id"]); ?>"><?php echo ($v["alias"]); ?></option><?php endforeach; endif; ?>
                    </select>
                </dd>
            </dl>
            <div class="form_b">
                <input type="button" class="btn_blue" id="submit" value="提 交">
            </div>
        </form>
    </div>
</div>
</body>
<script type="text/javascript">
    var loading = null;
    function select_region() {
        layer.open({
            type: 1,
            title: '选择地区',
            skin: 'layui-layer-rim', //加上边框
            area: ['420px', '240px'], //宽高
            content: $('#select_region_form'),
        });
    }

    $('#submit').click(function() {
        confirm_add_to_region();
    });

    function confirm_add_to_region() {
//        loading = layer.load();
        var region_id = $('select[name=region_id]').val();
        var url = "/Manage/Region/addCountry/rid/" + region_id;
        doConfirmBatch(url, '确定添加到自定义地区？');
    }
</script>
</html>