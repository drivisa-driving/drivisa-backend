<script>
import { Doughnut } from "vue-chartjs";

export default {
  extends: Doughnut,
  name: "DoughnutChart",
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
      },
    };
  },
  watch: {
    data() {
      this.renderDoughnutChart();
    },
  },
  mounted() {
    this.renderDoughnutChart();
  },
  methods: {
    renderDoughnutChart() {
      this.renderChart(
        {
          labels: [
            "In Car Private Lesson",
            "Credit Package",
            "G2 Test",
            "G Test",
            "BDE",
          ],
          datasets: [
            {
              data: this.chartData,
              backgroundColor: [
                "rgba(90, 182, 94, 0.4)",
                "rgba(238,119,27,0.4)",
                "rgba(46,209,209,0.4)",
                "rgba(0,31,250,0.4)",
                "rgba(237,0,0,0.4)",
              ],
              borderColor: [
                "rgba(90, 182, 94, 1)",
                "rgba(238,119,27, 1)",
                "rgba(46,209,209, 1)",
                "rgba(0,31,250, 1)",
                "rgba(237,0,0, 1)",
              ],
              borderWidth: 1,
              borderAlign: "canter",
              hoverOffset: 4,
            },
          ],
        },
        this.options
      );
    },
  },
};
</script>
