<template>
  <MasterAdminLayout>
    <v-dialog v-model="lessonDetailsDialog" width="500">
      <v-card>
        <LessonDetails :lesson="lesson" />
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="lessonDetailsDialog = false"> Close </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="evaluationsDialog" width="500">
      <v-card>
        <EvaluationDetails :lesson="lesson" />
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="evaluationsDialog = false"> Close </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="messagesDialog" width="500">
      <v-card>
        <MessageDetails :data="data" :messages="messages" />
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="messagesDialog = false"> Close </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <div>
      <v-card class="border">
        <v-card-text>
          <div class="page-header pr-0">
            <p class="page-heading">Today Lessons</p>
            <div class="border" style="border-radius: 5px">
              <v-btn
                text
                class="text-capitalize primary btn-outline"
                style="font-size: 14px"
                @click="showFilter = !showFilter"
              >
                <i class="mdi mdi-filter-variant"></i> Filter
              </v-btn>
            </div>
          </div>

          <div
            class="row m-0 mb-3 border"
            style="border-radius: 5px"
            v-if="showFilter"
          >
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
                <v-date-picker v-model="from" min="1950-01-01"></v-date-picker>
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
                <v-date-picker v-model="to" min="1950-01-01"></v-date-picker>
              </v-menu>
            </div>
          </div>

          <BasicDatatable :data="data">
            <Column sortable field="no" header="Lesson No."></Column>
            <Column sortable field="lesson_type" header="Lesson Type">
              <template #body="slotProps">
                <span v-if="slotProps.data.lesson_type === 'Driving'">
                  In Car Private Lesson
                </span>
                <span v-else-if="slotProps.data.lesson_type === 'Bde'">
                  BDE
                </span>
                <span v-else> {{ slotProps.data.lesson_type }} </span>
              </template>
            </Column>
            <Column sortable field="status_text" header="Status">
              <template #body="slotProps">
                <p class="text-center">
                  <v-chip
                    class="expired"
                    v-if="
                      (getCurrentDateTime() > slotProps.data.startAt &&
                        slotProps.data.status_text === 'reserved') ||
                      (getCurrentDateTime() > slotProps.data.startAt &&
                        slotProps.data.status_text === 'inProgress')
                    "
                  >
                    Expired
                  </v-chip>
                  <v-chip
                    v-else
                    class="text-white"
                    :class="{
                      reserved:
                        slotProps.data.status === 1 ||
                        slotProps.data.status === 2,
                      completed: slotProps.data.status === 3,
                      red: slotProps.data.status === 4,
                      secondary: slotProps.data.status === 5,
                    }"
                  >
                    {{ slotProps.data.status_text }}
                  </v-chip>
                  <br />
                  <small
                    v-if="
                      slotProps.data.status === 4 &&
                      slotProps.data.lesson_cancellation
                    "
                    >by
                    {{ slotProps.data.lesson_cancellation.cancel_by }}</small
                  >
                  <small v-if="slotProps.data.is_refund_initiated != 0">
                    refunded</small
                  >
                </p>
              </template>
            </Column>
            <Column
              sortable
              field="startAt_formatted"
              header="Start At"
            ></Column>
            <Column sortable field="endAt_formatted" header="End At"></Column>
            <Column
              sortable
              field="instructor_details.full_name"
              header="Instructor Name"
            >
              <template #body="slotProps">
                <router-link
                  :to="
                    '/admin/instructors/details/' +
                    slotProps.data.instructor_details.id
                  "
                >
                  {{ slotProps.data.instructor_details.full_name }}
                </router-link>
              </template>
            </Column>
            <Column sortable field="trainee.fullName" header="Trainee Name">
              <template #body="slotProps">
                <router-link
                  :to="{
                    name: 'admin-trainees-details',
                    params: { id: slotProps.data.trainee_id },
                  }"
                >
                  {{ slotProps.data.trainee.fullName }}
                </router-link>
              </template>
            </Column>
            <Column field="" header="Details">
              <template #body="slotProps">
                <v-menu left>
                  <template v-slot:activator="{ on, attrs }">
                    <span
                      class="mdi mdi-dots-vertical option-icon text-warning text-large float-left option-icon"
                      v-bind="attrs"
                      v-on="on"
                    ></span>
                  </template>
                  <v-list>
                    <v-list-item @click="lessonDetails(slotProps.data)">
                      Lesson Details
                    </v-list-item>
                    <v-list-item
                      v-if="
                        slotProps.data.status_text === 'completed' &&
                        slotProps.data.lesson_type === 'Driving'
                      "
                      @click="evaluationDetails(slotProps.data)"
                    >
                      Evaluations
                    </v-list-item>
                    <v-list-item @click="getMessages(slotProps.data)">
                      View Messages
                    </v-list-item>
                  </v-list>
                </v-menu>
              </template>
            </Column>
          </BasicDatatable>
        </v-card-text>
      </v-card>
    </div>
  </MasterAdminLayout>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import BasicDatatable from "../../../components/Global/BasicDatatable.vue";
import LessonDetails from "../../../components/Global/LessonDetails/LessonDetailsDialog.vue";
import EvaluationDetails from "../../../components/Global/LessonDetails/EvaluationDetails.vue";
import MessageDetails from "../../../components/Global/LessonDetails/MessageDetails.vue";
export default {
  name: "TodayLessons",
  components: {
    BasicDatatable,
    MasterAdminLayout,
    LessonDetails,
    EvaluationDetails,
    MessageDetails,
  },
  data() {
    return {
      data: [],
      lesson: {},
      lessonDetailsDialog: false,
      evaluationsDialog: false,
      showFilter: false,
      from: null,
      to: null,
      currentPage: 1,
      messages: [],
      messagesDialog: false,
    };
  },
  watch: {
    from() {
      this.getTodayLessons();
    },
    to() {
      this.getTodayLessons();
    },
  },
  mounted() {
    this.getTodayLessons();
    this.getMessages();
  },
  methods: {
    getCurrentDateTime() {
      let tzoffset = new Date().getTimezoneOffset() * 60000;
      let dateTime = new Date(Date.now() - tzoffset)
        .toISOString()
        .slice(0, 19)
        .replace("T", " ");

      return dateTime;
    },
    lessonDetails(lesson) {
      this.lesson = lesson;
      this.lessonDetailsDialog = true;
    },
    evaluationDetails(lesson) {
      this.lesson = lesson;
      this.evaluationsDialog = true;
    },
    getAddress(address) {
      if (address) {
        return address.address;
      }
    },
    totalEarning(instructorLessonCost, additionalKM) {
      return (instructorLessonCost + additionalKM).toFixed(2);
    },
    async getTodayLessons() {
      try {
        let url = "/v1/drivisa/admin/lessons/today-lessons";
        let params = new URLSearchParams();
        if (this.from) {
          params.append("start_at", this.from);
        }
        if (this.to) {
          params.append("end_at", this.to);
        }

        params.append("page", this.currentPage);

        url += "?" + params.toString();
        const { data } = await axios.get(url);
        this.data = data;
        this.data = data.data;
        this.totalItems = data.meta.total;
        this.currentPage = this.data.meta.current_page;
      } catch (e) {}
    },
    async getMessages(lesson) {
      try {
        if (lesson) {
          let url = "/v1/drivisa/webhook/messages/" + lesson.id;
          const { data } = await axios.get(url);
          this.messages = data;
          this.messages = data.data;
          this.data = lesson;
          this.messagesDialog = true;
        }
      } catch (e) {}
    },
  },
};
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