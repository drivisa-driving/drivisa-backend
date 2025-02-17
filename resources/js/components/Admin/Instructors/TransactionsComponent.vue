<template>
  <DatatableApi
    :endpoint="`/v1/drivisa/admin/instructors/${this.$route.params.id}/transactions`"
  >
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
      header="Payment Intent ID"
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
  </DatatableApi>
</template>

<script>
import DatatableApi from "../../../components/Global/DatatableApi.vue";
export default {
  name: "TransactionDatatablePrimeVue",
  components: {
    DatatableApi,
  },
};
</script>