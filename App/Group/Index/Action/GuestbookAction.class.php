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

		$this->title = '留言本';
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
			$this->error('验证码不正确');
		}

		if (empty($data['username'])) {
			$this->error('姓名不能为空!');
		}
		if (empty($data['content'])) {
			$this->error('留言内容不能为空!');
		}
		if (checkBadWord($content)) {
			$this->error('留言内容包含非法信息，请认真填写!');
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
			$this->success('在线留言成功',U(GROUP_NAME. '/Guestbook/index'));
		}else {
			$this->error('在线留言失败，请再试！');
		}
	}
	


	

}



?>