<template>
  <div>
    <MasterInstructorLayout>
      <v-card elevation="0" class="border">
        <v-card-text class="mt-md-0 mt-6">
          <div class="d-flex justify-content-between mb-3">
            <h2 class="ac-info">Account Information</h2>
            <div>
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
                  v-if="!is_disabled"
                  @click="saveAccount"
              >
                {{ is_disabled && details.accountId ? "Update Changes" : "Save Changes" }}
              </v-btn>
              <v-btn
                  color="primary"
                  class="text-capitalize"
                  v-else
                  @click="is_disabled = false"
              >
                Edit Account
              </v-btn>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <ValidationProvider name="first name" rules="alpha" v-slot="{ errors }">
                <v-text-field
                    :disabled="is_disabled"
                    outlined
                    :error-messages="errors[0]"
                    label="First Name *"
                    placeholder="First Name * "
                    v-model="details.first_name"
                ></v-text-field>
              </ValidationProvider>
            </div>
            <div class="col-md-6">
              <ValidationProvider name="last name" rules="alpha" v-slot="{ errors }">
                <v-text-field
                    :disabled="is_disabled"
                    outlined
                    :error-messages="errors[0]"
                    label="Last Name *"
                    placeholder="Last Name * "
                    v-model="details.last_name"
                >
                </v-text-field>
              </ValidationProvider>
            </div>

            <div class="col-md-6">
              <ValidationProvider name="email" rules="email" v-slot="{ errors }">
                <v-text-field
                    :disabled="is_disabled"
                    outlined
                    :error-messages="errors[0]"
                    label="Email *"
                    placeholder="Email"
                    v-model="details.email"
                ></v-text-field>
              </ValidationProvider>
            </div>
            <div class="col-md-6">
              <ValidationProvider name="phone" rules="required" v-slot="{ errors }">
                <v-text-field
                    :disabled="is_disabled"
                    outlined
                    :error-messages="errors[0]"
                    label="Phone *"
                    placeholder="+1 (XXX) XXX-XXXXX * "
                    v-maska="'+1 (###) ###-####'"
                    v-model="details.phone"
                >
                </v-text-field>
              </ValidationProvider>
            </div>

            <div class="col-md-6">
              <ValidationProvider name="id number" rules="required|numeric" v-slot="{ errors }">
                <v-text-field
                    :disabled="is_disabled"
                    outlined
                    :error-messages="errors[0]"
                    label="Id Number *"
                    placeholder="Id Number * "
                    v-model="details.id_number"
                ></v-text-field>
              </ValidationProvider>
            </div>
            <div class="col-md-6">
              <ValidationProvider name="date of birth" rules="required" v-slot="{ errors }">
                <v-text-field
                    :disabled="is_disabled"
                    type="date"
                    outlined
                    :error-messages="errors[0]"
                    label="Date of Birth *"
                    placeholder="Date of Birth * "
                    v-model="details.birth_date"
                >
                </v-text-field>
              </ValidationProvider>
            </div>

            <div class="col-md-6">
              <ValidationProvider name="city" rules="required|alpha" v-slot="{ errors }">
                <v-text-field
                    :disabled="is_disabled"
                    outlined
                    :error-messages="errors[0]"
                    label="City *"
                    placeholder="city * "
                    v-model="details.address.city"
                ></v-text-field>
              </ValidationProvider>
            </div>
            <div class="col-md-6">
              <ValidationProvider name="state" rules="required|alpha" v-slot="{ errors }">
                <v-text-field
                    :disabled="is_disabled"
                    outlined
                    :error-messages="errors[0]"
                    label="State *"
                    placeholder="State * "
                    v-model="details.address.state"
                >
                </v-text-field>
              </ValidationProvider>
            </div>

            <div class="col-md-6">
              <ValidationProvider name="address" rules="required" v-slot="{ errors }">
                <v-text-field
                    :disabled="is_disabled"
                    outlined
                    :error-messages="errors[0]"
                    label="Address *"
                    placeholder="Address * "
                    v-model="details.address.line1"
                ></v-text-field>
              </ValidationProvider>
            </div>
            <div class="col-md-6">
              <ValidationProvider name="postal code" rules="required" v-slot="{ errors }">
                <v-text-field
                    :disabled="is_disabled"
                    outlined
                    :error-messages="errors[0]"
                    label="Postal Code *"
                    placeholder="Postal Code * "
                    v-model="details.address.postal_code"
                >
                </v-text-field>
              </ValidationProvider>
            </div>

            <div class="col-md-6">
              <ValidationProvider name="country" rules="required" v-slot="{ errors }">
                <v-select
                    :disabled="is_disabled"
                    v-model="details.country"
                    :items="countries"
                    item-text="name"
                    item-value="code"
                    label="Country *"
                    placeholder="Country * "
                    outlined
                    :error-messages="errors[0]"
                ></v-select>
              </ValidationProvider>
            </div>
            <div class="col-md-6">
              <ValidationProvider name="account holder type" rules="required|alpha" v-slot="{ errors }">
                <v-text-field
                    :disabled="is_disabled"
                    outlined
                    :error-messages="errors[0]"
                    label="Account Holder Type *"
                    placeholder="Account Holder Type * "
                    v-model="details.bank_account.account_holder_type"
                >
                </v-text-field>
              </ValidationProvider>
            </div>

            <div class="col-md-6">
              <ValidationProvider name="transit number" rules="required|numeric" v-slot="{ errors }">
                <v-text-field
                    :disabled="is_disabled"
                    outlined
                    :error-messages="errors[0]"
                    label="Transit Number *"
                    placeholder="Transit Number * "
                    v-model="details.bank_account.transit_number"
                ></v-text-field>
              </ValidationProvider>
            </div>
            <div class="col-md-6">
              <ValidationProvider name="institution number" rules="required|numeric" v-slot="{ errors }">
                <v-text-field
                    :disabled="is_disabled"
                    outlined
                    :error-messages="errors[0]"
                    label="Institution Number *"
                    placeholder="Institution Number * "
                    v-model="details.bank_account.institution_number"
                >
                </v-text-field>
              </ValidationProvider>
            </div>

            <div class="col-md-6">
              <ValidationProvider name="bank account number" rules="required|numeric" v-slot="{ errors }">
                <v-text-field
                    :disabled="is_disabled"
                    outlined
                    :error-messages="errors[0]"
                    label="Bank Account Number *"
                    placeholder="Bank Account Number * "
                    v-model="details.bank_account.account_number"
                ></v-text-field>
              </ValidationProvider>
            </div>
            <div class="col-md-6">
              <ValidationProvider name="Bank account holder name" rules="required|alpha" v-slot="{ errors }">
                <v-text-field
                    :disabled="is_disabled"
                    outlined
                    :error-messages="errors[0]"
                    label="Bank Account Holder Name *"
                    placeholder="Bank Account Holder Name * "
                    v-model="details.bank_account.account_holder_name"
                >
                </v-text-field>
              </ValidationProvider>
            </div>

            <div class="col-md-6">
              <ValidationProvider name="Bank account currency" rules="required" v-slot="{ errors }">
                <v-select
                    :disabled="is_disabled"
                    v-model="details.bank_account.currency"
                    :items="currencies"
                    item-text="name"
                    item-value="code"
                    :error-messages="errors[0]"
                    label="Bank Account Currency *"
                    placeholder="Bank Account Currency * "
                    outlined
                ></v-select>
              </ValidationProvider>
            </div>
            <div class="col-md-6">
              <ValidationProvider name="Bank account country" rules="required" v-slot="{ errors }">
                <v-select
                    :disabled="is_disabled"
                    v-model="details.bank_account.country"
                    :items="countries"
                    :error-messages="errors[0]"
                    item-text="name"
                    item-value="code"
                    label="Bank Account Country *"
                    placeholder="Bank Account Country * "
                    outlined
                ></v-select>
              </ValidationProvider>
            </div>
          </div>
        </v-card-text>
      </v-card>
    </MasterInstructorLayout>
  </div>
