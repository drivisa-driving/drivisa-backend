<template>
  <DataTable
    ref="dt"
    :value="data"
    :selection.sync="selectedData"
    :filters="filters"
    dataKey="id"
    :paginator="true"
    :rows="10"
    :rowsPerPageOptions="[5, 10, 25]"
    responsiveLayout="scroll"
  >
    <template #header>
      <div class="d-flex justify-content-end">
        <span class="p-input-icon-left">
          <i class="pi pi-search" />
          <InputText placeholder="Search" v-model="filters['global'].value" />
        </span>
      </div>
    </template>
    <template #empty>
      <div><strong> Data Not Available</strong></div>
    </template>
    <Column sortable field="lesson_no" header="Lesson No."></Column>
    <Column sortable field="lesson_type" header="Lesson Type">
      <template #body="slotProps">
        <span v-if="slotProps.data.lesson_type === 'Driving'">
          In Car Private Lesson
        </span>
        <span v-else-if="slotProps.data.lesson_type === 'Bde'"> BDE </span>
        <span v-else> {{ slotProps.data.lesson_type }} </span>
      </template>
    </Column>
    <Column sortable field="start_at" header="Start"></Column>
    <Column sortable field="end_at" header="End"></Column>
    <Column
      sortable
      field="payment_intent_id"
      header="Payment Indent ID"
    ></Column>
    <Column sortable field="txn_id" header="Txn ID"></Column>
    <Column sortable field="amount" header="Amount"></Column>
    <Column sortable field="trainee.full_name" header="Trainee Name">
      <template #body="slotProps">
        <router-link
          :to="{
            name: 'admin-trainees-details',
            params: { id: slotProps.data.trainee.id },
          }"
        >
          {{ slotProps.data.trainee.full_name }}
        </router-link>
      </template>
    </Column>
    <Column sortable field="created_at" header="Date"></Column>
  </DataTable>
</template>

<script>
import { FilterMatchMode } from "primevue/api";

export default {
  name: "Transactions",
  data() {
    return {
      perPage: 10,
      pageNo: 1,
      totalItems: 0,
      offset: 0,
      itemsPerPageOptions: [10, 20, 30, 50],
      search: "",
      headers: [],
      data: [],
      selectedData: [],
      filters: {},
    };
  },
  created() {
    this.initFilters();
  },
  watch: {
    perPage() {
      this.getTransactions();
    },
    pageNo() {
      this.getTransactions();
    },
  },
  mounted() {
    this.getTransactions();
  },
  methods: {
    initFilters() {
      this.filters = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
      };
    },
    onPage($event) {
      this.pageNo = $event.page + 1;
      this.perPage = $event.rows;
    },
    async getTransactions() {
      try {
        const { data } = await axios.get(
          "/v1/drivisa/admin/instructors/" +
            this.$route.params.id +
            "/transactions"
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