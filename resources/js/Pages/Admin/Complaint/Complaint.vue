<template>
  <div>
    <MasterAdminLayout>
      <v-card>
        <v-card-text>
          <!-- Reply Complaint Dialog -->
          <v-dialog v-model="complaintReplyDialog" max-width="500" persistent>
            <v-card>
              <h3 class="text-h5 text-center pt-3">Reply Complaint</h3>
              <v-textarea
                outlined
                auto-grow
                label="Notes"
                class="m-3"
                v-model="replyComplaint.reply"
              ></v-textarea>
              <div class="d-flex justify-content-around px-2 py-3">
                <v-btn
                  class="text-white text-capitalize"
                  color="red"
                  text
                  @click="
                    complaintReplyDialog = false;
                    complaint_id = null;
                  "
                >
                  Cancel
                </v-btn>
                <v-btn
                  class="text-white text-capitalize"
                  color="green"
                  @click="complaintReply"
                >
                  Reply
                </v-btn>
              </div>
            </v-card>
          </v-dialog>

          <!-- Complaint Datatable -->
          <ComplaintDatatablePrimeVue ref="table" @reply="dialogMethod" />
        </v-card-text>
      </v-card>
    </MasterAdminLayout>
  </div>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import ComplaintDatatablePrimeVue from "../../../components/Admin/Crud/ComplaintDatatablePrimeVue";

export default {
  name: "Complaint",
  components: { MasterAdminLayout, ComplaintDatatablePrimeVue },
  data() {
    return {
      dialog: false,
      complaintReplyDialog: false,
      complaint_id: null,
      replyComplaint: {
        reply: "",
      },
    };
  },
  methods: {
    updateTable() {
      this.$refs.table.getAllComplaint();
    },
    dialogMethod(id) {
      this.complaintReplyDialog = true;
      this.complaint_id = id;
    },
    clearForm() {
      this.replyComplaint = {
        complaint_id: null,
        reply: "",
      };
      this.dialog = false;
    },
    async complaintReply() {
      try {
        var url = `/v1/drivisa/complaint-reply/${this.complaint_id}`;
        const { data } = await axios.post(url, this.replyComplaint);
        this.$toasted.success(data.message);
        this.updateTable();
        this.clearForm();
        this.complaintReplyDialog = false;
      } catch (e) {
        this.$root.handleErrorToast("Can't reply");
      }
    },
  },
};
</script>