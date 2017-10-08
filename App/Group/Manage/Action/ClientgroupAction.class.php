<?php
/**
 * Created by PhpStorm.
 * User: Wrh
 * Date: 2016/9/26
 * Time: 18:32
 */

class ClientgroupAction extends CommonContentAction
{

    public function index()
    {

        $keyword = I('keyword', '', 'htmlspecialchars,trim');//关键字
        $where = array();
        $condition = array();
        if (!empty($keyword)) {
            $condition['name'] = ['like', '%'.$keyword.'%'];
            $condition['en_name'] = ['like', '%'.$keyword.'%'];
            $condition['_logic'] = 'OR';
            $where['_complex'] = $condition;
        }
        $where['status'] = 1;

        //分页
        import('ORG.Util.Page');
        $count = M('clientGroup')->count();

        $page = new Page($count, 10);
        $limit = $page->firstRow . ',' . $page->listRows;
        $list = M('clientGroup')->where($where)->order('id')->limit($limit)->select();

        $this->page = $page->show();
        $this->vlist = $list;
        $this->type = '客户分组列表';

        $this->display();
    }

    public function add()
    {
        $this->type = '添加客户分组';
        $this->display();
    }

    public function doAdd()
    {
        $data['name'] = I('post.name', '', 'htmlspecialchars,trim');
        $data['en_name'] = I('post.en_name', '', 'htmlspecialchars,trim');

        if( empty($data['name']) || empty($data['en_name']) ) {
            $this->error('请输入客户分组名');
            exit;
        }
        $data['operator_id'] = session('yang_adm_uid');
        $result = M('ClientGroup')->add($data);
        if( $result ) {
            $this->success('添加客户分组成功',U(GROUP_NAME. '/Clientgroup/index'));
        } else {
            $this->error('添加客户分组失败');
        }
    }

    public function edit()
    {
        $id = I('get.id', 0, 'intval');
        $client_group = M('ClientGroup')->where(['id' => $id])->find();
        if( empty($client_group) || $client_group['status'] == 0 ) {
            $this->error('客户分组不存在');
            exit;
        }
        $this->assign('client_group', $client_group);
        $this->type = '编辑客户分组';
        $this->display();
    }

    public function doEdit()
    {
        $id = I('post.id', 0, 'intval');
        $client_group = M('ClientGroup')->where(['id' => $id])->find();
        if( empty($client_group) || $client_group['status'] == 0 ) {
            $this->error('客户分组不存在');
            exit;
        }
        $data['name'] = I('post.name', '', 'htmlspecialchars,trim');
        $data['en_name'] = I('post.en_name', '', 'htmlspecialchars,trim');

        if( empty($data['name']) || empty($data['en_name']) ) {
            $this->error('请输入客户分组名');
            exit;
        }
        $result = M('ClientGroup')->where(['id' => $id])->save($data);
        if( is_numeric($result) ) {
            $this->success('编辑客户分组成功');
        } else {
            $this->error('编辑客户分组失败');
        }
    }

    public function del() {
        $id = I('get.id', 0, 'intval');
        $batchFlag = I('get.batchFlag', 0, 'intval');
        //批量删除
        if ($batchFlag) {
            $this->multiDel();
            return;
        }
        if( $id == 1 ) {
            $this->error('默认分组，请勿删除');
            exit;
        }
        $client_group = M('ClientGroup')->where(['id' => $id])->find();
        if( empty($client_group) || $client_group['status'] == 0 ) {
            $this->error('客户分组不存在');
            exit;
        }

        //是否有下属用户
        $client = M('client')->where(['group_id' => $id])->find();
        if( $client ) {
            $this->error('该客户分组下有客户，无法删除');
            exit;
        }
        $result = M('ClientGroup')->where(['id' => $id])->save(['status' => 0]);
        if( $result ) {
            $this->success('删除客户分组成功');
        } else {
            $this->error('删除客户分组失败');
        }
    }


