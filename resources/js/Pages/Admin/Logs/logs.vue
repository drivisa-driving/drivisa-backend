<template>
    <v-card elevation="0" class="profile_section">
      <v-card-text>
        <TabView ref="tabview1">
          <TabPanel header="Notification Logs">
          <Notifications/>
          </TabPanel>
<!--          <TabPanel header="Lessons">-->
<!--          2-->
<!--          </TabPanel>-->


        </TabView>
      </v-card-text>
    </v-card>
</template>

<script>
import Notifications from "./Notifications.vue"
export default {
  components: {Notifications}
}
</script>

<style scoped>

@media (max-width: 960px) {
  .profile_section {
    padding-top: 50px!important;
  }
}
</style>