<template>
  <div id="box">
    <div id="title">若比邻</div>
    <div id="top">
      <div id="left" @click="expand">
        <img :src="icon_img" alt style="width:30px;height:30px;margin:5px 0;">
      </div>
      <div id="right">
        <img :src="change" @click="changeStyle" style="width:30px;height:30px;margin:5px 0;">
      </div>
    </div>
    <header></header>
    <div id="map"></div>
    <div id="publish" @click="publish">+</div>
    <Modal title="About" ref="modal" @refresh="getData" @preview="preview"/>
    <Msgbox v-show="msgBoxShow" ref="msgBox" @refresh="getData" @preview="preview"/>
    <Itembox v-show="itemBoxShow" ref="itemBox" @cancel="cancel" @refresh="getData" @preview="preview"/>
    <previewimg :imgUrl="previewImg" v-show="isPreview" @cancelPreview="cancelPreview"/>
  </div>
</template>
 
<script>
// import Amap from 'AMap'
import Modal from "../components/modal.vue";
import Msgbox from "../components/messageBox.vue";
import Itembox from "../components/itemBox";
import previewimg from "../components/previewImg.vue";

import left from "@/assets/icon/left.png";
import right from "@/assets/icon/right.png";
import fire from "@/assets/icon/fire.gif";
import change from "@/assets/icon/icon_change.png";

import { get } from "../utils/httputil.js";

