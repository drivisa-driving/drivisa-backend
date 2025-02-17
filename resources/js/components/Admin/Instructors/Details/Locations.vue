<template>
  <div>
    <table class="table">
      <thead>
      <tr>
        <th>Source Name</th>
        <th>Source Address</th>
      </tr>
      </thead>
      <tr v-for="point in points">
        <td>{{ point.sourceName }}</td>
        <td>{{ point.sourceAddress }}</td>
      </tr>
    </table>


  </div>
</template>

<script>


export default {
  name: "Locations",
  data() {
    return {
      points: [],
    };
  },

  mounted() {
    this.getLocations();
  },
  methods: {
    async getLocations(selectInfo) {

          try {
            const url = `/v1/drivisa/admin/instructors/instructor-points/${this.$route.params.id}`;
            const {data} = await axios.get(url);
            this.points = data.data;
          } catch (e) {
            console.log(e)
          }
    },
  },
};
</script>
