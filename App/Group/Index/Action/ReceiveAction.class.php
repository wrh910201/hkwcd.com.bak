<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2016/10/8
 * Time: 22:31
 */

class ReceiveAction extends BaseAction {

    public function index() {
        $where['client_id'] = session('hkwcd_user.user_id');
        $where['status'] = 1;
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $receive_list =M('ReceiveAddress')->where($where)->order('is_default desc, id asc')->select();
        if( $receive_list ) {
            foreach( $receive_list  as $k => $v ) {
                $receive_list[$k]['index'] = $k+1;
            }
        }
        $this->assign('title', '收货地址列表');
        $this->assign('receive_list',$receive_list);// 赋值数据集
        $this->display(); // 输出模板
    }

    public function add() {
        $where = array('pid' => 0,'types'=>0);
        $country_list = M('country')->where($where)->order('sort,id')->select();
        $this->assign('country_list', $country_list);
        $this->assign('title', '添加收货地址');
        $this->display(); // 输出模板
    }

    public function doAdd() {
        if( !IS_POST ) {
            exit;
        }
        $data['company'] = I('post.company', '', 'trim');
        $data['addressee'] = I('post.addressee', '', 'trim');
        $data['country_id'] = I('post.country', 0, 'intval');
        $data['state'] = I('post.state', '', 'trim');
        $data['city'] = I('post.city', '', 'trim');
        $data['detail_address'] = I('post.address', '', 'trim');
        $data['postal_code'] = I('post.postal_code', '', 'trim');
        $data['mobile'] = I('post.mobile', '', 'trim');
        $data['phone'] = I('post.phone', '', 'trim');
        $data['is_default'] = I('post.default', 0, 'intval');

        if( empty($data['company']) ) {
            $this->error('请输入公司');
        }

        if( empty($data['addressee']) ) {
            $this->error('请输入联系人');
        }
        if( $data['country_id'] <= 0 ) {
            $this->error('请选择国家');
        }
        if( empty($data['detail_address']) ) {
            $this->error('请输入具体地址');
        }
        if( empty($data['mobile']) && empty($data['phone']) ) {
            $this->error('手机与座机至少输入一个');
        }
        $data['is_default'] = $data['is_default'] == 1 ? 1:0;

        $data['client_id'] = session('hkwcd_user.user_id');

        if( $data['is_default'] ) {
            $old_where = ['is_default' => 1, 'status' => 1, 'client_id' => $data['client_id']];
            M('ReceiveAddress')->where($old_where)->save(['is_default' => 0]);
        }

        $result = M('ReceiveAddress')->add($data);
//        echo M('ReceiveAddress')->getLastSql();exit;
        if( $result ) {
            $this->success('添加收货地址成功', U('Receive/index'));
        } else {
            $this->error('系统繁忙，请稍后重试');
        }
    }

    public function ajaxAdd() {
        if( !IS_AJAX ) {
            exit;
        }
        $response = [
            'code' => -1,
            'msg' => '',
        ];
        $has_error = false;
        $errorMsg = '';

        $data['company'] = I('post.company', '', 'trim');
        $data['addressee'] = I('post.addressee', '', 'trim');
        $data['country_id'] = I('post.country', 0, 'intval');
        $data['state'] = I('post.state', '', 'trim');
        $data['city'] = I('post.city', '', 'trim');
        $data['detail_address'] = I('post.detail_address', '', 'trim');
        $data['mobile'] = I('post.mobile', '', 'trim');
        $data['phone'] = I('post.phone', '', 'trim');
        $data['postal_code'] = I('post.postal_code', '', 'trim');
        $data['is_default'] = I('post.is_default', 0, 'intval');

        if( empty($data['company']) ) {
            $has_error = true;
            $errorMsg .= empty($errorMsg) ? '请输入公司' : '<br />请输入公司';
        }

        if( empty($data['addressee']) ) {
            $has_error = true;
            $errorMsg .= empty($errorMsg) ? '请输入联系人' : '<br />请输入联系人';
        }
        if( $data['country_id'] <= 0 ) {
            $has_error = true;
            $errorMsg .= empty($errorMsg) ? '请选择国家' : '<br />请选择国家';
        }
        if( empty($data['detail_address']) ) {
            $has_error = true;
            $errorMsg .= empty($errorMsg) ? '请输入具体地址' : '<br />请输入具体地址';
        }
        if( empty($data['mobile']) && empty($data['phone']) ) {
            $has_error = true;
            $errorMsg .= empty($errorMsg) ? '手机与座机至少输入一个' : '<br />手机与座机至少输入一个';
        }
        if( $has_error ) {
            $response['msg'] = $errorMsg;
            echo json_encode($response);
            exit;
        }

        $data['is_default'] = $data['is_default'] == 1 ? 1:0;

        $data['client_id'] = session('hkwcd_user.user_id');

        if( $data['is_default'] ) {
            $old_where = ['is_default' => 1, 'status' => 1, 'client_id' => $data['client_id']];
            M('ReceiveAddress')->where($old_where)->save(['is_default' => 0]);
        }

        $result = M('ReceiveAddress')->add($data);
        if( $result ) {
            $response['code'] = 1;
            $data['id'] = $result;
            $response['data'] = $data;
        } else {
            $response['msg'] = '系统繁忙，请稍后重试';
        }
        echo json_encode($response);
        exit;
    }

