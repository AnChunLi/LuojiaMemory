<template>
  <div id="box">
    <div id="title">LuojiaMemory</div>
    <div id="top">
      <div id="left" @click="expand">
        <img :src="icon_img" alt style="width:30px;height:30px;margin:5px 0;">
      </div>
      <!-- <div id="right" @click="changeStyle">涂鸦</div> -->
    </div>
    <header></header>
    <div id="map"></div>
    <div id="publish" @click="publish">add</div>
    <Modal title="About" ref="modal" @refresh="getData"/>
    <Msgbox v-show="msgBoxShow" ref="msgBox" @refresh="getData"/>
    <Itembox v-show="itemBoxShow" ref="itemBox" @cancel="cancel" @refresh="getData"/>
  </div>
</template>
 
<script>
// import Amap from 'AMap'
import Modal from "../components/modal.vue";
import Msgbox from "../components/messageBox.vue";
import Itembox from "../components/itemBox";

import left from "@/assets/icon/left.png";
import right from "@/assets/icon/right.png";
import fire from "@/assets/icon/fire.gif";

import { get } from "../utils/httputil.js";

export default {
  name: "Map",
  data() {
    return {
      fire,
      left,
      right,
      icon_img: right,
      itemBoxShow: false,
      msgBoxShow: false,
      Map: {},
      mapInfo: {
        mapStyle: "amap://styles/darkblue",
        resizeEnable: true,
        pitch: 58, // 地图俯仰角度，有效范围 0 度- 83 度
        viewMode: "3D" // 地图模式
        // layers: [satellite]
      },
      position: {
        latitude: null,
        longitude: null
      },
      modalShowData: {
        title: "About",
        content: "毕业生留念地图..."
      },
      releaseList: []
    };
  },
  components: {
    Modal,
    Msgbox,
    Itembox
  },
  mounted() {
    // 获取定位数据
    this.getPosition();
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

    // 初始化数据
    this.getData();
  },
  methods: {
    getData() {
      let that = this;
      get(
        "release/getList.php",
        res => {
          if (res.data.code == 0) {
            let list = res.data.data;
            for (let i = 0; i < list.length; i++) {
              list[i].dis = that.getDis(list[i].longitude, list[i].latitude);
            }
            that.releaseList = list;
            console.log(that.releaseList);
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
      // var roadNet = new AMap.TileLayer.RoadNet();
      // var buildings = new AMap.Buildings({
      //   zooms: [16, 18],
      //   zIndex: 10,
      //   heightFactor: 2 //2倍于默认高度，3D下有效
      // }); //楼块图层
      let self = this;
      this.Map = new AMap.Map("map", self.mapInfo);
      this.Map.setZoomAndCenter(18, [
        this.position.longitude,
        this.position.latitude
      ]);
    },
    getPosition() {
      let that = this;
      if (navigator.geolocation) {
        navigator.geolocation.watchPosition(
          function(position) {
            console.log(position);
            that.position.latitude = position.coords.latitude;
            that.position.longitude = position.coords.longitude;
          },
          function(error) {
            switch (error.code) {
              case error.PERMISSION_DENIED:
                alert("用户拒绝对获取地理位置的请求。");
                break;
              case error.POSITION_UNAVAILABLE:
                alert("位置信息是不可用的。");
                break;
              case error.TIMEOUT:
                alert("请求用户地理位置超时。");
                break;
              case error.UNKNOWN_ERROR:
                alert("未知错误。");
                break;
            }
          }
        );
      } else {
        alert("浏览器不支持定位！");
      }
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
    },
    changeStyle() {
      // console.log(this.mapInfo.mapStyle);
      // if(this.mapInfo.mapStyle=="amap://styles/graffiti"){
      //   this.mapInfo.mapStyle = "amap://styles/darkblue";
      // }else{
      //   this.mapInfo.mapStyle="amap://styles/graffiti";
      // }
      // console.log(this.mapInfo.mapStyle);
      // this.Map.destroy();
      // this.getMap();
    },
    expand() {
      // this.$refs.msgBox.getData(this.releaseList);
      if (this.icon_img == this.right) {
        this.icon_img = this.left;
        this.msgBoxShow = true;
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
        return dis.slice(0, index + 2)+"km";
      } else {
        dis=dis.toString();
        let index = dis.indexOf(".");
        return dis.slice(0, index + 2)+"m";
      }
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
  font-weight: 600;
  line-height: 40px;
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
  font-size: 18px;
  font-weight: 600;
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


