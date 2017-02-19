<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2016/9/25
 * Time: 12:23
 */
class ClientAction extends CommonContentAction {

    /**
     * 列表
     */
    public function index() {

        $keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字
        $where = array();
        $condition = array();
        if (!empty($keyword)) {
            $condition['username'] = ['like', $keyword];
            $condition['full_name'] = ['like', $keyword];
            $condition['company'] = ['like', $keyword];
            $condition['_logic'] = 'OR';
            $where['_complex'] = $condition;
        }
        $where['status'] = 1;
        //分页
        import('ORG.Util.Page');
        $count = M('client')->where($where)->count();

        $page = new Page($count, 10);
        $limit = $page->firstRow. ',' .$page->listRows;
        $list = M('client')->where($where)->order('id')->limit($limit)->select();
        if( $list ) {
            foreach( $list as $k => $v ) {
                $list[$k]['company'] = mb_strlen($v['company'], 'utf-8') > 13 ? mb_substr($v['company'], 0, 13, 'utf-8').'...' : $v['company'];
            }
        }
        $group_list = M('clientGroup')->select();

        if( $group_list ) {
            $temp = array();
            foreach( $group_list as $k => $v ) {
                $temp[$v['id']] = $v['name'].'/<br />'.$v['en_name'];
            }
            $group_list = $temp;
        }
        $this->group_list = $group_list;

        $this->page = $page->show();
        $this->client_list = $list;
        $this->type = '客户列表';
        $this->keyword = $keyword;


        $this->display();
    }

    /**
     * 注册用户页面
     */
    public function add() {
        $where = array('pid' => 0,'types'=>0);
        $country_list = M('country')->where($where)->order('sort,id')->select();
        $this->assign('country_list', $country_list);
        $group_list = M('clientGroup')->where('1')->order('id asc')->select();
        $this->assign('group_list', $group_list);
        $this->display();
    }

    public function doAdd() {
        if( IS_POST ) {
            //验证输入，生成用户名、帐号
            $data['username'] = I('post.username', '', 'htmlspecialchars,trim');
            $data['password'] = I('post.password', '', 'htmlspecialchars,trim');
            $data['group_id'] = I('post.group_id', 0, 'intval');
            $data['full_name'] = I('post.full_name', '', 'htmlspecialchars,trim');
            $data['company'] = I('post.company', '', 'htmlspecialchars,trim');
            $data['email'] = I('post.email', '', 'htmlspecialchars,trim');
            $data['mobile'] = I('post.mobile', '', 'htmlspecialchars,trim');
            $data['spare_mobile'] = I('post.spare_mobile', '', 'htmlspecialchars,trim');
            $data['wechat'] = I('post.wechat', '', 'htmlspecialchars,trim');
            $data['qq'] = I('post.qq', '', 'htmlspecialchars,trim');
            $data['country_id'] = I('post.country_id', '', 'htmlspecialchars,intval');
            $data['state'] = I('post.state', '', 'htmlspecialchars,trim');
            $data['city'] = I('post.city', '', 'htmlspecialchars,trim');
            $data['address'] = I('post.address', '', 'htmlspecialchars,trim');
            $data['postal_code'] = I('post.postal_code', '', 'htmlspecialchars,trim');
            $data['spare_country_id'] = I('post.spare_country_id', '', 'htmlspecialchars,intval');
            $data['spare_state'] = I('post.spare_state', '', 'htmlspecialchars,trim');
            $data['spare_city'] = I('post.spare_city', '', 'htmlspecialchars,trim');
            $data['spare_address'] = I('post.spare_address', '', 'htmlspecialchars,trim');
            $data['spare_postal_code'] = I('post.spare_postal_code', '', 'htmlspecialchars,trim');
            $data['phone'] = I('post.phone', '', 'htmlspecialchars,trim');
            $data['fax'] = I('post.fax', '', 'htmlspecialchars,trim');
            $data['remark'] = I('post.remark', '', 'htmlspecialchars,trim');
            $data['is_locked'] = I('post.islock', 0, 'intval');

            //检测用户名是否可以用
            if( empty($data['username']) ) {
                $this->error('请输入用户名');
                exit;
            }
            $username_exists = M('client')->where(['username' => $data['username']])->find();
            if( $username_exists ) {
                $this->error('用户名已存在，请更换用户名');
                exit;
            }
            //检测用户组是否合法
            $group_exists = M('clientGroup')->where(['id' => $data['group_id']])->find();
            if( empty($group_exists) ) {
                $this->error('请选择用户组');
                exit;
            }
            //密码
            $password = $data['password'];
            if( empty($password) ) {
                $this->error('请输入密码');
                exit;
            } elseif( strlen($password) < 6) {
                $this->error('密码长度至少为6位');
            }


            //生成6位随机密码
//            $password = get_randomstr();
            $data['encrypt'] = get_randomstr();
            $data['password'] = get_password($password, $data['encrypt']);

            $data['is_person'] = 0;
            if( empty($data['company']) ) {
                $data['is_person'] = 1;
            }

            //设置添加的管理员id
            $data['operator_id'] = session('yang_adm_uid');

            $db = M('client');

            if( $id = $db->add($data) ) {
                $this->success('添加客户成功', U(GROUP_NAME. '/Client/index'));
            } else {
                $this->error('添加客户失败');
            }

        }
    }

