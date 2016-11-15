<?php

class CountryAction extends Action{
	
	public function index(){
		$pid = I('pid', 0,'intval');
		
		$keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字	
	
		if (!empty($keyword)) {
			$where = array('pid' => $pid,'types'=>0,'name'=>array('like','%'.$keyword.'%')); 
		}else{
			 $where =array('pid' => $pid,'types'=>0);
		}

		//分页
		import('ORG.Util.Page');
		$count = M('country')->where($where)->count();

		$page = new Page($count, 20);
		$limit = $page->firstRow. ',' .$page->listRows;
		$list = M('country')->where($where)->order('sort,id')->limit($limit)->select();

        $region_list = M('Region')->select();
        $this->region_list = $region_list;

		$this->page = $page->show();
		$this->vlist = $list;
		$this->pid = $pid;
		$this->type = '地区列表';
        $this->keyword = $keyword;
		$this->display();
	}
	
	public function indexzip(){
		$pid = I('pid', 0,'intval'); 
		//分页
		import('ORG.Util.Page');
		$count = M('country')->where(array('pid' => $pid,'types'=>1))->count();

		$page = new Page($count, 20);
		$limit = $page->firstRow. ',' .$page->listRows;
		$list = M('country')->where(array('pid' => $pid,'types'=>1))->order('sort,id')->limit($limit)->select();

		$this->page = $page->show();
		$this->vlist = $list;
		$this->pid = $pid;
		$this->type = '邮编列表';

		$this->display();
	}

	//添加
	public function add() {
		//当前控制器名称		 
		$id = I('id', 0, 'intval');
		$pid = I('pid', 0, 'intval');
		$actionName = strtolower($this->getActionName());
		 
		if ($pid) {
			$pinfo = M($actionName)->find($pid);
			$this->pname = $pinfo['name']; 
		}else {
			$this->pname = '顶级';
		}
		 
		if (IS_POST) {
			$this->addHandle();
			exit();
		}
		cache('add_country_referer', $_SERVER['HTTP_REFERER']);
		$this->pid = $pid;
		$this->type = '添加内容';
		$this->display();
	}

	//
	public function addHandle() {
		$pid = I('pid', 0, 'intval');		  
		$types =0;
		$data = array(
			'name'		=> I('name', '', 'trim'),
			'ename'		=> I('ename', '', 'trim'),
			'pid'		=> I('pid', 0, 'intval'), 
			'sort'		=> I('sort', 0, 'intval'), 
			'types'		=> $types,
			);
		 
		if (empty($data['name'])) {
			$this->error('名称不能为空');
		}  
		 
		if($id = M('country')->add($data)) {  
			if($pid>0){
				$this->success('添加成功',U(GROUP_NAME. '/Country/index',array('pid' => $data['pid'])));
			}else{
			    $referer = cache('add_country_referer');
                cache('add_country_referer', null);
//				$this->success('添加成功',U(GROUP_NAME. '/Country/index'));
                $this->success('添加成功', $referer);
			}
		}else {
			$this->error('添加失败');
		}
	}


	//添加
	public function addzip() {
		//当前控制器名称		  
		$pid = I('pid', 0, 'intval');
		$actionName = strtolower($this->getActionName());
		
		if ($pid) {
			$pinfo = M($actionName)->find($pid);
			$this->pname = $pinfo['name']; 
		}else {
			$this->pname = '顶级';
		}
		
		if (IS_POST) {
			$this->addzipHandle();
			exit();
		}
		
		$this->pid = $pid;
		$this->type = '添加邮编';
		$this->display();
	}
	
