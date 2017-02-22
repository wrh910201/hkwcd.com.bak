<?php

class PublicAction extends Action {
	
	public function index() {

	}
	 


	public function login() {
		$furl = $_SERVER['HTTP_REFERER'];
		if (IS_POST) {
			$this->loginPost();
			exit();
		}
		$this->furl = $furl;
		$this->title = '用户登录';
		$this->display();
	}


	public function loginPost() {

		if (!IS_POST) exit();

		$furl = I('furl', '','htmlspecialchars,trim');
		if (empty($furl) || strpos($furl, 'register') || strpos($furl, 'login') || strpos($furl, 'logout') || strpos($furl, 'activate') || strpos($furl, 'sendActivate')) {
			$furl = U(GROUP_NAME. '/Member/index');
	
		}

		$email = I('email','','htmlspecialchars,trim');
		$password = I('password','');
		
		$verify = I('vcode','','md5');
		if (C('cfg_verify_login') == 1 && $_SESSION['verify'] != $verify) {
			$this->error('验证码不正确');
		}

		if ($email == '') {
			$this->error('请输入帐号！', '', array('input'=>'email'));//支持ajax,$this->error(info,url,array);
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->error('账号为邮箱地址，格式不正确！', '', array('input'=>'email'));//支持ajax,$this->error(info,url,array);
		}

		if (strlen($password)<4 || strlen($password)>20) {
			$this->error('密码必须是4-20位的字符！', '', array('input'=>'password'));
		}

	
		$user = M('member')->where(array('email' => $email))->find();

		if (!$user || ($user['password'] != get_password($password, $user['encrypt']))) {
			$this->error('账号或密码错误', '', array('input'=>'password'));
		}

		if ($user['islock']) {
			$this->error('用户被锁定！', '', array('input'=>''));
		}
		//更新数据库的参数
		$data = array('id' => $user['id'] ,//保存时会自动为此ID的更新
				'logintime' => time(),
				'loginip' => get_client_ip(),
				'loginnum' => $user['loginnum']+1,

		);
		//更新数据库
		M('member')->save($data);

		//保存Session
		//session(C('USER_AUTH_KEY'), $user['id']);
		//保存到cookie
		set_cookie( array('name' => 'uid', 'value' => $user['id'] ));
		set_cookie( array('name' => 'email', 'value' => $user['email'] ));
		set_cookie( array('name' => 'nickname', 'value' => $user['nickname'] ));
		set_cookie( array('name' => 'logintime', 'value' => date('Y-m-d H:i:s', $user['logintime'])));
		set_cookie( array('name' => 'loginip', 'value' => $user['loginip']));
		set_cookie( array('name' => 'status', 'value' => $user['status']));//激活状态
		set_cookie( array('name' => 'verifytime', 'value' => time()));//激活状态


		//跳转
		//$this->redirect(GROUP_NAME.'/Member/index');
		//redirect(__GROUP__);
		$this->success('登录成功', $furl , array('input'=>''));
	}

		//退出
	public function logout() {

		$furl = $_SERVER['HTTP_REFERER'];
	
		if (empty($furl) || strpos($furl, 'register') || strpos($furl, 'login') || strpos($furl, 'activate') || strpos($furl, 'sendActivate')) {
			$furl = U(GROUP_NAME. '/Public/login');
	
		}

		//session_unset();
		//session_destroy();


		del_cookie(array('name' => 'uid'));
		del_cookie(array('name' => 'email'));
		del_cookie(array('name' => 'nickname'));
		del_cookie(array('name' => 'logintime'));
		del_cookie(array('name' => 'loginip'));
		del_cookie(array('name' => 'status'));


		//$this->redirect(GROUP_NAME.'/Public/login');
		$this->success('安全退出', $furl);
	}



