/**
 * Created by Carso on 2017/10/9.
 */
var cloneObj = function (source) {
    var result = {};
    for (var key in source) {
        result[key] = typeof source[key] === 'object' ? this.cloneObj(source[key]) : source[key];
    }
    return result;
};
Vue.config.devtools = true;
Vue.directive('select2', {
    inserted: function (el, binding, vnode) {
        var options = binding.value || {};
        $(el).select2(options).on("select2:select", function (e){
            el.dispatchEvent(new Event('change', { target: e.target })); //说好的双向绑定，竟然不安套路
        });
    },
    update: function(el, binding, vnode) {
        $(el).trigger("change");
    }
});

var addOrder = new Vue({
    el: "#addOrder",
    data: {
        deliveryInfo: {
            delivery_id: 0,
            company: "",
            consignor: "",
            country: "",
            state: "",
            city: "",
            detail_address: "",
            postal_code: "",
            mobile: "",
            phone: "",
            exporter_code: "",
            certificate_num:""
        },
        addDeliveryInfo: {
            company: default_company,
            consignor: "",
            country: "",
            state: "",
            city: "",
            detail_address: "",
            postal_code: "",
            mobile: "",
            phone: "",
            exporter_code: "",
            certificate_num: "",
            certificate1_url: "",
            certificate2_url: "",
            is_default: false
        },
        receiveInfo: {
            receive_id: 0,
            receiver_code:'',
            company: "",
            id:null,
            client_id:"",
            addressee: "",
            country_id: "",
            country_name: "",
            en_country_name:"",
            state: "",
            status:"",
            type:"",
            city: "",
            detail_address: "",
            show_detail_address: "",
            postal_code: "",
            mobile: "",
            phone: "",
            exporter_code: "",
            is_default: null,
            remark:"",
            created_at:"",
            updated_at:""
        },
        addReceiveInfo: {
            company: "",
            addressee: "",
            country: "",
            state: "",
            city: "",
            detail_address: "",
            mobile: null,
            phone: null,
            receiver_code: null,
            is_default: false,
            postal_code: null
        },
        spareInfo:{
            spare_receive_company: '',
            spare_receive_addressee: '',
            spare_country: '',
            spare_state: '',
            spare_city: '',
            spare_receive_detail_address: '',
            spare_receive_phone: null,
            spare_receive_mobile: null,
            spare_receive_postal_code: '',
            spare_receiver_code: '',
            enable_spare: false
        },
        emptyInfo:{},
        deliveryList: [],
        receiveList: [],
        deliveryListRow: selected_delivery_id,
        receiveListRow: selected_receive_id,
        allInfo: {
            channel:"",
            package_type:"",
            price_terms:"",
            tariff_payment:"",
            shipment_reference:"",
            settlement:"",
            declaration_of_power_attorney:"",
            contract_num:"",
            mode_of_transportation:"",
            express_service:"",
            export_reason:"",
            export_nature:"",
            manufacturer:"",
            remark:""
        }
    },
    mounted:function () {
        this.emptyInfo.deliveryInfo = this.deliveryInfo;
        this.emptyInfo.receiveInfo  = this.receiveInfo;
        this.emptyInfo.spareInfo  = this.spareInfo;
        this.emptyInfo.addReceiveInfo  = this.addReceiveInfo;
        this.emptyInfo.addDeliveryInfo  = this.addDeliveryInfo;
    },
    methods: {
        setDefaultDelivery: function () {
            var that = this;
            show_loading();
            $.ajax({
                url:"/Order/getDefaultDelivery",
                data:{ },
                dataType:"json",
                type:"POST",
                success:function (res) {
                    hide_loading();
                    var resCode = Number(res.code);
                    if( resCode === 0 ) {
                        swal(res.msg);
                        window.location.href = '/';
                    } else if( resCode === 1 ) {
                        that.setDeliveryData(res.data);
                    } else if( resCode === -1 ) {
                        layer.msg(res.msg);
                    }
                }
            });
        },
        setDefaultReceive: function () {
            show_loading();
            var that = this;
            $.ajax({
                url: "/Order/getDefaultReceive",
                data: { },
                dataType: "json",
                type: "POST",
                success: function (res) {
                    var resCode = Number(res.code);
                    hide_loading();
                    if( resCode === 0 ) {
                        layer.msg(res.msg);
                        window.location.href = '/';
                    } else if( resCode === 1 ) {
                        that.setReceiveData(res.data)
                    } else if( resCode === -1 ) {
                        layer.msg(res.msg);
                    }
                }
            });
        },

        getDeliveryList: function () {
            show_loading();
            var url = "/Order/getDeliveryList";
            var param = { },that = this;
            $.post(url, param, function (res) {
                hide_loading();
                var resCode = res.code;
                if( resCode === 0 ) {
                    swal(res.msg);
                    window.location.href = '/';
                } else if( resCode === 1 ) {
                    that.deliveryList = res.data;
                    tips = layer.open({
                        type: 1,
                        skin: 'layui-layer-rim', //加上边框
                        area: ['750px', '400px'], //宽高
                        title: false,
                        closeBtn: 1,
                        shadeClose: false,
                        content: $('#delivery-list')
                    });
                } else if( resCode === -1 ) {
                    swal(res.msg);
                }
            }, 'json');

        },

        getReceiveList: function () {
            show_loading();
            var url = "/Order/getReceiveList",param = {},that = this;
            $.post(url, param, function (res) {
                hide_loading();
                var resCode = Number(res.code);
                if( resCode === 0 ) {
                    swal({
                        title: res.msg,
                        type: "info",
                        text: ""
                    });
                    window.location.href = '/';
                } else if( resCode === 1 ) {
                    console.log("res.data",res.data);
                    that.receiveList = res.data;
                    tips = layer.open({
                        type: 1,
                        skin: 'layui-layer-rim', //加上边框
                        area: ['750px', '400px'], //宽高
                        title: false,
                        closeBtn: 1,
                        shadeClose: false,
                        content: $('#receive-list')
                    });
//                        render_receive_list(res.data);
                } else if( resCode === -1 ) {
                    swal({
                        title: res.msg,
                        type: "error",
                        text: ""
                    });
                }
            }, 'json');

        },

        addDelivery: function () {
//                add_delivery();
            show_loading();
            var url = "/Delivery/ajaxAdd";
            var params = this.addDeliveryInfo, that = this;
            $.post(url, params, function (res) {
                hide_loading();
                var resCode = Number(res.code);
                if( resCode === 0 ) {
                    swal(res.msg);
                    window.location.href = '/';
                } else if( resCode === 1 ) {
                    that.setDeliveryData(res.data);
                    that.addDeliveryInfo  = that.emptyInfo.addDeliveryInfo;
                    close_delivery_form();
                    layer.close(tips);
                } else if( resCode === -1 ) {
                    alert(res.msg);
                }
            }, 'json');
        },

        addReceive: function () {
            show_loading();
            var that = this,params = {};
            for(var i  in this.addReceiveInfo){
                params[i] = this.addReceiveInfo[i]
            }
            $.post("/Receive/ajaxAdd", params, function (res) {
                hide_loading();
                var resCode = Number(res.code);
                if( resCode === 0 ) {
                    layer.msg(res.msg);
                    window.location.href = '/';
                } else if( resCode === 1 ) {
                    that.setReceiveData(res.data);
                    that.addReceiveInfo  = that.emptyInfo.addReceiveInfo;
                    layer.close(tips);
                } else if( resCode === -1 ) {
                    layer.msg(res.msg);
                }
            }, 'json');
        },
        showDeliveryForm: function () {
            show_delivery_form()
        },
        showReceiveForm: function () {
            show_receive_form()
        },
        showSpareForm: function () {
            show_spare_form()
        },
        selectDelivery: function () {
//                select_delivery();
            if(this.deliveryListRow === null){
                swal("请选择一个收货地址",'','error');
                return false
            }else{
                for(var i in this.deliveryList){
                    if(this.deliveryList[i].id == this.deliveryListRow){
                        console.log("this.receiveList[i]",this.deliveryList[i]);
                        this.setDeliveryData(this.deliveryList[i])
                    }
                }
            }
            layer.close(tips);
        },
        selectReceive: function () {
//                select_receive()
            if(this.receiveListRow === null){
                swal("请选择一个收货地址",'','error');
                return false
            }
            else {
                for(var i in this.receiveList){
                    if(this.receiveList[i].id == this.receiveListRow){
                        console.log("this.receiveList[i]",this.receiveList[i]);
                        this.setReceiveData(this.receiveList[i])
                    }
                }
            }
            layer.close(tips);
        },
        setDeliveryData: function (resData) {
            this.deliveryInfo = resData;
            selected_delivery_id = resData.id;
            selected_delivery_data = resData;
            $('#delivery_id').val(resData.id);
            set_mode_of_transportation();
        },
        setReceiveData: function (resData) {
            this.receiveInfo = resData;
            $('#receive_id').val(resData.id);
            selected_receive_id = resData.id;
            selected_receive_data = resData;
            set_mode_of_transportation();
        }
    }
});

