/**
 * Created by arao on 2015/12/24.
 */
/* 侧栏导航条 */
function menu1(dx, dx1, dx3) {
    var urla = window.location.href;
    tmp = urla.split("/");
    wza = tmp[tmp.length - 1];
    wza = wza.split("?")[0];
    $(dx).slideDown();
    $("a.nav_top_item").removeClass("hover");
    $(dx3).addClass("hover").siblings().removeClass("hover");
    $(".aside ul li a").each(function () {
        var href = $(this).attr("href");
        if ($(this).attr("href") == wza) {
            $(this).parent().addClass("current");
        }else {
            $(this).parent().removeClass("current");
        }
    })
}
/* END 侧栏导航条 */

function show_loading() {
    loading = layer.load(0, { shade: 0.4 });
}

function hide_loading() {
    layer.close(loading);
}

function matchStart(params, data) {
    // If there are no search terms, return all of the data
    if ($.trim(params.term) === '') {
        return data;
    }

    // Skip if there is no 'children' property
    if (typeof data.text === 'undefined') {
        return null;
    }

    // `data.children` contains the actual options that we are matching against
    if (data.text.toUpperCase().indexOf(params.term.toUpperCase()) == 0 ) {
        var modifiedData = $.extend({}, data, true);
        // modifiedData.text += ' (matched)';

        // You can return modified objects from here
        // This includes matching the `children` how you want in nested data sets
        return modifiedData;
    }

    // Return `null` if the term should not be displayed
    return null;
}

