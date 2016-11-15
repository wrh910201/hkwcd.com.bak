<?php

//自定义标签库
class TagLibYang extends TagLib {
	

	protected $tags = array(
		//自定义标签
		//文章列表
		'artlist'	=> array(
			'attr'	=> 'flag,typeid,arcid,titlelen,infolen,orderby,keyword,limit,pagesize',//attr 属性列表,arcid[new|20140413] 指定文档ID
			'close'	=> 1,// close 是否闭合（0 或者1 默认为1，表示闭合）
		),
		//产品列表分页
		'prolist'	=> array(
			'attr'	=> 'flag,typeid,arcid,titlelen,infolen,orderby,keyword,limit,pagesize',
			'close'	=> 1,
		),
		//图片列表分页
		'piclist'	=> array(
			'attr'	=> 'flag,typeid,arcid,titlelen,infolen,orderby,keyword,limit,pagesize',
			'close'	=> 1,
		),
		//软件列表分页
		'soflist'	=> array(
			'attr'	=> 'flag,typeid,arcid,titlelen,infolen,orderby,keyword,limit,pagesize',
			'close'	=> 1,
		),

		//通用列表
		'list'	=> array(
			'attr'	=> 'flag,typeid,titlelen,infolen,orderby,keyword,limit,pagesize',
			'close'	=> 1,
		),

		//专题列表分页
		'spelist'	=> array(
			'attr'	=> 'flag,typeid,titlelen,infolen,orderby,keyword,limit,pagesize',
			'close'	=> 1,
		),

		//栏目
		'catlist'	=> array(
			'attr'	=> 'typeid,type,orderby,limit,flag',//flag为是否全部显示
			'close'	=> 1,
		),
	
	   //子栏目
		'catlistsub'	=> array(
			'attr'	=> 'typeid,type,orderby,limit,flag',//flag为是否全部显示
			'close'	=> 1,
		),
	
	

		//导航
		'navlist'	=> array(
			'attr'	=> 'typeid,limit',
			'close'	=> 1,
		),

		//类名和链接
		'type'	=> array(
			'attr'	=> 'typeid',
			'close'	=> 1,
		),
		//user list
			'userlist'	=> array(
			'attr'	=> 'typeid,titlelen,infolen,orderby,limit,pagesize',//attr 属性列表
			'close'	=> 1,
		),
		//announce list
		'announcelist'	=> array(
			'attr'	=> 'titlelen,infolen,orderby,limit,pagesize',//attr 属性列表
			'close'	=> 1,
		),


		//friendLink list
		'flink'	=> array(
			'attr'	=> 'typeid,titlelen,infolen,orderby,limit,pagesize',//attr 属性列表
			'close'	=> 1,
		),
	
	    //2014年5月18日22:05:22
		//adlink list
		'adlink'	=> array(
			'attr'	=> 'typeid,titlelen,infolen,orderby,limit,pagesize',//attr 属性列表
			'close'	=> 1,
			),
		
		//2014年6月29日11:40:33
		//adlist list
		'adlist'	=> array(
			'attr'	=> 'typeid,id,titlelen,infolen,orderby,limit,pagesize',//attr 属性列表
			'close'	=> 1,
			),

		//2014年6月29日11:40:33
		//adlist list
		'adimg'	=> array(
			'attr'	=> 'typeid,id',//attr 属性列表
			'close'	=> 1,
			),

		'iteminfo'	=> array(
			'attr'	=> 'name,titlelen',
			'close'	=> 1,
		),


		'block'	=> array(
			'attr'	=> 'name,infolen,textflag',
			'close'	=> 0,
		),


		'field'	=> array(
			'attr'	=> 'typeid,artid,name,infolen,imgindex,imgwidth,imgheight',//imgindex,imgwidth,imgheight针对图片
			'close'	=> 0,
		),

		'position'	=> array(
			'attr'	=> 'typeid,ismobile,sname,surl,delimiter',
			'close'	=> 0,
		),


		'sitekeywords'	=> array('close' => 0),
		'sitedescription'	=> array('close' => 0),
		'sitename'	=> array('close' => 0),
		'sitetitle'	=> array('close' => 0),
		'siteurl'	=> array('close' => 0),
		'address'	=> array('close' => 0),
		'phone'	=> array('close' => 0),
		'qq'	=> array('close' => 0),
		'email'	=> array('close' => 0),
		//2014年5月18日12:05:22
		'qqauto'	=> array('close' => 0), 
		'qqmore'	=> array('close' => 0),
		'wwmore'	=> array('close' => 0), 
		'qqphone'	=> array('close' => 0), 
		'qqguestbook'	=> array('close' => 0),  
		'qqlr'	=> array('close' => 0), 
		'adauto'	=> array('close' => 0),
		'adnum'	=> array('close' => 0),
		'adwidth'	=> array('close' => 0),
		'adheight'	=> array('close' => 0),
		'adtarget'	=> array('close' => 0),
		'sharecode'	=> array('close' => 0),		
		'countcode'	=> array('close' => 0),			
		'beian'	=> array('close' => 0),	
		'cdn'	=> array('close' => 0),
		 
		'copyright'	=> array('close' => 0),
		'swturl'	=> array('close' => 0),
		'searchurl'	=> array('close' => 0),
		'gbookurl'	=> array('close' => 0),
		'gbookaddurl'	=> array('close' => 0),
		'vcodeurl'	=> array('close' => 0),	 
		'mobileauto'	=> array(
			'attr'	=> 'flag',//0自动,1是php,2是js
			'close' => 0
		),

		'prev'	=> array(
			'attr'	=> 'titlelen',//attr 属性列表
			'close' => 0
		),
		'next'	=> array(			
			'attr'	=> 'titlelen',//attr 属性列表
			'close' => 0
		),
		'click'	=> array('close' => 0),
		'yyonline' => array('close' => 0),

	);

	//标签名前加下划线
	//文章列表
	public function _artlist($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'artlist');
		////非debug参属性参数只处理 一次
		$flag = empty($attr['flag'])? '': $attr['flag'];
		$typeid = !isset($attr['typeid']) || $attr['typeid'] == '' ? -1 : trim($attr['typeid']);//-1后面自动获取
		$arcid  = empty($attr['arcid'])? '' : $attr['arcid'];//新增加20140413
		$titlelen = empty($attr['titlelen'])? 0 : intval($attr['titlelen']);
		$infolen = empty($attr['infolen'])? 0 : intval($attr['infolen']);		
		$orderby = empty($attr['orderby'])? 'id DESC' : $attr['orderby'];
		$limit = empty($attr['limit'])? '10' : $attr['limit'];
		$pagesize = empty($attr['pagesize'])? '0' : $attr['pagesize'];
		$keyword = empty($attr['keyword'])? '': trim($attr['keyword']);

		$flag = flag2sum($flag);
		$arcid = string2filter($arcid, ',', true);

		
		$str = <<<str
<?php
	\$_typeid = $typeid;	
	\$_keyword = "$keyword";
	\$_arcid = "$arcid";
	if(\$_typeid == -1) \$_typeid = I('get.cid', 0, 'intval');
	if (\$_typeid>0 || substr(\$_typeid,0,1) == '$') {
		import('Class.Category', APP_PATH);
		\$ids = Category::getChildsId(getCategory(), \$_typeid, true);
		//p(\$ids);
		\$where = array('article.status' => 0, 'article.cid'=> array('IN',\$ids));
	}else {
		\$where = array('article.status' => 0);
	}

