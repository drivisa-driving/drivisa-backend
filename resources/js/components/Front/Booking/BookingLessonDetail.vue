<template>
  <div class="col-lg-12 booking-details" v-if="lesson">
    <h2 class="booking-title">Booking Details</h2>
    <div class="location-info">
      <div style="width:250px">
        <span class="dot blue"></span>&nbsp;Instructor Training Address
      </div>
    </div>
    <div class="location-info mt-3" v-if="pick_drop.type != 'default'">
      <div style="width:250px">
        <span class="dot" style="background-color:  #8de30b"></span>&nbsp; Pick location
      </div>
      <div>
        <span class="dot " style="background-color: #e0921d"></span>&nbsp; Drop location
      </div>
    </div>
    <div class="location-pointer">
      <div>
        <i class="mdi mdi-map-marker blue--text" style="font-size: 20px"></i>&nbsp; {{ lesson.point.sourceAddress }}
      </div>
      <div>
        <i class="mdi mdi-clock" style="font-size: 20px"></i>&nbsp; {{ lesson.openAt | time }} -
        {{ lesson.closeAt | time }}
      </div>
      <div v-if="pick_drop.type != 'default'">
        <span>Location</span>
        <div>
          <i class="mdi mdi-map-marker" style="font-size: 20px;color:   #8de30b "></i>
          <span>{{ pick_drop.pick_address }}</span>
        </div>
        <div>
          <i class="mdi mdi-map-marker" style="font-size: 20px;color:#e0921d"></i>
          <span>{{ pick_drop.drop_address }}</span>
        </div>
      </div>
      <PickDrop
          :instructor_id="instructor_id"
          @ok_btn="calculateCharge"
      />
    </div>
    <div class="map-container">
      <GmapMap
          :center='center'
          :zoom='12'
          style='width:100%;  height: 400px;'>
        <GmapMarker
            :key="index"
            v-for="(m, index) in markers"
            :position="m.position"
            :icon="m.icon"
        />
      </GmapMap>
    </div>
  </div>
</template>

<script>
import PickDrop from "./PickDrop";

export default {
  name: "BookingLessonDetail",
  components: {PickDrop},
  props: ['lesson', 'instructor_id', 'pick_drop'],
  computed: {
    center() {
      let obj = {lat: 45.508, lng: -73.587};
      if (this.lesson) {
        obj = {
          lat: this.lesson.point.sourceLatitude,
          lng: this.lesson.point.sourceLongitude
        };
      }
      return obj;
    },
    markers() {
      let arr = [
        {
          type: "source",
          icon: {
            path:
                "M12 0c-4.198 0-8 3.403-8 7.602 0 6.243 6.377 6.903 8 16.398 1.623-9.495 8-10.155 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.342-3 3-3 3 1.343 3 3-1.343 3-3 3z",
            fillColor: '#2196f3',
            strokeColor: '#070707',
            fillOpacity: 0.8,
            scale: 1.5,
          },
          position: {
            lat: 45.508, lng: -73.587
          },
        },
      ];

      if (this.lesson) {
        arr = [
          {
            type: "source",
            icon: {
              path:
                  "M12 0c-4.198 0-8 3.403-8 7.602 0 6.243 6.377 6.903 8 16.398 1.623-9.495 8-10.155 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.342-3 3-3 3 1.343 3 3-1.343 3-3 3z",
              fillColor: '#2196f3',
              strokeColor: '#070707',
              fillOpacity: 0.8,
              scale: 1.5,
            },
            position: {
              lat: this.lesson.point.sourceLatitude, lng: this.lesson.point.sourceLongitude
            },
          },
        ];
      }
      if (this.pick_drop.pick_lat != '' && this.pick_drop.pick_long) {
        arr = [
          {
            type: "source",
            icon: {
              path:
                  "M12 0c-4.198 0-8 3.403-8 7.602 0 6.243 6.377 6.903 8 16.398 1.623-9.495 8-10.155 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.342-3 3-3 3 1.343 3 3-1.343 3-3 3z",
              fillColor: '#2196f3',
              strokeColor: '#070707',
              fillOpacity: 0.8,
              scale: 1.5,
            },
            position: {
              lat: this.lesson.point.sourceLatitude, lng: this.lesson.point.sourceLongitude
            },
          },
          {
            type: "pick",
            icon: {
              path:
                  "M12 0c-4.198 0-8 3.403-8 7.602 0 6.243 6.377 6.903 8 16.398 1.623-9.495 8-10.155 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.342-3 3-3 3 1.343 3 3-1.343 3-3 3z",
              fillColor: '#e0921d',
              strokeColor: '#070707',
              fillOpacity: 0.8,
              scale: 1.5,
            },
            position: {
              lat: this.pick_drop.pick_lat, lng: this.pick_drop.pick_long
            },
          },
          {
            type: "drop",
            icon: {
              path:
                  "M12 0c-4.198 0-8 3.403-8 7.602 0 6.243 6.377 6.903 8 16.398 1.623-9.495 8-10.155 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.342-3 3-3 3 1.343 3 3-1.343 3-3 3z",
              fillColor: '#8de30b',
              strokeColor: '#070707',
              fillOpacity: 0.8,
              scale: 1.5,
            },
            position: {
              lat: this.pick_drop.drop_lat, lng: this.pick_drop.drop_long
            },
          },
        ];
      }
      return arr;
    },
  },
  methods: {
    calculateCharge(additionalCosts, pickPoint, dropPoint, isSameAsPick) {
      this.$emit('update_cost', additionalCosts);
      this.$emit('pick_drop_ok', pickPoint, dropPoint, isSameAsPick);
    }
  }
}
</script>

<style scoped lang="scss">
.booking-details {
  width: 100%;
  box-shadow: 0 20px 105.701px rgba(51, 51, 51, .1);
  padding: 1.5rem;
  background-color: rgba(51, 102, 204, .1);
  overflow-x: hidden;

  .booking-title {
    font: 500 20px/32px Montserrat, sans-serif;
    color: rgb(0, 0, 0, 0.87);
    letter-spacing: normal;
    margin: 0 0 16px;
  }

  .location-info {
    display: flex;

    .dot {
      display: inline-block;
      width: 10px;
      height: 10px;
      border-radius: 50%;
    }

  }

  .location-pointer {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .map-container {
    margin-top: 20px;
  }
}
</style>