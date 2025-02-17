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
                <h2 class="font-weight-bold  text-center text-md-left">Resend Your Activation Code</h2>
              </div>
              <v-text-field
                  label="Email Address *"
                  placeholder="Email Address *"
                  outlined
                  v-model="account.email"
                  v-on:keydown.enter="resend"
              ></v-text-field>
              <v-btn
                  block
                  class="btn_register"
                  @click="resend"
                  :disabled="account.email ==''"
              >
                Resend
              </v-btn>
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
  name: "ResendCode",
  data() {
    return {
      account: {
        email: "",
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
    clearForm() {
      this.account = {
        email: ""
      }
    },
    async resend() {
      try {
        var url = "/v1/auth/resend-activation-code"
        const {data} = await axios.post(url, this.account)
        this.$toasted.success(data.message)
        this.clearForm()
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