	if (\$_keyword != '') {
		\$where['article.title'] = array('like','%'.\$_keyword.'%');
	}
	if (!empty(\$_arcid)) {
		\$where['article.id'] = array('IN', \$_arcid);
	}

	if ($flag > 0) {	
		\$where['_string'] = 'article.flag & $flag = $flag ';	
	}

	//分页
	if ($pagesize > 0) {
		
		import('ORG.Util.Page');
		\$count = D('ArticleView')->where(\$where)->count();

		\$thisPage = new Page(\$count, $pagesize);
		
		\$ename = I('e', '', 'htmlspecialchars,trim');
		if (!empty(\$ename) && C('URL_ROUTER_ON') == true) {
			\$thisPage->url = ''.\$ename. '/p';
		}
		//设置显示的页数
		\$thisPage->rollPage = 3;
		\$thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%');
		\$limit = \$thisPage->firstRow. ',' .\$thisPage->listRows;	
		\$page = \$thisPage->show();
	}else {
		\$limit = "$limit";
	}
	

	\$_artlist = D('ArticleView')->where(\$where)->order("$orderby")->limit(\$limit)->select();

	if (empty(\$_artlist)) {
		\$_artlist = array();
	}
	

	foreach(\$_artlist as \$autoindex => \$artlist):	

	\$_jumpflag = (\$artlist['flag'] & B_JUMP) == B_JUMP? true : false;
	\$artlist['url'] = getContentUrl(\$artlist['id'], \$artlist['cid'], \$artlist['ename'], \$_jumpflag, \$artlist['jumpurl']);

	if($titlelen) \$artlist['title'] = str2sub(\$artlist['title'], $titlelen, 0);
	if($infolen) \$artlist['description'] = str2sub(\$artlist['description'], $infolen, 0);

?>
str;
	$str .= $content;
	$str .='<?php endforeach;?>';
	return $str;

	}


	//产品列表
	public function _prolist($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'prolist');
		$flag = empty($attr['flag'])? '': $attr['flag'];
		$typeid = !isset($attr['typeid']) || $attr['typeid'] == '' ? -1 : trim($attr['typeid']);
		$arcid  = empty($attr['arcid'])? '' : $attr['arcid'];
		$titlelen = empty($attr['titlelen'])? 0 : intval($attr['titlelen']);
		$infolen = empty($attr['infolen'])? 0 : intval($attr['infolen']);		
		$orderby = empty($attr['orderby'])? 'id DESC' : $attr['orderby'];
		$limit = empty($attr['limit'])? '10' : $attr['limit'];
		$pagesize = empty($attr['pagesize'])? '0' : $attr['pagesize'];
		$keyword = empty($attr['keyword'])? '': trim($attr['keyword']);

		$flag = flag2sum($flag);
		$arcid = string2filter($arcid, ',', true);

		$str = <<<str
<?php
	\$_typeid = $typeid;	
	\$_keyword = "$keyword";
	\$_arcid = "$arcid";
	if(\$_typeid == -1) \$_typeid = I('get.cid', 0, 'intval');
	if (\$_typeid>0 || substr(\$_typeid,0,1) == '$') {
		import('Class.Category', APP_PATH);
		\$ids = Category::getChildsId(getCategory(), \$_typeid, true);
		//p(\$ids);
		\$where = array('product.status' => 0, 'product.cid'=> array('IN',\$ids));
	}else {
		\$where = array('product.status' => 0);
	}


	if (\$_keyword != '') {
		\$where['product.title'] = array('like','%'.\$_keyword.'%');
	}
	if (!empty(\$_arcid)) {
		\$where['product.id'] = array('IN', \$_arcid);
	}


	if ($flag > 0) {	
		\$where['_string'] = 'product.flag & $flag = $flag ';	
	}

	//分页
	if ($pagesize > 0) {
		
		import('ORG.Util.Page');
		\$count = D('ProductView')->where(\$where)->count();

		\$thisPage = new Page(\$count, $pagesize);
		
		\$ename = I('e', '', 'htmlspecialchars,trim');
		if (!empty(\$ename) && C('URL_ROUTER_ON') == true) {
			\$thisPage->url = ''.\$ename. '/p';
		}
		//设置显示的页数
		\$thisPage->rollPage = 3;
		\$thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%');
		\$limit = \$thisPage->firstRow. ',' .\$thisPage->listRows;	
		\$page = \$thisPage->show();
	}else {
		\$limit = "$limit";
	}
	

	\$_prolist = D('ProductView')->where(\$where)->order("$orderby")->limit(\$limit)->select();

	if (empty(\$_prolist)) {
		\$_prolist = array();
	}


	foreach(\$_prolist as \$autoindex => \$prolist):	
	\$_jumpflag = (\$prolist['flag'] & B_JUMP) == B_JUMP? true : false;
	\$prolist['url'] = getContentUrl(\$prolist['id'], \$prolist['cid'], \$prolist['ename'], \$_jumpflag, \$prolist['jumpurl']);


	if($titlelen) \$prolist['title'] = str2sub(\$prolist['title'], $titlelen, 0);
	if($infolen) \$prolist['description'] = str2sub(\$prolist['description'], $infolen, 0);

?>
str;
	$str .= $content;
	$str .='<?php endforeach;?>';
	return $str;

	}


	//图片列表
	public function _piclist($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'piclist');
		$flag = empty($attr['flag'])? '': $attr['flag'];
		$typeid = !isset($attr['typeid']) || $attr['typeid'] == '' ? -1 : trim($attr['typeid']);
		$arcid  = empty($attr['arcid'])? '' : $attr['arcid'];
		$titlelen = empty($attr['titlelen'])? 0 : intval($attr['titlelen']);
		$infolen = empty($attr['infolen'])? 0 : intval($attr['infolen']);		
		$orderby = empty($attr['orderby'])? 'id DESC' : $attr['orderby'];
		$limit = empty($attr['limit'])? '10' : $attr['limit'];
		$pagesize = empty($attr['pagesize'])? '0' : $attr['pagesize'];
		$keyword = empty($attr['keyword'])? '': trim($attr['keyword']);

		$flag = flag2sum($flag);
		$arcid = string2filter($arcid, ',', true);
		
		$str = <<<str
