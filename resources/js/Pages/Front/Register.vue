<template>
  <div>
    <RegisterSelectionComponent v-if="userType === undefined"/>
    <StudentRegisterComponent v-else-if="userType === '2'"/>
    <InstructorRegisterComponent v-else-if="userType === '1'"/>
  </div>
</template>

<script>
import axios from 'axios';
import RegisterSelectionComponent from "../../components/Front/Auth/RegisterSelectionComponent";
import InstructorRegisterComponent from "../../components/Front/Auth/InstructorRegisterComponent";
import StudentRegisterComponent from "../../components/Front/Auth/StudentRegisterComponent";

export default {
  name: "Register",
  components: {RegisterSelectionComponent, StudentRegisterComponent, InstructorRegisterComponent},
  computed: {
    userType() {
      return this.$route.query.usertype;
    }
  },
  mounted() {
    let elHtml = document.getElementsByTagName('html')[0]
    elHtml.style.overflowY = 'hidden'
  },
  destroyed() {
    let elHtml = document.getElementsByTagName('html')[0]
    elHtml.style.overflowY = null
  },
}
</script>

<style lang="scss">
html {
  @media screen and (max-width: 900px) {
    overflow-y: scroll !important;
  }
}
</style>
