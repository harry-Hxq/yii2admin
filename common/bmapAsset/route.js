


    // 百度地图API功能
function G(id) {
    return document.getElementById(id);
}
var marker = {}
var map = new BMap.Map("allmap");

var latitude = $("#latitude").val();
var longitude = $("#longitude").val();
var point = {};
if(latitude && longitude){
    point = new BMap.Point(longitude, latitude);
    map.centerAndZoom(point, 15);
    map.enableScrollWheelZoom(true);     //开启鼠标滚轮缩放
    map.setCurrentCity("龙岩");// 初始化地图,设置城市和地图级别。
    marker = new BMap.Marker(point)
    map.addOverlay(marker);    //添加标注

}else{
    point = new BMap.Point(117.02147, 25.118569);
    map.centerAndZoom(point, 15);
    map.enableScrollWheelZoom(true);     //开启鼠标滚轮缩放
    map.setCurrentCity("龙岩");// 初始化地图,设置城市和地图级别。
}



    var geoc = new BMap.Geocoder();  //位置转换
    function showInfo(e){
        geoc.getLocation(e.point, function(rs){

            $("#remark").val(rs.address)
            $("#latitude").val(e.point.lat)
            $("#longitude").val(e.point.lng)

            //remove markers
            let allOverlay = map.getOverlays();
            console.log(allOverlay);
            let lengthOverLay  = allOverlay.length;
            for (let i=0;i<lengthOverLay; i++){
                map.removeOverlay(allOverlay[i]);  //删除定位的点
            }

            marker = new BMap.Marker(e.point)
            map.addOverlay(marker);    //添加标注

        });

    }

map.addEventListener("click",showInfo);

var ac = new BMap.Autocomplete(    //建立一个自动完成的对象
    {"input" : "suggestId","location" : map}
    );

ac.addEventListener("onhighlight", function(e) {  //鼠标放在下拉列表上的事件
    var str = "";
    var _value = e.fromitem.value;
    var value = "";
    if (e.fromitem.index > -1) {
        value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
    }
    str = "FromItem<br />index = " + e.fromitem.index + "<br />value = " + value;

    value = "";
    if (e.toitem.index > -1) {
        _value = e.toitem.value;
        value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
    }
    str += "<br />ToItem<br />index = " + e.toitem.index + "<br />value = " + value;
    G("searchResultPanel").innerHTML = str;
});

var myValue;
ac.addEventListener("onconfirm", function(e) {    //鼠标点击下拉列表后的事件
    var _value = e.item.value;
    myValue = _value.province + _value.city + _value.district + _value.street + _value.business;
    G("searchResultPanel").innerHTML = "onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue;
    setPlace();
})


function setPlace(){
    map.clearOverlays();    //清除地图上所有覆盖物
    function myFun(){
        var pp = local.getResults().getPoi(0).point;    //获取第一个智能搜索的结果

        $("#remark").val(myValue)
        $("#latitude").val(pp.lat)
        $("#longitude").val(pp.lng)

        map.centerAndZoom(pp, 16);
        marker = new BMap.Marker(pp)
        map.addOverlay(marker);    //添加标注
    }
    var local = new BMap.LocalSearch(map, { //智能搜索
        onSearchComplete: myFun
    });
    local.search(myValue);
}

