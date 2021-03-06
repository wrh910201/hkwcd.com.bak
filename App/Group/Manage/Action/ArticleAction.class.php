<?php

class ArticleAction extends CommonContentAction {
	
	public function index() {

		$pid = I('pid', 0, 'intval');//类别ID
		$keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字

		//所有子栏目列表
		import('Class.Category', APP_PATH);
		//$cate = D('CategoryView')->order('category.sort')->select();
		$cate = getCategory();
		$this->subcate = Category::getChilds($cate, $pid);
		$this->poscate = Category::getParents($cate, $pid);
		
		
		if ($pid) {
			$idarr = Category::getChildsId($cate, $pid, 1);//所有子类ID
			$where = array('article.status' => 0, 'cid' => array('in', $idarr));
		}else {
			$where = array('article.status' => 0);
		}

		if (!empty($keyword)) {
			$where['article.title'] = array('LIKE', "%{$keyword}%");
		}
		
		//分页
		import('ORG.Util.Page');
		$count = D('ArticleView')->where($where)->count();

		$page = new Page($count, 10);
		$limit = $page->firstRow. ',' .$page->listRows;
		$art = D('ArticleView')->where($where)->order('article.id DESC')->limit($limit)->select();
		$this->pid = $pid;
		$this->keyword = $keyword;
		$this->page = $page->show();
		$this->vlist = $art;
		$this->type = '文章列表';

		$this->display();
	}
	//添加文章
	public function add() {

		
		//当前控制器名称		
		$actionName = strtolower($this->getActionName());
		$this->pid = I('pid', 0, 'intval');

		if (IS_POST) {
			$this->addPost();
			exit();
		}


		//$cate = D('CategoryView')->where(array('type' => 0))->order('category.sort')->select();
		$cate = getCategory(2);
		import('Class.Category', APP_PATH);
		$cate = Category::unlimitedForLevel($cate);
		$this->flagtypelist = getArrayOfItem('flagtype');//文档属性
		$this->cate = Category::getLevelOfModel($cate, $actionName);
		$this->display();
	}

	//
	public function addPost() {

		$pid = I('pid', 0, 'intval');
		$cid = I('cid', 0, 'intval');
		$title = I('title', '', 'htmlspecialchars,rtrim');	
		$flags = I('flags', array(),'intval');
		$jumpurl = I('jumpurl', '');
		$description = I('description', '', 'htmlspecialchars');
		$content = I('content', '', '');
		

		$pic = I('litpic', '', 'htmlspecialchars,trim');

		if (empty($title)) {
			$this->error('标题不能为空');
		}
		if (!$cid) {
			$this->error('请选择栏目');
		}
		$pid = $cid;//转到自己的栏目
		if (empty($description)) {			
			$description = str2sub(strip_tags($content), 120);
		}

		//图片标志
		if (!empty($pic) && !in_array(B_PIC, $flags)) {
			$flags[] = B_PIC;
		}
		$flag = 0;
		foreach ($flags as $v) {
			$flag += $v;
		}

		//获取属于分类信息,得到modelid
		import('Class.Category', APP_PATH);			
		$selfCate = Category::getSelf(getCategory(0), $cid);//当前栏目信息
		$modelid = $selfCate['modelid'];

		$data =array(
			'title' => $title ,
			'shorttitle' => I('shorttitle', '', 'htmlspecialchars,trim'),
			'color' => I('color'),
			'cid'	=> $cid,
			'litpic'	=> $pic,
			'keywords' => I('keywords','','htmlspecialchars,trim'),
			'description' => $description,
			'content' => $content,
			'publishtime' =>strtotime(I('publishtime', '', 'trim')),
			'updatetime' => time(),
			'click' => rand(10,95),
			'status' => 0,
			'commentflag' => I('commentflag', 0,'intval'),
			'flag'	=> $flag,
			'jumpurl' => $jumpurl,
			'aid'	=> $_SESSION[C('USER_AUTH_KEY')]

		);
		

		if($id = M('article')->add($data)) {

			//更新上传附件表
			if (!empty($pic)) {
				//更新3个小时内的.即10800秒
				$pic = preg_replace('/!(\d+)X(\d+)\.jpg$/i', '', $pic);//清除缩略图的!200X200.jpg后缀
				$attid = M('attachment')->where(array('filepath' => $pic))->getField('id');
				if($attid){
					M('attachmentindex')->add(array('attid' => $attid,'arcid' => $id, 'modelid' => $modelid));
				}
				//halt(M('attachment')->getlastsql());
			}


			//内容中的图片
			$img_arr = array();
			$reg = "/<img[^>]*src=\"((.+)\/(.+)\.(jpg|gif|bmp|png))\"/isU";
			preg_match_all($reg, $content, $img_arr, PREG_PATTERN_ORDER);
			// 匹配出来的不重复图片
			$img_arr = array_unique($img_arr[1]);
			if (!empty($img_arr)) {
				$attid = M('attachment')->where(array('filepath' => array('in', $img_arr)))->getField('id', true);
				$dataAtt = array();
				if ($attid) {
					foreach ($attid as $v) {
						$dataAtt[] = array('attid' => $v,'arcid' => $id, 'modelid' => $modelid);
					}
					M('attachmentindex')->addAll($dataAtt);
				}
				
			}			
			//$this->display('/Test:empty');exit();


			//更新静态缓存
			delCacheHtml('List/index_'.$cid, false, 'list:index');	
			delCacheHtml('Index_index', false, 'index:index');

			$this->success('添加文章成功',U(GROUP_NAME. '/Article/index', array('pid' => $pid)));
		}else {
			$this->error('添加文章失败');
		}
	}