<?php
	\$_typeid = $typeid;		
	\$_keyword = "$keyword";
	\$_arcid = "$arcid";
	if(\$_typeid == -1) \$_typeid = I('cid', 0, 'intval');
	if (\$_typeid>0 || substr(\$_typeid,0,1) == '$') {
		import('Class.Category', APP_PATH);
		\$ids = Category::getChildsId(getCategory(), \$_typeid, true);
		//p(\$ids);
		\$where = array('picture.status' => 0, 'picture.cid'=> array('IN',\$ids));
	}else {
		\$where = array('picture.status' => 0);
	}

	if (\$_keyword != '') {
		\$where['picture.title'] = array('like','%'.\$_keyword.'%');
	}
	if (!empty(\$_arcid)) {
		\$where['picture.id'] = array('IN', \$_arcid);
	}


	if ($flag > 0) {	
		\$where['_string'] = 'picture.flag & $flag = $flag ';	
	}

	//分页
	if ($pagesize > 0) {
		
		import('ORG.Util.Page');
		\$count = D('PictureView')->where(\$where)->count();

		\$thisPage = new Page(\$count, $pagesize);
		
		\$ename = I('e', '', 'htmlspecialchars,trim');
		if (!empty(\$ename) && C('URL_ROUTER_ON') == true) {
			\$thisPage->url = ''.\$ename. '/p';
		}
		//设置显示的页数
		\$thisPage->rollPage = 3;
		\$thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%');
		\$limit = \$thisPage->firstRow. ',' .\$thisPage->listRows;	
		\$page = \$thisPage->show();
	}else {
		\$limit = "$limit";
	}
	

	\$_piclist = D('PictureView')->where(\$where)->order("$orderby")->limit(\$limit)->select();

	if (empty(\$_piclist)) {
		\$_piclist = array();
	}


	foreach(\$_piclist as \$autoindex => \$piclist):
	\$_jumpflag = (\$piclist['flag'] & B_JUMP) == B_JUMP? true : false;
	\$piclist['url'] = getContentUrl(\$piclist['id'], \$piclist['cid'], \$piclist['ename'], \$_jumpflag, \$piclist['jumpurl']);
	if($titlelen) \$piclist['title'] = str2sub(\$piclist['title'], $titlelen, 0);
	if($infolen) \$piclist['description'] = str2sub(\$piclist['description'], $infolen, 0);

?>
str;
	$str .= $content;
	$str .='<?php endforeach;?>';
	return $str;

	}


	//软件下载列表
	public function _soflist($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'soflist');
		$flag = empty($attr['flag'])? '': $attr['flag'];
		$typeid = !isset($attr['typeid']) || $attr['typeid'] == '' ? -1 : trim($attr['typeid']);
		$arcid  = empty($attr['arcid'])? '' : $attr['arcid'];
		$titlelen = empty($attr['titlelen'])? 0 : intval($attr['titlelen']);
		$infolen = empty($attr['infolen'])? 0 : intval($attr['infolen']);		
		$orderby = empty($attr['orderby'])? 'id DESC' : $attr['orderby'];
		$limit = empty($attr['limit'])? '10' : $attr['limit'];
		$pagesize = empty($attr['pagesize'])? '0' : $attr['pagesize'];
		$keyword = empty($attr['keyword'])? '': trim($attr['keyword']);

		$flag = flag2sum($flag);
		$arcid = string2filter($arcid, ',', true);

		$str = <<<str
<?php
	\$_typeid = $typeid;	
	\$_keyword = "$keyword";
	\$_arcid = "$arcid";
	if(\$_typeid == -1) \$_typeid = I('cid', 0, 'intval');
	if (\$_typeid>0 || substr(\$_typeid,0,1) == '$') {
		import('Class.Category', APP_PATH);
		\$ids = Category::getChildsId(getCategory(), \$_typeid, true);
		//p(\$ids);
		\$where = array('soft.status' => 0, 'soft.cid'=> array('IN',\$ids));
	}else {
		\$where = array('soft.status' => 0);
	}

	if (\$_keyword != '') {
		\$where['soft.title'] = array('like','%'.\$_keyword.'%');
	}
	if (!empty(\$_arcid)) {
		\$where['soft.id'] = array('IN', \$_arcid);
	}

	if ($flag > 0) {	
		\$where['_string'] = 'soft.flag & $flag = $flag ';	
	}

	//分页
	if ($pagesize > 0) {
		
		import('ORG.Util.Page');
		\$count = D('SoftView')->where(\$where)->count();

		\$thisPage = new Page(\$count, $pagesize);
		
		\$ename = I('e', '', 'htmlspecialchars,trim');
		if (!empty(\$ename) && C('URL_ROUTER_ON') == true) {
			\$thisPage->url = ''.\$ename. '/p';
		}
		//设置显示的页数
		\$thisPage->rollPage = 3;
		\$thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%');
		\$limit = \$thisPage->firstRow. ',' .\$thisPage->listRows;	
		\$page = \$thisPage->show();
	}else {
		\$limit = "$limit";
	}
	

	\$_soflist = D('SoftView')->where(\$where)->order("$orderby")->limit(\$limit)->select();

	if (empty(\$_soflist)) {
		\$_soflist = array();
	}
	

	foreach(\$_soflist as \$autoindex => \$soflist):	
	\$_jumpflag = (\$soflist['flag'] & B_JUMP) == B_JUMP? true : false;
	\$soflist['url'] = getContentUrl(\$soflist['id'], \$soflist['cid'], \$soflist['ename'], \$_jumpflag, \$soflist['jumpurl']);
	if($titlelen) \$soflist['title'] = str2sub(\$soflist['title'], $titlelen, 0);
	if($infolen) \$soflist['description'] = str2sub(\$soflist['description'], $infolen, 0);

?>
str;
	$str .= $content;
	$str .='<?php endforeach;?>';
	return $str;

	}


	//标签名前加下划线
	//文章列表
	public function _list($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'list');
		$flag = empty($attr['flag'])? '': $attr['flag'];
		$typeid = !isset($attr['typeid']) || $attr['typeid'] == '' ? -1 : trim($attr['typeid']);//只接收一个栏目ID
		$titlelen = empty($attr['titlelen'])? 0 : intval($attr['titlelen']);
		$infolen = empty($attr['infolen'])? 0 : intval($attr['infolen']);		
		$orderby = empty($attr['orderby'])? 'id DESC' : $attr['orderby'];
		$limit = empty($attr['limit'])? '10' : $attr['limit'];
		$pagesize = empty($attr['pagesize'])? '0' : $attr['pagesize'];
		$keyword = empty($attr['keyword'])? '': trim($attr['keyword']);
		

		$flag = flag2sum($flag); 
		$str = <<<str
