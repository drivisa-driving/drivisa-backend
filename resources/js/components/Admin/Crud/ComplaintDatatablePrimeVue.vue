<template>
  <div>
    <BasicDatatable :data="data" heading="Complaints">
      <Column
        sortable
        hidden
        field="complaint_date"
        header="Complaint Date"
      ></Column>
      <Column sortable field="name" header="Name"></Column>
      <Column sortable field="incident_date" header="Incident Date"></Column>
      <Column sortable field="reason" header="Reason"></Column>
      <Column
        sortable
        field="incident_summary"
        header="Incident Summary"
      ></Column>
      <Column sortable field="is_replied" header="Status"></Column>
      <Column header="Actions">
        <template #body="slotProps">
          <v-btn
            :disabled="slotProps.data.is_replied === 'Replied'"
            color="green"
            small
            class="text-capitalize text-white"
            @click="$emit('reply', slotProps.data.id)"
            >Reply
          </v-btn>
        </template>
      </Column>
    </BasicDatatable>
  </div>
</template>

<script>
import BasicDatatable from "../../Global/BasicDatatable.vue";
export default {
  name: "ComplaintDatatablePrimeVue",
  components: { BasicDatatable },
  data() {
    return {
      data: [],
    };
  },
  mounted() {
    this.getAllComplaint();
  },
  methods: {
    async getAllComplaint() {
      try {
        const { data } = await axios.get("/v1/drivisa/complaint");
        this.data = data;
        this.data = data.data;
      } catch (e) {}
    },
  },
};
</script>
