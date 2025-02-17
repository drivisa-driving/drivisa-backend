<template>
  <DataTable
    ref="dt"
    :value="data"
    :filters="filters"
    dataKey="id"
    :loading="loading"
    :paginator="true"
    :rowHover="true"
    stripedRows
    :rows="10"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
    :rowsPerPageOptions="[5, 10, 25]"
    responsiveLayout="scroll"
    :alwaysShowPaginator="false"
  >
    <template #header>
      <div class="d-flex justify-content-between">
        <span class="p-input-icon-left">
          <i class="pi pi-search" />
          <InputText
            v-model="filters['global'].value"
            placeholder="Search..."
            style="border-radius: 5px"
          />
        </span>
        <div class="field">
          <label for="range">Sales Report Range</label>
          <Calendar
            id="range"
            v-model="dates"
            selectionMode="range"
            :manualInput="false"
            style="width: 400px; border-radius: 5px"
          />
        </div>
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
  name: "TableApi",
  props: {
    endpoint: {
      type: String,
      required: true,
    },
    query: {
      Type: Object,
      required: false,
      default: {},
    },
    dateFilter: {
      type: Boolean,
      required: false,
      default: false,
    },
    year: {
      type: Number,
      required: false,
      default: {},
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
      dates: null,
      startFrom: null,
      endAt: null,
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
    dates(newValue) {
      if (newValue) {
        let startFrom = formatDateFromString(newValue[0]);
        let endAt = formatDateFromString(newValue[1]);

        this.startFrom = startFrom;
        this.endAt = endAt;

        if (this.startFrom && this.endAt) {
          this.getData();
        }
      }
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
          start_from: this.startFrom,
          end_at: this.endAt,
        });

        if (this.query) {
          for (var key in this.query) {
            query.append(key, this.query[key]);
          }
        }

        const { data } = await axios.get(
          this.endpoint + "?" + query + "&year=" + this.year
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