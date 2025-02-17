<template>
  <MasterStudentLayout>
    <div class="border p-3 rounded">
      <div class="mb-3 border-bottom">
        <h4 class="font-weight-light text-dark">Raise a Complaint</h4>
      </div>
      <div>
        <div class="col-12">
          <v-menu
            ref="menu"
            v-model="menu"
            :close-on-content-click="false"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                v-model="complaint.incident_date"
                label="When did the incident happen?"
                prepend-inner-icon="mdi-calendar"
                outlined
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="complaint.incident_date"
              no-title
              scrollable
            >
              <v-spacer></v-spacer>
              <v-btn
                text
                color="primary"
                @click="$refs.menu.save(complaint.incident_date)"
              >
                OK
              </v-btn>
            </v-date-picker>
          </v-menu>
        </div>
        <div class="col-12">
          <v-select
            :items="complaintReason"
            label="What kind of problem did you find?"
            outlined
            v-model="complaint.reason"
          ></v-select>
        </div>
        <div class="col-12">
          <v-textarea
            outlined
            label="Write a brief summary of the incident"
            v-model="complaint.incident_summary"
          ></v-textarea>
        </div>
      </div>
      <v-btn color="primary ml-3" @click="addComplaint">Submit</v-btn>
    </div>
  </MasterStudentLayout>
</template>

<script>
import MasterStudentLayout from "../Layouts/MasterStudentLayout";
export default {
  name: "RaiseComplaint",
  components: { MasterStudentLayout },
  data() {
    return {
      menu: false,
      complaintReason: ["Service", "Timing", "Guidelines", "Information"],
      complaint: {
        name: "",
        email: "",
        phone: "",
        incident_date: "",
        reason: "",
        incident_summary: "",
      },
    };
  },

  methods: {
    clearForm() {
      this.complaint = {
        name: "",
        email: "",
        phone: "",
        incident_date: "",
        reason: "",
        incident_summary: "",
      };
    },
    async addComplaint() {
      try {
        var url = "/v1/drivisa/complaint";
        const { data } = await axios.post(url, this.complaint);
        this.$toasted.success("Your Complaint raised successfully");
        this.clearForm();
      } catch (e) {
        this.$toasted.error("Can't raise your complaint");
      }
    },
  },
};
</script>

<style>
</style>