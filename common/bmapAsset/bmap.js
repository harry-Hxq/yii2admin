$(function(){
    // 百度地图API功能

    $.get('/api/v1/user/get-all-stop',{},function (res) {
        console.log(res);
        var map = new BMap.Map("allmap");
        var point = new BMap.Point(117.02147, 25.118569);
        map.centerAndZoom(point, 15);
        map.enableScrollWheelZoom(true);     //开启鼠标滚轮缩放
        map.setCurrentCity("龙岩");
        // 编写自定义函数,创建标注
        function addMarker(point){
            var marker = new BMap.Marker(point);
            map.addOverlay(marker);
        }
        // 随机向地图添加25个标注
        var length = res.data.length;
        for (var i = 0; i < length; i ++) {
            var point = new BMap.Point(res.data[i].longitude,res.data[i].latitude);
            addMarker(point);
        }
    })


})