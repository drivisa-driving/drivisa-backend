<template>
  <div class="d-container">
    <section style="margin-bottom: 5rem">
      <div class="section_title">
        <p>Find Your Instructor</p>
      </div>
      <div class="search-input">
        <input
          name="term"
          type="text"
          v-model="filter.term"
          v-on:keydown.enter="goToInstructor"
          class="ng-untouched"
          placeholder="Search for the location or instructor"
        />
        <button
          class="primary btn-outline"
          type="submit"
          @click="goToInstructor"
        >
          Search
        </button>
      </div>
      <div class="section_title">
        <p class="mt-3">Or By Map</p>
      </div>
      <div>
        <GmapMap
          :center="center"
          :zoom="15"
          style="width: 100%; height: 400px; border-radius: 10px"
          class="google_map"
        >
          <GmapMarker
            :key="index"
            v-for="(m, index) in markers"
            :position="m.position"
            :clickable="true"
            :icon="m.icon"
          >
            <gmap-info-window
              :options="{
                content: m.infoText,
                pixelOffset: {
                  width: 0,
                  height: 0,
                },
              }"
              :position="m.position"
              :opened="true"
            >
            </gmap-info-window>
          </GmapMarker>
        </GmapMap>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: "InstructorMapComponent",
  props: ["location", "instructors"],
  data() {
    return {
      center: { lat: 45.508, lng: -73.587 },
      filter: {
        term: null,
      },
    };
  },
  computed: {
    markers() {
      let mrker = [];
      this.instructors.forEach((instructor) => {
        mrker.push({
          icon: {
            path: "M12 0c-4.198 0-8 3.403-8 7.602 0 6.243 6.377 6.903 8 16.398 1.623-9.495 8-10.155 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.342-3 3-3 3 1.343 3 3-1.343 3-3 3z",
            fillColor: "#f67674",
            strokeColor: "#f67674",
            fillOpacity: 0.8,
            scale: 1.5,
          },
          position: {
            lat: instructor.sourceLatitude,
            lng: instructor.sourceLongitude,
          },
          infoText: `
             <div class="bg-white d-flex" style="max-width: 200px;max-height: 100px;border-radius: 5px">
               <div class="mr-2"><img style="width: 40px;height: 40px;border-radius: 50%" src="${instructor.instructor.avatar}" alt=""></div>
               <div>
                    <strong class="font-weight-bold">${instructor.instructor.fullName}</strong>
                    <p>
                      Not Provided
                    </p>
               </div>
            </div>
          `,
        });
      });
      return mrker;
    },
  },
  methods: {
    async goToInstructor() {
      await this.$router.push({
        name: "instructors",
        query: {
          term: this.filter.term,
          address: this.filter.term,
        },
      });
    },
  },
};
</script>

<style scoped lang="scss">
section {
  .section_title {
    text-align: center !important;
    color: rgba(0, 0, 0, 0.87);

    p {
      font-weight: 700;
      font-size: 25px;
      text-transform: capitalize;
    }
  }
}
.search-input {
  flex-direction: row;
  box-sizing: border-box;
  display: flex;
  place-content: stretch space-between;
  align-items: stretch;
  position: relative;
  border: 0.5px solid #cfcfcf;
  outline: none;
  border-radius: 6px;
  max-width: 520px;
  margin: 0 auto;
  padding: 4px !important;
  width: 100% !important;
  height: 60px !important;
  min-height: 60px !important;
  max-height: 60px !important;

  input {
    flex: 0 0 100%;
    box-sizing: border-box;
    max-width: 80%;
    min-width: 80%;
    float: left;
    width: 80%;
    outline: none;
    border: none;
    padding: 12px !important;
    overflow: visible;
    font-size: 100%;
    line-height: 1.15;
    margin: 0;
  }

  button {
    flex: 0 0 100%;
    box-sizing: border-box;
    max-width: 20%;
    min-width: 20%;
    background: #3f54d1;
    color: #fff;
    cursor: pointer;
    border: none;
    border-radius: 6px;
    outline: none;
    overflow: visible;
    font-size: 100%;
    line-height: 1.15;
    margin: 0;
  }
}
</style>