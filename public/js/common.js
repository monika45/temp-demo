$(function() {

});

function requestApi(obj) {
    var requestParam = Object.assign({
        url: '',
        method: 'GET',
        userToken: '',
        data: {},
        callback: null,
        // 是否显示加载提示
        showLoading: true,
        // 内容容器ID，用于显示加载提示
        contentWrapId: ''
    }, obj);
    if (requestParam.showLoading) {
        var tpl = $('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
        var contentWrapEle = $('#' + requestParam.contentWrapId);
        contentWrapEle.css({
            'position': 'relative'
        });
        tpl.css({
            'position': 'absolute',
            'top': '0',
            'width': '100%',
            'height': '100%',
            'text-align': 'center',
            'padding-top': (contentWrapEle.height() - 80) / 2 + 'px',
            'font-size': '3rem',
            'color': '#999',
            'background': 'rgba(255, 255, 255, 0.5)'
        });
        contentWrapEle.append(tpl);
    }
    var option = {
        url: requestParam.url,
        method: requestParam.method,
        contentType: 'application/json',
        headers: {},
        data: requestParam.data,
        dataType: 'json',
        success: function(res) {
            if (requestParam.showLoading) {
                $('#' + requestParam.contentWrapId).children('.overlay')[0].remove();
            }
            if (res.err != 0) {
                console.log('error:' + res.msg);
            } else if (requestParam.callback) {
                // 接口返回了data,把data传回去，否则把msg传回去
                if (res.hasOwnProperty('data')) {
                    requestParam.callback(res.data);
                } else {
                    requestParam.callback(res.msg);
                }
            }
        },
        error: function (res) {
            console.log('error');
            console.log(res);
        }
    };
    if (requestParam.userToken) {
        option.headers['Authorization'] = 'Bearer ' + requestParam.userToken;
    }
    $.ajax(option);
}