	//编辑文章
	public function edit() {
		//当前控制器名称
		$id = I('id', 0, 'intval');
		$actionName = strtolower($this->getActionName());
		$this->pid = I('pid', 0, 'intval');

		if (IS_POST) {
			$this->editPost();
			exit();
		}

		//$cate = D('CategoryView')->where(array('type' => 0))->order('category.sort')->select();
		$cate = getCategory(2);
		import('Class.Category', APP_PATH);
		$cate = Category::unlimitedForLevel($cate);
		$this->cate = Category::getLevelOfModel($cate, $actionName);

		
		$vo = M($actionName)->find($id);
		$vo['content'] = htmlspecialchars($vo['content']);//ueditor
		$this->vo = $vo;		
		$this->flagtypelist = getArrayOfItem('flagtype');//文档属性
		$this->display();
	}


	//修改文章处理
	public function editPost() {

		$data =array(
			'id' => I('id', 0, 'intval'),
			'title' => I('title', '', 'htmlspecialchars,rtrim'),
			'shorttitle' => I('shorttitle', '', 'htmlspecialchars,rtrim'),
			'color' => I('color'),
			'cid'	=> I('cid', 0, 'intval'),
			'litpic'	=> I('litpic', ''),
			'keywords' => I('keywords', '', 'htmlspecialchars,trim'),
			'description' =>  I('description', ''),
			'publishtime' =>strtotime(I('publishtime', '', 'trim')),
			'content' => I('content', '', ''),
			'updatetime' => time(),
			'commentflag' => I('commentflag', 0,'intval'),
			'jumpurl' => I('jumpurl', ''),

		);
		$id = $data['id'];			
		$pid = I('pid', 0, 'intval');
		$flags = I('flags', array(),'intval');
		$pic = $data['litpic'];

		if (empty($data['title'])) {
			$this->error('标题不能为空');
		}
		if (!$data['cid']) {
			$this->error('请选择栏目');
		}		
		$pid = $data['cid'];//转到自己的栏目

		if (empty($data['description'])) {			
			$data['description'] = str2sub(strip_tags($data['content']), 120);
		}


		//图片标志
		if (!empty($pic) && !in_array(B_PIC, $flags)) {
			$flags[] = B_PIC;
		}
		$data['flag'] = 0;
		foreach ($flags as $v) {
			$data['flag'] += $v;
		}




		//获取属于分类信息,得到modelid
		import('Class.Category', APP_PATH);			
		$selfCate = Category::getSelf(getCategory(0), $data['cid']);//当前栏目信息
		$modelid = $selfCate['modelid'];


	
		if (false !== M('article')->save($data)) {
			//del
			M('attachmentindex')->where(array('arcid' => $id, 'modelid' => $modelid))->delete();
			
			//更新上传附件表
			if (!empty($pic)) {

				//$pic = preg_replace('/_(s|m)\.(jpg|gif|bmp|png)$/i', '.$2', $pic);//清除缩略图的_m,_s后缀
				$pic = preg_replace('/!(\d+)X(\d+)\.jpg$/i', '', $pic);//清除缩略图的!200X200.jpg后缀
				$attid = M('attachment')->where(array('filepath' => $pic))->getField('id');
				if($attid){
					M('attachmentindex')->add(array('attid' => $attid,'arcid' => $id, 'modelid' => $modelid));
				}
				//hetlastsql());
			}


			//内容中的图片
			$img_arr = array();
			$reg = "/<img[^>]*src=\"((.+)\/(.+)\.(jpg|gif|bmp|png))\"/isU";
			preg_match_all($reg, $data['content'], $img_arr, PREG_PATTERN_ORDER);
			// 匹配出来的不重复图片
			$img_arr = array_unique($img_arr[1]);
			if (!empty($img_arr)) {
				$attid = M('attachment')->where(array('filepath' => array('in', $img_arr)))->getField('id', true);
				$dataAtt = array();
				if ($attid) {
					foreach ($attid as $v) {
						$dataAtt[] = array('attid' => $v,'arcid' => $id, 'modelid' => $modelid);
					}
					M('attachmentindex')->addAll($dataAtt);
				}
				
			}	

			//更新静态缓存
			delCacheHtml('List/index_'.$data['cid'].'_', false, 'list:index');
			delCacheHtml('List/index_'.$selfCate['ename'], false, 'list:index');//还有只有名称
			delCacheHtml('Show/index_*_'. $id, false, 'show:index');//不太精确，会删除其他模块同id文档	

			$this->success('修改成功', U(GROUP_NAME. '/Article/index', array('pid' => $pid)));
		}else {

			$this->error('修改失败');
		}
		
	}


