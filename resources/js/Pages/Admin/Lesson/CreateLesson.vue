<template>
  <div>
    <master-admin-layout>
      <v-card>
        <v-card-text>
          <div class="page-header pr-0 pl-0">
            <p class="page-heading">Create Lesson</p>
          </div>

          <div class="row mt-3">
            <div class="col-6">
              <v-autocomplete
                :items="trainees"
                outlined
                dense
                label="Select Trainee *"
                item-text="full_name"
                item-value="id"
                height="54px"
                v-model="requestData.trainee_id"
              >
              </v-autocomplete>
            </div>
            <div class="col-md-6">
              <v-autocomplete
                :items="instructors"
                outlined
                dense
                label="Select Instructor *"
                item-text="full_name"
                item-value="id"
                height="54px"
                v-model="requestData.instructor_id"
              >
              </v-autocomplete>
            </div>
            <div class="col-md-6">
              <v-autocomplete
                :items="courses"
                outlined
                dense
                label="Select Course *"
                item-text="text"
                item-value="text"
                height="54px"
                @change="setLessonType"
                v-model="requestData.courseType"
              ></v-autocomplete>
            </div>
            <div class="col-md-6">
              <v-text-field
                disabled
                outlined
                label="Lesson Type *"
                placeholder="Enter Lesson Type * "
                v-model="requestData.lessonType"
              >
              </v-text-field>
            </div>
            <div class="col-12 pt-0 mb-4">
              <Calendar
                id="calendar-12h"
                showTime
                hourFormat="12"
                style="width: 100%; height: 50px; border-radius: 5px"
                placeholder="Select Date Time *"
                touchUI
                v-model="requestData.dateTime"
              />
            </div>
            <div class="col-md-6">
              <v-autocomplete
                :items="shift"
                outlined
                dense
                label="Select Duration (in hours) *"
                item-text="value"
                item-value="value"
                height="54px"
                v-model="requestData.duration"
              >
              </v-autocomplete>
            </div>
            <div class="col-md-6">
              <v-autocomplete
                :items="lesson_status"
                outlined
                dense
                label="Select Status *"
                item-text="text"
                item-value="value"
                height="54px"
                v-model="requestData.lessonStatus"
              >
              </v-autocomplete>
            </div>

            <v-btn
              color="primary btn-outline"
              class="text-capitalize ml-3 mb-3"
              @click="createLesson"
              :disabled="isCreatingLesson"
            >
              {{ isCreatingLesson ? "Creating..." : "Create" }}
            </v-btn>
          </div>

          <div class="mt-5 border-top">
            <h4 class="mt-2">History</h4>
            <create-lesson-history ref="table" />
          </div>
        </v-card-text>
      </v-card>
    </master-admin-layout>
  </div>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout.vue";
import CreateLessonHistory from "../../../components/Admin/Lesson/CreateLessonHistory.vue";

export default {
  components: { MasterAdminLayout, CreateLessonHistory },
  name: "CreateLesson",
  data() {
    return {
      trainees: [],
      instructors: [],
      requestData: {
        trainee_id: null,
        instructor_id: null,
        courseType: "",
        lessonType: "",
        dateTime: "",
        duration: "",
        lessonStatus: "",
      },
      courses: [{ text: "BDE" }, { text: "Package" }],
      shift: [{ value: 1 }, { value: 2 }],
      lesson_status: [
        { value: "completed", text: "Completed" },
        { value: "completedPay", text: "Completed + Pay" },
      ],
      isCreatingLesson: false,
    };
  },
  mounted() {
    this.getTrainees();
    this.getInstructors();
  },
  methods: {
    updateTable() {
      this.$refs.table.getLessonsHistory();
    },
    setLessonType($event) {
      if ($event === "BDE") {
        this.requestData.lessonType = "BDE";
      } else {
        this.requestData.lessonType = "In Car Private Lesson";
      }
    },
    async getTrainees() {
      try {
        const url = "/v1/drivisa/admin/trainees/all";
        const { data } = await axios.get(url);
        this.trainees = data;
      } catch (e) {}
    },
    async getInstructors() {
      try {
        const url = "/v1/drivisa/admin/instructors/all";
        const { data } = await axios.get(url);
        this.instructors = data;
      } catch (e) {}
    },
    async createLesson() {
      try {
        this.isCreatingLesson = true;
        const url = "/v1/drivisa/admin/lessons/create-lesson";
        const { data } = await axios.post(url, this.requestData);
        this.$toasted.success(data);
        this.clearForm();
        this.updateTable();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to create lesson");
      } finally {
        this.isCreatingLesson = false;
      }
    },
    clearForm() {
      this.requestData = {
        trainee_id: null,
        instructor_id: null,
        courseType: "",
        lessonType: "",
        dateTime: "",
        duration: "",
        lessonStatus: "",
      };
    },
  },
};
</script>