<?php 
	\$_typeid = $typeid;	
	\$_keyword = "$keyword";
	if(\$_typeid == -1) \$_typeid = I('cid', 0, 'intval');
	if (\$_typeid>0 || substr(\$_typeid,0,1) == '$') {
		import('Class.Category', APP_PATH);
		\$_selfcate = Category::getSelf(getCategory(), \$_typeid);
		\$_tablename = strtolower(\$_selfcate['tablename']);
		\$ids = Category::getChildsId(getCategory(), \$_typeid, true);
		//p(\$ids);
		\$where = array(\$_tablename.'.status' => 0, \$_tablename .'.cid'=> array('IN',\$ids));
	}else {
		\$_tablename = 'article';
		\$where = array(\$_tablename.'.status' => 0);
		
	}
	if (\$_keyword != '') {
		\$where[\$_tablename.'.title'] = array('like','%'.\$_keyword.'%');
	}


	if ($flag > 0) {	
		\$where['_string'] = \$_tablename.'.flag & $flag = $flag ';	
	}

	if (!empty(\$_tablename) && \$_tablename != 'page') {
	
		//分页
		if ($pagesize > 0) {
			
			import('ORG.Util.Page');
			\$count = D(ucfirst(\$_tablename ).'View')->where(\$where)->count();

			\$thisPage = new Page(\$count, $pagesize);
			
			\$ename = I('e', '', 'htmlspecialchars,trim');
			if (!empty(\$ename) && C('URL_ROUTER_ON') == true) {
				\$thisPage->url = ''.\$ename. '/p';
			}
			//设置显示的页数
			\$thisPage->rollPage = 3;
			\$thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%');
			\$limit = \$thisPage->firstRow. ',' .\$thisPage->listRows;	
			\$page = \$thisPage->show();

		}else {
			\$limit = "$limit";
		}	

		\$_list = D(ucfirst(\$_tablename ).'View')->where(\$where)->order("$orderby")->limit(\$limit)->select();
		if (empty(\$_list)) {
			\$_list = array();
		}
	}else {
		\$_list = array();
	}


	//Load('extend');//调用msubstr()  
	
	foreach(\$_list as \$autoindex => \$list):

	\$_jumpflag = (\$list['flag'] & B_JUMP) == B_JUMP? true : false;
	\$list['url'] = getContentUrl(\$list['id'], \$list['cid'], \$list['ename'], \$_jumpflag, \$list['jumpurl']);
	if($titlelen) \$list['title'] = str2sub(\$list['title'], $titlelen, 0);	
	if($infolen) \$list['description'] = str2sub(\$list['description'], $infolen, 0);
  
	
	

?>
str;
	$str .= $content;
	$str .='<?php endforeach;?>';
	return $str;

	}



	//专题列表
	public function _spelist($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'spelist');
		$flag = empty($attr['flag'])? '': $attr['flag'];
		$typeid = !isset($attr['typeid']) || $attr['typeid'] == '' ? 0 : trim($attr['typeid']);//只接收一个栏目ID
		$titlelen = empty($attr['titlelen'])? 0 : intval($attr['titlelen']);
		$infolen = empty($attr['infolen'])? 0 : intval($attr['infolen']);		
		$orderby = empty($attr['orderby'])? 'id DESC' : $attr['orderby'];
		$limit = empty($attr['limit'])? '10' : $attr['limit'];
		$pagesize = empty($attr['pagesize'])? '0' : $attr['pagesize'];
		$keyword = empty($attr['keyword'])? '': trim($attr['keyword']);
		

		$flag = flag2sum($flag);
		
		$str = <<<str
<?php
	\$_typeid = $typeid;	
	\$_keyword = "$keyword";
	if (\$_typeid>0) {
		import('Class.Category', APP_PATH);
		\$_selfcate = Category::getSelf(getCategory(), \$_typeid);
		if (\$_selfcate) {
			\$_tablename = strtolower(\$_selfcate['tablename']);
			\$ids = Category::getChildsId(getCategory(), \$_typeid, true);

			\$where = array('special.status' => 0, 'special.cid'=> array('IN',\$ids));
		}else {
			\$_typeid = 0;
		}			
		
	}
	if (\$_typeid == 0) {
		\$where = array('special.status' => 0);
		
	}

	if (\$_keyword != '') {
		\$where['special.title'] = array('like','%'.\$_keyword.'%');
	}


	if ($flag > 0) {	
		\$where['_string'] = 'special.flag & $flag = $flag ';	
	}


	//分页
	if ($pagesize > 0) {
		
		import('ORG.Util.Page');
		\$count = D('SpecialView')->where(\$where)->count();

		\$thisPage = new Page(\$count, $pagesize);
		
		\$ename = I('e', '', 'htmlspecialchars,trim');
		if (!empty(\$ename) && C('URL_ROUTER_ON') == true) {
			\$thisPage->url = ''.\$ename. '/p';
		}
		//设置显示的页数
		\$thisPage->rollPage = 3;
		\$thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%');
		\$limit = \$thisPage->firstRow. ',' .\$thisPage->listRows;	
		\$page = \$thisPage->show();

	}else {
		\$limit = "$limit";
	}	

	\$_spelist = D('SpecialView')->where(\$where)->order("$orderby")->limit(\$limit)->select();
	if (empty(\$_spelist)) {
		\$_spelist = array();
	}


	foreach(\$_spelist as \$autoindex => \$spelist):

	if ((\$spelist['flag'] & B_JUMP)  && !empty(\$spelist['jumpurl'])) {
        \$spelist['url'] = \$spelist['jumpurl'];
    }else {
    	//开启路由
	    if(C('URL_ROUTER_ON') == true) {
	        \$spelist['url'] = U('Special/'.\$spelist['id'],'');
	    }else {
	        \$spelist['url']  = U('Special/shows', array('id'=> \$spelist['id']));     
	        
	    }
    }
	

	if($titlelen) \$spelist['title'] = str2sub(\$spelist['title'], $titlelen, 0);	
	if($infolen) \$spelist['description'] = str2sub(\$spelist['description'], $infolen, 0);

?>
str;
	$str .= $content;
	$str .='<?php endforeach;?>';
	return $str;

	}



	//当前栏目名称
	public function _type($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'artlist');
		$typeid = empty($attr['typeid'])? 0 : intval($attr['typeid']);
		if ($typeid == 0) {
			$typeid = $attr['typeid'];
		}
		$str = <<<str
<?php

	import('Class.Category', APP_PATH);	
	\$type = Category::getSelf(getCategory(0), $typeid);		
	\$type['url'] = getUrl(\$type);
	

?>
str;
		$str .= $content;
		return $str;

	}

	//导航
	public function _catlist($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'catlist');
		$typeid = !isset($attr['typeid']) || $attr['typeid'] == '' ? -1 : trim($attr['typeid']);//只接收一个栏目ID
		$type = empty($attr['type'])? 'son' : $attr['type'];//son表示下级栏目,self表示同级栏目,top顶级栏目(top忽略typeid)
		$flag = empty($attr['flag']) ? 0: intval($attr['flag']);//0(不显示链接和单页),1(全部显示),2
		$orderby = empty($attr['orderby'])? 'id DESC' : $attr['orderby'];
		$limit = empty($attr['limit'])? '30' : $attr['limit'];
		$str = <<<str
<?php
	\$_typeid = intval($typeid);
	\$type = "$type";
	if(\$_typeid == -1) \$_typeid = I('cid', 0, 'intval');
	\$__catlist = getCategory(1);

	import('Class.Category', APP_PATH);	
	if ($flag == 0) {
		//where array('status' => 1, 'type' => 0 , 'modelid' => array('neq',2));//2是单页模型
		\$__catlist = Category::clearPageAndLink(\$__catlist);
	}
	
	//\$type为top,忽略$typeid
	if(\$_typeid == 0 || \$type == 'top') {
		\$_catlist  = Category::unlimitedForLayer(\$__catlist);
	}else {
		//同级分类
		if (\$type == 'self') {
			\$_typeinfo  = Category::getSelf(\$__catlist, \$_typeid );
			//if (\$_typeinfo['pid'] != 0) {
				\$_catlist  = Category::unlimitedForLayer(\$__catlist, 'child', \$_typeinfo['pid']);
			//}
		}else {
			//son，子类列表
			\$_catlist  = Category::unlimitedForLayer(\$__catlist, 'child', \$_typeid);
		}
	}

	foreach(\$_catlist as \$autoindex => \$catlist):
	if(\$autoindex >= $limit) break;
	\$catlist['url'] = getUrl(\$catlist);
