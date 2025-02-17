<template>
  <div>
    <MasterStudentLayout>
      <v-card elevation="0" class="border">
        <v-card-text class="mt-7 mt-md-0">
          <div class="d-flex justify-content-between">
            <h4 class="font-weight-light text-dark">Purchase History </h4>
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
                    Type
                  </th>
                  <th class="text-left">
                    Amount
                  </th>
                  <th class="text-left">
                    PI ID
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
                  <td>{{ item.type | uppercase }}</td>
                  <td>${{ item.transaction.amount }}</td>
                  <td>{{ item.transaction.payment_intent_id }}</td>
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
  name: "PurchaseHistory",
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
        let url = '/v1/drivisa/trainees/history/purchases';
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