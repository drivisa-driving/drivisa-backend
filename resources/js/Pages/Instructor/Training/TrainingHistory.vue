<template>
  <div>
    <MasterInstructorLayout>
      <v-card elevation="0" class="border">
        <v-card-text>
          <div class="d-lg-flex justify-content-lg-between">
            <div><h4 class="font-weight-light text-dark">Training History</h4></div>
            <div>
              <v-btn text class="text-capitalize" style="font-size: 14px" @click="showFilter = !showFilter">
                <i class="mdi mdi-filter-variant"></i> Filter
              </v-btn>
            </div>
          </div>
          <div class="row mt-4" v-if="showFilter">
            <div class="col-sm-6">
              <v-menu
                  ref="menu"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                      v-model="from"
                      label="From"
                      prepend-icon="mdi-calendar"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                    v-model="from"
                    :max="(new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)"
                    min="1950-01-01"
                ></v-date-picker>
              </v-menu>
            </div>
            <div class="col-sm-6">
              <v-menu
                  ref="to"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                      v-model="to"
                      label="To"
                      prepend-icon="mdi-calendar"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                    v-model="to"
                    :max="(new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)"
                    min="1950-01-01"
                ></v-date-picker>
              </v-menu>
            </div>
          </div>

          <div class="" v-if="lessons && lessons.data.length > 0">
            <v-simple-table>
              <template v-slot:default>
                <thead>
                <tr>
                  <th class="text-left">
                    Name
                  </th>
                  <th class="text-left">
                    Start
                  </th>
                  <th class="text-left">
                    End
                  </th>
                  <th class="text-left">
                    Status
                  </th>
                  <th class="text-left">
                    Details
                  </th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="lesson in lessons.data"
                    :key="lesson.id"
                >
                  <td class="p-2">
                    <img :src="lesson.trainee.avatar" width="70" height="70" class="v-avatar" alt="">
                    {{ lesson.trainee.fullName }}
                  </td>
                  <td>{{ lesson.startAt | dateTime }}</td>
                  <td>{{ lesson.endAt | dateTime }}</td>
                  <td>
                    <v-chip>
                      {{ lesson.status | status }}
                    </v-chip>
                  </td>
                  <td>
                    <v-menu
                        offset-y
                        bottom
                    >
                      <template v-slot:activator="{ attrs, on }">
                       <span v-bind="attrs" v-on="on">
                         <span class="mdi mdi-dots-horizontal option-icon"></span>
                       </span>
                      </template>
                      <v-list>
                        <router-link :to="{name:'instructor-single-training-history-page', params:{id:lesson.id}}">
                          <v-list-item>
                           View
                          </v-list-item>
                        </router-link>
                        <v-list-item  v-if="lesson.status === 1">
                          <router-link to="#"
                          >Cancel</router-link>
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </td>
                </tr>
                </tbody>
              </template>
            </v-simple-table>
            <v-pagination
                v-model="currentPage"
                :length="lessons.meta.last_page"
                @input="getLesson"
            ></v-pagination>
          </div>
          <div v-else>
            <h2 class="text-muted text-center">There are no lesson</h2>
          </div>

        </v-card-text>
      </v-card>
    </MasterInstructorLayout>
  </div>
</template>

<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";

export default {
  name: "TrainingHistory",
  components: {MasterInstructorLayout},
  data() {
    return {
      currentPage: 1,
      showFilter: false,
      from: null,
      to: null,
      lessons: null
    }
  },
  watch: {
    from() {
      this.getLesson()
    },
    to() {
      this.getLesson()
    }
  },
  mounted() {
    this.getLesson();
  },
  methods: {
    async getLesson() {
      try {
        let url = '/v1/drivisa/instructors/lessons';
        let params = new URLSearchParams();
        if (this.from) {
          params.append('start_at', this.from);
        }
        if (this.to) {
          params.append('end_at', this.to);
        }

        params.append("page", this.currentPage)

        url += "?" + params.toString();
        const {data} = await axios.get(url);
        this.lessons = data;
        this.currentPage = this.lessons.meta.current_page;
      } catch (e) {

      }
    }
  }
}
</script>

<style scoped>
.option-icon {
  background-repeat: no-repeat;
  display: inline-block;
  fill: currentColor;
  height: 24px;
  width: 24px;
  font-size: 24px;
}
</style>