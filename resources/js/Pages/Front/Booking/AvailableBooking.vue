<template>
  <div>
    <Header/>
    <div>
      <div class="container-fluid px-0 profile_section" v-if="singleInstructor">
        <div class="cover-section rounded-0" :style="{ backgroundImage: 'url('+ singleInstructor.cover +')' }">
          <div class="instructor-avatar">
            <img class="avatar" style="margin-bottom: 10px;"
                 :src="singleInstructor.avatar">
            <h5 class="font-weight-bold  text-center">
              {{ singleInstructor.fullName }}
            </h5>
            <div class="car" style="max-width: 250px">
              <i class="car-icon mdi mdi-car"></i>

              <span v-if="singleInstructor.cars.length > 0" style="width: 202px">
                {{ singleInstructor.cars[0].make }}
                {{ singleInstructor.cars[0].model }}
              </span>
              <span v-else>
                Not Provided
              </span>
            </div>
            <div class="star_rating">
              <div class="rating">
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
          <div class="content-section justify-center">
            <p class="">{{ singleInstructor.bio }}</p>
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
          <div class="content-section pt-4">
            <div>
              <v-btn color="white" class="text-capitalize"
                     :to="{name:'single-instructor', params:{username:$route.query.instructor}}">
                <i class="mdi mdi-arrow-left"></i>
                Back To Calendar
              </v-btn>
            </div>
            <div>
              <h2 class="text-dark">{{ $route.query.date | date }}</h2>
            </div>
            <div class="d-flex justify-content-center align-content-center">
              <div class="square-dot mr-2">
              </div>
              <div>Available</div>
            </div>
          </div>

          <div class="items row" v-if="workHours.length > 0">
            <div class="col-lg-6" v-for="(workingHour, i) in workHours"
                 :key="i">
              <div class="item">
                <div class="time available-bg">
                  <div>
                    <p><strong>Start:</strong> {{ workingHour.openAt | time }}</p>
                    <br class="d-none my-2 d-sm-block">
                    <p><strong>End:</strong> {{ workingHour.closeAt | time }}</p>
                  </div>
                </div>
                <div class="address">
                  <div class="box">
                    <div class="location">
                      <i class="mdi mdi-map-marker mat_icon"></i>
                      <span class="mat-tooltip-trigger">
                       {{ workingHour.point.sourceName }}
                      </span>
                    </div>
                  </div>
                </div>
                <div class="btn-box align-self-end mr-3 mb-3">
                  <v-btn
                      small
                      class="available-bg"
                      v-if="$store.getters.isAuthenticated === false"
                      to="/login"
                  >
                    Login
                  </v-btn>
                  <div v-else-if="bookingAvailable(workingDay.date, workingHour.closeAt)">

                    <div class="d-flex">
                      <v-btn
                          class="available-bg mr-3"
                          small
                          :to="{name:'lesson-page', query:{instructor:singleInstructor.username, lesson:workingHour.id}}"
                      >
                        Book Now
                      </v-btn>
                      <v-btn
                          color="primary"
                          small
                          @click="dialog = true; currentSelectedLesson=workingHour;"
                          :disabled="rescheduleLessons.length === 0"
                      >
                        Reschedule
                      </v-btn>
                    </div>
                  </div>
                  <button
                      v-else
                      class="btn btn-sm btn-danger"
                      style="width: 150px"
                      disabled
                  >
                    Time Expired
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="items row">
            <div class="col-12" style="margin-left: 50px">
              <h3 class="text-sm-center text-left">No Availability</h3>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Footer/>
    <div class="modal fade in modal-active" v-show="dialog" id="res_dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <v-card>
            <v-card-text>
              <v-container>
                <v-row>
                  <v-col cols="12">
                    <v-select
                        :items="filteredRescheduleLessons"
                        item-value="id"
                        label="Select Lesson"
                        persistent-hint
                        return-object
                        single-line
                        @change="$event => this.rescheduleSelectedLesson = $event"
                    >
                      <template slot="selection" slot-scope="data">
                        {{ data.item.no }} - {{ data.item.start_at }}
                      </template>
                      <template slot="item" slot-scope="data">
                        {{ data.item.no }} - {{ data.item.start_at }}
                      </template>
                    </v-select>
                  </v-col>
                </v-row>
                <v-row v-if="rescheduleSelectedLesson">
                  <v-col cols="12">
                    <h4>Selected Lesson to Reschedule</h4>
                    <v-simple-table>
                      <template v-slot:default>
                        <thead>
                        <tr>
                          <th class="text-left">
                            Start Time
                          </th>
                          <td class="text-left">
                            {{ rescheduleSelectedLesson.start_at }}
                          </td>
                        </tr>
                        <tr>
                          <th class="text-left">
                            End Time
                          </th>
                          <td class="text-left">
                            {{ rescheduleSelectedLesson.end_at }}
                          </td>
                        </tr>
                        </thead>
                      </template>
                    </v-simple-table>
                  </v-col>
                </v-row>
                <v-row v-if="currentSelectedLesson">
                  <v-col cols="12">
                    <h4>New Reschedule</h4>
                    <v-simple-table>
                      <template v-slot:default>
                        <thead>
                        <tr>
                          <th class="text-left">
                            Start Time
                          </th>
                          <td class="text-left">
                            {{ currentSelectedLesson.openAt_formatted }}
                          </td>
                        </tr>
                        <tr>
                          <th class="text-left">
                            End Time
                          </th>
                          <td class="text-left">
                            {{ currentSelectedLesson.closeAt_formatted }}
                          </td>
                        </tr>
                        </thead>
                      </template>
                    </v-simple-table>
                  </v-col>
                </v-row>
              </v-container>
              <v-container v-show="within24Hours">
                <p class="text-danger">*You rescheduling a lesson that conduct within 24 hours that's why you see this
                  notice for extra payment.</p>
                <h6 class="font-weight-bold">
                  Total Payment: ${{ amount * (rescheduleSelectedLesson ? rescheduleSelectedLesson.duration : 1) }}
                  + 13% HST
                </h6>
                <div class="payment-form">
                  <div id="card-element" class="input-lg form-control cc-number" style="height:40px"></div>
                </div>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                  color="danger"
                  small
                  class="mb-4"
                  @click="dialog = false"
              >
                Close
              </v-btn>
              <v-btn
                  :disabled="rescheduleLessons.length === 0"
                  color="primary"
                  small
                  class="mb-4"
                  @click="reschedule"
              >
                {{ within24Hours ? "Pay & Reschedule" : "Reschedule" }}
              </v-btn>
            </v-card-actions>
          </v-card>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Footer from "../../../components/Front/Footer";
