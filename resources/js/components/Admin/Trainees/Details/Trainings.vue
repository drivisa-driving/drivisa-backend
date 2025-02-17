<template>
  <div>
    <v-tabs centered class="my-5">
      <v-tab> Today Lessons </v-tab>
      <v-tab> Upcoming Lessons </v-tab>
      <v-tab> All Lessons </v-tab>
      <v-tab> Completed Lessons </v-tab>

      <v-tab-item>
        <TrainingsDatatable :lessons="data.today" />
      </v-tab-item>
      <v-tab-item>
        <TrainingsDatatable :lessons="data.upcoming" />
      </v-tab-item>
      <v-tab-item>
        <TrainingsDatatable :lessons="getAllLessons" />
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
          "/v1/drivisa/admin/trainees/lessons/today-upcoming/" +
          this.$route.params.id;
        const { data } = await axios.get(url);
        this.data = data;
        this.data = data.data;
      } catch (e) {}
    },
    async getCompletedLessons() {
      const { data } = await axios.get(
        "v1/drivisa/admin/trainees/lessons/completed/" + this.$route.params.id
      );
      this.completedLessons = data;
      this.completedLessons = data.data;
    },
    async getLessons() {
      try {
        let url =
          "/v1/drivisa/admin/trainees/lessons/history/" + this.$route.params.id;
        const { data } = await axios.get(url);
        this.getAllLessons = data;
        this.getAllLessons = data.data;
      } catch (e) {}
    },
  },
};
</script>