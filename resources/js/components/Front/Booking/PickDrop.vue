<template>
  <v-row justify="start">
    <v-dialog
        v-model="dialog"
        persistent
        max-width="500px"
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn
            color="primary"
            dark
            class="my-6"
            v-bind="attrs"
            v-on="on"
        >
          Select pick & drop
        </v-btn>
      </template>
      <v-card>
        <v-card-text>
          <v-container class="px-3">
            <h4 class="text-center">Pick And Drop Facility</h4>
            <div class="form-group">
              <label class="font-weight-bold">Pick Location</label>
              <vue-google-autocomplete
                  ref="pick-address"
                  id="pick-map"
                  classname="form-control"
                  placeholder="Start typing"
                  @placechanged="getAddressData"
                  country="CA"
              >
              </vue-google-autocomplete>
            </div>
            <div class="form-group">
              <v-checkbox
                  v-model="isSameAsPick"
                  label="Drop Address Same As Pick Address"
                  hide-details
              ></v-checkbox>
            </div>
            <div class="form-group">
              <label class="font-weight-bold">Drop Location</label>
              <vue-google-autocomplete
                  ref="drop-address"
                  id="drop-map"
                  v-model="dropAddress"
                  :readonly="isSameAsPick"
                  classname="form-control"
                  placeholder="Start typing"
                  @placechanged="getAddressData"
                  country="CA"
              >
              </vue-google-autocomplete>
            </div>

            <div class="col-12 p-0" v-if="hasAdditionalCost && this.additionalCost">
              <ul class="list-group p-0">
                <li class="list-group-item"><strong>Total Extra KM:</strong>
                  <span class="float-right">
                    {{ (parseFloat(additionalCost.totalDistance) - parseFloat(additionalCost.free_km)).toFixed(2) }}
                  </span></li>
                <li class="list-group-item" v-if="!road_test">
                  <strong>Lesson Tax:</strong>
                  <span class="float-right">${{ additionalCost.lesson_cost.costs.tax }}</span>
                </li>
                <li class="list-group-item" v-else>
                  <strong>Booking Fee + HST:</strong>
                  <span class="float-right">${{ price }}</span>
                </li>
                <li class="list-group-item">
                  <strong>Tax:</strong>
                  <span class="float-right">${{ parseFloat(additionalCost.tax).toFixed(2) }}</span>
                </li>
                <li class="list-group-item">
                  <strong>Additional Cost:
                    <i
                        class="mdi mdi-information-outline"
                        @click="$refs.dialogComponent.openDialog()"
                    ></i>
                  </strong>
                  <span class="float-right">${{ parseFloat(additionalCost.cost).toFixed(2) }}</span>
                </li>
                <li class="list-group-item" v-if="!road_test">
                  <strong>Lesson Cost:</strong>
                  <span class="float-right">${{ additionalCost.lesson_cost.costs.cost }}</span>
                </li>
                <li
                    class="list-group-item text-white" style="background: #3f51b5">
                  <strong>Total:</strong>
                  <span class="float-right">${{ total.toFixed(2) }}</span></li>
              </ul>

            </div>

          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
              color="red"
              class="text-white"
              small
              @click="dialog = false"
          >
            Cancel
          </v-btn>
          <v-btn
              color="success"
              small
              class="text-white"
              :disabled="!pickAddress"
              @click="calculateCost"
          >
            Calculate Cost
          </v-btn>
          <v-btn
              color="primary"
              dark
              small
              @click="dialog = false; $emit('ok_btn', additionalCost, pickPoint, dropPoint, isSameAsPick)"
          >
            Ok
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <AdditionalKMPolicyDialogComponent ref="dialogComponent"/>
  </v-row>
</template>

<script>
import VueGoogleAutocomplete from 'vue-google-autocomplete'
import AdditionalKMPolicyDialogComponent from "../../../components/Front/Booking/AdditionalKMPolicyDialogComponent";

export default {
  name: "PickDrop",
  components: {
    VueGoogleAutocomplete,
    AdditionalKMPolicyDialogComponent
  },
  props: {
    instructor_id: {
      type: Number,
      required: true,
    },
    road_test: {
      type: Boolean,
      default: false
    },
    price: {
      type: Number,
      default: 0
    }
  },
  mounted() {
    if (!("geolocation" in navigator)) {
      return;
    }
    navigator.geolocation.getCurrentPosition(pos => {
      this.location = pos;
    }, err => {
    })
  },
  computed: {
    total() {
      let cost = 0;
      if (this.additionalCost) {
        cost = parseFloat(this.additionalCost.cost)
            + parseFloat(this.additionalCost.tax);

        if (!this.road_test) {
          cost += parseFloat(this.additionalCost.lesson_cost.costs.cost)
              + parseFloat(this.additionalCost.lesson_cost.costs.tax);
        } else {
          cost += parseFloat(this.price)
        }
      }

      return cost;
    }
  },
  watch: {
    isSameAsPick(newVal) {
      if (newVal) {
        this.dropAddress = this.pickAddress;
      } else {
        this.dropAddress = "";
      }
    }
  },
  methods: {
    getAddressData(addressData, placeResultData, id) {
      this.pickAddress = placeResultData.formatted_address;

      if (id === 'pick-map') {
        this.pickPoint = {
          type: "pick",
          lat: addressData.latitude,
          lng: addressData.longitude,
          address: placeResultData.formatted_address,
        }
      } else {
        this.dropPoint = {
          type: "drop",
          lat: addressData.latitude,
          lng: addressData.longitude,
          address: placeResultData.formatted_address,
        }
      }

      this.$emit('onAddressChange', {
        type: id === 'pick-map' ? "pick" : "drop",
        lat: addressData.latitude,
        lng: addressData.longitude,
        address: placeResultData.formatted_address,
      })
    },
    async calculateCost() {
      try {
        let pickDrop = {
          instructor_id: this.instructor_id,
          lesson_id: this.$route.query.lesson,
          type: this.isSameAsPick || this.dropPoint.lat !== "" ? "pick-drop" : 'pick',
          drop_lat: this.isSameAsPick ? this.pickPoint.lat : this.dropPoint.lat,
          drop_long: this.isSameAsPick ? this.pickPoint.lng : this.dropPoint.lng,
          pick_lat: this.pickPoint.lat,
          pick_long: this.pickPoint.lng,
        }
        const {data} = await axios.post("/v1/drivisa/trainees/get-additional-price", pickDrop)
        this.hasAdditionalCost = true;
        this.additionalCost = data
      } catch (e) {
      }
    }
  },
  data() {
    return {
      location: null,
      dialog: false,
      isSameAsPick: false,
      pickAddress: "",
      dropAddress: "",
      pickPoint: {
        type: "pick",
        lat: "",
        lng: "",
        address: "",
      },
      dropPoint: {
        type: "drop",
        lat: "",
        lng: "",
        address: "",
      },
      hasAdditionalCost: false,
      additionalCost: null
    }
  }
}
</script>

<style scoped>

</style>