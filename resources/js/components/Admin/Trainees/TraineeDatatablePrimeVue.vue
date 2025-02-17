<template>
  <div>
    <v-dialog v-model="rejectDialog" max-width="500">
      <v-card class="p-4">
        <h3 class="text-center">Document Rejection Form</h3>
        <v-textarea
            placeholder="Enter Rejection Reason"
            v-model="requestData.message"
        ></v-textarea>
        <v-btn
            v-if="!isReject"
            color="red"
            class="text-white text-capitalize"
            @click="rejectDocument"
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
    <v-dialog v-model="dialog" max-width="800">
      <v-card>
        <h3 class="text-center pt-3">Documents</h3>
        <div class="mt-3 pb-5" v-if="documents.length > 0">
          <div
              v-for="document in documents"
              :key="document.id"
              class="mx-5 px-1"
          >
            <div
                class="d-flex justify-content-between align-items-center border mb-2 rounded p-3"
            >
              <div class="mb-2">
                <h6>
                  {{ document.zone.replaceAll("_", " ") | uppercase }}
                </h6>
                <p>
                  <strong>Verification Status:</strong>
                  <span v-if="document.status == 1" class="text-primary"
                  >In review</span
                  >
                  <span v-else-if="document.status == 2" class="text-success"
                  >Approved</span
                  >
                  <span v-else-if="document.status == 3" class="text-danger"
                  >Rejected, <br/>
                    <span style="color: black">Reason:</span>
                    {{ document.reason }}</span
                  >
                  <br/><span>Date: {{ document.updated_at }}</span>
                </p>
                <div v-if="document.status !== 3">
                  <img :src="document.thumb" alt=""/>
                </div>
              </div>
              <div class="d-flex align-items-center">
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
                      color="primary"
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
          <div
              class="d-flex justify-content-end align-items-center p-3 mx-2"
              v-if="trainee_verified == 1"
          >
            <div>
              <v-btn
                  v-if="!isVerify"
                  color="green"
                  class="text-white text-capitalize"
                  @click="verifyDocument"
              >Verify All
              </v-btn>
              <v-progress-circular
                  v-else
                  disabled
                  indeterminate
                  color="primary btn-outline"
              ></v-progress-circular>
            </div>
            <v-btn
                color="red"
                class="text-white text-capitalize ml-1"
                @click="rejectDialog = true"
            >Reject All
            </v-btn>
            <v-btn
                color="primary"
                class="text-white text-capitalize ml-1"
                @click="dialog = false"
            >Close
            </v-btn>
          </div>
        </div>
        <div class="my-3 text-center font-weight-bold p-3" v-else>
          No Document Uploaded Yet
        </div>
      </v-card>
    </v-dialog>

    <div class="page-header d-flex justify-content-end">
      <Dropdown
          v-model="selectedOption"
          :options="trainees"
          @input="page=1;getTrainees();"
          optionLabel="option"
          optionValue="value"
          placeholder="Filter Trainees"
          style="width: 15rem; border-radius: 5px"
      />
     <a :href="'/export-trainee?type='+selectedOption+'&search='+search"> <button
          class="btn btn-primary btn-outline mt-0 ml-3  btn-"
      > Export </button></a>
    </div>
    <div class="d-flex align-items-center justify-content-between">
      <p class="mb-2 md:m-0 p-as-md-center" style="font-size: 25px">
        Trainees
      </p>
      <span class="p-input-icon-left">
          <i class="pi pi-search"/>
          <InputText
              v-model="search"
              @input="getTrainees"
              placeholder="Search..."
              style="border-radius: 5px; width: 15rem"
          />
        </span>
    </div>
    <BasicDatatable :data="filteredTrainees">
      <Column field="no" header="Is Verified">
        <template #body="slotProps">
          <v-chip
              :color="
              slotProps.data.verified === 0 || slotProps.data.verified === false
                ? 'red'
                : 'green'
            "
              small
          >
          </v-chip>
        </template>
      </Column>
      <Column sortable field="fullName" header="Name">
        <template #body="slotProps">
          <router-link
              :to="{
              name: 'admin-trainees-details',
              params: { id: slotProps.data.id },
            }"
          >
            {{ slotProps.data.fullName }}
          </router-link>
        </template>
      </Column>
      <Column field="phoneNumber" header="Phone Number"></Column>
      <Column field="email" header="Email"></Column>
      <Column field="verified_at" header="Approval On"></Column>
      <Column field="createdAt" header="Join On"></Column>
      <Column header="Details">
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
              <v-list-item @click="fetchDocuments(slotProps.data.id)">
                Documents
              </v-list-item>
              <v-list-item
                  v-if="slotProps.data.user.blocked_at == null"
                  @click="
                  $root.blockUser(slotProps.data.user.id);
                  getTrainees();
                "
              >
                Block
              </v-list-item>
              <v-list-item
                  v-if="slotProps.data.user.blocked_at != null"
                  @click="
                  $root.unblockUser(slotProps.data.user.id);
                  getTrainees();
                "
              >
                Unblock
              </v-list-item>
              <v-list-item
                  :to="{
                  name: 'admin-trainees-details',
                  params: { id: slotProps.data.id },
                }"
              >
                Details
              </v-list-item>
              <v-list-item
                  :to="{
                  name: 'admin-trainees-transactions',
                  params: { id: slotProps.data.id },
                }"
              >
                Transactions
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
      </Column>
    </BasicDatatable>
    <v-pagination
        v-model="page"
        @input="handlePageChange"
        :length="count"
        :total-visible="7"
        pills
    ></v-pagination>

  </div>
