<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2016/10/8
 * Time: 22:31
 */

class DeliveryAction extends BaseAction {

    public function index() {
        $where['client_id'] = session('hkwcd_user.user_id');
        $where['status'] = 1;
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        //分页
        import('ORG.Util.Page');
        $count = M('DeliveryAddress')->where($where)->count();

        $page = new Page($count, C('usercenter_page_count'));
        $limit = $page->firstRow. ',' .$page->listRows;

        $delivery_list =M('DeliveryAddress')->where($where)->limit($limit)->order('is_default desc, id asc')->select();
        if( $delivery_list ) {
            foreach( $delivery_list  as $k => $v ) {
                $delivery_list[$k]['index'] = $k+1;
            }
        }
        $this->assign('title', '发货地址列表');
        $this->page = $page->show();

        $this->assign('delivery_list',$delivery_list);// 赋值数据集
        $this->display(); // 输出模板
    }

    public function add() {
        $client_id = session('hkwcd_user.user_id');
        $client = M('Client')->where(['client_id' => $client_id])->find();
        $where = array('pid' => 0,'types'=>0);
        $country_list = M('country')->where($where)->order('sort,id')->select();

        $this->assign('country_list', $country_list);
        $this->assign('title', '添加发货地址');
        $this->assign('client', $client);
        $this->display(); // 输出模板
    }

