<template>
  <div>
    <Header/>
    <HeaderHeroComponent/>
    <InfoComponent/>
    <CeoLetterComponent/>
    <CorporateComponent :data="aboutData.corporate"/>
    <CompanyInfoComponent :data="aboutData.info"/>
    <Footer/>
  </div>
</template>

<script>
import Header from "../../../components/Front/Header";
import Footer from "../../../components/Front/Footer";
import HeaderHeroComponent from "../../../components/Front/AboutUs/HeaderHeroComponent";
import InfoComponent from "../../../components/Front/AboutUs/InfoComponent";
import CeoLetterComponent from "../../../components/Front/AboutUs/CeoLetterComponent";
import CorporateComponent from "../../../components/Front/AboutUs/CorporateComponent";
import CompanyInfoComponent from "../../../components/Front/AboutUs/CompanyInfoComponent";
import aboutusdata from "../../../data/aboutus";

export default {
  name: "AboutUs",
  data() {
    return {
      aboutData: aboutusdata
    }
  },
  components: {
    Header,
    Footer,
    HeaderHeroComponent,
    InfoComponent,
    CeoLetterComponent,
    CorporateComponent,
    CompanyInfoComponent
  }
}
</script>

<style scoped>

</style>
