<template>
  <div id="itemBox" @click="cancel">
    <div id="dis">距离：{{item.dis}}</div>
    <div id="content">{{item.content}}</div>
    <div id="imgBox">
      <img :src="img" v-for="img in item.imgList" :key="img" @click.stop="preview(img)">
    </div>
    <div id="center">
      <div id="hot">
        <img :src="hot">
        <div>{{item.hot}}</div>
      </div>
      <div id="time">{{item.time}}</div>
    </div>
    <div id="like">
      <img :src="item.isLiked==1?like:to_like" @click.stop="liked">
    </div>
  </div>
</template>

<script>
import like from "@/assets/icon/liked.png";
import to_like from"@/assets/icon/to_like.png"
import hot from "@/assets/icon/fire.gif";

import { get } from "../utils/httputil.js";
import { toLike } from "../data/like.js";

export default {
  data() {
    return {
      item: {},
      like,
      to_like,
      hot
    };
  },
  methods: {
    getData(data) {
      this.item = data;
    },
    cancel() {
      this.$emit("cancel");
    },
    liked() {
      // let that = this;
      // get(
      //   "release/addLike.php?id=" + this.item.id,
      //   res => {
      //     if (res.data.code == 0) {
      //       that.item.hot++;
      //       that.$emit("refresh");
      //     }
      //   },
      //   err => {}
      // );
      toLike(this.item.id).then((e) => {
        if(e==1){
          this.item.hot++;
          this.item.isLiked=1;
        }else{
          this.item.hot--;
          this.item.isLiked=0;
        }
        this.$emit("refresh");
      });
    },
    // 图片预览
    preview(img) {
      this.$emit("preview", img);
    }
  }
};
</script>

<style scoped>
@import url("../utils/animate.css");
#itemBox {
  width: 88%;
  min-height: 100px;
  margin: -100px 6% 0 6%;
  position: absolute;
  z-index: 11;
  top: 50%;
  background-color: #ffffff;
  opacity: 0.8;
  border-radius: 20px;
  animation: slideInUp 200ms;
}
#itemBox #dis {
  width: 80%;
  height: 20px;
  margin: 10px 10%;
  line-height: 20px;
  font-size: 12px;
  text-align: right;
  font-style: oblique;
}
#itemBox #content {
  width: 80%;
  margin: 2px 10% 20px 10%;
  text-align: left;
  word-break: break-all;
}
#itemBox #imgBox {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  justify-content: flex-start;
  width: 80%;
  min-height: 0px;
  margin: 10px 10%;
}
#itemBox #imgBox img {
  width: 80px;
  height: 80px;
  background-color: #fff;
  /* background: transparent; */
  opacity: 1;
  border-radius: 10px;
  margin: 0 4px;
}
#itemBox #center {
  display: inline-flex;
  align-items: center;
  width: 80%;
  height: 40px;
  margin: 20px 10%;
}
#itemBox #center #time {
  position: absolute;
  right: 10%;
  font-size: 12px;
  font-style: oblique;
}
#itemBox #like {
  width: 80%;
  height: 60px;
  margin: 10px 10%;
  text-align: center;
}
#itemBox #like img {
  width: 50px;
  height: 50px;
}
#itemBox #center #hot {
  display: inline-flex;
  align-items: center;
  position: absolute;
  left: 5%;
  font-size: 14px;
  font-style: oblique;
}
#itemBox #center #hot img {
  width: 60px;
  height: 40px;
}
</style>

