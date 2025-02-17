<template>
  <DataTable
    ref="dt"
    :value="data"
    :filters="filters"
    dataKey="id"
    :loading="loading"
    :rowHover="true"
    stripedRows
    :rows="10"
    :paginator="true"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
    :rowsPerPageOptions="[5, 10, 25]"
    responsiveLayout="scroll"
    :alwaysShowPaginator="false"
  >
    <template #header>
      <div class="d-flex align-items-center justify-content-between">
        <p class="mb-2 md:m-0 p-as-md-center" style="font-size: 25px">
          {{ heading }}
        </p>
        <span class="p-input-icon-left">
          <i class="pi pi-search" />
          <InputText
            v-model="filters['global'].value"
            placeholder="Search..."
            style="border-radius: 5px"
          />
        </span>
      </div>
    </template>
    <template #empty>
      <div>
        <strong> Data Not Available</strong>
      </div>
    </template>
    <slot></slot>
  </DataTable>
</template>

<script>
import { FilterMatchMode } from "primevue/api";
export default {
  name: "DatatableApi",
  props: {
    endpoint: {
      type: String,
      required: true,
    },
    heading: {
      type: String,
    },
  },
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
      filters: {},
      loading: false,
    };
  },
  watch: {
    perPage() {
      this.getData();
    },
    pageNo() {
      this.getData();
    },
  },
  created() {
    this.initFilters();
  },
  mounted() {
    this.loading = true;
    setTimeout(() => {
      this.getData().then(() => {
        this.loading = false;
      });
    }, Math.random() * 1000 + 250);
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
    async getData() {
      try {
        let query = new URLSearchParams({
          per_page: this.perPage,
          page: this.pageNo,
        });

        if (this.query) {
          for (var key in this.query) {
            query.append(key, this.query[key]);
          }
        }

        const { data } = await axios.get(this.endpoint);
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