<template>
  <div>
    <MasterAdminLayout>
      <v-card>
        <v-card-text>
          <div class="page-header pr-0">
            <p class="page-heading">Package Types</p>
            <div class="admin_add_btn">
              <v-btn
                color="primary"
                class="text-capitalize btn-outline"
                @click="dialog = true"
                >Add Package Type
              </v-btn>
            </div>
          </div>

          <!-- Delete  Package Type Dialog -->
          <v-dialog v-model="deleteDialog" max-width="500" persistent>
            <v-card>
              <h3 class="text-h5 text-center pt-3">
                Do you want to delete Package Type?
              </h3>
              <!--  Package Type Delete -->

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

          <!-- Create Package Type Dialog -->

          <v-dialog v-model="dialog" max-width="500" persistent>
            <v-card>
              <h3 class="text-h5 text-center pt-3">
                {{ packageType.id ? "Edit" : "Create A New" }} Package Type
              </h3>
              <!-- Package Type Create Form -->
              <v-card-text class="pb-0">
                <ValidationProvider
                  name="package type name"
                  rules="required"
                  v-slot="{ errors }"
                >
                  <v-text-field
                    label="Package Type Name *"
                    placeholder="Name"
                    outlined
                    :error-messages="errors[0]"
                    v-model="packageType.name"
                    v-on:keydown.enter="createPackageType"
                  ></v-text-field>
                </ValidationProvider>
              </v-card-text>
              <div class="check_box">
                <div class="box1 my-6">
                  <v-card-title class="p-0">Fees</v-card-title>
                  <v-checkbox
                    v-model="packageType.instructor"
                    :true-value="1"
                    label="Instructor"
                    color="indigo"
                    hide-details
                  >
                  </v-checkbox>
                  <v-checkbox
                    v-model="packageType.drivisa"
                    :true-value="1"
                    label="Drivisa"
                    color="indigo"
                    hide-details
                  >
                  </v-checkbox>
                  <v-checkbox
                    v-model="packageType.pdio"
                    :true-value="1"
                    label="PDIO"
                    color="indigo"
                    hide-details
                  >
                  </v-checkbox>
                  <v-checkbox
                    v-model="packageType.mto"
                    :true-value="1"
                    label="MTO"
                    color="indigo"
                    hide-details
                  >
                  </v-checkbox>
                  <v-btn
                    class="text-white text-capitalize my-2 mt-4"
                    color="red"
                    @click="clearForm"
                    >Close</v-btn
                  >
                </div>
                <div class="box2 my-6">
                  <v-card-title class="p-0">Cancellation</v-card-title>
                  <v-checkbox
                    v-model="packageType.instructor_cancel_fee"
                    :true-value="1"
                    label="Instructor"
                    color="red"
                    hide-details
                  >
                  </v-checkbox>
                  <v-checkbox
                    v-model="packageType.drivisa_cancel_fee"
                    :true-value="1"
                    label="Drivisa"
                    color="red"
                    hide-details
                  >
                  </v-checkbox>
                  <v-checkbox
                    v-model="packageType.pdio_cancel_fee"
                    :true-value="1"
                    label="PDIO "
                    color="red"
                    hide-details
                  >
                  </v-checkbox>
                  <v-checkbox
                    v-model="packageType.mto_cancel_fee"
                    :true-value="1"
                    label="MTO"
                    color="red"
                    hide-details
                  >
                  </v-checkbox>
                  <v-btn
                    class="text-white text-capitalize my-2 mt-4"
                    color="green"
                    @click="createPackageType"
                  >
                    {{ packageType.id ? "Update" : "Create" }}
                  </v-btn>
                </div>
              </div>
            </v-card>
          </v-dialog>

          <!-- Package Type Datatable -->
          <PackageTypeDataTablePrimeVue
            ref="table"
            @edit="editPackageType"
            @delete="dialogMethod"
          />
        </v-card-text>
      </v-card>
    </MasterAdminLayout>
  </div>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import PackageTypeDataTablePrimeVue from "../../../components/Admin/Crud/PackageTypeDataTablePrimeVue";

export default {
  name: "PackageTypes",
  components: { MasterAdminLayout, PackageTypeDataTablePrimeVue },
  data() {
    return {
      dialog: false,
      deleteDialog: false,
      delete_id: null,
      packageType: {
        id: null,
        name: null,
        instructor: null,
        drivisa: null,
        pdio: null,
        mto: null,
        instructor_cancel_fee: null,
        drivisa_cancel_fee: null,
        pdio_cancel_fee: null,
        mto_cancel_fee: null,
      },
    };
  },
  methods: {
    dialogMethod(id) {
      this.deleteDialog = true;
      this.delete_id = id;
    },
    editPackageType(packageType) {
      this.packageType = {
        id: packageType.id,
        name: packageType.name,
        instructor: packageType.instructor,
        drivisa: packageType.drivisa,
        pdio: packageType.pdio,
        mto: packageType.mto,
        instructor_cancel_fee: packageType.instructor_cancel_fee,
        drivisa_cancel_fee: packageType.drivisa_cancel_fee,
        pdio_cancel_fee: packageType.pdio_cancel_fee,
        mto_cancel_fee: packageType.mto_cancel_fee,
      };
      this.dialog = true;
    },
    updateTable() {
      this.$refs.table.getAllPackageType();
    },
    clearForm() {
      this.packageType = {
        id: null,
        name: null,
        instructor: null,
        drivisa: null,
        pdio: null,
        mto: null,
        instructor_cancel_fee: null,
        drivisa_cancel_fee: null,
        pdio_cancel_fee: null,
        mto_cancel_fee: null,
      };
      this.dialog = false;
    },
    async createPackageType() {
      try {
        let url = "/v1/drivisa/admin/package-types";
        let method = axios.post;

        if (this.packageType.id) {
          url = "/v1/drivisa/admin/package-types/" + this.packageType.id;
          method = axios.put;
        }

        const { data } = await method(url, this.packageType);
        this.$toasted.success(data.message);
        this.updateTable();
        this.clearForm();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to save Package Type");
      }
    },
    async deleteType() {
      try {
        let url = "/v1/drivisa/admin/package-types/" + this.delete_id;
        let method = axios.delete;

        const { data } = await method(url);
        this.$toasted.success(data.message);
        this.updateTable();
        this.deleteDialog = false;
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to delete Package Type");
      }
    },
  },
};
</script>

<style scoped lang="scss">
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
