<template>
  <div>
    <Header/>
    <div class="container-fluid px-0 profile_section" v-if="trainee != null">
      <div class="cover-section" :style="{ backgroundImage: 'url('+ trainee.cover +')' }">
        <div class="instructor-avatar">
          <img class="avatar" style="margin-bottom: 10px;"
               :src="trainee.avatar">
          <h5 class="font-weight-bold mb-3">{{ trainee.fullName }}</h5>
        </div>
      </div>

      <div class="d-container pt-4">
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive">
              <table class="table table-bordered" v-if="trainee.lessons.length > 0">
                <tr>
                  <td width="50%"></td>
                  <td v-for="lesson in trainee.lessons" :key="lesson.id">
                    {{ lesson.endedAt | date }}
                  </td>
                </tr>
                <tr v-for="evalu in trainee.evaluations" :key="evalu.id">
                  <td>{{ evalu.title }}</td>
                  <td v-for="lesson in trainee.lessons" :key="lesson.id">
                    {{ evalu['lesson-' + lesson.id] }}
                  </td>
                </tr>
              </table>
              <div class="text-center" v-else>
                <h2>There Are No Lessons</h2>
              </div>
            </div>
            <div class="comment_card">
              <div class="card p-4" v-for="review in trainee.reviews">
                <div class="user_info">
                  <img class="avatar" style="margin-bottom: 10px;" :src="trainee.avatar"/>
                </div>
                <div class="comment" >
                  <div class="star_rating">
                  </div>
                  <p class="mb-0">{{ review.instructor_name }}</p>
                  <p>{{  review.review }}</p>
                  <small class="date-info">{{ review.lesson_date_formatted}}</small>
                </div>
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
import Header from "../../../components/Front/Header";
import Footer from "../../../components/Front/Footer";

export default {
  name: "TraineeProfile",
  components: {Header, Footer},
  data() {
    return {
      trainee: null
    }
  },
  mounted() {
    this.getTraineeProfile();
  },
  methods: {
    async getTraineeProfile() {
      try {
        let trainee_id = this.$route.params.trainee_id;
        const url = "/v1/drivisa/trainees/" + trainee_id;
        const {data} = await axios.get(url);
        this.trainee = data.data;
      } catch (e) {

      }
    }
  }
}
</script>

<style scoped lang="scss">

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
  height: 0;
  -o-object-fit: cover;
  object-fit: cover;
  margin: auto;

  .star_rating {
    text-align: center !important;
    margin-top: 20px;

    i {
      color: orange;
      vertical-align: middle;
      fill: currentColor;
      font-size: 24px;
      margin: 7px;
    }
  }
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
</style>