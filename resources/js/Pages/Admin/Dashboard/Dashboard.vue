<template>
  <div>
    <MasterAdminLayout>
      <div class="mb-6 d-flex justify-content-between">
        <div class="w-50 mr-1">
          <instructors-stats-chart :data="stats" />
        </div>
        <div class="w-50 ml-1">
          <trainees-stats-chart :data="stats" />
        </div>
      </div>
      <CardComponent :items="list" />
    </MasterAdminLayout>
  </div>
</template>


<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import CardComponent from "../../../components/Admin/Dashboard/CardComponent";
import InstructorsStatsChart from "../../../components/Admin/Dashboard/DashboardCharts/InstructorsStatsChart.vue";
import TraineesStatsChart from "../../../components/Admin/Dashboard/DashboardCharts/TraineesStatsChart.vue";

export default {
  components: {
    MasterAdminLayout,
    CardComponent,
    InstructorsStatsChart,
    TraineesStatsChart,
  },
  data() {
    return {
      stats: {
        total_instructors: 0,
        total_instructors_in_day: 0,
        total_trainees: 0,
        total_trainees_in_day: 0,
        total_verified_instructors: 0,
        total_verified_instructors_in_day: 0,
        total_verified_trainees: 0,
        total_verified_trainees_in_day: 0,
      },
      list: [
        {
          name: "Today Joined Instructors",
          data: [],
        },
        {
          name: "Today Joined Trainees",
          data: [],
        },
        {
          name: "Today Document Uploaded (Instructor)",
          data: [],
        },
        {
          name: "Today Document Uploaded (Trainee)",
          data: [],
        },
      ],
    };
  },
  mounted() {
    this.getDashboardStats();
  },
  methods: {
    async getDashboardStats() {
      try {
        const { data } = await axios.get("/v1/drivisa/admin/stats/dashboard");
        const response = data.data;
        this.stats = response.stats;
        this.list = response.lists;
      } catch (e) {}
    },
  },
};
</script>