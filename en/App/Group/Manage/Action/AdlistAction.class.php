<?php

class AdlistAction extends CommonAction {
	
	public function index() {
					
		//分页
		import('ORG.Util.Page');
		$count = M('adlist')->count();

		$page = new Page($count, 10);
		$limit = $page->firstRow. ',' .$page->listRows;
		$list = M('adlist')->order('sort')->limit($limit)->select();

		$this->page = $page->show();
		$this->vlist = $list;
		$this->type = '广告图列表';

		$this->display();
	}
	//添加
	public function add() {
		//当前控制器名称		
		$actionName = strtolower($this->getActionName());
		if (IS_POST) {
			$this->addHandle();
			exit();
		}
		$this->display();
	}

	//
	public function addHandle() {

		$name = I('name', '', 'trim');
		$url = I('url', '', 'trim');
		$pic = I('logo', '', 'trim');
		$width = I('width', '', 'trim');
		$height = I('height', '', 'trim');
		if (empty($name) || empty($url)|| empty($pic)|| empty($width)|| empty($height)) {
			$this->error('广告名称或广告地址或广告图片不能为空或宽高度不能为空');
		}

		$data = array(
			'name'		=> $name,
			'url'		=> $url,
			'logo'		=> $pic,
			'width'		=> $width,
			'height'	=> $height,
			'description' => I('description', ''),
			'ischeck'	=> I('ischeck', 0, 'intval'),
			'sort'		=> I('sort', 0, 'intval'),
			'types'	=> I('types', 1, 'intval'),
			'target'	=> I('target', 1, 'intval'),
			'posttime'	=> time(),

		);

		if($id = M('adlist')->add($data)) {  
			$this->success('添加成功',U(GROUP_NAME. '/Adlist/index'));
		}else {
			$this->error('添加失败');
		}
	}

	//编辑文章
	public function edit() {
		//当前控制器名称
		$id = I('id', 0, 'intval');
		$actionName = strtolower($this->getActionName());
		if (IS_POST) {
			$this->editHandle();
			exit();
		}
		$this->vo = M($actionName)->find($id);
		$this->display();
	}


	//修改文章处理
	public function editHandle() {

		$name = I('name', '', 'trim');
		$url = I('url', '', 'trim');
		$pic = I('logo', '', 'trim');
		$width = I('width', '', 'trim');
		$height = I('height', '', 'trim');
		if (empty($name) || empty($url)|| empty($pic)|| empty($width)|| empty($height)) {
			$this->error('广告名称或广告地址或广告图片不能为空或宽高度不能为空');
		}
		

		if (false !== M('adlist')->save($_POST)) { 
			$this->success('修改成功', U(GROUP_NAME. '/Adlist/index'));
		}else {

			$this->error('修改失败');
		}
		
	}



	//彻底删除文章
	public function del() {

		$id = I('id',0 , 'intval');
		$batchFlag = isset($_GET['batchFlag'])? intval($_GET['batchFlag']) : 0;
		//批量删除
		if ($batchFlag) {
			$this->delBatch();
			return;
		}
		
		if (M('adlist')->delete($id)) { 
			$this->success('彻底删除成功', U(GROUP_NAME. '/Adlist/index'));
		}else {
			$this->error('彻底删除失败');
		}
	}


	//批量彻底删除文章
	public function delBatch() {

		$idArr = I('key',0 , 'intval');		
		if (!is_array($idArr)) {
			$this->error('请选择要彻底删除的项');
		}
		$where = array('id' => array('in', $idArr));

		if (M('adlist')->where($where)->delete()) { 
			$this->success('彻底删除成功', U(GROUP_NAME. '/Adlist/index'));
		}else {
			$this->error('彻底删除失败');
		}
	}




}



?>