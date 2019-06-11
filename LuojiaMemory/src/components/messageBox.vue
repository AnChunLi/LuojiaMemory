<template>
  <div id="msg">
    <div id="msgitem" v-for="item in list" :key="item.id">
      <div id="dis">距离：{{item.dis}}</div>
      <div id="content">{{item.content}}</div>
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
      like,
      hot,
      list: [
        {
          id: 1,
          content: "hhhhhhhh",
          time: "2019年6月10日"
        },
        {
          id: 2,
          content: "3123123123",
          time: "2019年6月10日"
        },
        {
          id: 3,
          content:
            "hklasjnjkldfjkl;ajflk;ja;lkfjl;kasjfl;kjl;kasj;lkfja;ljflk;ajfl;kajmjfkl;jakl;fjkl",
          time: "2019年6月10日"
        }
      ]
    };
  },
  mounted() {
    let height = window.innerHeight - 160 + "px";
    document.querySelector("#msg").style.height = height;
  },
  methods: {
    getData(data) {
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
  animation: slideInLeft 400ms;
  overflow: auto;
  /* animation: slideOutLeft 400ms; */
}
#msg::-webkit-scrollbar {
  display: none;
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
#msgitem #dis{
  width: 90%;
  height:20px;
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