?>
str;

	$str .= $content;
	$str .='<?php endforeach;?>';
	return $str;

	}
	//导航
	public function _catlistsub($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'catlistsub');
		$typeid = !isset($attr['typeid']) || $attr['typeid'] == '' ? -1 : trim($attr['typeid']);//只接收一个栏目ID
		$type = empty($attr['type'])? 'son' : $attr['type'];//son表示下级栏目,self表示同级栏目,top顶级栏目(top忽略typeid)
		$flag = empty($attr['flag']) ? 0: intval($attr['flag']);//0(不显示链接和单页),1(全部显示),2
		$orderby = empty($attr['orderby'])? 'id DESC' : $attr['orderby'];
		$limit = empty($attr['limit'])? '10' : $attr['limit'];
		$str = <<<str
<?php
	\$_typeid = intval($typeid);
	\$type = "$type";
	if(\$_typeid == -1) \$_typeid = I('cid', 0, 'intval');
	\$__catlistsub = getCategory(1);  
	
   
	import('Class.Category', APP_PATH);	
	//if ($flag == 0) {
	//	//where array('status' => 1, 'type' => 0 , 'modelid' => array('neq',2));//2是单页模型
	//	\$__catlistsub = Category::clearPageAndLink(\$__catlistsub); 
	//	dump(\$__catlistsub);
	//}
	
	//\$type为top,忽略$typeid
	if(\$_typeid == 0 || \$type == 'top') {
		\$_catlistsub  = Category::unlimitedForLayer(\$__catlistsub);
	}else {
		//同级分类
		if (\$type == 'self') {
			\$_typeinfo  = Category::getSelf(\$__catlistsub, \$_typeid );
			//if (\$_typeinfo['pid'] != 0) {
				\$_catlistsub  = Category::unlimitedForLayer(\$__catlistsub, 'child', \$_typeinfo['pid']);
			//}
		}else {
			//son，子类列表
			\$_catlistsub  = Category::unlimitedForLayer(\$__catlistsub, 'child', \$_typeid);
		}
	}

	foreach(\$_catlistsub as \$autoindex => \$catlistsub):
	if(\$autoindex >= $limit) break;
	\$catlistsub['url'] = getUrl(\$catlistsub);
?>
str;

		$str .= $content;
		$str .='<?php endforeach;?>';
		return $str;

	}

	//导航
	public function _navlist($attr, $content) {
		//$attr = $this->parseXmlAttr($attr, 'navlist');
		$attr = !empty($attr)? $this->parseXmlAttr($attr, 'navlist') : null;
		$typeid = $attr['typeid'] == '' ? -1 : intval($attr['typeid']);//不能用empty,0,'','0',会认为true 
		$limit = empty($attr['limit'])? '-1' : $attr['limit']; //2014年5月12日17:04:03  0为空不限条
		$str = <<<str
<?php
	\$_typeid = $typeid;
	if(\$_typeid == -1) \$_typeid = I('cid', 0, 'intval');
	\$_navlist = getCategory(1);
	import('Class.Category', APP_PATH);	
	if(\$_typeid == 0) {
		\$_navlist  = Category::unlimitedForLayer(\$_navlist);
		
	}else {
		\$_navlist  = Category::unlimitedForLayer(\$_navlist, 'child', \$_typeid);
	}

	foreach(\$_navlist as \$autoindex => \$navlist):{	
		\$navlist['url'] = getUrl(\$navlist);	
		 if($limit!='-1'){
		  if(\$autoindex >= $limit) break;
		 }
		
   }
?>
str;

	$str .= $content;
	$str .='<?php endforeach;?>';
	return $str;

	}


	//user list
	public function _userlist($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'userlist');
		$typeid = isset($attr['typeid']) ? trim($attr['typeid']) : 0;
		$typeid = trim($typeid) == '' ? 0 : $typeid;
		$titlelen = empty($attr['titlelen'])? 0 : intval($attr['titlelen']);
		$infolen = empty($attr['infolen'])? 0 : intval($attr['infolen']);		
		$orderby = empty($attr['orderby'])? 'id DESC' : $attr['orderby'];
		$limit = empty($attr['limit'])? '10' : $attr['limit'];
		$pagesize = empty($attr['pagesize'])? '0' : $attr['pagesize'];
		//echo "$typeid---------";

		
		$str = <<<str
<?php
	\$_typeid = $typeid;	
	if (\$_typeid>0 || substr(\$_typeid,0,1) == '$') {
		\$where = array('member.islock' => 0, 'member.groupid'=> \$_typeid);
	}else {
		\$where = array('member.islock' => 0);
	}

	//分页
	if ($pagesize > 0) {
		
		import('ORG.Util.Page');
		\$count = D('MemberView')->where(\$where)->count();

		\$thisPage = new Page(\$count, $pagesize);
		
		//设置显示的页数
		\$thisPage->rollPage = 3;
		\$thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%');
		\$limit = \$thisPage->firstRow. ',' .\$thisPage->listRows;	
		\$page = \$thisPage->show();
	}else {
		\$limit = "$limit";
	}
	

	\$_userlist = D('MemberView')->where(\$where)->order("$orderby")->limit(\$limit)->select();
	if (empty(\$_userlist)) {
		\$_userlist = array();
	}

	foreach(\$_userlist as \$autoindex => \$userlist):
	//开启路由
	if(C('URL_ROUTER_ON') == true) {
		\$userlist['url'] = U('u/'. \$userlist['id']);
	}else {
		\$userlist['url'] = U(GROUP_NAME.'/Public/user', array('id'=> \$userlist['id']));
	}


?>
str;
	$str .= $content;
	$str .='<?php endforeach;?>';
	return $str;

	}

	//announce list
	public function _announcelist($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'announcelist');
		$titlelen = empty($attr['titlelen'])? 0 : intval($attr['titlelen']);
		$infolen = empty($attr['infolen'])? 0 : intval($attr['infolen']);		
		$orderby = empty($attr['orderby'])? 'starttime DESC' : $attr['orderby'];
		$limit = empty($attr['limit'])? '10' : $attr['limit'];
		$pagesize = empty($attr['pagesize'])? '0' : $attr['pagesize'];
	

		
		$str = <<<str
<?php

	\$where = array('endtime' => array('gt',time()));


	//分页
	if ($pagesize > 0) {
		
		import('ORG.Util.Page');
		\$count = M('announce')->where(\$where)->count();

		\$thisPage = new Page(\$count, $pagesize);
		

		//设置显示的页数
		\$thisPage->rollPage = 3;
		\$thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%');
		\$limit = \$thisPage->firstRow. ',' .\$thisPage->listRows;	
		\$page = \$thisPage->show();
	}else {
		\$limit = "$limit";
	}
	

	\$_announcelist = M('announce')->where(\$where)->order("$orderby")->limit(\$limit)->select();
	if (empty(\$_announcelist)) {
		\$_announcelist = array();
	}

	foreach(\$_announcelist as \$autoindex => \$announcelist):

	if($titlelen) \$announcelist['title'] = str2sub(\$announcelist['title'], $titlelen, 0);
	if($infolen) \$announcelist['content'] = str2sub(strip_tags(\$announcelist['content']), $infolen, 0);//清除html再截取


