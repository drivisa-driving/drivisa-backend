<template>
  <div>
    <AddTimeline
        :timedialog="timedialog"
        @updateTimeDialog="getSchedules"
        :update_id="updateID"
    />
    <MasterInstructorLayout>
      <v-card elevation="0" class="border">
        <v-card-text>
          <div class="timeline">
            <div class="go-back">
              <i class="mdi mdi-arrow-left"></i>
              <router-link to="/instructor/calendar">
                <h2 class="go-back-title">
                  Back to Calendar
                </h2>
              </router-link>
            </div>
            <div class="header">
              <h1 class="date">{{ $route.query.date | date }}</h1>
              <div class="actions">
                <v-dialog
                    v-if="schedules.length > 0 && schedules[0].workingHours.length > 0"
                    v-model="copydialog"
                    width="500"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn class="copy-action text-capitalize" v-bind="attrs"
                           v-on="on">
                      <i class="mdi mdi-content-copy"></i>
                      Copy Schedule
                    </v-btn>
                  </template>

                  <v-card>
                    <v-card-title class="text-h5">
                      Copy Schedule
                    </v-card-title>

                    <v-card-text>
                      <v-dialog
                          ref="fromdialog"
                          v-model="date_modal1"
                          :return-value.sync="date"
                          persistent
                          width="290px"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-text-field
                              v-model="copyRequestData.from"
                              label="From *"
                              placeholder="From *"
                              filled
                              readonly
                              v-bind="attrs"
                              v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="copyRequestData.from"
                            no-title
                            scrollable
                        >
                          <v-spacer></v-spacer>
                          <v-btn
                              text
                              color="primary"
                              @click="date_modal1 = false"
                          >
                            Cancel
                          </v-btn>
                          <v-btn
                              text
                              color="primary"
                              @click="$refs.fromdialog.save(copyRequestData.from)"
                          >
                            OK
                          </v-btn>
                        </v-date-picker>
                      </v-dialog>
                      <v-dialog
                          ref="todialog"
                          v-model="date_modal2"
                          :return-value.sync="date"
                          persistent
                          width="290px"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-text-field
                              filled
                              v-model="copyRequestData.to"
                              label="To *"
                              placeholder="To *"
                              readonly
                              v-bind="attrs"
                              v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="copyRequestData.to"
                            no-title
                            scrollable
                        >
                          <v-spacer></v-spacer>
                          <v-btn
                              text
                              color="primary"
                              @click="date_modal2 = false"
                          >
                            Cancel
                          </v-btn>
                          <v-btn
                              text
                              color="primary"
                              @click="$refs.todialog.save(copyRequestData.to)"
                          >
                            OK
                          </v-btn>
                        </v-date-picker>
                      </v-dialog>
                      <h5 class="text-dark">Exclude Days</h5>
                      <div class="row align-items-center align-content-center" style="height: 150px">
                        <v-checkbox
                            v-for="(week, j) in weeks"
                            :key="j"
                            v-model="copyRequestData.exclude[j]"
                            :label="week.label"
                            :value="week.value"
                            :false-value="null"
                            class="mr-2"
                        ></v-checkbox>
                      </div>
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn
                          color="primary"
                          text
                          @click="copySchedule()"
                      >
                        Copy
                      </v-btn>
                      <v-btn
                          color="primary"
                          text
                          @click="clearCopyData"
                      >
                        Close
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
                <v-btn class="add-action text-capitalize mt-3 mt-sm-0" @click.stop="timedialog = true">
                  Add Time
                </v-btn>
              </div>
            </div>
            <ul class="header_all_status">
              <li class="available-status-style">
                Available
              </li>
              <li class="reserved-status-style">
                Reserved
              </li>
              <li class="completed-status-style">
                Completed
              </li>
              <li class="canceled-status-style">
                Canceled
              </li>
            </ul>
            <div class="container">
              <div class="items" v-if="schedules.length > 0">
                <div class="item"
                     v-for="(workingHour, i) in schedules[0].workingHours"
                     :key="i"
                     v-bind:class="{
                        'reserved-status': getLessonStatus(workingHour) === 1 || workingHour.status === 2,
                        'completed-status':  getLessonStatus(workingHour) === 3,
                        'available-status': getLessonStatus(workingHour) === 0 && workingHour.status === 1,
                        'progress-status':  getLessonStatus(workingHour)=== 2,
                        'canceled-status':  getLessonStatus(workingHour)=== 4
                      }"
                >
                  <div class="time">
                    <div>
                      <h2>
                        {{ workingHour.openAt | time }}
                        - {{ workingHour.closeAt | time }}
                      </h2>
                    </div>
                  </div>
                  <div class="timeline_info">
                    <div v-if="getLessonStatus(workingHour) !== 0" class="details px-3 py-2">
                      <div class="content text-center">
                        <div class="text-center">
                          <img
                              :src="getLesson(workingHour).trainee.avatar" alt="">
                        </div>
                        <div class="name">{{ getLesson(workingHour).trainee.fullName }}</div>
                        <div class="payment"><strong>Paid :</strong>
                          {{ getLesson(workingHour).cost + getLesson(workingHour).tax }} $
                        </div>
                      </div>
                      <div> {{ getLesson(workingHour).status | status }}</div>
                    </div>
                    <v-menu
                        bottom
                        left
                        v-else
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            icon
                            v-bind="attrs"
                            v-on="on"
                            class="float-right"
                        >
                          <v-icon>mdi-dots-vertical</v-icon>
                        </v-btn>
                      </template>

                      <v-list>
                        <v-list-item>
                          <v-list-item-title
                              @click="updateID = workingHour.id; timedialog=true"
                          >Edit
                          </v-list-item-title>
                        </v-list-item>
                        <v-dialog
                            v-model="deletedialog"
                            persistent
                            max-width="290"
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-list-item v-bind="attrs" v-on="on">
                              <v-list-item-title>Delete</v-list-item-title>
                            </v-list-item>
                          </template>
                          <v-card>
                            <v-card-title class="text-h6">
                              Delete Schedule
                            </v-card-title>
                            <v-card-text>
                              Are you sure delete Schedule?
                            </v-card-text>
                            <v-card-actions>
                              <v-btn
                                  color="dark"
                                  text
                                  @click="deletedialog = false"
                              >
                                Cancel
                              </v-btn>
                              <v-btn
                                  color="primary"
                                  @click="deleteSchedule(workingHour.id)"
                              >
                                Yes
                              </v-btn>
                            </v-card-actions>
                          </v-card>
                        </v-dialog>
                      </v-list>
                    </v-menu>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </v-card-text>
      </v-card>
    </MasterInstructorLayout>
  </div>
