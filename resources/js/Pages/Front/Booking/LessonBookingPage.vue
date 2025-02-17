<template>
  <div>
    <Header/>
    <AdditionalKMPolicyDialogComponent ref="dialogComponent"/>
    <div class="d-container my-5">
      <div class="row">
        <div class="col-lg-9">
          <BookingLessonDetail
              :pick_drop="pick_drop"
              :lesson="lesson"
              :instructor_id="lesson.point.instructorId"
              @pick_drop_ok="updatePickDrop"
              @update_cost="(additionalCost) => this.additionalCost = additionalCost"
          />
        </div>
        <div class="col-lg-3">
          <div class="col-12 p-0" v-if="additionalCost">
            <ul class="list-group p-0">
              <li class="list-group-item"><strong>Total Extra KM:</strong>
                <span class="float-right">
                  {{ (parseFloat(additionalCost.totalDistance) - parseFloat(additionalCost.free_km)).toFixed(2) }}
                </span></li>
              <li class="list-group-item">
                <strong>Lesson Tax:</strong>
                <span class="float-right">${{ additionalCost.lesson_cost.costs.tax }}</span>
              </li>
              <li class="list-group-item">
                <strong>Tax:</strong>
                <span class="float-right">${{ parseFloat(additionalCost.tax).toFixed(2) }}</span>
              </li>
              <li class="list-group-item">
                <strong>Additional Cost:
                  <i
                      class="mdi mdi-information-outline"
                      @click="$refs.dialogComponent.openDialog()"
                  ></i>
                </strong>
                <span class="float-right">${{ parseFloat(additionalCost.cost).toFixed(2) }}</span>
              </li>
              <li class="list-group-item">
                <strong>Lesson Cost:</strong>
                <span class="float-right">${{ additionalCost.lesson_cost.costs.cost }}</span>
              </li>
              <li
                  class="list-group-item text-white" style="background: #3f51b5">
                <strong>Total:</strong>
                <span class="float-right">${{ total.toFixed(2) }}</span></li>
            </ul>
          </div>
          <div class="col-12 p-0" v-else>
            <ul class="p-0 mb-0">
              <li class="list-group-item">
                <strong>Lesson Tax:</strong>
                <span class="float-right">${{ lesson.costs.tax }}</span>
              </li>
              <li class="list-group-item">
                <strong>Lesson Cost:</strong>
                <span class="float-right">${{ lesson.costs.cost }}</span>
              </li>
              <li class="list-group-item text-white" style="background: #3f51b5">
                <strong>Total:</strong>
                <span class="float-right">${{ lesson.costs.cost + lesson.costs.tax }}</span></li>
            </ul>
          </div>
          <BookingPaymentCard
              :lesson="lesson"
              :add_cost="additionalCost"
              :pick_drop="pick_drop"
          />
        </div>
      </div>
    </div>
    <Footer/>
  </div>
</template>

<script>
import Footer from "../../../components/Front/Footer";
import Header from "../../../components/Front/Header";
import BookingLessonDetail from "../../../components/Front/Booking/BookingLessonDetail";
import BookingPaymentCard from "../../../components/Front/Booking/BookingPaymentCard";
import AdditionalKMPolicyDialogComponent from "../../../components/Front/Booking/AdditionalKMPolicyDialogComponent";

export default {
  name: "LessonBookingPage",
  components: {Header, Footer, BookingLessonDetail, BookingPaymentCard, AdditionalKMPolicyDialogComponent},
  data() {
    return {
      lesson: null,
      additionalCost: null,
      pick_drop: {
        type: "default",
        drop_lat: "",
        drop_long: "",
        pick_lat: "",
        pick_long: "",
        pick_address: "",
        drop_address: ""
      }
    }
  },
  mounted() {
    this.getBookingLesson();
  },
  computed: {
    instructor_username() {
      return this.$route.query.instructor;
    },
    lesson_id() {
      return this.$route.query.lesson;
    },
    total() {
      let cost = 0;
      if (this.additionalCost) {
        cost = parseFloat(this.additionalCost.cost)
            + parseFloat(this.additionalCost.tax)
            + parseFloat(this.additionalCost.lesson_cost.costs.cost)
            + parseFloat(this.additionalCost.lesson_cost.costs.tax);
      }

      return cost;
    }
  },
  methods: {
    async updatePickDrop(pickPoint, dropPoint, isSameAsPick) {
      if (isSameAsPick) {
        this.pick_drop = {
          type: "pick-drop",
          drop_lat: pickPoint.lat,
          drop_long: pickPoint.lng,
          pick_lat: pickPoint.lat,
          pick_long: pickPoint.lng,
          pick_address: pickPoint.address,
          drop_address: pickPoint.address
        }
      } else {
        this.pick_drop = {
          type: "pick-drop",
          drop_lat: dropPoint.lat,
          drop_long: dropPoint.lng,
          pick_lat: pickPoint.lat,
          pick_long: pickPoint.lng,
          pick_address: pickPoint.address,
          drop_address: dropPoint.address
        }
      }
    },
    async getBookingLesson() {
      try {
        const url = "/v1/drivisa/workingHours/" + this.lesson_id;
        const {data} = await axios.get(url);
        this.lesson = data.data;
      } catch (e) {

      }
    }
  }
}
</script>

<style scoped>

</style>