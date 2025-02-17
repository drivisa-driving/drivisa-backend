<template>
  <div>

    <div class="page-header">
      <p class="page-heading">Notification Logs</p>

    </div>
    <div class="d-flex align-items-center justify-content-between">
      <p class="mb-2 md:m-0 p-as-md-center" style="font-size: 25px">
      </p>
      <span class="p-input-icon-left">
          <i class="pi pi-search" />
          <InputText
              v-model="search"
              @input="getNotificationLogs"
              placeholder="Search..."
              style="border-radius: 5px; width: 15rem"
          />
        </span>
    </div>
    <BasicDatatable :data="logs">
      <Column sortable field="activity_name" header="Activity Name"></Column>
      <Column sortable field="message" header="Message"></Column>
      <Column sortable field="data" header="Data"></Column>
      <Column sortable field="device_id" header="Device Id"></Column>
      <Column sortable field="instructor" header="Instructor"></Column>
      <Column sortable field="trainee" header="Trainee"></Column>
      <Column sortable field="created_at" header="Created At"></Column>

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
import BasicDatatable from "../../../components/Global/ServerSideTable.vue";
export default {
  name: "Notifications",
  components: { SplitButton, BasicDatatable },
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
      logs: [],
      agreementDialog: false,
    };
  },
  mounted() {
    this.getNotificationLogs();

  },
  methods: {
    handlePageChange(value) {
      this.page = value;
      this.getNotificationLogs();
    },
    async getNotificationLogs() {
      try {
        const {data} = await axios.get("/v1/drivisa/admin/logs/notifications?type=table&&per_page=15&&page=" + this.page);
        this.logs = data.data;
        let total = data.total / 15;
        this.count = total % 1 === 0 ? parseInt(total) : parseInt(total + 1);
      } catch (e) {
      }
    },
  }
}
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
