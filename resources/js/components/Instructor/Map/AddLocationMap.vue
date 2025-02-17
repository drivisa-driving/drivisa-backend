<template>
  <div>
    <GmapMap
        :center='center'
        :zoom='14'
        style='width:100%;  height: 400px;'>
      <GmapMarker
          :draggable="true"
          :key="index"
          v-for="(m, index) in markers"
          :position="m.position"
          :clickable="true"
          @dragend="dragEnd($event, m.type)"
          @click="center=m.position"
          :icon="m.icon"
      />
    </GmapMap>
  </div>
</template>

<script>
export default {
  name: "AddLocationMap",
  props: {
    location: {
      type: Object,
      required: false
    }
  },
  data() {
    return {
      center: {lat: 45.508, lng: -73.587},
      markers: [
        {
          type: "source",
          icon: {
            path:
                "M12 0c-4.198 0-8 3.403-8 7.602 0 6.243 6.377 6.903 8 16.398 1.623-9.495 8-10.155 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.342-3 3-3 3 1.343 3 3-1.343 3-3 3z",
            fillColor: '#f67674',
            strokeColor: '#f67674',
            fillOpacity: 0.8,
            scale: 1.5,
          },
          position: {
            lat: 45.508, lng: -73.587
          },
        },
        {
          type: "destination",
          icon: {
            path:
                "M12 0c-4.198 0-8 3.403-8 7.602 0 6.243 6.377 6.903 8 16.398 1.623-9.495 8-10.155 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.342-3 3-3 3 1.343 3 3-1.343 3-3 3z",
            fillColor: '#7776f3',
            strokeColor: '#7776f3',
            fillOpacity: 0.8,
            scale: 1.5,
          },
          position: {
            lat: (45.508 - 0.0008), lng: (-73.587 - 0.0008)
          },
        },
      ]
    }
  },
  watch: {
    location(newVal, oldVal) {
      if (newVal !== undefined) {
        this.center = {
          lat: newVal.source_latitude - 0.0008,
          lng: newVal.source_longitude - 0.0008,
        };
        this.markers = [
          {
            type: "source",
            icon: {
              path:
                  "M12 0c-4.198 0-8 3.403-8 7.602 0 6.243 6.377 6.903 8 16.398 1.623-9.495 8-10.155 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.342-3 3-3 3 1.343 3 3-1.343 3-3 3z",
              fillColor: '#f67674',
              strokeColor: '#f67674',
              fillOpacity: 0.8,
              scale: 1.5,
            },
            position: {
              lat: newVal.source_latitude - 0.0008, lng: newVal.source_longitude - 0.0008
            },
          },
          {
            type: "destination",
            icon: {
              path:
                  "M12 0c-4.198 0-8 3.403-8 7.602 0 6.243 6.377 6.903 8 16.398 1.623-9.495 8-10.155 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.342-3 3-3 3 1.343 3 3-1.343 3-3 3z",
              fillColor: '#7776f3',
              strokeColor: '#7776f3',
              fillOpacity: 0.8,
              scale: 1.5,
            },
            position: {
              lat: newVal.destination_latitude, lng: newVal.destination_longitude
            },
          },
        ]
      }
    }
  },

  mounted() {
    this.geoLocator();
  },
  methods: {
    dragEnd(event, type) {
      let lat = event.latLng.lat().toFixed(6);
      let lng = event.latLng.lng().toFixed(6);
      this.$emit('onMarkerUpdate', {
        type: type,
        lat: lat,
        lng: lng,
      })
    },
    geoLocator() {
      navigator.geolocation.getCurrentPosition(position => {
            this.center = {
              lat: position.coords.latitude,
              lng: position.coords.longitude,
            };
            this.markers = [
              {
                type: "source",
                icon: {
                  path:
                      "M12 0c-4.198 0-8 3.403-8 7.602 0 6.243 6.377 6.903 8 16.398 1.623-9.495 8-10.155 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.342-3 3-3 3 1.343 3 3-1.343 3-3 3z",
                  fillColor: '#f67674',
                  strokeColor: '#f67674',
                  fillOpacity: 0.8,
                  scale: 1.5,
                },
                position: {
                  lat: position.coords.latitude - 0.0008, lng: position.coords.longitude - 0.0008
                },
              },
              {
                type: "destination",
                icon: {
                  path:
                      "M12 0c-4.198 0-8 3.403-8 7.602 0 6.243 6.377 6.903 8 16.398 1.623-9.495 8-10.155 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.342-3 3-3 3 1.343 3 3-1.343 3-3 3z",
                  fillColor: '#7776f3',
                  strokeColor: '#7776f3',
                  fillOpacity: 0.8,
                  scale: 1.5,
                },
                position: {
                  lat: position.coords.latitude, lng: position.coords.longitude
                },
              },
            ];

            // emit update address
            this.$emit('onMarkerUpdate', {
              type: "source",
              lat: position.coords.latitude,
              lng: position.coords.longitude
            });
            this.$emit('onMarkerUpdate', {
              type: "destination",
              lat: position.coords.latitude,
              lng: position.coords.longitude
            });
          }
      );
    },
  },
}
</script>

<style>
.vue-map {
  border-radius: 0 !important;
}
</style>