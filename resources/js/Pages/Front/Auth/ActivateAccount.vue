<template>
  <div style="min-height: 100vh" class="p-0 register-container">
    <div class="p-0">
      <div class="mx-auto">
        <v-container style="padding: 1.5rem 3.5rem;">
          <v-card-title
              class=" justify-content-between">
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
                <h2 class="font-weight-bold  text-center text-md-left">Reset Your Password</h2>
              </div>
              <v-text-field
                  label="Code *"
                  placeholder="Code *"
                  outlined
                  v-model="account.code"
                  v-on:keydown.enter="activate"
              ></v-text-field>
              <v-btn
                  block
                  class="btn_register"
                  @click="activate"
                  :disabled="account.code ==''"
              >
                Activate
              </v-btn>
              <div class="mt-3">
                <router-link to="/resend-account-activation-code" class="text-decoration-none"
                             style="color:#36c; font-size: 14px"
                >Resend Activation Code
                </router-link>
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
  name: "ActivateAccount",
  data() {
    return {
      type: 'password',
      icon: "mdi-eye-off",
      account: {
        username: "",
        code: "",
      }
    }
  },
  mounted() {
    this.account.username = this.$route.query.username;
    this.account.code = this.$route.query.code;
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
      this.account = {
        code: ""
      }
    },
    async activate() {
      try {
        var url = "/v1/auth/activate"
        const {data} = await axios.post(url, this.account)
        this.$toasted.success(data.message)
        this.clearForm()
        await this.$router.push("/login");
      } catch (e) {
        this.$root.handleErrorToast(e, "Please Check Your Details")
      }
    }
  }
}
</script>


<style lang="scss">
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