    public function checkUsername() {
        if( IS_POST ) {
            $username = I('post.username', '', 'htmlspecialchars,trim');
            $length = mb_strlen($username, 'utf-8');
            if( $length < 4 || $length > 16 ) {
                $response['msg'] = '用户名必须在4~16个字符之间';
                $response['code'] = 0;
                echo json_encode($response);
                exit;
            }
            $username_exists = M('client')->where(['username' => $username])->find();
            if( $username_exists ) {
                $response['msg'] = '帐号已存在，请更换帐号';
                $response['code'] = 0;
                echo json_encode($response);
                exit;
            } else {
                $response['msg'] = '帐号可用';
                $response['code'] = 1;
                echo json_encode($response);
                exit;
            }
        }

    }

    /**
     * 编辑用户
     */
    public function edit() {
        $id = I('get.id', 0, 'intval');
        $client = M('client')->where(['id' => $id])->find();
        if( empty($client) ) {
            $this->error('客户不存在');
            exit;
        }
        $where = array('pid' => 0,'types'=>0);
        $country_list = M('country')->where($where)->order('sort,id')->select();
        $this->assign('country_list', $country_list);
        $this->assign('client', $client);

        $group_list = M('clientGroup')->where('1')->order('id asc')->select();
        $this->assign('group_list', $group_list);

        $this->display();
    }

    public function doEdit() {
        $id = I('post.id', 0, 'intval');
        $client = M('client')->where(['id' => $id])->find();
        if( empty($client) ) {
            $this->error('客户不存在');
            exit;
        }

        //验证输入
        $data['group_id'] = I('post.group_id', 0, 'intval');
        $data['full_name'] = I('post.full_name', '', 'htmlspecialchars,trim');
        $data['company'] = I('post.company', '', 'htmlspecialchars,trim');
        $data['email'] = I('post.email', '', 'htmlspecialchars,trim');
        $data['mobile'] = I('post.mobile', '', 'htmlspecialchars,trim');
        $data['spare_mobile'] = I('post.spare_mobile', '', 'htmlspecialchars,trim');
        $data['wechat'] = I('post.wechat', '', 'htmlspecialchars,trim');
        $data['qq'] = I('post.qq', '', 'htmlspecialchars,trim');
        $data['country_id'] = I('post.country_id', '', 'htmlspecialchars,intval');
        $data['state'] = I('post.state', '', 'htmlspecialchars,trim');
        $data['city'] = I('post.city', '', 'htmlspecialchars,trim');
        $data['address'] = I('post.address', '', 'htmlspecialchars,trim');
        $data['postal_code'] = I('post.postal_code', '', 'htmlspecialchars,trim');
        $data['spare_country_id'] = I('post.spare_country_id', '', 'htmlspecialchars,intval');
        $data['spare_state'] = I('post.spare_state', '', 'htmlspecialchars,trim');
        $data['spare_city'] = I('post.spare_city', '', 'htmlspecialchars,trim');
        $data['spare_address'] = I('post.spare_address', '', 'htmlspecialchars,trim');
        $data['spare_postal_code'] = I('post.spare_postal_code', '', 'htmlspecialchars,trim');
        $data['phone'] = I('post.phone', '', 'htmlspecialchars,trim');
        $data['fax'] = I('post.fax', '', 'htmlspecialchars,trim');
        $data['remark'] = I('post.remark', '', 'htmlspecialchars,trim');
        $data['is_locked'] = I('post.islock', 0, 'intval');

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
        //检测用户组是否合法
        $group_exists = M('clientGroup')->where(['id' => $data['group_id']])->find();
        if( empty($group_exists) ) {
            $this->error('请选择用户组');
            exit;
        }

        //设置添加的管理员id
//        $data['operator_id'] = session('yang_adm_uid');

        $db = M('client');
        $result = $db->where(['id' => $id])->save($data);
        if( is_numeric($result) ) {
            $this->success('编辑客户成功', U(GROUP_NAME. '/Client/index'));
        } else {
            $this->error('编辑客户失败');
        }

    }

