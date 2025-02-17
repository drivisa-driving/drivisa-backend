<script>
import { Bar } from "vue-chartjs";

export default {
  extends: Bar,
  name: "StatsChart",
  props: ["data"],
  computed: {
    chartData() {
      return {
        labels: ["Total", "Verified", "Today Joined", "Today Verified"],
        datasets: [
          {
            label: "Instructors",
            data: [
              this.data.total_instructors,
              this.data.total_verified_instructors,
              this.data.total_instructors_in_day,
              this.data.total_verified_instructors_in_day,
            ],
            backgroundColor: ["#36A2EB", "#FFCE56", "#FF6384", "#9966FF"],
          },
        ],
      };
    },
  },
  data() {
    return {
      delayed: true,
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 1000,
          easing: "easeInOutQuad",
        },
        scales: {
          xAxes: [
            {
              gridLines: {
                drawOnChartArea: false,
              },
            },
          ],
          yAxes: [
            {
              ticks: {
                beginAtZero: true,
              },
            },
          ],
        },
      },
    };
  },
  watch: {
    data() {
      this.renderChart(this.chartData, this.chartOptions);
    },
  },
  mounted() {
    this.renderChart(this.chartData, this.chartOptions);
  },
};
</script>
