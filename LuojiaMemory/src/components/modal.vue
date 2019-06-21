<template>
  <div id="modal" v-show="isShow">
    <div id="head">发布动态</div>
    <!-- <div id="divider"></div> -->
    <div id="content">
      <textarea type="text" v-model="textContent" placeholder="请输入发布内容" required/>
    </div>
    <div id="imgBox">
      <img :src="item" v-for="item in imgList" :key="item" @click="preview(item)">
      <img :src="addImage" alt="添加图片" @click="upload">
      <input type="file" style="display:none" id="inputFile" @change="toUpload">
    </div>
    <div id="foot">
      <div id="cancel" @click="onCancel">取消</div>
      <div id="confirm" @click="onConfirm">确定</div>
    </div>
  </div>
</template>

<script>
import { post ,baseUrl } from "../utils/httputil.js";
import addImage from "../assets/icon/add-image.png";

export default {
  props: {
    // showData:{
    //     title:String,
    //     content:String
    // }
    content: String
  },
  data() {
    return {
      isShow: false,
      addImage,
      textContent: "",
      position: {
        latitude: "",
        longitude: ""
      },
      imgList: []
    };
  },
  created() {},
  methods: {
    onConfirm() {
      // this.$emit("confirm",this.textContent);
      if (
        this.position.latitude.length == 0 ||
        this.position.longitude.length == 0
      ) {
        alert("获取位置异常！");
        return;
      }
      if (this.textContent.length == 0) {
        alert("请填写内容！");
        return;
      }
      let obj = {
        content: this.textContent,
        longitude: this.position.longitude,
        latitude: this.position.latitude
      };
      if(this.imgList.length!=0){
        obj.img=this.imgList.join("#");
      }
      post(
        "release/publish.php",
        obj,
        res => {
          if (res.data.code == 0) {
            alert("发布成功！");
            this.isShow = false;
            this.textContent = "";
            this.imgList=[];
            this.$emit("refresh");
          } else {
            alert("发布失败,服务器异常");
          }
        },
        err => {
          alert("发布失败,服务器异常");
        }
      );
    },
    onCancel() {
      this.isShow = false;
    },
    onShow(data) {
      this.isShow = true;
      this.position = data;
      console.log(data);
    },
    upload() {
      document.querySelector("#inputFile").click();
    },
    toUpload(e) {
      let that=this;
      let img = e.target.files[0];
      let formData = new FormData();
      formData.set("file", img);
      let xhr=new XMLHttpRequest();
      xhr.open("post",baseUrl+"release/addImage.php",true);
      xhr.send(formData);
      xhr.onreadystatechange=function(){
        if(xhr.readyState==4&&xhr.status==200){
          let res=JSON.parse(xhr.response);
          if(res.code==0){
            console.log(res);
            that.imgList.push(baseUrl+"image/"+res.path.split("/")[2]);
          }else{
            alert("服务器错误，请联系开发者~");
          }
        }
      }
    },
    preview(img){
      this.$emit("preview",img);
    }
  }
};
</script>

<style scoped>
#modal {
  width: 80%;
  background-color: #fff;
  /* opacity: 0.8; */
  background: rgba(255, 255, 255, 0.8);
  border-radius: 20px;
  padding: 20px;
  position: absolute;
  text-align: center;
  z-index: 99;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  /* animation: slideInDown 400ms; */
}
#head {
  width: 100%;
  height: 20px;
  font-size: 16px;
  font-weight: 600;
  color: #000;
}
#divider {
  width: 100%;
  height: 1px;
  background-color: #888;
}
#content {
  width: 100%;
  /* min-height:120px; */
  word-break: break-all;
  color: #000;
  margin: 20px 0 5px 0;
}
#content textarea {
  width: 100%;
  min-height: 80px;
  border: none;
  border-radius: 10px;
  outline: none;
  font-size: 16px;
  background: transparent;
}
#imgBox {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  justify-content: flex-start;
  width: 90%;
  min-height: 80px;
  margin: 0 5% 30px 5%;
}
#imgBox img {
  width: 80px;
  height: 80px;
  background-color: #fff;
  /* background: transparent; */
  opacity: 1;
  border-radius: 10px;
  margin: 0 4px;
}
#foot {
  width: 100%;
  position: absolute;
  bottom: 10px;
  right: 0px;
  display: flex;
  justify-content: center;
  align-items: center;
  /* text-align: center; */
}
#cancel {
  width: 60px;
  height: 20px;
  border-radius: 20px;
  background-color: #ff6600;
  color: #fff;
  text-align: center;
  font-size: 16px;
  font-weight: 500;
  padding: 5px 0;
  line-height: 20px;
  cursor: pointer;
  /* float:right; */
}
#confirm {
  width: 60px;
  height: 20px;
  border-radius: 20px;
  color: #fff;
  background-color: #3399ff;
  text-align: center;
  font-size: 16px;
  font-weight: 500;
  margin-left: 40px;
  padding: 5px 0;
  line-height: 20px;
  cursor: pointer;
  /* float:right; */
}
</style>

