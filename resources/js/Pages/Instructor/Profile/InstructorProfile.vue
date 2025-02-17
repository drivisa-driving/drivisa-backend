<template>
  <div>
    <MasterInstructorLayout>
      <v-card elevation="0" class="profile_section">
        <v-card-text v-if="profile != null">
          <div class="cover-section" :style="{ backgroundImage: 'url(' + profile.cover + ')' }">
            <div class="flex-box-start"></div>
            <div class="instructor-avatar">
              <img class="avatar"
                   :src="profile.avatar">
              <h5 class="font-weight-bold my-2">{{ profile.fullName }}</h5>
              <div class="car">
                <i class="car-icon mdi mdi-car"></i>
                <span v-if="profile.cars.length > 0">
                  {{ profile.cars[0].generation }}
                  {{ profile.cars[0].make }}
                  {{ profile.cars[0].model }}
                </span>
                <span v-else>Not Provided</span>
              </div>
              <div class="star-rating d-flex justify-content-center">
                <v-rating
                    v-model="profile.evaluation.avg"
                    readonly
                    background-color="orange darken"
                    color="orange"
                    icon-label="custom icon label text"
                ></v-rating>
              </div>
            </div>
            <div>
              <router-link to="/instructor/edit-profile" class="edit-profile-btn">
                <i class="mdi mdi-pencil"></i>
                <span class="d-none d-md-block">Edit Profile</span>
              </router-link>
            </div>
          </div>
          <div class="content-section">
            <div class="stat">
              <img alt="" src="/assets/media/imgs/Group.png" style="margin-left: 10px;margin-right: 10px;">
              <div class="details">
                <span class="num">{{ profile.lessons.trainee }} +</span>
                <span> Trainee </span>
              </div>
            </div>
            <div class="stat">
              <img alt="" src="/assets/media/imgs/Group.png" style="margin-left: 10px;margin-right: 10px;">
              <div class="details">
                <span class="num">{{ profile.lessons.hours }} +</span>
                <span> Training Hours  </span>
              </div>
            </div>
            <div class="stat">
              <img alt="" src="/assets/media/imgs/Group.png" style="margin-left: 10px;margin-right: 10px;">
              <div class="details">
                <span class="num">{{ profile.lessons.count }} +</span>
                <span> Lessons </span>
              </div>
            </div>
          </div>
          <div class="lesson-content-wrapper">
            <div>
              <div class="lessons">
                <h1>Today Lessons</h1>
                <InstructorLessonListComponent :lessons="profile.lessons.today"/>
                <router-link to="/instructor/upcoming-lessons"
                             class="font-weight-light float-right text-dark"
                >See All
                </router-link>
              </div>
              <div class="lessons">
                <h1>Upcoming lesson</h1>
                <InstructorLessonListComponent :lessons="profile.lessons.upcoming"/>
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
import InstructorLessonListComponent from "../../../components/Instructor/Lessons/InstructorLessonListComponent";

export default {
  name: "InstructorProfile",
  components: {MasterInstructorLayout, InstructorLessonListComponent},
  computed: {
    profile() {
      return this.$store.state.instructorProfile.profile;
    }
  },
  created() {
    this.$store.dispatch('instructorProfile/getProfile');
  }
}
</script>