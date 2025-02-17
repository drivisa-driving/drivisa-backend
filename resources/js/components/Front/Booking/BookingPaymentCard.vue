<template>
  <div class="col-lg-12 card-details">
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
            <li>Cancellation fee: $25/hr</li>
            <li>Reschedule fee: $25/hr within 24 hours of the booked lesson time</li>
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
        <v-btn color="primary" rounded @click="bookLesson" class="mt-2" :disabled="!privacy_accepted">
          Pay
        </v-btn>
        <v-btn
            @click="bookLessonByCredit"
            color="primary"
            rounded
            class="mt-2"
            :disabled="!privacy_accepted"
            v-if="user.credit > 0">
          Pay By Credit
        </v-btn>
      </div>
      <div class="text-center" style="margin-top: 22px">
        <v-btn
            @click="bookLessonByCredit(e, true)"
            color="primary"
            rounded
            class="mt-2"
            :disabled="!privacy_accepted"
            v-if="user.credit > 0">
          Book As BDE Lesson
        </v-btn>
      </div>
      <div class="remaining text-center" v-if="user.credit > 0">
        <small class="ml-0">Remaining Credit {{ user.credit }}</small>
      </div>
    </div>
  </div>
</template>

<script>
import {maska} from "maska";

let pk = process.env.STRIPE_KEY;

export default {
  name: "BookingPaymentCard",
  directives: {maska},
  props: ['lesson', 'pick_drop', 'add_cost'],
  data() {
    return {
      privacy_accepted: false,
      privacy_dialog: false,
      cardElement: null,
      stripe: null,
      bookingRequestData: {
        lesson_id: this.$route.query.lesson,
        payment_method_id: null,
        pick_drop: {
          type: "default",
          drop_lat: "",
          drop_long: "",
          pick_lat: "",
          pick_long: "",
        },
        working_hours: []
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

    this.bookingRequestData.working_hours.push({
      id: this.$route.query.lesson
    })
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

        this.bookingRequestData.payment_method_id = payment_method_id;

      } catch (e) {
        this.$toasted.error("Please Check your payment Details")
      }
    },
    async bookLesson() {
      try {

        // charge to card
        await this.paymentMethod();

        const url = "/v1/drivisa/trainees/booking-lesson";
        const {data} = axios.post(url, this.bookingRequestData);


        this.$toasted.success("Booking was created successfully !");
        await this.$router.push({name: "single-instructor", params: {username: this.$route.query.instructor}})

      } catch (e) {
        this.$root.handleErrorToast(e, "Error Happen!")
      }
    },
    async bookLessonByCredit($event, is_bde = false) {
      try {

        if (this.add_cost && this.add_cost.cost > 0) {
          // charge to card
          await this.paymentMethod()

          if (this.bookingRequestData.payment_method_id == null) {
            this.$toasted.error("Payment Not Completed Yet! please check your card");
            return;
          }

        }

        const url = "/v1/drivisa/trainees/booking-lesson/by-credit";

        this.bookingRequestData['bde_lesson'] = is_bde;

        const {data} = axios.post(url, this.bookingRequestData);


        this.$toasted.success("Booking was created successfully !");
        await this.$router.push({name: "single-instructor", params: {username: this.$route.query.instructor}})

      } catch (e) {
        this.$root.handleErrorToast(e, "Error Happen!")
      }
    }
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