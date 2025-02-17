<template>
  <div>
    <MasterStudentLayout>
      <v-card elevation="0" class="border">
        <v-dialog
            v-model="cancelDialog"
            width="500"
        >
          <v-card>
            <v-card-title class="text-h5 grey lighten-2">
              Reason
            </v-card-title>

            <v-textarea
                v-model="cancel.reason"
                placeholder="Enter Your Reason to instructor"
                class="m-2"
                solo
                name="input-7-4"
            ></v-textarea>

            <v-divider></v-divider>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                  color="primary"
                  text
                  @click="cancelDialog = false"
              >
                No
              </v-btn>
              <v-btn
                  color="red accent-5"
                  text
                  @click="cancelcourse()"
              >
                Yes
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-card-text class="mt-7 mt-md-0">
          <div class="d-flex justify-content-between">
            <h4 class="font-weight-light text-dark">Rental Requests </h4>
          </div>
          <div class="" v-if="requests && requests.length > 0">
            <v-simple-table>
              <template v-slot:default>
                <thead>
                <tr>
                  <th class="text-left">
                    Date
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
                    Action
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
                  <td>{{ single_request.created_at }}</td>
                  <td>
                    <v-chip>
                      {{ single_request.status }}
                    </v-chip>
                  </td>
                  <td>
                    <v-btn
                        v-if="single_request.status === 'accepted'"
                        small color="green" class="text-capitalize text-white"
                        data-toggle="modal" data-target="#pay_modal"
                        @click="selected_request = single_request.id"
                    >
                      Pay
                    </v-btn>
                    <v-btn
                        v-if="single_request.status === 'registered'"
                        small color="red accent-2" class="text-capitalize text-white"
                        @click="dialogMethod(single_request.id)"
                    >
                      Cancel
                    </v-btn>
                  </td>
                </tr>
                </tbody>
              </template>
            </v-simple-table>
          </div>
          <div v-else>
            <h2 class="text-muted text-center">There are no course</h2>
          </div>
        </v-card-text>
      </v-card>

      <div class="modal" tabindex="-1" role="dialog" id="pay_modal">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"> Car Rental Request Finalize</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" ref="close_modal">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="payment-form">
                <div id="card-element" class="input-lg form-control cc-number" style="height:40px"></div>
                <div class="text-center" style="margin-top: 22px">
                  <v-btn color="primary" rounded @click="paymentMethod" class="mt-2">
                    Pay
                  </v-btn>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </MasterStudentLayout>
  </div>
</template>

<script>
import MasterStudentLayout from "../Layouts/MasterStudentLayout";
import StripeElementDialog from "../../../components/Student/PaymentCard/StripeElementDialog";


let pk = process.env.STRIPE_KEY;

export default {
  name: "Requests",
  components: {StripeElementDialog, MasterStudentLayout},
  data() {
    return {
      cardElement: null,
      stripe: null,
      selected_request: null,
      dialog: false,
      currentPage: 1,
      showFilter: false,
      requests: [],
      cancelDialog: false,
      cancel: {
        request_id: null,
        reason: ""
      }
    }
  },
  watch: {
    from() {
      this.getRequests()
    },
    to() {
      this.getRequests()
    }
  },
  mounted() {
    this.stripe = Stripe(pk);
    let elements = this.stripe.elements();
    this.cardElement = elements.create('card');
    this.cardElement.mount('#card-element');
    this.getRequests();
  },
  methods: {
    async paymentMethod() {
      try {
        let payment_method_id = undefined;
        const {
          paymentMethod,
          error
        } = await this.stripe.createPaymentMethod(
            'card', this.cardElement);

        if (error) {
          this.$root.handleErrorToast("Unable to process your card")
          return;
        } else {
          payment_method_id = paymentMethod.id;
        }
        if (payment_method_id) {
          await this.requestPaymentUpdate(payment_method_id)
        }

      } catch (e) {
        this.$toasted.error("Please Check your payment Details")
      }
    },
    dialogMethod(id) {
      this.cancelDialog = true;
      this.cancel.course_id = id;
    },
    async getRequests() {
      try {
        let url = '/v1/drivisa/trainees/car-rental/all-requests';
        const {data} = await axios.get(url);
        this.requests = data.data;
      } catch (e) {

      }
    },
    async requestPaymentUpdate(payment_id) {
      try {
        let url = `/v1/drivisa/trainees/car-rental/${this.selected_request}/paid`;
        const {data} = await axios.post(url, {
          payment_id: payment_id
        });
        this.$toasted.success(data.message)
        this.$refs.close_modal.click();
        this.selected_request = null;
        await this.getRequests();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to buy package");
      }
    },
    async cancelcourse() {
      try {
        //const url = `/v1/drivisa/trainees/requests/${this.cancel.course_id}/cancel-by-trainee`;
        // const {data} = await axios.post(url, {
        //   reason: this.cancel.reason
        // });
        this.cancelDialog = false;
        await this.getRequests()

        this.$toasted.success("Cancel Successfully and Refund Initiated");


      } catch (e) {
        this.$toasted.error(e.response.data.message)
      }
    }
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