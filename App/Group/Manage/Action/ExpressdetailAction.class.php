<?php

class ExpressdetailAction extends CommonContentAction {
	
	public function index() {
		$fkid = I('fkid', 0, 'intval');
		
		$data = M('express')->where(array('id'=> $fkid ))->find();
		if(!$data)
		{
			$this->error('参数不正确');
		}
		
		//分页
		import('ORG.Util.Page');
		$count = M('expressdetail')->where(array('fkid'=> $fkid ))->count();

		$page = new Page($count, 10);
		$limit = $page->firstRow. ',' .$page->listRows;
		$list = M('expressdetail')->where(array('fkid'=> $fkid ))->order('id DESC')->limit($limit)->select();

		$this->page = $page->show();
		$this->vlist = $list;
		$this->data = $data; 
		$this->type = '详细运单列表';
		$this->fkid = $fkid; 
		$this->display();
		
	}

	//添加
	public function add() {
		$fkid = I('fkid', 0, 'intval');
		//当前控制器名称		
		$actionName = strtolower($this->getActionName());		
		if (IS_POST) {
			$this->addHandle();
			exit();
		}
		$this->type = '添加运单详细资料';
		$this->progress = getArrayOfItem('progress');
		$this->remark = getArrayOfItem('remark');
		$this->fkid = $fkid; 
		$this->display();
	}

	//
	public function addHandle() {

		//M验证 
		$auto = array ( 
			array('posttime','time',1,'function'),  
			); 

		$db = M('expressdetail');
		if (!$db->auto($auto)->validate($validate)->create()) {
			$this->error($db->getError());
		}


		if($id = $db->add()) {
			$this->success('添加成功',U(GROUP_NAME. '/Expressdetail/index', array('fkid'=>  I('fkid', 0, 'intval'))));
		}else {
			$this->error('添加失败');
		}
	}
	

	//编辑
	public function edit() {
		//当前控制器名称
		$id = I('id', 0, 'intval');
		$fkid = I('fkid', 0, 'intval');
		$actionName = strtolower($this->getActionName());

		if (IS_POST) {
			$this->editHandle();
			exit();
		}
		
		$this->type = '修改运单详细资料'; 
		
		$vo = M($actionName)->find($id); 
		$this->vo = $vo;
		$this->progress = getArrayOfItem('progress'); 
		$this->remark = getArrayOfItem('remark');
		$this->fkid = $fkid; 
		$this->display();
	}

	//
	public function editHandle() { 
		
		$data['id'] = I('id', '', 'intval');
		$data['sj'] = I('sj', '', 'trim');
		$data['gj']  = I('gj', '', 'trim');
		$data['chs']  = I('chs', '', 'trim');
		$data['hwz']  = I('hwz', '', 'trim');
		$data['fkid']  = I('fkid', '', 'trim'); 
		$data['info']  = I('info', '', 'trim'); 
		if (!$data['id']) {
			$this->error('参数错误');
		}
		

		if (false !== M('expressdetail')->save($data)) {
			$this->success('修改成功', U(GROUP_NAME. '/Expressdetail/index', array('fkid'=>  I('fkid', 0, 'intval'))));
		}else {

			$this->error('修改失败');
		} 
	}

	//彻底删除
	public function del() {

		$id = I('id',0 , 'intval'); 
		
		if (M('expressdetail')->delete($id)) {
			$this->success('彻底删除成功', U(GROUP_NAME. '/Expressdetail/index', array('fkid'=>  I('fkid', 0, 'intval'))));
		}else {
			$this->error('彻底删除失败');
		} 
	}  

}



?>