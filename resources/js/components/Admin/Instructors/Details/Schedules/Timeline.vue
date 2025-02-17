<template>
  <div>
    <h3 class="text-h5 text-center">Timeline</h3>
    <ul class="header_all_status list-unstyled d-flex text-body mt-3 mb-5">
      <li class="available-status-style mr-8">Available</li>
      <li class="reserved-status-style mr-8">Reserved</li>
      <li class="completed-status-style mr-8">Completed</li>
      <li class="canceled-status-style">Canceled</li>
    </ul>
    <div v-if="schedules.length > 0">
      <div v-for="(workingHour, i) in schedules[0].workingHours" :key="i">
        <div class="card my-3" style="border-radius: 10px">
          <div
            class="d-flex justify-content-between align-items-center py-2 px-5"
            style="border-radius: 10px 10px 0px 0px"
            v-bind:class="{
              'reserved-status':
                getLessonStatus(workingHour) === 1 || workingHour.status === 2,
              'completed-status': getLessonStatus(workingHour) === 3,
              'available-status':
                getLessonStatus(workingHour) === 0 && workingHour.status === 1,
              'progress-status': getLessonStatus(workingHour) === 2,
              'canceled-status': getLessonStatus(workingHour) === 4,
            }"
          >
            <span>
              {{ workingHour.openAt | time }} - {{ workingHour.closeAt | time }}
            </span>
            <span v-if="getLessonStatus(workingHour) !== 0">{{
              getLesson(workingHour).status | status
            }}</span>
          </div>
          <div
            class="d-flex justify-content-start align-items-center p-1 py-2 ml-5"
          >
            <i
              class="fas mdi mdi-map-marker-radius strong"
              style="font-size: 18px; color: #3266cc"
            ></i>
            <span class="ml-2 strong">
              {{ workingHour.point? workingHour.point.sourceAddress:null }}</span
            >
          </div>
          <div
            v-if="getLessonStatus(workingHour) !== 0"
            class="d-flex justify-content-start align-items-center p-1 py-2"
          >
            <div>
              <img
                :src="getLesson(workingHour).trainee.avatar"
                alt="avatar"
                class="v-avatar ml-5"
                style="width: 40px; height: 40px"
              />
            </div>
            <div class="ml-5">
              <h6>{{ getLesson(workingHour).trainee.fullName }}</h6>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Timeline",
  props: ["schedules"],
  methods: {
    getLessonStatus(workingHour) {
      let lesson = this.getLesson(workingHour);
      return lesson ? lesson.status : 0;
    },
    getLesson(workingHour) {
      const lesson = this.schedules[0].lessons.find((item) => {
        return (
          item.startAt === this.schedules[0].date + " " + workingHour.openAt &&
          workingHour.status === 2
        );
      });
      return lesson ? lesson : null;
    },
  },
};
</script>

<style lang="scss" scoped>
@import "resources/js/scss/vue_variables";
.reserved-status {
  background-color: $primary;
  color: white;
}
.available-status {
  background: #46adaa;
  color: white;
}
.completed-status {
  background-color: $green;
  color: white;
}
.progress-status {
  background-color: $progress-dark;
  color: white;
}
.canceled-status {
  background-color: $danger;
  color: white;
}
</style>