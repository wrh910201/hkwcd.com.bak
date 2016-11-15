<?php

class ExpressAction extends Action {
	
	public function index() { 
	 
		$dda = I('dda', '', 'trim');
		if (IS_POST) {
			$data = M('express')->where(array('dda'=> $dda ))->find(); 
			if($data['status']=='0'){  
				$child= M('expressdetail')->where(array('fkid' => $data['id']))->order('id DESC')->select();  
			}
			$this->data = $data; 
			$this->vlist = $child;  
		}
		$this->dda = $dda; 
		$this->display();
	}
}


?>