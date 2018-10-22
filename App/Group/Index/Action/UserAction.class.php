<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2016/10/6
 * Time: 17:43
 */

class UserAction extends Action {


    public function login() {
        $is_login = false;
        if( session('hkwcd_user') ) {
            $is_login = true;
        } else {
            $ip_address = get_client_ip();
            $date_time = date('Y-m-d H:i:s', time());
            $disabled_ip = M('DisabledIp')->where(['ip_address' => $ip_address, 'expired_time' => ['GT', $date_time]])->find();
            if( $disabled_ip ) {
                echo '由于频繁登录，请于'.$disabled_ip['expired_time'].'之后重试';exit;
            }
        }
        $this->assign('is_login', $is_login);
        $this->assign('js_is_login', json_encode($is_login));
        $this->display();
    }

    public function doLogin() {
        if (!IS_POST) {
            exit();
        }

        $ip_address = get_client_ip();
        $date_time = date('Y-m-d H:i:s', time());
        $disabled_ip = M('DisabledIp')->where(['ip_address' => $ip_address, 'expired_time' => ['GT', $date_time]])->find();
        if( $disabled_ip ) {
            $this->_error('由于频繁登录，请于'.$disabled_ip['expired_time'].'之后重试');
        }

        $furl = I('post.furl', '','htmlspecialchars,trim');
        if (empty($furl) || strpos($furl, 'register') || strpos($furl, 'login') || strpos($furl, 'logout') || strpos($furl, 'activate') || strpos($furl, 'sendActivate')) {
//            $furl = U(GROUP_NAME. '/User/index');
            $furl = '/Client/index';
        }

        $username = I('post.username','','htmlspecialchars,trim');
        $password = I('post.password','', 'trim');

        $verify = I('vcode','','md5');
        if (C('cfg_verify_login') == 1 && $_SESSION['verify'] != $verify) {
            $this->_error('验证码不正确');
        }

        if( empty($username) ) {
            $this->_error('请输入用户名');
        }

        if( empty($password) ) {
            $this->_error('请输入密码');
        }

        if (strlen($password)<6 || strlen($password)>16) {
            $this->_error('密码必须是6-16位的字符！', '', array('input'=>'password'));
        }

        $user = M('Client')->where(['username' => $username])->find();
        if( empty($user) || ($user['password'] != get_password($password, $user['encrypt'])) ) {
            //禁止ip
            if( C('login_time_limit') ) {
                $data['ip_address'] = get_client_ip();
                $ip_record = M('IpError')->where($data)->find();
                if( $ip_record ) {
                    if( $ip_record['error_count'] >= 5 ) {
                        $expired_time = date('Y-m-d H:i:s', time() + C('ip_expired_time'));
                        M('DisabledIp')->add(['ip_address' => $data['ip_address'], 'expired_time' => $expired_time]);
                        M('IpError')->where($data)->save('error_count', 0);
                    } else {
                        M('IpError')->where($data)->setInc('error_count', 1);
                    }
                } else {
                    M('IpError')->add($data);
                }
            }
            //锁定用户
            if( C('login_time_limit') && $user ) {
                M('ClientError')->add(['client_id' => $user['id']]);
                $error_count = M('ClientError')->where(['client_id' => $user['id']])->count();
                if( $error_count >= 5 ) {
                    M('Client')->where(['id' => $user['id']])->save(['is_locked' => 1]);
                }
            }
            $this->_error('帐号或密码错误');
        }
        if( $user['is_locked'] ) {
            $this->_error('您的帐号已被锁定，请联系华度工作人员');
        }

        //记录登录时间、IP、次数、如果是重置密码后的首次登录，删除明文密码
        $login_data = [
            'last_login' => date('Y-m-d H:i:s', time()),
            'last_ip' => get_client_ip(),
        ];
        if( $user['real_password'] ) {
            $login_data['real_password'] = '';
        }
        M('Client')->where(['id' => $user['id']])->save($login_data);
        M('Client')->where(['id' => $user['id']])->setInc('login_num', 1);

        //session
        $session_data = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'group_id' => $user['group_id'],
        ];
        session('hkwcd_user', $session_data);
        $this->_success('登录成功', $furl);
    }

    public function index() {
        echo '用户中心';
    }

    public function logout() {
        $furl = $_SERVER['HTTP_REFERER'];

        if (empty($furl) || strpos($furl, 'register') || strpos($furl, 'login') || strpos($furl, 'activate') || strpos($furl, 'sendActivate')) {
            $furl = '/';
        }
        $furl = '/';
        session('hkwcd_user', null);
        $this->success('安全退出', $furl);
    }

    public function forget() {
        $this->display();
    }

    public function reset() {
        $email = I('email', '', 'trim');
        if( empty($email) ) {
            $this->_error('请输入绑定的邮箱');
        }
        if( !is_email($email) ) {
            $this->_error('邮箱格式错误');
        }

        $verify = I('vcode','','md5');
        if (C('cfg_verify_login') == 1 && $_SESSION['verify'] != $verify) {
            $this->_error('验证码不正确');
        }

        $where = [
            'email' => $email,
            'status' => 1,
        ];
        $email_exists = M('Client')->where($where)->find();
        if( empty($email_exists) ) {
            $this->_error('邮箱不存在');
        }
        //发送邮件

    }

    private function _error($msg, $url = '') {
        $output = array(
            'code' => 0,
            'msg' => $msg,
            'url' => $url,
        );
        echo json_encode($output);
        exit;
    }

    private function _success($msg, $url = '') {
        $output = array(
            'code' => 1,
            'msg' => $msg,
            'url' => $url,
        );
        echo json_encode($output);
        exit;
    }
}