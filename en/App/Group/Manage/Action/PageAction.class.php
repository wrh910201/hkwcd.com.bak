<?php
//单页
class PageAction extends CommonContentAction {
	
	//编辑
	public function index() {
		//当前控制器名称
		$actionName = strtolower($this->getActionName());
		$pid = I('pid', 0, 'intval');
		$vo = M('category')->find($pid);//直接是编辑
	
		$vo['content'] = htmlspecialchars($vo['content']);//ueditor
		$this->pid = $pid;
		$this->vo = $vo;
		//所有子栏目列表
		import('Class.Category', APP_PATH);
		$cate = getCategory();//全部分类
		$this->subcate = Category::getChilds($cate, $pid);
		$this->poscate = Category::getParents($cate, $pid);

		$this->display();
	}


	//修改文章处理
	public function indexHandle() {

		$id = I('pid', 0, 'intval');
		$pid = I('pid', 0, 'intval');
		$content = I('content', '', 'trim');		
		$description = I('description', '');

		if (!$pid) {
			$this->error('参数错误');
		}

		
		if (empty($description)) {			
			$description = str2sub(strip_tags($content), 200);
		}

		$data = array('id' => $pid, 'description' => $description, 'content' => $content);

		//获取属于分类信息,得到modelid
		import('Class.Category', APP_PATH);			
		$selfCate = Category::getSelf(getCategory(0), $id);//当前栏目信息
		$modelid = $selfCate['modelid'];

		if (false !== M('category')->save($data)) {
	
			M('attachmentindex')->where(array('arcid' => $id, 'modelid' => $modelid))->delete();
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

			$this->success('修改成功', U(GROUP_NAME. '/Page/index', array('pid' => $pid)));
		}else {

			$this->error('修改失败');
		}
		
	}




}



?>