		//自动登录后，js验证，更新积分
	public function loginChk() {

		if (!IS_AJAX) exit();

		

		$uid = intval(get_cookie('uid'));
		$email = get_cookie('email');
		$nickname = get_cookie('nickname');
		$logintime = get_cookie('logintime');
		$loginip = get_cookie('loginip');
		$verifytime = intval(get_cookie('verifytime'));//上次登录时间

		$furl = '';

		$nickname = empty($nickname)? $email : $nickname;


		if ($uid <= 0 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			del_cookie(array('name' => 'uid'));
			del_cookie(array('name' => 'nickname'));
			del_cookie(array('name' => 'verifytime'));
			del_cookie(array('name' => 'logintime'));
			$this->error('请登录', '');//支持ajax,$this->error(info,url,array);
		}

		if (date('Y-m-d', $verifytime) != date('Y-m-d', time())) {
			$user = M('member')->where(array('id'=> $uid, 'email' => $email))->find();
			if (!$user) {
				del_cookie(array('name' => 'uid'));
				del_cookie(array('name' => 'nickname'));
				del_cookie(array('name' => 'verifytime'));
				del_cookie(array('name' => 'logintime'));
				$this->error('请登录!', '');
			}
			set_cookie( array('name' => 'verifytime', 'value' => time()));//本次状态

		}

		$this->success('已登录', $furl , array('nickname'=>$nickname));
	}


	//注册
	public function register() {

		if (IS_POST) {
			$this->registerHandle();
			exit();
		}

		$this->title = '用户注册';
		$this->display();
	}


	//注册
	public function registerHandle() {

		if (!IS_POST) {
			exit(0);
		}

		$password = I('password', '');

		
		$verify = I('vcode','','md5');
		if (C('cfg_verify_register') == 1 && $_SESSION['verify'] != $verify) {
			$this->error('验证码不正确');
		}
		//M验证
		$validate = array(
			array('email','require','电子邮箱必须填写！'),
			array('email','email','邮箱格式不符合要求。'), 
			//array('groupid','require','请选择会员组！'), 
			array('password','require','密码必须填写！'), 
			array('rpassword','require','确认密码必须填写！'), 
			array('password','rpassword','两次密码不一致',0,'confirm'),
			array('email','','邮箱已经存在！',0,'unique',1), //使用这个是否存在，auto就不能自动完成
		);

				

		$db = M('member');
		if (!$db->validate($validate)->create()) {
			$this->error($db->getError());
		}

		$nickname = I('nickname', '', 'htmlspecialchars,trim');
		$notallowname = explode(',', C('cfg_member_notallow'));
		if (in_array($nickname, $notallowname)) {
			$this->error('此昵称系统禁用，请重新更换一个！');
		}

		$mGroup = M('membergroup')->Field('id')->find();
		if ($mGroup) {
			$data['groupid'] = $mGroup['id'];
		}
		$email = I('email', '', 'htmlspecialchars,trim');
		$data['email'] = $email;
		$data['nickname'] = $nickname;
		$data['nickname'] = I('nickname', '');
		//代替自动完成
		$data['regtime'] = time();
		$passwordinfo = I('password', '','get_password');
		$data['password'] = $passwordinfo['password'];
		$data['encrypt'] = $passwordinfo['encrypt'];
		$regtime = date('Y年m月d日', time());
		$nextday = date('Y年m月d日 H:i', strtotime("+2 day"));
		$subject = "[{$cfg_webname}]请激活你的帐号，完成注册";




		if($id = $db->add($data)) {
			$msg = '注册会员成功<br/>'; 
			$active['expire'] = strtotime("+2 day")  ;//二天后时间截,相当于time() + 2 * 24 * 60 * 60
			$active['code'] = get_randomstr(11);
			$active['userid'] = $id;
			$active['id'] = M('active')->add($active);


		    $url = rtrim(C('cfg_weburl'),'/'). "/index.php?g=Index&m=Public&a=activate&va={$active['id']}&vc={$active['code']}";
		    //$url = preg_replace("#http:\/\/#i", '', $url);
		    //$url = 'http://'.preg_replace("#\/\/#i", '/', $url);
		   
		    $webname = C('cfg_webname');
		    $weburl = C('cfg_weburl');
		    $weburl2 = str_replace('http://www.', '', $weburl);
		    $webqq = C('cfg_qq');
		    $webmail = C('cfg_email');
		   
			$subject = "[{$webname}]请激活你的帐号，完成注册";
			$message = <<<str
<p>您于 {$regtime} 注册{$webname}帐号 <a href="mailto:{$email}">{$email}</a> ，点击以下链接，即可激活该帐号：</p>
<p><a href="{$url}" target="_blank">{$url}</a></p>
<p>(如果您无法点击此链接，请将它复制到浏览器地址栏后访问)</p>
<p>为了保障您帐号的安全性，请在 48小时内完成激活，此链接将在您激活过一次后失效！</p>
<p>此邮件由系统发送，请勿直接回复。</p>
str;
			if (C('cfg_member_verifyemail')) {
				if (SendMail($email, $subject , $message) == true) 
				{
					$msg .= '验证邮件已发送，请尽快查收邮件，激活该帐号';
				} else {

					$msg .= '验证邮件发送失败，请写管理员联系';
				}
			}
			
			$this->success($msg ,U(GROUP_NAME. '/Public/login'));
		}else {
			$this->error('注册失败');
		}

	}


