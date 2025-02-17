<script>
import { Bar } from "vue-chartjs";

export default {
  extends: Bar,
  name: "YearlyChart",
  props: ["selected_year"],
  data() {
    return {
      chartData: {
        labels: [],
        datasets: [
          {
            label: "In Car Private Lesson",
            backgroundColor: "#420cbf",
            data: [],
          },
          {
            label: "Credit Package: 4 Hours",
            backgroundColor: "#5524c9",
            data: [],
          },
          {
            label: "Credit Package: 6 Hours",
            backgroundColor: "#7b2ff2",
            data: [],
          },
          {
            label: "Credit Package: 8 Hours",
            backgroundColor: "#ae3ee6",
            data: [],
          },
          {
            label: "Credit Package: 10 Hours",
            backgroundColor: "#d850c0",
            data: [],
          },
          {
            label: "G2 Test",
            backgroundColor: "#ff63a3",
            data: [],
          },
          {
            label: "G Test",
            backgroundColor: "#ff86a0",
            data: [],
          },
          {
            label: "BDE",
            backgroundColor: "#ffae95",
            data: [],
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
          xAxes: [
            {
              stacked: true,
              gridLines: {
                drawOnChartArea: false,
              },
            },
          ],
          yAxes: [
            {
              stacked: true,
              // ticks: {
              //   display: false,
              // },
            },
          ],
        },
      },
    };
  },
  mounted() {
    this.getYearlyData();
  },
  watch: {
    selected_year() {
      this.getYearlyData();
    },
  },
  methods: {
    getYearlyData() {
      axios
        .get(`/v1/drivisa/admin/sales-report/yearly?year=${this.selected_year}`)
        .then((response) => {
          let data = response.data;

          // Filter out the data for the upcoming months
          const currentDate = new Date();
          const currentYear = currentDate.getFullYear();
          const currentMonth = currentDate.getMonth() + 1;
          if (this.selected_year === currentYear) {
            data = data.filter((d) => {
              const monthNumber =
                new Date(`${d.month} 1, ${currentYear}`).getMonth() + 1;
              return monthNumber <= currentMonth;
            });
          }

          this.chartData.labels = data.map((d) => d.month);
          this.chartData.datasets[0].data = data.map((d) => d.lesson);
          this.chartData.datasets[1].data = data.map((d) => d.package_4_hour);
          this.chartData.datasets[2].data = data.map((d) => d.package_6_hour);
          this.chartData.datasets[3].data = data.map((d) => d.package_8_hour);
          this.chartData.datasets[4].data = data.map((d) => d.package_10_hour);
          this.chartData.datasets[5].data = data.map((d) => d.g2_test);
          this.chartData.datasets[6].data = data.map((d) => d.g_test);
          this.chartData.datasets[7].data = data.map((d) => d.BDE);

          this.renderChart(this.chartData, this.options);
        })
        .catch((error) => {
          console.error(error);
        });
    },
  },
};
</script>
<style scoped>
  #bar-chart{
    height: 750px !important;
  }
</style>