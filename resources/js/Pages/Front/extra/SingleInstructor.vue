<template>
  <div>
    <Header/>
    <div>
      <div class="container-fluid px-0 profile_section" v-if="singleInstructor">
        <div class="cover-section rounded-0" :style="{ backgroundImage: 'url('+ singleInstructor.cover +')' }">
          <div class="instructor-avatar">
            <img class="avatar"
                 :src="singleInstructor.avatar">
            <h5 class="font-weight-bold my-2 text-center">
              {{ singleInstructor.fullName }}
            </h5>
            <div class="car" style="max-width: 250px">
              <i class="car-icon mdi mdi-car"></i>

              <span v-if="singleInstructor.cars.length > 0" style="width: 202px">
                {{ singleInstructor.cars[0].make }}  {{ singleInstructor.cars[0].model }}
              </span>
              <span v-else>
                Not Provided
              </span>
            </div>
            <div class="star_rating">
              <div class="star-rating d-flex justify-content-center">
                <v-rating
                    v-model="singleInstructor.evaluation.avg"
                    readonly
                    background-color="orange darken"
                    color="orange"
                    icon-label="custom icon label text"
                ></v-rating>
              </div>
              <small>
                {{ singleInstructor.evaluation.comments.length }}
                <span v-if="singleInstructor.evaluation.comments.length < 2">comment</span>
                <span v-else>comments</span>
              </small>
            </div>
          </div>
        </div>
        <div class="d-container">
          <div class="content-section ">
            <div class="stat">
              <img alt="" src="/assets/media/imgs/Group.png" style="margin-left: 10px;margin-right: 10px;">
              <div class="details">
                <span class="num">{{ singleInstructor.lessons.trainee }} +</span>
                <span> Trainee </span>
              </div>
            </div>
            <div class="stat">
              <img alt="" src="/assets/media/imgs/Group.png" style="margin-left: 10px;margin-right: 10px;">
              <div class="details">
                <span class="num">{{ singleInstructor.lessons.hours }} +</span>
                <span> Training Hours  </span>
              </div>
            </div>
            <div class="stat">
              <img alt="" src="/assets/media/imgs/Group.png" style="margin-left: 10px;margin-right: 10px;">
              <div class="details">
                <span class="num">{{ singleInstructor.lessons.count }} +</span>
                <span> Lessons </span>
              </div>
            </div>
          </div>
          <div class="mb-5">
            <FullCalendar
                class='demo-app-calendar'
                :options='calendarOptions'
                ref='calendar'
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
                    <div class="available-badge mr-1 text-center"
                         v-if=" workHoursCount(arg.date, 1) > 0"
                    >
                    <span>
                      {{ workHoursCount(arg.date, 1) }}
                    </span>
                    </div>
                  </div>
                </div>
              </template>
            </FullCalendar>
          </div>
          <div class="comment_card">
            <div class="card"
                 v-for="(comment, i) in singleInstructor.evaluation.comments" :key="i"
            >
              <div class="user_info">
                <p>{{ comment.name }}</p>
                <img class="avatar"
                     :src="comment.profilePhoto">
              </div>
              <div class="comment">
                <div class="star_rating">
                  <v-rating
                      style="margin-top: -9px;margin-left: -11px"
                      v-model="comment.rating"
                      readonly
                      background-color="orange darken"
                      color="orange"
                      icon-label="custom icon label text"
                  ></v-rating>
                </div>
                <p style="margin-top: 20px">{{ comment.note }}</p>
                <small class="date-info">{{ comment.formattedDate }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
    <Footer/>
  </div>
</template>

<script>
import axios from "axios";
import Header from "../../../components/Front/Header";
import Footer from "../../../components/Front/Footer";

import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import {createEventId} from "../../../data/event-utils";


export default {
  name: "InstructorProfile",
  components: {Footer, Header, FullCalendar},
  data() {
    return {
      date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
      singleInstructor: null,
      options: {
        plugins: [
          dayGridPlugin,
          timeGridPlugin,
          interactionPlugin,
        ],
        customButtons: {
          prev: {
            click: () => {
              this.getSchedules(this.currentMonth.start, 'decrement');
              let calendarApi = this.$refs.calendar.getApi();
              calendarApi.prev();
            }
          },
          next: {
            click: () => {
              this.getSchedules(this.currentMonth.start, 'increment')
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
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        weekends: true,
        height: '700px',
        select: this.handleDateSelect,
      },
      currentEvents: [],
      schedules: [],
      currentMonth: null
    }
  },
  computed: {
    calendarOptions() {
      return this.options;
    }
  },
  mounted() {
    this.getSingleInstructors();
    this.getSchedules();
    this.currentMonth = getCurrentMonthFirstAndLastDay(null, null);
  },
  methods: {
    workHoursCount(date, status = null, by_type = "workingHour") {
      return hasWorkHours(this.schedules, date, status, by_type)
    },
    async getSingleInstructors() {
      const username = this.$route.params.username;
      const {data} = await axios.get(`/v1/drivisa/instructors/${username}`)
      this.singleInstructor = data.data
    },
    async getSchedules(actionDate = null, action = null) {
      try {
        let currentMonth = getCurrentMonthFirstAndLastDay(actionDate, action);

        this.currentMonth = currentMonth;

        const username = this.$route.params.username;
        const {data} = await axios.get(`/v1/drivisa/instructors/${username}/get-schedule?from=` + currentMonth.start + "&to=" + currentMonth.end)

        let allSchedules = data.data;
        this.schedules = allSchedules;

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
      const username = this.$route.params.username;
      const workDay = this.schedules.find(sch => sch.date === selectInfo.startStr)
      if (!workDay) return;
      this.$router.push(`/booking?instructor=${username}&date=${selectInfo.startStr}&workDay=${workDay.id}`);
    },
  },
}
</script>