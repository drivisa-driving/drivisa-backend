<template>
  <v-container class="d-container" style="margin-bottom: 80px;">
    <v-row>
      <v-col>
        <h1 class="d-info-title">We ignite opportunity by setting the world in motion</h1>
        <p class="d-info-text">
          Good things happen when people can move, whether across town or toward their dreams. Opportunities appear,
          open up, become reality. What started as a way to tap a button to get a ride has led to billions of moments of
          human connection as people around the world go all kinds of places in all kinds of ways with the help of our
          technology.
        </p>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: "InfoComponent"
}
</script>

<style scoped>
.d-info-title {
  font-size: 40px;
  font-weight: 600;
  max-width: 35ch;
  line-height: 1.2;
  font-family: Montserrat, sans-serif !important;
  letter-spacing: normal;
  margin: 0 0 16px;
}

.d-info-text {
  font-size: 16px;
  line-height: 1.7;
  max-width: 70ch;
  font-family: Montserrat, sans-serif !important;
}
</style>