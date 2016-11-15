<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>华度,香港华度,香港华度货运有限公司唯一官方网站</title>
<meta name="keywords" content="华度,香港华度,香港华度货运有限公司" />
<meta name="description" content="香港华度货运有限公司成立于2012年4月7日，专营国际速递，国际空运。咨询电话020-36311406！" /> 
<script type="text/javascript" src="/Public/config/js/jquery-1.3.2.min.js"></script>  
<script type="text/javascript" src="/Public/config/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="/Public/config/js/messages_cn.js"></script>
<link type="text/css" rel="stylesheet" href="/Public/config/css/msg.css" />
<link href="__PUBLIC__/css/hd.css" rel="stylesheet" type="text/css">
<link href="__PUBLIC__/css/zy.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/style.css" />
<script type="text/javascript">
    $(function () {
        //表单验证JS
        $("#form_do").validate({
            //出错时添加的标签
            errorElement: "span",
            success: function (label) {
                //正确时的样式
                label.text(" ").addClass("success");
            }
        });
    });
    function changeVcode(obj) {
        $("#VCode").attr("src", '/Public/verify.html' + '#' + Math.random());
        return false;
    }
</script>
<script type="text/javascript" src="__PUBLIC__/js/common.js"></script> 
</head> 
<body> 
<link href="/Public/config/css/tipswindown.css" rel="stylesheet" type="text/css" />
<script src="/Public/config/js/tipswindown.js" type="text/javascript"></script>
<div class="top">
    <div class="m_1100"> 
     <div class="conne">  
        <div class="c_l"> <div id="ggao"><b>最新公告：</b><span><marquee  scrollamount="2"><?php
 $where = array('endtime' => array('gt',time())); if (0 > 0) { import('ORG.Util.Page'); $count = M('announce')->where($where)->count(); $thisPage = new Page($count, 0); $thisPage->rollPage = 3; $thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%'); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "1"; } $_announcelist = M('announce')->where($where)->order("starttime DESC")->limit($limit)->select(); if (empty($_announcelist)) { $_announcelist = array(); } foreach($_announcelist as $autoindex => $announcelist): if(0) $announcelist['title'] = str2sub($announcelist['title'], 0, 0); if(100) $announcelist['content'] = str2sub(strip_tags($announcelist['content']), 100, 0); echo ($announcelist["content"]); endforeach;?></marquee></span></div></div> 
        <div class="c_r">
            <?php if($_SESSION['hkwcd_user']!= null): ?><a href="<?php echo U('Profile/index');?>">个人中心</a> | <a href="<?php echo U('User/logout');?>">登出</a>
            <?php else: ?>
                <a href="javascript:win_login()"  >登录</a><?php endif; ?>
            | <a href="javascript:zh_tran('s');" class="zh_click" id="zh_click_s">简体</a>  |  <a href="javascript:zh_tran('t');" class="zh_click" id="zh_click_t">繁體</a>  |  <a href="/en/"  >English</a>  |  <a href="#" onclick="AddFavorite('http://www.hkwcd.com', '香港华度货运有限公司')">加入收藏</a> | <a href="<?php echo U(GROUP_NAME.'/Guestbook/index');?>">在线留言</a>  |  <?php
 import('Class.Category', APP_PATH); $type = Category::getSelf(getCategory(0), 10); $type['url'] = getUrl($type); ?><a href="<?php echo ($type["url"]); ?>">联系我们</a>
		</div> 
      </div>
	  <div class="min">
    	<div class="logo"><a href="http://www.hkwcd.com"><img src="__PUBLIC__/images/t_b.gif"></a></div>
        <div class="navi">
          <div class="serch"><font style="font-family:Georgia; font-size:22px; font-style:italic; color:#005dab;">联系电话:020-36311406</font><br>
            <script type="text/javascript" src="__PUBLIC__/js/biweb.js"></script>
           <script type="text/javascript" src="__PUBLIC__/js/common.js" ></script>  
           <form id="SearchForm" name="SearchForm" method="post"  action="/Search/index.html"> 
	       <select name="cid" class="selectcss" style="float: left; height:23px;">
	  	    <?php
 $_typeid = intval(0); $type = "son"; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); $__catlist = getCategory(1); import('Class.Category', APP_PATH); if (0 == 0) { $__catlist = Category::clearPageAndLink($__catlist); } if($_typeid == 0 || $type == 'top') { $_catlist = Category::unlimitedForLayer($__catlist); }else { if ($type == 'self') { $_typeinfo = Category::getSelf($__catlist, $_typeid ); $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeinfo['pid']); }else { $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeid); } } foreach($_catlist as $autoindex => $catlist): if($autoindex >= 30) break; $catlist['url'] = getUrl($catlist); ?><option value="<?php echo ($catlist["id"]); ?>" <?php if($catlist["id"] == $cid): ?>selected="selected"<?php endif; ?>><?php echo ($catlist["name"]); ?></option><?php endforeach;?>
	      </select> 
          <input name="keyword" type="text" class="sinput" id="keyword" size="15" height="21"  value="<?php if(empty($keyword)): ?>请输入关键词<?php else: echo ($keyword); endif; ?>" onfocus="if(this.value=='请输入关键词'){this.value='';}" onblur="if(this.value==''){this.value='请输入关键词';}" />
	      <input type="submit" value="查询"  style="padding-left: 1px; width:40px" class="sbtn"/> 
        </form>

        </div>

         
          <div class="nav">
            <ul>
            <li><a href="/index.php">首页</a></li>
            <?php
 $_typeid = 0; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); $_navlist = getCategory(1); import('Class.Category', APP_PATH); if($_typeid == 0) { $_navlist = Category::unlimitedForLayer($_navlist); }else { $_navlist = Category::unlimitedForLayer($_navlist, 'child', $_typeid); } foreach($_navlist as $autoindex => $navlist):{ $navlist['url'] = getUrl($navlist); if(-1!='-1'){ if($autoindex >= -1) break; } } ?><li><a href='<?php echo ($navlist["url"]); ?>' target="<?php echo ($navlist["target"]); ?>"  ><?php echo ($navlist["name"]); ?></a>
                <table cellpadding="0" cellspacing="0"><tr><td>
                    <ul>
                     <?php
 $_typeid = intval($navlist[id]); $type = "son"; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); $__catlist = getCategory(1); import('Class.Category', APP_PATH); if (1 == 0) { $__catlist = Category::clearPageAndLink($__catlist); } if($_typeid == 0 || $type == 'top') { $_catlist = Category::unlimitedForLayer($__catlist); }else { if ($type == 'self') { $_typeinfo = Category::getSelf($__catlist, $_typeid ); $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeinfo['pid']); }else { $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeid); } } foreach($_catlist as $autoindex => $catlist): if($autoindex >= 30) break; $catlist['url'] = getUrl($catlist); ?><li><a href="<?php echo ($catlist["url"]); ?>" target="<?php echo ($catlist["target"]); ?>"  ><?php echo ($catlist["name"]); ?></a></li><?php endforeach;?>
                    </ul>
                  </td></tr>
             </table>
            </li><?php endforeach;?>
            </ul>
            </div>
        </div>
    </div>
    </div>
   </div>

     
