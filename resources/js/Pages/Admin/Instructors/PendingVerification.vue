<template>
  <MasterAdminLayout>
    <v-card elevation="0" class="profile_section border mb-4">
      <v-card-text>
        <h3>Instructor Pending Verifications</h3>
        <InstructorDatatablePrimeVue :show-btn="true" :only-verified="true" />
      </v-card-text>
    </v-card>
  </MasterAdminLayout>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import InstructorDatatablePrimeVue from "../../../components/Admin/Instructors/InstructorDatatablePrimeVue";

export default {
  name: "PendingVerification",
  components: { MasterAdminLayout, InstructorDatatablePrimeVue },
};
</script>

<style scoped>
@media (max-width: 530px) {
  h3 {
    width: 400px;
  }
}
</style>