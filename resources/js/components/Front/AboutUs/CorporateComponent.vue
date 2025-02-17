<template>
  <v-container class="d-container">
    <v-row
        justify="space-between"
        class="d-corporate-container"
        v-for="(item, i) in data"
        :key="i"
    >
      <v-col v-if="item.img_side === 'left'">
        <img :src="item.image" alt="" class="img-fluid"
             style="max-width: 500px; vertical-align: middle;">
      </v-col>
      <v-col class="d-corporate-content">
        <h2 class="d-corporate-title">{{ item.title }}</h2>
        <p class="d-corporate-text"> {{ item.text }} </p>
        <router-link v-if="item.btn" :to="item.link" class="d-corporate-btn">Learn More</router-link>
      </v-col>
      <v-col v-if="item.img_side === 'right'">
        <img :src="item.image" alt="" class="img-fluid float-right"
             style="max-width: 500px; vertical-align: middle;">
      </v-col>
    </v-row>
  </v-container>
</template>

<script>

export default {
  props: ['data'],
  name: "CorporateComponent",
}
</script>

<style scoped lang="scss">
.d-corporate-container {
  margin-bottom: 80px;

  .d-corporate-content {
    max-width: 70ch;
  }

  .d-corporate-title {
    font: 700 30px/32px Roboto, Helvetica Neue, sans-serif;
    font-family: Roboto, Helvetica Neue, sans-serif;
    letter-spacing: normal;
    margin: 0 0 16px;
  }

  .d-corporate-text {
    font-size: 16px;
    line-height: 1.7;
    margin: 0 0 12px;
    font-family: Montserrat, sans-serif !important;
  }

  .d-corporate-btn {
    font-size: 16px;
    font-weight: 700;
    color: #fff;
    padding: 6px 12px;
    background-color: #36c;
    display: inline-block;
    margin-top: 10px;
  }
}
</style>