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
          <v-btn @click="messagesDialog = false"> Close </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="endLessonDialog" width="500">
      <v-card>
        <h3 class="text-h5 text-center py-3">
          {{
            data.status_text === "completed"
              ? "Add BDE Marking Keys"
              : "End Lesson"
          }}
        </h3>
        <div class="mx-5">
          <div v-if="data.status_text !== 'completed'">
            <v-checkbox
              v-model="fillMarkingKeys"
              label="Do you want to fill Marking Keys?"
              color="indigo"
              @change="getMarkingKeys(data.id)"
            >
            </v-checkbox>
          </div>

          <div v-if="fillMarkingKeys == true">
            <div v-for="marking in markings" :key="marking.id">
              <div class="px-3 pt-2 my-2 border rounded">
                <span
                  ><strong>{{ marking.title }}</strong></span
                >
                <v-radio-group v-model="marking.mark" row class="p-0 m-0 mt-2">
                  <v-radio
                    v-for="(option, i) in options"
                    :label="option.label"
                    :key="i"
                    :value="option.value"
                  ></v-radio>
                </v-radio-group>
              </div>
            </div>
          </div>
        </div>
        <v-card-actions>
          <v-spacer></v-spacer>
          <div class="d-flex justify-content-between px-2 py-3">
            <v-btn
              class="text-white text-capitalize mr-3"
              color="red"
              @click="closeDialog"
            >
              Close
            </v-btn>
            <v-btn
              class="text-white text-capitalize"
              color="green"
              @click="
                $emit(
                  'endBdeLesson',
                  fillMarkingKeys,
                  markings,
                  data.id,
                  (endLessonDialog = false)
                )
              "
            >
              {{
                data.status_text === "completed" ? "Add Markings" : "End Lesson"
              }}
            </v-btn>
          </div>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <BasicDatatable :data="lessons">
      <Column sortable header="">
        <template #body="slotProps">
          <img
            :src="slotProps.data.trainee.avatar"
            alt="avatar"
            class="v-avatar"
            style="width: 80px; height: 80px"
          />
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
              <v-list-item
                v-if="
                  slotProps.data.is_refund_initiated === 0 &&
                  getCurrentDateTime() > slotProps.data.startAt &&
                  slotProps.data.status_text === 'reserved' &&
                  slotProps.data.lesson_type !== 'Bde'
                "
                @click="$emit('initiateRefund', slotProps.data.id)"
              >
                Refund
              </v-list-item>
              <v-list-item
                v-if="
                  slotProps.data.is_refund_initiated === 0 &&
                  getCurrentDateTime() > slotProps.data.startAt &&
                  (slotProps.data.status_text === 'reserved' ||
                    slotProps.data.status_text === 'inProgress') &&
                  slotProps.data.lesson_type !== 'Bde'
                "
                @click="$emit('endLesson', slotProps.data.id)"
              >
                End Lesson
              </v-list-item>
              <v-list-item
                v-else-if="
                  slotProps.data.is_refund_initiated === 0 &&
                  getCurrentDateTime() > slotProps.data.startAt &&
                  (slotProps.data.status_text === 'reserved' ||
                    slotProps.data.status_text === 'inProgress') &&
                  slotProps.data.lesson_type === 'Bde'
                "
                @click="dialogMethod(slotProps.data)"
              >
                End Lesson
              </v-list-item>
              <v-list-item
                v-else-if="
                  slotProps.data.status_text === 'completed' &&
                  slotProps.data.lesson_type === 'Bde' &&
                  slotProps.data.bdeLog == null
                "
                @click="fillMarkings(slotProps.data)"
              >
                Add Markings
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
      endLessonDialog: false,
      fillMarkingKeys: "",
      markingKeys: [],
      options: [
        { label: "E", value: 1 },
        { label: "M", value: 2 },
        { label: "P", value: 3 },
        { label: "N", value: 4 },
      ],
      markings: [],
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
    dialogMethod(data) {
      this.data = data;
      this.endLessonDialog = true;
    },
    fillMarkings(data) {
      this.data = data;
      this.getMarkingKeys(data.id);
      this.fillMarkingKeys = true;
      this.endLessonDialog = true;
    },
    closeDialog() {
      this.endLessonDialog = false;
      this.fillMarkingKeys = "";
    },
    async getMarkingKeys(id) {
      try {
        let url = "/v1/drivisa/admin/instructors/bde/" + id + "/marking-keys";
        const { data } = await axios.get(url);
        this.markingKeys = data;
        this.markingKeys = data.data;

        this.markings = [];
        data.data.forEach((item) => {
          this.markings.push({
            marking_key_id: item.id,
            title: item.title,
            mark: null,
          });
        });
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