    /**
     * 重置密码
     */
    public function password() {
        $id = I('get.id', 0, 'intval');
        $client = M('client')->where(['id' => $id])->find();
        if( $client ) {
            $this->assign('client', $client);
            $this->display();
        } else {
            $this->error('客户不存在');
            exit;
        }
    }

    public function reset() {
        $id = I('post.id', 0, 'intval');
        $client = M('client')->where(['id' => $id])->find();
        if( $client ) {
            $password = I('post.password', '', 'htmlspecialchars,trim');
            $confirm_password = I('post.confirm_password', '', 'htmlspecialchars,trim');
            if( empty($password) ) {
                $this->error('请输入新密码');
            }
            if( $confirm_password != $password ) {
                $this->error('两次输入的密码不一致');
            }
            $where = ['id' => $id];
            $data = ['password' => get_password($password, $client['encrypt'])];
            $result = M('Client')->where($where)->save($data);
            if( $result ) {
                $this->success('密码重置成功');
            } else {
                $this->error('密码已重置失败');
            }
        } else {
            $this->error('客户不存在');
            exit;
        }
    }

    /**
     * 锁定/解锁客户
     */
    public function lock() {
        $id = I('get.id', 0, 'intval');
        $client = M('client')->where(['id' => $id])->find();
        if( $client ) {
            $data['is_locked'] = $client['is_locked'] ? 0 : 1;
            $msg = $client['is_locked'] ? '解锁' : '锁定';
            if( M('client')->where(['id' => $id])->save($data) ) {
                $this->success($msg.'成功');
            } else {
                $this->error($msg.'失败');
            }
        } else {
            $this->error('客户不存在');
            exit;
        }
    }

    /**
     * 导入价格
     */
    public function import() {

    }

    /**
     * 显示价格
     */
    public function price() {
        $id = I('get.id', 0, 'intval');
        $client = M('client')->where(['id' => $id])->find();
        if( empty($client) ) {
            $this->error('客户不存在');
            exit;
        }
        $type = I('get.type', 1, 'intval');
        if( $type == 2 ) {
            $type = 2;
        } else {
            $type = 1;
        }
        $channel_id = I('get.channel', 0, 'intval');
        $channel_list = M('Channel')->where(['status' => 1])->select();
        $where = [
            'type' => $type,
            'channel_id' => $channel_id,
            'group_id' => $client['group_id'],
            'status' => 1,
        ];
        $price = M('ChannelMap')->where($where)->find();
//        echo M('ChannelMap')->getLastSql();exit;
        $content = $price['content'] ? json_decode($price['content'], true) : null;
//        var_dump($content);exit;
        $price = [];
        if( $content ) {
            foreach( $content as $k => $row ) {
                if( $k == 1 ) {
                    $region_list = $row;
                    continue;
                }
                foreach( $row as $k1 => $v ) {
                    if (is_numeric(strpos($v, '{'))) {
                        $temp = explode('{', $v);
                        $offset = $temp[0];
                        $per_kilo = explode('}', $temp[1]);
                        $per_kilo = $per_kilo[0];
                        $price[$k][$k1] = '超过'.$offset.'kg，按每'.$per_kilo.'kg';
                    } else {
                        $price[$k][$k1] = sprintf('%.2f', $v);
                    }
                }
            }
        }
        $this->assign('client', $client);
        $this->assign('price', $price);
        $this->assign('region_list', $region_list);
        $this->assign('package_type', $type);
        $this->assign('channel_list', $channel_list);
        $this->assign('channel_id', $channel_id);
        $this->assign('id', $id);
        $this->type = '查看价格';
        $this->display();
    }

