<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="baidu-site-verification" content="4S9341sIne" />
<title>Hongkong WCD Express Company Limited</title>
<meta name="keywords" content="Hongkong WCD Express Company Limited" />
<meta name="description" content="Hongkong WCD Express Company Limited" />
<link href="__PUBLIC__/css/hd.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/style.css" />
<link href="/Public/config/css/tipswindown.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="__PUBLIC__/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/jquery.lazyload.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/scroll.js"></script>
<script src="/Public/config/js/tipswindown.js" type="text/javascript"></script>
</head>

<body>

  <div class="top">
  <div class="m_1100">
    <div class="conne">  
      <div class="c_l"> <div id="ggao"><b>Latest News：</b><span><marquee  scrollamount="2"><?php
 $where = array('endtime' => array('gt',time())); if (0 > 0) { import('ORG.Util.Page'); $count = M('announce')->where($where)->count(); $thisPage = new Page($count, 0); $thisPage->rollPage = 3; $thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%'); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "1"; } $_announcelist = M('announce')->where($where)->order("starttime DESC")->limit($limit)->select(); if (empty($_announcelist)) { $_announcelist = array(); } foreach($_announcelist as $autoindex => $announcelist): if(0) $announcelist['title'] = str2sub($announcelist['title'], 0, 0); if(100) $announcelist['content'] = str2sub(strip_tags($announcelist['content']), 100, 0); echo ($announcelist["content"]); endforeach;?></marquee></span></div></div> 
      <div class="c_r">
	   <a href="/index.php"  >Chinese</a> | <a href="/en/"  >English</a> | <a href="#" onclick="AddFavorite('http://www.hkwcd.com/en', 'Hongkong WCD Express Company Limited')">AddFavorite</a> | <a href="/en/sitemap.xml" target="_blank" >sitemap</a> | <a href="<?php echo U(GROUP_NAME.'/Guestbook/index');?>">Guestbook</a> | <?php
 import('Class.Category', APP_PATH); $type = Category::getSelf(getCategory(0), 10); $type['url'] = getUrl($type); ?><a href="<?php echo ($type["url"]); ?>">Contact</a>
      </div> 
    </div>
	<div class="min">
    	<div class="logo"><a href="http://www.hkwcd.com/en"><img src="__PUBLIC__/images/t_b.gif"></a></div>
        <div class="navi">
          <div class="serch"><font style="font-family:Georgia; font-size:23px; font-style:italic; color:#005dab;">Tel:+86-020-36311406</font><br>
          <script type="text/javascript" src="__PUBLIC__/js/common.js"></script>  
           <form id="SearchForm" name="SearchForm" method="post"  action="/en/Search/index.html"> 
	       <select name="cid" class="selectcss" style="float: left; height:23px;">
	  	    <?php
 $_typeid = intval(0); $type = "son"; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); $__catlist = getCategory(1); import('Class.Category', APP_PATH); if (0 == 0) { $__catlist = Category::clearPageAndLink($__catlist); } if($_typeid == 0 || $type == 'top') { $_catlist = Category::unlimitedForLayer($__catlist); }else { if ($type == 'self') { $_typeinfo = Category::getSelf($__catlist, $_typeid ); $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeinfo['pid']); }else { $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeid); } } foreach($_catlist as $autoindex => $catlist): if($autoindex >= 30) break; $catlist['url'] = getUrl($catlist); ?><option value="<?php echo ($catlist["id"]); ?>" <?php if($catlist["id"] == $cid): ?>selected="selected"<?php endif; ?>><?php echo ($catlist["name"]); ?></option><?php endforeach;?>
	      </select> 
          <input name="keyword" type="text" class="sinput" id="keyword" size="15" height="21"   value="<?php if(empty($keyword)): ?>Enter a keyword<?php else: echo ($keyword); endif; ?>" onfocus="if(this.value=='Enter a keyword'){this.value='';}" onblur="if(this.value==''){this.value='Enter a keyword';}" />
	      <input type="submit" value="Query"  style="padding-left: 1px; width:40px" class="sbtn"/> 
        </form>

        </div>

         
          <div class="nav">
            <ul>
            <li><a href="http://www.hkwcd.com/en">Home</a></li>
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

    
 <div id="imageShow">
  <div id="imgshow_mask"></div>
  <ul class="imagebg" id="imagebg">
     <?php
 $_typeid = 0; if ($_typeid==0) { $where = array('ischeck'=> $_typeid); }else if ($_typeid==1) { $where = array('ischeck'=> $_typeid); }else{ $where = array('id' => array('gt',0)); } if (0 > 0) { import('ORG.Util.Page'); $count = M('adbanner')->where($where)->count(); $thisPage = new Page($count, 0); $thisPage->rollPage = 3; $thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%'); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "4"; } $_adlink = M('adbanner')->where($where)->order("sort ASC")->limit($limit)->select(); if (empty($_adlink)) { $_adlink = array(); } foreach($_adlink as $autoindex => $adlink): ?><li data-sPic="<?php echo ($adlink["description"]); ?>"  class="bannerbg"  > 
             <div class="bannerbg_main" style="background:url(<?php echo ($adlink["logo"]); ?>) center no-repeat; cursor:pointer;" onclick="javascript:location.href='<?php echo ($adlink["url"]); ?>'"></div> 
		</li><?php endforeach;?>  
  </ul>
  <div class="scrollbg">
    <div class="scroll">  
      <div class="current" id="current"></div>
      <div class="outScroll_pic">
        <ul class="scroll_pic cls" id="small_pic">
        </ul>
      </div>
     </div>
  </div>
</div>


    <div class="main_a">
    <div class="m_1100">
	  <div class="ser_kk"><li style="margin-top:20px; margin-left:20px;"><font style="font-size:30px;">Track</font></li>
        <li><input type="text" id="dda" placeholder="Enter the waybill number" style="border:1px solid #ccc; border-radius:3px; width:530px; height:70px; font-size:30px; color:#666; font-family:Arial, Helvetica, sans-serif; padding-left:20px;"></li>
        <li style="width:150px; height:70px; border:1px solid #ccc; border-radius:3px; background:url(__PUBLIC__/images/butn_re.jpg); font-size:30px; font-family:'微软雅黑'; line-height:70px; text-align:center;text-shadow: 1px 1px 1px #fff;">
        <a href="javascript:void(0)" onclick="tipsWinComm()">Query</a></li>
        <li style="width:150px; height:70px; border:1px solid #ccc; border-radius:3px; background:url(__PUBLIC__/images/butn_re.jpg); font-size:30px; font-family:'微软雅黑'; line-height:70px; text-align:center;text-shadow: 1px 1px 1px #fff;">
        <a href="/en/VolumeCalculation">Volume Calc</a></li>
	</div>
      <div class="b_block">
   			<div class="lef">
            	<!--燃油附加费-->
            	<div class="race">
                    <?php
 $block = getBlock("index_surcharge"); $block_content = ''; if ($block) { if ($block['blocktype'] == 2) { if (!0) { $block_content = '<img src="'. $block['content'] .'" />'; }else { $block_content = $block['content']; } }else { if(0) { $block_content = str2sub(strip_tags($block['content']), 0, 0); }else { $block_content = $block['content']; } } } echo $block_content; ?>
              	</div>
                <!--自助服务-->
           	 	<div class="race" style="background:url(__PUBLIC__/images/ser_pp.jpg) no-repeat right bottom;">
                	<div class="ttl">｜Self service</div>
                    <div class="text" style="padding-bottom:10px">
                  <table width="264" border="0" cellspacing="0" cellpadding="0" style="background:url(__PUBLIC__/images/serve.png) no-repeat; text-align:center">
                    <tbody>
                        <tr class="firstRow">
                            <td width="17" height="54">
                                &nbsp;
                            </td>
                            <td width="65" rowspan="2" valign="bottom">
                               <a href="/en//ExtraFee">Extra Fee</a>
                            </td>
                            <td width="10">
                                &nbsp;
                            </td>
                            <td width="80" rowspan="2" valign="bottom">
                                <a href="http://time.123cha.com/" target="_blank">WorldTime</a>
                            </td>
                            <td width="5">
                                &nbsp;
                            </td>
                            <td width="120" rowspan="2" valign="bottom">
                             <a href="http://ipseeker.cn/wcode.php" style="margin-left: -40px;" target="_blank">Country</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                        <tr class="towRow">
                            <td width="17" height="60">
                                &nbsp;
                            </td>
                            <td width="50" rowspan="2" valign="bottom">
                                     <a href="http://airportcode.911cha.com/" target="_blank">Fly Code</a>
                            </td>
                            <td width="1">
                                &nbsp;
                            </td>
                            <td width="48" rowspan="2" valign="bottom">
                                   <a href="/en/ForbiddenProducts" >Forbidden</a>
                            </td>
                            <td width="3">
                                &nbsp;
                            </td>
                            <td width="52" rowspan="2" valign="bottom" style="text-align:left;">
                                 <a href="http://raslist.dhl.com/jsp/first.jsp" target="_blank" >Remote Query</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                    </tbody>
                </table>
                     </div>
              	</div>
                <!--下载中心-->
            	<div class="race_la">
                	<div class="ttl_la">｜Download Center</div>
                    <div class="text_la" style="padding-bottom:12px;">
                   <?php
 $_typeid = 5; $_keyword = ""; $_arcid = ""; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); if ($_typeid>0 || substr($_typeid,0,1) == '$') { import('Class.Category', APP_PATH); $ids = Category::getChildsId(getCategory(), $_typeid, true); $where = array('soft.status' => 0, 'soft.cid'=> array('IN',$ids)); }else { $where = array('soft.status' => 0); } if ($_keyword != '') { $where['soft.title'] = array('like','%'.$_keyword.'%'); } if (!empty($_arcid)) { $where['soft.id'] = array('IN', $_arcid); } if (0 > 0) { $where['_string'] = 'soft.flag & 0 = 0 '; } if (0 > 0) { import('ORG.Util.Page'); $count = D('SoftView')->where($where)->count(); $thisPage = new Page($count, 0); $ename = I('e', '', 'htmlspecialchars,trim'); if (!empty($ename) && C('URL_ROUTER_ON') == true) { $thisPage->url = ''.$ename. '/p'; } $thisPage->rollPage = 3; $thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%'); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "5"; } $_soflist = D('SoftView')->where($where)->order("id DESC")->limit($limit)->select(); if (empty($_soflist)) { $_soflist = array(); } foreach($_soflist as $autoindex => $soflist): $_jumpflag = ($soflist['flag'] & B_JUMP) == B_JUMP? true : false; $soflist['url'] = getContentUrl($soflist['id'], $soflist['cid'], $soflist['ename'], $_jumpflag, $soflist['jumpurl']); if(0) $soflist['title'] = str2sub($soflist['title'], 0, 0); if(0) $soflist['description'] = str2sub($soflist['description'], 0, 0); ?><li><a href="<?php echo ($soflist["url"]); ?>"><?php echo ($soflist["title"]); ?></a></li><?php endforeach;?>
                    </div>
              	</div>
            </div>
            <div class="midd">
            	<!--华度服务-->
            	<div class="svs" style="background:url(__PUBLIC__/images/kf.jpg) no-repeat right bottom; padding:5px;">
                	<div class="ttl">｜WCD Overview</div>
                    <div class="text" style="padding:3px 100px 5px 5px; color:#686868; line-height:21px; word-break:break-all;">
                        <?php
 import('Class.Category', APP_PATH); $type = Category::getSelf(getCategory(0), 1); $type['url'] = getUrl($type); echo (str2sub($type["description"],475, true)); ?>
                    　     <a href="<?php echo ($type["url"]); ?>" style="color:#33C; text-decoration:underline"><font style="float:right;margin-top: 4px;">Learn more +</font></a>
                    </div>
              	</div>
                <!--公司动态-->
            	<div class="svs" style="width:460px;">
                	<div class="ttl_la">｜Company News</div>
                    <div class="text_la" style="padding:4px 0px;">
                     <ul class="artlist1" style=" height:143px">
                    <?php
 $_typeid = 32; $_keyword = ""; $_arcid = ""; if($_typeid == -1) $_typeid = I('get.cid', 0, 'intval'); if ($_typeid>0 || substr($_typeid,0,1) == '$') { import('Class.Category', APP_PATH); $ids = Category::getChildsId(getCategory(), $_typeid, true); $where = array('article.status' => 0, 'article.cid'=> array('IN',$ids)); }else { $where = array('article.status' => 0); } if ($_keyword != '') { $where['article.title'] = array('like','%'.$_keyword.'%'); } if (!empty($_arcid)) { $where['article.id'] = array('IN', $_arcid); } if (0 > 0) { $where['_string'] = 'article.flag & 0 = 0 '; } if (0 > 0) { import('ORG.Util.Page'); $count = D('ArticleView')->where($where)->count(); $thisPage = new Page($count, 0); $ename = I('e', '', 'htmlspecialchars,trim'); if (!empty($ename) && C('URL_ROUTER_ON') == true) { $thisPage->url = ''.$ename. '/p'; } $thisPage->rollPage = 3; $thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%'); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "5"; } $_artlist = D('ArticleView')->where($where)->order("id DESC")->limit($limit)->select(); if (empty($_artlist)) { $_artlist = array(); } foreach($_artlist as $autoindex => $artlist): $_jumpflag = ($artlist['flag'] & B_JUMP) == B_JUMP? true : false; $artlist['url'] = getContentUrl($artlist['id'], $artlist['cid'], $artlist['ename'], $_jumpflag, $artlist['jumpurl']); if(45) $artlist['title'] = str2sub($artlist['title'], 45, 0); if(0) $artlist['description'] = str2sub($artlist['description'], 0, 0); ?><li><a href="<?php echo ($artlist["url"]); ?>"><span>[<?php echo (date('Y-m-d',$artlist["publishtime"])); ?>]</span><?php echo ($artlist["title"]); ?></a></li><?php endforeach;?>
                       </ul>
                    </div>
              	</div>
                <!--物流知识-->
            	<div class="svs" style="width:460px;">
                	<div class="ttl_la">｜Logistics Knowledge</div>
                    <div class="text_la" style="padding: 0px 5px 0px;">
                      <ul class="artlist2" style=" height:143px">
                    <?php
 $_typeid = 31; $_keyword = ""; $_arcid = ""; if($_typeid == -1) $_typeid = I('get.cid', 0, 'intval'); if ($_typeid>0 || substr($_typeid,0,1) == '$') { import('Class.Category', APP_PATH); $ids = Category::getChildsId(getCategory(), $_typeid, true); $where = array('article.status' => 0, 'article.cid'=> array('IN',$ids)); }else { $where = array('article.status' => 0); } if ($_keyword != '') { $where['article.title'] = array('like','%'.$_keyword.'%'); } if (!empty($_arcid)) { $where['article.id'] = array('IN', $_arcid); } if (0 > 0) { $where['_string'] = 'article.flag & 0 = 0 '; } if (0 > 0) { import('ORG.Util.Page'); $count = D('ArticleView')->where($where)->count(); $thisPage = new Page($count, 0); $ename = I('e', '', 'htmlspecialchars,trim'); if (!empty($ename) && C('URL_ROUTER_ON') == true) { $thisPage->url = ''.$ename. '/p'; } $thisPage->rollPage = 3; $thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%'); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "5"; } $_artlist = D('ArticleView')->where($where)->order("id DESC")->limit($limit)->select(); if (empty($_artlist)) { $_artlist = array(); } foreach($_artlist as $autoindex => $artlist): $_jumpflag = ($artlist['flag'] & B_JUMP) == B_JUMP? true : false; $artlist['url'] = getContentUrl($artlist['id'], $artlist['cid'], $artlist['ename'], $_jumpflag, $artlist['jumpurl']); if(45) $artlist['title'] = str2sub($artlist['title'], 45, 0); if(0) $artlist['description'] = str2sub($artlist['description'], 0, 0); ?><li><a href="<?php echo ($artlist["url"]); ?>"><span>[<?php echo (date('Y-m-d',$artlist["publishtime"])); ?>]</span><?php echo ($artlist["title"]); ?></a></li><?php endforeach;?>
                       </ul>
                    </div>
              	</div>
            </div>

            <div class="rig">
               <div class="huoy">
                	<div class="ttl_la">｜Import Express Services</div>
                    <div class="text_la" style="padding:5px;">
                     <?php  $_typeid = 14; $_keyword = ""; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); if ($_typeid>0 || substr($_typeid,0,1) == '$') { import('Class.Category', APP_PATH); $_selfcate = Category::getSelf(getCategory(), $_typeid); $_tablename = strtolower($_selfcate['tablename']); $ids = Category::getChildsId(getCategory(), $_typeid, true); $where = array($_tablename.'.status' => 0, $_tablename .'.cid'=> array('IN',$ids)); }else { $_tablename = 'article'; $where = array($_tablename.'.status' => 0); } if ($_keyword != '') { $where[$_tablename.'.title'] = array('like','%'.$_keyword.'%'); } if (0 > 0) { $where['_string'] = $_tablename.'.flag & 0 = 0 '; } if (!empty($_tablename) && $_tablename != 'page') { if (0 > 0) { import('ORG.Util.Page'); $count = D(ucfirst($_tablename ).'View')->where($where)->count(); $thisPage = new Page($count, 0); $ename = I('e', '', 'htmlspecialchars,trim'); if (!empty($ename) && C('URL_ROUTER_ON') == true) { $thisPage->url = ''.$ename. '/p'; } $thisPage->rollPage = 3; $thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%'); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "3"; } $_list = D(ucfirst($_tablename ).'View')->where($where)->order("id")->limit($limit)->select(); if (empty($_list)) { $_list = array(); } }else { $_list = array(); } foreach($_list as $autoindex => $list): $_jumpflag = ($list['flag'] & B_JUMP) == B_JUMP? true : false; $list['url'] = getContentUrl($list['id'], $list['cid'], $list['ename'], $_jumpflag, $list['jumpurl']); if(20) $list['title'] = str2sub($list['title'], 20, 0); if(0) $list['description'] = str2sub($list['description'], 0, 0); ?><li><a href="<?php echo ($list["url"]); ?>"><?php echo ($list["title"]); ?></a></li><?php endforeach;?>
                    </div>
              	</div>
                <div class="huoy">
                	<div class="ttl_la">｜Export Express Services</div>
                    <div class="text_la" style="padding:5px;">
                     <?php  $_typeid = 6; $_keyword = ""; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); if ($_typeid>0 || substr($_typeid,0,1) == '$') { import('Class.Category', APP_PATH); $_selfcate = Category::getSelf(getCategory(), $_typeid); $_tablename = strtolower($_selfcate['tablename']); $ids = Category::getChildsId(getCategory(), $_typeid, true); $where = array($_tablename.'.status' => 0, $_tablename .'.cid'=> array('IN',$ids)); }else { $_tablename = 'article'; $where = array($_tablename.'.status' => 0); } if ($_keyword != '') { $where[$_tablename.'.title'] = array('like','%'.$_keyword.'%'); } if (0 > 0) { $where['_string'] = $_tablename.'.flag & 0 = 0 '; } if (!empty($_tablename) && $_tablename != 'page') { if (0 > 0) { import('ORG.Util.Page'); $count = D(ucfirst($_tablename ).'View')->where($where)->count(); $thisPage = new Page($count, 0); $ename = I('e', '', 'htmlspecialchars,trim'); if (!empty($ename) && C('URL_ROUTER_ON') == true) { $thisPage->url = ''.$ename. '/p'; } $thisPage->rollPage = 3; $thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%'); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "3"; } $_list = D(ucfirst($_tablename ).'View')->where($where)->order("id")->limit($limit)->select(); if (empty($_list)) { $_list = array(); } }else { $_list = array(); } foreach($_list as $autoindex => $list): $_jumpflag = ($list['flag'] & B_JUMP) == B_JUMP? true : false; $list['url'] = getContentUrl($list['id'], $list['cid'], $list['ename'], $_jumpflag, $list['jumpurl']); if(20) $list['title'] = str2sub($list['title'], 20, 0); if(0) $list['description'] = str2sub($list['description'], 0, 0); ?><li><a href="<?php echo ($list["url"]); ?>"><?php echo ($list["title"]); ?></a></li><?php endforeach;?>
                    </div>
              	</div>
                <!--｜客服中心-->
                <div class="huoy" style="background:url(__PUBLIC__/images/con_img.jpg) no-repeat right bottom;">
                	<div class="ttl_la">｜Customer Service Center</div>
                    <div class="text_la" style="padding:4px 0 126px 40px; background:url(__PUBLIC__/images/tel_img.jpg) no-repeat 6px 0px; color:#575757; line-height:28px;">
                    <?php
 $block = getBlock("index_contact"); $block_content = ''; if ($block) { if ($block['blocktype'] == 2) { if (!0) { $block_content = '<img src="'. $block['content'] .'" />'; }else { $block_content = $block['content']; } }else { if(0) { $block_content = str2sub(strip_tags($block['content']), 0, 0); }else { $block_content = $block['content']; } } } echo $block_content; ?>
                    </div>
              	</div>
            </div>
		</div>
      <div class="gs_imgshow">
      		<div class="ttl_la">｜Daily Delivey</div>
            <div class="text_img" style=" padding:10px 20px;">
              <ul class="scrollleft">
              <?php  $_typeid = 40; $_keyword = ""; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); if ($_typeid>0 || substr($_typeid,0,1) == '$') { import('Class.Category', APP_PATH); $_selfcate = Category::getSelf(getCategory(), $_typeid); $_tablename = strtolower($_selfcate['tablename']); $ids = Category::getChildsId(getCategory(), $_typeid, true); $where = array($_tablename.'.status' => 0, $_tablename .'.cid'=> array('IN',$ids)); }else { $_tablename = 'article'; $where = array($_tablename.'.status' => 0); } if ($_keyword != '') { $where[$_tablename.'.title'] = array('like','%'.$_keyword.'%'); } if (0 > 0) { $where['_string'] = $_tablename.'.flag & 0 = 0 '; } if (!empty($_tablename) && $_tablename != 'page') { if (0 > 0) { import('ORG.Util.Page'); $count = D(ucfirst($_tablename ).'View')->where($where)->count(); $thisPage = new Page($count, 0); $ename = I('e', '', 'htmlspecialchars,trim'); if (!empty($ename) && C('URL_ROUTER_ON') == true) { $thisPage->url = ''.$ename. '/p'; } $thisPage->rollPage = 3; $thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%'); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "12"; } $_list = D(ucfirst($_tablename ).'View')->where($where)->order("id DESC")->limit($limit)->select(); if (empty($_list)) { $_list = array(); } }else { $_list = array(); } foreach($_list as $autoindex => $list): $_jumpflag = ($list['flag'] & B_JUMP) == B_JUMP? true : false; $list['url'] = getContentUrl($list['id'], $list['cid'], $list['ename'], $_jumpflag, $list['jumpurl']); if(8) $list['title'] = str2sub($list['title'], 8, 0); if(0) $list['description'] = str2sub($list['description'], 0, 0); ?><li><a href="<?php echo ($list["url"]); ?>"><img class="lazy" src="<?php echo ($list["litpic"]); ?>" data-original="<?php echo ($list["litpic"]); ?>" width="220" height="100" alt="<?php echo ($list["title"]); ?>"/></a></li><?php endforeach;?>
            </ul>
          </div>
      </div>
    </div>
  </div>

 <?php if(1 == '1' ): ?><link href="__ROOT__/Data/static/js_plugins/qqOnline/css/qqOnline.css" rel="stylesheet" type="text/css" />
<div id="qqOnlineView" class="qq_online_view">
	<div class="top_b"></div>
	<div class="body">
	 <dl><dd class="title">Online Service</dd><dd><span class="ico_zx">Online</span></dd><div id="qqshow"></div></dl>
      <dl>
        <dd class="title bborder">Email Service</dd><dd><a href="mailto:wcd003@hkwcd.com">wcd003@hkwcd.com</a></dd> 
     </dl> 
	 <dl>
     <?php if(1 == '1' ): ?><dd class="title bborder">Tel Service</dd><dd><span class="ico_tel">86-020-36311406</span></dd><?php endif; ?>
     <?php if(1 == '1' ): ?><dd class="msg noborder"><a href="<?php echo U('Guestbook/index');?>">Feedback</a></dd><?php endif; ?>
     </dl>
	</div>
</div>
<script type="text/javascript">    var str = "252003071|874802107"; var strww = "huadu56"; var lr = -1;</script>
<script src="__ROOT__/Data/static/js_plugins/qqOnline/js/qqOnline.js" type="text/javascript" language="JavaScript"></script><?php endif; ?> 
<link href="__ROOT__/Data/static/js_plugins/wbShare/css/wbShare.css" rel="stylesheet" type="text/css">   
<div class="gotop">
  <ul id="jump">
    <li style="display:none;height:50px;"><a  id="gotop" href="#top"></a></li>
    <li style="height:50px"><a id="sina" rel="nofollow" href="http://weibo.com/u/2609168294" target="_blank"></a></li>
    <li style="height:50px"><a id="ceping" href="http://t.qq.com/HY-WSH" target="_blank"></a></li>
    <li style="height:50px"><a id="weixin" href="javascript:void(0)" onmouseover="showEWM()" onmouseout="hideEWM()">
      <div id="EWM"><img src="__PUBLIC__/images/weixin_code.jpg" alt="Hongkong WCD Express Company Limited" /></div>
      </a></li> 
    <li style="height:50px"><a id="reply"  href="javascript:void(0)" onmouseover="showWHATAPP()" onmouseout="hideWHATAPP()">
     <div id="WHATAPP"><img src="__PUBLIC__/images/whatapp.jpg" alt="Hongkong WCD Express Company Limited" /></div></a></li> 
  </ul>
</div>
 <script src="__ROOT__/Data/static/js_plugins/wbShare/js/wbShare.js" type="text/javascript"></script> 
<div class="footer">
   <div class="m_1100">
	<div class="link_b">
    <div class="f_top">
    	<div class="ttab">
            <li><?php
 import('Class.Category', APP_PATH); $type = Category::getSelf(getCategory(0), 29); $type['url'] = getUrl($type); echo ($type["name"]); ?></li>
            <li><?php
 import('Class.Category', APP_PATH); $type = Category::getSelf(getCategory(0), 30); $type['url'] = getUrl($type); echo ($type["name"]); ?></li>
            <li><?php
 import('Class.Category', APP_PATH); $type = Category::getSelf(getCategory(0), 1); $type['url'] = getUrl($type); echo ($type["name"]); ?></li>
            <li><?php
 import('Class.Category', APP_PATH); $type = Category::getSelf(getCategory(0), 10); $type['url'] = getUrl($type); echo ($type["name"]); ?></li>
        </div>
        <div class="xx_li">
             <?php
 $_typeid = 29; $_keyword = ""; $_arcid = ""; if($_typeid == -1) $_typeid = I('get.cid', 0, 'intval'); if ($_typeid>0 || substr($_typeid,0,1) == '$') { import('Class.Category', APP_PATH); $ids = Category::getChildsId(getCategory(), $_typeid, true); $where = array('article.status' => 0, 'article.cid'=> array('IN',$ids)); }else { $where = array('article.status' => 0); } if ($_keyword != '') { $where['article.title'] = array('like','%'.$_keyword.'%'); } if (!empty($_arcid)) { $where['article.id'] = array('IN', $_arcid); } if (8 > 0) { $where['_string'] = 'article.flag & 8 = 8 '; } if (0 > 0) { import('ORG.Util.Page'); $count = D('ArticleView')->where($where)->count(); $thisPage = new Page($count, 0); $ename = I('e', '', 'htmlspecialchars,trim'); if (!empty($ename) && C('URL_ROUTER_ON') == true) { $thisPage->url = ''.$ename. '/p'; } $thisPage->rollPage = 3; $thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%'); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "5"; } $_artlist = D('ArticleView')->where($where)->order("id DESC")->limit($limit)->select(); if (empty($_artlist)) { $_artlist = array(); } foreach($_artlist as $autoindex => $artlist): $_jumpflag = ($artlist['flag'] & B_JUMP) == B_JUMP? true : false; $artlist['url'] = getContentUrl($artlist['id'], $artlist['cid'], $artlist['ename'], $_jumpflag, $artlist['jumpurl']); if(0) $artlist['title'] = str2sub($artlist['title'], 0, 0); if(0) $artlist['description'] = str2sub($artlist['description'], 0, 0); ?><li><a href="<?php echo ($artlist["url"]); ?>">·<?php echo ($artlist["title"]); ?></a><li><?php endforeach;?>
         </div>
        <div class="xx_li">
            <?php
 $_typeid = 30; $_keyword = ""; $_arcid = ""; if($_typeid == -1) $_typeid = I('get.cid', 0, 'intval'); if ($_typeid>0 || substr($_typeid,0,1) == '$') { import('Class.Category', APP_PATH); $ids = Category::getChildsId(getCategory(), $_typeid, true); $where = array('article.status' => 0, 'article.cid'=> array('IN',$ids)); }else { $where = array('article.status' => 0); } if ($_keyword != '') { $where['article.title'] = array('like','%'.$_keyword.'%'); } if (!empty($_arcid)) { $where['article.id'] = array('IN', $_arcid); } if (8 > 0) { $where['_string'] = 'article.flag & 8 = 8 '; } if (0 > 0) { import('ORG.Util.Page'); $count = D('ArticleView')->where($where)->count(); $thisPage = new Page($count, 0); $ename = I('e', '', 'htmlspecialchars,trim'); if (!empty($ename) && C('URL_ROUTER_ON') == true) { $thisPage->url = ''.$ename. '/p'; } $thisPage->rollPage = 3; $thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%'); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "5"; } $_artlist = D('ArticleView')->where($where)->order("id DESC")->limit($limit)->select(); if (empty($_artlist)) { $_artlist = array(); } foreach($_artlist as $autoindex => $artlist): $_jumpflag = ($artlist['flag'] & B_JUMP) == B_JUMP? true : false; $artlist['url'] = getContentUrl($artlist['id'], $artlist['cid'], $artlist['ename'], $_jumpflag, $artlist['jumpurl']); if(0) $artlist['title'] = str2sub($artlist['title'], 0, 0); if(0) $artlist['description'] = str2sub($artlist['description'], 0, 0); ?><li><a href="<?php echo ($artlist["url"]); ?>">·<?php echo ($artlist["title"]); ?></a><li><?php endforeach;?>
        </div>
        <div class="xx_li" style=" padding-left:20px">
            <?php
 $_typeid = intval(1); $type = "son"; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); $__catlist = getCategory(1); import('Class.Category', APP_PATH); if (1 == 0) { $__catlist = Category::clearPageAndLink($__catlist); } if($_typeid == 0 || $type == 'top') { $_catlist = Category::unlimitedForLayer($__catlist); }else { if ($type == 'self') { $_typeinfo = Category::getSelf($__catlist, $_typeid ); $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeinfo['pid']); }else { $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeid); } } foreach($_catlist as $autoindex => $catlist): if($autoindex >= 30) break; $catlist['url'] = getUrl($catlist); ?><li><a href="<?php echo ($catlist["url"]); ?>">·<?php echo ($catlist["name"]); ?></a></li><?php endforeach;?>
        </div>
        <div class="xx_li" style="width:270px;">
            <?php
 $_typeid = intval(10); $type = "son"; if($_typeid == -1) $_typeid = I('cid', 0, 'intval'); $__catlist = getCategory(1); import('Class.Category', APP_PATH); if (1 == 0) { $__catlist = Category::clearPageAndLink($__catlist); } if($_typeid == 0 || $type == 'top') { $_catlist = Category::unlimitedForLayer($__catlist); }else { if ($type == 'self') { $_typeinfo = Category::getSelf($__catlist, $_typeid ); $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeinfo['pid']); }else { $_catlist = Category::unlimitedForLayer($__catlist, 'child', $_typeid); } } foreach($_catlist as $autoindex => $catlist): if($autoindex >= 5) break; $catlist['url'] = getUrl($catlist); ?><li><a href="<?php echo ($catlist["url"]); ?>">·<?php echo ($catlist["name"]); ?></a></li><?php endforeach;?>
        </div>
    </div>
    <div class="bei">Terms of Service.　Privacy Statement.　<a href="http://www.hkwcd.com/en/sitemap.xml" target="_blank" >Site Map.</a>
	<br>Links:
	   <?php
 $_typeid = 0; if ($_typeid==0) { $where = array('ischeck'=> $_typeid); }else if ($_typeid==1) { $where = array('ischeck'=> $_typeid); }else{ $where = array('id' => array('gt',0)); } if (0 > 0) { import('ORG.Util.Page'); $count = M('link')->where($where)->count(); $thisPage = new Page($count, 0); $thisPage->rollPage = 3; $thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%'); $limit = $thisPage->firstRow. ',' .$thisPage->listRows; $page = $thisPage->show(); }else { $limit = "12"; } $_flink = M('link')->where($where)->order("sort ASC")->limit($limit)->select(); if (empty($_flink)) { $_flink = array(); } foreach($_flink as $autoindex => $flink): ?><a href="<?php echo ($flink["url"]); ?>" title="<?php echo ($flink["name"]); ?>" target="_blank"><?php echo ($flink["name"]); ?></a> |<?php endforeach;?> 
	<br>CopyRight @ 2012-2020 hkwcd.com All Rights Reserved.hongkong wcd express company limited<span style="float:right">Technical Support：<a href="http://www.hoxinit.com" target="_blank" >Hoxinit tech</a></span></div>
    </div>
   </div>
     
</div>





<script type="text/javascript" src="__PUBLIC__/js/jquery.imgSlider.js"></script>
<script type="text/javascript">
    jQuery(function ($) {
        $(".scrollleft").imgscroll({
            speed: 40,    //图片滚动速度
            amount: 0,    //图片滚动过渡时间
            width: 1,     //图片滚动步数
            dir: "left"   // "left" 或 "up" 向左或向上滚动
        });
        $(".artlist1").imgscroll({
            speed: 40,    //图片滚动速度
            amount: 0,    //图片滚动过渡时间
            width: 1,     //图片滚动步数
            dir: "up"   // "left" 或 "up" 向左或向上滚动
        });
        $(".artlist2").imgscroll({
            speed: 40,    //图片滚动速度
            amount: 0,    //图片滚动过渡时间
            width: 1,     //图片滚动步数
            dir: "up"   // "left" 或 "up" 向左或向上滚动
        });
        $("img.lazy").lazyload();
    });
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
    function tipsWinComm() {
        var dda = $("#dda").val();
        if (dda != "") {
            url ="/Express/en/dda/" + dda;
            tipsWindown("Waybill tracking progress list", "iframe:" + url, "840", "350", "true", "", "false", "leotheme");
        } else {
            alert("Please enter a query waybill number！");
        }
    }
    $(document).keypress(function (e) {
        // 回车键事件
        if (e.which == 13) {
            tipsWinComm();
        }
    });

</script>

</body>
</html>