//添加时间监听器

//使用默认发货地址
$('#get-default-delivery').click(function() {
    get_default_delivery();
});
//获取发货地址列表
$('#get-delivery-list').click(function(){
    get_delivery_list();
});
//打开添加新发货地址窗口
$('#delivery-form').click(function() {
    show_delivery_form();
});
//获取默认的收货地址
$('#get-default-receive').click(function() {
    get_default_receive();
});
//获取收货地址列表
$('#get-receive-list').click(function(){
    get_receive_list();
});
//打开添加新收货地址窗口
$('#receive-form').click(function() {
    show_receive_form()
});
//从发货地址列表中确认选中的地址
$('#select-delivery-btn').click(function() {
    select_delivery();
});
//从收货地址列表中确认选中的地址
$('#select-receive-btn').click(function() {
    select_receive();
});
//确认添加新发货地址
$('#add-new-delivery').click(function() {
    add_delivery();
});
//确认添加新收货地址
$('#add-new-receive').click(function() {
    add_receive();
});
//打开添加备用收货地址的窗口
$('#spare-receive').click(function() {
    show_spare_form();
});

$('#show_order_detail').click(function() {
    show_order_detail_form();
});

$('#show_order_specifications').click(function() {
    show_order_specifications_form();
});

$('#add_order_specifications').click(function() {
    add_order_specifications();
});

$('#add_order_detail').click(function() {
    add_order_detail();
});

$('#add_order_button').click(function() {
    layer.confirm('确定添加订单？', {
        btn: ['确定', '取消'],
        btn1: function () {
            add_order(0);
        },
        btn2: function () {
            return false;
        }
    });
});

$('#commit_order_button').click(function() {
    layer.confirm('确定添加订单并提交？（订单提交之后进入待审核状态，无法编辑）', {
        btn: ['确定', '取消'],
        btn1: function () {
            add_order(1);
        },
        btn2: function () {
            return false;
        }
    });
});

//==========时间监听器结束============================================
//==========发货地址开始============================================

function get_default_delivery() {
    show_loading();
    var url = "/Order/getDefaultDelivery";
    var param = { };
    $.post(url, param, get_default_delivery_handler, 'json');
}

function get_default_delivery_handler(response) {
    hide_loading();
    if( response.code == 0 ) {
        alert(response.msg);
        window.location.href = '/';
    } else if( response.code == 1 ) {
        set_delivery(response.data);
    } else if( response.code == -1 ) {
        layer.msg(response.msg);
    }
}

function get_delivery_list() {
    show_loading();
    var url = "/Order/getDeliveryList";
    var param = { };
    $.post(url, param, get_delivery_list_handler, 'json');
}

function set_delivery(data) {
    // console.log(data);
    $('#delivery_company').text(data.company);
    $('#consignor').text(data.consignor);
    $('#delivery_country').text(data.country_name);
    $('#delivery_state').text(data.state);
    $('#delivery_city').text(data.city);
    $('#delivery_phone').text(data.phone);
    $('#delivery_mobile').text(data.mobile);
    $('#delivery_detail_address').text(data.show_detail_address);
    $('#delivery_detail_address').attr('title', data.detail_address);
    $('#delivery_postal_code').text(data.postal_code);
    $('#exporter_code').text(data.exporter_code);
    selected_delivery_id = data.id;
    selected_delivery_data = data;
}

function get_delivery_list_handler(response) {
    hide_loading();
    if( response.code == 0 ) {
        alert(response.msg);
        window.location.href = '/';
    } else if( response.code == 1 ) {
        render_delivery_list(response.data);
    } else if( response.code == -1 ) {
        alert(response.msg);
    }
}

function render_delivery_list(data) {
    delivery_list = data;
//        console.log(delivery_list);
    var html = '';
    $('#select_delivery_items').empty();
    $.each(data, function (k, v) {
        html += '<tr>';
        html += '<td><input type="radio" class="ace delivery-check-item" name="delivery-check-item" value="'+v.id+'"';
        if( selected_delivery_id && v.id == selected_delivery_id) {
            html += ' checked></td>';
        } else {
            if (v.is_default == 1) {
                html += ' checked></td>';
            } else {
                html += ' ></td>';
            }
        }
        html += '<td>' + v.company + '</td>';
        html += '<td>' + v.consignor + '</td>';
        html += '<td>' + v.detail_address + '</td>';
        html += '<td>' + v.postal_code + '</td>';
        if (v.is_default == 1) {
            html += '<td>是</td>';
        } else {
            html += '<td>否</td>';
        }
        html += '</tr>';
    });
    $('#select_delivery_items').append(html);
    tips = layer.open({
        type: 1,
        skin: 'layui-layer-rim', //加上边框
        area: ['750px', '400px'], //宽高
        title: false,
        closeBtn: 1,
        shadeClose: false,
        content: $('#delivery-list')
    });
}