    /**
     * 删除
     */
    public function del() {
        $id = I('get.id', 0, 'intval');
        $batchFlag = I('get.batchFlag', 0, 'intval');
        //批量删除
        if ($batchFlag) {
            $this->multiDel();
            return;
        }

        $client = M('client')->where(['id' => $id])->find();
        if( empty($client) ) {
            $this->error('客户不存在');
            exit;
        }
        $data['status'] = 0;
        if( M('client')->where(['id' => $id])->save($data) ) {
            $this->success('客户已被删除');
        } else {
            $this->error('删除失败');
        }
    }

    public function multiDel() {
        $idArr = I('key',0 , 'intval');
        if (!is_array($idArr)) {
            $this->error('请选择要彻底删除的项');
        }
        $where = array('id' => array('in', $idArr));
        $data['status'] = 0;
        if( M('client')->where($where)->save($data) ) {
            $this->success('客户已被移入回收站');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 回收站
     */
    public function recycle() {
        $keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字
        $where = array();
        $condition = array();
        if (!empty($keyword)) {
            $condition['username'] = ['like', $keyword];
            $condition['full_name'] = ['like', '$keyword'];
            $condition['company'] = ['like', '$keyword'];
            $condition['_logic'] = 'OR';
            $where['_complex'] = $condition;
        }
        $where['status'] = 0;
        //分页
        import('ORG.Util.Page');
        $count = M('client')->where($where)->count();
        $page = new Page($count, 10);
        $limit = $page->firstRow. ',' .$page->listRows;
        $list = M('client')->where($where)->order('id')->limit($limit)->select();
//        $list = M('client')->where($where)->order('id')->limit($limit)->buildSql();
//        echo $list;exit;

        if( $list ) {
            foreach( $list as $k => $v ) {
                $list[$k]['company'] = mb_strlen($v['company'], 'utf-8') > 13 ? mb_substr($v['company'], 0, 13, 'utf-8').'...' : $v['company'];
            }
        }
        $group_list = M('clientGroup')->select();

        if( $group_list ) {
            $temp = array();
            foreach( $group_list as $k => $v ) {
                $temp[$v['id']] = $v['name'].'/<br />'.$v['en_name'];
            }
            $group_list = $temp;
        }
        $this->group_list = $group_list;

        $this->page = $page->show();
        $this->client_list = $list;
        $this->type = '回收站';
        $this->keyword = $keyword;

        $this->display();
    }

    /**
     * 撤销删除
     */
    public function revoke() {
        $id = I('get.id', 0, 'intval');
        $batchFlag = I('get.batchFlag', 0, 'intval');
        //批量删除
        if ($batchFlag) {
            $this->multiRevoke();
            return;
        }
        $client = M('client')->where(['id' => $id])->find();
        if( empty($client) ) {
            $this->error('客户不存在');
            exit;
        }
        $data['status'] = 1;
        if( M('client')->where(['id' => $id])->save($data) ) {
            $this->success('客户已被移出回收站');
        } else {
            $this->error('撤销失败');
        }
    }

    public function multiRevoke() {
        $idArr = I('key',0 , 'intval');
        if (!is_array($idArr)) {
            $this->error('请选择要彻底删除的项');
        }
        $where = array('id' => array('in', $idArr));
        $data['status'] = 1;
        if( M('client')->where($where)->save($data) ) {
            $this->success('客户已被移出回收站');
        } else {
            $this->error('撤销失败');
        }
    }


    /**
     * 彻底删除
     */
    public function remove() {
        $this->error('功能开发中');
        exit;
    }

}