?>
str;
	$str .= $content;
	$str .='<?php endforeach;?>';
	return $str;

	}

	//friend Link list
	public function _flink($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'flink');
		$typeid = !isset($attr['typeid']) || $attr['typeid'] == '' ? 0 : trim($attr['typeid']);
		$typeid = trim($typeid) == '' ? 0 : $typeid; 
		$titlelen = empty($attr['titlelen'])? 0 : intval($attr['titlelen']);
		$infolen = empty($attr['infolen'])? 0 : intval($attr['infolen']);		
		$orderby = empty($attr['orderby'])? 'sort ASC' : $attr['orderby'];
		$limit = empty($attr['limit'])? '10' : $attr['limit'];
		$pagesize = empty($attr['pagesize'])? '0' : $attr['pagesize'];
		//echo "$typeid---------";

		
		$str = <<<str
<?php
	\$_typeid = $typeid;	
	if (\$_typeid==0) {
		\$where = array('ischeck'=> \$_typeid);
	}else if (\$_typeid==1) { 
		\$where = array('ischeck'=> \$_typeid);
	}else{
		\$where = array('id' => array('gt',0));
	}

	//分页
	if ($pagesize > 0) {
		
		import('ORG.Util.Page');
		\$count = M('link')->where(\$where)->count();

		\$thisPage = new Page(\$count, $pagesize);
		

		//设置显示的页数
		\$thisPage->rollPage = 3;
		\$thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%');
		\$limit = \$thisPage->firstRow. ',' .\$thisPage->listRows;	
		\$page = \$thisPage->show();
	}else {
		\$limit = "$limit";
	}
	

	\$_flink = M('link')->where(\$where)->order("$orderby")->limit(\$limit)->select();
	if (empty(\$_flink)) {
		\$_flink = array();
	}

	foreach(\$_flink as \$autoindex => \$flink):



?>
str;
	$str .= $content;
	$str .='<?php endforeach;?>';
	return $str;

	}

	//2014年5月18日22:07:36
	//adlink list
	public function _adlink($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'adlink'); 
		$typeid = !isset($attr['typeid']) || $attr['typeid'] == '' ? 0 : trim($attr['typeid']);
		$typeid = trim($typeid) == '' ? 0 : $typeid; 
		$titlelen = empty($attr['titlelen'])? 0 : intval($attr['titlelen']);
		$infolen = empty($attr['infolen'])? 0 : intval($attr['infolen']);		
		$orderby = empty($attr['orderby'])? 'sort ASC' : $attr['orderby'];
		$limit = empty($attr['limit'])? C('cfg_ad_num') : $attr['limit'];
		$pagesize = empty($attr['pagesize'])? '0' : $attr['pagesize'];
		//echo "$typeid---------";

		
		$str = <<<str
<?php
	\$_typeid = $typeid;	
	if (\$_typeid==0) {
		\$where = array('ischeck'=> \$_typeid);
	}else if (\$_typeid==1) { 
		\$where = array('ischeck'=> \$_typeid);
	}else{
		\$where = array('id' => array('gt',0));
	}

	//分页
	if ($pagesize > 0) {
		
		import('ORG.Util.Page');
		\$count = M('adbanner')->where(\$where)->count();

		\$thisPage = new Page(\$count, $pagesize);
		

		//设置显示的页数
		\$thisPage->rollPage = 3;
		\$thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%');
		\$limit = \$thisPage->firstRow. ',' .\$thisPage->listRows;	
		\$page = \$thisPage->show();
	}else {
		\$limit = "$limit";
	}
	

	\$_adlink = M('adbanner')->where(\$where)->order("$orderby")->limit(\$limit)->select();
	if (empty(\$_adlink)) {
		\$_adlink = array();
	}

	foreach(\$_adlink as \$autoindex => \$adlink):



?>
str;
		$str .= $content;
		$str .='<?php endforeach;?>';
		return $str;

	}


	//2014年6月29日11:41:40
	//adlist list
	public function _adlist($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'adlist'); 
		$typeid = !isset($attr['typeid']) || $attr['typeid'] == '' ? 1 : trim($attr['typeid']);
		$typeid = trim($typeid) == '' ? 1 : $typeid; 
		$id = !isset($attr['id']) || $attr['id'] == '' ? 1 : trim($attr['id']);
		$id = trim($id) == '' ? 1 : $id; 
		$titlelen = empty($attr['titlelen'])? 0 : intval($attr['titlelen']);
		$infolen = empty($attr['infolen'])? 0 : intval($attr['infolen']);		
		$orderby = empty($attr['orderby'])? 'sort ASC' : $attr['orderby'];
		$limit = empty($attr['limit'])? 1 : $attr['limit'];
		$pagesize = empty($attr['pagesize'])? '0' : $attr['pagesize'];
		//echo "$typeid---------";
		
		
		$str = <<<str
<?php
	\$_id = $id;	
    \$_typeid = $typeid;	
	if (\$_typeid==0) {
		\$where = array('ischeck'=> \$_typeid,'id'=>\$_id);
	}else if (\$_typeid==1) { 
		\$where = array('ischeck'=> \$_typeid,'id'=>\$_id);
	}else{
		\$where = array('id' => \$_id);
	}
	//分页
	if ($pagesize > 0) {
		
		import('ORG.Util.Page');
		\$count = M('adlist')->where(\$where)->count();

		\$thisPage = new Page(\$count, $pagesize);
		

		//设置显示的页数
		\$thisPage->rollPage = 3;
		\$thisPage->setConfig('theme',' %upPage% %downPage% %linkPage%');
		\$limit = \$thisPage->firstRow. ',' .\$thisPage->listRows;	
		\$page = \$thisPage->show();
	}else {
		\$limit = "$limit";
	}
	

	\$_adlist = M('adlist')->where(\$where)->order("$orderby")->limit(\$limit)->select();
	 
	if (empty(\$_adlist)) {
		\$_adlist = array();
	}

	foreach(\$_adlist as \$autoindex => \$adlist):



