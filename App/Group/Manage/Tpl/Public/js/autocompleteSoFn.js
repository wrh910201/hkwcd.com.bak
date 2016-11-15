
$().ready(function () {

    $("#gjj").autocomplete(countrylist, {
        minChars: 0,
        max: 100,
        width: 350,
        autoFill: false,
        matchContains: true,
        cacheLength: 1,
        scrollHeight: 2000,
        formatItem: function (data, i, total) { 
            return data.name
        },
        formatMatch: function (data, i, total) {
            return data.name;
        },
        formatResult: function (data, value) {
            return data.to;
        }
    }).result(function (event, data, formatted) { //回调  
        $("#gjj").val(data.to + "  " + data.to1);
        $("#lmdd").val(data.to);
    });

    $("#gj").autocomplete(countrylist, {
        minChars: 0,
        max: 100,
        width: 350,
        autoFill: false,
        matchContains: true,
        cacheLength: 1,
        scrollHeight: 20000,
        formatItem: function (data, i, total) {
            return data.name
        },
        formatMatch: function (data, i, total) {
            return data.name;
        },
        formatResult: function (data, value) {
            return data.to;
        }
    }).result(function (event, data, formatted) { //回调  
        $("#gj").val(data.to + "  " + data.to1);
        $("#chs").val(data.to);
    });


    $("#lmdd").autocomplete(citylist, {
        minChars: 0,
        max: 100,
        width: 350,
        autoFill: false,
        matchContains: true,
        cacheLength: 1,
        scrollHeight: 2000,
        formatItem: function (data, i, total) { 
          return data.name
        },
      formatMatch: function (data, i, total) { 
          return data.name;
        },
        formatResult: function (data, value) {
            return data.to;
        }
    }).result(function (event, data, formatted) { //回调  
        $("#lmdd").val(data.to + "  " + data.to1);
        $("#ckh").val(data.to);
    });

    $("#chs").autocomplete(citylist, {
        minChars: 0,
        max: 100,
        width: 350,
        autoFill: false,
        matchContains: true,
        cacheLength: 1,
        scrollHeight: 2000,
        formatItem: function (data, i, total) {
            return data.name
        },
        formatMatch: function (data, i, total) {
            return data.name;
        },
        formatResult: function (data, value) {
            return data.to;
        }
    }).result(function (event, data, formatted) { //回调  
        $("#chs").val(data.to + "  " + data.to1);
    });



    $("#ckh").autocomplete(ziplist, {
                     minChars: 0, 
                    max:100, 
                    width: 350,
                    autoFill: false,
                    matchContains:true,
                    cacheLength:1, 
                    scrollHeight: 2000,
            formatItem: function(data, i, total) {  
                   return data.name
             },
            formatMatch: function(data, i, total) {
                        return data.name; 
             },
            formatResult: function(data, value) { 
                        return data.to;
             }
            }).result(function(event, data, formatted) { //回调   
                $("#ckh").val(data.to); 
           });

});
        