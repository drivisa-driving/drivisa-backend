<template>
  <div>
    <v-card elevation="0" class="border">
      <div class="px-3 my-3">
        <h3 class="font-weight-bold text-dark mb-4"> Add Training Location </h3>
      </div>
      <v-card-text>
        <div class="container">
          <div class="row">
            <div class="col-md-12">
              <ValidationProvider name="Training Point Identical Name" rules="required" v-slot="{ errors }">
                <v-text-field
                    v-model="requestData.source_name"
                    outlined
                    :error-messages="errors[0]"
                    label="Training Point Identical Name *"
                    required
                ></v-text-field>
              </ValidationProvider>
            </div>

            <div class="col-md-12">
              <vue-google-autocomplete
                  ref="source_location"
                  id="source_location"
                  classname="form-control mb-3"
                  style="height: 56px"
                  placeholder="Training Point Address"
                  @placechanged="getAddressData"
                  country="CA"
              >
              </vue-google-autocomplete>
            </div>

            <div class="col-md-12">
              <div class="d-flex align-items-center justify-content-start">
                <v-btn
                    class="primary"
                    :disabled="requestData.source_name === ''"
                    @click="saveLocation(); $emit('locationContinue')">
                  Save Changes
                </v-btn>
              </div>
            </div>
          </div>
        </div>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
import VueGoogleAutocomplete from 'vue-google-autocomplete'

export default {
  name: "LocationAddComponent",
  components: {VueGoogleAutocomplete},
  data() {
    return {
      requestData: {
        source_name: "",
        source_address: "",
        source_latitude: "",
        source_longitude: "",
        destination_latitude: "",
        destination_longitude: "",
      }
    }
  },
  methods: {
    getAddressData(addressData, placeResultData, id) {
      this.requestData.source_latitude = addressData.latitude;
      this.requestData.source_longitude = addressData.longitude;
      this.requestData.source_address = placeResultData.formatted_address;

      // destination address duplicate for now || as per discussion with uday patel
      this.requestData.destination_latitude = addressData.latitude;
      this.requestData.destination_longitude = addressData.longitude;
      this.requestData.destination_address = placeResultData.formatted_address;
    },

    async saveLocation() {
      try {
        const url = "/v1/drivisa/instructors/points/set-point";
        const {data} = await axios.post(url, this.requestData);
        this.$toasted.success(data.message)
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to Save Location")
      }
    }
  }
}
</script>

<style scoped lang="scss">
.location-info {
  display: flex;
  padding-left: 20px;
  padding-top: 10px;
  padding-bottom: 30px;

  .dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
  }
}
</style>