import Header from "../../../components/Front/Header";
import RescheduleExtraChargeDialog from "../../../components/Front/dailogs/RescheduleExtraChargeDialog";
import PaymentDialog from "../../Stripe/PaymentDialog";

let pk = process.env.STRIPE_KEY;
export default {
  name: "AvailableBooking",
  components: {RescheduleExtraChargeDialog, Footer, Header, PaymentDialog},
  data() {
    return {
      cardElement: null,
      stripe: null,
      amount: 25,
      dialog: false,
      singleInstructor: null,
      workHours: [],
      workingDay: null,
      rescheduleLessons: [],
      currentSelectedLesson: null,
      rescheduleSelectedLesson: null,
    }
  },
  computed: {
    filteredRescheduleLessons() {
      let allLessons = this.rescheduleLessons;

      if (this.currentSelectedLesson == null) return allLessons;

      return allLessons.filter(lesson => lesson.duration == this.currentSelectedLesson.duration);
    },
    within24Hours() {
      if (this.rescheduleSelectedLesson == null || this.currentSelectedLesson == null) {
        return false;
      }

      return this.$root.isDateWithin24(this.rescheduleSelectedLesson.start_at);


    }
  },
  watch: {
    dialog(newValue) {
      if (newValue == true) {
        $("#res_dialog").modal("show");
      } else {
        $("#res_dialog").modal("hide");
      }
    }
  },
  mounted() {

    this.stripe = Stripe(pk);
    let elements = this.stripe.elements();
    this.cardElement = elements.create('card');
    this.cardElement.mount('#card-element');

    this.getSingleInstructors();
    this.getSchedules();
    this.getRescheduleLessons();


  },
  methods: {
    async getSingleInstructors() {
      const username = this.$route.query.instructor;
      const {data} = await axios.get(`/v1/drivisa/instructors/${username}`)
      this.singleInstructor = data.data
    },
    async getRescheduleLessons() {
      try {
        const {data} = await axios.get(`/v1/drivisa/trainees/lessons/available-for-reschedule`)
        this.rescheduleLessons = data.data;
      } catch (e) {

      }
    },
    async getSchedules() {
      try {
        const workDay = this.$route.query.workDay;
        const {data} = await axios.get(`/v1/drivisa/instructors/workingDays/${workDay}`)
        this.workingDay = data.data;
        this.workHours = this.workingDay.workingHours;
      } catch (e) {

      }
    },
    bookingAvailable(date, time) {
      return isBookingAvailable(date, time);
    },
    async paymentMethod() {
      try {
        let payment_method_id = undefined;
        const {
          paymentMethod,
          error
        } = await this.stripe.createPaymentMethod(
            'card', this.cardElement);
        if (error) {
          this.$root.handleErrorToast("Unable to process your card")
          return;
        } else {
          payment_method_id = paymentMethod.id;
        }
        return payment_method_id;
      } catch (e) {
        this.$toasted.error("Please Check your payment Details")
      }
    },
    async reschedule() {
      try {

        let payment_method_id = null;
        if (this.within24Hours) {
          payment_method_id = await this.paymentMethod();

          if (!payment_method_id) {
            this.$root.handleErrorToast("Unable to process your payment")
            return;
          }
        }

        let url = "/v1/drivisa/trainees/lessons/reschedule";
        const {data} = await axios.post(url, {
          working_hour_id: this.currentSelectedLesson.id,
          lesson_id: this.rescheduleSelectedLesson.id,
          payment_method_id: payment_method_id
        });
        this.$toasted.success(data.message)
        this.dialog = false;
        await this.getSingleInstructors();
        await this.getSchedules();
        await this.getRescheduleLessons();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to reschedule")
      }
    }

  },
}
</script>

<style scoped lang="scss">
.modal-active {
  display: block;
}

.square-dot {
  display: inline-block;
  width: 20px;
  height: 20px;
  border-radius: 3px;
  background: #1bc5bd;
}

.items {
  border-color: #1bc5bd;
  display: flex;
}

.item {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 10rem;
  border-radius: 10px;
  margin: 2rem 0rem;
  border: 1px solid cadetblue;

  .time {
    width: 72%;
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

  .address {
    flex-direction: column;
    box-sizing: border-box;
    display: flex;
    flex: 1 1 100%;
    place-content: flex-start space-evenly;
    align-items: flex-start;
    max-width: 60%;

    .box {
      flex-direction: column;
      box-sizing: border-box;
      display: flex;
      padding: 4px;

      .location {
        margin-bottom: 10px;
        flex-direction: row;
        box-sizing: border-box;
        display: flex;
        width: 24px;
        height: 24px;
        font-size: 20px;
      }
    }

  }
}

</style>