	//回收站文章列表
	public function trach() {
		import('ORG.Util.Page');
		$where = array('article.status' => 1);
		$count = D('ArticleView')->where($where)->count();

		$page = new Page($count, 10);
		$limit = $page->firstRow. ',' .$page->listRows;
		$art = D('ArticleView')->where($where)->limit($limit)->select();

		$this->pid = I('pid', 0, 'intval');
		$this->page = $page->show();
		$this->vlist = $art;		
		$this->type = '文章回收站';
		$this->subcate = '';
		$this->display('index');
	}

	//删除文章到回收站
	public function del() {

		$id = I('id',0 , 'intval');
		$batchFlag = I('get.batchFlag', 0, 'intval');
		//批量删除
		if ($batchFlag) {
			$this->delBatch();
			return;
		}

		$pid = I('pid',0 , 'intval');//单纯的GET没问题
		if (false !== M('article')->where(array('id' => $id))->setField('status', 1)) {

			delCacheHtml('Show/index_*_'. $id.'.', false, 'show:index');				
			$this->success('删除成功', U(GROUP_NAME. '/Article/index', array('pid' => $pid)));
			
		}else {
			$this->error('删除失败');
		}
	}

	//批量删除到回收站
	public function delBatch() {

		$idArr = I('key',0 , 'intval');
		$pid = I('get.pid',0 , 'intval');

		if (!is_array($idArr)) {
			$this->error('请选择要删除的项');
		}


		if (false !== M('article')->where(array('id' => array('in', $idArr)))->setField('status', 1)) {
			
			//更新静态缓存
			foreach ($idArr as $v) {
				delCacheHtml('Show/index_*_'. $v.'.', false, 'show:index');	
			}
			//. M('article')->getlastsql();
			$this->success('批量删除成功', U(GROUP_NAME. '/Article/index', array('pid' => $pid)));
			
		}else {
			$this->error('批量删除文失败');
		}
	}

	//还原文章
	public function restore() {
		
		$id = I('id',0 , 'intval');
		$batchFlag = I('get.batchFlag', 0, 'intval');
		//批量删除
		if ($batchFlag) {
			$this->restoreBatch();
			return;
		}

		$pid = I('get.pid', 0, 'intval');

		if (false !== M('article')->where(array('id' => $id))->setField('status', 0)) {
			
			$this->success('还原成功', U(GROUP_NAME. '/Article/trach', array('pid' => $pid)));
			
		}else {
			$this->error('还原失败');
		}
	}

	//批量还原文章
	public function restoreBatch() {
		
		$idArr = I('key',0 , 'intval');
		$pid = I('get.pid', 0, 'intval'); 
		if (!is_array($idArr)) {
			$this->error('请选择要还原的项');
		}

		if (false !== M('article')->where(array('id' => array('in', $idArr)))->setField('status', 0)) {
			
			$this->success('还原成功', U(GROUP_NAME. '/Article/trach', array('pid' => $pid)));
			
		}else {
			$this->error('还原失败');
		}
	}

	//彻底删除文章
	public function clear() {

		$id = I('id',0 , 'intval');
		$batchFlag = I('get.batchFlag', 0, 'intval');
		//批量删除
		if ($batchFlag) {
			$this->clearBatch();
			return;
		}

		$pid = I('get.pid', 0, 'intval');
		$modelid = D('ArticleView')->where(array('id' => $id))->getField('modelid');

		if (M('article')->delete($id)) {
			// delete picture index
			if ($modelid) {
				M('attachmentindex')->where(array('arcid' => $id , 'modelid' => $modelid ))->delete();//test
			}
			$this->success('彻底删除成功', U(GROUP_NAME. '/Article/trach', array('pid' => $pid)));
		}else {
			$this->error('彻底删除失败');
		}
	}


	//批量彻底删除文章
	public function clearBatch() {

		$idArr = I('key',0 , 'intval');		
		$pid = I('get.pid', 0, 'intval');
		if (!is_array($idArr)) {
			$this->error('请选择要彻底删除的项');
		}
		$where = array('id' => array('in', $idArr));
		$modelid = D('ArticleView')->where(array('id' => $idArr[0]))->getField('modelid');//

		if (M('article')->where($where)->delete()) {
			// delete picture index
			if ($modelid) {
				M('attachmentindex')->where(array('arcid' => array('in', $idArr) , 'modelid' => $modelid ))->delete();
			}
			$this->success('彻底删除成功', U(GROUP_NAME. '/Article/trach', array('pid' => $pid)));
		}else {
			$this->error('彻底删除失败');
		}
	}

	
}



?>