export default {
  name: "Map",
  data() {
    return {
      fire,
      left,
      right,
      icon_img: right,
      change,
      itemBoxShow: false,
      msgBoxShow: false,
      Map: {},
      currentStyle: 0,
      MapStyle: [
        "amap://styles/whitesmoke",
        "amap://styles/darkblue",
        "amap://styles/macaron",
        "amap://styles/dark",
        "amap://styles/light"
      ],
      mapInfo: {
        mapStyle: "amap://styles/whitesmoke",
        resizeEnable: true,
        pitch: 58, // 地图俯仰角度，有效范围 0 度- 83 度
        viewMode: "3D" // 地图模式
        // layers: [this.roadNet]
      },
      position: {
        latitude: null,
        longitude: null
      },
      modalShowData: {
        title: "About",
        content: "毕业生留念地图..."
      },
      releaseList: [],
      // 图片预览路径
      previewImg:"",
      // 是否预览
      isPreview:false
    };
  },
  components: {
    Modal,
    Msgbox,
    Itembox,
    previewimg
  },
  mounted() {
    let self = this;
    let docHeight = window.innerHeight;
    document.querySelector("#box").style.height = docHeight + "px";
    // 加载高德CDN
    let aMapScript = document.createElement("script");
    aMapScript.setAttribute(
      "src",
      "https://webapi.amap.com/maps?v=1.4.2&key=400e0e0777def2b66af2e4493190fbb9"
    );
    document.head.appendChild(aMapScript);

    aMapScript.onload = function() {
      self.getMap();
    };
  },
  methods: {
    // 全局数据更新
    getData() {
      let that = this;
      // 本地存储更新判断
      let getToArr=function(arr){
        if(localStorage.getItem('list')){
          var lastList=JSON.parse(localStorage.getItem('list'));
        }else{
          var lastList=[];
        }
        for(let i=0,len=arr.length;i<len;i++){
          // 判断动态是否新增
          let isExt=false;
          for(let j=0;j<lastList.length;j++){
            if(arr[i].id==lastList[j].id){
              isExt=true;
              // 如果动态不是新增就要更新其点赞数
              lastList[j].hot=arr[i].hot;
            }
          }
          if(!isExt){
            arr[i].isLiked=0;
            lastList.push(arr[i]);
          }
        }
        return lastList;
      }
      get(
        "release/getList.php",
        res => {
          if (res.data.code == 0) {
            let list = getToArr(res.data.data);
            for (let i = 0; i < list.length; i++) {
              list[i].dis = that.getDis(list[i].longitude, list[i].latitude);
              if(list[i].image!=""){
                list[i].imgList=list[i].image.split("#");
              }
            }
            that.releaseList = list;
            console.log(that.releaseList);
            // 本地存储
            localStorage.setItem('list',JSON.stringify(list));
            that.$refs.msgBox.getData(that.releaseList);
            that.makeMarker();
          } else {
            alert("服务器错误！");
          }
        },
        err => {}
      );
    },
    getMap() {
      // var satellite = new AMap.TileLayer.Satellite();
      // this.roadNet = new AMap.TileLayer.RoadNet();
      // var buildings = new AMap.Buildings({
      //   zooms: [16, 18],
      //   zIndex: 10,
      //   heightFactor: 2 //2倍于默认高度，3D下有效
      // }); //楼块图层
      let self = this;
      // 加载地图
      this.Map = new AMap.Map("map", self.mapInfo);

      // 使用定位插件
      this.Map.plugin("AMap.Geolocation", function() {
        var geolocation = new AMap.Geolocation({
          enableHighAccuracy: true, //是否使用高精度定位，默认:true
          timeout: 5000, //超过10秒后停止定位，默认：无穷大
          maximumAge: 1000, //定位结果缓存0毫秒，默认：0
          convert: true, //自动偏移坐标，偏移后的坐标为高德坐标，默认：true
          showButton: true, //显示定位按钮，默认：true
          buttonPosition: "LB", //定位按钮停靠位置，默认：'LB'，左下角
          buttonOffset: new AMap.Pixel(10, 20), //定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
          showMarker: true, //定位成功后在定位到的位置显示点标记，默认：true
          showCircle: true, //定位成功后用圆圈表示定位精度范围，默认：true
          panToLocation: true, //定位成功后将定位到的位置作为地图中心点，默认：true
          zoomToAccuracy: true //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
        });
        self.Map.addControl(geolocation);
        geolocation.getCurrentPosition();
        AMap.event.addListener(geolocation, "complete", function(res) {
          console.log(res);
          self.position.latitude = res.position.N;
          self.position.longitude = res.position.L;
          self.getData();
        }); //返回定位信息
        AMap.event.addListener(geolocation, "error", function(err) {
          console.log(err);
        }); //返回定位出错信息
      });

      // 设置地图中心点
      this.Map.setZoomAndCenter(18, [
        this.position.longitude,
        this.position.latitude
      ]);
    },
    getPosition() {
      let that = this;
      // if (navigator.geolocation) {
      //   navigator.geolocation.watchPosition(
      //     function(position) {
      //       console.log(position);
      //       that.position.latitude = position.coords.latitude;
      //       that.position.longitude = position.coords.longitude;
      //     },
      //     function(error) {
      //       switch (error.code) {
      //         case error.PERMISSION_DENIED:
      //           alert("用户拒绝对获取地理位置的请求。");
      //           break;
      //         case error.POSITION_UNAVAILABLE:
      //           alert("位置信息是不可用的。");
      //           break;
      //         case error.TIMEOUT:
      //           alert("请求用户地理位置超时。");
      //           break;
      //         case error.UNKNOWN_ERROR:
      //           alert("未知错误。");
      //           break;
      //       }
      //     }
      //   );
      // } else {
      //   alert("浏览器不支持定位！");
      // }
    },
    // confirm(textContent) {
    //   if (textContent.length == 0) {
    //     alert("请输入内容！");
    //     return;
    //   }
    //   alert(textContent);
    // },
    publish() {
      this.$refs.modal.onShow(this.position);
      this.msgBoxShow=false;
      this.icon_img=this.right;
    },
    changeStyle() {
      // if(this.mapInfo.mapStyle=="amap://styles/graffiti"){
      //   this.mapInfo.mapStyle = "amap://styles/darkblue";
      // }else{
      //   this.mapInfo.mapStyle="amap://styles/graffiti";
      // }
      if(this.currentStyle==this.MapStyle.length-1){
        this.currentStyle=0;
      }else{
        this.currentStyle++;
      }
      this.mapInfo.mapStyle=this.MapStyle[this.currentStyle];
      // this.Map.destroy();
      this.getMap();
    },
    expand() {
      // this.$refs.msgBox.getData(this.releaseList);
      if (this.icon_img == this.right) {
        this.icon_img = this.left;
        this.msgBoxShow = true;
        this.$refs.msgBox.addAnimate();
      } else {
        this.icon_img = this.right;
        this.msgBoxShow = false;
      }
    },
    makeMarker() {
      let list = this.releaseList;
      var icon = new AMap.Icon({
        size: new AMap.Size(40, 50), // 图标尺寸
        image: this.fire, // Icon的图像
        imageSize: new AMap.Size(60, 50) // 根据所设置的大小拉伸或压缩图片
      });
      for (let i = 0; i < list.length; i++) {
        let marker = new AMap.Marker({
          icon: icon,
          position: new AMap.LngLat(list[i].longitude, list[i].latitude), // 经纬度对象，也可以是经纬度构成的一维数组[116.39, 39.9]
          title: list[i].content
        });
        this.Map.add(marker);
        let that = this;
        marker.on("click", function(ev) {
          that.$refs.itemBox.getData(list[i]);
          that.itemBoxShow = true;
        });
      }
    },
    cancel() {
      this.itemBoxShow = false;
    },
    getDis(longitude, latitude) {
      let p1 = [longitude, latitude];
      let p2 = [this.position.longitude, this.position.latitude];
      let dis = AMap.GeometryUtil.distance(p1, p2);
      if (dis >= 1000) {
        dis = (dis / 1000).toString();
        let index = dis.indexOf(".");
        return dis.slice(0, index + 2) + "km";
      } else {
        dis = dis.toString();
        let index = dis.indexOf(".");
        return dis.slice(0, index + 2) + "m";
      }
    },
    // 图片预览
    preview(img){
      this.previewImg=img;
      this.isPreview=true;
    },
    cancelPreview(){
      this.isPreview=false;
    }
  }
};
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
}
#box {
  width: 100%;
}
#map {
  width: 100%;
  height: 100%;
}
header {
  position: absolute;
  top: 0;
  z-index: 1;
  width: 100%;
  height: 40px;
  background-color: #000;
  opacity: 0.4;
}
#title {
  position: absolute;
  top: 0;
  z-index: 2;
  width: 100%;
  height: 40px;
  color: #ffffff;
  font-size: 20px;
  font-weight: 400;
  line-height: 40px;
  font-style: oblique;
  letter-spacing: 10px;
}
#top {
  position: absolute;
  top: 0;
  z-index: 2;
  width: 90%;
  height: 40px;
  margin: 0 5%;
  color: #ffffff;
  font-size: 16px;
  font-weight: 500;
  line-height: 40px;
  text-align: right;
}
#top #left {
  float: left;
  line-height: 40px;
}
#top #right {
  float: right;
  line-height: 40px;
}
#publish {
  width: 60px;
  height: 60px;
  background-color: #fff;
  opacity: 0.6;
  border-radius: 50%;
  position: fixed;
  bottom: 40px;
  right: 20px;
  z-index: 1;
  text-align: center;
  color: #000;
  line-height: 60px;
  font-size: 40px;
  font-weight: 500;
  cursor: pointer;
}
#publish:hover {
  background-color: #5cadff;
  color: #fff;
  width: 66px;
  height: 66px;
  line-height: 66px;
  position: fixed;
  opacity: 1;
  bottom: 37px;
  right: 17px;
  z-index: 1;
}
</style>


