<?php

class ExpressAction extends CommonContentAction {
	
	public function index() {
		
		$keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字	
	
		if (!empty($keyword)) {
			$where['dda'] = array('like','%'.$keyword.'%'); 
		}
		
		//分页
		import('ORG.Util.Page');
		$count = M('express')->where($where)->count();

		$page = new Page($count, 10);
		$limit = $page->firstRow. ',' .$page->listRows;
		$list = M('express')->where($where)->order('id DESC')->limit($limit)->select();

		$this->page = $page->show();
		$this->vlist = $list;
		$this->group = $group;
		$this->keyword = $keyword;
		$this->type = '运单列表';

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
		$this->type = '添加运单资料';
		$this->express = getArrayOfItem('express');
		$this->display();
	}

	//
	public function addHandle() {

		//M验证
		$validate = array(
			array('dda','require','运单号不能为空！'),  
			);

		$auto = array ( 
			array('posttime','time',1,'function'),  
			);
		
		$vo = M('express')->where(array('dda' =>  I('dda', '', 'trim')))->find();
		if ($vo) {
			$this->error('运单号已经存在，请重新填写');
		} 

		$db = M('express');
		if (!$db->auto($auto)->validate($validate)->create()) {
			$this->error($db->getError());
		}


		if($id = $db->add()) {
			$this->success('添加成功',U(GROUP_NAME. '/Express/index'));
		}else {
			$this->error('添加失败');
		}
	}
	

	//编辑
	public function edit() {
		//当前控制器名称
		$id = I('id', 0, 'intval');
		$actionName = strtolower($this->getActionName());

		if (IS_POST) {
			$this->editHandle();
			exit();
		}
		
		$this->type = '修改运单资料';
		$this->express = getArrayOfItem('express');
		
		$vo = M($actionName)->find($id); 
		$this->vo = $vo;
		$this->display();
	}

	//
	public function editHandle() {

		//M验证
		$validate = array(
			array('dda','require','运单号不能为空！'),  
			);
		
		$data['id'] = I('id', '', 'intval');
		$data['dda'] = I('dda', '', 'trim');
		$data['leix']  = I('leix', '', 'trim');
		$data['ckh']  = I('ckh', '', 'trim');
		$data['lmdd']  = I('lmdd', '', 'trim');
		$data['gjj']  = I('gjj', '', 'trim');
		$data['qjrq']  = I('qjrq', '', 'trim');
		$data['status']  = I('status', '', 'trim'); 
		
		if (!$data['id']) {
			$this->error('参数错误');
		}
		

		if (false !== M('express')->save($data)) {
			$this->success('修改成功', U(GROUP_NAME. '/Express/index'));
		}else {

			$this->error('修改失败');
		} 
	}

	//彻底删除
	public function del() {

		$id = I('id',0 , 'intval');
		$batchFlag = intval($_GET['batchFlag']); 
		$where = array('fkid' => $id); 
		$child= M('expressdetail')->where($where)->delete(); 
		 
		//批量删除
		if ($batchFlag) {
			$this->delBatch();
			return;
		}
		
		if (M('express')->delete($id)) {
			$this->success('彻底删除成功', U(GROUP_NAME. '/Express/index'));
		}else {
			$this->error('彻底删除失败');
		} 
	} 
	
	

	//批量彻底删除
	public function delBatch() {

		$idArr = I('key',0 , 'intval');		
		if (!is_array($idArr)) {
			$this->error('请选择要彻底删除的项');
		}
		$where = array('id' => array('in', $idArr)); 
		if (M('express')->where($where)->delete()) { 
			$this->success('彻底删除成功', U(GROUP_NAME. '/Express/index'));
		}else {
			$this->error('彻底删除失败');
		}
	}
	



}



?>