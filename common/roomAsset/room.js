/**
 * Created by hp on 2018/5/15.
 */

$(function(){
    $(".roomurl").on("click",function () {

        var roomurl =  $("#roomurl").val()
        console.log(11323)
        $.get({
            url:'/admin/room/ajax_roomurl?roomurl='+roomurl,
            type: 'get',
            dataType:'json',
            success:function(data){
                if(data.code === 200){
                    alert("保存成功");
                    // $("#roomurl").val(roomurl)
                }else{
                    // alert('登录过期,请重新登录！');
                    //window.location.href="http://" + location.host + "/?room=" + info['roomid'];
                    // window.location.href="http://" + location.host + "/qr.php?room=" + info['roomid'];
                    // window.location.href="http://" + location.host + "/login.php?room=" + info['roomid'];
                }
            },
            error:function(){}
        });
    })
})