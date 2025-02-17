<template>
  <div>
    <div class="d-hero-container">
      <div class="d-hero-content">
        <h1 class="d-hero-title">A letter from our CEO</h1>
        <p class="d-hero-text"> Read about our team’s commitment to provide everyone on our global platform with the
          technology that
          can
          help them move ahead. </p>
        <router-link to="/" class="d-hero-btn">Read Letter</router-link>
      </div>
      <img src="/assets/media/imgs/ceo.jpg" alt="" class="d-hero-img">
    </div>
  </div>
</template>

<script>
export default {
  name: "CeoLetterComponent"
}
</script>

<style scoped lang="scss">
.d-hero-container {
  position: relative;
  margin-bottom: 80px;

  .d-hero-content {
    position: absolute;
    top: 15%;
    left: 5%;
    color: #fff;

    .d-hero-title {
      font-size: 60px;
      font-weight: 700;
      max-width: 15ch;
      font-family: Montserrat, sans-serif !important;
    }

    .d-hero-text {
      font-size: 16px;
      line-height: 1.7;
      max-width: 40ch;
      font-family: Montserrat, sans-serif !important;
    }

    .d-hero-btn {
      color: #fff;
      font-size: 16px;
      font-weight: 700;
      background-color: #000;
      padding: 12px 20px;
      margin-top: 20px;
      display: inline-block;
      text-decoration: none;
    }
  }

  .d-hero-img {
    width: 100%;
    max-height: 500px;
    -o-object-fit: cover;
    object-fit: cover;
  }
}
</style>