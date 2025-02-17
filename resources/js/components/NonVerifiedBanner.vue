<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12 box">
        <i class="mdi mdi-alert"></i>
        Your account not published yet! Please Wait for approval.
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "NonVerifiedBanner",
  props: ['link']
}
</script>

<style scoped>
.box {
  padding: 1rem;
  background-color: #ff120075;
  color: black !important;
  font: 400 15px/24px Roboto, Helvetica Neue, sans-serif;
  letter-spacing: normal;
  margin: 0 0 16px;
}

.box * {
  color: black;
}
</style>