?>
str;
		$str .= $content;
		$str .='<?php endforeach;?>';
		return $str;

	}


	//2014年6月29日11:41:40
	//adimg img
	public function _adimg($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'adimg'); 
		$typeid = !isset($attr['typeid']) || $attr['typeid'] == '' ? 0 : trim($attr['typeid']);
		$typeid = trim($typeid) == '' ? 0 : $typeid; 
		$id = !isset($attr['id']) || $attr['id'] == '' ? 0 : trim($attr['id']);
		$id = trim($id) == '' ? 1 : $id;   
		$adlist = M('adlist')->where(array('ischeck'=> $typeid,'id'=>$id))->find();  
		if(!empty($adlist))
		{
			$target ='';
			if($adlist['target']=='1') 
			{
				$target = 'target="_blank"';
			}
			return '<a href="'.$adlist['url'] .'"  '.$target.' title="'.$adlist['description'].'"><img  alt="'.$adlist['description'].'"  title="'.$adlist['description'].'" width="'.$adlist['width'].'px" height="'.$adlist['height'].'px" src="'.$adlist['logo'].'"></a>';
		}
		else{
			return '';
		}
		 

	}


	//iteminfo List
	public function _iteminfo($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'iteminfo');
		$name = isset($attr['name']) ? trim($attr['name']) : '';
		$titlelen = empty($attr['titlelen'])? 0 : intval($attr['titlelen']);
		$limit = empty($attr['limit'])? '10' : $attr['limit'];
		
		$str = <<<str
<?php
	
	if ("$name" == '') {
		\$_iteminfo= array();
	}else {
		\$_iteminfo = getArrayOfItem("$name");
	}



	foreach(\$_iteminfo as \$autoindex => \$iteminfo):
	if($titlelen>0) \$iteminfo = str2sub(\$iteminfo, $titlelen);

