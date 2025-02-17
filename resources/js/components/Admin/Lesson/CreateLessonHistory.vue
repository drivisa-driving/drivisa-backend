<template>
  <div>
    <basic-datatable :data="data">
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
      <Column sortable field="startAt_formatted" header="Start At"></Column>
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
            <v-list v-if="slotProps.data.transfer_id === null">
              <v-list-item
                @click="
                  transferToInstructor(
                    slotProps.data.id,
                    slotProps.data.instructor_id
                  )
                "
              >
                Pay
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
      </Column>
    </basic-datatable>
  </div>
</template>

<script>
import BasicDatatable from "../../Global/BasicDatatable.vue";
export default {
  name: "CreateLessonHistory",
  components: { BasicDatatable },
  data() {
    return {
      data: [],
    };
  },
  mounted() {
    this.getLessonsHistory();
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
    async getLessonsHistory() {
      try {
        const url = "/v1/drivisa/admin/lessons/created-by-admin";
        const { data } = await axios.get(url);
        this.data = data;
        this.data = data.data;
      } catch (e) {}
    },
    async transferToInstructor(lessonId, instructorId) {
      try {
        let url = `v1/drivisa/admin/lessons/create-transfer/${lessonId}/${instructorId}`;
        const { data } = await axios.post(url);
        this.$toasted.success("Transfer created successfully!");
        await this.getLessonsHistory();
      } catch (e) {
        this.$toasted.error("Unable to create transfer!");
      }
    },
  },
};
</script> 