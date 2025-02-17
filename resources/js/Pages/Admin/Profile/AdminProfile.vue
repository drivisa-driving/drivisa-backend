<template>
  <div>
    <MasterAdminLayout>
      <v-card elevation="0" class="profile_section border">
        <v-card-text v-if="profile != null">
          <div
            class="cover-section mb-3"
            :style="{ backgroundImage: 'url(' + profile.cover + ')' }"
          >
            <div class="flex-box-start"></div>
            <div class="instructor-avatar">
              <img
                class="avatar"
                style="margin-bottom: 10px"
                :src="profile.avatar"
              />
              <h5 class="font-weight-bold mb-3 mt-3">{{ profile.fullName }}</h5>
            </div>
            <div>
              <router-link to="/admin/edit-profile" class="edit-profile-btn">
                <i class="mdi mdi-pencil"></i>
                <span class="d-none d-md-block">Edit Profile</span>
              </router-link>
            </div>
          </div>
          <table class="table table-bordered">
            <tr>
              <td>Name</td>
              <td>{{ profile.firstName }}</td>
            </tr>
            <tr>
              <td>Full Name</td>
              <td>{{ profile.fullName }}</td>
            </tr>
            <tr>
              <td>Email</td>
              <td>{{ profile.email }}</td>
            </tr>
          </table>
        </v-card-text>
      </v-card>
    </MasterAdminLayout>
  </div>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
export default {
  name: "AdminProfile",
  components: { MasterAdminLayout },
  computed: {
    profile() {
      return this.$store.state.user.user;
    },
  },
};
</script>
