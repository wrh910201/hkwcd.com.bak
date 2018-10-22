<?php
//header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
//header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
date_default_timezone_set("Asia/chongqing");
error_reporting(E_ERROR);
header("Content-Type: text/html; charset=utf-8");

$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("config.json")), true);
$action = $_GET['action'];

switch ($action) {
    case 'config':
        $result =  json_encode($CONFIG);
        break;

    /* 上传图片 */
    case 'uploadimage':
        $result = include("action_upload.php");
        $result = move_image_to_oss($result);
        break;
    /* 上传涂鸦 */
    case 'uploadscrawl':
    /* 上传视频 */
    case 'uploadvideo':
    /* 上传文件 */
    case 'uploadfile':
        $result = include("action_upload.php");
        break;

    /* 列出图片 */
    case 'listimage':
        $result = include("action_list.php");
        break;
    /* 列出文件 */
    case 'listfile':
        $result = include("action_list.php");
        break;

    /* 抓取远程文件 */
    case 'catchimage':
        $result = include("action_crawler.php");
        $data = json_decode($result, true);
        if( $data ) {
            foreach( $data['list'] as $k => $v ) {
                $data['list'][$k] = json_decode(move_image_to_oss(json_encode($v)), true);
            }
            $result = json_encode($data);
        }
        break;

    default:
        $result = json_encode(array(
            'state'=> '请求地址出错'
        ));
        break;
}

/* 输出结果 */
if (isset($_GET["callback"])) {
    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
    } else {
        echo json_encode(array(
            'state'=> 'callback参数不合法'
        ));
    }
} else {
    echo $result;
}

/**
 * 暂时写死吧，以后有其他地方需要用到ueditor再说，现在赶时间
 * @param $result
 * @param $object_prefix
 * @param $type
 * @return mixed
 * @author 老王
 */
function move_image_to_oss($result, $object_prefix = "ueditor/so_post/image/", $type = "json") {

    if( $type == "json" ) {
        $data = json_decode($result, true);
    } else {
        $data = $result;
    }
//    var_dump(realpath($_SERVER['DOCUMENT_ROOT'].$data['url']));exit;
    $file = realpath($_SERVER['DOCUMENT_ROOT'].$data['url']);
    if( !file_exists($file) ) {
        return $result;
    }

    $host = $_SERVER['HTTP_HOST'];
    if( $host === "mmd.bbdfun.com" || $host === "mmd.vdoou.cn" ) {
        $endpoint = "oss-cn-shenzhen-internal.aliyuncs.com";//内网-华南1
    } else {
        $endpoint = "oss-cn-shenzhen.aliyuncs.com";//外网-华南1
    }

    include("../../../../../extend/oss/myoss/Oss.php");
    include("../../../../../vendor/aliyuncs/oss-sdk-php/autoload.php");
    $simple_oss_option = [
        "bucket" => "vdoou-mmd",
        "endpoint" => $endpoint,
        "host" => "//vdoou-mmd.oss-cn-shenzhen.aliyuncs.com",
    ];

    $oss = new oss\myoss\Oss($simple_oss_option);
    $object = $object_prefix . $data['title'];
    $oss_result = $oss->uploadFile($object, $file);
    if( $oss_result ) {
        $data['url'] = "//vdoou-mmd.oss-cn-shenzhen.aliyuncs.com/{$object}";
        @unlink($file);
    }
    $result = json_encode($data);

    return $result;
}