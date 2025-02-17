<template>
  <div>
    <DatatableApi
      :endpoint="`/v1/drivisa/admin/trainees/courses/${this.$route.params.id}`"
    >
      <Column sortable field="name" header="Course Type">
        <template #body="slotProps">
          <span v-if="slotProps.data.name === 'package'">Credit Package</span>
          <span v-else-if="slotProps.data.name === 'bde'">BDE</span>
          <span v-else></span>
        </template>
      </Column>
      <Column
        sortable
        field="data.data.total_hours"
        header="Total Hours"
      ></Column>
      <Column
        sortable
        field="data.data.used_hours"
        header="User Hours"
      ></Column>
      <Column
        sortable
        field="data.data.remaining_hours"
        header="Remaining Hours"
      ></Column>
    </DatatableApi>
  </div>
</template>

<script>
import DatatableApi from "../../../Global/DatatableApi.vue";
export default {
  name: "Courses",
  components: { DatatableApi },
};
</script>