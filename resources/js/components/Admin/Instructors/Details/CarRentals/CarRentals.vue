<template>
  <div>
    <v-tabs centered>
      <v-tab> Requests </v-tab>
      <v-tab> History </v-tab>

      <v-tab-item>
        <div id="generic_price_table">
          <div
            class="row d-flex justify-content-start flex-wrap"
            v-if="availableRequests.length > 0"
          >
            <Requests
              v-for="request in availableRequests"
              :key="request.id"
              :request="request"
            />
          </div>
          <h3 v-else class="text-center">No Requests Found</h3>
        </div>
      </v-tab-item>
      <v-tab-item>
        <div id="generic_price_table">
          <div
            class="row d-flex justify-content-start flex-wrap"
            v-if="acceptedRequests.length > 0"
          >
            <Requests
              v-for="acceptedRequest in acceptedRequests"
              :key="acceptedRequest.id"
              :request="acceptedRequest"
            />
          </div>
          <h3 v-else class="text-center">No Requests Found</h3>
        </div>
      </v-tab-item>
    </v-tabs>
  </div>
</template>

<script>
import Requests from "./Requests.vue";
export default {
  name: "Rentals",
  components: {
    Requests,
  },
  data() {
    return {
      availableRequests: [],
      acceptedRequests: [],
    };
  },
  mounted() {
    this.getAvailableRequests();
    this.getAcceptedRequests();
  },
  methods: {
    async getAvailableRequests() {
      const { data } = await axios.get(
        `/v1/drivisa/admin/instructors/car-rentals/${this.$route.params.id}/available-requests`
      );
      this.availableRequests = data.data;
    },
    async getAcceptedRequests() {
      const { data } = await axios.get(
        `/v1/drivisa/admin/instructors/car-rentals/${this.$route.params.id}/accepted-requests`
      );
      this.acceptedRequests = data.data;
    },
  },
};
</script>