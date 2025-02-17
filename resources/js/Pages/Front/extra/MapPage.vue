<template>
  <InstructorMapComponent :location="location" :instructors="instructors"/>
</template>

<script>
import InstructorMapComponent from "../../../components/Front/Home/InstructorMapComponent";
export default {
  name: "MapPage",
  components: {InstructorMapComponent},
  data() {
    return {
      location: null,
      instructors: []
    }
  },
  mounted() {
    if (!("geolocation" in navigator)) {
      return;
    }
    navigator.geolocation.getCurrentPosition(pos => {
      this.location = pos;
      this.getInstructorNearest();
    }, err => {
      this.getInstructorNearest();
    })


  },
  methods: {
    async getInstructorNearest() {
      try {
        const {data} = await this.$api.front.getNearestInstructors(this.location)
        this.instructors = data.data
      } catch (e) {

      }
    }
  }
}
</script>

<style scoped>

</style>