<?php

//自定义标签库
class TagLibOther extends TagLib {
	

	protected $tags = array(
		
		'mprev'	=> array(
			'attr'	=> 'titlelen',//attr 属性列表
			'close' => 0
			),
		'mnext'	=> array(			
			'attr'	=> 'titlelen',//attr 属性列表
			'close' => 0
			), 

		); 


	public function _mprev($attr, $content) {
		$attr = !empty($attr)? $this->parseXmlAttr($attr, 'mprev') : null;
		$titlelen = empty($attr['titlelen']) ? 0 : intval($attr['titlelen']);
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
			echo '<a href="'. \$_vo['url'] .'"  title="'. \$_vo['title']  .'" class="prevbtn" ></a>';
        } else {
        	echo '第一篇';
        }
	}

?>
str;

		return $str;
	}

	public function _mnext($attr, $content) {
		$attr = !empty($attr)? $this->parseXmlAttr($attr, 'mnext') : null;
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
			echo '<a href="'. \$_vo['url'] .'"  title="'. \$_vo['title']  .'" class="nextbtn" ></a>';
        } else {
        	echo '最后一篇';
        }
	}

?>
str;

		return $str;
	}
 


}


?>