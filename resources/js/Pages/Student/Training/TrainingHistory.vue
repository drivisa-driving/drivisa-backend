<template>
  <div>
    <MasterStudentLayout>
      <v-card elevation="0" class="border">
        <!-- Reschedule dialog -->
        <div class="modal fade in modal-active" v-show="reScheduleDialog" id="res_dialog">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <div
                    v-for="(reschedule, index) in reschedules"
                    :key="index"
                    class="mb-2"
                >
                  <v-card-title class="text-h5">
                    {{ reschedule.date_formatted }}
                  </v-card-title>
                  <v-item>
                    <v-card>
                      <div
                          v-for="single_time in reschedule.times"
                          :key="single_time.working_hour_id"
                          @click="selectedWorkingHourId = single_time.working_hour_id"
                          class="d-flex align-center justify-content-between px-4"
                      >
                        <div>
                          <v-card-title class="text-h6">
                            Available Schedule
                          </v-card-title>
                          <v-card-subtitle>{{ single_time.time_formatted }}</v-card-subtitle>
                        </div>
                        <i class="mdi mdi-check text-success font-weight-bold"
                           style="font-size: 25px"
                           v-show="selectedWorkingHourId == single_time.working_hour_id"></i>
                      </div>
                    </v-card>
                  </v-item>

                </div>

                <div class="modal-footer">
                  <v-container v-show="within24Hours">
                    <p class="text-danger">*You rescheduling a lesson that conduct within 24 hours that's why you see
                      this
                      notice for extra payment.</p>
                    <h6 class="font-weight-bold">
                      Total Payment: ${{ amount * (1) }} + 13% HST
                    </h6>
                    <div class="payment-form">
                      <div id="card-element" class="input-lg form-control cc-number" style="height:40px"></div>
                    </div>
                  </v-container>
                  <v-container>
                    <v-row>
                      <v-col cols="12">
                        <v-btn color="danger" @click="reScheduleDialog = false">
                          Not Now
                        </v-btn>
                        <v-btn color="success" @click="rescheduleConfirmation"
                               :disabled="selectedWorkingHourId == null">
                          Confirm
                        </v-btn>
                      </v-col>
                    </v-row>
                  </v-container>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- cancel dialog -->
        <v-dialog
            v-model="cancelDialog"
            width="500"
        >
          <v-card>
            <v-card-title class="text-h5 grey lighten-2">
              Reason
            </v-card-title>

            <v-textarea
                v-model="cancel.reason"
                placeholder="Enter Your Reason to instructor"
                class="m-2"
                solo
                name="input-7-4"
            ></v-textarea>

            <v-divider></v-divider>

            <v-card-actions>
              <v-btn color="primary" @click="reScheduleDialog = true; getAvailability()">
                Reschedule
              </v-btn>

              <v-spacer></v-spacer>
              <v-btn
                  color="primary"
                  text
                  @click="cancelDialog = false"
              >
                No
              </v-btn>
              <v-btn
                  color="red accent-5"
                  text
                  @click="cancelLesson()"
              >
                Yes
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-card-text class="mt-7 mt-md-0">
          <div class="d-flex justify-content-between">
            <h4 class="font-weight-light text-dark">Training History </h4>
            <div>
              <v-btn text class="text-capitalize" style="font-size: 14px" @click="showFilter = !showFilter">
                <i class="mdi mdi-filter-variant"></i> Filter
              </v-btn>
            </div>
          </div>
          <div class=" row mt-4" v-if="showFilter">
            <div class="col-md-6">
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
            <div class="col-md-6">
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
                    Details
                  </th>
                  <th class="text-left">
                    Action
                  </th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="lesson in lessons.data"
                    :key="lesson.id"
                >
                  <td class="p-2">
                    <img :src="lesson.instructor.avatar" width="70" height="70" class="v-avatar" alt="">
                    {{ lesson.instructor.fullName }}
                  </td>
                  <td>{{ lesson.startAt | dateTime }}</td>
                  <td>{{ lesson.endAt | dateTime }}</td>
                  <td>
                    <v-chip>
                      {{ lesson.status | status }}
                    </v-chip>
                  </td>
                  <td>
                    <v-btn
                        small
                        color="green accent-2"
                        class="text-capitalize text-dark"
                        :to="{name:'trainee-lesson-page', params:{lesson:lesson.id}}"
                    >
                      Details
                    </v-btn>
                    <span v-if="lesson.lesson_type !== 'Bde'">
                      <v-btn
                        v-if="lesson.status === 1"
                        small color="red accent-2" class="text-capitalize text-white"
                        @click="dialogMethod(lesson.id)"
                    >
                      Cancel
                    </v-btn>
                    </span>
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
    </MasterStudentLayout>
  </div>
</template>

<script>
import MasterStudentLayout from "../Layouts/MasterStudentLayout";

let pk = process.env.STRIPE_KEY;
export default {
  name: "TrainingHistory",
  components: {MasterStudentLayout},
  data() {
    return {
      cardElement: null,
      stripe: null,
      amount: 25,
      selectedLessonId: null,
      reschedules: [],
      selectedWorkingHourId: null,
      currentPage: 1,
      reScheduleDialog: false,
      showFilter: false,
      from: null,
      to: null,
      lessons: null,
      cancelDialog: false,
      cancel: {
        lesson_id: null,
        reason: ""
      }
    }
  },
  watch: {
    from() {
      this.getLesson()
    },
    to() {
      this.getLesson()
    },
    reScheduleDialog(newValue) {
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

    this.getLesson();
  },
  computed: {
    within24Hours() {
      if (this.selectedLessonId == null) {
        return false;
      }

      let lesson = this.lessons.data.find(lesson => lesson.id === this.selectedLessonId);

      return this.$root.isDateWithin24(lesson.startAt);
    }
  },
  methods: {
    dialogMethod(id) {
      this.cancelDialog = true;
      this.cancel.lesson_id = id;
      this.selectedLessonId = id;
    },
    async getLesson() {
      try {
        let url = '/v1/drivisa/trainees/lessons';
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
    },
    async cancelLesson() {

      try {
        const url = `/v1/drivisa/trainees/lessons/${this.cancel.lesson_id}/cancel-by-trainee`;
        const {data} = await axios.post(url, {
          reason: this.cancel.reason
        });
        this.cancelDialog = false;

        this.$toasted.success("Cancel Successfully and Refund Initiated")

      } catch (e) {

      }
    },
    async getAvailability() {
      try {
        const url = `/v1/drivisa/trainees/lessons/${this.selectedLessonId}/get-instructor-availability`;
        const {data} = await axios.get(url);
        this.reschedules = data.data.availability;
      } catch (e) {

      }
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
    async rescheduleConfirmation() {
      try {

        let paymentMethodId = null;

        if (this.within24Hours) {
          paymentMethodId = await this.paymentMethod();

          if (!paymentMethodId) {
            this.$toasted.error("Unable to Process the Payment")
            return;
          }

        }


        let url = "/v1/drivisa/trainees/lessons/reschedule";
        const {data} = await axios.post(url, {
          lesson_id: this.selectedLessonId,
          working_hour_id: this.selectedWorkingHourId,
          payment_method_id: paymentMethodId
        })

        await this.getLesson();

        this.$toasted.success(data.message)
        this.reScheduleDialog = false;
        this.cancelDialog = false;
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to reschedule");
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