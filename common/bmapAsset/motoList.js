

$(function(){
    // 百度地图API功能


    // $.get('/api/v1/user/get-all-stop',{},function (res) {
    //     console.log(res);
    //
    //     // 编写自定义函数,创建标注
    //     function addMarker(point){
    //         var marker = new BMap.Marker(point);
    //         map.addOverlay(marker);
    //     }
    //     // 随机向地图添加25个标注
    //     var length = res.data.length;
    //     for (var i = 0; i < length; i ++) {
    //         var point = new BMap.Point(res.data[i].longitude,res.data[i].latitude);
    //         addMarker(point);
    //     }
    // })
})

var address = ''
var latitude = ''
var longitude = ''
var pointMoto = ''

var map = new BMap.Map("allmap");
var point = new BMap.Point(117.02147, 25.118569);
map.centerAndZoom(point, 15);
map.enableScrollWheelZoom(true);     //开启鼠标滚轮缩放
map.setCurrentCity("龙岩");
var geoc = new BMap.Geocoder();  //位置转换

map.addEventListener("click", function (e) {
    geoc.getLocation(e.point, function(rs){
        pointMoto = e.point;
        address = rs.address;
        latitude = e.point.lat;
        longitude = e.point.lng;
        $("#address").text(rs.address)
        $("#confirmMoto").modal('show');

    });
    // alert(e.point.lng+","+e.point.lat)
});
function submitMoto(){
    let data = {
        start_time: $("#start_time").val(),
        end_time: $("#end_time").val(),
        latitude: latitude,
        longitude: longitude,
        remark: address,
    }
    console.log(data)
    $.post('/api/v1/user/edit-moto',data,function (res) {
        console.log(res);
        if(res.code === 200){
            alert('添加成功');
            addMarker(pointMoto)
        }
    })
}


function addMarker(point){
    var marker = new BMap.Marker(point);
    map.addOverlay(marker);
    marker.addEventListener("rightclick", function (e) {
        console.log(e);
        // alert(e.point.lng+","+e.point.lat)
    });
}

$.get('/api/v1/user/moto-list',{},function (res) {

    // 随机向地图添加25个标注
    var length = res.data.length;
    for (var i = 0; i < length; i ++) {
        var point = new BMap.Point(res.data[i].longitude,res.data[i].latitude);
        addMarker(point);
    }
})

