<template>
  <div>
    <MasterInstructorLayout>
      <v-card elevation="0" class="border">
        <v-card-text class="mt-md-0 mt-6">
          <div class="d-flex justify-content-between mb-3">
            <h2 class="ac-info">Account Information</h2>
          </div>
          <div class="text-center">
            <h4 v-if="details.accountId" class="text-success">You are connected with our payment system</h4>
            <h4 v-else class="text-danger">You are not connected with our payment system</h4>
          </div>
          <div class="table-responsive" v-if="details.stripe_account_details">
            <table class="table table-bordered">
              <tr>
                <th class="text-left">
                  Email
                </th>
                <td class="text-left text-capitalize">
                  {{ details.stripe_account_details.email }}
                </td>
              </tr>
              <tr>
                <th class="text-left">
                  Business Type
                </th>
                <td class="text-left text-capitalize">
                  {{ details.stripe_account_details.business_type }}
                </td>
              </tr>
              <tr>
                <th class="text-left">
                  Business Country
                </th>
                <td class="text-left text-capitalize">
                  {{ details.stripe_account_details.country }}
                </td>
              </tr>
              <tr>
                <th class="text-left">
                  Capabilities
                </th>
                <td class="text-left">
                  <ul class="list-group">
                    <li class="list-group-item">
                      Card Payments : {{ details.stripe_account_details.capabilities.card_payments }}
                    </li>
                    <li class="list-group-item">
                      Transfer : {{ details.stripe_account_details.capabilities.transfers }}
                    </li>
                  </ul>
                </td>
              </tr>
              <tr>
                <th class="text-left">
                  Detail Submitted
                </th>
                <td class="text-left text-capitalize">
                  {{ details.stripe_account_details.details_submitted ? 'Yes' : 'No' }}
                </td>
              </tr>
            </table>
          </div>
          <div class="text-center">
            <v-btn
                color="red"
                class="text-capitalize text-white"
                v-if="details.accountId !== null"
                @click="deleteAccount"
            >
              Delete Account
            </v-btn>
            <v-btn
                color="primary"
                class="text-capitalize"
                @click="saveAccount"
            >
              {{ details.accountId ? "Update Account" : "Connect Account" }}
            </v-btn>
          </div>
        </v-card-text>
      </v-card>

      <v-card elevation="0" class="border">
        <v-card-text class="mt-md-0 mt-6">
          <div v-if="details.accountId !== null">
            <h3>Bank Account Details</h3>
            <div class="form-group">
              <label for="routing-number">Transit Number</label>
              <input class="form-control" type="text" v-model="details.transit_number"
                     :disabled="details.stripe_account_details.details_submitted"/>
            </div>
            <div class="form-group">
              <label for="routing-number">Institution Number</label>
              <input class="form-control" type="text" v-model="details.institution_number"
                     :disabled="details.stripe_account_details.details_submitted"/>
            </div>
            <div class="form-group">
              <label for="account-number">Account Number</label>
              <input class="form-control" type="text" v-model="details.account_number"
                     :disabled="details.stripe_account_details.details_submitted"/>
            </div>
            <div class="form-group">
              <label for="account-holder-name">Account Holder Name</label>
              <input class="form-control" type="text" v-model="details.account_holder_name"
                     :disabled="details.stripe_account_details.details_submitted"/>
            </div>
          </div>


          <div class="text-center">
            <v-btn
                color="primary"
                class="text-capitalize"
                @click="attachBankAccount"
                :disabled="details.stripe_account_details.details_submitted"
            >
              Save Bank Account
            </v-btn>
          </div>
        </v-card-text>
      </v-card>
    </MasterInstructorLayout>
  </div>
</template>

<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";

let pk = process.env.STRIPE_KEY;

export default {
  name: "BankAccount",
  components: {MasterInstructorLayout},
  data() {
    return {
      stripe: null,
      account: null,
      details: {
        accountId: null,
        stripe_account_details: null,
        account_holder_name: null,
        account_number: null,
        account_holder_type: null,
        transit_number: null,
        institution_number: null,
      }
    }
  },

  mounted() {
    this.stripe = Stripe(pk);
    this.getAccount();
  },
  methods: {
    async getAccount() {
      try {
        const url = "/v1/drivisa/instructors/finance/accounts";
        const {data} = await axios.get(url);
        this.details = data.data;
      } catch (e) {

      }
    },
    async attachBankAccount() {
      try {

        let paramsObject = {
          country: "CA",
          currency: "CAD",
          account_holder_name: this.details.account_holder_name,
          account_number: this.details.account_number,
          account_holder_type: 'individual'
        };

        paramsObject['routing_number'] = this.details.transit_number + '-' + this.details.institution_number;

        let tokenObject = await this.stripe.createToken('bank_account', paramsObject)

        let url = "/v1/drivisa/instructors/finance/accounts/attach-bank-account";

        const {data} = await axios.post(url, {
          token: tokenObject.token.id,
          account_number: tokenObject.account_number
        });

        this.$toasted.success("Bank Account Attached Successfully")
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to Save Bank Account")
      }
    },
    async saveAccount() {
      try {
        let url = "/v1/drivisa/instructors/finance/accounts";
        if (this.details.accountId !== null) {
          url = "/v1/drivisa/instructors/finance/accounts/update";
        }
        const {data} = await axios.post(url, this.details);
        window.location.href = data;
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to Save Bank Account")
      }
    },
    async deleteAccount() {
      try {
        let url = "/v1/drivisa/instructors/finance/accounts";
        await axios.delete(url);
        this.$toasted.success("Account Deleted");
        this.details.accountId = null;
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to delete Bank Account")
      }
    }
  }
}
</script>

<style scoped>
.ac-info {
  font-weight: 400;
  font-size: 24px;
  letter-spacing: normal;
  margin: 0 0 16px;
  font-family: Montserrat, sans-serif !important;
  color: rgba(0, 0, 0, .87);
}
</style>