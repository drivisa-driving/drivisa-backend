<template>
  <DatatableApi
    :endpoint="`/v1/drivisa/admin/trainees/${this.$route.params.id}/transactions`"
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
      header="Payment Indent ID"
    ></Column>
    <Column sortable field="txn_id" header="Txn ID"></Column>
    <Column sortable field="amount" header="Amount"></Column>
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
import DatatableApi from "../../../Global/DatatableApi.vue";
export default {
  name: "Transactions",
  components: {
    DatatableApi,
  },
};
</script>