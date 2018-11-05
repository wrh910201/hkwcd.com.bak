<?php
/**
 * Created by PhpStorm.
 * User: wrh42
 * Date: 2018/7/28
 * Time: 4:48
 */

class ProductAction extends BaseAction
{

    public $client;
    public $client_id;
    public $has_error = false;
    public $error_msg = '';

    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->client_id = session('hkwcd_user.user_id');
        $this->client = M('Client')->where(['status' => 1, 'id' => $this->client_id])->find();


    }

    public function index() {
        $where['client_id'] = $this->client_id;
        $where['status'] = 1;
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        //分页
        import('ORG.Util.Page');
        $count = M('ClientProduct')->where($where)->count();

        $page = new Page($count, C('usercenter_page_count'));
        $limit = $page->firstRow. ',' .$page->listRows;

        $product_list =M('ClientProduct')->where($where)->limit($limit)->order('id asc')->select();
        if( $product_list ) {
            foreach( $product_list  as $k => $v ) {
                $product_list[$k]['index'] = $k+1;
            }
        }
        $this->assign('title', '商品列表');
        $this->page = $page->show();

        $order_detail_unit = M("ProductUnit")
            ->select();
        if( $order_detail_unit ) {
            $temp = [];
            foreach( $order_detail_unit as $k => $v ) {
                $temp[$v["en_name"]] = $v["name"];
            }
            $order_detail_unit = $temp;
        }

        $this->assign('order_detail_unit', $order_detail_unit);
        $this->assign('product_list',$product_list);// 赋值数据集
        $this->assign("json_product_list", json_encode($product_list));
        $this->display(); // 输出模板
    }

    public function getList() {
        $where['client_id'] = $this->client_id;
        $where['status'] = 1;
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        //分页
        import('ORG.Util.Page');
        $count = M('ClientProduct')->where($where)->count();

        $page = new Page($count, C('usercenter_page_count'));
        $limit = $page->firstRow. ',' .$page->listRows;

        $page = I("page", 1, "intval");
        if( $page <= 0 ) {
            $page = 1;
        }
        $limit = C('usercenter_page_count');
        $offset = ($page - 1) * $limit;

        $product_list =M('ClientProduct')->where($where)->limit($offset, $limit)->order('id asc')->select();
        if( $product_list ) {
            foreach( $product_list  as $k => $v ) {
                $product_list[$k]['index'] = $k+1;
            }
        }
        $response = [
            "data" => $product_list,
            "total" => $count,
        ];
        echo json_encode($response);
        exit;

    }

    public function add() {
        $order_detail_unit = M("ProductUnit")
            ->select();
        if( $order_detail_unit ) {
            $temp = [];
            foreach( $order_detail_unit as $k => $v ) {
                $temp[$v["en_name"]] = $v["name"];
            }
            $order_detail_unit = $temp;
        }

        $this->assign('order_detail_unit', $order_detail_unit);
        $this->assign('title', '添加商品');
        $this->display(); // 输出模板
    }

    public function doAdd() {
        if( IS_POST ) {
            $data["product_name"] = I("product_name", "", "trim");
            $data["en_product_name"] = I("en_product_name", "", "trim");
            $data["goods_code"] = I("goods_code", "", "trim");
            $data["unit"] = I("unit", "", "trim");
            $data["single_declared"] = I("single_declared", "", "trim");
            $data["origin"] = I("origin", "", "trim");
            $data["texture"] = I("texture", "", "trim");

            if( empty($data["product_name"]) ) {
                $this->error('请输入中文品名');
            }
            if( empty($data["en_product_name"]) ) {
                $this->error('请输入英文品名');
            }
            if( empty($data["goods_code"]) ) {
                $this->error('请输入商品编号');
            }
            if( empty($data["unit"]) ) {
                $this->error('请输入单位');
            }
            if( empty($data["single_declared"]) ) {
                $this->error('请输入单件申报价值');
            }
            if( empty($data["origin"]) ) {
                $this->error('请输入原产地');
            }

            $data["client_id"] = $this->client_id;
            $result = M("ClientProduct")
                ->add($data);
            if( $result ) {
                $this->success('添加商品成功', U('Product/index'));
            } else {
                $this->error('系统繁忙，请稍后重试');
            }

        }
    }

    public function ajaxAdd() {
        if( IS_POST ) {
            $data["product_name"] = I("product_name", "", "trim");
            $data["en_product_name"] = I("en_product_name", "", "trim");
            $data["goods_code"] = I("goods_code", "", "trim");
            $data["unit"] = I("unit", "", "trim");
            $data["single_declared"] = I("single_declared", "", "trim");
            $data["origin"] = I("origin", "", "trim");
            $data["texture"] = I("texture", "", "trim");

            $has_error = false;
            $errorMsg = "";

            if( empty($data["product_name"]) ) {
                $has_error = true;
                $errorMsg .= empty($errorMsg) ? '请输入中文品名' : '<br />请输入中文品名';
            }
            if( empty($data["en_product_name"]) ) {
                $has_error = true;
                $errorMsg .= empty($errorMsg) ? '请输入英文品名' : '<br />请输入英文品名';
            }
            if( empty($data["goods_code"]) ) {
                $has_error = true;
                $errorMsg .= empty($errorMsg) ? '请输入商品编号' : '<br />请输入商品编号';
            }
            if( empty($data["unit"]) ) {
                $has_error = true;
                $errorMsg .= empty($errorMsg) ? '请输入单位' : '<br />请输入单位';
            }
            if( empty($data["single_declared"]) ) {
                $has_error = true;
                $errorMsg .= empty($errorMsg) ? '请输入单件申报价值' : '<br />请输入单件申报价值';
                $this->error('请输入单件申报价值');
            }
            if( empty($data["origin"]) ) {
                $has_error = true;
                $errorMsg .= empty($errorMsg) ? '请输入原产地' : '<br />请输入原产地';
            }

            if( $has_error ) {
                $response['msg'] = $errorMsg;
                echo json_encode($response);
                exit;
            }

            $data["client_id"] = $this->client_id;
            $result = M("ClientProduct")
                ->add($data);
            if( $result ) {
                $data["id"] = M("ClientProduct")->getLastInsID();
                $response['code'] = 1;
                $response['msg'] = '添加商品成功';
                $response['data'] = $data;
            } else {
                $response['msg'] = '系统繁忙，请稍后重试';
            }
            echo json_encode($response);
            exit;
        }
    }


    public function edit() {
        $id = I('get.id', 0, 'intval');
        $where = ['id' => $id, 'status' => 1, 'client_id' => $this->client_id];
        $product = M('ClientProduct')->where($where)->find();
        if( empty($product) ) {
            $this->error('商品不存在');
        }

        $order_detail_unit = M("ProductUnit")
            ->select();
        if( $order_detail_unit ) {
            $temp = [];
            foreach( $order_detail_unit as $k => $v ) {
                $temp[$v["en_name"]] = $v["name"];
            }
            $order_detail_unit = $temp;
        }

        $this->assign('order_detail_unit', $order_detail_unit);
        $this->assign('product', $product);
        $this->assign('title', '编辑商品');
        $this->display(); // 输出模板
    }

    public function doEdit() {
        if( IS_POST ) {

            $id = I('id', 0, 'intval');
            $where = ['id' => $id, 'status' => 1, 'client_id' => $this->client_id];
            $product = M('ClientProduct')->where($where)->find();
            if( empty($product) ) {
                $this->error('商品不存在');
            }

            $data["product_name"] = I("product_name", "", "trim");
            $data["en_product_name"] = I("en_product_name", "", "trim");
            $data["goods_code"] = I("goods_code", "", "trim");
            $data["unit"] = I("unit", "", "trim");
            $data["single_declared"] = I("single_declared", "", "trim");
            $data["origin"] = I("origin", "", "trim");
            $data["texture"] = I("texture", "", "trim");

            if( empty($data["product_name"]) ) {
                $this->error('请输入中文品名');
            }
            if( empty($data["en_product_name"]) ) {
                $this->error('请输入英文品名');
            }
            if( empty($data["goods_code"]) ) {
                $this->error('请输入商品编号');
            }
            if( empty($data["unit"]) ) {
                $this->error('请输入单位');
            }
            if( empty($data["single_declared"]) ) {
                $this->error('请输入单件申报价值');
            }
            if( empty($data["origin"]) ) {
                $this->error('请输入原产地');
            }

            $data["client_id"] = $this->client_id;
            $result = M("ClientProduct")->where(["id" => $id])
                ->save($data);
            if( $result ) {
                $this->success('编辑商品成功', U('Product/index'));
            } else {
                $this->error('系统繁忙，请稍后重试');
            }

        }

    }

    public function del() {
        $id = I('get.id', 0, 'intval');
        $client_id = session('hkwcd_user.user_id');
        $where = ['id' => $id, 'status' => 1, 'client_id' => $client_id];
        $product = M('ClientProduct')->where($where)->find();
        if( empty($product) ) {
            $this->error('商品不存在');
        }
        $data = ['status' => 0];
        $result = M('ClientProduct')->where($where)->save($data);
        if( is_numeric($result) ) {
            $this->success('删除成功');
        } else {
            $this->error('系统繁忙，请稍后重试');
        }
    }
}

