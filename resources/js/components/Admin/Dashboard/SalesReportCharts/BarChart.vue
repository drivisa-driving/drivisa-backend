<script>
import { Bar } from "vue-chartjs";

export default {
  extends: Bar,
  name: "BarChart",
  props: ["data"],
  computed: {
    chartData() {
      return this.data;
    },
  },
  data() {
    return {
      options: {
        responsive: true,
        maintainAspectRatio: false,
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
      this.renderBarChart();
    },
  },
  mounted() {
    this.renderBarChart();
  },
  methods: {
    renderBarChart() {
      this.renderChart(
        {
          labels: [
            "In Car Private Lesson",
            "Credit Package: 4 Hours",
            "Credit Package: 6 Hours",
            "Credit Package: 8 Hours",
            "Credit Package: 10 Hours",
            "G2 Test",
            "G Test",
            "BDE",
          ],
          datasets: [
            {
              label: "Sales",
              data: this.chartData,
              backgroundColor: [
                "#420cbf",
                "#5524c9",
                "#7b2ff2",
                "#ae3ee6",
                "#d850c0",
                "#ff63a3",
                "#ff86a0",
                "#ffae95",
              ],
            },
          ],
        },
        this.options
      );
    },
  },
};
</script>
