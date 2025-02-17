<template>
  <div>
    <MasterInstructorLayout>
      <v-card elevation="0" class="border">
        <v-card-text>
          <h3 class="font-weight-bold text-dark"> Edit Training Location </h3>
          <div class="container">
            <div class="row">
              <div class="col-md-12">
                <v-text-field
                    v-model="requestData.source_name"
                    outlined
                    label="Training Point Identical Name *"
                    required
                ></v-text-field>
              </div>
              <div class="col-md-12">
                <vue-google-autocomplete
                    ref="source_location"
                    id="source_location"
                    classname="form-control mb-3"
                    placeholder="Training Point Address"
                    style="height: 56px"
                    @placechanged="getAddressData"
                    v-model="requestData.source_address"
                    country="CA"
                >
                </vue-google-autocomplete>

              </div>
            </div>
            <div class="row">
              <div class="d-flex align-items-center justify-content-start">
                <v-btn class="primary" :disabled="requestData.source_name === ''" @click="saveLocation">
                  Save Changes
                </v-btn>
              </div>
            </div>
          </div>
        </v-card-text>
      </v-card>
    </MasterInstructorLayout>
  </div>
</template>

<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";
import AddLocationMap from "../../../components/Instructor/Map/AddLocationMap";
import VueGoogleAutocomplete from 'vue-google-autocomplete'

export default {
  name: "TrainingLocationEdit",
  components: {MasterInstructorLayout, AddLocationMap, VueGoogleAutocomplete},
  data() {
    return {
      requestData: {
        source_name: "",
        source_address: "",
        source_latitude: "",
        source_longitude: "",
        destination_name: "",
        destination_address: "",
        destination_latitude: "",
        destination_longitude: "",
      }
    }
  },
  mounted() {
    this.getLocation();
  },
  methods: {
    getAddressData(addressData, placeResultData, id) {
      this.requestData.source_latitude = addressData.latitude;
      this.requestData.source_longitude = addressData.longitude;
      this.requestData.source_address = placeResultData.formatted_address;
    },
    getAddressData1(addressData, placeResultData, id) {
      this.requestData.destination_latitude = addressData.latitude;
      this.requestData.destination_longitude = addressData.longitude;
      this.requestData.destination_address = placeResultData.formatted_address;
    },
    async getLocation() {
      try {
        let id = this.$route.params.id;
        const url = "/v1/drivisa/instructors/points/" + id;
        const {data} = await axios.get(url);

        let location = data.data;

        this.requestData = {
          source_name: location.sourceName,
          source_address: location.sourceAddress,
          source_latitude: location.sourceLatitude,
          source_longitude: location.sourceLongitude,
          destination_name: location.destinationName,
          destination_address: location.destinationAddress,
          destination_latitude: location.destinationLatitude,
          destination_longitude: location.destinationLongitude,
        }
      } catch (e) {

      }
    },
    getMarkerData($event) {
      if ($event.type === 'source') {
        this.requestData.source_latitude = $event.lat;
        this.requestData.source_longitude = $event.lng;
      } else {
        this.requestData.destination_latitude = $event.lat;
        this.requestData.destination_longitude = $event.lng;
      }
    },

    async saveLocation() {
      try {
        let id = this.$route.params.id;
        const url = "/v1/drivisa/instructors/points/" + id;
        const {data} = await axios.post(url, this.requestData);
        this.$toasted.success(data.message)
        await this.$router.push("/instructor/training-locations");
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
  padding-top: 30px;
  padding-bottom: 30px;

  .dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
  }
}
</style>