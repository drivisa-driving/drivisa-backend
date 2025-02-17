<template>
  <div>
    <MasterInstructorLayout>
      <v-card elevation="0" class="border">
        <v-card-text>
          <h1 class="setting-title">Settings</h1>
          <div class="setting-info">
            <h2>Delete account</h2>
            <p>
              Once your account is deleted, all of its resources and data will be
              permanently deleted. Before deleting your account, please download any
              data or information that you wish to retain.
            </p>
          </div>
          <UserDeleteDialog/>
        </v-card-text>
      </v-card>
    </MasterInstructorLayout>
  </div>
</template>

<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";
import UserDeleteDialog from "../../../components/UserDeleteDialog";

export default {
  name: "Settings",
  components: {MasterInstructorLayout, UserDeleteDialog}
}
</script>

<style scoped>
.setting-title {
  font-weight: 400;
  font-size: 24px;
  letter-spacing: normal;
  margin: 0 0 16px;
  font-family: Montserrat, sans-serif !important;
  color: rgba(0, 0, 0, .87);
}

.setting-info {
  width: 70%;
  margin: 3rem 0;
  font-family: Montserrat, sans-serif !important;
  line-height: 1.4 !important;
  letter-spacing: -.1px !important;
}

.setting-info h2 {
  font: 500 20px/32px Montserrat, sans-serif;
  letter-spacing: normal;
  margin: 0 0 16px;
  color: rgba(0, 0, 0, .87);
}

.setting-info p {
  font-size: 16px;
  color: rgba(28, 37, 44, .85);
}
</style>