	public function sendActivate() {


		$uid = get_cookie('uid');
		if (empty($uid)) {
			$this->error('请登录后尝试');
		}

		$user = M('member')->find($uid);		
		$email = $user['email'];
		$regtime = date('Y年m月d日', $user['regtime']);

		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	        $this->error('你的邮箱格式有错误！');
	    }

	    if($user['status'] == 1)
	    {
	        $this->error('你的帐号邮件已经激活，本操作无效！');
	    }

	    $actinfo = M('active')->where(array('userid' => $uid, 'expire' => array('gt', time())))->find();
	    $data = array();
	    //有记录
	    if ($actinfo) {
	    	$data['id'] = $actinfo['id'];
	    	$data['expire'] = $actinfo['expire'] ;
			$data['code'] = $actinfo['code'];
			$data['userid'] = $uid;
	    }else {

	    	$data['expire'] = strtotime("+2 day")  ;//二天后时间截,相当于time() + 2 * 24 * 60 * 60
			$data['code'] = get_randomstr(11);
			$data['userid'] = $uid;
			//M('active')->delete($uid);//清除有的记录
			$data['id'] = M('active')->add($data);

	    }
		
		$nextday = date('Y年m月d日 H:i', $data['expire']);

	    $url = rtrim(C('cfg_weburl'),'/'). "/index.php?g=Index&m=Public&a=activate&va={$data['id']}&vc={$data['code']}";
	    //$url = preg_replace("#http:\/\/#i", '', $url);
	    //$url = 'http://'.preg_replace("#\/\/#i", '/', $url);

	    $webname = C('cfg_webname');
	    $weburl = C('cfg_weburl');
	    $weburl2 = str_replace('http://www.', '', $weburl);
	    $webqq = C('cfg_qq');
	    $webmail = C('cfg_email');
	   
	    $subject = "[{$webname}]会员邮件验证通知，完成激活";
		$message = <<<str
<p>您于 {$regtime} 注册{$webname}帐号 <a href="mailto:{$email}">{$email}</a> ，点击以下链接，即可激活该帐号：</p>
<p><a href="{$url}" target="_blank">{$url}</a></p>
<p>(如果您无法点击此链接，请将它复制到浏览器地址栏后访问)</p>
<p>为了保障您帐号的安全性，请在 48小时内完成激活，此链接将在您激活过一次后失效！</p>
<p>此邮件由系统发送，请勿直接回复。</p>
str;