	//
	public function addzipHandle() {
		$pid = I('pid', 0, 'intval');		  
		$types = 1;		  
		$data = array(
			'name'		=> I('name', '', 'trim'),
			'zip'		=> I('zip', '', 'trim'),
			'types'		=> $types,
			'pid'		=> I('pid', 0, 'intval'), 
			'sort'		=> I('sort', 0, 'intval'), 

			);
		
		if (empty($data['zip'])) {
			$this->error('邮编不能为空');
		}  
		
		if($id = M('country')->add($data)) {  
			if($pid>0){
				$this->success('添加成功',U(GROUP_NAME. '/Country/indexzip',array('pid' => $data['pid'])));
			}else{
				$this->success('添加成功',U(GROUP_NAME. '/Country/indexzip'));
			}
			
		}else {
			$this->error('添加失败');
		}
	}



	//编辑
	public function editzip() {
		//当前控制器名称
		$id = I('id', 0, 'intval');
		$pid = I('pid', 0, 'intval');
		$actionName = strtolower($this->getActionName());

		if (IS_POST) {
			//M验证
			$data['id'] = I('id',  0, 'intval');
			$data['name'] = I('name', '', 'trim'); 
			$data['zip'] = I('zip', '', 'trim');
			$data['pid'] = I('pid', 0, 'intval');			
			$data['sort'] = I('sort',  0, 'intval');
			$data['types'] = 1;
 
			if (empty($data['zip'])) {
				$this->error('邮编不能为空');
			} 
			if (empty($data['id'])) {
				$this->error('参数错误');
			} 

			if (false !== M('country')->save($data)) {
				$this->success('修改成功',U(GROUP_NAME. '/Country/indexzip', array('pid' => $data['pid'])));
			}else {

				$this->error('修改失败');
			}
			exit();
		}

		$this->vo = M($actionName)->find($id);
		if ($pid) {
			$pinfo = M($actionName)->find($pid);
			$this->pname = $pinfo['name'];
		}else {
			$this->pname = '顶级';
		}
		$this->pid = $pid;
		$this->type = '修改邮编信息';
		$this->display();
	}



	//编辑
	public function edit() {
		//当前控制器名称
		$id = I('id', 0, 'intval');
		$pid = I('pid', 0, 'intval');
		$actionName = strtolower($this->getActionName());

		if (IS_POST) {
			//M验证
			$data['id'] = I('id',  0, 'intval');
			$data['name'] = I('name', '', 'trim'); 
			$data['ename'] = I('ename', '', 'trim');
			$data['pid'] = I('pid', 0, 'intval');			
			$data['sort'] = I('sort',  0, 'intval');

			if (empty($data['name'])) {
				$this->error('名称不能为空');
			} 
			if (empty($data['id'])) {
				$this->error('参数错误');
			}
			$vo = M('country')->where(array('id' => array('neq', $data['id']), 'name' => $data['name']))->find();
			if ($vo) {
				$this->error('名称已经存在，请重新填写');
			}


			if (false !== M('country')->save($data)) {
				$this->success('修改成功',U(GROUP_NAME. '/Country/index', array('pid' => $data['pid'])));
			}else {

				$this->error('修改失败');
			}
			exit();
		}

		$this->vo = M($actionName)->find($id);
		if ($pid) {
			$pinfo = M($actionName)->find($pid);
			$this->pname = $pinfo['name'];
		}else {
			$this->pname = '顶级';
		}
		$this->pid = $pid;
		$this->type = '修改区域信息';
		$this->display();
	}