    public function multiDel() {
        $idArr = I('key',0 , 'intval');
        if (!is_array($idArr)) {
            $this->error('请选择要彻底删除的项');
        }
        if( in_array(1, $idArr) ) {
            $this->error('默认分组，请勿删除');
        }

        $client_where = ['group_id' => ['in', $idArr]];
        //是否有下属用户
        $client = M('client')->where($client_where)->select();
        if( $client ) {
            $this->error('选中的客户分组下有客户，无法删除');
            exit;
        }

        $where = array('id' => array('in', $idArr));
        $result = M('ClientGroup')->where($where)->save(['status' => 0]);
        if( $result ) {
            $this->success('删除客户分组成功');
        } else {
            $this->error('删除客户分组失败');
        }
    }

    public function price() {
        $id = I('get.id', 0, 'intval');
        $client_group = M('ClientGroup')->where(['id' => $id])->find();
        if( empty($client_group) || $client_group['status'] == 0 ) {
            $this->error('客户分组不存在');
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
            'group_id' => $id,
            'status' => 1,
        ];
        $price = M('ChannelMap')->where($where)->find();
//        echo M('ChannelMap')->getLastSql();exit;
//        var_dump($price);exit;
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
                        if( substr($offset, '~') === false ) {
                            $price[$k][$k1] = '大于等于'.$offset . 'kg，按每' . $per_kilo . 'kg';
                        } else {
                            $price[$k][$k1] = $offset . 'kg，按每' . $per_kilo . 'kg';
                        }
                    } else {
                        $price[$k][$k1] = sprintf('%.2f', $v);
                    }
                }
            }
        }
        $this->assign('price', $price);
        $this->assign('region_list', $region_list);
        $this->assign('package_type', $type);
        $this->assign('channel_list', $channel_list);
        $this->assign('channel_id', $channel_id);
        $this->assign('id', $id);
        $this->type = '查看价格';
        $this->display();

    }

    public function import() {
        $id = I('get.id', 0, 'intval');
        $client_group = M('ClientGroup')->where(['id' => $id])->find();
        if( empty($client_group) || $client_group['status'] == 0 ) {
            $this->error('客户分组不存在');
            exit;
        }
        $this->assign('client_group', $client_group);

        $channel_list = M('Channel')->where(['status' => 1])->select();
        if( empty($channel_list) ) {
            $this->error('请先添加渠道', U(GROUP_NAME. '/Channel/add'));
        }
        $this->assign('channel_list', $channel_list);

        $this->type = '设置价格';
        $this->display();
    }

    public function doImport() {
        $id = I('post.id', 0, 'intval');
        $client_group = M('ClientGroup')->where(['id' => $id])->find();
        if( empty($client_group) || $client_group['status'] == 0 ) {
            $this->error('客户分组不存在');
            exit;
        }
        $data['channel_id'] = I('post.channel_id', 0, 'intval');
        $channel = M('Channel')->where(['id' => $data['channel_id']])->find();
        if( empty($channel) || $channel['status'] == 0 ) {
            $this->error('渠道不存在');
            exit;
        }
        $path = I('post.url', '', 'trim');

        $model = new Model;
        $model->startTrans();
        $transaction = true;

        $read_result = $this->_readPackageFromExcel($path);
        if( $read_result['code'] == 0 ) {
            @unlink($_SERVER['DOCUMENT_ROOT'].$path);
            $this->error('导入包裹价格时，'.$read_result['msg']);
        }
//        var_dump($read_result['data']);exit;
        $data['content'] = $read_result['data'];
        $data['type'] = 2;
        $data['content'] = json_encode($data['content']);
        $data['group_id'] = $id;
        $where = [
            'group_id' => $id,
            'channel_id' => $data['channel_id'],
            'type' => 2,
        ];
        $exists = M('ChannelMap')->where($where)->find();
        if( $exists ) {
            $map_id = $exists['id'];
            $result = M('ChannelMap')->where($where)->save(['content' => $data['content']]);
            if( !is_numeric($result) ) {
                $transaction = false;
            }
            $delete_result = M('ChannelMapPrice')->where(['map_id' => $map_id])->delete();
            if( !is_numeric($delete_result) ) {
                $transaction = false;
            }

        } else {
            $result = M('ChannelMap')->add($data);
            $map_id = M('ChannelMap')->getLastInsID();
            if( !$result ){
                $transaction = false;
            }
        }

        $price_data = [];
        if( $transaction ) {
            foreach( $read_result['data'] as $i => $row ) {
                foreach( $row as $j => $v ) {
                    if( $i == 1 ) {
                        $region = M('Region')->where(['alias' => $v])->find();
                        $price_data[$j+1] = $region['id'];
                        continue;
                    }
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
                    $insert_data['map_id'] = $map_id;
                    $insert_data['region_id'] = $price_data[$j];
                    $insert_result = M('ChannelMapPrice')->add($insert_data);
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

        $read_result = $this->_readDocumentFromExcel($path);
        if( $read_result['code'] == 0 ) {
            $model->rollback();
            @unlink($_SERVER['DOCUMENT_ROOT'].$path);
            $this->error('导入文件价格时，'.$read_result['msg']);
        }
        $data['content'] = $read_result['data'];
        $data['type'] = 1;
        $data['content'] = json_encode($data['content']);
        $data['group_id'] = $id;
        $where = [
            'group_id' => $id,
            'channel_id' => $data['channel_id'],
            'type' => 1,
        ];
        $exists = M('ChannelMap')->where($where)->find();
        if( $exists ) {
            $map_id = $exists['id'];
            $result = M('ChannelMap')->where($where)->save(['content' => $data['content']]);
            if( !is_numeric($result) ) {
                $transaction = false;
            }
            $delete_result = M('ChannelMapPrice')->where(['map_id' => $map_id])->delete();
            if( !is_numeric($delete_result) ) {
                $transaction = false;
            }
        } else {
            $result = M('ChannelMap')->add($data);
            $map_id = M('ChannelMap')->getLastInsID();
            if( !$result ){
                $transaction = false;
            }
        }
        if( $transaction ) {
            foreach( $read_result['data'] as $i => $row ) {
                foreach( $row as $j => $v ) {
                    if( $i == 1 ) {
                        $region = M('Region')->where(['alias' => $v])->find();
                        $price_data[$j+1] = $region['id'];
                        continue;
                    }
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
                    $insert_data['map_id'] = $map_id;
                    $insert_data['region_id'] = $price_data[$j];
                    $insert_result = M('ChannelMapPrice')->add($insert_data);
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
                if( $row == 1 && $col == 0 ) {
                    continue;
                }
                //检测分区是否存在
                if( $row == 1 ) {
                    $region = trim($objWorksheet->getCellByColumnAndRow($col, $row)->getValue());
                    if( is_numeric($region) && is_integer($region) ) {
                        $region = intval($region);
                        $exists = M('Region')->where(['id' => $region])->find();
                    } else {
                        $exists = M('Region')->where(['alias' => $region])->find();
                    }
                    if( empty($exists) ) {
                        if( $error_region == '' ) {
                            $error_region .= '分区【';
                            $error_region .= (string)$region;
                        } else {
                            $error_region .= '，'.(string)$region;
                        }
                    }
                }
                $excelData[$row][] =(string)trim($objWorksheet->getCellByColumnAndRow($col, $row)->getValue());
            }
            if( $error_region != '' ) {
                $error_region .= '】不存在';
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
                if( $row == 1 && $col == 0 ) {
                    continue;
                }
                //检测分区是否存在
                if( $row == 1 ) {
                    $region = trim($objWorksheet->getCellByColumnAndRow($col, $row)->getValue());
                    if( is_numeric($region) && is_integer($region) ) {
                        $region = intval($region);
                        $exists = M('Region')->where(['id' => $region])->find();
                    } else {
                        $exists = M('Region')->where(['alias' => $region])->find();
                    }
                    if( empty($exists) ) {
                        if( $error_region == '' ) {
                            $error_region .= '分区【';
                            $error_region .= (string)$region;
                        } else {
                            $error_region .= '，'.(string)$region;
                        }
                    }
                }
                $excelData[$row][] =(string)trim($objWorksheet->getCellByColumnAndRow($col, $row)->getValue());
            }
            if( $error_region != '' ) {
                $error_region .= '】不存在';
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