	$msg = ''; 
	if (SendMail($email, $subject , $message) == true) {
		$msg .= '验证邮件已发送，请尽快查收邮件，激活该帐号';
	} else {

		$msg .= '验证邮件发送失败，请写管理员联系';
	}
	$this->success($msg ,U(GROUP_NAME. '/Member/index'), 60);
	    
	}

	public function activate() {
		header("Content-Type:text/html; charset=utf-8");

		$id = I('va', 0, 'intval');
		$code = I('vc', '', 'htmlspecialchars,trim');
	    if(empty($code) || $id == 0)
	    {
	        exit('你的效验串不合法！<a href="'. C('cfg_weburl') .'">返回首页</a>');
	    }
	    $row = M('active')->where(array('id' => $id, 'expire' => array('gt', time())))->find();
	    if($code != $row['code'])
	    {
	        exit('激活码过期或错误！<a href="'. C('cfg_weburl') .'">返回首页</a>');
	    }

	    M('member')->where(array('id' => $row['userid'] ))->setField('status','1');//激活用户状态设置
	    //M('active')->delete($id);//从激活表中删除
	     M('active')->where(array('id' => $row['id'] ))->setField('expire','0');//激活用户状态设置
	    // 清除会员缓存
	    //DelCache($mid);
	    $this->success('激活操作成功，请重新登录！' ,U(GROUP_NAME. '/Public/login'));

	}



	/*Send verification code*/
	public function sendCode() {
		header("Content-Type:text/html; charset=utf-8");
		if (!IS_POST) {
			exit();
		}
		
		$email = I('username','','htmlspecialchars,trim');
		$flag = I('flag', 0, 'intval');

		//$flag为1时，需要验证email是否已经被使用，注册必需未使用的email
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			//exit(json_encode(array('status'=>0,'info'=>'E-mail格式不正确！','input'=>'email')));
			//$this->ajaxReturn(array('status'=>0,'info'=>'E-mail格式不正确！','input'=>'email'),'JSON');//Thinkphp内部
			$this->error('E-mail格式不正确！','', array('input'=>'email'));//TP3.1后，error和success支持ajax返回

		}
		

		if ($flag) {
			
			if ($user = M('member')->where(array('email' => $email))->find()) {
				$this->error('邮箱已经存在，请更换邮箱或直接登录！','', array('input'=>'email'));
			}

		}

		//查询active表，是否发送过注册验证码，发过，则不再重新生成新的验证码，直接发送
		$actinfo = M('active')->where(array('email' => $email, 'type' => 1, 'expire' => array('gt', time())))->order('expire DESC')->find();
	    $data = array();
	    //有记录
	    if ($actinfo) {
	    	$data['id'] = $actinfo['id'];
			$data['userid'] = 0;
			$data['code'] = $actinfo['code'];
	    	$data['expire'] = $actinfo['expire'] ;
			$data['type'] = $actinfo['type'];
			//小于3分钟,则更新有效期(延长)
			if ($data['expire'] - time() < 3 * 60) {
				$data['expire'] = time()+ 20 * 60;//20 minutes
				M('active')->where(array('id' => $data['id']))->setField('expire', $data['expire']);
			}
	    }else {

			$data['userid'] = 0;
			$data['code'] = get_random(6, '1234567890');//产生数字
	    	$data['expire'] = time()+ 20 * 60;//20 minutes//strtotime("+2 day")  ;
			$data['email'] = $email;
			$data['type'] = 1;
			//M('active')->delete($uid);//清除有的记录
			$data['id'] = M('active')->add($data);

	    }
		
		$nextday = date('Y年m月d日 H:i', $data['expire']);



		$regtime = date('Y年m月d日', time());
		$nextday = date('Y年m月d日 H:i', strtotime("+2 day"));


		//$url = rtrim(C('cfg_weburl'),'/'). "/index.php?g=Index&m=Public&a=activate&va={$active['id']}&vc={$active['code']}";
		    //$url = preg_replace("#http:\/\/#i", '', $url);
		    //$url = 'http://'.preg_replace("#\/\/#i", '/', $url);
		   
	    $webname = C('cfg_webname');
	    $weburl = C('cfg_weburl');
	    $weburl2 = str_replace('http://www.', '', $weburl);
	    $webqq = C('cfg_qq');
	    $webmail = C('cfg_email');
	   
		$subject = "[{$webname}]会员注册验证码";
		$message = <<<str
