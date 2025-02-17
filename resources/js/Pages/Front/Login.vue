<template>
  <div style="min-height: 100vh" class="p-0 login-container">
    <div class="p-0">
      <div class="mx-auto">
        <v-container style="padding: 1.5rem 3.5rem">
          <v-card-title class="justify-content-between px-0">
            <router-link to="/">
              <img src="/assets/media/logos/drivisa-logo.svg" alt="" />
            </router-link>
          </v-card-title>
        </v-container>

        <v-container>
          <v-row style="height: 85vh" justify="center" align="center">
            <v-col md="7">
              <div style="margin-bottom: 4rem">
                <p class="welcome-text text-center text-md-left">
                  Welcome Back
                </p>
                <h2 class="font-weight-bold text-center text-md-left">
                  Log Into Your Account
                </h2>
              </div>
              <ValidationProvider
                name="Email Address"
                rules="email"
                v-slot="{ errors }"
              >
                <v-text-field
                  label="Email Address *"
                  placeholder="Email Address *"
                  v-on:keydown.enter="loginUser"
                  outlined
                  :error-messages="errors[0]"
                  v-model="login.email"
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider
                name="Password"
                rules="required"
                v-slot="{ errors }"
              >
                <v-text-field
                  label="Password *"
                  placeholder="Password *"
                  v-model="login.password"
                  v-on:keydown.enter="loginUser"
                  :type="type"
                  outlined
                  :error-messages="errors[0]"
                  :append-icon="icon"
                  @click:append="changePasswordType"
                ></v-text-field>
              </ValidationProvider>
              <v-btn
                block
                class="btn_login btn-outline"
                @click="loginUser"
                v-if="!isLogin"
                v-on:keydown.enter="loginUser"
              >
                Login
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
      <img src="/assets/images/bg/login-bg.png" class="login-side-image" />
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "Login",
  data() {
    return {
      isLogin: false,
      type: "password",
      icon: "mdi-eye-off",
      login: {
        email: "",
        password: "",
      },
    };
  },
  mounted() {
    let elHtml = document.getElementsByTagName("html")[0];
    elHtml.style.overflowY = "hidden";
  },
  destroyed() {
    let elHtml = document.getElementsByTagName("html")[0];
    elHtml.style.overflowY = null;
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
    async loginUser() {
      try {
        this.isLogin = true;
        var url = "/v1/auth/login";
        const { data } = await axios.post(url, {
          email: this.login.email,
          password: this.login.password,
        });

        var token = data.user.token.token;

        axios.defaults.headers.common["Authorization"] = "Bearer " + token;
        localStorage.setItem("access_token", token);

        axios
          .get("/v1/auth/me", {
            headers: {
              Authorization: "Bearer " + token,
            },
          })
          .then((res) => {
            localStorage.setItem("access_token", res.data.user.token.token);
            axios.defaults.headers.common["Authorization"] =
              "Bearer " + res.data.user.token.token;
            this.$store.dispatch("setUser", res.data.user);

            let user = this.$store.state.user.user;

            if (user.userType === 0) {
              this.$router.push("/admin/dashboard");
            } else {
              this.$store.dispatch("logout");
              this.$toasted.error(
                "Please Check Your Login Details Or Try To Login In Mobile Application"
              );
            }
          });
      } catch (e) {
        this.$root.handleErrorToast(e, "Please Check Your Login Details");
      } finally {
        this.isLogin = false;
      }
    },
  },
};
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
  letter-spacing: -0.1px !important;
}

.btn_login {
  padding: 2rem !important;
  border-radius: 4px;
  background: #3366cc !important;
  color: white !important;
  text-transform: capitalize !important;
  font-size: 20px !important;
  font-weight: 500 !important;
  font-family: "Nunito", sans-serif !important;
}

.login-container {
  display: grid;
  grid-template-columns: 70% 30%;
  grid-template-areas: "auth-form auth-img";
  height: 80vh;
  overflow-y: hidden;

  @media screen and (max-width: 900px) {
    grid-template-columns: 1fr;
    grid-template-areas: "auth-form";
    padding: 2rem 0;
    overflow-y: scroll !important;
  }
}

.login-side-image {
  width: 100%;
  height: 100% !important;
  object-fit: cover;

  @media screen and (max-width: 900px) {
    display: none;
  }
}
</style>
