<template>
  <div style="min-height: 100vh" class="p-0 register-container">
    <div class="p-0">
      <div class="mx-auto">
        <v-container style="padding: 1.5rem 3.5rem;">
          <v-card-title
              class=" justify-content-between px-0">
            <router-link to="/">
              <img src="/assets/media/logos/drivisa-logo.svg" alt="">
            </router-link>
            <v-btn
                outlined
                small
                class="border-0 text-decoration-none"
                to="login"
                style="text-transform: capitalize; font-size: 14px"
            >
              login
            </v-btn>
          </v-card-title>
        </v-container>

        <v-container>
          <v-row style="height: 85vh" justify="center" align="center">
            <v-col md="7">
              <div style="margin-bottom:4rem ">
                <h2 class="font-weight-bold  text-center text-md-left">Register New Student Account</h2>
              </div>
              <ValidationProvider name="first name" rules="required" v-slot="{ errors }">
                <v-text-field
                    outlined
                    :error-messages="errors[0]"
                    label="First Name *"
                    placeholder="First Name"
                    v-model="register.first_name"
                    v-on:keydown.enter="registerUser"
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider name="last name" rules="required" v-slot="{ errors }">
                <v-text-field
                    outlined
                    :error-messages="errors[0]"
                    label="Last Name *"
                    placeholder="Last Name * "
                    v-model="register.last_name"
                    v-on:keydown.enter="registerUser"
                >
                </v-text-field>
              </ValidationProvider>
              <ValidationProvider name="email address" rules="email" v-slot="{ errors }">
                <v-text-field
                    outlined
                    :error-messages="errors[0]"
                    label="Email Address *"
                    placeholder="Email Address * "
                    v-model="register.email"
                    v-on:keydown.enter="registerUser"
                ></v-text-field>
              </ValidationProvider>
              <ValidationObserver>
                <ValidationProvider v-slot="{ errors }"  vid="confirmation">
                  <v-text-field
                      label="Password *"
                      placeholder="Password *"
                      v-model="register.password"
                      :type="type"
                      outlined
                      :append-icon="icon"
                      @click:append="changePasswordType"
                      v-on:keydown.enter="registerUser"
                  ></v-text-field>
                </ValidationProvider>
                <ValidationProvider v-slot="{ errors }"  name="password" rules="confirmed:confirmation">
                  <v-text-field
                      label="Confirm Password *"
                      placeholder="ConfirmPassword *"
                      v-model="register.password_confirmation"
                      :type="type"
                      outlined
                      :error-messages="errors[0]"
                      :append-icon="icon"
                      @click:append="changePasswordType"
                      v-on:keydown.enter="registerUser"
                  ></v-text-field>
                </ValidationProvider>
              </ValidationObserver>
              <div class="d-flex">
                <v-checkbox
                    class="mt-0"
                    v-model="checkbox"
                ></v-checkbox>
                <v-dialog
                    v-model="dialog"
                    width="500"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <p
                        style="margin-top: 7px"
                        v-bind="attrs"
                        v-on="on"
                    >
                  I agree Privacy Policy
                    </p>
                  </template>

                  <v-card>
                    <v-card-title class="text-h5 grey lighten-2">
                      Privacy Policy
                    </v-card-title>

                    <v-card-text>
                      Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                      labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
                      laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in
                      voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat
                      non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn
                          color="primary"
                          text
                          @click="dialog = false"
                      >
                        I accept
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
              </div>
              <v-btn
                  :disabled="checkbox === false"
                  block
                  class="btn_register"
                  v-if="!isRegister"
                  @click="registerUser">
                Register
              </v-btn>
              <div class="text-center" v-else>
                <v-progress-circular
                    disabled
                    indeterminate
                    color="primary"
                ></v-progress-circular>
              </div>
            </v-col>
          </v-row>
        </v-container>

      </div>
    </div>
    <div class="p-0">
      <img src="/assets/media/imgs/register.png" class="register-side-image">
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: "StudentRegisterComponent",
  data() {
    return {
      checkbox: false,
      dialog: false,
      isRegister: false,
      type: 'password',
      icon: "mdi-eye-off",
      register: {
        first_name: "",
        last_name: "",
        email: "",
        password: "",
        password_confirmation: "",
        user_type: 2
      }
    }
  },
  mounted() {
    let elHtml = document.getElementsByTagName('html')[0]
    elHtml.style.overflowY = 'hidden'
  },
  destroyed() {
    let elHtml = document.getElementsByTagName('html')[0]
    elHtml.style.overflowY = null
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
    clearForm() {
      this.register = {
        first_name: "",
        last_name: "",
        email: "",
        password: "",
        password_confirmation: "",
        user_type: 2
      }
    },
    async registerUser() {
      try {
        this.isRegister = true;
        var url = "/v1/auth/register"
        const {data} = await axios.post(url, this.register)
        this.$toasted.success(data.message)
        this.clearForm()
      } catch (e) {
        this.$root.handleErrorToast(e, "Please Check Your Register Details")
      } finally {
        this.isRegister = false
      }
    }
  }
}
</script>


<style lang="scss" scoped>
html {
  @media screen and (max-width: 900px) {
    overflow-y: scroll !important;
  }
}

.welcome-text {
  font-weight: 500;
  font-style: normal;
  font-variant: normal;
  text-transform: none;
  text-decoration: rgb(0, 0, 0);
  text-align: left;
  text-indent: 0;
  font-size: 20px;
  line-height: 25px;
  margin: 0 0 12px;
  letter-spacing: -.1px !important;
}

.btn_register {
  padding: 2rem !important;
  border-radius: 4px;
  background: #3366cc !important;
  color: white !important;
  text-transform: capitalize !important;
  font-size: 20px !important;
  font-weight: 500 !important;
  font-family: "Nunito", sans-serif !important;
}

.register-container {
  display: grid;
  grid-template-columns: 70% 30%;
  grid-template-areas: "auth-form auth-img";
  height: 100vh;

  @media screen and (max-width: 900px) {
    grid-template-columns: 1fr;
    grid-template-areas: "auth-form";
    padding: 2rem 0;
    overflow-y: scroll !important;
  }
}

.register-side-image {
  width: 100%;
  height: 100% !important;
  object-fit: cover;

  @media screen and (max-width: 900px) {
    display: none;
  }
}


</style>