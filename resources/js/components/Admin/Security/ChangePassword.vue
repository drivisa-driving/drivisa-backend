<template>
  <div>
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
    <v-btn
      color="primary btn-outline"
      class="text-capitalize"
      @click="updatePassword"
    >
      Save Changes
    </v-btn>
  </div>
</template>

<script>
export default {
  name: "ChangePassword",
  data() {
    return {
      type: "password",
      icon: "mdi-eye-off",

      security: {
        current_password: "",
        new_password: "",
        new_password_confirmation: "",
      },
    };
  },

  methods: {
    changePasswordType() {
      if (this.type === "password") {
        this.type = "text";
        this.icon = "mdi-eye";
      } else {
        this.type = "password";
        this.icon = "mdi-eye-off";
      }
    },
    async updatePassword() {
      try {
        const url = "/v1/user/change-password";
        await axios.post(url, this.security);
        this.clearForm();
        this.$toasted.success("Password Updated Successfully");
      } catch (e) {
        this.$root.handleErrorToast(e, "Please Check Your Details");
      }
    },
    clearForm() {
      this.security = {
        current_password: "",
        new_password: "",
        new_password_confirmation: "",
      };
    },
  },
};
</script>
