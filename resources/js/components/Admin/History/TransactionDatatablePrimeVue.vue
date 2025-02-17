<template>
  <DatatableApi
    endpoint="/v1/drivisa/admin/history/transactions"
    heading="Transactions History"
  >
    <Column
      sortable
      field="payment_intent_id"
      header="Payment Intent ID"
    ></Column>
    <Column sortable field="txn_id" header="Transaction ID"></Column>
    <Column sortable field="type" header="Purchase Type"></Column>
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
    <Column sortable field="instructor.full_name" header="Instructor Name">
      <template #body="slotProps">
        <router-link
          :to="{
            name: 'admin-instructor-details',
            params: { id: slotProps.data.instructor.id },
          }"
        >
          {{ slotProps.data.instructor.full_name }}
        </router-link>
      </template>
    </Column>
    <Column sortable field="created_at" header="Date"></Column>
  </DatatableApi>
</template>

<script>
import DatatableApi from "../../../components/Global/DatatableApi.vue";
export default {
  name: "TransactionDatatablePrimeVue",
  components: { DatatableApi },
};
</script>
