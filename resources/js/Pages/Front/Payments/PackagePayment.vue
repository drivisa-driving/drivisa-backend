<template>
  <div>
    <Header/>
    <v-dialog
        v-model="privacy_dialog"
        width="500"
    >
      <v-card>
        <v-card-title class="text-h5 bg-primary lighten-2 text-white">
          Booking Policy
        </v-card-title>

        <v-card-text class="mt-2">
          <ul>
            <li>Once you booked a lesson with credit then you can't cancel package and no refund will provide</li>
            <li>Reschedule fee: $25/hr within 24 hours of the booked lesson time even you booked a lesson with credit
            </li>
            <li>The instructor has the right to cancel the lesson if the trainee is 15 minutes late for the start of the
              lesson (will be subjected to fee $25/hr)
            </li>
          </ul>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
              color="primary"
              text
              @click="privacy_accepted = true; privacy_dialog = false"
          >
            I accept
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <div class="d-container my-5">
      <h3>Package</h3>
      <div class="row">
        <div class="col-lg-6">
          <table class="table table-bordered" v-if="single_package">
            <tr>
              <th>Package Name:</th>
              <td>{{ single_package.name }}</td>
            </tr>
            <tr>
              <th>Total Credit Receive:</th>
              <td>{{ single_package.packageData.hours }} Hours</td>
            </tr>
            <tr>
              <th>Sale Price:</th>
              <td>${{ single_package.packageData.amount }}</td>
            </tr>
            <tr>
              <th>Discount Price:</th>
              <td>${{ single_package.packageData.discount_price }}</td>
            </tr>
            <tr>
              <th>Tax:</th>
              <td>${{ single_package.packageData.discount_price * 0.13 }}</td>
            </tr>
            <tr class="bg-success text-white font-weight-bold">
              <th>Chargeable:</th>
              <td>${{ single_package.packageData.discount_price * 0.13 + single_package.packageData.discount_price }}
              </td>
            </tr>
          </table>
        </div>
        <div class="col-lg-6 card-details">
          <div class="payment-form">
            <div id="card-element" class="input-lg form-control cc-number" style="height:40px"></div>
            <div class="text-center" style="margin-top: 22px">
              <input type="checkbox" v-model="privacy_accepted" id="privacy_accepted">
              <label for="privacy_accepted">
                I read & Accept <span
                  class="text-primary text-decoration-underline"
                  style="cursor:pointer;"
                  @click="privacy_dialog = true"
              >Privacy Policy</span>
              </label>
            </div>
            <div class="text-center" style="margin-top: 22px">
              <v-btn color="primary" rounded @click="paymentMethod" class="mt-2" :disabled="!privacy_accepted">
                Buy
              </v-btn>
              <div v-if="user.credit > 0">
                <small>Remaining Credit: {{ user.credit }} Hours</small>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
    <Footer/>
  </div>

</template>

<script>
import {maska} from "maska";
import Header from "../../../components/Front/Header";
import Footer from "../../../components/Front/Footer";

let pk = process.env.STRIPE_KEY;

export default {
  name: "PackagePayment",
  components: {Header, Footer},
  directives: {maska},
  data() {
    return {
      privacy_dialog: false,
      privacy_accepted: false,
      cardElement: null,
      stripe: null,
      single_package: null,
      bookingRequestData: {
        payment_method_id: null,
      }
    }
  },
  watch: {
    pick_drop(newVal) {
      this.bookingRequestData.pick_drop = newVal;
    }
  },
  computed: {
    user() {
      return this.$store.state.user.user
    }
  },
  created() {
    this.$store.dispatch('traineeProfile/getProfile')
  },
  mounted() {
    this.stripe = Stripe(pk);
    let elements = this.stripe.elements();
    this.cardElement = elements.create('card');
    this.cardElement.mount('#card-element');
    this.getPackageDetails();
  },
  methods: {
    async getPackageDetails() {
      try {
        let url = '/v1/drivisa/trainees/packages/' + this.$route.query.package

        const {data} = await axios.get(url)
        this.single_package = data.data

      } catch (e) {

      }
    },
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

        this.bookingRequestData.payment_method_id = payment_method_id;
        if (this.bookingRequestData.payment_method_id) {
          await this.buyPackage()
        }

      } catch (e) {
        this.$toasted.error("Please Check your payment Details")
      }
    },
    async buyPackage() {
      try {
        const url = `/v1/drivisa/trainees/packages/${this.$route.query.package}/buy`;
        const {data} = await axios.post(url, this.bookingRequestData);

        this.$toasted.success("Credit Stored successfully !");

        await this.$router.push("/trainee/purchase-history");
      } catch (e) {
        this.$root.handleErrorToast(e, "Error Happen!")
      }
    },
  }
}
</script>

<style scoped lang="scss">
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