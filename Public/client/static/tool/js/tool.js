/**
 * Created by 矢吹丈 on 2017/7/6.
 */
function getModalData(id){
    var inputs = $(id).find("input,select,textarea");
    //ajax这里传值，必须是对象，辣鸡，数据是不会有东西的
    var data = new Object();
    for (var i = 0; i < inputs.length; i++) {
        if ($(inputs[i]).attr("type") == "checkbox") {
            if (data[$(inputs[i]).attr("name")]) {
                data[$(inputs[i]).attr("name")].push($(inputs[i]).val());
            } else {
                data[$(inputs[i]).attr("name")] = [];
                data[$(inputs[i]).attr("name")].push($(inputs[i]).val());
            }

        } else if($(inputs[i]).attr("type") == "radio") {
            data[$(inputs[i]).attr("name")] = $(id).find("input[name="+$(inputs[i]).attr("name")+"]:checked").val();
        } else{
            data[$(inputs[i]).attr("name")] = $(inputs[i]).val();

        }
    }


    return data;
}

function setModalData(data,id){
    var inputs = $(id).find("input,select,textarea");
    console.log(inputs);
    for (var i = 0; i < inputs.length; i++) {
        if ($(inputs[i]).attr("type") == "checkbox") {
            if(data[$(input[i]).attr("name")] instanceof Array||data[$(input[i]).attr("name")] instanceof Object){
                var options = data[input[i].attr("name")];
                if(options.indexOf($(input[i]).val())){
                    $(input[i]).attr("checked",true);
                }
            }else{
                var options = data[input[i].attr("name")].split(",");
                if(options.indexOf($(input[i]).val())){
                    $(input[i]).attr("checked",true);
                }
            }
        }else if($(inputs[i]).attr("type") == "radio"){
            $(id).find("input[name="+$(inputs[i]).attr("name")+"][value="+data[$(inputs[i]).attr("name")]+"]").attr("checked",true);
        } else {
            $(inputs[i]).val(data[$(inputs[i]).attr("name")]);
        }
    }


    return data;
}
//bootstrap-table
function myInitTable(id,url,columns,query_data){
    var $table = $(id);
    $table.bootstrapTable({
        method: "post",  //使用get请求到服务器获取数据
        url: url, //获取数据的Servlet地址
        striped: true,  //表格显示条纹
        pagination: true, //启动分页
        pageSize: 10,  //每页显示的记录数
        pageNumber: 1, //当前第几页
        pageList: [5, 10, 15, 20, 25],  //记录数可选列表
        search: true,  //是否启用查询
        showColumns: false,  //显示下拉框勾选要显示的列
        showRefresh: true,  //显示刷新按钮
        sidePagination: "server", //表示服务端请求
        dataType: 'json',
        sortOrder: 'desc',
        showExport: false,
        //设置为undefined可以获取pageNumber，pageSize，searchText，sortName，sortOrder
        //设置为limit可以获取limit, offset, search, sort, order
        queryParamsType: "limit",
        queryParams: function queryParams(params) {   //设置查询参数
            if(query_data){
                for(var key in query_data){
                    params[key] = query_data[key];
                }
            }
            return params;
        },
        responseHandler: function responseHandler(res) {
            var result = {
                rows: res.data,
                total: res.total
            };
            return result;
        },
        columns: columns
    });

    setTimeout(function () {
        $table.bootstrapTable('resetView');
    }, 200);
    $table.on('check.bs.table uncheck.bs.table ' +
        'check-all.bs.table uncheck-all.bs.table', function () {

        selections = getIdSelections();

    });


    function getIdSelections() {
        return $.map($table.bootstrapTable('getSelections'), function (row) {
            return row.id
        });
    }


    return $table;
}