    public function doAdd() {
        if( !IS_POST ) {
            exit;
        }
        $data['company'] = I('post.company', '', 'trim');
        $data['consignor'] = I('post.consignor', '', 'trim');
        $data['country_id'] = I('post.country', 0, 'intval');
        $data['state'] = I('post.state', '', 'trim');
        $data['city'] = I('post.city', '', 'trim');
        $data['detail_address'] = I('post.detail_address', '', 'trim');
        $data['mobile'] = I('post.mobile', '', 'trim');
        $data['phone'] = I('post.phone', '', 'trim');
        $data['postal_code'] = I('post.postal_code', '', 'trim');
        $data['certificate_num'] = I('post.certificate_num', '', 'trim');
        $data['certificate1_url'] = I('post.certificate1_url', '', 'trim');
        $data['certificate2_url'] = I('post.certificate2_url', '', 'trim');
        $data['is_default'] = I('post.is_default', 0, 'intval');

        if( empty($data['consignor']) ) {
            $this->error('请输入联系人');
        }
        if( $data['country_id'] <= 0 ) {
            $this->error('请选择国家');
        }
        if( empty($data['city']) ) {
            $this->error('请输入城市');
        }
        if( empty($data['detail_address']) ) {
            $this->error('请输入具体地址');
        }
        if( empty($data['postal_code']) ) {
            $this->error('请输入邮编');
        }
        if( empty($data['mobile']) ) {
            $this->error('请输入手机');
        }

        if( empty($data['certificate_num'])  ) {
            $this->error('请输入身份证号码');
        }

        if( empty($data['certificate1_url'])  ) {
            $this->error('请上传身份证正面');
        }

        if( empty($data['certificate2_url'])  ) {
            $this->error('请上传身份证反面');
        }

        $data['is_default'] = $data['is_default'] == 1 ? 1:0;

        $data['client_id'] = session('hkwcd_user.user_id');

        if( $data['is_default'] ) {
            $old_where = ['is_default' => 1, 'client_id' => $data['client_id']];
            M('DeliveryAddress')->where($old_where)->save(['is_default' => 0]);
        }
        $result = M('DeliveryAddress')->add($data);
        if( $result ) {
            //加水印
            $data['id'] = M('DeliveryAddress')->getLastInsID();
            $result1 = $this->_certificate_water($data);

            if( $result1 ) {
                $this->success('添加发货地址成功', U('Delivery/index'));
            } else {
                $this->error('系统繁忙，请稍后重试');
            }
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
        $data['consignor'] = I('post.consignor', '', 'trim');
        $data['country_id'] = I('post.country', 0, 'intval');
        $data['state'] = I('post.state', '', 'trim');
        $data['city'] = I('post.city', '', 'trim');
        $data['detail_address'] = I('post.detail_address', '', 'trim');
        $data['mobile'] = I('post.mobile', '', 'trim');
        $data['phone'] = I('post.phone', '', 'trim');
        $data['postal_code'] = I('post.postal_code', '', 'trim');
        $data['certificate_num'] = I('post.certificate_num', '', 'trim');
        $data['certificate1_url'] = I('post.certificate1_url', '', 'trim');
        $data['certificate2_url'] = I('post.certificate2_url', '', 'trim');
        $data['is_default'] = I('post.is_default', 0, 'intval');

        if( empty($data['consignor']) ) {
            $has_error = true;
            $errorMsg .= empty($errorMsg) ? '请输入联系人' : '<br />请输入联系人';
        }
        if( $data['country_id'] <= 0 ) {
            $has_error = true;
            $errorMsg .= empty($errorMsg) ? '请选择国家' : '<br />请选择国家';
        }
        if( empty($data['city']) ) {
            $has_error = true;
            $errorMsg .= empty($errorMsg) ? '请输入城市' : '<br />请输入城市';
        }
        if( empty($data['detail_address']) ) {
            $has_error = true;
            $errorMsg .= empty($errorMsg) ? '请输入具体地址' : '<br />请输入具体地址';
        }

        if( empty($data['postal_code']) ) {
            $has_error = true;
            $errorMsg .= empty($errorMsg) ? '请输入邮编' : '<br />请输入邮编';
        }

        if( empty($data['mobile'])  ) {
            $has_error = true;
            $errorMsg .= empty($errorMsg) ? '请输入手机' : '<br />请输入手机';
        }

        if( empty($data['certificate_num'])  ) {
            $has_error = true;
            $errorMsg .= empty($errorMsg) ? '请输入身份证号码' : '<br />请输入身份证号码';
        }

        if( empty($data['certificate1_url'])  ) {
            $has_error = true;
            $errorMsg .= empty($errorMsg) ? '请上传身份证正面' : '<br />请上传身份证正面';
        }

        if( empty($data['certificate2_url'])  ) {
            $has_error = true;
            $errorMsg .= empty($errorMsg) ? '请上传身份证反面' : '<br />请上传身份证反面';
        }

        if( $has_error ) {
            $response['msg'] = $errorMsg;
            echo json_encode($response);
            exit;
        }

        $data['is_default'] = $data['is_default'] == 1 ? 1:0;

        $data['client_id'] = session('hkwcd_user.user_id');

        if( $data['is_default'] ) {
            $old_where = ['is_default' => 1, 'client_id' => $data['client_id']];
            M('DeliveryAddress')->where($old_where)->save(['is_default' => 0]);
        }

        $result = M('DeliveryAddress')->add($data);
        if( $result ) {
            //加水印
            $data['id'] = M('DeliveryAddress')->getLastInsID();
            $result1 = $this->_certificate_water($data);

            if( $result1 ) {
                $response['code'] = 1;
                $data['id'] = $result;
                $response['data'] = $data;
            } else {
                $response['msg'] = '系统繁忙，请稍后重试';
            }
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
        $delivery = M('DeliveryAddress')->where($where)->find();
        if( empty($delivery) ) {
            $this->error('地址不存在');
        }
        $where = array('pid' => 0,'types'=>0);
        $country_list = M('country')->where($where)->order('sort,id')->select();
        foreach( $country_list as $k => $v ) {
            $country_list[$k]['id'] = intval($v['id']);
            if( $delivery['country_id'] == $v['id'] ) {
                $selected = $v['id'];
            }
        }
        $this->assign('selected', json_encode($selected));
        $this->assign('country_list', $country_list);
        $this->assign('delivery', $delivery);
        $this->assign('title', '编辑发货地址');
        $this->display(); // 输出模板
    }

    public function doEdit() {
        $id = I('post.id', 0, 'intval');
        $client_id = session('hkwcd_user.user_id');
        $where = ['id' => $id, 'status' => 1, 'client_id' => $client_id];
        $delivery = M('DeliveryAddress')->where($where)->find();
        if( empty($delivery) ) {
            $this->error('地址不存在');
        }
        $data['consignor'] = I('post.consignor', '', 'trim');
        $data['country_id'] = I('post.country', 0, 'intval');
        $data['state'] = I('post.state', '', 'trim');
        $data['city'] = I('post.city', '', 'trim');
        $data['detail_address'] = I('post.address', '', 'trim');
        $data['mobile'] = I('post.mobile', '', 'trim');
        $data['phone'] = I('post.phone', '', 'trim');
        $data['postal_code'] = I('post.postal_code', '', 'trim');
        $data['is_default'] = I('post.default', 0, 'intval');

        if( empty($data['consignor']) ) {
            $this->error('请输入发货人');
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
            M('DeliveryAddress')->where($old_where)->save(['is_default' => 0]);
        }
        $result = M('DeliveryAddress')->where($where)->save($data);
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
        $delivery = M('DeliveryAddress')->where($where)->find();
        if( empty($delivery) ) {
            $this->error('地址不存在');
        }
        $data = ['status' => 0];
        $result = M('DeliveryAddress')->where($where)->save($data);
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
        $delivery = M('DeliveryAddress')->where($where)->find();
        if( empty($delivery) ) {
            $this->error('地址不存在');
        }
        if( $delivery['is_default'] == 1 ) {
            $this->error('该地址已是默认地址');
        }
        $old_where = ['is_default' => 1, 'status' => 1, 'client_id' => $client_id];
        M('DeliveryAddress')->where($old_where)->save(['is_default' => 0]);
        $result = M('DeliveryAddress')->where($where)->save(['is_default' => 1]);
        if( is_numeric($result) ) {
            $this->success('设置成功');
        } else {
            $this->error('系统繁忙，请稍后重试');
        }
    }

    private function _certificate_water($data) {
        $update_data = [];
        $delivery_id = $data['id'];
        $username = session('hkwcd_user.username');
        if( !file_exists(THINK_PATH.'../uploads/client/'.$username.'/') ) {
            mkdir(THINK_PATH.'../uploads/client/'.$username.'/');
        }
        if( !file_exists(THINK_PATH.'../uploads/client/'.$username.'/delivery') ) {
            mkdir(THINK_PATH.'../uploads/client/'.$username.'/delivery');
        }
        //正面
        $url = realpath(THINK_PATH.'../'.$data['certificate1_url']);
        $ext_array = explode('.', $data['certificate1_url']);
        $ext1 = $ext_array[1];
        $save_name_1 = THINK_PATH.'../uploads/client/'.$username.'/delivery/'.$delivery_id.'_front.'.$ext1;
        $update_save_name_1 = '/uploads/client/'.$username.'/delivery/'.$delivery_id.'_front.'.$ext1;
        $result = $this->_add_water($url, $save_name_1);
        if( $result ) {
            $update_data['certificate1_url'] = $update_save_name_1;
        }
        @unlink($url);

        //反面
        $url = realpath(THINK_PATH.'../'.$data['certificate2_url']);
        $ext_array = explode('.', $data['certificate2_url']);
        $ext2 = $ext_array[1];
        $save_name_2 = THINK_PATH.'../uploads/client/'.$username.'/delivery/'.$delivery_id.'_back.'.$ext1;
        $update_save_name_2 = '/uploads/client/'.$username.'/delivery/'.$delivery_id.'_back.'.$ext2;
        $result = $this->_add_water($url, $save_name_2);
        if( $result ) {
            $update_data['certificate2_url'] = $update_save_name_2;
        }
        @unlink($url);
        $map = [
            'id' => $delivery_id,
        ];
        if( file_exists($save_name_1) && file_exists($save_name_2) ) {
//            $update_data['status'] = 1;
            M('DeliveryAddress')->where($map)->save($update_data);
            return true;
        } else {
            M('DeliveryAddress')->where($map)->delete();
            return false;
        }
    }


    private function _add_water($url, $savename) {
        import('ORG.Util.Image.ThinkImage');
        $shuiyin = realpath(THINK_PATH.'../Public/config/images/shuiyin.png');
        $image = new ThinkImage(THINKIMAGE_GD, $url);
        $result = $image->water($shuiyin);
        $result->save($savename);
        return true;
    }
}