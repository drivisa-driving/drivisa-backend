<template>
  <div>
    <MasterAdminLayout>
      <div>
        <v-card class="border">
          <v-card-text>
            <div class="page-header pl-0">
              <p class="page-heading">Send Notification's</p>
            </div>
            <hr />

            <div class="row">
              <div class="col-md-6">
                <div class="mb-0">
                  <Dropdown
                    v-model="selectedInstructorsOption"
                    :options="instructorOptions"
                    optionLabel="option"
                    optionValue="value"
                    placeholder="Filter Instructors"
                    class="mb-4"
                    style="width: 100%"
                  />
                  <ValidationProvider
                    name="Instructor's"
                    rules="required"
                    v-slot="{ errors }"
                  >
                    <MultiSelect
                      style="width: 100%"
                      v-model="selectedInstructors.id"
                      :options="filteredInstructors"
                      optionLabel="full_name"
                      optionValue="user_id"
                      placeholder="Select Instructor's"
                      display="chip"
                      :filter="true"
                      :error-messages="errors[0]"
                      v-on:change.select="sendNotification"
                    />
                  </ValidationProvider>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-4">
                  <Dropdown
                    v-model="selectedTraineesOption"
                    :options="traineeOptions"
                    optionLabel="option"
                    optionValue="value"
                    placeholder="Filter Trainees"
                    class="mb-4"
                    style="width: 100%"
                  />
                  <ValidationProvider
                    name="Trainee's"
                    rules="required"
                    v-slot="{ errors }"
                  >
                    <MultiSelect
                      style="width: 100%"
                      v-model="selectedTrainees.id"
                      :options="filteredTrainees"
                      optionLabel="full_name"
                      optionValue="user_id"
                      placeholder="Select Trainee's"
                      display="chip"
                      :filter="true"
                      :error-messages="errors[0]"
                      v-on:change.select="sendNotification"
                    />
                  </ValidationProvider>
                </div>
              </div>
            </div>
            <div>
              <v-textarea
                outlined
                auto-grow
                label="Message"
                class="mb-0"
                v-model="message.message"
              ></v-textarea>
              <button
                class="btn btn-primary btn-outline mt-0"
                @click="sendNotification"
              >
                Send
              </button>
            </div>
          </v-card-text>
        </v-card>
      </div>
    </MasterAdminLayout>
  </div>
</template>

<script src="https://unpkg.com/primevue@^3/core/core.min.js"></script>
<script src="https://unpkg.com/primevue@^3/multiselect/multiselect.min.js"></script>
<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";

export default {
  name: "Notification",
  components: { MasterAdminLayout },
  data() {
    return {
      selectedInstructors: {
        id: [],
      },
      selectedTrainees: {
        id: [],
      },
      message: {
        message: null,
      },
      instructors: [],
      trainees: [],
      selectedInstructorsOption: null,
      instructorOptions: [
        { option: "Verified", value: 1 },
        { option: "Pending Verification", value: 0 },
      ],
      selectedTraineesOption: null,
      traineeOptions: [
        { option: "Verified", value: 1 },
        { option: "Pending Verification", value: 0 },
      ],
    };
  },
  computed: {
    filteredInstructors() {
      let allInstructors = this.instructors;
      if (this.selectedInstructorsOption == null) return allInstructors;

      if (this.selectedInstructorsOption == 1) {
        return allInstructors.filter(
          (instructor) => instructor.verified == this.selectedInstructorsOption
        );
      } else if (this.selectedInstructorsOption == 0) {
        return allInstructors.filter(
          (instructor) => instructor.verified == this.selectedInstructorsOption
        );
      }
    },
    filteredTrainees() {
      let allTrainees = this.trainees;
      if (this.selectedTraineesOption == null) return allTrainees;

      if (this.selectedTraineesOption == 1) {
        return allTrainees.filter(
          (trainee) => trainee.verified == this.selectedTraineesOption
        );
      } else if (this.selectedTraineesOption == 0) {
        return allTrainees.filter(
          (trainee) => trainee.verified == this.selectedTraineesOption
        );
      }
    },
  },
  mounted() {
    this.getInstructors();
    this.getTrainees();
  },
  methods: {
    clearForm() {
      this.selectedInstructors = {
        id: [],
      };
      this.selectedTrainees = {
        id: [],
      };
      this.message = {
        message: null,
      };
      this.selectedInstructorsOption = null;
      this.selectedTraineesOption = null;
    },
    async getInstructors() {
      const { data } = await axios.get(
        "/v1/drivisa/admin/notification/instructors"
      );
      this.instructors = data.data;
    },
    async getTrainees() {
      const { data } = await axios.get(
        "/v1/drivisa/admin/notification/trainees"
      );
      this.trainees = data.data;
    },
    async sendNotification() {
      try {
        const url = "/v1/drivisa/admin/notification";
        const { data } = await axios.post(url, {
          selectedInstructors: this.selectedInstructors.id,
          selectedTrainees: this.selectedTrainees.id,
          message: this.message.message,
        });
        this.$toasted.success("Notification Sent Successfully");
        this.clearForm();
      } catch (e) {
        this.$toasted.error("Can't Send Notification");
      }
    },
  },
};
</script>
