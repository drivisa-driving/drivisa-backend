<template>
  <div>
    <IntroComponent />
    <!-- <InstructorMapComponent :location="location" :instructors="instructors" /> -->
    <InstructorComponent />
    <QualificationsReqComponent />
    <OntarioComponent />
    <AppStoreComponent />
  </div>
</template>

<script>
import IntroComponent from "./Home/IntroComponent";
import InstructorMapComponent from "./Home/InstructorMapComponent";
import InstructorComponent from "./Home/InstructorComponent";
import QualificationsReqComponent from "./Home/QualificationsReqComponent";
import OntarioComponent from "./Home/OntarioComponent";
import AppStoreComponent from "./Home/AppStoreComponent";

export default {
  name: "Home",
  components: {
    AppStoreComponent,
    OntarioComponent,
    QualificationsReqComponent,
    InstructorComponent,
    InstructorMapComponent,
    IntroComponent,
  },
  data() {
    return {
      location: null,
      instructors: [],
    };
  },
  mounted() {
    if (!("geolocation" in navigator)) {
      return;
    }
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        this.location = pos;
        this.getInstructorNearest();
      },
      (err) => {
        this.getInstructorNearest();
      }
    );
  },
  methods: {
    async getInstructorNearest() {
      try {
        const { data } = await this.$api.front.getNearestInstructors(
          this.location
        );
        this.instructors = data.data;
      } catch (e) {}
    },
  },
};
</script>