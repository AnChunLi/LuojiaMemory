<template>
  <div id="bg" @click="cancelPreview">
    <img :src="imgUrl">
  </div>
</template>

<script>
export default {
  props: {
    imgUrl: String
  },
  methods: {
    cancelPreview() {
      this.$emit("cancelPreview");
    }
  },
  updated(){
    // 通过计算设置图片marginTop
    let imgHeight=document.querySelector('#bg img').clientHeight;
    let marginTop=(window.innerHeight-imgHeight)/2+"px";
    document.querySelector('#bg img').style.margin=marginTop+" 10%";
  }
};
</script>

<style scoped>
#bg {
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  position: fixed;
  top: 0;
  z-index: 9999;
}
#bg img {
  width: 80%;
  max-height: 80%;
  margin: 10% 10% 0 10%;
  opacity: 1;
}
</style>


