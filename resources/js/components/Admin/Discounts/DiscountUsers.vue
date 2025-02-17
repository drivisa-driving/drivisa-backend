<template>
  <div>
    <MasterAdminLayout>
      <div class="d-flex align-items-center justify-content-between">
        <p class="mb-2 md:m-0 p-as-md-center" style="font-size: 25px">
          User Discounts
        </p>
        <span class="p-input-icon-left">
          <i class="pi pi-search" />
          <InputText
              v-model="search"
              @input="getDiscounts"
              placeholder="Search..."
              style="border-radius: 5px; width: 15rem"
          />
        </span>
      </div>
      <BasicDatatable :data="filteredData">

        <Column field="user" header="User Name"></Column>
        <Column sortable field="code" header="Code"></Column>
        <Column sortable field="discount_amount" header="Discount Amount"></Column>
        <Column sortable field="discount_type" header="Discount Type"></Column>
        <Column sortable field="main_amount" header="Main Amount"></Column>
        <Column sortable field="total_discount" header="Total Discount"></Column>
        <Column sortable field="after_discount" header="After Discount"></Column>
        <Column sortable field="discount_used_name" header="Discount Applied on"></Column>
        <Column sortable field="created_at" header="Discount Applied At"></Column>
      </BasicDatatable>
      <v-pagination
          v-model="page"
          @input="handlePageChange"
          :length="count"
          :total-visible="7"
          pills
      ></v-pagination>
    </MasterAdminLayout>
  </div>

</template>

<script>
import MasterAdminLayout from "../../../Pages/Admin/Layouts/MasterAdminLayout";

import SplitButton from "primevue/splitbutton";
import BasicDatatable from "../../../components/Global/ServerSideTable.vue";

export default {
  name: "List",
  components: {SplitButton, BasicDatatable, MasterAdminLayout},
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
      dialog: false,
      rejectDialog: false,
      documents: [],
      data: [],
      selectedOption: null,
      agreementDialog: false,
      page:1,
      count:1,
      pageSize:1,
    };
  },
  mounted() {
    this.getDiscounts();
  },
  computed: {
    filteredData() {
      return this.data;
    }
  },
  methods: {
    handlePageChange(value) {
      this.page = value;
      this.getDiscounts();
    },
    async getDiscounts() {
      try {
        const {data} = await axios.get("/v1/drivisa/admin/discounts/discountUsers?per_page=15&&page=" + this.page+"&&search=" + this.search);
        this.data = data.data;
        let total=data.total/15;
        this.count=total%1 ===0?parseInt(total):parseInt(total+1);
      } catch (e) {
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
