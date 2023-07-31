$(function () {
    /*$('input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue'
    });*/

    $(".select-all-checkbox").click(function () {
        var checkState = this.checked;
        if(!checkState) {
            $("input[name='select_id[]']").prop("checked", false);
            $(".select-all-checkbox").prop("checked", false);
        } else {
            $("input[name='select_id[]']").prop("checked", true);
        }
    });
});

//通用获取列表方法
function dberpAjaxList(listUrl,showDivDd) {
    $.get(listUrl,{showDivDd:showDivDd}, function(html){
        $("#"+showDivDd).html(html);
    });
}

/**
 * 通用提示信息
 * @param msg
 */
function erpMessage(msg) {
    layer.msg(msg);
}

function openDocsUrl(titleContent, docsUrl, clickText) {
    layer.open({
        title: titleContent,
        type: 2,
        area: ['40%', '80%'],
        maxmin: true,
        offset: 'rb',
        shade: 0,
        content: docsUrl,
        btn: [clickText],
        btn1: function (index) {
            layer.close(index);
            window.open(docsUrl);
        },min: function(index) {
            setTimeout(function(){
                index.css({'left': 'inherit','right':'0px'})
            })
        }
    });
}

/**
 * 删除问询
 * @param msg
 * @param url
 * @param toUrlState
 */
function deleteConfirm(msg, url, toUrlState) {
    layer.confirm(msg, {}, function () {
        if(toUrlState == 'false') {
            $.get(url, {}, function (data) {
                if(data.state == 'ok') window.location.reload();
                else {
                    if(data.hasOwnProperty("msg")) {
                        layer.msg(data.msg);
                    } else window.location.reload();
                }
            });
        } else window.location.href = url;
    })
}