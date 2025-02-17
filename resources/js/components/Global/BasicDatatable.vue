<template>
  <DataTable
    ref="dt"
    :value="data"
    :filters="filters"
    dataKey="id"
    :rowHover="true"
    :loading="loading"
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
            style="border-radius: 5px; width: 15rem"
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
  name: "BasicDatatable",
  props: ["data", "heading"],
  data() {
    return {
      perPage: 10,
      pageNo: 1,
      totalItems: 0,
      offset: 0,
      itemsPerPageOptions: [10, 20, 30, 50],
      search: "",
      headers: [],
      filters: {},
      loading: false,
    };
  },
  created() {
    this.initFilters();
  },
  mounted() {
    this.loading = true;
    setTimeout(() => {
      this.loading = false;
    }, Math.random() * 2000 + 250);
  },
  methods: {
    initFilters() {
      this.filters = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
      };
    },
  },
};
</script>