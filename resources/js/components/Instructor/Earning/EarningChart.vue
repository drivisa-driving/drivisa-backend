<script>
import {Bar} from 'vue-chartjs';

export default {
  extends: Bar,
  name: "EarningChart",
  props: ['data'],
  computed: {
    chartData() {
      return this.data;
    }
  },
  data() {
    return {
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true
            }
          }]
        }
      }
    }
  },
  watch: {
    data() {
      this.renderBarChart();
    }
  },
  mounted() {
    this.renderBarChart()
  },
  methods: {
    renderBarChart() {
      this.renderChart({
        labels: ['Balance'],
        datasets: [
          {
            label: 'Profits',
            data: this.chartData,
            backgroundColor: [
              'rgba(90, 182, 94, 0.4)',
            ],
            borderColor: [
              'rgba(90, 182, 94, 1)'
            ],
            borderWidth: 1
          },
        ]
      }, this.options);
    }
  }
}
</script>

<style scoped>

</style>