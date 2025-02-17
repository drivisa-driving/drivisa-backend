<template>
  <div>
    <MasterAdminLayout>
      <!-- <div v-if="isRevenueLoaded === false">
        <ProgressSpinner
          style="width: 50px; height: 50px"
          strokeWidth="5"
          animationDuration=".8s"
          aria-label="Loading"
          color="primary"
        />
      </div> -->
      <!-- <div v-else> -->
         <div>
        <v-card class="border">
          <v-card-text>
            <h4 class="font-weight-light text-dark strong">
              <b style="font-weight: 700"
                >Welcome Back, {{ profile.firstName }}</b
              >
            </h4>
            <p>Drivisa current sales summary</p>

            <v-tabs class="my-5">
              <v-tab
                class="border rounded-left rounded-bottom-left border-right-0 font-weight-bold"
                @click="salesReportType('yearly')"
              >
                12 months
              </v-tab>
              <v-tab
                class="border border-right-0 font-weight-bold"
                @click="salesReportType('monthly')"
                :disabled="selected_year !== currentYear"
              >
                30 days
              </v-tab>
              <v-tab
                class="border border-right-0 font-weight-bold"
                @click="salesReportType('weekly')"
                :disabled="selected_year !== currentYear"
              >
                7 days
              </v-tab>
              <v-tab
                class="border rounded-right rounded-bottom-right font-weight-bold"
                @click="salesReportType('daily')"
                :disabled="selected_year !== currentYear"
              >
                24 hours
              </v-tab>

              <v-tab-item>
                <v-card-text>
                  <div>
                    <div class="d-flex justify-content-end align-items-center">
                      <Dropdown
                        v-model="selected_year"
                        :options="years"
                        optionLabel="value"
                        optionValue="value"
                        placeholder="Select Year"
                        style="width: 7rem"
                      />
                    </div>
                    <yearly-chart :selected_year="selected_year" />

                    <!-- <div
                      class="mt-5 d-flex justify-content-start align-items-center"
                      v-if="isRevenueLoaded && revenue.total_revenue > 0"
                    >
                      <div class="border rounded py-3 px-5">
                        <p class="mb-2">Total revenue</p>
                        <p class="amount">
                          <strong>${{ revenue.total_revenue }}</strong>
                        </p>
                      </div>
                      <div class="border rounded py-3 px-5 ml-3">
                        <p class="mb-2">Paid to instructors</p>
                        <p class="amount">
                          <strong>${{ revenue.transfer_amount }}</strong>
                        </p>
                      </div>
                      <div class="border rounded py-3 px-5 ml-3">
                        <p class="mb-2">Actual revenue</p>
                        <p class="amount">
                          <strong>${{ revenue.actual_revenue }}</strong>
                        </p>
                      </div>
                    </div> -->
                  </div>
                </v-card-text>
              </v-tab-item>
              <v-tab-item>
                <v-card-text>
                  <BarChart :data="data.monthly" />
                </v-card-text>
              </v-tab-item>
              <v-tab-item>
                <v-card-text>
                  <BarChart :data="data.weekly" />
                </v-card-text>
              </v-tab-item>
              <v-tab-item>
                <v-card-text>
                  <BarChart :data="data.daily" />
                </v-card-text>
              </v-tab-item>
            </v-tabs>

            <div :key="selected_year">
              <detail-report
                :reportType="reportType"
                :year="selected_year"
                :key="reportType"
              />
            </div>
          </v-card-text>
        </v-card>
      </div>
      <!-- </div> -->
    </MasterAdminLayout>
  </div>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import BarChart from "../../../components/Admin/Dashboard/SalesReportCharts/BarChart.vue";
import DoughnutChart from "../../../components/Admin/Dashboard/SalesReportCharts/DoughnutChart.vue";
import YearlyChart from "../../../components/Admin/Dashboard/SalesReportCharts/YearlyChart.vue";
import DetailReport from "./DetailReport.vue";
import ProgressSpinner from "primevue/progressspinner";

export default {
  name: "SalesReport",
  components: {
    MasterAdminLayout,
    BarChart,
    DoughnutChart,
    YearlyChart,
    DetailReport,
    ProgressSpinner,
  },

  data() {
    return {
      reveal: false,
      data: {
        daily: [1.0, 5.0, 3.0, 7.0, 2.0],
        weekly: [3.0, 10.0, 20.0, 50.0, 11.0],
        monthly: [15.0, 25.0, 23.0, 49.0, 22.0],
      },
      selected_year: "",
      currentYear: new Date().getFullYear(),
      years: [],
      revenue: [],
      isRevenueLoaded: false,
      reportType: "",
    };
  },
  watch: {
    selected_year() {
      this.isRevenueLoaded = false;
      // this.getRevenue();
    },
  },
  computed: {
    profile() {
      return this.$store.state.user.user;
    },
  },
  mounted() {
    this.getSalesReportStats();
  },
  created() {
    const currentYear = new Date().getFullYear();
    const minimumYear = 2022;
    const years = [];
    for (let i = currentYear; i >= minimumYear; i--) {
      years.push({ value: i });
    }
    this.years = years;
    this.selected_year = currentYear;
  },
  methods: {
    async getSalesReportStats() {
      try {
        let url = "/v1/drivisa/admin/sales-report/all";
        const { data } = await axios.get(url);
        this.data = data;
      } catch (e) {}
    },
    async getRevenue() {
      try {
        let url = `/v1/drivisa/admin/sales-report/revenue?year=${this.selected_year}`;
        const { data } = await axios.get(url);
        this.revenue = data;
        this.isRevenueLoaded = true;
      } catch (e) {}
    },
    salesReportType(term) {
      this.reportType = term;
    },
  },
};
</script>

<style scoped lang="scss">
.v-card--reveal {
  bottom: 0;
  opacity: 1 !important;
  position: absolute;
  width: 100%;
}

.values {
  padding: 1rem;
  width: 100%;

  .value {
    width: 45%;
    border-radius: 5px;
    height: 120px;
    color: rgb(37, 37, 37);
    font-size: 1.2rem;
    line-height: 120px;
    text-align: center;
    box-shadow: 0 3px 3px -2px rgba(0, 0, 0, 0.2),
      0 3px 4px 0 rgba(0, 0, 0, 0.14), 0 1px 8px 0 rgba(0, 0, 0, 0.12);

    .icon {
      height: 50px;
      width: 50px;

      mat-icon {
        font-size: 50px;
      }
    }
  }

  @media (max-width: 810px) {
    .value {
      width: 100%;
      margin-bottom: 10px;
    }
  }

  .balance {
    background-image: linear-gradient(to right, #23d0ce, #e3f9f9);
  }

  .av {
    background-image: linear-gradient(to right, #53b357, #e4fae5);
  }
}

th,
td {
  padding: 0 10px !important;
}

.amount {
  font-size: 28px;
}
</style>