<div id="sbanner" class="img_div1"></div> 

 <div class="main_a"><div class="m_1100">
  <div class="b_block">
    <div class="midd" style="width:831px;">
    	<div class="x_nav"><?php
 $_sname = ""; $_typeid =-1; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); if ($_typeid == 0 && $_sname == '') { $_sname = isset($title) ? $title : ''; } echo getPosition($_typeid, $_sname, "", 0, ""); ?></div>
        <div class="zgwen">　　
            <h1  style="text-align: center; border-bottom:1px dashed #CCC; line-height:36px; font-size:16px;color: #005daa;">尊敬的客户，欢迎您访问我们的网站，请仔细填写下表，我们将尽快与您联系，谢谢！</h1>
               <div class="form">
		 
                    <form method='post' id="form_do" name="form_do" action="/Guestbook/add.html">
		            <dl>
			            <dt>姓 名：</dt>
			            <dd>
				            <input type="text" name="username" class="inp_one required" />
			            </dd>
		            </dl>
		            <dl>
			            <dt> 电 话：</dt>
			            <dd>
				            <input type="text" name="tel" class="inp_one required"/>
			            </dd>
		            </dl>
		            <dl>
			            <dt>Email：</dt>
			            <dd>
				            <input type="text" name="email" class="inp_one email" />
			            </dd>
		            </dl>
		            <dl>
			            <dt>Q Q：</dt>
			            <dd>
				            <input type="text" name="qq" class="inp_one required" />
			            </dd>
		            </dl>
		            <dl>
			            <dt>留 言：</dt>
			            <dd>
				            <textarea name="content" cols="50" rows="10"></textarea>
			            </dd>
		            </dl>

		            <?php if(C('cfg_verify_guestbook') == 1): ?><dl>
			            <dt>验证码</dt>
			            <dd>
				            <input type="text" name="vcode" class="inp_small"  style="margin-top: -20px;margin-right: 5px;"/><img src="/Public/verify.html" id="VCode" onclick="javascript:changeVcode(this);" />
			            </dd>
		            </dl><?php endif; ?>
		            <div class="form_b">		
			            <input type="submit" class="btn_blue" style="height: 40px;width: 80px;font-size: 16px;" id="submit" value="提 交">
		            </div>
	               </form>



        </div>

        </div>
    </div>
            <div class="rig">
                

              <!--公司简介-->  
           <?php if($flag_son): ?><div class="sidebar" >  
	        <div class="titl">栏目导航</div>
	         <ul class="navbar list-none">  
		       <?php
 $_typeid = intval($cid); $type = "son"; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); $__catlist = getCategory(1); import('Class.Category', APP_PATH); if (1 == 0) { $__catlist = Category::clearPageAndLink($__catlist); } if($_typeid == 0 || $type == 'top') { $_catlist = Category::unlimitedForLayer($__catlist); }else { if ($type == 'self') { $_typeinfo = Category::getSelf($__catlist, $_typeid ); $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeinfo['pid']); }else { $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeid); } } foreach($_catlist as $autoindex => $catlist): if($autoindex >= 30) break; $catlist['url'] = getUrl($catlist); ?><li>
                 <h4><a href="<?php echo ($catlist["url"]); ?>"><?php echo ($catlist["name"]); ?></a></h4>
                 <div class="list"> 
                 <?php
 $_typeid = intval($catlist['id']); $type = "son"; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); $__catlistsub = getCategory(1); import('Class.Category', APP_PATH); if($_typeid == 0 || $type == 'top') { $_catlistsub = Category::unlimitedForLayer($__catlistsub); }else { if ($type == 'self') { $_typeinfo = Category::getSelf($__catlistsub, $_typeid ); $_catlistsub = Category::unlimitedForLayer($__catlistsub, 'child', $_typeinfo['pid']); }else { $_catlistsub = Category::unlimitedForLayer($__catlistsub, 'child', $_typeid); } } foreach($_catlistsub as $autoindex => $catlistsub): if($autoindex >= 10) break; $catlistsub['url'] = getUrl($catlistsub); ?><a href="<?php echo ($catlistsub["url"]); ?>"><?php echo ($catlistsub["name"]); ?></a><?php endforeach;?>
                 </div>
               </li><?php endforeach;?> 
            </ul>  
	       </div>
            <?php else: ?>
	        <?php if($cate['pid'] > 0): ?><div class="sidebar" >  
             <div class="titl">栏目导航</div> 
              <ul class="navbar list-none">  
		        <?php
 $_typeid = intval($cate['pid']); $type = "son"; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); $__catlist = getCategory(1); import('Class.Category', APP_PATH); if (1 == 0) { $__catlist = Category::clearPageAndLink($__catlist); } if($_typeid == 0 || $type == 'top') { $_catlist = Category::unlimitedForLayer($__catlist); }else { if ($type == 'self') { $_typeinfo = Category::getSelf($__catlist, $_typeid ); $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeinfo['pid']); }else { $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeid); } } foreach($_catlist as $autoindex => $catlist): if($autoindex >= 30) break; $catlist['url'] = getUrl($catlist); ?><li>
                 <h4><a href="<?php echo ($catlist["url"]); ?>"><?php echo ($catlist["name"]); ?></a></h4>
                <div class="list"> 
                 <?php
 $_typeid = intval($catlist['id']); $type = "son"; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); $__catlistsub = getCategory(1); import('Class.Category', APP_PATH); if($_typeid == 0 || $type == 'top') { $_catlistsub = Category::unlimitedForLayer($__catlistsub); }else { if ($type == 'self') { $_typeinfo = Category::getSelf($__catlistsub, $_typeid ); $_catlistsub = Category::unlimitedForLayer($__catlistsub, 'child', $_typeinfo['pid']); }else { $_catlistsub = Category::unlimitedForLayer($__catlistsub, 'child', $_typeid); } } foreach($_catlistsub as $autoindex => $catlistsub): if($autoindex >= 10) break; $catlistsub['url'] = getUrl($catlistsub); ?><a href="<?php echo ($catlistsub["url"]); ?>"><?php echo ($catlistsub["name"]); ?></a><?php endforeach;?>
                 </div>
                </li><?php endforeach;?> 
            </ul> 
	       </div><?php endif; endif; ?>  
                  <div class="huoy">
                	<div class="ttl_la">｜快件出口简</div>
                    <div class="text_la" style="padding:5px;">
                    <?php  $_typeid = 6; $_keyword = ""; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); if ($_typeid>0 || substr($_typeid,0,1) == '$') { import('Class.Category', APP_PATH); $_selfcate = Category::getSelf(getCategory(), $_typeid); $_tablename = strtolower($_selfcate['tablename']); $ids = Category::getChildsId(getCategory(), $_typeid, true); $where = array($_tablename.'.status' => 0, $_tablename .'.cid'=> array('IN',$ids)); }else { $_tablename = 'article'; $where = array($_tablename.'.status' => 0); } if ($_keyword != '') { $where[$_tablename.'.title'] = array('like','%'.$_keyword.'%'); } if (0 > 0) { $where['_string'] = $_tablename.'.flag & 0 = 0 '; } if (!empty($_tablename) && $_tablename != 'page') { if (0 > 0) { import('ORG.Util.Page'); $count = D(ucfirst($_tablename ).'View')->where($where)->count(); $thisPage = new Page($count, 0); $ename = I('e', '', 'htmlspecialchars,trim'); if (!empty($ename) && C('URL_ROUTER_ON') == true) { $thisPage->url = ''.$ename. '/p'; } $thisPage->rollPage = 3; $thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%'); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "3"; } $_list = D(ucfirst($_tablename ).'View')->where($where)->order("id")->limit($limit)->select(); if (empty($_list)) { $_list = array(); } }else { $_list = array(); } foreach($_list as $autoindex => $list): $_jumpflag = ($list['flag'] & B_JUMP) == B_JUMP? true : false; $list['url'] = getContentUrl($list['id'], $list['cid'], $list['ename'], $_jumpflag, $list['jumpurl']); if(20) $list['title'] = str2sub($list['title'], 20, 0); if(0) $list['description'] = str2sub($list['description'], 0, 0); ?><li><a href="<?php echo ($list["url"]); ?>"><?php echo ($list["title"]); ?></a></li><?php endforeach;?> 
                    </div>
              	</div> 
                <div class="huoy">
                	<div class="ttl_la">｜快件进口便</div>
                    <div class="text_la" style="padding:5px;"> 
                     <?php  $_typeid = 14; $_keyword = ""; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); if ($_typeid>0 || substr($_typeid,0,1) == '$') { import('Class.Category', APP_PATH); $_selfcate = Category::getSelf(getCategory(), $_typeid); $_tablename = strtolower($_selfcate['tablename']); $ids = Category::getChildsId(getCategory(), $_typeid, true); $where = array($_tablename.'.status' => 0, $_tablename .'.cid'=> array('IN',$ids)); }else { $_tablename = 'article'; $where = array($_tablename.'.status' => 0); } if ($_keyword != '') { $where[$_tablename.'.title'] = array('like','%'.$_keyword.'%'); } if (0 > 0) { $where['_string'] = $_tablename.'.flag & 0 = 0 '; } if (!empty($_tablename) && $_tablename != 'page') { if (0 > 0) { import('ORG.Util.Page'); $count = D(ucfirst($_tablename ).'View')->where($where)->count(); $thisPage = new Page($count, 0); $ename = I('e', '', 'htmlspecialchars,trim'); if (!empty($ename) && C('URL_ROUTER_ON') == true) { $thisPage->url = ''.$ename. '/p'; } $thisPage->rollPage = 3; $thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%'); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "3"; } $_list = D(ucfirst($_tablename ).'View')->where($where)->order("id")->limit($limit)->select(); if (empty($_list)) { $_list = array(); } }else { $_list = array(); } foreach($_list as $autoindex => $list): $_jumpflag = ($list['flag'] & B_JUMP) == B_JUMP? true : false; $list['url'] = getContentUrl($list['id'], $list['cid'], $list['ename'], $_jumpflag, $list['jumpurl']); if(20) $list['title'] = str2sub($list['title'], 20, 0); if(0) $list['description'] = str2sub($list['description'], 0, 0); ?><li><a href="<?php echo ($list["url"]); ?>"><?php echo ($list["title"]); ?></a></li><?php endforeach;?>
                    </div>
              	</div>


                <div class="huoy" style="background:url(__PUBLIC__/images/con_img.jpg) no-repeat right bottom;">
                	<div class="ttl_la">｜客服中心</div>
                    <div class="text_la" style="padding:4px 0 137px 40px; background:url(__PUBLIC__/images/tel_img.jpg) no-repeat 6px 0px; color:#575757; line-height:28px;">
                    <?php
 $block = getBlock("index_contact"); $block_content = ''; if ($block) { if ($block['blocktype'] == 2) { if (!0) { $block_content = '<img src="'. $block['content'] .'" />'; }else { $block_content = $block['content']; } }else { if(0) { $block_content = str2sub(strip_tags($block['content']), 0, 0); }else { $block_content = $block['content']; } } } echo $block_content; ?>
                    </div>
              	</div> 
            </div>
		</div>
</div></div>

<?php if(1 == '1' ): ?><link href="__ROOT__/Data/static/js_plugins/qqOnline/css/qqOnline.css" rel="stylesheet" type="text/css" />
<div id="qqOnlineView" class="qq_online_view">
	<div class="top_b"></div>
	<div class="body">
	 <dl>
         <dd class="title">QQ在线客服</dd>
         <dd><span class="ico_zx">在线咨询</span></dd>
         <div id="qqshow"></div>
     </dl>
	 <dl>
        <dd class="title bborder">Email服务</dd><dd><a href="mailto:wcd003@hkwcd.com">wcd003@hkwcd.com</a></dd> 
     </dl> 
     <dl>
     <?php if(1 == '1' ): ?><dd class="title bborder">电话咨询</dd><dd><span class="ico_tel">020-36311406</span></dd><?php endif; ?>
     <?php if(1 == '1' ): ?><dd class="msg noborder"><a href="<?php echo U('Guestbook/index');?>">给我们留言</a></dd><?php endif; ?>
     </dl>
	</div>
</div>
<script type="text/javascript">    var str = "252003071|874802107|1178201785"; var strww = "lywsh68:lywsh681|lywsh68"; var lr = -1;</script>
<script src="__ROOT__/Data/static/js_plugins/qqOnline/js/qqOnline.js" type="text/javascript" language="JavaScript"></script><?php endif; ?> 
<link href="__ROOT__/Data/static/js_plugins/wbShare/css/wbShare.css" rel="stylesheet" type="text/css">   
<div class="gotop">
  <ul id="jump">
    <li style="display:none;height:50px;"><a  id="gotop" href="#top"></a></li>
    <li style="height:50px"><a id="sina" rel="nofollow" href="http://weibo.com/u/2609168294" target="_blank"></a></li>
    <li style="height:50px"><a id="ceping" href="http://t.qq.com/HY-WSH" target="_blank"></a></li>
    <li style="height:50px"><a id="weixin" href="javascript:void(0)" onmouseover="showEWM()" onmouseout="hideEWM()">
      <div id="EWM"><img src="__PUBLIC__/images/weixin_code.jpg" alt="香港华度货运有限公司" /></div>
      </a></li> 
    <li style="height:50px"><a id="reply"  href="javascript:void(0)" onmouseover="showWHATAPP()" onmouseout="hideWHATAPP()">
     <div id="WHATAPP"><img src="__PUBLIC__/images/whatapp.jpg" alt="香港华度货运有限公司" /></div></a></li> 
  </ul>
</div>
 <script src="__ROOT__/Data/static/js_plugins/wbShare/js/wbShare.js" type="text/javascript"></script> 
<div class="footer">
   <div class="m_1100">
	<div class="link_b">
     <div class="f_top">
    	<div class="ttab">
            <li>新手指南</li>
            <li>帮助支持</li>
            <li>了解华度</li>
            <li>联系我们</li>
        </div>
        <div class="xx_li">
             <?php
 $_typeid = intval(13); $type = "son"; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); $__catlist = getCategory(1); import('Class.Category', APP_PATH); if (1 == 0) { $__catlist = Category::clearPageAndLink($__catlist); } if($_typeid == 0 || $type == 'top') { $_catlist = Category::unlimitedForLayer($__catlist); }else { if ($type == 'self') { $_typeinfo = Category::getSelf($__catlist, $_typeid ); $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeinfo['pid']); }else { $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeid); } } foreach($_catlist as $autoindex => $catlist): if($autoindex >= 5) break; $catlist['url'] = getUrl($catlist); ?><li><a href="<?php echo ($catlist["url"]); ?>">·<?php echo ($catlist["name"]); ?></a><li><?php endforeach;?>
         </div>
        <div class="xx_li">
            <?php
 $_typeid = intval(5); $type = "son"; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); $__catlist = getCategory(1); import('Class.Category', APP_PATH); if (1 == 0) { $__catlist = Category::clearPageAndLink($__catlist); } if($_typeid == 0 || $type == 'top') { $_catlist = Category::unlimitedForLayer($__catlist); }else { if ($type == 'self') { $_typeinfo = Category::getSelf($__catlist, $_typeid ); $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeinfo['pid']); }else { $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeid); } } foreach($_catlist as $autoindex => $catlist): if($autoindex >= 5) break; $catlist['url'] = getUrl($catlist); ?><li><a href="<?php echo ($catlist["url"]); ?>">·<?php echo ($catlist["name"]); ?></a><li><?php endforeach;?>
        </div>
        <div class="xx_li" style=" padding-left:20px">
            <?php
 $_typeid = intval(1); $type = "son"; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); $__catlist = getCategory(1); import('Class.Category', APP_PATH); if (1 == 0) { $__catlist = Category::clearPageAndLink($__catlist); } if($_typeid == 0 || $type == 'top') { $_catlist = Category::unlimitedForLayer($__catlist); }else { if ($type == 'self') { $_typeinfo = Category::getSelf($__catlist, $_typeid ); $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeinfo['pid']); }else { $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeid); } } foreach($_catlist as $autoindex => $catlist): if($autoindex >= 5) break; $catlist['url'] = getUrl($catlist); ?><li><a href="<?php echo ($catlist["url"]); ?>">·<?php echo ($catlist["name"]); ?></a></li><?php endforeach;?>
        </div>
        <div class="xx_li" style="width:270px;">
           <?php
 $_typeid = intval(10); $type = "son"; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); $__catlist = getCategory(1); import('Class.Category', APP_PATH); if (1 == 0) { $__catlist = Category::clearPageAndLink($__catlist); } if($_typeid == 0 || $type == 'top') { $_catlist = Category::unlimitedForLayer($__catlist); }else { if ($type == 'self') { $_typeinfo = Category::getSelf($__catlist, $_typeid ); $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeinfo['pid']); }else { $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeid); } } foreach($_catlist as $autoindex => $catlist): if($autoindex >= 5) break; $catlist['url'] = getUrl($catlist); ?><li><a href="<?php echo ($catlist["url"]); ?>">·<?php echo ($catlist["name"]); ?></a></li><?php endforeach;?>
        </div> 
      </div> 
      <div class="bei">服务条款　隐私声明　<a href="http://www.hkwcd.com/sitemap.xml" target="_blank" >网站地图</a>
	   <br>友情链接:
	   <?php
 $_typeid = 0; if ($_typeid==0) { $where = array('ischeck'=> $_typeid); }else if ($_typeid==1) { $where = array('ischeck'=> $_typeid); }else{ $where = array('id' => array('gt',0)); } if (0 > 0) { import('ORG.Util.Page'); $count = M('link')->where($where)->count(); $thisPage = new Page($count, 0); $thisPage->rollPage = 3; $thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%'); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "12"; } $_flink = M('link')->where($where)->order("sort ASC")->limit($limit)->select(); if (empty($_flink)) { $_flink = array(); } foreach($_flink as $autoindex => $flink): ?><a href="<?php echo ($flink["url"]); ?>" title="<?php echo ($flink["name"]); ?>" target="_blank"><?php echo ($flink["name"]); ?></a> |<?php endforeach;?> 
	  <br>Copyright @ 2012-2020 hkwcd.com All Rights Reserved.site map香港华度货运有限公司　版权所有<span style="float:right">技术支持：<a href="http://www.hoxinit.com"  target="_blank"  >宏信网络</a></span></div>     
	</div>
     
  </div> 
</div>
<script type="text/javascript">
    function win_login() {
        url = "/User/login";
        tipsWindown("登录", "iframe:" + url, "420", "225", "true", "", "false", "leotheme");
    }
</script>





<script type="text/javascript" src="__PUBLIC__/js/imgSlider.js"></script>
<script type="text/javascript">
    img.init();
    img.play(0);
    //阻止事件冒泡
    function estop(e) {
        var e = arguments.callee.caller.arguments[0] || event;
        if (e && e.stopPropagation) {
            //因此它支持W3C的stopPropagation()方法
            e.stopPropagation();
        } else {
            //否则，我们需要使用IE的方式来取消事件冒泡
            window.event.cancelBubble = true;
            return false;
        }
    }
</script>

</body>
</html>