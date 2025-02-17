<template>
  <div>
    <MasterAdminLayout>
      <v-card class="border">
        <v-card-text>
          <v-dialog v-model="deleteDialog" max-width="500" persistent>
            <v-card>
              <h3 class="text-h5 text-center pt-3">Do you want to delete?</h3>
              <v-card-text class="text-center">
                <p>Once deleted training location can't be recovered back</p>
              </v-card-text>

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
                  @click="deleteLocation"
                >
                  Confirm
                </v-btn>
              </div>
            </v-card>
          </v-dialog>

          <!-- Create Training Location Dialog -->
          <v-dialog v-model="dialog" max-width="500" persistent>
            <v-card>
              <h3 class="text-h5 text-center pt-3">
                Add Road Test Training Location
              </h3>
              <div>
                <v-card-text>
                  <ValidationProvider
                    name="address "
                    rules="alpha"
                    v-slot="{ errors }"
                  >
                    <vue-google-autocomplete
                      ref="source_location"
                      id="source_location"
                      classname="form-control my-3"
                      placeholder="Training Location"
                      @placechanged="getAddressData"
                      country="CA"
                      :error-messages="errors[0]"
                      v-on:keydown.enter="saveLocation"
                    >
                    </vue-google-autocomplete>
                  </ValidationProvider>
                </v-card-text>
              </div>

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
                  @click="saveLocation"
                >
                  Add
                </v-btn>
              </div>
            </v-card>
          </v-dialog>

          <div class="page-header pr-0">
            <p class="page-heading">Road Test Training Locations</p>
            <div class="location_add_btn">
              <v-btn color="primary btn-outline" @click="dialog = true"
                >Add Location</v-btn
              >
            </div>
          </div>

          <!-- Get All Training Locations -->
          <BasicDatatable :data="data">
            <Column sortable field="sourceAddress" header="Address"></Column>
            <Column header="">
              <template #body="slotProps">
                <v-btn
                  color="red"
                  small
                  class="text-capitalize text-white"
                  @click="dialogMethod(slotProps.data.id)"
                  >Delete
                </v-btn>
              </template>
            </Column>
          </BasicDatatable>
        </v-card-text>
      </v-card>
    </MasterAdminLayout>
  </div>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import VueGoogleAutocomplete from "vue-google-autocomplete";
import BasicDatatable from "../../../components/Global/BasicDatatable.vue";
export default {
  name: "TrainingLocation",
  components: { MasterAdminLayout, VueGoogleAutocomplete, BasicDatatable },
  data() {
    return {
      data: [],
      dialog: false,
      deleteDialog: false,
      delete_id: null,
      requestData: {
        source_address: "",
        source_latitude: "",
        source_longitude: "",
      },
    };
  },
  mounted() {
    this.getLocations();
  },
  methods: {
    dialogMethod(id) {
      this.deleteDialog = true;
      this.delete_id = id;
    },
    closeDialogMethod() {
      this.deleteDialog = false;
      this.delete_id = null;
    },
    clearForm() {
      this.requestData = {
        source_address: "",
        source_latitude: "",
        source_longitude: "",
      };
      this.dialog = false;
    },
    getAddressData(addressData, placeResultData) {
      this.requestData.source_latitude = addressData.latitude;
      this.requestData.source_longitude = addressData.longitude;
      this.requestData.source_address = placeResultData.formatted_address;
    },
    async getLocations() {
      try {
        const { data } = await axios.get("/v1/drivisa/training-location");
        this.data = data;
        this.data = data.data;
      } catch (e) {}
    },
    async saveLocation() {
      try {
        let url = "/v1/drivisa/admin/training-location";
        let method = axios.post;
        const { data } = await method(url, this.requestData);
        this.$toasted.success(data.message);
        this.getLocations();
        this.clearForm();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to Add Training Location");
      }
    },
    async deleteLocation() {
      try {
        let url = `/v1/drivisa/admin/training-location/${this.delete_id}`;
        const { data } = await axios.delete(url);
        this.$toasted.success(data.message);
        this.getLocations();
        this.closeDialogMethod();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to delete Training Location");
      }
    },
  },
};
</script>

<style>
.location_add_btn {
  text-align: right;
}

@media (max-width: 960px) {
  .location_add_btn {
    text-align: left !important;
  }
}
</style>