<p>您本次申请的验证码为：{$data['code']}</p>
<p> </p>
<p>1、为了保障您的安全，请不要将以上验证码告诉任何人，本站工作人员不会向您索取验证码。</p>
<p>2、如果本次验证码并非您本人申请，请忽略本邮件。。</p>
<p>此邮件由系统发送，请勿直接回复。</p>
str;
		$msg = '';
		if (SendMail($email, $subject , $message) == true) 
		{
			$msg .= '';//'验证邮件已发送，请尽快查收邮件，激活该帐号';
		} else {

			$msg .= '!';//'验证邮件发送失败，请写管理员联系';
		}


		$this->success('验证码发送成功,请到邮箱查收'.$msg,'', array('input'=>'email'));
	
	}


	//增加点击数
	public function click(){	
		$id = I('id', 0, 'intval');
		$tablename = I('tn', '');
		if (C('HTML_CACHE_ON') == true) {
			echo 'document.write('. getClick($id, $tablename) .')';
		}
		else {
			echo getClick($id, $tablename);
		}
		
	}


	//证码码
	public function verify(){	
		import('ORG.Util.Image');//导入验证码Image类库
		return Image::buildImageVerify(4, 1);
	}

    //上传图片
    public function upload() {
        header("Content-Type:text/html; charset=utf-8");//不然返回中文乱码
        $tb = I('get.tb', 0, 'intval'); //缩略图地址前缀/,1:_s,2:_m,0默认


        //百度编辑新版要求--start
        //获取存储目录--对应百度编辑器
        $imgSavePathConfig = array (
            'upload',
        );
        if ( isset( $_GET[ 'fetch' ] ) ) {

            header( 'Content-Type: text/javascript' );
            echo 'updateSavePath('. json_encode($imgSavePathConfig) .');';
            return;

        }
        //百度编辑要求--end

        //文件上传地址提交给他，并且上传完成之后返回一个信息，让其写入数据库
        if(empty($_FILES)){
            //$this->error('必须选择上传文件');
            echo json_encode(array(
                'url' => '', 'title' => '',	'original' => '',
                'state' => '必须选择上传文件'
            ));
        }else{
            $info = $this->_uploadPicture();//获取图片信息

            //p($info);exit();

            if(isset($info) && is_array($info)){
                //写入数据库的自定义c方法
                if(!$this->_uploadData($info)){
                    //echo '上传入库失败';
                    echo json_encode(array(
                        'url' => '',
                        'title' => '',
                        'original' => '',
                        'state' => '上传入库失败'
                    ));
                    exit();
                }
                //$picture_url = ltrim($info[0]['savepath'],'.').$info[0]['savename'];
                $picture_url = $info[0]['savepath'].$info[0]['savename'];
                //返回缩略图地址

                $picture_turl = $picture_url;
                //if ($tb == 2 || $tb == 1)
                {

                    //$picture_url = preg_replace('/\.(.+)$/', '_m.$1', $picture_url);//缩略图的_m,_s后缀
                    $imgtbSize = explode(',', C('cfg_imgthumb_size'));//配置缩略图第一个参数
                    $imgTSize = explode('X', $imgtbSize[0]);


                    if (!empty($imgTSize)) {
                        $picture_turl = get_picture($picture_url, $imgTSize[0], $imgTSize[1]);
                    }
                }

                echo json_encode(array(
                    'url' => $picture_url,
                    'turl' => $picture_turl,
                    'title' => $info[0]['name'],
                    'original' => $info[0]['name'],
                    'state' => 'SUCCESS',
                    'size' => round($info[0]['size']/1024,2)
                ));


            }else{
                //echo "{'url':'','title':'','original':'','state':'". $info ."'}";
                echo json_encode(array(
                    'url' => '',  'title' => '', 'original' => '',
                    'state' => '失败:'. $info
                ));

            }
        }

    }

    //上传图片
    public function _uploadPicture() {
        $ext = '';//原文件后缀
        $ext_dest = 'jpg';//生成缩略图格式
        foreach ($_FILES as $key => $v) {
            $strtemp = explode('.', $v['name']);
            $ext = end($strtemp);//获取文件后缀，或$ext = end(explode('.', $_FILES['fileupload']['name']));
            break;
        }

        import('ORG.Net.UploadFile');//导入ThinkPHP的上传类
        //..这里可以配置上传类的参数config，设置N个配置项，可在这里设置new UploadFile($config)
        $upload = new UploadFile();
        //只修改几个配置项，可在这里设置
        $upload->autoSub =true;//是否使用子目录保存图片
        $upload->subType = 'date';//子目录保存规则
        $upload->dateFormat = 'Ymd';
        $upload->maxSize = getUploadMaxsize();//设置上传文件大小
        //设置上传文件类型
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        //上传目录
        $upload->savePath ='./uploads/img1/';

        //$upload->saveRule = 'time';
        $upload->saveRule = 'uniqid';//设置上传文件规则
        $upload->uploadReplace = true; //是否存在同名文件是否覆盖

        /*缩略图设置*/
        //设置需要生成缩略图,仅对图像文件有效
        //读取配置文件中的设置
        $imgtbSize = explode(',', C('cfg_imgthumb_size'));
        $imgtbArray = array();
        foreach ($imgtbSize as $v) {
            $t_size = explode('X', $v);

            if (empty($t_size) || empty($t_size[0]) || empty($t_size[1])) {
                continue;
            }
            $imgtbArray[] = array('w' => intval($t_size[0]), 'h' => intval($t_size[1]));
        }


        /*

        $strWidth = '';
        $strHeight ='';
        $strSuffix ='';
        foreach ($imgtbArray as $i => $v) {
            if ($i !=0) {
                $strWidth .= ',';
                $strHeight .= ',';
                $strSuffix .= ',';
            }
            $strWidth .= $v['w'];
            $strHeight .= $v['h'];
            $strSuffix .= '.'.$ext.'!'.$v['w'].'X'.$v['h'];

        }



        //系统缩略图设置
        $upload->thumb = true;
        //设置引用图片类库包路径
        //$upload->imageClassPath = 'ORG.Util.Image';
        //设置需要生成缩略图片的文件后缀
        //$upload->thumbPrefix = 'm_,s_'; //生成2张缩略图前缀
        $upload->thumbPrefix = ''; //缩略图前缀
        $upload->thumbExt = $ext_dest;//缩略图后缀,但他根据后缀转换类型，还是默认的格式
        //$upload->thumbMaxWidth = '300,100';//设置缩略图最大宽度
        $upload->thumbMaxWidth = $strWidth;//设置缩略图最大宽度
        $upload->thumbMaxHeight = $strHeight;//设置缩略图最大宽度
        //$upload->thumbSuffix = '_m,_s';//
        $upload->thumbSuffix = $strSuffix;//
        $upload->thumbPath = './uploads/img1/'.date('Ym', time()) .'/';// . $path . date('Ymd', time()) . '/'; //缩略图保存路径
        $upload->thumbType = C('cfg_imgthumb_type') ? 1:0; // 缩略图生成方式 1 按设置大小截取 0 按原图等比例缩略

        $upload->thumbRemoveOrigin = false;//删除原图

        */



        //$upload->upload('./uploads/img1/')
        if($upload->upload()) {
            $info = $upload->getUploadFileInfo();//获取图片信息
            $real_path = './uploads/img1/'.$info[0]['savename'];

            //读取配置文件固定宽等比缩略
            $imgtbFixWidth = explode(',', C('cfg_imgthumb_width'));
            $imgtbFixArray = array();
            foreach ($imgtbFixWidth as $v) {
                if (empty($v) || intval($v) == 0) {
                    continue;
                }
                $imgtbFixArray[] = array('w' => intval($v), 'h' => intval($v * 100));
            }

            if (!empty($imgtbFixArray) || !empty($imgtbArray)) {
                import('ORG.Util.Image.ThinkImage');
                $Think_img = new ThinkImage(THINKIMAGE_GD);
                $thumbType = C('cfg_imgthumb_type') ? 3:1;//配置大小
                //生成缩略图,固定大小
                foreach ($imgtbArray as $i => $v) {
                    $strSuffix = '!'.$v['w'].'X'.$v['h'];
                    $Think_img->open($real_path)->thumb($v['w'],$v['h'], $thumbType)->save($real_path.$strSuffix.'.'.$ext_dest,$ext_dest);

                }
                //生成缩略图，不放大，等宽，高度不限
                foreach ($imgtbFixArray as $v) {
                    $strSuffix = '!'.$v['w'].'X';
                    $Think_img->open($real_path)->thumb($v['w'],$v['h'], 1)->save($real_path.$strSuffix.'.'.$ext_dest,$ext_dest);
                }

            }

            //转换成网站根目录绝对路径,.Uploads 转成 /目录/Uploads
            $info[0]['savepath'] = __ROOT__.ltrim($info[0]['savepath'],'.');//去掉第一个"."字符
            $info[0]['haslitpic'] = 1;

            return $info;

        }else {

            //$str = array('err' =>1 ,'msg' => $upload->getErrorMsg() );
            return $upload->getErrorMsg();
        }


    }

    /**
    //图片(上传后)数组入库
    filearr:图片数组
     **/
    public function _uploadData($filearr) {

        $db=M('attachment');
        $num  = 0;

        for($i = 0; $i < count($filearr); ++$i) {
            $savepath = $filearr[$i]['savepath'];

            /*
            if (!empty($savepath) && substr($savepath,0,1)  == '.') {//判断首字符是否是'.'
                $savepath = substr($savepath,1,(strlen($savepath)-1));//去掉第一个字符
            }
            */

            $data['filepath'] = $savepath .$filearr[$i]['savename'];
            $data['title'] = $filearr[$i]['name'];
            $data['haslitpic'] = empty($filearr[$i]['haslitpic']) ? 0 : 1;
            $filetype =1;
            //后缀
            switch ($filearr[$i]['extension']) {
                case 'gif':
                    $filetype =1;
                    break;
                case 'jpg':
                    $filetype =1;
                    break;
                case 'png':
                    $filetype =1;
                    break;
                case 'bmp':
                    $filetype =1;
                    break;
                case 'swf'://flash
                    $filetype =2;
                    break;
                case 'mp3'://音乐
                    $filetype =3;
                    break;
                case 'wav':
                    $filetype =3;
                    break;
                case 'rm'://电影
                    $filetype =4;
                    break;

                case 'doc'://
                    $filetype =5;
                    break;
                case 'docx'://
                    $filetype =5;
                    break;
                case 'xls'://
                    $filetype =5;
                    break;
                case 'ppt'://
                    $filetype =5;
                    break;
                case 'zip'://
                    $filetype =6;
                    break;
                case 'rar'://
                    $filetype =6;
                    break;
                case 'pptx'://
                    $filetype =6;
                    break;
                case 'pdf'://
                    $filetype =6;
                    break;
                case 'xlsx'://
                    $filetype =6;
                    break;
                case '7z'://
                    $filetype =6;
                    break;

                default://其他
                    $filetype = 0;
                    break;
            }
            $data['filetype'] = $filetype;
            $data['filesize'] = $filearr[$i]['size'];
            $data['uploadtime'] = time();
            $data['aid'] = 0;//管理员ID
            if( $db->add($data))
            {
                ++$num;
            }
            //echo $db->getLastSql();
        }

        if($num==count($filearr))
        {
            return true;
        }else
        {
            return false;
        }


    }


}


