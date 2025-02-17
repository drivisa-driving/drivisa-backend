<template>
  <div>
    <MasterInstructorLayout>
      <v-card elevation="0" class="border">
        <v-card-text class="mt-5 mt-md-0 test">
          <v-text-field
              label="Old Password *"
              placeholder="Old Password *"
              v-model="security.current_password"
              :type="type"
              outlined
              :append-icon="icon"
              @click:append="changePasswordType"
          ></v-text-field>
          <v-text-field
              label="New Password *"
              placeholder="New Password *"
              v-model="security.new_password"
              :type="type"
              outlined
              :append-icon="icon"
              @click:append="changePasswordType"
          ></v-text-field>
          <v-text-field
              label="Password Confirmation *"
              placeholder="Password Confirmation *"
              v-model="security.new_password_confirmation"
              :type="type"
              outlined
              :append-icon="icon"
              @click:append="changePasswordType"
          ></v-text-field>
          <v-btn color="primary" class="text-capitalize" @click="updatePassword">
            Save Changes
          </v-btn>

          <h1 class="mt-3 login-history-h1">Login History</h1>
          <div class="login-history"
               v-for="(item, i) in history"
               :key="i"
          >
            <h2 class="login-history-os">{{ item.operatingSystem }}</h2>
            <div class="login-history-detail">
              <span class="login-history-detail-when">{{ item.lastLogin | dateAgo }}</span>
              <span class="login-history-detail-browser">{{ item.browser }}</span>
            </div>
          </div>
        </v-card-text>
      </v-card>
    </MasterInstructorLayout>
  </div>
</template>

<script>
import MasterInstructorLayout from "../Layouts/MasterInstructorLayout";

export default {
  name: "Security",
  components: {MasterInstructorLayout},
  data() {
    return {
      type: 'password',
      icon: "mdi-eye-off",
      history: [],
      security: {
        current_password: "",
        new_password: "",
        new_password_confirmation: ""
      }
    }
  },
  mounted() {
    this.loginHistory();
  },
  methods: {
    changePasswordType() {
      if (this.type === 'password') {
        this.type = 'text';
        this.icon = "mdi-eye"
      } else {
        this.type = 'password';
        this.icon = "mdi-eye-off"
      }
    },
    async updatePassword() {
      try {
        const url = "/v1/user/change-password";
        await axios.post(url, this.security);
        this.clearForm();
        this.$toasted.success("Password Updated Successfully");
      } catch (e) {
        this.$root.handleErrorToast(e, "Please Check Your Details")
      }
    },
    clearForm() {
      this.security = {
        current_password: "",
        new_password: "",
        new_password_confirmation: ""
      }
    },
    async loginHistory() {
      const url = "/v1/auth/login-history";
      const {data} = await axios.get(url);
      this.history = data.data;
    }
  }
}
</script>

<style scoped lang="scss">
.login-history-h1 {
  font-weight: 600;
  letter-spacing: normal;
  margin: 0 0 16px;
  font-size: 20px;
  font-family: Montserrat, sans-serif !important;
  color: rgba(0, 0, 0, .87);
}

.login-history {
  margin: 2rem 0;
  font-family: Montserrat, sans-serif !important;
  color: rgba(0, 0, 0, .87);

  .login-history-os {
    font: 600 20px/20px Montserrat, sans-serif;
    display: block;
    padding-bottom: 5px;
    color: red;
  }

  .login-history-detail {
    .login-history-detail-when {
      font: 400 14px/20px Montserrat, sans-serif;
      padding-bottom: 5px;
      color: #1bc5bd;
    }

    .login-history-detail-browser {
      font: 400 14px/20px Montserrat, sans-serif;
      padding-bottom: 5px;
      color: #1c252c;
    }
  }
}


</style>