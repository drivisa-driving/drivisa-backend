<template>
  <MasterAdminLayout>
    <v-card elevation="0" class="profile_section border">
      <v-card-text>
        <InstructorDatatablePrimeVue />
      </v-card-text>
    </v-card>
  </MasterAdminLayout>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import InstructorDatatablePrimeVue from "../../../components/Admin/Instructors/InstructorDatatablePrimeVue";
export default {
  name: "InstructorsView",
  components: { MasterAdminLayout, InstructorDatatablePrimeVue },
};
</script>

<style scoped>
@media (max-width: 960px) {
  .profile_section {
    padding-top: 50px !important;
  }
}
</style>