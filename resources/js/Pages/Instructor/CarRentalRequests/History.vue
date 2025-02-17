<template>
  <div>
    <MasterInstructorLayout>
      <v-card elevation="0" class="border">
        <v-card-text class="mt-7 mt-md-0">
          <div class="d-flex justify-content-between">
            <h4 class="font-weight-light text-dark">Rental Requests History</h4>
          </div>
          <div class="" v-if="requests && requests.length > 0">
            <v-simple-table>
              <template v-slot:default>
                <thead>
                <tr>
                  <th class="text-left">
                    Booking Date
                  </th>
                  <th class="text-left">
                    Time
                  </th>
                  <th class="text-left">
                    Location
                  </th>
                  <th class="text-left">
                    Status
                  </th>
                  <th class="text-left">
                    Amount
                  </th>
                  <th class="text-left">
                    Payment Status
                  </th>
                  <th class="text-left">
                    Creation Date
                  </th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="single_request in requests"
                    :key="single_request.id"
                >
                  <td>{{ single_request.booking_date }}</td>
                  <td>{{ single_request.booking_time | moment("H:i a") }}</td>
                  <td>{{ single_request.location }}</td>
                  <td>
                    <v-chip>
                      {{ single_request.status }}
                    </v-chip>
                  </td>
                  <td>${{ single_request.charge }}</td>
                  <td>{{ single_request.purchase_id == null ? "Not Paid" : "Paid" }}</td>
                  <td>{{ single_request.created_at }}</td>
                </tr>
                </tbody>
              </template>
            </v-simple-table>
          </div>
          <div v-else>
            <h2 class="text-muted text-center">There are no Rental Request</h2>
          </div>
        </v-card-text>
      </v-card>
    </MasterInstructorLayout>
  </div>
</template>

<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";

export default {
  name: "Requests",
  components: {MasterInstructorLayout},
  data() {
    return {
      requests: [],
    }
  },
  mounted() {
    this.getAcceptedRequests();
  },
  methods: {
    async getAcceptedRequests() {
      try {
        let url = '/v1/drivisa/instructors/car-rentals/accepted-requests';
        const {data} = await axios.get(url);
        this.requests = data.data;
      } catch (e) {

      }
    },
  }
}
</script>

<style scoped lang="scss">
.option-icon {
  background-repeat: no-repeat;
  display: inline-block;
  fill: currentColor;
  height: 24px;
  width: 24px;
  font-size: 24px;
}

.card-details {
  background-color: #eee;
  padding-top: 3rem;
  padding-bottom: 2rem;
}

.payment-form {
  .fieldset {
    margin: 0 15px 10px;
    padding: 0;
    border-style: none;
    display: flex;
    flex-flow: row wrap;
    justify-content: space-between;
    font-family: Montserrat, sans-serif !important;

    .form-input {
      color: #333;
      margin-top: 6px;
      padding: 10px 20px 11px;
      background-color: rgba(255, 255, 255, 0.8);
      border-radius: 5px;
      width: 100%;
      font-family: Montserrat, sans-serif !important;

      &:focus {
        border: none;
        color: #333;
        background-color: #f6f9fc;
      }

      &:focus-visible {
        outline: none;
      }
    }
  }

  .remaining {
    text-align: center;

    small {
      margin-left: 70px;
      color: #80808096;
    }
  }

}


</style>