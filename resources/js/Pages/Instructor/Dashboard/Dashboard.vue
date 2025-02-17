<template>
  <div>
    <MasterInstructorLayout>
      <v-card >
        <v-tabs centered>
          <v-tab>
            Today Lessons
          </v-tab>
          <v-tab>
            Upcoming Lesson
          </v-tab>
          <v-tab>
            Training History
          </v-tab>

          <v-tab-item>
            <InstructorLessonListComponent :lessons="profile.lessons.today"/>
          </v-tab-item>
          <v-tab-item>
            <InstructorLessonListComponent :lessons="profile.lessons.upcoming"/>
          </v-tab-item>
          <v-tab-item>
            <TrainingHistory/>
          </v-tab-item>
        </v-tabs>
      </v-card>
    </MasterInstructorLayout>
  </div>
</template>

<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";
import TrainingHistory from "../../../components/Instructor/Dashboard/TrainingHistory";
import InstructorLessonListComponent from "../../../components/Instructor/Lessons/InstructorLessonListComponent";

export default {
  name: "Dashboard",
  components: {
    InstructorLessonListComponent,
    TrainingHistory,
    MasterInstructorLayout,
  },
  computed: {
    profile() {
      return this.$store.state.instructorProfile.profile;
    }
  },
  created() {
    this.$store.dispatch('instructorProfile/getProfile');
  },
  data() {
    return {}
  }
}
</script>