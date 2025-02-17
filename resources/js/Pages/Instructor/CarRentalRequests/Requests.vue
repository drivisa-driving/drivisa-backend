<template>
  <div>
    <MasterInstructorLayout>
      <v-card elevation="0" class="border">
        <v-card-text class="mt-7 mt-md-0">
          <div class="d-flex justify-content-between">
            <h4 class="font-weight-light text-dark">Rental Requests</h4>
          </div>
          <div class="" v-if="requests && requests.length > 0">
            <v-simple-table>
              <template v-slot:default>
                <thead>
                <tr>
                  <th class="text-left">Booking Date</th>
                  <th class="text-left">Time</th>
                  <th class="text-left">Location</th>
                  <th class="text-left">Status</th>
                  <th class="text-left">Amount</th>
                  <th class="text-left">Creation Date</th>
                  <th class="text-left">Action</th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="single_request in requests"
                    :key="single_request.id"
                >
                  <td>{{ single_request.booking_date_formatted }}</td>
                  <td>{{ single_request.booking_time_formatted | moment("H:i a") }}</td>
                  <td>{{ single_request.location }}</td>
                  <td>
                    <v-chip>
                      {{ single_request.status }}
                    </v-chip>
                  </td>
                  <td>${{ single_request.charge }}</td>
                  <td>{{ single_request.created_at }}</td>
                  <td>
                    <v-btn
                        small
                        color="green"
                        class="text-capitalize text-white"
                        data-toggle="modal"
                        @click="confirmRentalRequest(single_request.id)"
                    >
                      Accept
                    </v-btn>

                    <!-- Confirm Rental Requests Dialog-->
                    <v-dialog v-model="dialog" persistent max-width="500px">
                      <v-card>
                        <h4 class="text-h5 pt-3 pl-5">
                          Rental Request
                        </h4>
                        <v-card-text></v-card-text>

                        <div class="p-3" v-if="conflict.lenght == 0">
                          <p>Great! you have no conflict and just click confirm to trainee that you are
                            available
                            for booking</p>
                        </div>
                        <div class="p-3" v-else>
                          <p>Once You click on confirm, following lesson will canceled automatically</p>
                          <ul class="list-group">
                            <li
                                v-for="lesson in conflict"
                                class="list-group-item"
                            >
                              <span>{{ lesson.startAt_formatted }} </span>
                              <span class="font-weight-bold float-right">Amount: ${{ lesson.purchase_amount }} </span>
                            </li>
                          </ul>
                        </div>

                        <div class="d-flex justify-content-around px-2 py-3">
                          <v-btn
                              class="text-white text-capitalize"
                              color="red"
                              @click="cancelRentalRequest"
                          >
                            Cancel
                          </v-btn>

                          <v-btn
                              class="text-white text-capitalize"
                              color="green"
                              @click="acceptRequest(single_request.id)"
                          >
                            Confirm
                          </v-btn>
                        </div>
                      </v-card>
                    </v-dialog>
                  </td>
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
import StripeElementDialog from "../../../components/Student/PaymentCard/StripeElementDialog";

let pk = process.env.STRIPE_KEY;

export default {
  name: "Requests",
  components: {StripeElementDialog, MasterInstructorLayout},
  data() {
    return {
      requests: [],
      conflict: [],
      dialog: false,
      request_id: null
    };
  },
  mounted() {
    this.getRequests();
  },
  methods: {
    async getRequests() {
      try {
        let url = "/v1/drivisa/instructors/car-rentals/available-requests";
        const {data} = await axios.get(url);
        this.requests = data.data;
      } catch (e) {
      }
    },
    async checkConflict(id) {
      try {
        let url = "/v1/drivisa/instructors/car-rentals/check-conflict/" + id;
        const {data} = await axios.get(url);
        this.conflict = data.data;
      } catch (e) {
      }
    },
    async acceptRequest() {
      try {
        let url = `/v1/drivisa/instructors/car-rentals/${this.request_id}/accepted`;
        const {data} = await axios.put(url);
        this.$toasted.success(data.message);
        await this.getRequests();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to Accept Request");
      } finally {
        this.dialog = false;
      }
    },
    confirmRentalRequest(id) {
      this.dialog = true;
      this.request_id = id;
      this.checkConflict(id)
    },
    cancelRentalRequest() {
      this.dialog = false;
      this.request_id = null;
    },
  },
};
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