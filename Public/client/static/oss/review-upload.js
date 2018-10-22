accessid = ''
accesskey = ''
host = ''
policyBase64 = ''
signature = ''
callbackbody = ''
filename = ''
key = ''
expire = 0
g_object_name = ''
g_object_name_type = 'random_name'
now = timestamp = Date.parse(new Date()) / 1000;

var post_id = 0;

var upload_total = 0;
var upload_result = [];
var upload_result_msg = '发布成功';
var has_error = false;
var loading = 0;

var uploader = new plupload.Uploader({
    browse_button: 'ignoreMe',
    multi_selection: false,
    container: document.getElementById('selfie_preview'),
    flash_swf_url: 'lib/plupload-2.1.2/js/Moxie.swf',
    silverlight_xap_url: 'lib/plupload-2.1.2/js/Moxie.xap',
    url: 'http://oss.aliyuncs.com',
    filters: {
        mime_types: [ //只允许上传图片和zip文件
            {title: "Image files", extensions: "jpeg,jpg,gif,png,bmp"}
            // { title : "Zip files", extensions : "zip,rar" }
        ],
        max_file_size: '5mb', //最大只能上传10mb的文件
        prevent_duplicates: true //不允许选取重复文件
    }
});

uploader.init();


uploader.bind('FilesAdded', function (uploader, files) {
    // console.log(files.length,uploader.files.length);

    for (var i = 0; i < files.length; i++) {
        // console.log(files);
        var html = "<div class='curr_selfies' id='file-" + files[i].id + "'><div class='upload_progress'>等待上传</div>" +
            "<div class='progress_bar'></div></div>";
        $(html).appendTo("#selfie_preview");
        !function (i) {
            previewImage(files[i], function (imgsrc) {
                $('#file-' + files[i].id).append("<img src='" + imgsrc + "'/>")
            })
        }(i);
        //console.log(uploader.files.length)
    }

    if(uploader.files.length>(gallery_limit-1)){
        $("#selectfiles").css({"width":0,"marginLeft":0,"border":"0","display":"none"});
    }

    if (uploader.files.length > gallery_limit) {
        alert("图片限制"+gallery_limit+"张");
        var overflowFileId = new Array();
        for (var j = gallery_limit,k=0; j < uploader.files.length; j++) {
            overflowFileId[k] = uploader.files[j].id;
            // console.log("超出的图片的ID",overflowFileId);
            // console.log(j, uploader.files[j].id);
            k++;
        }
        for(var l = 0;l<overflowFileId.length;l++){
            $("#file-" + overflowFileId[l]).remove();
            uploader.removeFile(uploader.getFile(overflowFileId[l]));
        }
    }
});

uploader.bind('BeforeUpload', function (uploader, file) {
    set_upload_param(uploader, file.name, false);
});


uploader.bind("Error",function (up, err) {
        if (err.code == -600) {
            // document.getElementById('console').appendChild(document.createTextNode("\n选择的文件太大了,可以根据应用情况，在upload.js 设置一下上传的最大大小"));
            alert("文件大小超过5M，请重新选择");
        }
        else if (err.code == -601) {
            // document.getElementById('console').appendChild(document.createTextNode("\n选择的文件后缀不对,可以根据应用情况，在upload.js进行设置可允许的上传文件类型"));
            alert("图片格式不正确，请重新选择");
        }
        else if (err.code == -602) {
            // document.getElementById('console').appendChild(document.createTextNode("\n这个文件已经上传过一遍了"));
            alert("这个文件已经上传过一遍了");
        }
});

uploader.bind('UploadProgress', function (uploader, file) {
    //绑定上传进度
    $('#file-' + file.id + ' .upload_progress').fadeIn().html("上传中···" + file.percent + '%');
    $('#file-' + file.id + ' .progress_bar').css('width', file.percent + '%');//控制进度条
});

uploader.bind("FileUploaded", function (up, file, info) {
    /*上传完成*/
    console.log(info);
    if (info.status == 200) {
        var response = eval('(' + info.response + ')');
        // console.log(response);
        if (response.code == 1) {
            // console.log(response.msg,file);
            $('#file-' + file.id + ' .upload_progress').html(response.msg);
            // alert("图片上传完成");
        } else {
            // console.log(response.msg);
            upload_result_msg = '部分图片上传失败';
            $('#file-' + file.id + ' .upload_progress').html(upload_result_msg);
            has_error = true;
        }
    } else {
        upload_result_msg = '未知错误';
    }
    upload_total++;
    console.log(upload_total);
    console.log('file length = '+ uploader.files.length);
    if (upload_total == uploader.files.length) {
        loading = 0;
        $('#upload_selfie').removeAttr('disabled');
        alert(upload_result_msg);
        if (has_error) {
            window.location.reload();
        } else {
            window.location.href = '/selfie/Index/post/post_id/' + post_id;
        }
    }
});


