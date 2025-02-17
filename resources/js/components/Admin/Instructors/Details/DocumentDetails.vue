<template>
  <div>
    <v-dialog v-model="singleRejectDialog" max-width="500">
      <v-card class="p-4">
        <h3 class="text-center">Document Single Rejection Form</h3>
        <v-textarea
          placeholder="Enter Rejection Reason"
          v-model="requestData.message"
        ></v-textarea>
        <v-btn
          v-if="!isSingleReject"
          color="red"
          class="text-white text-capitalize"
          @click="rejectSingleDocument"
          >Reject
        </v-btn>
        <v-progress-circular
          v-else
          disabled
          indeterminate
          color="red"
        ></v-progress-circular>
      </v-card>
    </v-dialog>

    <div class="col-md-6">
      <div class="document-box">
        <h4>{{ documentName }}</h4>
        <div>
          <div class="my-2" v-if="thumb || preview">
            <DocumentStatus :document="document" />
            <div v-if="document.status !== 3">
              <img
                :src="thumb"
                :alt="documentName"
                ref="img_tag"
                style="width: 360px; height: 180px; object-fit: contain"
              />
              <div class="mt-3 d-flex">
                <div v-if="document.status == 1">
                  <v-btn
                    v-if="!isSingleVerify"
                    color="green"
                    class="text-white text-capitalize"
                    @click="verifySingleDocument(document.id)"
                    >Approve
                  </v-btn>
                  <v-progress-circular
                    v-else
                    disabled
                    indeterminate
                    color="green"
                  ></v-progress-circular>
                  <v-btn
                    color="red"
                    class="text-white text-capitalize"
                    @click="
                      requestData.document_id = document.id;
                      singleRejectDialog = true;
                    "
                    >Reject
                  </v-btn>
                </div>
                <a
                  :href="document.path"
                  class="btn btn-primary ml-1 text-white btn-outline"
                  download
                  target="_blank"
                  >Download
                </a>
              </div>
            </div>
          </div>
          <div v-else>
            <p>
              <strong>Status:</strong>
              <span class="text-danger">Pending</span>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
  
  <script>
import DocumentStatus from "../Details/DocumentStatus.vue";

export default {
  name: "DocumentDetails",
  components: { DocumentStatus },
  props: ["document", "zone_name", "documentName"],
  data() {
    return {
      isSingleReject: false,
      isSingleVerify: false,
      singleRejectDialog: false,
      file: null,
      preview: false,
      requestData: {
        id: null,
        message: null,
      },
    };
  },
  computed: {
    thumb() {
      let url = null;
      if (this.document) {
        url = this.document.path;
      }
      return url;
    },
  },
  methods: {
    async verifySingleDocument(id) {
      try {
        this.isSingleVerify = true;
        const url = `/v1/drivisa/admin/instructors/${this.$route.params.id}/${id}/change-status`;
        this.requestData.status = "approved";
        const { data } = await axios.post(url, this.requestData);
        this.requestData.id = null;
        this.requestData.message = null;
        this.$toasted.success(data.message);
      } catch (e) {
      } finally {
        this.isSingleVerify = false;
      }
    },
    async rejectSingleDocument() {
      try {
        this.isSingleReject = true;
        const url = `/v1/drivisa/admin/instructors/${this.$route.params.id}/${this.requestData.document_id}/change-status`;
        this.requestData.status = "rejected";
        const { data } = await axios.post(url, this.requestData);
        this.requestData.id = null;
        this.requestData.message = null;
        this.$toasted.error(data.message);
      } catch (e) {
      } finally {
        this.isSingleReject = false;
        this.singleRejectDialog = false;
      }
    },
  },
};
</script>
  
  <style scoped>
.document-box {
  margin-bottom: 20px;
  flex-direction: column;
  box-sizing: border-box;
  display: flex;
  place-content: stretch center;
  align-items: stretch;
  flex: 1 1 33%;
}

.document-box h4 {
  font: Montserrat, sans-serif !important;
  letter-spacing: normal;
  margin: 0 0 16px;
  color: rgba(0, 0, 0, 0.87);
}
</style>