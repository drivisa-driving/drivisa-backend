<template>
  <div>
    <div class="float-right mr-3" style="margin-top: -25px">
      <span class="mr-3"
        >Total Trainee: <strong>{{ totalHours?.trainee }}</strong></span
      >
      <span
        >Total Hours: <strong>{{ totalHours?.hours }}</strong></span
      >
    </div>

    <v-tabs centered class="my-5">
      <v-tab> Today Lessons </v-tab>
      <v-tab> Upcoming Lessons </v-tab>
      <v-tab> All Lessons </v-tab>
      <v-tab> Completed Lessons </v-tab>

      <v-tab-item>
        <TrainingsDatatable
          @endLesson="endLesson"
          @endBdeLesson="endBdeLesson"
          :lessons="data.today"
        />
      </v-tab-item>
      <v-tab-item>
        <TrainingsDatatable
          @endLesson="endLesson"
          @endBdeLesson="endBdeLesson"
          :lessons="data.upcoming"
        />
      </v-tab-item>
      <v-tab-item>
        <TrainingsDatatable
          :lessons="getAllLessons"
          @endLesson="endLesson"
          @endBdeLesson="endBdeLesson"
          @initiateRefund="initiateRefund"
        />
      </v-tab-item>
      <v-tab-item>
        <TrainingsDatatable :lessons="completedLessons" />
      </v-tab-item>
    </v-tabs>
  </div>
</template>

<script>
import TrainingsDatatable from "./Trainings/TrainingsDatatable.vue";
export default {
  name: "Trainings",
  props: ["totalHours"],
  components: { TrainingsDatatable },
  data() {
    return {
      data: [],
      completedLessons: [],
      getAllLessons: [],
    };
  },
  mounted() {
    this.getTodayUpcoming();
    this.getCompletedLessons();
    this.getLessons();
  },
  methods: {
    async getTodayUpcoming() {
      try {
        let url =
          "/v1/drivisa/admin/instructors/lessons/today-upcoming/" +
          this.$route.params.id;
        const { data } = await axios.get(url);
        this.data = data;
        this.data = data.data;
      } catch (e) {}
    },
    async getCompletedLessons() {
      const { data } = await axios.get(
        "v1/drivisa/admin/instructors/lessons/completed/" +
          this.$route.params.id
      );
      this.completedLessons = data;
      this.completedLessons = data.data;
    },
    async getLessons() {
      try {
        let url =
          "/v1/drivisa/admin/instructors/lessons/history/" +
          this.$route.params.id;
        const { data } = await axios.get(url);
        this.getAllLessons = data;
        this.getAllLessons = data.data;
      } catch (e) {}
    },

    async endLesson(id) {
      try {
        let url = "v1/drivisa/admin/instructors/lessons/" + id + "/end-lesson";
        const { data } = await axios.post(url);
        await this.getLessons();
        this.$toasted.success("Lesson Ended Successfully!");
      } catch (e) {
        this.$toasted.error("Unable to End Lesson!");
      }
    },
    async endBdeLesson(fillMarkingKeys, markings, id) {
      try {
        let url = "v1/drivisa/admin/instructors/bde/" + id + "/end-bde-lesson";
        const { data } = await axios.post(url, {
          fillMarkingKeys: fillMarkingKeys,
          markings: markings,
        });
        await this.getLessons();
        this.$toasted.success("Success!");
      } catch (e) {
        this.$toasted.error("Failed!");
      }
    },
    async initiateRefund(id) {
      try {
        let url =
          "v1/drivisa/admin/instructors/lessons/" + id + "/initiate-refund";
        const { data } = await axios.post(url);
        await this.getLessons();
        this.$toasted.success(data.message);
      } catch (e) {
        this.$toasted.error("Unable to initiate refund!");
      }
    },
  },
};
</script>

<style scoped>
</style>