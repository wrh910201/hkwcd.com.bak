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
            $condition['username'] = ['like', '%'.$keyword.'%'];
            $condition['full_name'] = ['like', '%'.$keyword.'%'];
            $condition['company'] = ['like', '%'.$keyword.'%'];
            $condition['_logic'] = 'OR';
            $where['_complex'] = $condition;
        }
        $where['status'] = 1;
        //分页
        import('ORG.Util.Page');
        $count = M('client')->where($where)->count();

        $page = new Page($count, 10);
        $limit = $page->firstRow. ',' .$page->listRows;
        $list = M('client')->where($where)->order('id desc')->limit($limit)->select();
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
            $data['en_company'] = I('post.en_company', '', 'htmlspecialchars,trim');
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
            $data['single_country'] = I('post.single_country', 0, 'intval');
            $data['single_country_id'] = I('post.single_country_id', 0, 'intval');

            $data['single_country'] = $data['single_country'] == 1 ? 1 : 0;


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

            if( $data['single_country'] == 1 ) {
                if( empty($data['single_country_id']) ) {
                    $this->error('请选择经营国家');
                }
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
//        var_dump($client);exit;
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
        $data['en_company'] = I('post.en_company', '', 'htmlspecialchars,trim');
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

        $data['single_country'] = I('post.single_country', 0, 'intval');
        $data['single_country_id'] = I('post.single_country_id', 0, 'intval');

        $data['single_country'] = $data['single_country'] == 1 ? 1 : 0;

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

        if( $data['single_country'] == 1 ) {
            if( empty($data['single_country_id']) ) {
                $this->error('请选择经营国家');
            }
        }

        $data['is_person'] = 0;
        if( empty($data['company']) ) {
            $data['is_person'] = 1;
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
        $country_name = '';
        if( $client['single_country'] == 1 ) {
            $where = [
                'cp.type' => $type,
                'cp.channel_id' => $channel_id,
                'cp.client_id' => $client['id'],
                'cp.status' => 1,
            ];
            $price = M('ClientPrice')
                ->alias('cp')
                ->field('cp.*, c.name, c.ename')
                ->join('left join hx_country as c on c.id = cp.country_id')
                ->where($where)
                ->find();
            $country_name = $price['name'].'/'.$price['ename'];
        } else {
            $where = [
                'type' => $type,
                'channel_id' => $channel_id,
                'group_id' => $client['group_id'],
                'status' => 1,
            ];
            $price = M('ChannelMap')->where($where)->find();
        }
//        echo M('ClientPrice')->getLastSql();exit;
        $content = $price['content'] ? json_decode($price['content'], true) : null;
//        var_dump($content);exit;
        $price = [];
        if( $content ) {
            foreach( $content as $k => $row ) {
                if( $client['single_country'] == 0 ) {
                    if ($k == 1) {
                        $region_list = $row;
                        continue;
                    }
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
        $this->assign('country_name', $country_name);
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

    public function uploadCertificate() {
        $id = I('post.id', 0, 'intval');
        $client = M('client')->where(['id' => $id])->find();
        if( empty($client) ) {
            $this->error('客户不存在');
            exit;
        }
        import('ORG.Util.Image.ThinkImage');
        $savename_prefix = THINK_PATH.'../uploads/client/'.$client['username'];

        if( !file_exists(THINK_PATH.'../uploads') ) {
            mkdir(THINK_PATH.'../uploads');
        }

        if( !file_exists(THINK_PATH.'../uploads/client') ) {
            mkdir(THINK_PATH.'../uploads/client');
        }

        if( !file_exists(THINK_PATH.'../uploads/client/'.$client['username']) ) {
            mkdir(THINK_PATH.'../uploads/client/'.$client['username']);
        }

        if( !file_exists($savename_prefix) ) {
            mkdir($savename_prefix);
        }

        $certificate1 = I('post.certificate1', '', 'trim');
        $ext_array = explode('.', $certificate1);
        $certificate1 = realpath(THINK_PATH.'..'.$certificate1);
        if( strpos($certificate1, '!') ) {
            $ext = substr($ext_array[1], 0, 3);
        } else {
            $ext = $ext_array[1];
        }
        $ext1 = $ext;

        $shuiyin = realpath(THINK_PATH.'../Public/config/images/shuiyin.png');
        $savename = $savename_prefix.'/certificate1/'.$client['id'].'.'.$ext;
        $savename1 = $savename;
        if( !file_exists($savename_prefix.'/certificate1') ) {
            mkdir($savename_prefix.'/certificate1');
        }
        $image = new ThinkImage(THINKIMAGE_GD, $certificate1);
        $result = $image->water($shuiyin);
        $result->save($savename);

        $certificate2 = I('post.certificate2', '', 'trim');
        $ext_array = explode('.', $certificate2);
        $certificate2 = realpath(THINK_PATH.'..'.$certificate2);
        if( strpos($certificate2, '!') ) {
            $ext = substr($ext_array[1], 0, 3);
        } else {
            $ext = $ext_array[1];
        }
        $ext2 = $ext;
        $shuiyin = realpath(THINK_PATH.'../Public/config/images/shuiyin.png');
        $savename = $savename_prefix.'/certificate2/'.$client['id'].'.'.$ext;
        $savename2 = $savename;
        if( !file_exists($savename_prefix.'/certificate2') ) {
            mkdir($savename_prefix.'/certificate2');
        }
        $image = new ThinkImage(THINKIMAGE_GD, $certificate2);
        $result = $image->water($shuiyin);
        $result->save($savename);


        $map = [
            'id' => $client['id'],
        ];
        $data = [
            'certificate1' => '/uploads/client/'.$client['username'].'/certificate1/'.$client['id'].'.'.$ext1,
            'certificate2' => '/uploads/client/'.$client['username'].'/certificate2/'.$client['id'].'.'.$ext2,
        ];
        $result = M('Client')->where($map)->save($data);
        if( is_numeric($result) ) {
            $this->success('资料上传成功');
        } else {
            $this->error('资料上传失败');
        }
    }

    public function certificate() {
        $id = I('get.id', 0, 'intval');
        $client = M('client')->where(['id' => $id])->find();
        if( empty($client) ) {
            $this->error('客户不存在');
            exit;
        }
        $this->assign('client', $client);
        $this->assign('type', '身份证');
        $this->display();
    }

    public function license() {
        $id = I('get.id', 0, 'intval');
        $client = M('client')->where(['id' => $id])->find();
        if( empty($client) ) {
            $this->error('客户不存在');
            exit;
        }
        $this->assign('client', $client);
        $this->assign('type', '营业执照');
        $this->display();
    }

    public function uploadLicense() {
        $id = I('post.id', 0, 'intval');
        $client = M('client')->where(['id' => $id])->find();
        if( empty($client) ) {
            $this->error('客户不存在');
            exit;
        }
        $license = I('post.license', '', 'trim');
        $ext_array = explode('.', $license);
        $license = realpath(THINK_PATH.'..'.$license);
        if( strpos($license, '!') ) {
            $ext = substr($ext_array[1], 0, 3);
        } else {
            $ext = $ext_array[1];
        }
        $shuiyin = realpath(THINK_PATH.'../Public/config/images/shuiyin.png');
        $savename_prefix = THINK_PATH.'../uploads/client/'.$client['username'];

        if( !file_exists(THINK_PATH.'../uploads') ) {
            mkdir(THINK_PATH.'../uploads');
        }

        if( !file_exists(THINK_PATH.'../uploads/client') ) {
            mkdir(THINK_PATH.'../uploads/client');
        }

        if( !file_exists(THINK_PATH.'../uploads/client/'.$client['username']) ) {
            mkdir(THINK_PATH.'../uploads/client/'.$client['username']);
        }

        if( !file_exists($savename_prefix) ) {
            mkdir($savename_prefix);
        }
        $savename = $savename_prefix.'/license/'.$client['id'].'.'.$ext;
        if( !file_exists($savename_prefix.'/license') ) {
            mkdir($savename_prefix.'/license');
        }
        import('ORG.Util.Image.ThinkImage');
        $image = new ThinkImage(THINKIMAGE_GD, $license);
        $result = $image->water($shuiyin);
        $result->save($savename);

        $map = [
            'id' => $client['id'],
        ];
        $data = [
            'license' => '/uploads/client/'.$client['username'].'/license/'.$client['id'].'.'.$ext,
        ];
        $result = M('Client')->where($map)->save($data);
        if( is_numeric($result) ) {
            $this->success('资料上传成功');
        } else {
            $this->error('资料上传失败');
        }
    }

    public function delivery() {
        $id = I('get.id', 0, 'intval');
        $client = M('client')->where(['id' => $id])->find();
        if( empty($client) ) {
            $this->error('客户不存在');
            exit;
        }
        $map = [
            'client_id' => $id
        ];
        //分页
        import('ORG.Util.Page');
        $count = M('DeliveryAddress')->where($map)->count();

        $page = new Page($count, 10);
        $limit = $page->firstRow. ',' .$page->listRows;

        $delivery_list = M('DeliveryAddress')
            ->alias('d')
            ->field('d.*, c.name as country_name, c.ename as country_ename')
            ->join('hx_country as c on d.country_id = c.id')
            ->where($map)
            ->limit($limit)
            ->select();

        $this->assign('type', $client['full_name'].'-发货地址列表');
        $this->assign('delivery_list', $delivery_list);
        $this->assign('page', $page->show());
        $this->display();
    }

    public function cert() {
        $id = I("id");
        $delivery = M('DeliveryAddress')->where(['id' => $id])->find();
        $result = [
            "title"=> '身份证',
            "id" => $id,
            "start" => 0,
            "data" => [
                ["alt" => "正面", "pid" => 0, "src" => "{$delivery['certificate1_url']}", "thumb" => "{$delivery['certificate1_url']}"],
                ["alt" => "反面", "pid" => 1, "src" => "{$delivery['certificate2_url']}", "thumb" => "{$delivery['certificate2_url']}"],
            ],
        ];
        echo json_encode($result);
        exit;
    }

    public function receive() {
        $id = I('get.id', 0, 'intval');
        $client = M('client')->where(['id' => $id])->find();
        if( empty($client) ) {
            $this->error('客户不存在');
            exit;
        }
        $map = [
            'client_id' => $id
        ];
        //分页
        import('ORG.Util.Page');
        $count = M('ReceiveAddress')->where($map)->count();

        $page = new Page($count, 10);
        $limit = $page->firstRow. ',' .$page->listRows;

        $receive_list = M('ReceiveAddress')
            ->alias('d')
            ->field('d.*, c.name as country_name, c.ename as country_ename')
            ->join('hx_country as c on d.country_id = c.id')
            ->where($map)
            ->limit($limit)
            ->select();

        $this->assign('type', $client['full_name'].'-收货列表');
        $this->assign('receive_list', $receive_list);
        $this->assign('page', $page->show());
        $this->display();
    }

    public function import() {
        $id = I('get.id', 0, 'intval');
        $client = M('Client')->where(['id' => $id])->find();
        if( empty($client) || $client['status'] == 0 ) {
            $this->error('客户不存在');
            exit;
        }
        $this->assign('client', $client);

        $channel_list = M('Channel')->where(['status' => 1])->select();
        if( empty($channel_list) ) {
            $this->error('请先添加渠道', U(GROUP_NAME. '/Channel/add'));
        }
        $this->assign('channel_list', $channel_list);

        $where = array('pid' => 0,'types'=>0);
        $country_list = M('country')->where($where)->order('sort,id')->select();
        $this->assign('country_list', $country_list);

        $this->type = '设置价格';
        $this->display();
    }

    public function doImport() {
        $id = I('post.id', 0, 'intval');
        $client = M('Client')->where(['id' => $id])->find();
        if( empty($client) || $client['status'] == 0 ) {
            $this->error('客户不存在');
            exit;
        }
        $data['channel_id'] = I('post.channel_id', 0, 'intval');
        $channel = M('Channel')->where(['id' => $data['channel_id']])->find();
        if( empty($channel) || $channel['status'] == 0 ) {
            $this->error('渠道不存在');
            exit;
        }

        $data['country_id'] = I('post.country_id', 0, 'intval');
        $data['has_extra_fee'] = I('post.has_extra_fee', 0, 'intval');
        $data['has_extra_fee'] = $data['has_extra_fee'] == 1 ? 1 : 0;

        $path = I('post.url', '', 'trim');

        $model = new Model;
        $model->startTrans();
        $transaction = true;

        $read_result = $this->_readPackageFromExcel($path);
        if( $read_result['code'] == 0 ) {
            @unlink($_SERVER['DOCUMENT_ROOT'].$path);
            $this->error('导入包裹价格时，'.$read_result['msg']);
        }
        $data['content'] = $read_result['data'];
        $data['type'] = 2;
        $data['content'] = json_encode($data['content']);
        $data['client_id'] = $id;
        $where = [
            'client_id' => $id,
            'channel_id' => $data['channel_id'],
            'type' => 2,
        ];
        $exists = M('ClientPrice')->where($where)->find();
        if( $exists ) {
            $price_id = $exists['id'];
            $result = M('ClientPrice')->where($where)->save(['content' => $data['content']]);
            if( !is_numeric($result) ) {
                $transaction = false;
            }
        } else {
            $result = M('ClientPrice')->add($data);
            $price_id = M('ClientPrice')->getLastInsID();
            if( !$result ){
                $transaction = false;
            }
        }
//        var_dump($read_result['data']);
//        $model->rollback();exit;
//        $price_data = [];
        if( $transaction ) {
            foreach( $read_result['data'] as $i => $row ) {
                foreach( $row as $j => $v ) {
//                    if( $i == 1 ) {
//                        $region = M('Region')->where(['alias' => $v])->find();
//                        $price_data[$j+1] = $region['id'];
//                        continue;
//                    }
                    if( $j == 0 ) {
                        if (is_numeric(strpos($v, '{'))) {
                            $temp = explode('{', $v);
                            $offset = $temp[0];
                            $per_kilo = explode('}', $temp[1]);
                            $per_kilo = $per_kilo[0];
                            if( strpos($offset, '~') === false ) {
                                $insert_data['status'] = 3;
                                $insert_data['max_weight'] = $v;
                                $insert_data['min_weight'] = 0;
                                $insert_data['per_kilo'] = $per_kilo;
                            } else {
                                $temp2 = explode('~', $temp[0]);
                                $insert_data['status'] = 2;
                                $insert_data['min_weight'] = $temp2[0];
                                $insert_data['max_weight'] = $temp2[1];
                                $insert_data['per_kilo'] = $per_kilo;
                            }
                        } else {
                            $insert_data['status'] = 1;
                            $insert_data['min_weight'] = $v;
                            $insert_data['max_weight'] = 0;
                        }
                        continue;
                    }
                    $insert_data['price'] = $v;
                    $insert_data['price_id'] = $price_id;
                    $insert_data['country_id'] = $data['country_id'];
                    $insert_result = M('ClientPriceDetail')->add($insert_data);
                    if( !$insert_result ) {
                        $transaction = false;
                        break;
                    }
                }
                if( !$transaction ) {
                    break;
                }
            }
        }
//        echo $model->getLastSql();
//        $model->rollback();exit;

        $read_result = $this->_readDocumentFromExcel($path);
        if( $read_result['code'] == 0 ) {
            $model->rollback();
            @unlink($_SERVER['DOCUMENT_ROOT'].$path);
            $this->error('导入文件价格时，'.$read_result['msg']);
        }
        $data['content'] = $read_result['data'];
        $data['type'] = 1;
        $data['content'] = json_encode($data['content']);
        $data['client_id'] = $id;
        $where = [
            'client_id' => $id,
            'channel_id' => $data['channel_id'],
            'type' => 1,
        ];
        $exists = M('ClientPrice')->where($where)->find();
        if( $exists ) {
            $price_id = $exists['id'];
            $result = M('ClientPrice')->where($where)->save(['content' => $data['content']]);
            if( !is_numeric($result) ) {
                $transaction = false;
            }
        } else {
            $result = M('ClientPrice')->add($data);
            $price_id = M('ClientPrice')->getLastInsID();
            if( !$result ){
                $transaction = false;
            }
        }

        if( $transaction ) {
            foreach( $read_result['data'] as $i => $row ) {
                foreach( $row as $j => $v ) {
//                    if( $i == 1 ) {
//                        $region = M('Region')->where(['alias' => $v])->find();
//                        $price_data[$j+1] = $region['id'];
//                        continue;
//                    }
                    if( $j == 0 ) {
                        if (is_numeric(strpos($v, '{'))) {
                            $temp = explode('{', $v);
                            $offset = $temp[0];
                            $per_kilo = explode('}', $temp[1]);
                            $per_kilo = $per_kilo[0];
                            if( strpos($offset, '~') === false ) {
                                $insert_data['status'] = 3;
                                $insert_data['max_weight'] = $v;
                                $insert_data['min_weight'] = 0;
                                $insert_data['per_kilo'] = $per_kilo;
                            } else {
                                $temp2 = explode('~', $temp[0]);
                                $insert_data['status'] = 2;
                                $insert_data['min_weight'] = $temp2[0];
                                $insert_data['max_weight'] = $temp2[1];
                                $insert_data['per_kilo'] = $per_kilo;
                            }
                        } else {
                            $insert_data['status'] = 1;
                            $insert_data['min_weight'] = $v;
                            $insert_data['max_weight'] = 0;
                        }
                        continue;
                    }
                    $insert_data['price'] = $v;
                    $insert_data['price_id'] = $price_id;
                    $insert_data['country_id'] = $data['country_id'];
                    $insert_result = M('ClientPriceDetail')->add($insert_data);
                    if( !$insert_result ) {
                        $transaction = false;
                        break;
                    }
                }
                if( !$transaction ) {
                    break;
                }
            }
        }

        @unlink($_SERVER['DOCUMENT_ROOT'].$path);

        if( $transaction ) {
            $model->commit();
            $this->success('价格导入成功');
        } else {
            $model->rollback();
            $this->error('价格导入失败');
        }
    }

    private function _readPackageFromExcel($path) {
        vendor('PHPExcel.Classes.PHPExcel');
        $filename = $_SERVER['DOCUMENT_ROOT'].$path;
        if( !file_exists($filename) ) {
            @unlink($filename);
            $this->error('请上传Excel文件,后缀为xls');
        }
        $type = substr($filename, strrpos($filename, '.')+1);
        if( !($type == 'xls') ) {
            @unlink($filename);
            $this->error('请上传Excel文件,后缀为xls');
        }
//        echo $filename;exit;
        $phpExcel = new PHPExcel();
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);
        //包裹
        $objPHPExcel->setActiveSheetIndex(0);
        $objWorksheet = $objPHPExcel->getActiveSheet();

        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = [];
        for ($row = 1; $row <= $highestRow; $row++) {
            $error_region = '';
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] =(string)trim($objWorksheet->getCellByColumnAndRow($col, $row)->getValue());
            }
//            if( $error_region != '' ) {
//                $error_region .= '】不存在';
//            }
            if( $error_region ) {
//                @unlink($filename);
//                $this->error($error_region);
//                exit;
                return ['code' => 0, 'msg' => $error_region];
            }
        }
//        var_dump($excelData);exit;
        return ['code' => 1, 'data' => $excelData];
    }

    private function _readDocumentFromExcel($path) {
        vendor('PHPExcel.Classes.PHPExcel');
        $filename = $_SERVER['DOCUMENT_ROOT'].$path;
        if( !file_exists($filename) ) {
            @unlink($filename);
            $this->error('请上传Excel文件,后缀为xls');
        }
        $type = substr($filename, strrpos($filename, '.')+1);
        if( !($type == 'xls') ) {
            @unlink($filename);
            $this->error('请上传Excel文件,后缀为xls');
        }
//        echo $filename;exit;
        $phpExcel = new PHPExcel();
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);
        //文件
        $objPHPExcel->setActiveSheetIndex(1);
        $objWorksheet = $objPHPExcel->getActiveSheet();

        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = [];
        for ($row = 1; $row <= $highestRow; $row++) {
            $error_region = '';
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] =(string)trim($objWorksheet->getCellByColumnAndRow($col, $row)->getValue());
            }
            if( $error_region ) {
//                @unlink($filename);
//                $this->error($error_region);
//                exit;
                return ['code' => 0, 'msg' => $error_region];
            }
        }
//        var_dump($excelData);exit;
        return ['code' => 1, 'data' => $excelData];
    }

}