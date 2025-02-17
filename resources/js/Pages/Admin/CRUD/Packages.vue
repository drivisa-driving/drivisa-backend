<template>
  <div>
    <MasterAdminLayout>
      <v-card>
        <v-card-text>
          <div class="page-header pr-0">
            <p class="page-heading">Packages</p>
            <div class="admin_add_btn">
              <v-btn
                color="primary"
                class="text-capitalize btn-outline"
                @click="dialog = true"
                >Add Package
              </v-btn>
            </div>
          </div>

          <!-- Delete  Package Dialog -->
          <v-dialog v-model="deleteDialog" max-width="500" persistent>
            <v-card>
              <h3 class="text-h5 text-center pt-3">
                Do you want to delete Package?
              </h3>
              <!--  Package Delete -->

              <div class="d-flex justify-content-around px-2 py-3">
                <v-btn
                  class="text-white text-capitalize"
                  color="red"
                  text
                  @click="
                    deleteDialog = false;
                    delete_id = null;
                  "
                >
                  Cancel
                </v-btn>

                <v-btn
                  class="text-white text-capitalize"
                  color="red"
                  @click="deleteType"
                >
                  Confirm
                </v-btn>
              </div>
            </v-card>
          </v-dialog>

          <!-- Create Package Dialog -->

          <v-dialog v-model="dialog" persistent max-width="700px">
            <v-card>
              <h3 class="text-h5 text-center pt-3">
                {{ package.id ? "Edit" : "Create A New" }} Package
              </h3>
              <!-- Package Create Form -->
              <v-card-text>
                <ValidationProvider
                  name="package name"
                  rules="required"
                  v-slot="{ errors }"
                >
                  <v-text-field
                    label="Package Name *"
                    placeholder="Package Name"
                    outlined
                    v-model="package.name"
                    v-on:keydown.enter="createPackage"
                    :error-messages="errors[0]"
                  ></v-text-field>
                </ValidationProvider>
                <v-select
                  outlined
                  v-model="package.package_type_id"
                  :items="packageTypes"
                  item-text="name"
                  item-value="id"
                  label="Package Type"
                ></v-select>
                <v-row>
                  <v-col cols="12">
                    <v-row
                      v-if="package.package_type_id"
                      style="padding-bottom: 28px"
                    >
                      <v-col
                        cols="12"
                        sm="6"
                        v-if="selectedPackageType.instructor"
                      >
                        <ValidationProvider
                          name="instructor fee"
                          rules="required"
                          v-slot="{ errors }"
                        >
                          <v-text-field
                            label="Instructor Fee *"
                            placeholder="Instructor Fee"
                            outlined
                            v-on:keypress="NumbersOnly"
                            v-model="package.instructor"
                            :error-messages="errors[0]"
                            v-on:keydown.enter="createPackage"
                          ></v-text-field>
                        </ValidationProvider>
                      </v-col>
                      <v-col
                        cols="12"
                        sm="6"
                        v-if="selectedPackageType.drivisa"
                      >
                        <ValidationProvider
                          name="drivisa fee"
                          rules="required"
                          v-slot="{ errors }"
                        >
                          <v-text-field
                            label="Drivisa Fee*"
                            placeholder="Drivisa Fee"
                            outlined
                            v-on:keypress="NumbersOnly"
                            v-model="package.drivisa"
                            :error-messages="errors[0]"
                            v-on:keydown.enter="createPackage"
                          ></v-text-field>
                        </ValidationProvider>
                      </v-col>
                      <v-col cols="12" sm="6" v-if="selectedPackageType.pdio">
                        <ValidationProvider
                          name="pdio fee"
                          rules="required"
                          v-slot="{ errors }"
                        >
                          <v-text-field
                            label="PDIO Fee *"
                            placeholder="PDIO Fee"
                            outlined
                            v-on:keypress="NumbersOnly"
                            v-model="package.pdio"
                            :error-messages="errors[0]"
                            v-on:keydown.enter="createPackage"
                          ></v-text-field>
                        </ValidationProvider>
                      </v-col>
                      <v-col cols="12" sm="6" v-if="selectedPackageType.mto">
                        <ValidationProvider
                          name="mto fee"
                          rules="required"
                          v-slot="{ errors }"
                        >
                          <v-text-field
                            label="MTO Fee *"
                            placeholder="MTO Fee"
                            outlined
                            v-on:keypress="NumbersOnly"
                            :error-messages="errors[0]"
                            v-model="package.mto"
                            v-on:keydown.enter="createPackage"
                          ></v-text-field>
                        </ValidationProvider>
                      </v-col>
                      <v-col
                        cols="12"
                        sm="6"
                        v-if="selectedPackageType.instructor_cancel_fee"
                      >
                        <ValidationProvider
                          name="instructor cancel fee "
                          rules="required"
                          v-slot="{ errors }"
                        >
                          <v-text-field
                            label="Instructor Cancel Fee *"
                            placeholder="Instructor Cancel Fee "
                            outlined
                            v-on:keypress="NumbersOnly"
                            :error-messages="errors[0]"
                            v-model="package.instructor_cancel_fee"
                            v-on:keydown.enter="createPackage"
                          ></v-text-field>
                        </ValidationProvider>
                      </v-col>
                      <v-col
                        cols="12"
                        sm="6"
                        v-if="selectedPackageType.drivisa_cancel_fee"
                      >
                        <ValidationProvider
                          name="drivisa cancel fee "
                          rules="required"
                          v-slot="{ errors }"
                        >
                          <v-text-field
                            label="Drivisa Cancel Fee *"
                            placeholder="Drivisa Cancel Fee "
                            outlined
                            v-on:keypress="NumbersOnly"
                            :error-messages="errors[0]"
                            v-model="package.drivisa_cancel_fee"
                            v-on:keydown.enter="createPackage"
                          ></v-text-field>
                        </ValidationProvider>
                      </v-col>
                      <v-col
                        cols="12"
                        sm="6"
                        v-if="selectedPackageType.pdio_cancel_fee"
                      >
                        <ValidationProvider
                          name="pdio cancel fee"
                          rules="required"
                          v-slot="{ errors }"
                        >
                          <v-text-field
                            label="PDIO Cancel Fee *"
                            placeholder="PDIO Cancel Fee"
                            outlined
                            v-on:keypress="NumbersOnly"
                            :error-messages="errors[0]"
                            v-model="package.pdio_cancel_fee"
                            v-on:keydown.enter="createPackage"
                          ></v-text-field>
                        </ValidationProvider>
                      </v-col>
                      <v-col
                        cols="12"
                        sm="6"
                        v-if="selectedPackageType.mto_cancel_fee"
                      >
                        <ValidationProvider
                          name="mto fancel fee "
                          rules="required"
                          v-slot="{ errors }"
                        >
                          <v-text-field
                            label="MTO Cancel Fee *"
                            placeholder="MTO Cancel Fee "
                            outlined
                            v-on:keypress="NumbersOnly"
                            :error-messages="errors[0]"
                            v-model="package.mto_cancel_fee"
                            v-on:keydown.enter="createPackage"
                          ></v-text-field>
                        </ValidationProvider>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
                <ValidationProvider
                  name="hours"
                  rules="double:2"
                  v-slot="{ errors }"
                >
                  <v-text-field
                    label="Hours *"
                    placeholder="Hours"
                    outlined
                    v-on:keypress="NumbersOnly"
                    :error-messages="errors[0]"
                    v-model="package.hours"
                    v-on:keydown.enter="createPackage"
                  ></v-text-field>
                </ValidationProvider>
                <ValidationProvider
                  name="hours charges"
                  rules="double:2"
                  v-slot="{ errors }"
                >
                  <v-text-field
                    label="Hours Charges*"
                    placeholder="Hours Charges"
                    outlined
                    v-on:keypress="NumbersOnly"
                    :error-messages="errors[0]"
                    v-model="package.hour_charge"
                    v-on:keydown.enter="createPackage"
                  ></v-text-field>
                </ValidationProvider>
                <ValidationProvider
                  name="amount"
                  rules="required"
                  v-slot="{ errors }"
                >
                  <v-text-field
                    label="Amount *"
                    placeholder="Amount"
                    outlined
                    v-on:keypress="NumbersOnly"
                    :error-messages="errors[0]"
                    v-model="package.amount"
                    v-on:keydown.enter="createPackage"
                  ></v-text-field>
                </ValidationProvider>
                <ValidationProvider
                  name="discount"
                  rules="required"
                  v-slot="{ errors }"
                >
                  <v-text-field
                    label="Discount *"
                    placeholder="Discount"
                    outlined
                    v-on:keypress="NumbersOnly"
                    v-model="package.discount_price"
                    :error-messages="errors[0]"
                    v-on:keydown.enter="createPackage"
                  ></v-text-field>
                </ValidationProvider>

                <v-textarea
                  outlined
                  label="Additional Information"
                  v-model="package.additional_information"
                ></v-textarea>
              </v-card-text>

              <div class="d-flex justify-content-around px-2 py-3">
                <v-btn
                  class="text-white text-capitalize"
                  color="red"
                  @click="clearForm"
                >
                  Close
                </v-btn>

                <v-btn
                  class="text-white text-capitalize"
                  color="green"
                  @click="createPackage"
                >
                  {{ package.id ? "Update" : "Create" }}
                </v-btn>
              </div>
            </v-card>
          </v-dialog>

          <!-- Package Datatable -->
          <PackageDataTablePrimeVue
            ref="table"
            @edit="editPackage"
            @delete="dialogMethod"
          />
        </v-card-text>
      </v-card>
    </MasterAdminLayout>
  </div>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import PackageDataTablePrimeVue from "../../../components/Admin/Crud/PackageDataTablePrimeVue";

