<?php

class GuestbookAction extends Action {
	
	public function index() {
					
		//分页
		import('ORG.Util.Page');
		$count = M('guestbook')->count();

		$page = new Page($count, 10);
		$limit = $page->firstRow. ',' .$page->listRows;
		$list = M('guestbook')->order('id DESC')->limit($limit)->select();

		$this->page = $page->show();
		$this->vlist = $list;

		$this->title = 'Feedback';
		$this->display();
	}
	//添加

	public function add() {

		if (!IS_POST) {
			exit();
		}
		$content = I('content', '');
		$data =  I('post.');		
		$verify = I('vcode','','md5');
		if (C('cfg_verify_guestbook') == 1 && $_SESSION['verify'] != $verify) {
			$this->error('Incorrect verification code');
		}

		if (empty($data['username'])) {
			$this->error('Name can not be empty!');
		}
		if (empty($data['content'])) {
			$this->error('Message content can not be empty!');
		}
		if (checkBadWord($content)) {
			$this->error('Content contains illegal information, please fill!');
		}

	
		

		$data['posttime'] = time();
		$data['ip'] = get_client_ip();
	
		$db = M('guestbook');


		//添加邮件发送功能
		$subject = "客户{$data['username']}在线留言，联系电话:{$data['tel']}";
		$message = <<<str
<p>客户{$data['username']}在线留言</p>
<p>联系电话:{$data['tel']}  QQ号码:{$data['qq']}  Email:{$data['email']}</p>
<p>留言内容：{$data['content']} </p>
<p>此邮件由系统发送，请勿直接回复。</p>
str;
		if (C('cfg_feedback_email')) {
			SendMail(C('cfg_email'), $subject , $message);
		}


		if($id = $db->add($data)) 
		{
			$this->success('Feedback success',U(GROUP_NAME. '/Guestbook/index'));
		}else {
			$this->error('Message failed, please try again！');
		}
	}
	


	

}



?>