	//批量更新排序
	public function sort() {
		$pid = intval($_GET['pid']);
		$actionName = strtolower($this->getActionName());
		//exit();
		foreach ($_POST as $k => $v) {
			if ($k == 'key') {
				continue;
			}
			M($actionName)->where(array('id'=>$k))->setField('sort',$v);
			//echo 'id:'.$k.'___v:'.$v.'<br/>';//debug
		}
		$this->redirect(GROUP_NAME. '/Country/index', array('pid' => $pid));
	}

	
	//彻底删除文章
	public function del() {

		$id = I('id',0 , 'intval');
		$pid = I('pid',0 , 'intval');
		$batchFlag = isset($_GET['batchFlag'])? intval($_GET['batchFlag']) : 0;
		//批量删除
		if ($batchFlag) {
			$this->delBatch();
			return;
		}
		
		if (M('country')->delete($id)) { 
			M('country')->where(array('pid' => $id))->delete();
			$this->success('彻底删除成功', U(GROUP_NAME. '/Country/index', array('pid' => $pid)));
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

		if (M('country')->where($where)->delete()) {
			M('country')->where(array('pid' => array('in', $idArr)))->delete(); 
			$this->success('彻底删除成功', U(GROUP_NAME. '/Country/index'));
		}else {
			$this->error('彻底删除失败');
		}
	}


	//彻底删除文章
	public function delzip() {

		$id = I('id',0 , 'intval');
		$pid = isset($_GET['pid'])? intval($_GET['pid']) : 0;
		$batchFlag = isset($_GET['batchFlag'])? intval($_GET['batchFlag']) : 0;
		//批量删除
		if ($batchFlag) { 
			$this->delzipBatch();
			return;
		}
		
		if (M('country')->delete($id)) { 
			M('country')->where(array('pid' => $id))->delete();
			$this->success('彻底删除成功', U(GROUP_NAME. '/Country/indexzip', array('pid' => $pid)));
		}else {
			$this->error('彻底删除失败');
		}
	}


	//批量彻底删除文章
	public function delzipBatch() {
		$pid = isset($_GET['pid'])? intval($_GET['pid']) : 0;
		$idArr = I('key',0 , 'intval');		
		if (!is_array($idArr)) {
			$this->error('请选择要彻底删除的项');
		}
		$where = array('id' => array('in', $idArr));

		if (M('country')->where($where)->delete()) { 
			M('country')->where(array('pid' => array('in', $idArr)))->delete(); 
			$this->success('彻底删除成功', U(GROUP_NAME. '/Country/indexzip', array('pid' => $pid)));
		}else {
			$this->error('彻底删除失败');
		}
	}



	public function createJsOfCountry(){ 
		if ($this->getJsOfCountry()) {
			$this->success('生成js成功',U(GROUP_NAME. '/Country/index', array('pid' => 0)));
		}else {
			$this->error('生成js失败');
		}
	}
	
/*get js of City*/
function getJsOfCountry() {
	 
	$countystr .= "var countrylist = [\n";
	$countystr .= "{name:\" \",to:\" \",to1:\" \"}"; 
	$citystr .= "var citylist = [\n";
	$citystr .= "{name:\" \",to:\" \",to1:\" \"}"; 
	$zipstr .= "var ziplist = [\n";
	$zipstr .= "{name:\" \",to:\" \",to1:\" \"}"; 
	$country = M('country')->where(array('pid' => 0,'types'=>0))->order('sort,id')->select();
	// country 
	foreach ($country as $k => $v) {
		$countystr .= ',{name:"'. $v['name']." ".$v['ename'].'",to:"'. $v['name'].'",to1:"'.$v['ename'].'"}';
		
		$city = M('country')->where(array('pid' => $v['id'],'types'=>0))->order('sort,id')->select(); 
		foreach ($city as $k => $vc) {
			$citystr .= ',{name:"'. $v['name']." ".$vc['name']." ".$vc['ename'].'",to:"'. $vc['name'].'",to1:"'.$vc['ename'].'"}';
			$zip= M('country')->where(array('pid' => $vc['id'],'types'=>1))->order('sort,id')->select(); 
			foreach ($zip as $k => $vz) {
				$zipstr .= ',{name:"'. $v['name']." ".$vc['name']." ".$vz['zip'].'",to:"'. $vz['zip'].'",to1:""}';
			}
		}
		
	}
	$countystr .= " \n];\n";
	$citystr .= " \n];\n";
	$zipstr .= " \n];\n";
	$str = $countystr.$citystr.$zipstr; 

	//echo $str;
	if (file_put_contents('./App/Group/Manage/Tpl/Public/js/localdata.js', $str)) {
		return true;
	} else {
		return false;
	}

}





}


?>