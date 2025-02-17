<template>
  <MasterAdminLayout>
    <v-card elevation="0" class="profile_section">
      <v-card-text>
        <DetailsComponent />
      </v-card-text>
    </v-card>
  </MasterAdminLayout>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import DetailsComponent from "../../../components/Admin/Instructors/DetailsComponent";

export default {
  components: {MasterAdminLayout,DetailsComponent}
}
</script>

<style scoped>

@media (max-width: 960px) {
  .profile_section {
    padding-top: 50px!important;
  }
}
</style>