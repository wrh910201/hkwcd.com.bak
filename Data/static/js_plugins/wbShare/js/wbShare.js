$(document).ready(function () {
    $('#welcome').fadeTo(2000, 1).delay(2000).animate({
        opacity: 0,
        marginTop: '-=200'
    },
	1000,
	function () {
	    $('#welcome').hide();
	});
    $(window).scroll(function () {
        if ($(window).scrollTop() > 50) {
            $('#jump li:eq(0)').fadeIn(800);
        } else {
            $('#jump li:eq(0)').fadeOut(800);
        }
    });
    $("#gotop").click(function () {
        $('body,html').animate({
            scrollTop: 0
        },
		1000);
        return false;
    });
});
function showEWM() {
    document.getElementById("EWM").style.display = 'block';
}
function hideEWM() {
    document.getElementById("EWM").style.display = 'none';
}
function showWHATAPP() {
    document.getElementById("WHATAPP").style.display = 'block';
}
function hideWHATAPP() {
    document.getElementById("WHATAPP").style.display = 'none';
}
 