</template>

<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";
import AddTimeline from "../../../components/Instructor/Calendar/AddTimeline";
import weeks from "../../../data/weekData";

export default {
  name: "Timeline",
  components: {MasterInstructorLayout, AddTimeline},
  data() {
    return {
      date_modal1: false,
      date_modal2: false,
      timedialog: false,
      deletedialog: false,
      copydialog: false,
      schedules: [],
      updateID: null,
      frommenu: false,
      tomenu: false,
      weeks: weeks,
      copyRequestData: {
        from: "",
        to: "",
        working_day_id: null,
        exclude: []
      }
    }
  },
  mounted() {
    this.getSchedules();
  },
  methods: {
    async getSchedules() {
      try {
        const date = this.$route.query.date;
        const url = `/v1/drivisa/instructors/schedules?from=${date}&to=${date}`;
        const {data} = await axios.get(url);
        this.schedules = data.data;
        this.copyRequestData.working_day_id = data.data[0].id;
      } catch (e) {

      } finally {
        this.timedialog = false
      }
    },
    getLesson(workingHour) {
      const lesson = this.schedules[0].lessons.find((item) => {
        return (item.startAt === (this.schedules[0].date + ' ' + workingHour.openAt) && workingHour.status === 2)
      })
      return lesson ? lesson : null;
    },
    getLessonStatus(workingHour) {
      let lesson = this.getLesson(workingHour);
      return lesson ? lesson.status : 0;
    },
    async deleteSchedule(id) {
      try {
        let url = "/v1/drivisa/instructors/workingHours/" + id;

        await axios.delete(url);
        this.$toasted.success("Schedule Deleted");
        await this.getSchedules();
        this.deletedialog = false;
        this.updateID = null;
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to Delete Working Hours")
      }
    },
    clearCopyData() {
      this.copyRequestData = {
        from: "",
        to: "",
        working_day_id: null,
        exclude: []
      }
      this.copydialog = false
    },
    async copySchedule() {
      try {
        const url = "/v1/drivisa/instructors/schedules/copy-schedule";
        const {data} = await axios.post(url, this.copyRequestData);
        this.$toasted.success("Schedule Copied Successfully")
        this.clearCopyData();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to Copy Schedule")
      }
    }
  }
}
</script>

