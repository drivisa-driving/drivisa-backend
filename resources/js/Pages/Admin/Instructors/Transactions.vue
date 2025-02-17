<template>
  <MasterAdminLayout>
    <v-card elevation="0" class="profile_section">
      <v-card-text>
        <TransactionsComponent />
      </v-card-text>
    </v-card>
  </MasterAdminLayout>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import TransactionsComponent from "../../../components/Admin/Instructors/TransactionsComponent";
export default {
  name: "Transactions",
  components: { MasterAdminLayout, TransactionsComponent },
};
</script>

<style scoped>
@media (max-width: 960px) {
  .profile_section {
    padding-top: 50px !important;
  }
}
</style>