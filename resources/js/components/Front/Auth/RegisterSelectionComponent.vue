<template>
  <div style="min-height: 100vh" class="p-0 register-container">
    <div class="p-0">
      <div class="mx-auto">
        <div style="padding: 1.5rem 3.5rem;">
          <v-card-title
              class=" justify-content-between px-0">
            <router-link to="/">
              <img src="/assets/images/drivisa-logo-light.svg" alt="" width="100px">
            </router-link>
            <v-btn
                outlined
                color="white"
                class="text-decoration-none"
                to="login"
                style="text-transform: capitalize; font-size: 14px"
            >
              Sign In
            </v-btn>
          </v-card-title>
        </div>
        <div>
          <v-row justify="center" align="center" style="height: calc(80vh - 4rem)">
            <v-card>
              <v-card-title style="font-size: 16px; margin-bottom: 0.5rem">
                Select Type Of Your Account
              </v-card-title>
              <div class="d-list">
                <router-link
                    to="register?usertype=2"
                    class="d-list-item"
                >
                  <p class="m-0">Student Account</p>
                  <span class="float-right mdi mdi-chevron-right" style="font-size: 24px;"></span>
                </router-link>
                <router-link
                    to="register?usertype=1"
                    class="d-list-item"
                ><p class="m-0">Instructor Account </p>
                  <span class="float-right mdi mdi-chevron-right" style="font-size: 24px;"></span>
                </router-link>
              </div>
            </v-card>
          </v-row>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "RegisterSelectionComponent",
  mounted() {
    let elHtml = document.getElementsByTagName('html')[0]
    elHtml.style.overflowY = 'hidden'
  },
  destroyed() {
    let elHtml = document.getElementsByTagName('html')[0]
    elHtml.style.overflowY = null
  },
}
</script>

<style lang="scss" scoped>
html {
  @media screen and (max-width: 900px) {
    overflow-y: scroll !important;
  }
}

.register-container {
  background-image: url("/assets/images/bg/Rectangle-563.jpg");
  background-size: cover;
}

.register-container {
  //display: grid;
  //grid-template-columns: 70% 30%;
  //grid-template-areas: "auth-form auth-img";
  //height: 100vh;

  @media screen and (max-width: 900px) {
    grid-template-columns: 1fr;
    grid-template-areas: "auth-form";
    padding: 2rem 0;
  }
}

.d-list {
  width: min(70vw, 30rem);
  padding: 5px 20px;
  display: flex;
  flex-direction: column;

  .d-list-item {
    margin: 0.5rem 0;
    padding: .5rem;
    display: flex;
    justify-content: space-between;
    background-color: #c4c4c41a;
    cursor: pointer;
    color: #000;
    text-decoration: none;
    line-height: 30px;
  }
}
</style>