</template>

<script>
import BasicDatatable from "../../../components/Global/ServerSideTable.vue";
import moment from "moment/moment";

export default {
  name: "TraineeDatatablePrimeVue",
  components: {BasicDatatable},
  data() {
    return {
      search: null,
      page: 1,
      count: 1,
      pageSize: 1,
      trainee_verified: false,
      isReject: false,
      isSingleVerify: false,
      isSingleReject: false,
      singleRejectDialog: false,
      dialog: false,
      rejectDialog: false,
      requestData: {
        id: null,
        document_id: null,
        message: null,
      },
      documents: [],
      data: [],
      selectedOption: null,
      trainees: [
        {option: "Verified", value: 1},
        {option: "Pending Verification", value: 0},
      ],
      isVerify: false,
    };
  },
  computed: {
    filteredTrainees() {
      let allTrainees = this.data;
      if (this.selectedOption == null) return allTrainees;

      if (this.selectedOption == 1) {
        let storageObject = {
          option: "Verified",
          value: this.selectedOption,
        };
        sessionStorage.filteredTraineeValue = JSON.stringify(storageObject);

        return allTrainees.filter(
            (trainee) => trainee.verified == this.selectedOption
        );
      } else if (this.selectedOption == 0) {
        let storageObject = {
          value: this.selectedOption,
          option: "Pending Verification",
        };
        sessionStorage.filteredTraineeValue = JSON.stringify(storageObject);

        return allTrainees.filter(
            (trainee) => trainee.verified == this.selectedOption
        );
      }
    },
  },
  mounted() {
    this.getTrainees();

    let sessionObject = sessionStorage.filteredTraineeValue;
    if (sessionObject) {
      let obj = JSON.parse(sessionObject);
      this.trainees.option = obj.option;
      this.selectedOption = obj.value;
    }

    window.onunload = function () {
      sessionStorage.removeItem("filteredTraineeValue");
    };
  },
  methods: {
    handlePageChange(value) {
      this.page = value;
      this.getTrainees();
    },
    async getTrainees() {
      try {
         const {data} = await axios.get("/v1/drivisa/admin/trainees?per_page=15&&page=" + this.page + "&&type=" + this.selectedOption + "&&search=" + this.search);
        this.data = data.data;
        let total = data.total / 15;
        this.count = total % 1 === 0 ? parseInt(total) : parseInt(total + 1);
      } catch (e) {
      }
    },
    fetchDocuments(id) {
      let trainee = this.data.find((trainee) => trainee.id == id);
      this.trainee_verified = !trainee.verified;
      this.documents = trainee.documents;
      this.requestData.id = trainee.id;
      this.dialog = true;
    },
    async verifyDocument() {
      try {
        this.isVerify = true;
        const url = `/v1/drivisa/admin/trainees/${this.requestData.id}/verify`;
        this.requestData.verified = true;
        const {data} = await axios.post(url, this.requestData);
        this.requestData.id = null;
        this.requestData.message = null;
        this.$toasted.success(data.message);
        await this.getTrainees();
      } catch (e) {
      } finally {
        this.isVerify = false;
        this.rejectDialog = false;
        this.dialog = false;
      }
    },
    async rejectDocument() {
      try {
        this.isReject = true;
        const url = `/v1/drivisa/admin/trainees/${this.requestData.id}/verify`;
        const {data} = await axios.post(url, this.requestData);
        this.requestData.id = null;
        this.requestData.message = null;
        this.$toasted.error(data.message);
      } catch (e) {
      } finally {
        this.isReject = false;
        this.rejectDialog = false;
        this.dialog = false;
      }
    },
    async verifySingleDocument(id) {
      try {
        this.isSingleVerify = true;
        const url = `/v1/drivisa/admin/trainees/${this.requestData.id}/${id}/change-status`;
        this.requestData.status = "approved";
        const {data} = await axios.post(url, this.requestData);
        this.requestData.id = null;
        this.requestData.message = null;
        this.$toasted.success(data.message);
        await this.getTrainees();
      } catch (e) {
      } finally {
        this.isSingleVerify = false;
        this.rejectDialog = false;
        this.dialog = false;
      }
    },
    async rejectSingleDocument() {
      try {
        this.isSingleReject = true;
        const url = `/v1/drivisa/admin/trainees/${this.requestData.id}/${this.requestData.document_id}/change-status`;
        this.requestData.status = "rejected";
        const {data} = await axios.post(url, this.requestData);
        this.requestData.id = null;
        this.requestData.message = null;
        this.$toasted.error(data.message);
        await this.getTrainees();
      } catch (e) {
      } finally {
        this.isSingleReject = false;
        this.rejectDialog = false;
        this.singleRejectDialog = false;
        this.dialog = false;
      }
    },
  },
};
</script>

<style scoped>
.option-icon {
  background-repeat: no-repeat;
  /*display: inline-block;*/
  fill: currentColor;
  height: 24px;
  width: 24px;
  font-size: 24px;
  float: right !important;
}
</style>
