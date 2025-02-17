<template>
  <div>
    <Header/>
    <div class="d-container">
      <div id="generic_price_table">
        <div class="row" v-if="packages.length > 0">
          <BuyCardComponent
              v-for="(s_package, i) in packages" :key="i"
              :s_package="s_package"
              :buy_now_link="'/payment?package='+s_package.id"/>
        </div>
        <h2 v-else class="text-center">No Lesson Found</h2>
      </div>
    </div>
    <Footer/>
  </div>
</template>
<script>
import Header from "../../../components/Front/Header";
import Footer from "../../../components/Front/Footer";
import BuyCardComponent from "../../../components/BuyCardComponent";

export default {
  components: {Footer, Header, BuyCardComponent},
  data() {
    return {
      packages: [],
    }
  },
  mounted() {
    this.getBde();
  },
  methods: {
    async getBde() {
      const {data} = await axios.get('/v1/drivisa/get-packages/credit package')
      this.packages = data.data

    }
  }
}
</script>

<style>

</style>