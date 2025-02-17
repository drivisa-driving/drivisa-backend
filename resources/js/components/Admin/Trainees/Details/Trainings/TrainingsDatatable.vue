<template>
  <div>
    <v-dialog v-model="lessonDetailsDialog" width="500">
      <v-card>
        <LessonDetailsDialog :lesson="data" />
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="lessonDetailsDialog = false"> Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="evaluationsDialog" width="500">
      <v-card>
        <EvaluationDetails :lesson="data" />
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="evaluationsDialog = false"> Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="messagesDialog" width="500">
      <v-card>
        <MessageDetails :data="data" :messages="messages" />
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="messagesDialog = false"> Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <BasicDatatable :data="lessons">
      <Column sortable header="">
        <template #body="slotProps">
          <img
            :src="slotProps.data.instructor.avatar"
            alt="avatar"
            class="v-avatar"
            style="width: 80px; height: 80px"
          />
        </template>
      </Column>
      <Column sortable field="instructor.fullName" header="Instructor Name">
        <template #body="slotProps">
          <router-link
            :to="{
              name: 'admin-instructor-details',
              params: { id: slotProps.data.instructor_id },
            }"
          >
            {{ slotProps.data.instructor.fullName }}
          </router-link>
        </template>
      </Column>
      <Column sortable field="no" header="Lesson No."></Column>
      <Column sortable field="lesson_type" header="Lesson Type">
        <template #body="slotProps">
          <span v-if="slotProps.data.lesson_type === 'Driving'">
            In Car Private Lesson
          </span>
          <span v-else-if="slotProps.data.lesson_type === 'Bde'"> BDE </span>
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
                  slotProps.data.status === 1 || slotProps.data.status === 2,
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
              >by {{ slotProps.data.lesson_cancellation.cancel_by }}</small
            >
            <small v-if="slotProps.data.is_refund_initiated != 0">
              refunded</small
            >
          </p>
        </template>
      </Column>
      <Column sortable field="startAt_formatted" header="Start"></Column>
      <Column sortable field="endAt_formatted" header="End"></Column>
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
  </div>
</template>

<script>
import BasicDatatable from "../../../../Global/BasicDatatable.vue";
import LessonDetailsDialog from "../../../../Global/LessonDetails/LessonDetailsDialog.vue";
import EvaluationDetails from "../../../../Global/LessonDetails/EvaluationDetails.vue";
import MessageDetails from "../../../../Global/LessonDetails/MessageDetails.vue";
export default {
  name: "TrainingsDatatable",
  components: {
    BasicDatatable,
    MessageDetails,
    EvaluationDetails,
    LessonDetailsDialog,
  },
  props: ["lessons"],
  data() {
    return {
      data: {},
      lessonDetailsDialog: false,
      evaluationsDialog: false,
      messages: [],
      messagesDialog: false,
    };
  },
  mounted() {
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
    lessonDetails(data) {
      this.data = data;
      this.lessonDetailsDialog = true;
    },
    evaluationDetails(data) {
      this.data = data;
      this.evaluationsDialog = true;
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