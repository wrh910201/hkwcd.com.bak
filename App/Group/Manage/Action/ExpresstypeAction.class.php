<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2016/10/21
 * Time: 17:00
 */

class ExpresstypeAction extends CommonAction {

    public function index() {
        $keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字
        $where = array();
        $condition = array();
        if (!empty($keyword)) {
            $condition['name'] = ['like', $keyword];
            $condition['type'] = ['like', $keyword];
            $condition['_logic'] = 'OR';
            $where['_complex'] = $condition;
        }
        $where['status'] = 1;
        //分页
        import('ORG.Util.Page');
        $count = M('ExpressType')->where($where)->count();

        $page = new Page($count, 10);
        $limit = $page->firstRow. ',' .$page->listRows;
        $list = M('ExpressType')->where($where)->order('id')->limit($limit)->select();

        $this->page = $page->show();
        $this->express_list = $list;
        $this->type = '物流列表';
        $this->keyword = $keyword;

        $this->display();
    }

    public function add() {
        $this->type = '添加物流';
        $this->display();
    }

    public function doAdd() {
        if( !IS_POST ) {
            exit;
        }
        $name = I('post.name', '', 'trim');
        $type = I('post.type', '', 'trim');
        if( empty($name) ) {
            $this->error('请输入名称');
        }

        if( empty($type) ) {
            $this->error('请输入编码');
        }

        $data['name'] = $name;
        $data['type'] = $type;

        $result = M('ExpressType')->add($data);
        if( is_numeric($result) ) {
            $this->success('添加物流成功', U('Expresstype/index'));
        } else {
            $this->error('添加物流失败');
        }
    }

    public function edit() {
        $id = I('get.id', 0, 'intval');
        $express = M('ExpressType')->where(['id' => $id])->find();
        if( empty($express) ) {
            $this->error('物流不存在');
        }
        $this->assign('express', $express);

        $this->type = '编辑物流';
        $this->display();
    }

    public function doEdit() {
        $id = I('post.id', 0, 'intval');
        $express = M('ExpressType')->where(['id' => $id])->find();
        if( empty($express) ) {
            $this->error('物流不存在');
        }
        $name = I('post.name', '', 'trim');
        $type = I('post.type', '', 'trim');
        if( empty($name) ) {
            $this->error('请输入名称');
        }

        if( empty($type) ) {
            $this->error('请输入编码');
        }

        $data['name'] = $name;
        $data['type'] = $type;

        $result = M('ExpressType')->where(['id' => $id])->save($data);
        if( is_numeric($result) ) {
            $this->success('编辑物流成功', U('Expresstype/index'));
        } else {
            $this->error('编辑物流失败');
        }
    }

    public function del() {
        $id = I('get.id', 0, 'intval');
//        $batchFlag = I('get.batchFlag', 0, 'intval');
//        //批量删除
//        if ($batchFlag) {
//            $this->multiDel();
//            return;
//        }

        $express = M('ExpressType')->where(['id' => $id])->find();
        if( empty($express) ) {
            $this->error('物流不存在');
            exit;
        }
        $data['status'] = 0;
        if( M('ExpressType')->where(['id' => $id])->delete() ) {
            $this->success('物流已被删除');
        } else {
            $this->error('删除失败');
        }
    }
}