</template>

<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";
import countryData from "../../../data/countryData";
import currencyData from "../../../data/currencyData";
import {maska} from "maska";

export default {
  name: "BankAccount",
  components: {MasterInstructorLayout},
  directives: {maska},
  data() {
    return {
      countries: countryData,
      currencies: currencyData,
      account: null,
      is_disabled: true,
      details: {
        id: null,
        accountId: null,
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
        country: "CA",
        birth_date: "",
        id_number: "",
        address: {
          city: "",
          line1: "",
          postal_code: "",
          state: ""
        },
        bank_account: {
          id: null,
          transit_number: "",
          institution_number: "",
          routing_number: "",
          account_number: "",
          account_holder_name: "",
          account_holder_type: "individual",
          country: "CA",
          currency: "CAD"
        }
      }
    }
  },

  mounted() {
    this.getAccount();
  },
  methods: {
    async getAccount() {
      try {
        const url = "/v1/drivisa/instructors/finance/accounts";
        const {data} = await axios.get(url);
        const account = data.data;

        if (account.accountId) {
          this.is_disabled = true;
        }

        this.details.id = account.id;
        this.details.accountId = account.accountId;
        this.details.birth_date = account.birthDate;
        this.details.first_name = account.firstName;
        this.details.last_name = account.lastName;
        this.details.email = account.email;
        this.details.phone = account.phone;
        this.details.country = account.country;
        this.details.id_number = account.idNumber;
        //address
        this.details.address.state = account.state;
        this.details.address.city = account.city;
        this.details.address.line1 = account.line1;
        this.details.address.postal_code = account.postalCode;
        //bank account
        this.details.bank_account.id = account.bankAccount.id;
        this.details.bank_account.country = account.bankAccount.country;
        this.details.bank_account.currency = account.bankAccount.currency;
        this.details.bank_account.transit_number = account.bankAccount.transitNumber;
        this.details.bank_account.institution_number = account.bankAccount.institutionNumber;
        this.details.bank_account.routing_number = account.bankAccount.transitNumber + "-" + account.bankAccount.institutionNumber;
        this.details.bank_account.account_number = account.bankAccount.accountNumber;
        this.details.bank_account.account_holder_name = account.bankAccount.accountHolderName;
        this.details.bank_account.account_holder_type = account.bankAccount.accountHolderType;
      } catch (e) {
        this.is_disabled = false;
      }
    },
    async saveAccount() {
      try {
        let url = "/v1/drivisa/instructors/finance/accounts";
        if (this.details.id !== null) {
          url = "/v1/drivisa/instructors/finance/accounts/update";
        }
        const {data} = await axios.post(url, this.details);

        window.location.href = data;

        this.$toasted.success("Account Saved");

      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to Save Bank Account")
      }
    },
    async deleteAccount() {
      try {
        let url = "/v1/drivisa/instructors/finance/accounts";
        await axios.delete(url);
        this.$toasted.success("Account Deleted");
        await this.getAccount();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to Save Bank Account")
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