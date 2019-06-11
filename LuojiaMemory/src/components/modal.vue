<template>
  <div id="modal" v-show="isShow">
    <div id="head">发布动态</div>
    <!-- <div id="divider"></div> -->
    <div id="content">
      <textarea type="text" v-model="textContent" placeholder="请输入发布内容" required/>
    </div>
    <div id="foot">
      <div id="cancel" @click="onCancel">取消</div>
      <div id="confirm" @click="onConfirm">确定</div>
    </div>
  </div>
</template>

<script>
import { post } from "../utils/httputil.js";

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
      textContent: "",
      position:{
        latitude:"",
        longitude:""
      }
    };
  },
  created(){

  },
  methods: {
    onConfirm() {
      // this.$emit("confirm",this.textContent);
      if(this.position.latitude.length==0||this.position.longitude.length==0){
        alert("获取位置异常！");
        return;
      }
      if(this.textContent.length==0){
        alert("请填写内容！");
        return;
      }
      let obj = {
        content:this.textContent,
        longitude:this.position.longitude,
        latitude:this.position.latitude
      };
      post("release/publish.php",obj,
        (res)=>{
          if(res.data.code==0){
            alert("发布成功！");
            this.isShow = false;
            this.textContent="";
            this.$emit("refresh");
          }else{
            alert("发布失败,服务器异常");
          }
        },
        (err)=>{
          alert("发布失败,服务器异常");
        }
      );
    },
    onCancel() {
      this.isShow = false;
    },
    onShow(data) {
      this.isShow = true;
      this.position=data;
      console.log(data);
    }
  }
};
</script>

<style scoped>
#modal {
  width: 80%;
  background-color: #fff;
  opacity: 0.8;
  border-radius: 20px;
  padding: 20px;
  position: absolute;
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
  font-weight: 500;
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
  margin-bottom: 30px;
}
#content textarea {
  width: 100%;
  min-height: 80px;
  border: 1px #888 solid;
  border-radius: 10px;
  outline: none;
  font-size: 16px;
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
  border-radius: 5px;
  background-color: #ff6600;
  color: #fff;
  text-align: center;
  font-size: 16px;
  font-weight: 500;
  padding: 3px 0;
  line-height: 20px;
  /* float:right; */
}
#confirm {
  width: 60px;
  height: 20px;
  border-radius: 5px;
  color: #fff;
  background-color: #3399ff;
  text-align: center;
  font-size: 16px;
  font-weight: 500;
  margin-left: 40px;
  padding: 3px 0;
  line-height: 20px;
  /* float:right; */
}
</style>

