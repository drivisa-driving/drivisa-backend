<template>
  <div>
    <MasterInstructorLayout>
      <v-card elevation="0" class="border">
        <v-card-text>
          <div class="car">
            <div class="d-lg-flex justify-content-lg-between">
              <h1 class="text-dark">Training Locations</h1>
              <router-link to="/instructor/training-locations/add" class="d-btn-blue"
                           style="min-width: 125px;height: 38px">
                Add Location
              </router-link>
            </div>

            <v-simple-table
                fixed-header
            >
              <template v-slot:default>
                <thead>
                <tr>
                  <th class="text-left">
                    Source Name
                  </th>
                  <th class="text-left">
                    Source Address
                  </th>
                  <th class="text-left">
                    Status
                  </th>
                  <th class="text-left"></th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="location in locations"
                    :key="location.id"
                >
                  <td>{{ location.sourceName }}</td>
                  <td>{{ location.sourceAddress }}</td>
                  <td>
                    <v-chip v-if="location.isActive" color="green" text-color="white" small>
                      Activated
                    </v-chip>
                    <v-chip v-else color="red" text-color="white" small>
                      Deactivated
                    </v-chip>
                  </td>
                  <td>
                    <v-menu offset-y>
                      <template v-slot:activator="{ on, attrs }">
                        <span class="mdi mdi-dots-horizontal option-icon"
                              v-bind="attrs"
                              v-on="on"
                        ></span>
                      </template>
                      <v-list>
                        <router-link :to="{name:'instructor-location-edit-page', params:{id:location.id}}">
                          <v-list-item>
                            <v-list-item-title>Edit</v-list-item-title>
                          </v-list-item>
                        </router-link>
                        <v-dialog
                            v-model="dialog"
                            persistent
                            max-width="290"
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-list-item v-bind="attrs" v-on="on">
                              <v-list-item-title>{{ location.isActive ? "Deactivate" : "Activate" }}</v-list-item-title>
                            </v-list-item>
                          </template>
                          <v-card>
                            <v-card-title class="text-h6">
                              Update Point Status
                            </v-card-title>
                            <v-card-text>
                              Are you sure update Status?
                            </v-card-text>
                            <v-card-actions>
                              <v-btn
                                  color="dark"
                                  text
                                  @click="dialog = false"
                              >
                                Cancel
                              </v-btn>
                              <v-btn
                                  color="primary"
                                  @click="togglePointStatus(location.id, !location.isActive)"
                              >
                                Yes
                              </v-btn>
                            </v-card-actions>
                          </v-card>
                        </v-dialog>
                      </v-list>
                    </v-menu>
                  </td>
                </tr>
                </tbody>
              </template>
            </v-simple-table>
          </div>
        </v-card-text>
      </v-card>
    </MasterInstructorLayout>
  </div>
</template>

<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";

export default {
  name: "TrainingLocation",
  components: {MasterInstructorLayout},
  data() {
    return {
      dialog: false,
      locations: [],
    }
  },
  mounted() {
    this.getLocations();
  },
  methods: {
    async getLocations() {
      const {data} = await axios.get('/v1/drivisa/instructors/get-points');
      this.locations = data.data;
    },
    async togglePointStatus(id, status) {
      try {
        const url = `/v1/drivisa/instructors/points/${id}/toggle-point`;
        await axios.post(url, {status: status});
        this.dialog = false;
        this.$toasted.success("Status Updated")
        await this.getLocations();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to Save Location")
      }
    }
  }
}
</script>

<style scoped>
.car h1 {
  font-family: Montserrat, sans-serif !important;
  font-weight: 400;
  font-size: 24px;
}

.option-icon {
  background-repeat: no-repeat;
  display: inline-block;
  fill: currentColor;
  height: 24px;
  width: 24px;
  font-size: 24px;
}

th, td {
  padding: 0 7px !important;
}
</style>