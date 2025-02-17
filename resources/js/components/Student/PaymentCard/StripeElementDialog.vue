<template>
  <v-dialog
      v-model="dialog"
      max-width="500"
      v-show="show"
  >
    <v-card>
      <v-card-title class="text-h5 text-center">
        Car Rental Request Finalize
      </v-card-title>

      <v-card-text>
        <div class="payment-form">
          <div id="card-element" class="input-lg form-control cc-number" style="height:40px"></div>
          <div class="text-center" style="margin-top: 22px">
            <v-btn color="primary" rounded @click="paymentMethod" class="mt-2">
              Pay
            </v-btn>
          </div>
        </div>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>

let pk = process.env.STRIPE_KEY;

export default {
  name: "StripeElementDialog",
  props: ['dialog', 'show'],
  data() {
    return {
      cardElement: null,
      stripe: null,
    }
  },
  created() {

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

        // this.bookingRequestData.payment_method_id = payment_method_id;
        // if (this.bookingRequestData.payment_method_id) {
        //   await this.buyPackage()
        // }

      } catch (e) {
        this.$toasted.error("Please Check your payment Details")
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