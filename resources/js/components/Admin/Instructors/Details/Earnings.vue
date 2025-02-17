<template>
  <div>
    <div
      class="d-flex justify-content-between mt-2 align-items-center"
      style="height: 30px"
    >
      <v-btn
        text
        style="font-size: 14px"
        class="text-capitalize"
        @click="getWeeklyEarningsPrevious(7)"
      >
        <i class="mdi mdi-arrow-left"></i> Previous
      </v-btn>
      <div class="font-weight-light text-muted">
        {{ this.label }}
      </div>
      <v-btn
        text
        style="font-size: 14px"
        class="text-capitalize"
        @click="getWeeklyEarningsNext(7)"
      >
        Next <i class="mdi mdi-arrow-right"></i>
      </v-btn>
    </div>
    <div class="mx-3">
      <div class="mt-4">
        <p style="font-size: 18px" class="mb-1">
          Instructor's earning in this week
        </p>
        <strong>${{ weeklyEarning.total }}</strong>
      </div>
      <div
        class="
          values
          d-flex
          flex-column flex-md-row
          justify-content-center justify-content-md-around
          pt-0
          px-4
          pb-4
        "
      ></div>
      <table
        class="table table-bordered"
        v-if="weeklyEarning.current_collection"
      >
        <tr>
          <td>Monday</td>
          <td>${{ weeklyEarning.current_collection.Monday.amount }}</td>
        </tr>
        <tr>
          <td>Tuesday</td>
          <td>${{ weeklyEarning.current_collection.Tuesday.amount }}</td>
        </tr>
        <tr>
          <td>Wednesday</td>
          <td>${{ weeklyEarning.current_collection.Wednesday.amount }}</td>
        </tr>
        <tr>
          <td>Thursday</td>
          <td>${{ weeklyEarning.current_collection.Thursday.amount }}</td>
        </tr>
        <tr>
          <td>Friday</td>
          <td>${{ weeklyEarning.current_collection.Friday.amount }}</td>
        </tr>
        <tr>
          <td>Saturday</td>
          <td>${{ weeklyEarning.current_collection.Saturday.amount }}</td>
        </tr>
        <tr>
          <td>Sunday</td>
          <td>${{ weeklyEarning.current_collection.Sunday.amount }}</td>
        </tr>
        <tr>
          <td colspan="2" class="pt-2">
            <strong>Earnings Breakdown</strong>
          </td>
        </tr>
        <tr>
          <td>Lesson</td>
          <td>${{ breakdownWeeklyEarning.lesson_earning }}</td>
        </tr>
        <tr>
          <td>Road Test</td>
          <td>${{ breakdownWeeklyEarning.road_test_earning }}</td>
        </tr>
        <tr>
          <td>Additional Km</td>
          <td>${{ breakdownWeeklyEarning.additional_amount }}</td>
        </tr>
        <tr>
          <td>Cancel/Reschedule Compensation</td>
          <td>${{ breakdownWeeklyEarning.compensation_earning }}</td>
        </tr>
        <tr>
          <td>Referral Earning</td>
          <td>${{ breakdownWeeklyEarning.referral_amount }}</td>
        </tr>
        <tr>
          <td>HST</td>
          <td>${{ breakdownWeeklyEarning.hst }}</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: "Earnings",
  data() {
    return {
      PreviousCount: 0,
      to: null,
      from: null,
      label: null,
      breakdownWeeklyEarning: {},
      weeklyEarning: {},
    };
  },
  mounted() {
    this.initRangeDates();
    this.getWeeklyEarnings();
    this.getWeeklyBreakdownEarnings();
  },
  methods: {
    async getWeeklyEarnings() {
      const url = `/v1/drivisa/admin/instructors/finance/earnings/weekly/${+this
        .$route.params.id}?from=${this.getFullDate(
        this.from
      )}&to=${this.getFullDate(this.to)}`;
      const { data } = await axios.get(url);
      this.weeklyEarning = data.data;
    },
    async getWeeklyBreakdownEarnings() {
      const url = `/v1/drivisa/admin/instructors/finance/earnings/breakdown-weekly/${+this
        .$route.params.id}?from=${this.getFullDate(
        this.from
      )}&to=${this.getFullDate(this.to)}`;
      const { data } = await axios.get(url);
      this.breakdownWeeklyEarning = data.data;
    },
    initRangeDates() {
      this.PreviousCount = 0;
      this.from = new Date(Date.now());
      this.to = new Date(Date.now());
      this.from.setDate(this.getPreviousMonday().getDate());
      this.to.setDate(this.getPreviousMonday().getDate() + 6);
      this.label = `${this.from.toLocaleDateString()} - ${this.to.toLocaleDateString()}`;
    },
    getWeeklyEarningsNext(value) {
      this.PreviousCount++;
      this.to.setDate(
        this.PreviousCount === 0
          ? new Date().getDate()
          : this.to.getDate() + value
      );
      // we don't get future values so in case prev is 0 then we set today's date
      this.from.setDate(this.from.getDate() + value);
      this.label = `${this.from.toLocaleDateString()} - ${this.to.toLocaleDateString()}`;
      this.getWeeklyEarnings();
      this.getWeeklyBreakdownEarnings();
    },
    getWeeklyEarningsPrevious(value) {
      this.PreviousCount--;
      this.to.setDate(this.to.getDate() - value);
      this.from.setDate(this.from.getDate() - value);
      this.label = `${this.from.toLocaleDateString()} - ${this.to.toLocaleDateString()}`;
      this.getWeeklyEarnings();
      this.getWeeklyBreakdownEarnings();
    },
    getFullDate(Date) {
      let year = Date.getFullYear();
      let month = Date.getMonth() + 1;
      let date = Date.getDate();

      if (month < 10) {
        month = "0" + month;
      }
      if (date < 10) {
        date = "0" + date;
      }
      return year + "-" + month + "-" + date;
    },
    getPreviousMonday() {
      var prevMonday = new Date();
      prevMonday.setDate(
        prevMonday.getDate() - ((prevMonday.getDay() + 6) % 7)
      );
      return prevMonday;
    },
  },
};
</script>

<style scoped lang="scss">
th,
td {
  padding: 3px 10px !important;
}
</style>