function AddFavorite(sURL, sTitle) {
    sURL = encodeURI(sURL);
    try {
        window.external.addFavorite(sURL, sTitle);
    } catch (e) {
        try {
            window.sidebar.addPanel(sTitle, sURL, "");
        } catch (e) {
            alert("抱歉，您的浏览器不支持自动加入收藏，请使用Ctrl+D进行添加,或使用浏览器菜单手动设置!");
        }
    }
}

var t;

//function changepic() {

//   if ($("#sbanner").attr("class") == "img_div2") {
//        $("#sbanner").attr("class", "img_div1");
//    } else {
//        $("#sbanner").attr("class", "img_div2");
//    } 
//    t = setTimeout("changepic()", 5000);
//}
// 

//$(document).ready(function () {  
//    clearInterval(t); 
//    changepic();  
//}); 


function kwsubmit(keyword, myform) {
    $("#keyword").val(keyword);
    document.getElementById(myform).submit();
}

//计算操作 
function CalcStructureRow(objid) {
    $("#isshow").html("填写数值后，请点击按钮计算，如有多计算请点击按钮添加行");
    var length = $("#length" + objid).val();
    var width = $("#width" + objid).val();
    var height = $("#height" + objid).val();
    if (length == '' || width == '' || height == '') {
        $("#isshow").html("长度、阔度、高度不能为空，请输入有效数值");
        return;
    }
    if (!checkRate("length" + objid) || !checkRate("width" + objid) || !checkRate("height" + objid)) {
        $("#isshow").html("请输入正确的数值,只允许输入整数");
        return;
    }

    //长度 * 宽度 * 高度 /5000 ，不足按0.5计算
    var calc = Math.round((parseFloat(length) * parseFloat(width) * parseFloat(height) / 5000) * 100) / 100;
    if (calc <= 0.5) {
        calc = 0.5;
    }
    $("#weight" + objid).val(calc);

    var sumlength = 0;
    var sumwidth = 0;
    var sumheight = 0;
    var sumweight = 0;
    $.each($(":input[name='length']"), function () {
        sumlength += parseFloat(this.value);
    });
    $.each($(":input[name='width']"), function () {
        sumwidth += parseFloat(this.value);
    });
    $.each($(":input[name='height']"), function () {
        sumheight += parseFloat(this.value);
    });
    $.each($(":input[name='weight']"), function () {
        sumweight += parseFloat(this.value);
    });
    if (sumlength.toString() == 'NaN' || sumlength.toString() == 'NaN' || sumlength.toString() == 'NaN' || sumweight.toString() == 'NaN') {
        $("#isshow").html("计算时其他计算项目不能为空，请输入有效数值，合计数值为无效，则为0表示");
        return;
    }
    $("#alllength").val(sumlength);
    $("#allwidth").val(sumwidth);
    $("#allheight").val(sumheight);
    $("#allweight").val(sumweight);
}

function checkRate(objid) {
    var nubmer = parseInt($("#" + objid).val());
    if (isNaN(nubmer) || nubmer <= 0 || !(/^\d+$/.test(nubmer))) {
        return false;
    } else {
        return true;
    }
}

function addTr2(tab, row) {
    var rowIndex = $("#rowid").val();
    rowIndex = parseInt(rowIndex) + parseInt(1);
    var trHtml = "<tr id=" + rowIndex + "><td><input id='length" + rowIndex + "' name='length' class='input' type='text'/> CM</td><td><input id='width" + rowIndex + "'  name='width' class='input' type='text'/> CM</td><td><input id='height" + rowIndex + "' name='height' class='input' type='text'/> CM</td><td><input id='btnDelRow' class='btnred' type='button' value='计算' onclick='CalcStructureRow(" + rowIndex + ")'/> <input id='btnDelRow' class='btn' type='button' value='删除行' onclick='delTr(" + rowIndex + ")'/></td><td><input id='weight" + rowIndex + "' name='weight' class='input' type='text'/> KG</td></tr>";
    addTr(tab, row, trHtml);
    $("#rowid").val(rowIndex);
}

function delTr(row) {
    $("#" + row).remove();
    var sumlength = 0;
    var sumwidth = 0;
    var sumheight = 0;
    var sumweight = 0;
    $.each($(":input[name='length']"), function () {
        sumlength += parseFloat(this.value);
    });
    $.each($(":input[name='width']"), function () {
        sumwidth += parseFloat(this.value);
    });
    $.each($(":input[name='height']"), function () {
        sumheight += parseFloat(this.value);
    });
    $.each($(":input[name='weight']"), function () {
        sumweight += parseFloat(this.value);
    });
    if (sumlength.toString() == 'NaN' || sumlength.toString() == 'NaN' || sumlength.toString() == 'NaN' || sumweight.toString() == 'NaN') {
        $("#isshow").html("计算时其他计算项目不能为空，请输入有效数值，合计数值为无效，则为0表示");
        return;
    }
    $("#alllength").val(sumlength);
    $("#allwidth").val(sumwidth);
    $("#allheight").val(sumheight);
    $("#allweight").val(sumweight); 
}


function addTr(tab, row, trHtml) {
    //获取table最后一行 $("#tab tr:last")
    //获取table第一行 $("#tab tr").eq(0)
    //获取table倒数第二行 $("#tab tr").eq(-2)
    var $tr = $("#" + tab + " tr").eq(row);
    if ($tr.size() == 0) {
        alert("指定的table id或行数不存在！");
        return;
    }
    $tr.after(trHtml);
}
