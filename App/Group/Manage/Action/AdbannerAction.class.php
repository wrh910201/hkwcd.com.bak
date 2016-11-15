<?php

class AdbannerAction extends CommonAction {
	
	public function index() {
					
		//分页
		import('ORG.Util.Page');
		$count = M('adbanner')->count();

		$page = new Page($count, 10);
		$limit = $page->firstRow. ',' .$page->listRows;
		$list = M('adbanner')->order('sort')->limit($limit)->select();

		$this->page = $page->show();
		$this->vlist = $list;
		$this->type = '形象图列表';

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
		if (empty($name) || empty($url)|| empty($pic)) {
			$this->error('形象图名称或形象图地址或形象图图片不能为空');
		}

		$data = array(
			'name'		=> $name,
			'url'		=> $url,
			'logo'		=> $pic,
			'description' => I('description', ''),
			'ischeck'	=> I('ischeck', 0, 'intval'),
			'sort'		=> I('sort', 0, 'intval'),
			'posttime'	=> time(),

		);

		if($id = M('adbanner')->add($data)) { 
			$this->success('添加成功',U(GROUP_NAME. '/Adbanner/index'));
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
		$id = I('id', 0, 'intval');
		if (empty($name) || empty($url)|| empty($pic)) {
			$this->error('形象图名称或形象图地址或形象图图片不能为空');
		}
		

		if (false !== M('adbanner')->save($_POST)) { 
			$this->success('修改成功', U(GROUP_NAME. '/Adbanner/index'));
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
		
		if (M('adbanner')->delete($id)) {			 
			$this->success('彻底删除成功', U(GROUP_NAME. '/Adbanner/index'));
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

		if (M('adbanner')->where($where)->delete()) { 
			$this->success('彻底删除成功', U(GROUP_NAME. '/Adbanner/index'));
		}else {
			$this->error('彻底删除失败');
		}
	}




}



?>