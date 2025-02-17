<template>
  <div>
    <v-dialog v-model="detailsDialog" width="500">
      <v-card>
        <h3 class="text-h5 text-center py-3">Refund Details</h3>
        <div class="m-3">
          <table class="table table-bordered">
            <tr>
              <td>Time Left</td>
              <td>{{ data["time_left"] }} Minutes</td>
            </tr>
            <tr>
              <td>Refund ID</td>
              <td>{{ data["refund_id"] }}</td>
            </tr>
            <tr>
              <td>Instructor Fee</td>
              <td>{{ data["instructor_fee"] }}</td>
            </tr>
            <tr>
              <td>Drivisa Fee</td>
              <td>{{ data["drivisa_fee"] }}</td>
            </tr>
            <tr>
              <td>Refund</td>
              <td>{{ data["refund"] }}</td>
            </tr>
          </table>
        </div>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="detailsDialog = false"> Close </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <DatatableApi endpoint="/v1/drivisa/admin/history/refund" heading="Refunds">
      <Column sortable field="lesson.no" header="Lesson No."></Column>
      <Column sortable field="lesson_type" header="Lesson Type">
        <template #body="slotProps">
          <span v-if="slotProps.data.lesson_type === 'Driving'">
            In Car Private Lesson
          </span>
          <span v-else-if="slotProps.data.lesson_type === 'Bde'"> BDE </span>
          <span v-else> {{ slotProps.data.lesson_type }} </span>
        </template>
      </Column>
      <Column sortable field="startAt_formatted" header="Start At"></Column>
      <Column
        sortable
        field="endAt_formatted"
        header="End At"
        :styles="{ width: '15rem' }"
      ></Column>
      <Column sortable field="cancel_at" header="Cancel At"></Column>
      <Column sortable field="cancel_by" header="Cancel By"></Column>
      <Column sortable field="reason" header="Reason"></Column>
      <Column sortable field="lesson.trainee.full_name" header="Trainee">
        <template #body="slotProps">
          <router-link
            :to="{
              name: 'admin-trainees-details',
              params: { id: slotProps.data.trainee_id },
            }"
          >
            {{ slotProps.data.lesson.trainee.full_name }}
          </router-link>
        </template>
      </Column>
      <Column sortable field="instructor" header="Instructor">
        <template #body="slotProps">
          <router-link
            :to="
              '/admin/instructors/details/' +
              slotProps.data.lesson.instructor_id
            "
          >
            {{ slotProps.data.instructor }}
          </router-link>
        </template>
      </Column>
      <Column sortable field="is_refunded" header="Refund Status"></Column>
      <Column header="Refund Details">
        <template #body="slotProps">
          <v-btn
            color="green accent-2"
            small
            class="text-capitalize"
            @click="details(slotProps.data)"
            >Details
          </v-btn>
        </template>
      </Column>
    </DatatableApi>
  </div>
</template>

<script>
import DatatableApi from "../../../components/Global/DatatableApi.vue";
export default {
  name: "TransactionDatatablePrimeVue",
  components: { DatatableApi },
  data() {
    return {
      data: [],
      detailsDialog: false,
    };
  },
  methods: {
    details(data) {
      this.data = { ...data };
      this.detailsDialog = true;
    },
  },
};
</script>
