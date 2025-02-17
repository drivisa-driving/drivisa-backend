<template>
  <div class="container d-container">
    <div class="row">
      <div class="col-md-4"
           v-for="(item, i) in data"
           :key="i"
      >
        <img :src="item.image" alt="" class="d-info-img">
        <p class="d-info-title mt-1">{{ item.title }}</p>
        <p class="d-info-text">{{ item.text }} </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['data'],
  name: "CompanyInfoComponent"
}
</script>

<style scoped lang="scss">
.d-info-title {
  font-weight: 700;
  font-size: 16px;
  line-height: 1.7;
  font-family: Montserrat, sans-serif !important;
}

.d-info-img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.d-info-text {
  font-size: 16px;
  line-height: 1.7;
  margin: 0 0 12px;
  font-family: Montserrat, sans-serif !important;
}
</style>