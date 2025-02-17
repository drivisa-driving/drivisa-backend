<template>
  <div>
    <div id="generic_price_table">
      <div
        class="row d-flex justify-content-start flex-wrap"
        v-if="allRequests.length > 0"
      >
        <Requests
          v-for="request in allRequests"
          :key="request.id"
          :request="request"
        />
      </div>
      <h3 v-else class="text-center">No Requests Found</h3>
    </div>
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
      allRequests: [],
    };
  },
  mounted() {
    this.getAllRequests();
  },
  methods: {
    async getAllRequests() {
      const { data } = await axios.get(
        `/v1/drivisa/admin/trainees/car-rental/${this.$route.params.id}/all-requests`
      );
      this.allRequests = data.data;
    },
  },
};
</script>