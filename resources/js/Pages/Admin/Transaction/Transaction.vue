<template>
  <div>
    <MasterAdminLayout>
      <v-card elevation="0" class="profile_section">
        <v-card-text>
          <TransactionDatatablePrimeVue />
        </v-card-text>
      </v-card>
    </MasterAdminLayout>
  </div>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import TransactionDatatablePrimeVue from "../../../components/Admin/History/TransactionDatatablePrimeVue";
export default {
  name: "Transition",
  components: {
    TransactionDatatablePrimeVue,
    MasterAdminLayout,
  },
};
</script>