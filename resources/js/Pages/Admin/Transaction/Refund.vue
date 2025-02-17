<template>
  <div>
    <MasterAdminLayout>
      <v-card elevation="0" class="profile_section">
        <v-card-text>
          <RefundDatatablePrimeVue />
        </v-card-text>
      </v-card>
    </MasterAdminLayout>
  </div>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import RefundDatatablePrimeVue from "../../../components/Admin/History/RefundDatatablePrimeVue";
export default {
  name: "Refund",
  components: {
    RefundDatatablePrimeVue,
    MasterAdminLayout,
  },
};
</script>