$('#upload_selfie').click(function () {
    if(confirm("提交成功之后一周内无法删除，请确认是否提交帖子")){
        //上传按钮
        //先将数据保存到服务器，然后再获取policy吗，最后上传
        if( uploader.files.length  < 1 ) {
            alert('请至少选择一张图片');
            return;
        }
        if( loading == 1 ) {
            return;
        }
        $("#add_selfies").hide();
        $("#selectfiles").css({"width":0,"marginLeft":0,"border":"0","display":"none"});
        $(this).attr('disabled', 'disabled').html("正在上传···");
        $(".remove_selfie").attr("disabled","disabled");
        loading = 1;
        if (post_id) {
            uploader.start();
            return;
        }
        release();

    }
});

function previewImage(file, callback) {//file为plupload事件监听函数参数中的file对象,callback为预览图片准备完成的回调函数
    if (!file || !/image\//.test(file.type)) return; //确保文件是图片
    if (file.type == 'image/gif') {//gif使用FileReader进行预览,因为mOxie.Image只支持jpg和png
        var fr = new mOxie.FileReader();
        fr.onload = function () {
            callback(fr.result);
            fr.destroy();
            fr = null;
        }
        fr.readAsDataURL(file.getSource());
    } else {
        var preloader = new mOxie.Image();
        preloader.onload = function () {
            // preloader.downsize(300, 300);//先压缩一下要预览的图片,宽300，高300
            var imgsrc = preloader.type == 'image/jpeg' ? preloader.getAsDataURL('image/jpeg', 80) : preloader.getAsDataURL(); //得到图片src,实质为一个base64编码的数据
            callback && callback(imgsrc); //callback传入的参数为预览图片的url
            preloader.destroy();
            preloader = null;
        };
        preloader.load(file.getSource());
    }
}

function send_request() {
    var xmlhttp = null;
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }

    if (xmlhttp != null) {
        // serverUrl = './php/get.php'
        serverUrl = '/selfie/User/getPolicy';
        xmlhttp.open("GET", serverUrl + '/post_id/' + post_id, false);
        xmlhttp.send(null);
        return xmlhttp.responseText
    }
    else {
        alert("Your browser does not support XMLHTTP.");
    }
}

function get_signature() {
    //可以判断当前expire是否超过了当前时间,如果超过了当前时间,就重新取一下.3s 做为缓冲
    now = timestamp = Date.parse(new Date()) / 1000;
    if (expire < now + 3) {
        //console.log('获取Policy');
        body = send_request();
        //console.log(body);
        var obj = eval("(" + body + ")");
        if (obj.code == -1) {
            alert(obj.msg);
            return false;
        }
        host = obj['host']
        policyBase64 = obj['policy']
        accessid = obj['accessid']
        signature = obj['signature']
        expire = parseInt(obj['expire'])
        callbackbody = obj['callback']
        key = obj['dir']
        return true;
    }
    return false;
}

function random_string(len) {
    len = len || 32;
    var chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
    var maxPos = chars.length;
    var pwd = '';
    for (i = 0; i < len; i++) {
        pwd += chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}

function get_suffix(filename) {
    //console.log('get_suffix   '+filename);
    pos = filename.lastIndexOf('.')
    suffix = ''
    if (pos != -1) {
        suffix = filename.substring(pos)
    }
    return suffix;
}

function calculate_object_name(filename) {
    if (g_object_name_type == 'local_name') {
        g_object_name += "${filename}"
    }
    else if (g_object_name_type == 'random_name') {
        suffix = get_suffix(filename)
        g_object_name = key + random_string(10) + suffix
    }
    return ''
}

function set_upload_param(up, filename, ret) {
    if (ret == false) {
        ret = get_signature()
    }
    g_object_name = key;
    if (filename != '') {
        suffix = get_suffix(filename)
        calculate_object_name(filename)
    }
    new_multipart_params = {
        'key': g_object_name,
        'policy': policyBase64,
        'OSSAccessKeyId': accessid,
        'success_action_status': '200', //让服务端返回200,不然，默认会返回204
        'callback': callbackbody,
        'signature': signature,
    };

    up.setOption({
        'url': host,
        'multipart_params': new_multipart_params
    });

    // up.start();
}

function release_handler(response) {
    //console.log(response);
    loading = 0;
    $('#upload_selfie').removeAttr('disabled');
    if (response.code == 1) {  //成功，获取policy，并上传
        post_id = response.data.post_id;
        upload_result_msg = response.msg;
        uploader.start();
    } else {
        alert(response.msg);
        $("#upload_selfie").attr('disabled',false).html("点击上传");
        if(uploader.files.length < gallery_limit){
            $("#selectfiles").css({
                "width": "5.5rem",
                "marginLeft": ".25rem",
                "border": "1px solid rgba(0, 0, 0, 0)",
                "display": "inline-block"
            }).siblings(".moxie-shim").css({"width": "90px", "height": "90px"});
        }
    }
}