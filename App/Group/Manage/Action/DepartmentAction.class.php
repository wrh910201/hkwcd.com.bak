<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2017/2/27
 * Time: 22:14
 */

class DepartmentAction extends CommonContentAction
{

    public function index() {
        $keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字
        $where = array();
        if (!empty($keyword)) {
            $where['department_name'] = ['like', "%".$keyword."%"];
        }
        $where['status'] = 1;
        //分页
        import('ORG.Util.Page');
        $count = M('Department')->where($where)->count();

        $page = new Page($count, 10);
        $limit = $page->firstRow. ',' .$page->listRows;
        $list = M('Department')->where($where)->order('id')->limit($limit)->select();

        $this->page = $page->show();
        $this->department_list = $list;
        $this->type = '部门列表';
        $this->keyword = $keyword;

        $this->display();
    }

    public function add() {
        $this->type = '添加部门';
        $this->display();
    }

    public function doAdd() {
        $department_name = I('department_name', '', 'trim');
        if( empty($department_name) ) {
            $this->error('请输入部门名称');
        }
        $data = [
            'department_name' => $department_name,
        ];
        $result = M('Department')->add($data);
        if( $result ) {
            $this->success('添加部门成功',U('Department/index'));
        } else {
            $this->error('添加部门失败');
        }
    }

    public function edit() {
        $id = I('id', 0, 'intval');
        $department = M('Department')->where(['id' => $id, 'status' => 1])->find();
        if( empty($department) ) {
            $this->error('部门不存在');
        }
        $this->assign('department', $department);
        $this->type = '部门列表';
        $this->display();
    }

    public function doEdit() {
        $id = I('id', 0, 'intval');
        $department = M('Department')->where(['id' => $id, 'status' => 1])->find();
        if( empty($department) ) {
            $this->error('部门不存在');
        }
        $department_name = I('department_name', '', 'trim');
        if( empty($department_name) ) {
            $this->error('请输入部门名称');
        }
        $data = [
            'department_name' => $department_name,
        ];
        $result = M('Department')->where(['id' => $id])->save($data);
        if( is_numeric($result) ) {
            $this->success('编辑部门成功');
        } else {
            $this->error('编辑部门失败');
        }
    }

    public function del() {
        $id = I('id', 0, 'intval');
        $batchFlag = I('get.batchFlag', 0, 'intval');
        //批量删除
        if ($batchFlag) {
            $this->multiDel();
            return;
        }
        $department = M('Department')->where(['id' => $id, 'status' => 1])->find();
        if( empty($department) ) {
            $this->error('部门不存在');
        }
        $result = M('Department')->where(['id' => $id])->save(['status' => 0]);
        if( $result ) {
            $this->success('删除部门成功');
        } else {
            $this->error('删除部门失败');
        }
    }

    public function multiDel() {
        $idArr = I('key',0 , 'intval');
        if (!is_array($idArr)) {
            $this->error('请选择要彻底删除的项');
        }
        $where = array('id' => array('in', $idArr));
        $data['status'] = 0;
        if( M('Department')->where($where)->save($data) ) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

}