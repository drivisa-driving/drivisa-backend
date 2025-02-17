<template>
  <div class="d-home-bg">
    <Header/>
    <HomeComponent/>
    <Footer/>
  </div>

</template>

<script>
import Footer from "../../components/Front/Footer";
import Header from "../../components/Front/Header";
import HomeComponent from "../../components/Front/HomeComponent";
export default {
  name: "Home",
  components: {Header, HomeComponent, Footer},
  data() {
    return {}
  }
}
</script>
<style scoped lang="scss">
//@import "resources/js/scss/vue_variables";
.d-home-bg {
  background-color: #fafafa !important;
}

.m_btn {
  background: rgba(0, 0, 0, 0) linear-gradient(to right, rgb(54, 209, 220), rgb(91, 134, 229)) repeat scroll 0% 0% !important;
  color: #fff !important;
  text-decoration: none;
}

.m_btn:hover {
  transition: 1.10s !important;
  background: rgba(56, 46, 46, 0) linear-gradient(to right, rgb(49, 161, 168), rgb(86, 170, 222)) repeat scroll 0% 0% !important;
}
</style>
