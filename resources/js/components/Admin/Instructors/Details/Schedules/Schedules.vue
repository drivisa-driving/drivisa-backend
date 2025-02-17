<template>
  <div>
    <v-dialog v-model="timelineDialog" width="500">
      <v-card>
        <v-card-text class="text-center py-3">
          <Timeline :schedules="schedules"/>
          <div class="d-flex justify-content-end">
            <v-btn class="text-capitalize" @click="timelineDialog = false">
              Close
            </v-btn>
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>
    <div
        class="text-danger text-right mb-1"
        style="width: 100%; font-size: 13px"
    >
      *Click on a particular date to view the instructor's timeline for that day
    </div>
    <div>
      <ul class="header_all_status list-unstyled d-flex text-body">
        <li class="available-status-style mr-8">Available</li>
        <li class="reserved-status-style mr-8">Reserved</li>
        <li class="completed-status-style mr-8">Completed</li>
        <li class="canceled-status-style">Canceled</li>
      </ul>
      <div class="relative">
        <div
            v-if="isLoading"
            class="absolute top-0 right-0 h-full w-full bg-white opacity-50 z-50 text-center"
        >
          <v-progress-circular
              :size="50"
              color="primary"
              indeterminate
          ></v-progress-circular>
        </div>
        <div >
      <FullCalendar
          class="demo-app-calendar"
          :options="calendarOptions"
          themeSystem="bootstrap"
          ref="calendar"
      >
        <template v-slot:dayCellContent="arg">
          <div class="font-weight-bold">
            <v-badge color="transparent">{{ arg.dayNumberText }}</v-badge>
            <div class="bottom-fix ml-1 cursor-pointer">
              <div
                  class="reserved-badge mr-1 text-center"
                  v-if="workHoursCount(arg.date, 1, 'lesson') > 0"
              >
                <span>
                  {{ workHoursCount(arg.date, 1, "lesson") }}
                </span>
              </div>
              <div
                  class="completed-badge mr-1 text-center"
                  v-if="workHoursCount(arg.date, 3, 'lesson') > 0"
              >
                <span>
                  {{ workHoursCount(arg.date, 3, "lesson") }}
                </span>
              </div>
              <div
                  class="available-badge mr-1 text-center"
                  v-if="workHoursCount(arg.date, 1) > 0"
              >
                <span>
                  {{ workHoursCount(arg.date, 1) }}
                </span>
              </div>
              <div
                  class="canceled-badge mr-1 text-center"
                  v-if="workHoursCount(arg.date, 4, 'lesson') > 0"
              >
                <span>
                  {{ workHoursCount(arg.date, 4, "lesson") }}
                </span>
              </div>
            </div>
          </div>
        </template>
      </FullCalendar>
    </div>
    </div>
    </div>
  </div>
</template>

<script>
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import {INITIAL_EVENTS, createEventId} from "../../../../../data/event-utils";
import Timeline from "./Timeline.vue";

export default {
  name: "Schedules",
  components: {FullCalendar, Timeline},
  data() {
    return {
      isLoading: false,
      currentMonth: null,
      allSchedules: [],
      schedules: [],
      date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
          .toISOString()
          .substr(0, 10),
      options: {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        customButtons: {
          prev: {
            click: () => {
              this.getSchedulesData(this.currentMonth.start, "decrement");
              let calendarApi = this.$refs.calendar.getApi();
              calendarApi.prev();
            },
          },
          next: {
            click: () => {
              this.getSchedulesData(this.currentMonth.start, "increment");
              let calendarApi = this.$refs.calendar.getApi();
              calendarApi.next();
            },
          },
        },
        headerToolbar: {
          left: "prev",
          center: "title",
          right: "next",
        },
        initialView: "dayGridMonth",
        events: [],
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        weekends: true,
        aspectRatio: 5,
        height: "700px",
        select: this.getSchedules,
      },
      timelineDialog: false,
    };
  },
  computed: {
    calendarOptions() {
      return this.options;
    },
  },
  mounted() {
    this.getSchedulesData();
    this.getSchedules();
  },
  methods: {
    workHoursCount(date, status = null, by_type = "workingHour") {
      return hasWorkHours(this.allSchedules, date, status, by_type);
    },
    async getSchedulesData(actionDate = null, action = null) {
      try {
        const thisMonth = getCurrentMonthFirstAndLastDay(actionDate, action);
        this.isLoading = true;
        this.currentMonth = thisMonth;

        const url = `/v1/drivisa/admin/instructors/instructor-schedules-new/${this.$route.params.id}?from=${thisMonth.start}&to=${thisMonth.end}`;
        const {data} = await axios.get(url);

        let allSchedules = data.data;
        this.allSchedules = allSchedules;

        this.options.events = [{}];
        setTimeout(() => {

          if (allSchedules.length) {

            allSchedules.forEach((item) => {
              if (item.status === 1) {
                this.options.events.push({
                  id: createEventId(),
                  title: item.workingHours,
                  start: item.date,
                  classNames: "event light-blue",
                });
              } else if (item.status === 2) {
                this.options.events.push({
                  id: createEventId(),
                  title: item.workingHours,
                  start: item.date,
                  classNames: "event green",
                });
              } else if (item.status === 3) {
                this.options.events.push({
                  id: createEventId(),
                  title: item.workingHours,
                  start: item.date,
                  classNames: "event blue",
                });
              } else if (item.status === 4) {
                this.options.events.push({
                  id: createEventId(),
                  title: item.workingHours,
                  start: item.date,
                  classNames: "event red",
                });
              }
            });
          } else {
            this.options.events = [{}];
          }

        }, 2500);
        this.isLoading = false;

      } catch (e) {
        console.log(e)
      }
    },
    async getSchedules(selectInfo) {
      if (selectInfo) {
        const date = selectInfo.startStr;
        if (date) {
          try {
            const url = `/v1/drivisa/admin/instructors/instructor-schedules/${this.$route.params.id}?from=${date}&to=${date}`;
            const {data} = await axios.get(url);
            this.schedules = data.data;
            this.timelineDialog = true;
          } catch (e) {
            console.log(e)
          }
        }
      }
    },
  },
};
</script>
