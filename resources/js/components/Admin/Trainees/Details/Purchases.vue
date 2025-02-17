<template>
  <div>
    <DatatableApi
      :endpoint="`/v1/drivisa/admin/trainees/purchases/${this.$route.params.id}`"
    >
      <Column sortable field="type" header="Type">
        <template #body="slotProps">
          {{ slotProps.data.type.replaceAll("_", " ") }}
        </template>
      </Column>
      <Column sortable field="transaction.amount" header="Amount"></Column>
      <Column
        sortable
        field="transaction.payment_intent_id"
        header="Payment Intent ID"
      ></Column>
      <Column sortable field="date" header="Creation Date"></Column>
    </DatatableApi>
  </div>
</template>

<script>
import DatatableApi from "../../../Global/DatatableApi.vue";
export default {
  name: "Purchases",
  components: { DatatableApi },
};
</script>