<template>
  <v-dialog
      v-model="timedialog"
      max-width="500px"
      persistent
  >
    <v-card>
      <v-card-title class="text-h5">
        Add Time
      </v-card-title>

      <v-card-text>
        <v-menu
            ref="menu"
            v-model="menu2"
            :close-on-content-click="false"
            :nudge-right="40"
            :return-value.sync="requestData.working_hours[0].open_at"
            transition="scale-transition"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
                v-model="requestData.working_hours[0].open_at"
                label="From *"
                filled
                readonly
                v-bind="attrs"
                v-on="on"
            ></v-text-field>
          </template>
          <v-time-picker
              v-if="menu2"
              v-model="requestData.working_hours[0].open_at"
              format="ampm"
              :allowed-minutes="m => m % 15 === 0"
              full-width
              @click:minute="$refs.menu.save(requestData.working_hours[0].open_at)"
          ></v-time-picker>
        </v-menu>

        <div class="radio-button">
          <v-radio-group
              v-model="requestData.working_hours[0].shift"
              row
          >
            <v-radio
                color="green"
                label="1 Hour"
                class="text-left"
                value="60"
            ></v-radio>
<!--            <v-radio-->
<!--                color="green"-->
<!--                label="1.5 Hour"-->
<!--                value="90"-->
<!--            ></v-radio>-->
            <v-radio
                color="green"
                label="2 Hours"
                class="float-end"
                value="120"
            ></v-radio>
          </v-radio-group>
        </div>

        <v-select
            v-model="requestData.working_hours[0].point_id"
            :items="locations"
            item-text="sourceName"
            item-value="id"
            filled
            label="Location"
        ></v-select>
      </v-card-text>

      <v-card-actions>
        <div class="row justify-content-around w-50 mb-3">

          <v-btn
              color="green darken-1"
              text
              @click="$emit('updateTimeDialog')"
          >
            Close
          </v-btn>

          <v-btn
              color="primary text-capitalize"
              @click="addSchedule"
          >
            {{ update_id ? "Update Time" : "Add Time" }}
          </v-btn>

        </div>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: "AddTimeline",
  props: ['timedialog', 'update_id'],
  data() {
    return {
      locations: [],
      menu2: false,
      requestData: {
        date: null,
        working_hours: [
          {
            open_at: null,
            point_id: null,
            shift: 0
          }
        ]
      }
    }
  },
  mounted() {
    this.requestData.date = this.$route.query.date;
    this.getLocations();
  },
  methods: {
    async getLocations() {
      const {data} = await axios.get('/v1/drivisa/instructors/get-active-points');
      this.locations = data.data;
    },
    async addSchedule() {
      try {
        let url = "/v1/drivisa/instructors/schedules";
        let message = "Schedule Added";

        if (this.update_id) {
          url = `/v1/drivisa/instructors/workingHours/${this.update_id}/update`;
          message = "Schedule Updated";

          let date = this.requestData.date + " " + this.requestData.working_hours[0].open_at;

          let today = new Date(date)
          let total_min = this.requestData.working_hours[0].shift;
          var futureDate = new Date(today.getTime() + total_min * 60000);
          let hours = futureDate.getHours();
          let min = futureDate.getMinutes()
          if (hours < 10) {
            hours = "0" + hours;
          }
          if (min < 10) {
            min = "0" + min
          }

          this.requestData.working_hours[0].close_at = hours + ":" + min;
        }

        const {data} = await axios.post(url, this.requestData);
        this.$toasted.success(message);
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to add location")
      } finally {
        await this.$emit('updateTimeDialog')
      }
    }
  }
}
</script>

<style lang="scss">
.radio-button {
  border-radius: 5px;
  border: 1px solid #aaa;
  background-color: #eee;
  margin-bottom: 1rem;
  display: flex;
  height: 60px;

  .v-input--radio-group__input {
    border: none !important;
    cursor: default !important;
    display: flex !important;
    width: 100vw !important;
    justify-content: space-around !important;
  }
}


</style>