<template>
  <div>
    <MasterStudentLayout>
      <v-card elevation="0" class="profile_section">
        <v-card-text v-if="profile != null">
          <div class="header">
            <h1>Profile</h1>
          </div>

          <div class="profile_details">
            <img
                alt="profile info"
                class="avatar"
                :src="profile.avatar"
            />
            <div class="d-flex">
              <p class="name">{{ profile.fullName }}</p>
              <v-btn to="/trainee/edit-profile" class="edit_btn edit-button" text>
                ...<i class="mdi mdi-pencil" style="font-size: 18px"></i>
              </v-btn>
            </div>
            <p
                :style="{ color: profile.phoneNumber ? 'black' : 'red' }"
                class="phone"
            >
              {{ profile.phoneNumber || "no phone number here" }}
            </p>
            <p class="email">{{ profile.email }}</p>

          </div>


          <div class="content-section">
            <div class="stat">
              <img alt="" src="/assets/media/imgs/Group.png" style="margin-left: 10px;margin-right: 10px;">
              <div class="details">
                <span class="num">{{ profile.lessons.instructor }} +</span>
                <span> Instructor </span>
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
                <LessonListComponent :lessons="profile.lessons.today"/>
              </div>
              <div class="lessons">
                <h1>Training history</h1>
                <LessonListComponent :lessons="profile.lessons.upcoming"/>
              </div>
            </div>
          </div>
        </v-card-text>
      </v-card>
    </MasterStudentLayout>
  </div>
</template>

<script>
import MasterStudentLayout from "../Layouts/MasterStudentLayout";
import LessonListComponent from "../../../components/Student/Lessons/LessonListComponent";

export default {
  name: "StudentProfile",
  components: {MasterStudentLayout, LessonListComponent},
  computed: {
    profile() {
      return this.$store.state.traineeProfile.profile;
    }
  },
  created() {
    this.$store.dispatch('traineeProfile/getProfile');
  }
}
</script>

<style scoped lang="scss">


.header {
  display: flex;
  justify-content: space-between;
  padding: .5rem;

  h1 {
    font-weight: 600;
    font-size: 25px;
    font-family: Montserrat, sans-serif !important;
    color: rgba(0, 0, 0, .87);
  }

  .edit_btn {
    background-color: lightgrey;

    .mdi {
      margin-right: 0.5rem;
    }
  }
}

.profile_details {
  margin-bottom: 4rem;
  padding: .5rem;
  display: grid;
  grid-template-columns: max-content 1fr;
  grid-column-gap: 1rem;
  grid-template-areas:
    "avatar name"
    "avatar phone"
    "avatar email";

  place-items: center start;
  margin-top: 2rem;

  p {
    margin: 0px;
  }

  .name {
    font-size: 1.4rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
  }

  .email {
    grid-area: email;
  }

  .phone {
    grid-area: phone;
  }

  .avatar {
    grid-area: avatar;
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 50%;
  }
}

.cover-section {
  margin-bottom: 4rem;
  flex-direction: row;
  box-sizing: border-box;
  display: flex;
  justify-content: space-between;
  place-content: flex-end space-between;
  align-items: flex-end;
  height: 300px;
  background-repeat: no-repeat;
  background-position: 50%;
  background-size: cover;
  border-top-left-radius: 6px;
  border-top-right-radius: 6px;
  border-bottom: 1px solid #eee;
}

.flex-box-start {
  flex: 1 1 152px;
  box-sizing: border-box;
  max-width: 152px;
  min-width: 152px;
}

.instructor-avatar {
  flex-direction: column;
  box-sizing: border-box;
  display: flex;
  align-items: center;
  width: 200px;
  height: 60px;
  -o-object-fit: cover;
  object-fit: cover;
  margin: auto;
}

.instructor-avatar img {
  width: 100px;
  height: 100px;
  -o-object-fit: cover;
  object-fit: cover;
  margin: auto;
}

.avatar {
  width: 40px;
  min-width: 40px;
  height: 40px;
  line-height: 40px;
  margin: 0 8px 0 0;
  border-radius: 50%;
  font-size: 17px;
  font-weight: 600;
  text-align: center;
}

.car {
  display: flex;
  align-items: center;
  color: gray;
}

.edit-profile-btn {
  box-sizing: border-box;
  position: relative;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  cursor: pointer;
  outline: none;
  border: none;
  -webkit-tap-highlight-color: transparent;
  display: inline-block;
  white-space: nowrap;
  text-decoration: none;
  vertical-align: baseline;
  text-align: center;
  min-width: 64px;
  line-height: 36px;
  padding: 0 16px;
  border-radius: 4px;
  overflow: visible;
  background-color: #d3d3d3;
  font-size: 15px;
  margin: 5px 15px 5px 0;
  color: black;
}

.car-icon {
  background-repeat: no-repeat;
  fill: currentColor;
  height: 24px;
  width: 24px;
  font-weight: normal;
  font-style: normal;
  font-size: 24px;
  line-height: 1;
  letter-spacing: normal;
  text-transform: none;
  display: inline-block;
  white-space: nowrap;
  word-wrap: normal;
  direction: ltr;
  -webkit-font-feature-settings: 'liga';
  -webkit-font-smoothing: antialiased;
}

.content-section {
  margin-bottom: 4rem;
  margin-right: 5px;
  flex-direction: row;
  box-sizing: border-box;
  display: flex;
  place-content: center space-between;
  align-items: center;
}

@media (max-width: 576px) {
  .content-section {
    margin-bottom: 4rem;
    flex-direction: column;
    box-sizing: border-box;
    display: flex;
    place-content: flex-start;
    align-items: flex-start;
  }
}

.stat {
  display: flex;
  height: 50px;
  align-items: center;
}

.lesson-content-wrapper {
  padding: 0 20px;
  box-sizing: border-box;
}

.lessons {
  margin-bottom: 4rem;
}

.lessons h1 {
  font-weight: 600;
  font-size: 24px;
  color: black;
  font-family: Montserrat, sans-serif !important;
  letter-spacing: normal;
  margin: 0 0 16px;
}

.lessons p {
  font-size: 30px;
  font-weight: 400;
  color: gray;
  text-shadow: 0 20px 105.701px rgba(51, 51, 51, .1);
  text-align: center;
  margin: 1rem 0;
}


</style>