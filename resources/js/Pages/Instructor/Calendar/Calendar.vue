<template>
  <div>
    <MasterInstructorLayout>
      <v-card elevation="0" class="border">
        <v-card-text>
          <ul class="header_all_status list-unstyled d-flex text-body">
            <li class="total-status-style mr-8">Total Timeline</li>
            <li class="available-status-style">Available</li>
          </ul>
          <div class="mb-5 mt-6 mt-lg-0">
            <FullCalendar
                class='demo-app-calendar'
                :options='calendarOptions'
                themeSystem="bootstrap"
                ref="calendar"
            >
              <template v-slot:dayCellContent="arg">
                <div class="font-weight-bold">
                  <v-badge color="transparent">{{ arg.dayNumberText }}</v-badge>
                  <div class="total-badge text-center"
                       v-if=" workHoursCount(arg.date) > 0">
                    <span>
                      {{ workHoursCount(arg.date) }}
                    </span>
                  </div>
                  <div class="bottom-fix ml-1">
                    <div class="reserved-badge mr-1 text-center"
                         v-if="workHoursCount(arg.date, 1, 'lesson') > 0">
                    <span>
                     {{ workHoursCount(arg.date, 1, 'lesson') }}
                    </span>
                    </div>
                    <div class="completed-badge mr-1 text-center"
                         v-if=" workHoursCount(arg.date, 3, 'lesson') > 0"
                    >
                    <span>
                      {{ workHoursCount(arg.date, 3, 'lesson') }}
                    </span>
                    </div>
                    <div class="available-badge mr-1 text-center"
                         v-if=" workHoursCount(arg.date, 1) > 0"
                    >
                    <span>
                      {{ workHoursCount(arg.date, 1) }}
                    </span>
                    </div>
                    <div class="canceled-badge mr-1 text-center"
                         v-if=" workHoursCount(arg.date, 4, 'lesson') > 0"
                    >
                    <span>
                      {{ workHoursCount(arg.date, 4, 'lesson') }}
                    </span>
                    </div>
                  </div>
                </div>
              </template>
            </FullCalendar>
          </div>
        </v-card-text>
      </v-card>
    </MasterInstructorLayout>
  </div>
</template>

<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";

import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import {INITIAL_EVENTS, createEventId} from '../../../data/event-utils'

export default {
  name: "Calendar",
  components: {MasterInstructorLayout},
  data() {
    return {
      currentMonth: null,
      allSchedules: [],
      date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
      options: {
        plugins: [
          dayGridPlugin,
          timeGridPlugin,
          interactionPlugin,
        ],
        customButtons: {
          prev: {
            click: () => {
              this.getSchedulesData(this.currentMonth.start, 'decrement');
              let calendarApi = this.$refs.calendar.getApi();
              calendarApi.prev();
            }
          },
          next: {
            click: () => {
              this.getSchedulesData(this.currentMonth.start, 'increment')
              let calendarApi = this.$refs.calendar.getApi();
              calendarApi.next();
            }
          }
        },
        headerToolbar: {
          left: 'prev next ',
          center: 'title',
          right: 'dayGridMonth timeGridWeek timeGridDay'
        },
        initialView: 'dayGridMonth',
        events: [],
        eventLimit: false,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        weekends: true,
        aspectRatio: 5,
        height: '700px',
        select: this.handleDateSelect
      }
    }
  },
  computed: {
    calendarOptions() {
      return this.options;
    }
  },
  mounted() {
    this.getSchedulesData();
  },
  methods: {
    workHoursCount(date, status = null, by_type = "workingHour") {
      return hasWorkHours(this.allSchedules, date, status, by_type)
    },
    async getSchedulesData(actionDate = null, action = null) {
      try {
        const thisMonth = getCurrentMonthFirstAndLastDay(actionDate, action);

        this.currentMonth = thisMonth;

        const url = `/v1/drivisa/instructors/schedules?from=${thisMonth.start}&to=${thisMonth.end}`;
        const {data} = await axios.get(url);

        let allSchedules = data.data;
        this.allSchedules = allSchedules;

        this.options.events = [];

        allSchedules.forEach((item) => {
          if (item.status === 1) {
            this.options.events.push({
              id: createEventId(),
              title: item.workingHours.length,
              start: item.date,
              classNames: "event light-blue"
            })
          } else if (item.status === 2) {
            this.options.events.push({
              id: createEventId(),
              title: item.workingHours.length,
              start: item.date,
              classNames: "event green"
            })
          } else if (item.status === 3) {
            this.options.events.push({
              id: createEventId(),
              title: item.workingHours.length,
              start: item.date,
              classNames: "event blue"
            })
          } else if (item.status === 4) {
            this.options.events.push({
              id: createEventId(),
              title: item.workingHours.length,
              start: item.date,
              classNames: "event red"
            })
          }
        })
      } catch (e) {

      }
    },
    handleDateSelect(selectInfo) {
      this.$router.push("/instructor/timeline?date=" + selectInfo.startStr);
    }
  }
}
</script>
