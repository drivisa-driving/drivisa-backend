<template>
  <div>
    <MasterInstructorLayout>
      <v-card elevation="0" class="border">
        <v-card-text>
          <h3 class="font-weight-light text-dark">Earning</h3>
          <EarningChart :data="chartData"/>
          <div class="d-flex justify-content-between mt-5 align-items-center" style="height: 30px">
            <v-btn text style="font-size: 14px" class="text-capitalize"
                   @click="getEarningPrevious(7)"
            >
              <i class="mdi mdi-arrow-left"></i> Previous
            </v-btn>
            <div class="font-weight-light text-muted">
              {{ this.label }}
            </div>
            <v-btn text style="font-size: 14px" class="text-capitalize"
                   @click="getEarningNext(7)"
            >
              Next <i class="mdi mdi-arrow-right"></i>
            </v-btn>
          </div>

          <div
              class="values d-flex flex-column flex-md-row justify-content-center justify-content-md-around mt-4 p-4">
            <div class="value av mat-elevation-z3">
              <div class="">
                <strong>
                  Payout
                </strong>
                <span>${{ earning.available }}</span>
              </div>

            </div>
            <div class="value balance mat-elevation-z3">

              <div class="">
                <strong>
                  Pending
                </strong>
                <span>${{ earning.pending }}</span>
              </div>

            </div>
          </div>

          <v-simple-table>
            <template v-slot:default>
              <thead>
              <tr>
                <th class="text-left">
                  Net
                </th>
                <th class="text-left">
                  Status
                </th>
                <th class="text-left">
                  Type
                </th>
                <th class="text-left">
                  Created
                </th>
                <th class="text-left">
                  Available On
                </th>
              </tr>
              </thead>
              <tbody>
              <tr
                  v-for="transaction in earning.transactions"
                  :key="transaction.id"
              >
                <td>${{ transaction.net }}</td>
                <td>{{ transaction.status }}</td>
                <td>{{ transaction.type }}</td>
                <td>{{ transaction.created |dateAgo }}</td>
                <td>{{ transaction.availableOn }}</td>
              </tr>
              </tbody>
            </template>
          </v-simple-table>

        </v-card-text>
      </v-card>
    </MasterInstructorLayout>
  </div>
</template>

<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";
import EarningChart from "../../../components/Instructor/Earning/EarningChart";

export default {
  name: "Earning",
  components: {MasterInstructorLayout, EarningChart},
  data() {
    return {
      PreviousCount: 0,
      to: null,
      from: null,
      label: null,
      earning: {
        available: 0,
        pending: 0,
        balance: 0,
        transactions: []
      },
      chartData: []
    }
  },
  mounted() {
    this.initRangeDates();
    this.getEarnings();
  },
  methods: {
    async getEarnings() {
      const url =
          `/v1/drivisa/instructors/finance/earnings?from=${this.getFullDate(this.from)}&to=${this.getFullDate(this.to)}`;
      const {data} = await axios.get(url);
      this.earning = data.data;
      console.log(this.earning)
      this.chartData = [
        this.earning.balance,
      ]
    },
    initRangeDates() {
      this.PreviousCount = 0;
      this.from = new Date(Date.now());
      this.to = new Date(Date.now());
      this.from.setDate(this.getPreviousMonday().getDate());
      this.label = `${this.from.toLocaleDateString()} - ${this.to.toLocaleDateString()}`;
    },
    getEarningNext(value) {
      this.PreviousCount++;
      this.to.setDate(this.PreviousCount === 0 ? new Date().getDate() : (this.to.getDate() + value));
      // we don't get future values so in case prev is 0 then we set today's date
      this.from.setDate(this.from.getDate() + value);
      this.label = `${this.from.toLocaleDateString()} - ${this.to.toLocaleDateString()}`;
      this.getEarnings();
    },
    getEarningPrevious(value) {
      this.PreviousCount--;
      this.to.setDate(this.from.getDate() - 1)
      this.from.setDate(this.from.getDate() - value);
      this.label = `${this.from.toLocaleDateString()} - ${this.to.toLocaleDateString()}`;
      this.getEarnings();
    },
    getFullDate(Date) {
      let year = Date.getFullYear();
      let month = (Date.getMonth() + 1);
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
      var date = new Date();
      var day = date.getDay();
      var prevMonday = new Date();
      if (date.getDay() == 0) {
        prevMonday.setDate(date.getDate() - 7);
      } else {
        prevMonday.setDate(date.getDate() - (day - 1));
      }
      return prevMonday;
    }
  }
}
</script>

<style scoped lang="scss">
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
    box-shadow: 0 3px 3px -2px rgba(0, 0, 0, .2), 0 3px 4px 0 rgba(0, 0, 0, .14), 0 1px 8px 0 rgba(0, 0, 0, .12);

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

th, td {
  padding: 0 10px !important;
}
</style>