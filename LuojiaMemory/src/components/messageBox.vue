<template>
  <div id="msg" :class="boxClass">
    <div id="select" v-show="showSelect" @click="changeList">{{nowState}}</div>
    <div id="msgitem" v-for="item in list" :key="item.id">
      <div id="dis">距离：{{item.dis}}</div>
      <div id="content">{{item.content}}</div>
      <div id="imgBox">
        <img :src="img" v-for="img in item.imgList" :key="img" @click="preview(img)">
      </div>
      <!-- <div id="time">{{item.time}}</div> -->
      <div id="center">
        <div id="hot">
          <img :src="hot">
          <div>{{item.hot}}</div>
        </div>
        <div id="time">{{item.time}}</div>
      </div>
      <div id="like">
        <img :src="like" @click.stop="liked(item.id)">
      </div>
    </div>
  </div>
</template>

<script>
import like from "@/assets/icon/liked.png";
import hot from "@/assets/icon/fire.gif";

import { get } from "../utils/httputil.js";

export default {
  data() {
    return {
      boxClass: "",
      showSelect: true,
      like,
      hot,
      list: [],
      initList:[],
      // 筛选文字展示
      nowState: "全部"
    };
  },
  mounted() {
    let height = window.innerHeight - 160 + "px";
    document.querySelector("#msg").style.height = height;
    // 防抖优化
    let _this = this;
    function debounce(fn, wait) {
      _this.showSelect = true;
      let timeout;
      return function() {
        let that = this;
        let args = arguments;
        if (timeout) {
          clearTimeout(timeout);
        }
        timeout = setTimeout(() => {
          fn.apply(that, args);
        }, wait);
      };
    }
    // document.querySelector('#msg').onscroll=function(){
    //   let timeout;
    //   clearTimeout(timeout);
    //   this.showSelect=true;
    //   timeout=setTimeout(()=>{
    //     this.showSelect=false;
    //   },1000);
    // }
  },
  methods: {
    getData(data) {
      this.initList=data;
      this.list = data;
    },
    liked(id) {
      let that = this;
      get(
        "release/addLike.php?id=" + id,
        res => {
          if (res.data.code == 0) {
            that.$emit("refresh");
          }
        },
        err => {}
      );
    },
    // 图片预览
    preview(img) {
      this.$emit("preview", img);
    },
    addAnimate(){
      let msgBox = document.querySelector("#msg");
      msgBox.style.animation="slideInLeft 400ms";
    },
    changeList() {
      let msgBox = document.querySelector("#msg");
      var addAnimate = () => {
        msgBox.style.animation="flip 200ms";
      };
      msgBox.addEventListener("animationend",function(e){
        msgBox.style.animation="";
      });
      let getNewList=(dis)=>{
        let newList=[];
        for(let item of this.list){
          if(item.dis.indexOf('km')==-1){
            newList.push(item);
          }else{
            if(parseFloat(item.dis)<dis){
              newList.push(item);
            }
          }
        }
        return newList;
      }
      if (this.nowState == "全部") {
        this.nowState = "附近10km";
        addAnimate();
        this.list=getNewList(10);
      } else if (this.nowState == "附近10km") {
        this.nowState = "附近3km";
        addAnimate();
        this.list=getNewList(3);
      } else if (this.nowState == "附近3km") {
        this.nowState = "附近1km";
        addAnimate();
        this.list=getNewList(1);
      } else {
        this.nowState = "全部";
        addAnimate();
        this.list=this.initList;
      }
    }
  }
};
</script>

<style scoped>
@import url("../utils/animate.css");
#msg {
  width: 88%;
  margin: 0 6%;
  position: absolute;
  z-index: 10;
  top: 50px;
  background-color: #ffffff;
  opacity: 0.8;
  border-radius: 20px;
  /* animation: slideInLeft 400ms; */
  overflow: auto;
  /* animation: slideOutLeft 400ms; */
}
#msg::-webkit-scrollbar {
  display: none;
}
#select {
  width: 40%;
  height: 20px;
  background: rgba(0, 0, 0, 0.5);
  border-radius: 20px;
  margin: 20px 30%;
  padding: 5px 0;
  text-align: center;
  color: #fff;
  position: fixed;
  left: 0;
}

#msgitem {
  width: 90%;
  padding: 10px 0;
  /* min-height: 100px; */
  margin: 2px 5%;
  text-align: left;
  font-size: 18px;
  font-weight: 500;
  color: #000;
  border-bottom: 0.5px dashed #000;
}
#msgitem #dis {
  width: 90%;
  height: 20px;
  margin: 10px 5%;
  line-height: 20px;
  font-size: 12px;
  text-align: right;
  font-style: oblique;
}
#msgitem #content {
  width: 100%;
  word-break: break-all;
}
/* #msgitem #time {
  text-align: right;
  font-size: 12px;
  font-style: oblique;
} */
#msgitem #imgBox {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  justify-content: flex-start;
  width: 100%;
  min-height: 0px;
  margin: 10px 0;
}
#msgitem #imgBox img {
  width: 80px;
  height: 80px;
  background-color: #fff;
  /* background: transparent; */
  opacity: 1;
  border-radius: 10px;
  margin: 0 4px;
}
#msgitem #center {
  display: inline-flex;
  align-items: center;
  width: 80%;
  height: 40px;
  margin: 10px 10%;
}
#msgitem #center #time {
  position: absolute;
  right: 10%;
  font-size: 12px;
  font-style: oblique;
}
#msgitem #like {
  width: 80%;
  height: 40px;
  margin: 0px 10%;
  text-align: center;
}
#msgitem #like img {
  width: 40px;
  height: 40px;
}
#msgitem #center #hot {
  display: inline-flex;
  align-items: center;
  position: absolute;
  left: 5%;
  font-size: 14px;
  font-style: oblique;
}
#msgitem #center #hot img {
  width: 60px;
  height: 40px;
}
</style>


