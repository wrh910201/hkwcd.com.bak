<?php

class CategoryAction extends CommonAction {
	
	//分类列表
	public function index() {

		//CategoryView 视图模型
		$cate = D('CategoryView')->order('category.sort')->select();
		//$cate = getCategory();
		import('Class.Category', APP_PATH);
		$this->cate = Category::unlimitedForLevel($cate, '&nbsp;&nbsp;&nbsp;&nbsp;', 0);
		$this->display();
	}

	//添加分类
	public function add() {
	
		if (IS_POST) {
			$this->addHandle();
			exit();
		}
		$this->pid = I('pid', 0, 'intval');
		$cate = M('category')->order('sort')->select();
		import('Class.Category', APP_PATH);
		$this->cate = Category::unlimitedForLevel($cate, '---',0);
		$this->mlist = M('model')->where(array('status' => 1))->order('sort')->select();
		$this->styleListList = getFileFolderList(APP_PATH . C('APP_GROUP_PATH') . '/Index/Tpl/' .C('cfg_themestyle') , 2, 'List_*');
		$this->styleShowList = getFileFolderList(APP_PATH . C('APP_GROUP_PATH') . '/Index/Tpl/' .C('cfg_themestyle') , 2, 'Show_*');
		$this->display();
	}

	//添加分类处理

	public function addHandle() {

		$data = I('post.', '');
		
		$data['name'] = trim($data['name']);
		$data['ename'] = trim($data['ename']);		
		$data['type'] = empty($data['type'])? 0 : intval($data['type']);

		if (isset($data['type']) && $data['type'] ==1 ) {
			$data['modelid'] = 0;
		}
		//M验证
		if (empty($data['name'])) {
			$this->error('栏目名称不能为空！');
		}


		if (empty($data['ename'])) {
			$data['ename'] = get_pinyin(iconv('utf-8','gb2312//ignore',$data['name']),0);
		}elseif ($data['type'] == 0) {
			if (!ctype_alnum($data['ename'])) {
				$this->error('别名只能由字母和数字组成，不能包含特殊字符！');
			}
		}	
	

		if (M('category')->add($data)) {
			getCategory(0,1);//清除栏目缓存
			getCategory(1,1);//清除栏目缓存
			getCategory(2,1);//清除栏目缓存
			$this->success('添加栏目成功<script type="text/javascript" language="javascript">window.parent.get_cate();</script>',U(GROUP_NAME. '/Category/index'));
		}else {
			$this->error('添加栏目失败');
		}
		
	}


	//修改分类
	public function edit() {

		if (IS_POST) {
			$this->editHandle();
			exit();
		}
		$id = I('id', 0, 'intval');
		$data = M('category')->find($id);
		if (!$data) {
			$this->error('记录不存在');
		}
		$this->data = $data;
		$cate = M('category')->order('sort')->select();
		import('Class.Category', APP_PATH);
		$this->cate = Category::unlimitedForLevel($cate, '---',0);
		$this->mlist = M('model')->where(array('status' => 1))->order('sort')->select();		
		$this->styleListList = getFileFolderList(APP_PATH . C('APP_GROUP_PATH') . '/Index/Tpl/' .C('cfg_themestyle') , 2, 'List_*');
		$this->styleShowList = getFileFolderList(APP_PATH . C('APP_GROUP_PATH') . '/Index/Tpl/' .C('cfg_themestyle') , 2, 'Show_*');
	
		$this->display();
	}



	//修改分类处理

	public function editHandle() {

		$data = I('post.', '');				
		$id = $data['id'] = intval($data['id']);
		$pid = $data['pid'];
		$data['name'] = trim($data['name']);
		$data['ename'] = trim($data['ename']);		
		$data['type'] = empty($data['type'])? 0 : intval($data['type']);

		if (isset($data['type']) && $data['type'] ==1 ) {
			$data['modelid'] = 0;
		}

		if ($id == $pid) {
			$this->error('失败！不能设置自己为自己的子栏目，请重新选择父级栏目');
		}
		//M验证
		if (empty($data['name'])) {
			$this->error('栏目名称不能为空！');
		}

		if (empty($data['ename'])) {
			$data['ename'] = get_pinyin(iconv('utf-8','gb2312//ignore',$data['name']),0);
		}elseif ($data['type'] == 0) {
			if (!ctype_alnum($data['ename'])) {
				$this->error('别名只能由字母和数字组成，不能包含特殊字符！');
			}
		}

	

		/*
		if (M('category')->where(array('name' => $data['name'], 'id' => array('neq' , $id)))->find()) {
			$this->error('栏目名称已经存在！');
		}
		*/

		

		if (false !== M('category')->save($data)) {
			getCategory(0,1);//清除栏目缓存
			getCategory(1,1);
			getCategory(2,1);
			$this->success('修改栏目成功<script type="text/javascript" language="javascript">window.parent.get_cate();</script>',U(GROUP_NAME. '/Category/index'));
		}else {
			$this->error('修改栏目失败');
		}
		
	}

	//批量更新排序
	public function sort() {
	
		foreach ($_POST as $k => $v) {
			if ($k == 'key') {
				continue;
			}
			M('category')->where(array('id'=>$k))->setField('sort',$v);

			//echo 'id:'.$k.'___v:'.$v.'<br/>';//debug			
		}
		$this->redirect(GROUP_NAME. '/Category/index');
	}


	//修改分类处理

	public function del() {

		$id = I('id', 0, 'intval');

		//查询是否有子类
		$childCate = M('category')->where(array('pid' => $id))->select();
		if ($childCate) {
			$this->error('删除失败：请先删除本栏目下的子栏目');
		}

		if (M('category')->delete($id)) {
			//更新栏目缓存
			getCategory(0,1);
			getCategory(1,1);
			getCategory(2,1);
			$this->success('删除栏目成功<script type="text/javascript" language="javascript">window.parent.get_cate();</script>',U(GROUP_NAME. '/Category/index'));
		}else {
			$this->error('删除栏目失败');
		}		
	}


}




?>