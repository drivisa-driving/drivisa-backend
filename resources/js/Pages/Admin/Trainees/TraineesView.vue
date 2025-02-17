<template>
  <MasterAdminLayout>
    <v-card elevation="0" class="profile_section border">
      <v-card-text>
        <v-tabs left>
          <v-tab style="font-weight: 700"> All Trainees </v-tab>
          <v-tab style="font-weight: 700"> Unpurchased Trainees </v-tab>

          <v-tab-item>
            <TraineeDatatablePrimeVue />
          </v-tab-item>
          <v-tab-item>
            <UnpurchasedTrainees />
          </v-tab-item>
        </v-tabs>
      </v-card-text>
    </v-card>
  </MasterAdminLayout>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import TraineeDatatablePrimeVue from "../../../components/Admin/Trainees/TraineeDatatablePrimeVue";
import UnpurchasedTrainees from "./UnpurchasedTrainees.vue";

export default {
  name: "TraineesView",
  components: {
    MasterAdminLayout,
    TraineeDatatablePrimeVue,
    UnpurchasedTrainees,
  },
};
</script>

<style scoped>
@media (max-width: 960px) {
  .profile_section {
    padding-top: 50px !important;
  }
}
</style>