function select_delivery() {
    var obj = $('#delivery-check-item:checked');
    if( !obj ) {
        layer.msg('请选择一个发货地址');
        return;
    }
    var id = $('.delivery-check-item:checked').val();
    var data = null;
    $.each(delivery_list, function(k,v) {
        if( v.id == id ) {
            data = v;
        }
    });
    set_delivery(data);
    layer.close(tips);
}

function show_delivery_form() {
    $('#add_delivery_company').val(default_company);
    tips = layer.open({
        type: 1,
        skin: 'layui-layer-rim', //加上边框
        area: ['1050px', '500px'], //宽高
        title: false,
        closeBtn: 1,
        shadeClose: false,
        content: $('#add_delivery_form')
    });
}

function close_delivery_form() {
    $('#add_delivery_form input[type=text]').val('');
    $('#add_delivery_form input[type=hidden]').val('');
    $('#add_delivery_form input[type=file]').val('');
    $('#add_delivery_country option:selected').removeAttr('selected');
    $('#add_delivery_country option:first').attr('selected');
    $('input[name=add_delivery_default]').removeAttr('checked');
    $('#add_delivery_not_default').attr('checked', 'checked');
    $('#add_delivery_form img').attr('src', '');
    $('#add_delivery_form .select-image').show();
    $('#add_delivery_form .show-image').hide();
    $('#add_delivery_form #show-result-1').hide();
    $('#myupload-1 i').remove();
    $('#myupload-1 p').remove();
    $('#myupload-1').append('<i class="icon">&#xe629;</i> <p>上传身份证正面</p>');
    $('#myupload-2 i').remove();
    $('#myupload-2 p').remove();
    $('#myupload-2').append('<i class="icon">&#xe629;</i> <p>上传身份证反面</p>');
    layer.close(tips);
}

function add_delivery() {
    show_loading();
    var url = "/Delivery/ajaxAdd";
    var company = $('#add_delivery_company').val();
    var consignor = $('#add_delivery_consignor').val();
    var country = $('#add_delivery_country').val();
    var state = $('#add_delivery_state').val();
    var city = $('#add_delivery_city').val();
    var detail_address = $('#add_delivery_detail_address').val();
    var mobile = $('#add_delivery_mobile').val();
    var phone = $('#add_delivery_phone').val();
    var postal_code = $('#add_delivery_postal_code').val();
    var exporter_code = $('#add_exporter_code').val();
    var certificate_num = $('#certificate_num').val();
    var certificate1_url = $('#certificate1_url').val();
    var certificate2_url = $('#certificate2_url').val();
    var is_default = $('input[name=add_delivery_default]:checked').val();
    var param = {
        exporter_code:exporter_code,
        certificate_num:certificate_num,
        certificate1_url:certificate1_url,
        certificate2_url:certificate2_url,
        company:company,
        postal_code:postal_code,
        consignor:consignor,
        country:country,
        state:state,
        city:city,
        detail_address:detail_address,
        mobile:mobile,
        phone:phone,
        is_default:is_default
    };
    $.post(url, param, add_delivery_handler, 'json');
}

function add_delivery_handler(response) {
    hide_loading();
    if( response.code == 0 ) {
        alert(response.msg);
        window.location.href = '/';
    } else if( response.code == 1 ) {
        set_delivery(response.data);
        close_delivery_form();
    } else if( response.code == -1 ) {
        alert(response.msg);
    }
}

//==========发货地址结束============================================

//==========收货地址开始============================================

function get_default_receive() {
    show_loading();
    var url = "/Order/getDefaultReceive";
    var param = { };
    $.post(url, param, get_default_receive_handler, 'json');
}

function get_default_receive_handler(response) {
    hide_loading();
    if( response.code == 0 ) {
        alert(response.msg);
        window.location.href = '/';
    } else if( response.code == 1 ) {
        set_receive(response.data);
    } else if( response.code == -1 ) {
        layer.msg(response.msg);
    }
}

function set_receive(data) {
    console.log(data);
    $('#receive_company').text(data.company);
    $('#addressee').text(data.addressee);
    $('#receive_country').text(data.country_name);
    $('#receive_state').text(data.state);
    $('#receive_city').text(data.city);
    $('#receive_phone').text(data.phone);
    $('#receive_mobile').text(data.mobile);
    $('#receive_detail_address').text(data.show_detail_address);
    $('#receive_detail_address').attr('title', data.detail_address);
    $('#receive_postal_code').text(data.postal_code);
    $('#receiver_code').text(data.receiver_code);
    selected_receive_id = data.id;
    selected_receive_data = data;
}