var orderDetail = new Vue({
    el: "#orderDetail",
    data: {
        newDetail: {
            product_name:"",
            en_product_name:"",
            detail_goods_code:"",
            detail_count:"",
            unit:'PCS',
            single_declared:"",
            origin:""
        },
        editingIndex: null,
        emptyDetail:{},
        orderList:[],
        total_count:0,
        total_single_declared:0,
        total_declared:0
    },
    mounted: function () {
        for(var i in this.newDetail){
            this.emptyDetail[i] = this.newDetail[i]
        }
    },
    methods: {
        resetDetail: function (obj) {
            var $obj = obj ? obj : false;
            if($obj){
                for(var i in $obj){
                    this.newDetail[i] = $obj[i]
                }
            }else{
                this.newDetail = {
                    product_name:"",
                    en_product_name:"",
                    detail_goods_code:"",
                    detail_count:"",
                    unit:'PCS',
                    single_declared:"",
                    origin:""
                }
            }
        },
        fixedNum: function(num,fix){
            var $num = Number(num),$fix = Number(fix);
            return $num.toFixed($fix)
        },
        checkDetail: function (list) {
            var flag = true,msg = '',detail = list;
            for(var i in this.newDetail){
                detail[i] = this.newDetail[i]
            }
            if( detail.product_name === '' ) {
                msg += '请输入中文品名<br />';
                flag = false;
            }
            if( detail.en_product_name === '' ) {
                msg += '请输入英文品名<br />';
                flag = false;
            }
            if( detail.goods_code === '' ) {
                msg += '请输入商品编号<br />';
                flag = false;
            }
            if( isNaN(detail.detail_count) || detail.detail_count <= 0 ) {
                msg += '请正确的输入数量<br />';
                flag = false;
                this.newDetail.detail_count = 0;
            }
            if( isNaN(detail.single_declared) || detail.single_declared <= 0 ) {
                msg += '请输入正确的单件申报价值<br />';
                flag = false;
                this.newDetail.single_declared = 0;
            }
            if( detail.origin === '' ) {
                msg += '请输入原产地<br />';
                flag = false;
            }
            return {
                flag : flag,
                msg : msg
            }
        },
        editOrderDetail: function (index) {
            console.log("index",index);
            this.editingIndex = index>=0 ? index : -1;
            console.log("this.editingIndex",this.editingIndex);
            if(this.editingIndex >= 0){
                this.resetDetail(this.orderList[index]);
            }
            else{
                this.resetDetail()
            }
            show_order_detail_form()
        },
        addOrderDetail: function () {
            var that = this,detail = {};
            var check = this.checkDetail(this.newDetail);
            for(var i in this.newDetail){
                detail[i] = this.newDetail[i]
            }
            if(check.flag){
                if(this.editingIndex >=  0){
                    console.log("edit");
                    this.orderList[this.editingIndex] = detail;
                }else{
                    console.log("push");
                    this.orderList.push(detail);
                }
                hide_order_detail_form();
                this.resetDetail();
                this.recountTotal();
            }
            else{
                layer.msg(check.msg);
                return false;
            }
        },
        recountTotal:function () {
            this.total_count = 0;
            this.single_declared = 0;
            this.total_declared = 0;
            for(var i in this.orderList){
                this.total_count += Number(this.orderList[i].detail_count);
                this.single_declared += Number(this.orderList[i].single_declared);
                this.total_declared += Number(this.orderList[i].single_declared)*Number(this.orderList[i].detail_count);
            }
        },
        removeOrderDetail:function (index) {
            var that = this;
            layer.confirm('确定删除该项商品信息？', {
                btn: ['确定', '取消'],
                btn1: function () {
                    that.orderList.splice(index,1);
                },
                btn2: function () {
                    return false;
                }
            });
        }
    }
});

