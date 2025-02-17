<template>
  <div>
    <v-dialog v-model="messageDialog" width="500">
      <v-card>
        <h3 class="text-h5 text-center pt-3">Message</h3>
        <v-card-text class="text-center">
          <v-textarea
            label="Message"
            placeholder="Enter your message"
            outlined
            rows="3"
            row-height="25"
            v-model="message"
          ></v-textarea>
        </v-card-text>
        <div class="d-flex justify-content-around px-2 py-3">
          <v-btn
            class="text-white text-capitalize"
            color="red"
            @click="updateData"
          >
            Close
          </v-btn>

          <v-btn
            class="text-white text-capitalize"
            color="green"
            @click="notifyTrainee"
          >
            Send
          </v-btn>
        </div>
      </v-card>
    </v-dialog>

    <DataTable
      :value="trainees"
      :selection.sync="selectedIds"
      dataKey="id"
      ref="dt"
      :filters="filters"
      responsiveLayout="scroll"
    >
      <template #header>
        <div class="d-flex justify-content-between">
          <Dropdown
            v-model="selectedOption"
            :options="options"
            optionLabel="option"
            optionValue="value"
            placeholder="Select an option"
            style="width: 13rem"
            :disabled="isDropdownDisabled"
            @change="triggerMessageDialog"
          />
          <span class="p-input-icon-left">
            <i class="pi pi-search" />
            <InputText placeholder="Search" v-model="search" @input="getUnpurchasedTrainees" />
          </span>
        </div>
      </template>
      <template #empty>
        <div><strong> Data Not Available</strong></div>
      </template>
      <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
      <Column sortable field="fullName" header="Name">
        <template #body="slotProps">
          <router-link :to="'/admin/trainees-details/' + slotProps.data.id">
            {{ slotProps.data.fullName }}
          </router-link>
        </template>
      </Column>
      <Column sortable field="phoneNumber" header="Phone Number"></Column>
      <Column sortable field="email" header="Email"></Column>
      <Column sortable field="createdAtFormatted" header="Join On"></Column>
      <Column sortable field="verified_at" header="Approval On"></Column>
    </DataTable>
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
import MasterAdminLayout from "../Layouts/MasterAdminLayout.vue";
import { FilterMatchMode } from "primevue/api";
export default {
  name: "UnpurchasedTrainees",
  components: { MasterAdminLayout },
  data() {
    return {
      page: 1,
      count: 1,
      pageSize: 1,
      perPage: 10,
      pageNo: 1,
      totalItems: 0,
      offset: 0,
      search: "",
      headers: [],
      filters: {},
      messageDialog: false,
      trainees: [],
      selectedIds: [],
      metaKey: true,
      selectedOption: null,
      options: [
        { option: "Notification", value: "Notification" },
        { option: "Email", value: "Email" },
      ],
      message: "",
    };
  },
  watch: {
    perPage() {
      this.getUnpurchasedTrainees();
    },
    pageNo() {
      this.getUnpurchasedTrainees();
    },
  },
  computed: {
    isDropdownDisabled() {
      return this.selectedIds.length === 0;
    },
  },
  created() {
    this.initFilters();
    this.getUnpurchasedTrainees();
  },
  methods: {
    handlePageChange(value) {
      this.page = value;
      this.getUnpurchasedTrainees();
    },
    initFilters() {
      this.filters = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
      };
    },
    triggerMessageDialog() {
      if (this.selectedOption !== null) {
        this.messageDialog = true;
      }
    },
    async getUnpurchasedTrainees() {
      let url = "/v1/drivisa/admin/trainees/unpurchased-trainees/get?per_page=15&&page=" + this.page+"&&search=" + this.search
      const { data } = await axios.get(url);
      this.trainees = data.data;
      let total = data.total / 15;
      this.count = total % 1 === 0 ? parseInt(total) : parseInt(total + 1);
    },
    updateData() {
      this.messageDialog = false;
      this.message = "";
      this.selectedIds = [];
      this.selectedOption = null;
      this.getUnpurchasedTrainees();
    },
    async notifyTrainee() {
      try {
        this.selectedIds = this.selectedIds.map((data) => {
          return data.id;
        });

        let url = "/v1/drivisa/admin/trainees/unpurchased-trainees/notify";
        const { data } = await axios.post(url, {
          selected_option: this.selectedOption,
          traineeIds: this.selectedIds,
          message: this.message,
        });
        this.$toasted.success(`${this.selectedOption} Sent Successfully`);
        this.updateData();
      } catch (e) {
        this.$root.handleErrorToast(e, "Unable to notify trainee's");
      }
    },
  },
};
</script>