<template>
  <div>
    <v-dialog v-model="detailsDialog" width="500">
      <v-card>
        <h3 class="text-h5 text-center py-3">Details</h3>
        <div class="m-3">
          <table class="table table-bordered">
            <tr>
              <td colspan="2"><strong>Purchase Details</strong></td>
            </tr>
            <tr>
              <td>Cost</td>
              <td>{{ moreDetails.cost }}</td>
            </tr>
            <tr>
              <td>Lesson Tax</td>
              <td>{{ moreDetails.tax }}</td>
            </tr>
            <tr>
              <td>Additional Tax</td>
              <td>{{ moreDetails.additionalTax }}</td>
            </tr>
            <tr>
              <td>Additional Km</td>
              <td>{{ moreDetails.additionalCost }}</td>
            </tr>
            <tr>
              <td>Total Purchase Amount</td>
              <td>{{ moreDetails.purchase_amount }}</td>
            </tr>
            <tr>
              <td>Payment By</td>
              <td>{{ moreDetails.paymentBy }}</td>
            </tr>
            <tr class="mt-1">
              <td colspan="2"><strong>Refund Details</strong></td>
            </tr>
            <tr>
              <td>Time Left</td>
              <td>{{ moreDetails.time_left }} Minutes</td>
            </tr>
            <tr>
              <td>Refund ID</td>
              <td>{{ moreDetails.refund_id }}</td>
            </tr>
            <tr>
              <td>Instructor Fee</td>
              <td>{{ moreDetails.instructor_fee }}</td>
            </tr>
            <tr>
              <td>Drivisa Fee</td>
              <td>{{ moreDetails.drivisa_fee }}</td>
            </tr>
            <tr>
              <td>Refund</td>
              <td>{{ moreDetails.refund }}</td>
            </tr>
          </table>
        </div>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="detailsDialog = false"> Close </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <DatatableApi
      :endpoint="`/v1/drivisa/admin/trainees/refunds/${this.$route.params.id}`"
    >
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
      <Column sortable field="instructor" header="Instructor Name">
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
import DatatableApi from "../../../Global/DatatableApi.vue";
export default {
  name: "Refunds",
  components: {
    DatatableApi,
  },
  data() {
    return {
      detailsDialog: false,
      moreDetails: {},
    };
  },
  methods: {
    details(data) {
      this.moreDetails = data;
      this.detailsDialog = true;
    },
  },
};
</script>