?>
str;
	$str .= $content;
	$str .='<?php endforeach;?>';
	return $str;

	}


	public function _block($attr, $content) {
		$attr = !empty($attr)? $this->parseXmlAttr($attr, 'block') : null;
		$name = isset($attr['name']) ? $attr['name'] : '';
		$infolen = empty($attr['infolen']) ? 0 : intval($attr['infolen']);
		$textflag = empty($attr['textflag']) ? 0 : 1;

		$name = trim(htmlspecialchars($name));
		$str =<<<str
<?php

	\$block = getBlock("$name");
	\$block_content = '';
	if (\$block) {
		if (\$block['blocktype'] == 2) {
			if (!$textflag) {
				\$block_content = '<img src="'. \$block['content'] .'" />';
			}else {
				\$block_content = \$block['content'];
			}
			
		}else {
			if($infolen) {
				\$block_content = str2sub(strip_tags(\$block['content']), $infolen, 0);//清除html再截取
			}else {
				\$block_content = \$block['content'];
			}
		}
	}
	echo \$block_content;


?>
str;

		return $str;
	}


	//调用栏目或内容的指定字段
	public function _field($attr, $content) {
		$attr = $this->parseXmlAttr($attr, 'field');
		$typeid = !isset($attr['typeid']) || $attr['typeid'] == '' ? 0 : trim($attr['typeid']);//只接收一个栏目ID
		$artid = empty($attr['artid'])? 0 : intval($attr['artid']);
		$infolen = empty($attr['infolen'])? 0 : intval($attr['infolen']);	
		$name = empty($attr['name'])? '': trim($attr['name']);
		$imgindex = empty($attr['imgindex'])? 0 : intval($attr['imgindex']);	
		$imgwidth = empty($attr['imgwidth'])? 0 : intval($attr['imgwidth']);	
		$imgheight = empty($attr['imgheight'])? 0 : intval($attr['imgheight']);	
		

		
		$str = <<<str
<?php
	\$_typeid = $typeid;	
	\$_fieldname = "$name";
	\$_tempstr = '';
	if (\$_typeid>0 && !empty(\$_fieldname)) {
		import('Class.Category', APP_PATH);
		\$_selfcate = Category::getSelf(getCategory(), \$_typeid);
		\$_tablename = strtolower(\$_selfcate['tablename']);

		if (\$_tablename == 'page' || $artid == 0) {
			\$_tempstr = M('category')->where(array('id' => \$_typeid))->getField(\$_fieldname);
		}elseif (!empty(\$_selfcate )) {
			\$_tempstr = M(\$_tablename)->where(array('id' => $artid))->getField(\$_fieldname);
			if (\$_fieldname == 'pictureurls' || \$_fieldname == 'litpic') {
				if (empty(\$_tempstr)) {
					\$_tempstr = get_picture('', $imgwidth, $imgheight);
				}elseif (\$_fieldname == 'litpic') {
					\$_tempstr = get_picture(\$_tempstr, $imgwidth, $imgheight);
				}elseif (\$_fieldname == 'pictureurls') {
						\$_pictureurls_arr = explode('|||', \$_tempstr);			
						\$_pictureurls  = array();
						foreach (\$_pictureurls_arr as \$v) {
							\$temp_arr = explode('$$$', \$v);
							if (!empty(\$temp_arr[0])) {
								\$_pictureurls[] = array(
									'url' => \$temp_arr[0],
									'alt' => \$temp_arr[1]
								);
							}				
						}
					if(!isset(\$_pictureurls[$imgindex]['url'])) \$_pictureurls[$imgindex]['url'] = '';
					\$_tempstr = get_picture(\$_pictureurls[$imgindex]['url'],$imgwidth, $imgheight);
				}
			}
			 
		}
		if ($infolen >0 && !empty(\$_tempstr)) {
			\$_tempstr = str2sub(strip_tags(\$_tempstr), $infolen, 0);//清除html再截取
		}	
		
	}

	echo \$_tempstr;

?>
str;

	return $str;

	}




	/**/
	public function _position($attr, $content) {
		//非debug参数只处理 一次
		$attr = !empty($attr)?$this->parseXmlAttr($attr, 'position') : null;
		$typeid = !isset($attr['typeid']) || $attr['typeid'] == '' ? -1: trim($attr['typeid']);//只接收一个栏目ID
		$ismobile = empty($attr['ismobile'])? 0 : 1;
		$sname = isset($attr['sname'])? trim($attr['sname']) : '';	
		$surl = isset($attr['surl'])? trim($attr['surl']) : '';	
		$delimiter = isset($attr['delimiter'])? trim($attr['delimiter']) : '';

		$str =<<<str
<?php
		\$_sname = "$sname";
		\$_typeid =$typeid;
		//debug关闭后,typeid值不变
		//没有下面这步，非debug下，会写死了
		if(\$_typeid == -1) \$_typeid = I('cid', 0, 'intval');

		if (\$_typeid == 0 &&  \$_sname == '') {
			\$_sname = isset(\$title) ? \$title : '';
		}
		echo getPosition(\$_typeid, \$_sname, "$surl", $ismobile, "$delimiter");

?>
str;

		return $str;
	}


	public function _prev($attr, $content) {
		$attr = !empty($attr)? $this->parseXmlAttr($attr, 'prev') : null;
		$titlelen = empty($attr['titlelen']) ? 0 : intval($attr['titlelen']);
		$class = isset($attr['class'])? trim($attr['class']) : '';	
		$title = isset($attr['title'])? trim($attr['title']) : '';
			
		$str =<<<str
<?php

	if(empty(\$content['id']) || empty(\$content['cid']) || empty(\$cate['tablename']) ) {
		echo '无记录';
	} else {
		//上一条记录
        \$_vo=D(ucfirst(\$cate['tablename'].'View'))->where(array(\$cate['tablename'].'.status' => 0, 'id' => array('lt',\$content['id'])))->order('id desc')->find();

        if (\$_vo) {

			\$_jumpflag = (\$_vo['flag'] & B_JUMP) == B_JUMP? true : false;
        	\$_vo['url'] = getContentUrl(\$_vo['id'], \$_vo['cid'], \$_vo['ename'], \$_jumpflag, \$_vo['jumpurl']);
			if($titlelen) \$_vo['title'] = str2sub(\$_vo['title'], $titlelen, 0);	
			echo '<a href="'. \$_vo['url'] .'" >'. \$_vo['title'] .'</a>';
        } else {
        	echo '第一篇';
        }
	}

?>
str;

		return $str;
	}

	public function _next($attr, $content) {
		$attr = !empty($attr)? $this->parseXmlAttr($attr, 'next') : null;
		$titlelen = empty($attr['titlelen']) ? 0 : intval($attr['titlelen']);
		$str =<<<str
<?php
	if(empty(\$content['id']) || empty(\$content['cid']) || empty(\$cate['tablename']) ) {
		echo '无记录';
	} else {
		//下一条记录
        \$_vo=D(ucfirst(\$cate['tablename'].'View'))->where(array(\$cate['tablename'].'.status' => 0,'id' => array('gt',\$content['id'])))->order('id ASC')->find();

        if (\$_vo) {	

			\$_jumpflag = (\$_vo['flag'] & B_JUMP) == B_JUMP? true : false;
        	\$_vo['url'] = getContentUrl(\$_vo['id'], \$_vo['cid'], \$_vo['ename'], \$_jumpflag, \$_vo['jumpurl']);				
			if($titlelen) \$_vo['title'] = str2sub(\$_vo['title'], $titlelen, 0);	
			echo '<a href="'. \$_vo['url'] .'" >'. \$_vo['title'] .'</a>';
        } else {
        	echo '最后一篇';
        }
	}

?>
str;

		return $str;
	}

	//针对内容页
	public function _click($attr, $content) {

		$str =<<<str
<?php

	if (!empty(\$id) && !empty(\$tablename)) {


		//开启静态缓存
		if (C('HTML_CACHE_ON') == true) {
			echo '<script type="text/javascript" src="'.U(GROUP_NAME. '/Public/click', array('id' => \$id, 'tn' => \$tablename)).'"></script>';
		}
		else {
			echo getClick(\$id, \$tablename);
		}
		
		
	}

?>
str;
		return $str;
	}
	
	//yyOnline[QQ]
	public function _yyonline($attr, $content) {

		$str =<<<str
<?php
	

?>
<link href="__DATA__/static/js_plugins/yyOnline/default.css" rel="stylesheet" type="text/css" />
<div id="YYOnlineView" class="yy_online_view">
	<div class="top_b"></div>
	<div class="body">
	<dl>
		<dd class="title">QQ在线客服</dd>
		<dd>
			<span class="ico_zx">在线咨询</span>
		</dd>
		<dd class="qq">
			<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=307299635&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:307299635:51" alt="点击这里给我发消息" title="点击这里给我发消息"/></a>
		</dd>
		<dd class="qq">
			<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=3072996351&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:3072996351:51" alt="点击这里给我发消息" title="点击这里给我发消息"/></a>
		</dd>
	</dl>
	<dl>
		<dd class="title bborder">电话咨询</dd>
		<dd>
			<span class="ico_tel">400-000-000</span>
		</dd>
		<dd class="msg noborder">
				<a href="{:U('Guestbook/index')}">给我们留言</a>
		</dd>
	</dl>
	</div>
</div>
<script src="__DATA__/static/js_plugins/yyOnline/scrollx.js" type="text/javascript" language="JavaScript"></script>
<script type="text/javascript">
if(document.getElementById("YYOnlineView")){new scrollx({id:"YYOnlineView",l:-1,t:1,f:1,m:-1});}
</script>

str;
		return $str;
	}


	public function _sitename($attr, $content) {
		return C('cfg_webname');
	}

	public function _sitetitle($attr, $content) {
		return C('cfg_webtitle');
	}

	public function _siteurl($attr, $content) {
		return C('cfg_weburl');
	}

	public function _sitekeywords($attr, $content) {
		return C('cfg_keywords');
	}

	public function _sitedescription($attr, $content) {
		return C('cfg_description');
	}
	public function _address($attr, $content) {
		return C('cfg_address');
	}

	public function _phone($attr, $content) {
		return C('cfg_phone');
	}
	public function _qq($attr, $content) {
		return C('cfg_qq');
	}
	public function _email($attr, $content) {
		return C('cfg_email');
	}
	
	public function _qqauto($attr, $content) {
		return C('cfg_qq_auto');
	}
	public function _qqmore($attr, $content) {
		return C('cfg_qq_more');
	} 
	public function _wwmore($attr, $content) {
		return C('cfg_ww_more');
	} 
	public function _qqphone($attr, $content) {
		return C('cfg_qq_phone');
	} 
	public function _qqguestbook($attr, $content) {
		return C('cfg_qq_guestbook');
	}  
	public function _qqlr($attr, $content) {
		return C('cfg_qq_lr');
	}  

	public function _adauto($attr, $content) {
		return C('cfg_ad_auto');
	} 
	public function _adnum($attr, $content) {
		return C('cfg_ad_num');
	} 
	public function _adwidth($attr, $content) {
		return C('cfg_ad_width');
	} 
	public function _adheight($attr, $content) {
		return C('cfg_ad_height');
	} 
	public function _adtarget($attr, $content) {
		return C('cfg_ad_target');
	}   
	
	public function _copyright($attr, $content) {
		return C('cfg_powerby');
	}
	public function _swturl($attr, $content) {
		return C('cfg_swturl');
	}

	public function _searchurl($attr, $content) {
		return U('Search/index');
	}


	public function _gbookurl($attr, $content) {
		return U('Guestbook/index');
	}

	public function _gbookaddurl($attr, $content) {
		return U('Guestbook/add');
	}
	
	public function _vcodeurl($attr, $content) {
		return U('Public/verify', '');
	}
	
	public function _countcode($attr, $content) {
		return C('cfg_countcode');
	}
	
	public function _beian($attr, $content) {
		return C('cfg_beian');
	}
	public function _cdn($attr, $content) {
		return C('cfg_cdn');
	}
	
	
	public function _sharecode($attr, $content) {
		return C('cfg_sharecode');
	}

	public function _mobileauto($attr, $content) {
		$attr = !empty($attr)? $this->parseXmlAttr($attr, 'mobileauto') : null;
		$flag = empty($attr['flag']) ? 0 : intval($attr['flag']);

		$str =<<<str
<?php
		\$_flag = $flag;
		switch (\$_flag) {
			case 0:
				if (C('cfg_mobile_auto') == 1) {
					//开启静态缓存
					if (C('HTML_CACHE_ON') == true) {
						echo '<script type="text/javascript" src="__DATA__/static/js/mobile_auto.js"></script>';
					}
					else {
						goMobile();
					}
				}
				break;
			case 1:
				goMobile();
				break;
			case 2:
				if (C('cfg_mobile_auto') == 1) {
					echo '<script type="text/javascript" src="__DATA__/static/js/mobile_auto.js"></script>';					
				}
				break;
			
			default:
				break;
		}
	

?>
str;

		return $str;
	}


}


?>