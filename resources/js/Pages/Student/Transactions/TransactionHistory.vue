<template>
  <div>
    <MasterStudentLayout>
      <v-card elevation="0" class="border">
        <v-card-text class="mt-7 mt-md-0">
          <div class="d-flex justify-content-between">
            <h4 class="font-weight-light text-dark">Transaction History</h4>
          </div>
          <div class="" v-if="items && items.length > 0">
            <v-simple-table>
              <template v-slot:default>
                <thead>
                <tr>
                  <th class="text-left">
                    Sr. No.
                  </th>
                  <th class="text-left">
                    Amount
                  </th>
                  <th class="text-left">
                    Transaction ID
                  </th>
                  <th class="text-left">
                    Payment Intent ID
                  </th>
                  <th class="text-left">
                    Charge ID
                  </th>
                  <th class="text-left">
                    Date
                  </th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="(item, index) in items"
                    :key="index"
                >
                  <td>{{ index + 1 }}.</td>
                  <td>${{ item.amount }}</td>
                  <td>{{ item.tx_id }}</td>
                  <td>{{ item.payment_intent_id }}</td>
                  <td>{{ item.charge_id }}</td>
                  <td>{{ item.date }}</td>
                </tr>
                </tbody>
              </template>
            </v-simple-table>
          </div>
          <div v-else>
            <h2 class="text-muted text-center">There are no Purchases Yet</h2>
          </div>
        </v-card-text>
      </v-card>
    </MasterStudentLayout>
  </div>
</template>

<script>
import MasterStudentLayout from "../Layouts/MasterStudentLayout";

export default {
  name: "TransactionHistory",
  components: {MasterStudentLayout},
  data() {
    return {
      items: [],
    }
  },
  mounted() {
    this.getHistory();
  },
  methods: {
    async getHistory() {
      try {
        let url = '/v1/drivisa/trainees/history/transactions';
        const {data} = await axios.get(url);
        this.items = data.data;
      } catch (e) {

      }
    }
  }

}
</script>

<style scoped>
</style>