    public function edit() {
        $id = I('get.id', 0, 'intval');
        $client_id = session('hkwcd_user.user_id');
        $where = ['id' => $id, 'status' => 1, 'client_id' => $client_id];
        $receive = M('ReceiveAddress')->where($where)->find();
        if( empty($receive) ) {
            $this->error('地址不存在');
        }
        $where = array('pid' => 0,'types'=>0);
        $country_list = M('country')->where($where)->order('sort,id')->select();
        foreach( $country_list as $k => $v ) {
            $country_list[$k]['id'] = intval($v['id']);
            if( $receive['country_id'] == $v['id'] ) {
                $selected = $v['id'];
            }
        }
        $this->assign('selected', json_encode($selected));
        $this->assign('country_list', $country_list);
        $this->assign('receive', $receive);
        $this->assign('title', '编辑收货地址');
        $this->display(); // 输出模板
    }

    public function doEdit() {
        $id = I('post.id', 0, 'intval');
        $client_id = session('hkwcd_user.user_id');
        $where = ['id' => $id, 'status' => 1, 'client_id' => $client_id];
        $receive = M('ReceiveAddress')->where($where)->find();
        if( empty($receive) ) {
            $this->error('地址不存在');
        }
        $data['company'] = I('post.company', '', 'trim');
        $data['addressee'] = I('post.addressee', '', 'trim');
        $data['country_id'] = I('post.country', 0, 'intval');
        $data['state'] = I('post.state', '', 'trim');
        $data['city'] = I('post.city', '', 'trim');
        $data['detail_address'] = I('post.address', '', 'trim');
        $data['postal_code'] = I('post.postal_code', '', 'trim');
        $data['mobile'] = I('post.mobile', '', 'trim');
        $data['phone'] = I('post.phone', '', 'trim');
        $data['is_default'] = I('post.default', 0, 'intval');

        if( empty($data['company']) ) {
            $this->error('请输入公司');
        }

        if( empty($data['addressee']) ) {
            $this->error('请输入联系人');
        }
        if( $data['country_id'] <= 0 ) {
            $this->error('请选择国家');
        }
        if( empty($data['detail_address']) ) {
            $this->error('请输入具体地址');
        }
        if( empty($data['mobile']) && empty($data['phone']) ) {
            $this->error('手机与座机至少输入一个');
        }
        $data['is_default'] = $data['is_default'] == 1 ? 1:0;

        $data['client_id'] = session('hkwcd_user.user_id');
        if( $data['is_default'] == 1 ) {
            $old_where = ['is_default' => 1, 'status' => 1, 'client_id' => $client_id];
            M('ReceiveAddress')->where($old_where)->save(['is_default' => 0]);
        }
        $result = M('ReceiveAddress')->where($where)->save($data);
        if( is_numeric($result) ) {
            $this->success('编辑成功');
        } else {
            $this->error('系统繁忙，请稍后重试');
        }
    }

    public function del() {
        $id = I('get.id', 0, 'intval');
        $client_id = session('hkwcd_user.user_id');
        $where = ['id' => $id, 'status' => 1, 'client_id' => $client_id];
        $receive = M('ReceiveAddress')->where($where)->find();
        if( empty($receive) ) {
            $this->error('地址不存在');
        }
        $result = M('ReceiveAddress')->where($where)->limit('1')->delete();
        if( is_numeric($result) ) {
            $this->success('删除成功');
        } else {
            $this->error('系统繁忙，请稍后重试');
        }
    }

    public function setDefault() {
        $id = I('get.id', 0, 'intval');
        $client_id = session('hkwcd_user.user_id');
        $where = ['id' => $id, 'status' => 1, 'client_id' => $client_id];
        $receive = M('ReceiveAddress')->where($where)->find();
        if( empty($receive) ) {
            $this->error('地址不存在');
        }
        if( $receive['is_default'] == 1 ) {
            $this->error('该地址已是默认地址');
        }
        $old_where = ['is_default' => 1, 'status' => 1, 'client_id' => $client_id];
        M('ReceiveAddress')->where($old_where)->save(['is_default' => 0]);
        $result = M('ReceiveAddress')->where($where)->save(['is_default' => 1]);
        if( is_numeric($result) ) {
            $this->success('设置成功');
        } else {
            $this->error('系统繁忙，请稍后重试');
        }
    }
}