export default {
  name: "Package",
  components: { MasterAdminLayout, PackageDataTablePrimeVue },
  data() {
    return {
      dialog: false,
      deleteDialog: false,
      delete_id: null,
      packageTypes: [],
      package: {
        id: null,
        name: null,
        package_type_id: null,
        instructor: null,
        drivisa: null,
        pdio: null,
        mto: null,
        instructor_cancel_fee: null,
        drivisa_cancel_fee: null,
        pdio_cancel_fee: null,
        mto_cancel_fee: null,
        hours: null,
        hour_charge: null,
        amount: null,
        discount_price: null,
        additional_information: null,
      },
    };
  },
  mounted() {
    this.getPackageTypes();
  },
  computed: {
    selectedPackageType() {
      return this.packageTypes.find(
        (type) => type.id === this.package.package_type_id
      );
    },
    amount() {
      let instructorAmount = 0;
      let drivisaAmount = 0;
      let pdioAmount = 0;
      let mtoAmount = 0;

      if (this.package.instructor != "" && this.package.instructor != null) {
        instructorAmount = parseInt(this.package.instructor);
      }

      if (this.package.drivisa != "" && this.package.drivisa != null) {
        drivisaAmount = parseInt(this.package.drivisa);
      }

      if (this.package.pdio != "" && this.package.pdio != null) {
        pdioAmount = parseInt(this.package.pdio);
      }

      if (this.package.mto != "" && this.package.mto != null) {
      }

      return instructorAmount + drivisaAmount + pdioAmount + mtoAmount;
    },
    discount() {
      let discount = 0;
      if (
        this.package.discount_price != "" &&
        this.package.discount_price != null
      ) {
        discount = parseInt(this.package.discount_price);
      }
      discount = this.package.amount - discount;
    },
    hour_charge() {
      let totalAmount = 0;
      let totalHour = 0;

      if (this.package.amount != "" && this.package.amount != null) {
        totalAmount = parseInt(this.package.amount);
      }

      if (this.package.hours != "" && this.package.hours != null) {
        totalHour = parseInt(this.package.hours);
      }

      return (totalAmount / totalHour).toFixed(2);
    },
  },
  watch: {
    amount(newValue) {
      this.package.amount = newValue;
    },
    hour_charge(newValue) {
      this.package.hour_charge = newValue;
    },
  },
  methods: {
    dialogMethod(id) {
      this.deleteDialog = true;
      this.delete_id = id;
    },
    NumbersOnly(evt) {
      evt = evt ? evt : window.event;
      var charCode = evt.which ? evt.which : evt.keyCode;
      if (
        charCode > 31 &&
        (charCode < 48 || charCode > 57) &&
        charCode !== 46
      ) {
        evt.preventDefault();
      } else {
        return true;
      }
    },
    editPackage(singlePackage) {
      this.package = {
        id: singlePackage.id,
        name: singlePackage.name,
        package_type_id: singlePackage.packageType.id,
        instructor: singlePackage.packageData.instructor,
        drivisa: singlePackage.packageData.drivisa,
        pdio: singlePackage.packageData.pdio,
        mto: singlePackage.packageData.mto,
        instructor_cancel_fee: singlePackage.packageData.instructor_cancel_fee,
        drivisa_cancel_fee: singlePackage.packageData.drivisa_cancel_fee,
        pdio_cancel_fee: singlePackage.packageData.pdio_cancel_fee,
        mto_cancel_fee: singlePackage.packageData.mto_cancel_fee,
        hours: singlePackage.packageData.hours,
        hour_charge: singlePackage.packageData.hour_charge,
        amount: singlePackage.packageData.amount,
        discount_price: singlePackage.packageData.discount_price,
        additional_information:
          singlePackage.packageData.additional_information,
      };
      this.dialog = true;
    },
    updateTable() {
      this.$refs.table.getAllPackage();
    },
    clearForm() {
      this.package = {
        id: null,
        name: null,
        package_type_id: null,
        instructor: null,
        drivisa: null,
        pdio: null,
        mto: null,
        instructor_cancel_fee: null,
        drivisa_cancel_fee: null,
        pdio_cancel_fee: null,
        mto_cancel_fee: null,
        hours: null,
        hour_charge: null,
        amount: null,
        discount_price: null,
        additional_information: null,
      };
      this.dialog = false;
    },
    async getPackageTypes() {
      try {
        const { data } = await axios.get("/v1/drivisa/admin/all-package-types");
        this.packageTypes = data.data;
      } catch (e) {}
    },
    async createPackage() {
      try {
        let url = "/v1/drivisa/admin/packages";
        let method = axios.post;

        if (this.package.id) {
          url = "/v1/drivisa/admin/packages/" + this.package.id;
          method = axios.put;
        }

        const { data } = await method(url, this.package);
        this.$toasted.success(data.message);
        this.updateTable();
        this.clearForm();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to save Package");
      }
    },
    async deleteType() {
      try {
        let url = "/v1/drivisa/admin/packages/" + this.delete_id;
        let method = axios.delete;

        const { data } = await method(url);
        this.$toasted.success(data.message);
        this.updateTable();
        this.deleteDialog = false;
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to delete Package");
      }
    },
  },
};
</script>

<style>
.admin_add_btn {
  text-align: right;
}

@media (max-width: 960px) {
  .admin_add_btn {
    text-align: left !important;
  }
}

.v-data-footer {
  padding: 0 !important;
}
</style>
