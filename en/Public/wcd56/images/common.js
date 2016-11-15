function AddFavorite(sURL, sTitle) {
    sURL = encodeURI(sURL);
    try {
        window.external.addFavorite(sURL, sTitle);
    } catch (e) {
        try {
            window.sidebar.addPanel(sTitle, sURL, "");
        } catch (e) {
            alert("Sorry, your browser does not support automatic Favorite, use Ctrl + D to add, or use the browser menu to manually set!");
        }
    }
}


function changepic() {
    if ($("#sbanner").attr("class") == "img_div1") {
        $("#sbanner").attr("class", "img_div2");
    } else {
        $("#sbanner").attr("class", "img_div1");
    }
}
setTimeout("changepic()", 5000);   


function kwsubmit(keyword, myform) {
    $("#keyword").val(keyword);
    document.getElementById(myform).submit();
}

//计算操作 
function CalcStructureRow(objid) {
    $("#isshow").html("Click the button to fill in the number, if there are much more number,  please click button to add line .");
    var length = $("#length" + objid).val();
    var width = $("#width" + objid).val();
    var height = $("#height" + objid).val();
    if (length == '' || width == '' || height == '') {
        $("#isshow").html("Length, width, height can not be empty, please enter valid values");
        return;
    }
    if (!checkRate("length" + objid) || !checkRate("width" + objid) || !checkRate("height" + objid)) {
        $("#isshow").html("Please enter the correct values，are only allowed to enter an integer.");
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
        $("#isshow").html("The other projects can not be empty when calculating. Please enter a valid value, total value for the invalid with zero.");
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
    var trHtml = "<tr id=" + rowIndex + "><td><input id='length" + rowIndex + "' name='length' class='input' type='text'/> CM</td><td><input id='width" + rowIndex + "'  name='width' class='input' type='text'/> CM</td><td><input id='height" + rowIndex + "' name='height' class='input' type='text'/> CM</td><td><input id='btnDelRow' class='btnred' type='button' value='Calc' onclick='CalcStructureRow(" + rowIndex + ")'/> <input id='btnDelRow' class='btn' type='button' value='Del Row' onclick='delTr(" + rowIndex + ")'/></td><td><input id='weight" + rowIndex + "' name='weight' class='input' type='text'/> KG</td></tr>";
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
        $("#isshow").html("The other projects can not be empty when calculating. Please enter a valid value, total value for the invalid with zero.");
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
