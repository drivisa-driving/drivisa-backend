<template>
  <div>
    <MasterStudentLayout>
      <v-card elevation="0" class="border">
        <v-card-text class="mt-7 mt-md-0" v-if="lesson">
          <div class="lesson">
            <img :src="lesson.instructor.avatar" class="avatar" width="70px" height="70px" alt="">
            <div>
              <h5 class="name text-dark">{{ lesson.instructor.fullName }}</h5>
              <span class="date">{{ lesson.startAt | date }}</span>
            </div>
            <div class="status">
              <v-chip pill small>{{ lesson.status | status }}</v-chip>
            </div>
            <div class="location">
              <i class="mdi mdi-map-marker"></i>&nbsp; <span>{{ lesson.pickupPoint.address }}</span>
            </div>
            <div class="time">
              <i class="mdi mdi-clock"></i>&nbsp;
              <span>{{ lesson.startAt | dateTime(false) }} - {{ lesson.endAt | dateTime(false) }}</span>
            </div>
          </div>

          <div class="mb-5" v-if="lesson.status === 4 && this.lesson.is_refunded == null">
            <div class="col-md-6">
              <v-select
              :items="choices"
              item-text="label"
              item-value="value"
              outlined
              label="Refund Choice"
              placeholder="Refund Choice"
              v-model="refundChoices.refund_choice"
            ></v-select>
            <v-btn color="primary"
            @click="refundChoice"
              >
                Refund
              </v-btn>
            </div>
          </div>

          <div class="col-12">
            <h4 class="text-dark">Instructor Evaluation:</h4>
            <div class="notes">
              Note:
              {{ lesson.instructorNote }}
            </div>
          </div>

          <v-col cols="12" v-for="evaluation in lesson.instructorEvaluations" :key="evaluation.id">
            <v-subheader class="pl-0 mb-3">
              {{ evaluation.title }}
            </v-subheader>
            <v-slider
                :value="evaluation.value"
                :max="evaluation.points == null? 10: evaluation.points"
                step="1"
                ticks="always"
                :tick-size="5"
                :thumb-size="20"
                thumb-label="always"
                :readonly="true"
            ></v-slider>
          </v-col>

          <div class="col-12">
            <h4 class="text-dark">Your Evaluation:</h4>
            <div class="notes" v-if="lesson.traineeNote">
              Note:
              {{ lesson.traineeNote }}
            </div>
            <div v-else>
              <v-textarea
                  placeholder="Enter Your Note to instructor"
                  v-model="traineeRequestData.trainee_note"
              />
            </div>
            <div class="star-rating d-flex justify-content-center">
              <star-rating
                  class="border p-2 custom-star"
                  :show-rating="false"
                  :star-size="24"
                  :read-only="this.lesson.traineeEvaluation != null"
                  v-model="traineeRequestData.trainee_evaluation"
              />
            </div>
            <div class="d-flex justify-content-center mt-4">
              <v-btn color="primary"
                     @click="setEvaluation"
                     :disabled="this.lesson.traineeEvaluation != null"
              >
                Save
              </v-btn>
            </div>
          </div>
        </v-card-text>
      </v-card>
    </MasterStudentLayout>
  </div>
</template>

<script>
import MasterStudentLayout from "../Layouts/MasterStudentLayout";
import StarRating from 'vue-star-rating';

export default {
  name: "TrainingDetail",
  components: {MasterStudentLayout, StarRating},
  data() {
    return {
      lesson: null,
      traineeRequestData: {
        trainee_evaluation: 0,
        trainee_note: null
      },
      refundChoices:{
        refund_choice: null
      },
      choices: [
        { value: 1, label: "Credit"},
        { value: 2, label: "Cash" },
      ],
    }
  },
  mounted() {
    this.getLesson();
  },
  methods: {
    async getLesson() {
      try {
        let lesson_id = this.$route.params.lesson;
        let url = '/v1/drivisa/trainees/lessons/' + lesson_id;
        const {data} = await axios.get(url);
        this.lesson = data.data;

        this.traineeRequestData.trainee_note = this.lesson.traineeNote
        if (this.lesson.traineeEvaluation != null) {
          this.traineeRequestData.trainee_evaluation = this.lesson.traineeEvaluation.value;
        }
      } catch (e) {

      }
    },
    async setEvaluation() {
      try {
        let lesson_id = this.$route.params.lesson;
        const url = `/v1/drivisa/trainees/lessons/${lesson_id}/update-evaluation`;
        const {data} = await axios.post(url, this.traineeRequestData);
        this.lesson = data.data;

        this.traineeRequestData.trainee_note = this.lesson.traineeNote

        if (this.lesson.traineeEvaluation != null) {
          this.traineeRequestData.trainee_evaluation = this.lesson.traineeEvaluation.value;
        }
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to set evaluation");
      }
    },
    async refundChoice() {
      try {
        let lesson_id = this.$route.params.lesson;
        const url = `/v1/drivisa/trainees/lessons/${lesson_id}/refund-choice`;
        const {data} = await axios.post(url, this.refundChoices);

        await this.getLesson();
        
        this.$toasted.success("Refunded Successfully");

      } catch (e) {
        this.$root.handleErrorToast(e, "Already Refunded");
      }
    }
  }
}
</script>

<style scoped>
.lesson {
  display: grid;
  padding: 1rem;
  grid-template-columns: 70px max-content max-content auto max-content;
  grid-column-gap: 10px;
  grid-template-areas: "avatar name status . time" "avatar date ...... . location";
  place-items: center start;
  margin: 32px 0;
}

.avatar {
  grid-area: avatar;
  width: 60px;
  height: 60px;
  -o-object-fit: cover;
  object-fit: cover;
  border-radius: 50%;
}

.name {
  grid-area: name;
  font-weight: 700;
}

.date {
  grid-area: date;
  color: gray;
}

.status {
  grid-area: status;
  padding: 2px 6px;
  margin: 0;
  border-radius: 10px;
  place-self: center;
}

.location {
  grid-area: location;
  display: flex;
  align-items: center;
}

.time {
  grid-area: time;
  display: flex;
  align-items: center;
}

.location *, .time * {
  background-repeat: no-repeat;
  display: inline-block;
  font-size: 20px;
  color: rgba(0, 0, 0, .87);
}

.notes {
  background-color: rgba(51, 102, 204, .1);
  color: gray;
  padding: 22px;
  margin-block: 20px;
}

</style>