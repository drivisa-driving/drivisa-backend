<template>
  <MasterAdminLayout>
    <v-card elevation="0" class="profile_section">
      <v-card-text>
        <TransactionComponent />
      </v-card-text>
    </v-card>
  </MasterAdminLayout>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import TransactionComponent from "../../../components/Admin/Trainees/TransactionComponent";
export default {
  name: "Transactions",
  components: { MasterAdminLayout, TransactionComponent },
};
</script>

<style scoped>
@media (max-width: 960px) {
  .profile_section {
    padding-top: 50px !important;
  }
}
</style>