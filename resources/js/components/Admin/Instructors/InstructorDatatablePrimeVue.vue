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
                    >Rejected, <br />
                    <span style="color: black">Reason:</span>
                    {{ document.reason }}</span
                  >
                  <br /><span>Date: {{ document.updated_at }}</span>
                </p>
                <div v-if="document.status !== 3">
                  <img :src="document.thumb" alt="" />
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
                    color="primary btn-outline"
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
            v-if="instructor_verified == 1"
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
              color="primary btn-outline"
              class="text-white text-capitalize"
              @click="dialog = false"
              >Close
            </v-btn>
          </div>
        </div>
        <div class="my-3 text-center font-weight-bold pb-3" v-else>
          No Document Uploaded Yet
        </div>
      </v-card>
    </v-dialog>

    <v-dialog v-model="agreementDialog" max-width="500">
      <v-card class="p-4">
        <h4 class="text-center">Drivisa Agreement</h4>
        <div
          id="content"
          class="p-2 border rounded d-flex flex-column my-1"
          style="height: 60vh; overflow: auto"
        >
          <img
            class="d-none"
            src="/assets/media/logos/drivisa-logo200_80.svg"
            style="height: 60px"
          />
          <div>
            <contract />
            <p style="text-align: right; font-size: 14px">
              Joined On: {{ instructor.createdAt }} <br />
              Signed On: {{ instructor.signed_at }}
            </p>
          </div>
        </div>
        <v-card-actions>
          <v-spacer></v-spacer>
          <div class="d-flex justify-content-between">
            <v-btn
              small
              color="primary"
              class="text-white text-capitalize mr-1"
              @click="agreementDialog = false"
            >
              Close
            </v-btn>
            <v-btn
              small
              color="primary"
              class="text-white text-capitalize"
              @click="downloadAgreement"
              >Download</v-btn
            >
          </div>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <div class="page-header">
      <p class="page-heading">Instructors</p>
      <Dropdown
        v-model="selectedOption"
        :options="instructors"
        optionLabel="option"
        optionValue="value"
        @input="getInstructors(1)"
        placeholder="Filter Instructors"
        style="width: 15rem; border-radius: 5px"
      />
    </div>
    <div class="d-flex align-items-center justify-content-between">
      <p class="mb-2 md:m-0 p-as-md-center" style="font-size: 25px">
      </p>
      <span class="p-input-icon-left">
          <i class="pi pi-search" />
          <InputText
              v-model="search"
              @input="getInstructors"
              placeholder="Search..."
              style="border-radius: 5px; width: 15rem"
          />
        </span>
    </div>
    <BasicDatatable :data="filteredInstructors">
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
      <Column field="no" header="Document Signed">
        <template #body="slotProps">
          <v-icon
            :color="slotProps.data.signDocument == 0 ? 'red' : 'green'"
            small
          >
            {{
              slotProps.data.signDocument === 1
                ? "mdi-checkbox-marked-circle"
                : "mdi-minus-circle"
            }}
          </v-icon>
        </template>
      </Column>
      <Column sortable field="fullName" header="Name">
        <template #body="slotProps">
          <router-link
            :to="{
              name: 'admin-instructor-details',
              params: { id: slotProps.data.id },
            }"
          >
            {{ slotProps.data.fullName }}
          </router-link>
        </template>
      </Column>
      <Column sortable field="phoneNumber" header="Phone Number"></Column>
      <Column sortable field="email" header="Email"></Column>
      <Column sortable field="createdAt" header="Join On"></Column>
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
                v-if="slotProps.data.signDocument === 1"
                @click="fetchAgreementDetails(slotProps.data.id)"
              >
                Download Agreement
              </v-list-item>
              <v-list-item
                v-if="slotProps.data.user.blocked_at == null"
                @click="
                  $root.blockUser(slotProps.data.user.id);
                  getInstructors();
                "
              >
                Block
              </v-list-item>
              <v-list-item
                v-if="slotProps.data.user.blocked_at != null"
                @click="
                  $root.unblockUser(slotProps.data.user.id);
                  getInstructors();
                "
              >
                Unblock
              </v-list-item>
              <v-list-item
                :to="{
                  name: 'admin-instructor-details',
                  params: { id: slotProps.data.id },
                }"
              >
                Details
              </v-list-item>
              <v-list-item
                :to="{
                  name: 'admin-instructor-transactions',
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
import SplitButton from "primevue/splitbutton";
import Contract from "./Details/Contract/Contract.vue";
import BasicDatatable from "../../../components/Global/ServerSideTable.vue";
export default {
  name: "InstructorDatatablePrimeVue",
  components: { SplitButton, BasicDatatable, Contract },
  props: {
    showBtn: {
      type: Boolean,
      default: false,
    },
    onlyVerified: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      search:null,
      page:1,
      count:1,
      pageSize:1,
      instructor_verified: false,
      isReject: false,
      isSingleVerify: false,
      isSingleReject: false,
      singleRejectDialog: false,
      dialog: false,
      rejectDialog: false,
      requestData: {
        id: null,
        message: null,
      },
      documents: [],
      data: [],
      selectedOption: null,
      instructors: [
        { option: "Verified", value: 1 },
        { option: "Pending Verification", value: 0 },
        { option: "Agreement Signed", value: 2 },
      ],
      instructor: [],
      agreementDialog: false,
    };
  },
  computed: {
    filteredInstructors() {
      let allInstructors = this.data;
      if (this.selectedOption == null) return allInstructors;

      if (this.selectedOption == 1) {
        let storageObject = {
          option: "Verified",
          value: this.selectedOption,
        };
        sessionStorage.filteredInstructorValue = JSON.stringify(storageObject);

        return allInstructors.filter(
          (instructor) => instructor.verified == this.selectedOption
        );
      } else if (this.selectedOption == 0) {
        let storageObject = {
          value: this.selectedOption,
          option: "Pending Verification",
        };
        sessionStorage.filteredInstructorValue = JSON.stringify(storageObject);

        return allInstructors.filter(
          (instructor) => instructor.verified == this.selectedOption
        );
      } else if (this.selectedOption == 2) {
        let storageObject = {
          value: this.selectedOption,
          option: "Document Signed",
        };
        sessionStorage.filteredInstructorValue = JSON.stringify(storageObject);

        return allInstructors.filter(
          (instructor) => instructor.signDocument === 1
        );
      }
    },
  },
  mounted() {
    this.getInstructors();

    let sessionObject = sessionStorage.filteredInstructorValue;
    if (sessionObject) {
      let obj = JSON.parse(sessionObject);
      this.instructors.option = obj.option;
      this.selectedOption = obj.value;
    }

    window.onunload = function () {
      sessionStorage.removeItem("filteredInstructorValue");
    };
  },
  methods: {
    handlePageChange(value) {
      this.page = value;
      this.getInstructors();
    },
    async getInstructors(page=this.page) {

      this.page=page;

      try {
        const { data } = await axios.get("/v1/drivisa/admin/instructors?type=table&&per_page=15&&page=" + this.page+"&&instructor_type=" + this.selectedOption+"&&search=" + this.search);
        this.data = data.data;
        let total=data.total/15;
        this.count=total%1 ===0?parseInt(total):parseInt(total+1);
      } catch (e) {}
    },
    fetchDocuments(id) {
      let instructor = this.data.find((instructor) => instructor.id == id);
      this.instructor_verified = !instructor.verified;
      this.documents = instructor.documents;
      this.requestData.id = instructor.id;
      this.dialog = true;
    },
    async verifyDocument() {
      try {
        this.isVerify = true;
        const url = `/v1/drivisa/admin/instructors/${this.requestData.id}/verify`;
        this.requestData.verified = true;
        const { data } = await axios.post(url, this.requestData);
        this.requestData.id = null;
        this.requestData.message = null;
        this.$toasted.success(data.message);
        await this.getInstructors();
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
        const url = `/v1/drivisa/admin/instructors/${this.requestData.id}/verify`;
        const { data } = await axios.post(url, this.requestData);
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
        const url = `/v1/drivisa/admin/instructors/${this.requestData.id}/${id}/change-status`;
        this.requestData.status = "approved";
        const { data } = await axios.post(url, this.requestData);
        this.requestData.id = null;
        this.requestData.message = null;
        this.$toasted.success(data.message);
        await this.getInstructors();
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
        const url = `/v1/drivisa/admin/instructors/${this.requestData.id}/${this.requestData.document_id}/change-status`;
        this.requestData.status = "rejected";
        const { data } = await axios.post(url, this.requestData);
        this.requestData.id = null;
        this.requestData.message = null;
        this.$toasted.error(data.message);
        await this.getInstructors();
      } catch (e) {
      } finally {
        this.isSingleReject = false;
        this.rejectDialog = false;
        this.singleRejectDialog = false;
        this.dialog = false;
      }
    },
    fetchAgreementDetails(id) {
      let instructor = this.data.find((instructor) => instructor.id == id);
      this.agreementDialog = true;
      this.instructor = instructor;
    },
    downloadAgreement() {
      const content = document.getElementById("content").innerHTML;
      const printWindow = window.open("", "", "height=600,width=1000");

      printWindow.document.write(`
        <html>
          <head>
            <title>Drivisa Agreement</title>
            <style>
              @media print {
               .top-heading {
                  text-align: center;
                  margin-bottom: 0;
                }

                img {
                  text-align:center;
                  align-items:center;
                  margin: 0 auto;
                }

                body {
                  visibility: visible !important;
                  margin: 0 auto !important;
                  padding: 0 !important;
                }

                #content {
                  width: 100%;
                  font-family: sans-serif !important;
                  margin-bottom: 0mm;
                  line-height: 1.5;
                  margin: 0 auto;
                }

                .article {
                  margin-top: 5px;
                  margin-bottom: 5px;
                }

                .article-heading {
                  text-align: center;
                  margin-bottom: 0;
                }

                @page {
                  size: auto;
                  margin: 10mm;
                  /* Hides headers and footers */
                  table {
                    border-collapse: collapse;
                    margin: 0;
                    padding: 0;
                    width: 100%;
                  }
                  td {
                    border: none;
                    padding: 0;
                    margin: 0;
                  }
                  thead {
                    display: none;
                  }
                  tfoot {
                    display: none;
                  }
                }
              }

            </style>
          </head>
          <body>
            <div id="content">${content}</div>
          </body>
        </html>
      `);

      printWindow.document.close();
      printWindow.focus();
      printWindow.print();

      setTimeout(() => {
        printWindow.close();
        this.agreementDialog = false;
      }, 100);
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
