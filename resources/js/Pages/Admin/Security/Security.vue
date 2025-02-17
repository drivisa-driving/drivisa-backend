<template>
  <div>
    <MasterAdminLayout>
      <v-card elevation="0" class="profile_section">
        <v-card-text class="mt-3 mt-md-0 test">
          <div class="page-header pl-0">
            <p class="page-heading">Security</p>
          </div>
          <hr/>

          <TabView>
            <TabPanel header="Change Password">
              <ChangePassword/>
            </TabPanel>
            <TabPanel header="Login History">
              <LoginHistory/>
            </TabPanel>
            <TabPanel header="Delete Account">
              <DeleteAccount/>
            </TabPanel>
            <TabPanel header="Export Database">
              <v-btn color="primary btn-outline" @click="downloadDatabaseDump"
              >Export Database
              </v-btn
              >
            </TabPanel>
            <TabPanel header="Clear Telescope Entries">
              <v-btn color="primary btn-outline" @click="clearTelescopeEntries"
              >Clear Telescope Entries
              </v-btn
              >
            </TabPanel>
            <TabPanel header="Logs">
              <Logs/>
            </TabPanel>
          </TabView>
        </v-card-text>
      </v-card>
    </MasterAdminLayout>
  </div>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import ChangePassword from "../../../components/Admin/Security/ChangePassword.vue";
import DeleteAccount from "../../../components/Admin/Security/DeleteAccount.vue";
import LoginHistory from "../../../components/Admin/Security/LoginHistory.vue";
import moment from "moment";
import Logs from '../Logs/logs.vue'

export default {
  name: "Security",
  components: {
    MasterAdminLayout,
    ChangePassword,
    DeleteAccount,
    LoginHistory,
    Logs
  },
  methods: {
    async downloadDatabaseDump() {
      try {
        const filename = `drivisa-database-${moment().format(
            "YYYY-MM-DD"
        )}.sql`;

        await axios
            .get("/v1/drivisa/admin/export-database")
            .then((response) => {
              const url = window.URL.createObjectURL(new Blob([response.data]));
              const link = document.createElement("a");
              link.href = url;
              link.setAttribute(
                  "download",
                  `drivisa-production-${moment().format("YYYY-MM-DD")}.sql`
              );
              document.body.appendChild(link);
              link.click();
            });
      } catch (e) {
        this.$root.handleErrorToast(e, "Enable to export database");
      }
    },
    async clearTelescopeEntries() {
      try {
        await axios
            .post("/v1/drivisa/admin/clear-telescope-entries")
            .then((response) => {
              this.$toasted.success("Telescope entries cleared successfully");
            });
      } catch (e) {
        this.$root.handleErrorToast(e, "Enable to prune telescope entries");
      }
    },
  },
};
</script>

<style scoped lang="scss">
@media (max-width: 960px) {
  .profile_section {
    padding-top: 50px !important;
  }
}
</style>
