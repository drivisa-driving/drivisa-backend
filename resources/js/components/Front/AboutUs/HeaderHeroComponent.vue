<template>
  <div>
    <div class="d-hero-container">
      <h1 class="d-hero-text">About Us</h1>
      <img src="/assets/media/imgs/about-us.jpg" alt="" class="d-hero-img">
    </div>
  </div>
</template>

<script>
export default {
  name: "HeaderHeroComponent"
}
</script>

<style scoped lang="scss">
.d-hero-container {
  position: relative;
  margin-bottom: 80px;

  .d-hero-text {
    position: absolute;
    top: 70%;
    left: 10%;
    color: #fff;
    font-size: 60px;
    font-weight: 700;
  }

  .d-hero-img {
    width: 100%;
    max-height: 400px;
    -o-object-fit: cover;
    object-fit: cover;
  }
}

</style>