var orderSpec = new Vue({
    el:"#orderSpec",
    data: {
        specList: [],
        total_weight : 0,
        total_rate: 0,
        total_charging_weight: 0,
        total_order_specifications_count : 0,
        editingIndex : null,
        specOrder: {
            order_specifications_weight: "",
            order_specifications_length: "",
            order_specifications_width: "",
            order_specifications_height: "",
            order_specifications_count: "",
            order_specifications_cargo: [
                {
//                        product_index:'',
//                        en_product_name:'',
//                        product_count: null
                }
            ]
        },
        emptyOrder : {}
    },
    mounted: function () {
        for(var i in this.specOrder){
            this.emptyOrder[i] = this.specOrder[i];
        }
    },
    methods: {
        fixedNum: function(num,fix){
            var $num = Number(num),$fix = Number(fix);
            return $num.toFixed($fix)
        },
        resetList: function () {

        },
        showSpec: function () {
            if( orderDetail.orderList.length === 0 ) {
                layer.msg('请先至少录入一个商品信息');
                return false;
            }
            tips = layer.open({
                type: 1,
                skin: 'layui-layer-rim', //加上边框
                area: ['950px', '550px'], //宽高
                title: false,
                closeBtn: 1,
                shadeClose: false,
                content: $('#order_specifications_form')
            });
        },
        selectSpecDetail:function (index) {
            var id = Number(event.target.value);
            this.specOrder.order_specifications_cargo[index].en_product_name = orderDetail.orderList[id].en_product_name
            this.specOrder.order_specifications_cargo[index].product_name = orderDetail.orderList[id].product_name
        },
        addSpecCargo: function () {
            this.specOrder.order_specifications_cargo.push({
                product_name:'',
                en_product_name:'',
                product_count: null
            });
        },
        removeSpecCargo: function (index) {
            this.specOrder.order_specifications_cargo.splice(index,1);
            this.recountTotal()
        },
        addOrderSpec:function () {
            var flag = true;
            var msg = '';
            var specOrder = this.specOrder;
            var weight = parseFloat(specOrder.order_specifications_weight).toFixed(1);
            var length = parseFloat(specOrder.order_specifications_length).toFixed(1);
            var width = parseFloat(specOrder.order_specifications_width).toFixed(1);
            var height = parseFloat(specOrder.order_specifications_height).toFixed(1);
            var count = parseInt(specOrder.order_specifications_count);
            var detail = [];
            var detail_number = [];
            var cargo = this.specOrder.order_specifications_cargo;

            if( isNaN(weight) || weight <= 0 ) {
                msg += '请输入重量<br />';
                flag = false;
            }
            if( isNaN(length) || length <= 0 ) {
                msg += '请输入长度<br />';
                flag = false;
            }
            if( isNaN(width) || width <= 0 ) {
                msg += '请输入宽度<br />';
                flag = false;
            }
            if( isNaN(height) || height <= 0 ) {
                msg += '请输入高度<br />';
                flag = false;
            }
            if( cargo.length < 1 ) {
                msg += '请至少选择一个商品<br />';
                flag = false;
            }
            if( isNaN(count) || count <= 0 ) {
                msg += '请输入箱数<br />';
                flag = false;
            }
            for(var i in cargo ){
                for(var j in cargo[i]){
                    if(cargo[i][j] === '' || cargo[i][j] === null ){
                        msg += '请补全信息<br />';
                        flag = false;
                        break;
                    }
                }
            }
            if( flag ) {
                var  start = 1;

                var detailTemp = {
                    weight : weight,
                    length : length,
                    width : width,
                    height : height,
                    charging_weight : (length * width * height)/5000 > weight ? (length * width * height)/5000 : weight,
                    count : count
                };

                for(var i = 0 ;i < cargo.length;i++){
                    var index = cargo[i].product_index;
                    var detailCargo = $.extend([],cargo[i],orderDetail.orderList[index]);
                    detail.push(detailCargo);
                    start += Number(count)
//                        detail_number[i] = cargo[i].product_count;
                }
//                    console.log(detail);
//                    return;

                detailTemp.cargo = detail;
                if(this.editingIndex>=0){
                    this.specList[this.editingIndex] = detailTemp;
                }else{
                    this.specList.push(detailTemp);
                }
                console.log("specList,",this.specList);
                this.recountTotal();
                this.resetId();
                hide_order_specifications_form();
                /*hide_order_specifications_form();
                 refresh_order_specifications();
                 empty_order_specifications_form();*/
                this.specOrder = {
                    order_specifications_weight: "",
                    order_specifications_length: "",
                    order_specifications_width: "",
                    order_specifications_height: "",
                    order_specifications_count: "",
                    order_specifications_cargo: [
                        {
                            product_index: '',
                            en_product_name: '',
                            product_count: null
                        }
                    ]
                };
                return true;
            } else {
                layer.msg(msg);
                return false;
            }
        },
        recountTotal: function () {
            this.total_order_specifications_count = this.total_weight = this.total_rate = this.total_charging_weight = 0;
            var tempSpec;
            var weight = 0/*重量*/ , rate = 0 /*材积重*/, charging_weight = 0/*计算重量*/;
            for(var i in this.specList){
                tempSpec = this.specList[i];
                weight = tempSpec.weight;
                this.total_weight += weight;
                rate = tempSpec.charging_weight;
                this.total_rate += rate;
                charging_weight += (weight > rate ? weight*tempSpec.count : rate*tempSpec.count);
                console.log("charging_weight",charging_weight);
            }
            this.total_weight = this.fixedNum(this.total_weight,2);
            this.total_rate = this.fixedNum(this.total_rate,2);
            this.total_charging_weight = this.fixedNum(charging_weight,2);
        },
        resetId : function () {
            var count = 0,id = '',start = 1,end = 0,total = 0;
            for(var i in this.specList){
                count = this.specList[i].count;
                total += count;
                end = start + count - 1;
                this.specList[i].id = start + '-' + end;
                start = total+1;
//                    console.log("this.specList[i]",i,this.specList[i]);
            }
        },
        editSpecOrder: function (index) {
            console.log("index",index);
            this.editingIndex = index>=0 ? index : -1;
            var editOrder = this.specList[index];
            if(this.editingIndex >= 0){
                this.specOrder = {
                    order_specifications_weight: editOrder.weight,
                    order_specifications_length: editOrder.length,
                    order_specifications_width: editOrder.width,
                    order_specifications_height: editOrder.height,
                    order_specifications_count: editOrder.count,
                    order_specifications_cargo: editOrder.cargo
                };
            }
            else{
                this.specOrder = {
                    order_specifications_weight: "",
                    order_specifications_length: "",
                    order_specifications_width: "",
                    order_specifications_height: "",
                    order_specifications_count: "",
                    order_specifications_cargo: [
                        {
                            product_index: '',
                            en_product_name: '',
                            product_count: null
                        }
                    ]
                }
            }
            this.showSpec()
        },
        removeSpecOrder: function (index) {
            this.specList.splice(index,1);
            this.recountTotal();
            this.resetId();
        }
    }
});