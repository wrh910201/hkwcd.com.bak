<?php

class ExpressAction extends Action {
	 
	
	public function zh() {
		$this->common(); 
		$this->display();
	}
	
	public function en() {
		$this->common(); 
		$this->display();
	}
	
	public function common() {
		$dda = I('dda', '', 'trim');
		$data = M('express')->where(array('dda'=> $dda ))->find(); 
		if($data['status']=='0'){  
			$child= M('expressdetail')->where(array('fkid' => $data['id']))->order('id DESC')->select();  
		}
		$this->data = $data; 
		$this->vlist = $child;  
	}
}


?>