<style scoped lang="scss">
@import "resources/js/scss/vue_variables";

.timeline {
  padding: 1rem;

  .go-back {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
    cursor: pointer;
  }

  .go-back-title {
    margin: 0;
    font: 500 20px/32px Montserrat, sans-serif;
    color: rgba(0, 0, 0, .87);
  }

  ul {
    list-style: none;
    display: flex;
    margin: 2rem 0;

    li {
      margin: 0 1rem;
    }
  }

  .header {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;

    h1 {
      font: 400 24px/32px Roboto, Helvetica Neue, sans-serif;
      letter-spacing: normal;
      margin: 0;
    }

    .copy-action {
      background-color: $light-gray;
    }
  }


  .add-action {
    margin: 0 0.5rem;
    background: $primary-light;
    border-radius: 4px;
    color: $primary;
  }

  .time {
    grid-area: time;
  }

  .name {
    grid-area: name;
    font-weight: 500;
    font-size: 16px;
  }

  .payment {
    grid-area: payment;
    font-size: large;
    font-weight: 500;
  }

  .link {
    grid-area: link;
    text-decoration: dashed;
    color: $primary;
  }

  .avatar {
    grid-area: avatar;
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 50%;
  }

  .item {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 10rem;
    border-radius: 10px;
    margin: 2rem 0rem;
    outline: 1px solid cadetblue;

    .time {
      width: 30%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      border-top-left-radius: 10px;
      border-bottom-left-radius: 10px;

      h2 {
        font: 500 20px/32px Roboto, Helvetica Neue, sans-serif;
        letter-spacing: normal;
        margin: 0 0 16px;
        color: white;
      }
    }

    .timeline_info {
      width: 70%;
      height: 100%;
      display: block;
      border-top-right-radius: 10px;
      border-bottom-right-radius: 10px;

      .float-right {
        float: right !important;
      }

      .details {
        box-sizing: border-box;
        display: flex;
        place-content: center space-between;
        align-items: center;
        height: 100%;

        .content {
          flex-direction: column;
          box-sizing: border-box;
          display: flex;

          img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            margin-bottom: 15px;
            margin-right: 8px;
            border-radius: 50%;
          }

          .name {
            font-size: 16px;
            margin-bottom: 15px;
            font-weight: 500;
          }

          .payment {
            font-size: large;
            font-weight: 500;
          }
        }
      }
    }
  }
}

@media (max-width: 810px) {
  .header_all_status {
    display: block !important;
  }
  .v-card__text, .v-card__title {
    padding: 10px;
  }
  .container {
    padding-right: 0;
    padding-left: 0;
  }
  .timeline {
    padding: 0;
  }
}
</style>