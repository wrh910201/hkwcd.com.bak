<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2016/10/7
 * Time: 21:22
 */

class ProfileAction extends BaseAction {

    public function index() {

        $user_id = session('hkwcd_user.user_id');
        $user = M('Client')->where(['id' => $user_id])->find();
//        $user['country_id'] = intval($user['country_id']);
        $this->assign('user', $user);


        $where = array('pid' => 0,'types'=>0);
        $country_list = M('country')->where($where)->order('sort,id')->select();
        foreach( $country_list as $k => $v ) {
            $country_list[$k]['id'] = intval($v['id']);
            if( $user['country_id'] == $v['id'] ) {
                $selected = $v['id'];
            }
        }
        $this->assign('selected', json_encode($selected));
        $this->assign('country_list', $country_list);

        $this->assign('title', '帐号信息');
        $this->display();
    }

    public function edit() {
        $id = session('hkwcd_user.user_id');

        $data['first_name'] = I('post.first_name', '', 'htmlspecialchars,trim');
        $data['last_name'] = I('post.last_name', '', 'htmlspecialchars,trim');
        $data['full_name'] = I('post.full_name', '', 'htmlspecialchars,trim');
        $data['company'] = I('post.company', '', 'htmlspecialchars,trim');
        $data['care_of'] = I('post.care_of', '', 'htmlspecialchars,trim');
        $data['address'] = I('post.address', '', 'htmlspecialchars,trim');
        $data['address2'] = I('post.address2', '', 'htmlspecialchars,trim');
        $data['country_id'] = I('post.country', 0, 'intval');
        $data['city'] = I('post.city', '', 'htmlspecialchars,trim');
        $data['postal_code'] = I('post.postal_code', '', 'htmlspecialchars,trim');
        $data['phone'] = I('post.phone', '', 'htmlspecialchars,trim');
        $data['mobile'] = I('post.mobile', '', 'htmlspecialchars,trim');
        $data['fax'] = I('post.fax', '', 'htmlspecialchars,trim');
        $data['email'] = I('post.email', '', 'htmlspecialchars,trim');
        $data['agent'] = I('post.agent', '', 'htmlspecialchars,trim');
        $data['remark'] = I('post.remark', '', 'htmlspecialchars,trim');
        $data['is_locked'] = I('post.islock', 0, 'intval');

        $validate = array(
//            array('username','require','请输入用户名'), // 内置正则验证邮箱
            array('full_name','require','请输入姓名'), // 内置正则验证邮箱
//                array('first_name','require','请输入名'), // 内置正则验证邮箱
//                array('last_name','require','请输入姓'), // 内置正则验证邮箱
            array('company','require','请输入公司名称'), // 内置正则验证邮箱
            array('address','require', '请至少输入一个地址'),
            array('country_id', 'require', '请选择国家')
        );

        //检测用户名是否可以用
//        if( empty($data['username']) ) {
//            $this->error('请输入用户名');
//            exit;
//        }
//        $username_exists = M('client')->where(['username' => $data['username']])->find();
//        if( $username_exists ) {
//            $this->error('用户名已存在，请更换用户名');
//            exit;
//        }

        //检测国家是否合法
        $country_exists = M('country')->where(['id' => $data['country_id']])->find();
        if( empty($country_exists) ) {
            $this->error('请选择国家');
            exit;
        }

        //设置添加的管理员id
//        $data['operator_id'] = session('yang_adm_uid');

        $db = M('client');
        if (!$db->validate($validate)->create($data)) {
            $this->error($db->getError());
        }
        $result = $db->where(['id' => $id])->save($data);
        if( is_numeric($result) ) {
            $this->success('编辑成功');
        } else {
            $this->error('系统繁忙，请稍后重试');
        }
    }

    public function password() {
        $this->assign('title', '修改密码');
        $this->display();
    }

    public function reset() {
        $old_password = I('post.old_password', '', 'trim');
        $new_password = I('post.new_password', '', 'trim');
        $confirm_password = I('post.confirm_password', '', 'trim');

        if( strlen($new_password) < 6 || strlen($new_password) > 16 ) {
            $this->error('密码必须是6-16位的字符！');
        }
        if( $new_password != $confirm_password ) {
            $this->error('两次输入的密码不一致');

        }

        $user_id = session('hkwcd_user.user_id');
        $user = M('Client')->where(['id' => $user_id])->find();

        if( empty($user) ) {
            $this->error('请先登录', '/');
        }
        if( $user['password'] != get_password($old_password, $user['encrypt']) ) {
            $this->error('原密码错误');
        }

        $new_password = get_password($new_password, $user['encrypt']);
        $result = M('Client')->where(['id' => $user_id])->save(['password' => $new_password]);
        if( $result ) {
            $this->success('修改密码成功');
        } else {
            $this->error('系统繁忙，请稍后重试');
        }



    }

}