function get_receive_list() {
    show_loading();
    var url = "/Order/getReceiveList";
    var param = { };
    $.post(url, param, get_receive_list_handler, 'json');
}

function get_receive_list_handler(response) {
    hide_loading();
    if( response.code == 0 ) {
        alert(response.msg);
        window.location.href = '/';
    } else if( response.code == 1 ) {
        render_receive_list(response.data);
    } else if( response.code == -1 ) {
        alert(response.msg);
    }
}

function render_receive_list(data) {
    receive_list = data;
    var html = '';
    $('#select_receive_items').empty();
    $.each(data, function (k, v) {
        html += '<tr>';
        html += '<td><input type="radio" class="ace receive-check-item" name="receive-check-item" value="'+v.id+'"';
        if( v.is_default == 1 && selected_receive_id == v.id ) {
            html += ' checked></td>';
        } else {
            if( selected_receive_id == v.id ) {
                html += ' checked></td>';
            } else {
                html += ' ></td>';
            }
        }
        html += '<td>' + v.company + '</td>';
        html += '<td>' + v.addressee + '</td>';
        html += '<td>' + v.detail_address + '</td>';
        html += '<td>' + v.postal_code + '</td>';
        if (v.is_default == 1) {
            html += '<td>是</td>';
        } else {
            html += '<td>否</td>';
        }
        html += '</tr>';
    });
    $('#select_receive_items').append(html);
    tips = layer.open({
        type: 1,
        skin: 'layui-layer-rim', //加上边框
        area: ['750px', '400px'], //宽高
        title: false,
        closeBtn: 1,
        shadeClose: false,
        content: $('#receive-list')
    });
}

function select_receive() {
    var obj = $('#receive-check-item:checked');
    if( !obj ) {
        layer.msg('请选择一个收货地址');
        return;
    }
    var id = $('.receive-check-item:checked').val();
//        id = "'" + id + "'";
    var data = null;
    $.each(receive_list, function(k,v) {
        if( v.id == id ) {
            data = v;
        }
    });
    // var data = receive_list[id];
    set_receive(data);
    layer.close(tips);
}


function show_receive_form() {
    tips = layer.open({
        type: 1,
        skin: 'layui-layer-rim', //加上边框
        area: ['750px', '550px'], //宽高
        title: false,
        closeBtn: 1,
        shadeClose: false,
        content: $('#add_receive_form')
    });
}

function add_receive() {
    show_loading();
    var url = "/Receive/ajaxAdd";
    var company = $('#add_receive_company').val();
    var addressee = $('#add_receive_addressee').val();
    var country = $('#add_receive_country').val();
    var state = $('#add_receive_state').val();
    var city = $('#add_receive_city').val();
    var detail_address = $('#add_receive_detail_address').val();
    var mobile = $('#add_receive_mobile').val();
    var phone = $('#add_receive_phone').val();
    var receiver_code = $('#receiver_code').val();
    var is_default = $('input[name=add_receive_default]:checked').val();
    var postal_code = $('#add_receive_postal_code').val();
    var param = { receiver_code:receiver_code, company:company,addressee:addressee,country:country,state:state,city:city,detail_address:detail_address,mobile:mobile,phone:phone,postal_code:postal_code,is_default:is_default };
    $.post(url, param, add_receive_handler, 'json');
}

function add_receive_handler(response) {
    hide_loading();
    if( response.code == 0 ) {
        alert(response.msg);
        window.location.href = '/';
    } else if( response.code == 1 ) {
        set_receive(response.data);
        close_receive_form();
    } else if( response.code == -1 ) {
        layer.msg(response.msg);
    }
}

function close_receive_form() {
    $('#add_receive_form input[type=text]').val('');
    $('#add_receive_country option:selected').removeAttr('selected');
    $('#add_receive_country option:first').attr('selected');
    $('input[name=add_receive_default]').removeAttr('checked');
    $('#add_receive_not_default').attr('checked', 'checked');
    layer.close(tips);
}

//==========收货地址结束============================================

//==========备用收货地址开始============================================

function show_spare_form() {
    tips = layer.open({
        type: 1,
        skin: 'layui-layer-rim', //加上边框
        area: ['750px', '550px'], //宽高
        title: false,
        closeBtn: 1,
        shadeClose: false,
        content: $('#spare_receive_form')
    });
}

function hide_spare_form() {
    layer.close(tips);
}

//==========备用收货地址结束============================================