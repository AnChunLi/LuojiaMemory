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
  }
};
</script>

<style scoped>
#bg {
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  position: fixed;
  top: 0;
  z-index: 999;
}
#bg img {
  width: 80%;
  margin: 50% 10% 0 10%;
  opacity: 1;
}
</style>


