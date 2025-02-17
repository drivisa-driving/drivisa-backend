<template>
  <div>
    <MasterStudentLayout>
      <v-card elevation="0" class="border">
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
                  @click="cancelcourse()"
              >
                Yes
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-card-text class="mt-7 mt-md-0">
          <div class="d-flex justify-content-between">
            <h4 class="font-weight-light text-dark">Courses </h4>
          </div>
          <div class="" v-if="courses && courses.data.length > 0">
            <v-simple-table>
              <template v-slot:default>
                <thead>
                <tr>
                  <th class="text-left">
                    Name
                  </th>
                  <th class="text-left">
                    Credit
                  </th>
                  <th class="text-left">
                    Remaining Credit
                  </th>
                  <th class="text-left">
                    Status
                  </th>
                  <th class="text-left">
                    Action
                  </th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="course in courses.data"
                    :key="course.id"
                >
                  <td>{{ course.package_name }}</td>
                  <td>{{ course.credit }} Hours</td>
                  <td>{{ course.remaining_credit }} Hours</td>
                  <td>
                    <v-chip>
                      {{ course.status }}
                    </v-chip>
                  </td>
                  <td>
                    <v-btn
                        v-if="course.status === 'initiated'"
                        small color="red accent-2" class="text-capitalize text-white"
                        @click="dialogMethod(course.id)"
                    >
                      Cancel
                    </v-btn>
                  </td>
                </tr>
                </tbody>
              </template>
            </v-simple-table>
          </div>
          <div v-else>
            <h2 class="text-muted text-center">There are no course</h2>
          </div>
        </v-card-text>
      </v-card>
    </MasterStudentLayout>
  </div>
</template>

<script>
import MasterStudentLayout from "../Layouts/MasterStudentLayout";

export default {
  name: "Course",
  components: {MasterStudentLayout},
  data() {
    return {
      currentPage: 1,
      showFilter: false,
      courses: null,
      cancelDialog: false,
      cancel: {
        course_id: null,
        reason: ""
      }
    }
  },
  watch: {
    from() {
      this.getCourse()
    },
    to() {
      this.getCourse()
    }
  },
  mounted() {
    this.getCourse();
  },
  methods: {
    dialogMethod(id) {
      this.cancelDialog = true;
      this.cancel.course_id = id;
    },
    async getCourse() {
      try {
        let url = '/v1/drivisa/trainees/courses';
        const {data} = await axios.get(url);
        this.courses = data;
      } catch (e) {

      }
    },
    async cancelcourse() {
      try {
        const url = `/v1/drivisa/trainees/courses/${this.cancel.course_id}/cancel-by-trainee`;
        const {data} = await axios.post(url, {
          reason: this.cancel.reason
        });
        this.cancelDialog = false;
        await this.getCourse()

        this.$toasted.success("Cancel Successfully and Refund Initiated");


      } catch (e) {
        this.$toasted.error(e.response.data.message)
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