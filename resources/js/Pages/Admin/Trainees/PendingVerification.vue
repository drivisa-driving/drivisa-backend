<template>
  <MasterAdminLayout>
    <v-card elevation="0" class="profile_section border">
      <v-card-text>
        <TraineeDatatablePrimeVue :show-btn="true" :only-verified="true" />
      </v-card-text>
    </v-card>
  </MasterAdminLayout>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import TraineeDatatablePrimeVue from "../../../components/Admin/Trainees/TraineeDatatablePrimeVue";

export default {
  name: "PendingVerification",
  components: { MasterAdminLayout, TraineeDatatablePrimeVue },
};
</script>

<style scoped>
@media (max-width: 960px) {
  .profile_section {
    padding-top: 50px !important;
  }
}
</style>