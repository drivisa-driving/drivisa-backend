<template>
  <div class="footer-bg">
    <div class="d-container">
      <div class="row">
        <div class="col-lg-8">
          <div class="d_footer_logo">
            <router-link to="/">
              <img
                src="/assets/images/drivisa-logo-light.svg"
                alt=""
                class="img-fluid"
                width="150px"
              />
            </router-link>
          </div>
          <ul class="d-lg-flex">
            <li>
              <router-link to="/" class="active">Home</router-link>
            </li>
            <li>
              <router-link to="/faq" class="">FAQ</router-link>
            </li>
            <li>
              <router-link to="/about-us">About Us</router-link>
            </li>
            <li>
              <router-link to="/terms-conditions"
                >Terms &amp; Conditions</router-link
              >
            </li>
            <li>
              <router-link to="/privacy-policy">Privacy Policy</router-link>
            </li>
          </ul>
          <p class="copyright d-none d-lg-block">
            © {{ new Date().getFullYear() }} Drivisa ; All rights reserved
          </p>
        </div>
        <div class="col-lg-4">
          <div class="float-lg-right">
            <h6 class="get_app">Get the Apps</h6>
            <img
              src="/assets/media/imgs/download_on_the_App_Store_Badge 1.png"
              alt="download_on_the_App_Store_Badge"
              style="margin-bottom: 10px"
            />
            <br />
            <img
              style="border-radius: 8px"
              src="/assets/media/imgs/google-play.png"
              alt="google-play"
            />
            <br />
            <p class="copyright d-lg-none mt-3">
              © {{ new Date().getFullYear() }} Drivisa ; All rights reserved
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Footer",
};
</script>

<style scoped lang="scss">
@import "resources/js/scss/vue_variables";

.footer-bg {
  background: $footer !important;
  padding: 2rem;
}

.d_footer_logo {
  margin-bottom: 30px;
}
ul {
  padding: 0;
  margin-bottom: 30px;
  flex-direction: row;
  box-sizing: border-box;
  list-style: none;
  li {
    margin-right: 20px;
    margin-bottom: 20px;
    a {
      color: #c8c8c8;
      text-decoration: none !important;
      background-color: transparent;
      &:hover {
        color: #fff;
      }
    }
  }
}
.copyright {
  color: #8b8b8b;
  font-size: 90%;
  margin: 0 0 12px;
}
.get_app {
  margin-bottom: 10px;
  align-self: flex-start;
  color: #c8c8c8;
}
</style>