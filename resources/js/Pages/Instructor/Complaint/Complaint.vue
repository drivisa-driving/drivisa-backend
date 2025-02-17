<template>
  <MasterInstructorLayout>
    <v-card>
      <v-card-text>
        <!-- Complaint Reply Status -->
        <v-dialog
          v-model="complaintReplyStatusDialog"
          max-width="500"
          persistent
        >
          <v-card>
            <h3 class="text-h5 text-center pt-3">Complaint's Reply</h3>
            <div
              outlined
              auto-grow
              border
              p-2
              rounded
              class="mx-3 mt-5"
              v-if="selectedComplaint.replies != 0"
            >
              <div
                class="m-2"
                v-for="reply in selectedComplaint.replies"
                :key="reply.id"
              >
                <div class="border p-2 rounded mx-3 mt-5">
                  {{ reply.reply }}
                </div>
              </div>
            </div>

            <div class="border p-3 rounded mx-8 mt-5" v-else>
              <h6>No Reply's Found Of Your Complaint</h6>
            </div>

            <div class="d-flex justify-content-around px-2 py-3">
              <v-btn
                class="text-white text-capitalize"
                color="green"
                text
                @click="
                  complaintReplyStatusDialog = false;
                  complaint_id = null;
                "
              >
                Close
              </v-btn>
            </div>
          </v-card>
        </v-dialog>
      </v-card-text>
    </v-card>

    <div>
      <DataTable
        ref="dt"
        :value="data"
        :filters="filters"
        dataKey="id"
        :paginator="false"
        :rows="10"
        :rowsPerPageOptions="[5, 10, 25]"
        responsiveLayout="scroll"
        sortField="complaint_date"
        :sortOrder="-1"
      >
        <template #header>
          <div class="d-flex justify-content-between">
            <span class="p-input-icon-left">
              <i class="pi pi-search" />
              <InputText
                placeholder="Search Complaint"
                v-model="filters['global'].value"
              />
            </span>
          </div>
        </template>
        <Column sortable hidden field="incident_date" header="Incident Date"></Column>
        <Column sortable field="complaint_date" header="Complaint Date"></Column>
        <Column sortable field="reason" header="Reason"></Column>
        <Column
          sortable
          field="incident_summary"
          header="Incident Summary"
        ></Column>
        <Column sortable header="Replies">
          <template #body="{ data }">
            <v-btn
              color="green"
              small
              class="text-capitalize text-white"
              @click="dialogMethod(data.id)"
              >Status
            </v-btn>
          </template>
        </Column>
        <template #footer>
          <Paginator
            :first.sync="offset"
            :rows="10"
            @page="onPage($event)"
            :totalRecords="totalItems"
            :rowsPerPageOptions="[5, 10, 20, 30]"
          >
            <template #FirstPageLink>
              <p>First Page</p>
            </template>
          </Paginator>
        </template>
      </DataTable>
    </div>
  </MasterInstructorLayout>
</template>

<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";
import Paginator from "primevue/paginator";
import { FilterMatchMode } from "primevue/api";

export default {
  name: "Complaint",
  components: { Paginator, MasterInstructorLayout },
  data() {
    return {
      perPage: 10,
      pageNo: 1,
      totalItems: 10,
      itemsPerPageOptions: [10, 20, 30, 50],
      search: "",
      offset: 0,
      data: [],
      filters: {},
      dialog: false,
      complaintReplyStatusDialog: false,
      selectedComplaint: "",
    };
  },
  created() {
    this.initFilters();
  },
  watch: {
    perPage() {
      this.getAllComplaint();
    },
    pageNo() {
      this.getAllComplaint();
    },
  },
  mounted() {
    this.getAllComplaint();
  },
  methods: {
    dialogMethod(id) {
      this.complaintReplyStatusDialog = true;
      this.complaint_id = id;
      this.selectedComplaint = this.data.find(
        (s_complaint) => s_complaint.id == id
      );
    },
    initFilters() {
      this.filters = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
      };
    },
    async getAllComplaint() {
      try {
        let query = new URLSearchParams({
          per_page: this.perPage,
          page: this.pageNo,
        });

        const { data } = await axios.get(
          `/v1/drivisa/complaint/user`
        );
        this.data = data;
        this.data = data.data;
        this.totalItems = data.meta.total;
        this.page = data.meta.currentPage;
        this.form = data.meta.from;
        this.to = data.meta.to;
      } catch (e) {}
    },
  },
};
</script>

<style scoped>
</style>