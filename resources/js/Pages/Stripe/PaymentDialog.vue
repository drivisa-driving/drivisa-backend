<template>
  <div class="modal show" id="payment-modal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Payment</h5>
        </div>
        <div class="modal-body">
          <h6>Total Payment: ${{ amount * duration }}</h6>
          <div class="payment-form">
            <div id="card-element" class="input-lg form-control cc-number" style="height:40px"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

let pk = process.env.STRIPE_KEY;

export default {
  name: "PaymentDialog",
  props: ['duration'],
  data() {
    return {
      paymentDialog: false,
      cardElement: null,
      stripe: null,
      amount: 25
    }
  },
  watch: {
    paymentDialog(newValue) {
      if (newValue === true) {
        $("#payment-modal").modal('show');
      }
    }
  },
  mounted() {
    this.stripe = Stripe(pk);
    let elements = this.stripe.elements();
    this.cardElement = elements.create('card');
    this.cardElement.mount('#card-element');
  },
  methods: {
    open() {
      this.paymentDialog = true
    }
  }
}
</script>

<style scoped>

</style>