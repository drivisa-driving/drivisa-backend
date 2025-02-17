<template>
  <div>
    <TableApi
      endpoint="/v1/drivisa/admin/report"
      :query="{ type: reportType }"
      :year="year"
    >
      <Column field="trainee_name" header="Purchase By" style="min-width: 8rem">
        <template #body="slotProps">
          <router-link
            :to="'/admin/trainees-details/' + slotProps.data.trainee_id"
          >
            {{ slotProps.data.trainee_name }}
          </router-link>
        </template>
      </Column>
      <Column
        field="type"
        header="Purchase Type"
        style="min-width: 16rem"
      ></Column>
      <Column
        field="amount"
        header="Purchase Amount"
        style="min-width: 8rem"
      ></Column>
      <Column
        field="date"
        header="Creation Date"
        style="min-width: 8rem"
      ></Column>
    </TableApi>
  </div>
</template>

<script>
import TableApi from "../../../components/Global/TableApi";
export default {
  name: "Report",
  props: ["reportType", "year"],
  components: { TableApi },
  data() {
    return {
      reveal: false,
      sr_no: null,
      type: null,
      amount: null,
      date: null,
      data: [],
    };
  },
  mounted() {
    //this.getSalesReportStats();
  },
  methods: {
    async getSalesReportStats() {
      try {
        let url = "/v1/drivisa/admin/report";
        const { data } = await axios.get(url);
        this.data = data.data;
      } catch (e) {}
    },
  },
};
</script>