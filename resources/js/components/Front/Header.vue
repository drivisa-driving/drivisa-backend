<template>
  <div class="header">
    <div class="d-container-lg">
      <div class="header_item">
        <!--  Header Logo    -->
        <div class="logo">
          <router-link to="/">
            <img
              src="/assets/media/logos/drivisa-logo200_80.svg"
              tabindex="0"
            />
          </router-link>
        </div>
        <!--   Header Logo Close    -->
        <!-- <div>
          <router-link to="/" exact-active-class="al" class="web_nav"
            >Home</router-link
          >
          <router-link to="/faq" exact-active-class="al" class="web_nav"
            >FAQ</router-link
          >
          <router-link to="/instructors" exact-active-class="al" class="web_nav"
            >Instructors</router-link
          >
          <router-link
            to="/package/lessons"
            exact-active-class="al"
            class="web_nav"
            >Lessons</router-link
          >
          <router-link
            to="/road-test/rental"
            exact-active-class="al"
            class="web_nav"
            >Car Rental</router-link
          >
          <router-link to="/bde" exact-active-class="al" class="web_nav"
            >BDE</router-link
          >
          <v-menu offset-y>
            <template v-slot:activator="{ attrs, on }">
              <span class="web_nav" v-bind="attrs" v-on="on">Corporate</span>
            </template>
            <v-list>
              <router-link
                to="/about-us"
                v-slot="{ href, navigate, isExactActive }"
              >
                <a
                  v-bind:class="{ al: isExactActive, 'text-body': hasError }"
                  :href="href"
                  @click="navigate"
                >
                  <v-list-item link :class="{ al: isExactActive }">
                    About Us
                  </v-list-item>
                </a>
              </router-link>
              <router-link
                to="/terms-conditions"
                v-slot="{ href, navigate, isExactActive }"
              >
                <a
                  v-bind:class="{ al: isExactActive, 'text-body': hasError }"
                  :href="href"
                  @click="navigate"
                >
                  <v-list-item link :class="{ al: isExactActive }">
                    Terms & Conditions
                  </v-list-item>
                </a>
              </router-link>
              <router-link
                to="/privacy-policy"
                v-slot="{ href, navigate, isExactActive }"
              >
                <a
                  v-bind:class="{ al: isExactActive, 'text-body': hasError }"
                  :href="href"
                  @click="navigate"
                >
                  <v-list-item link :class="{ al: isExactActive }">
                    Privacy Policy
                  </v-list-item>
                </a>
              </router-link>
            </v-list>
          </v-menu>
        </div> -->
        <!--   Nav Link Close    -->
        <!--  Login    -->
        <div v-if="$store.getters.isAuthenticated">
          <v-menu offset-y bottom>
            <template v-slot:activator="{ attrs, on }">
              <span v-bind="attrs" v-on="on" class="user_profile_box d-flex">
                <span
                  :class="{
                    'mt-3': $store.state.user.user.userType === 0,
                    'mt-3': $store.state.user.user.userType === 1,
                  }"
                  >{{ $store.state.user.user.fullName }} <br />
                </span>
                <br />
                <img
                  :src="$store.state.user.user.avatar"
                  class="img-fluid"
                  alt="User Image"
                />
              </span>
            </template>
            <v-list style="top: 80px">
              <router-link :to="profileLink">
                <v-list-item> My Profile </v-list-item>
              </router-link>
              <v-list-item @click="$store.dispatch('logout')">
                Logout
              </v-list-item>
            </v-list>
          </v-menu>
        </div>
        <div v-else>
          <router-link class="login_btn" to="/login">
            <span class="mat-button-wrapper"> Sign In </span>
            <i class="mdi mdi-login"></i>
          </router-link>
        </div>
        <!--  Login Close    -->
      </div>
    </div>

    <!-- Mobile Header  -->

    <div class="mobile_menu">
      <div class="nav-mobile">
        <div class="menu-container nav_item">
          <input id="menu-toggle" type="checkbox" />
          <label for="menu-toggle" class="menu-button">
            <svg class="icon-open" viewBox="0 0 24 24">
              <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"></path>
            </svg>
            <svg class="icon-close" viewBox="0 0 100 100">
              <path
                d="M83.288 88.13c-2.114 2.112-5.575 2.112-7.69 0L53.66 66.188c-2.113-2.112-5.572-2.112-7.686 0l-21.72 21.72c-2.114 2.113-5.572 2.113-7.687 0l-4.693-4.692c-2.114-2.114-2.114-5.573 0-7.688l21.72-21.72c2.112-2.115 2.112-5.574 0-7.687L11.87 24.4c-2.114-2.113-2.114-5.57 0-7.686l4.842-4.842c2.113-2.114 5.57-2.114 7.686 0l21.72 21.72c2.114 2.113 5.572 2.113 7.688 0l21.72-21.72c2.115-2.114 5.574-2.114 7.688 0l4.695 4.695c2.112 2.113 2.112 5.57-.002 7.686l-21.72 21.72c-2.112 2.114-2.112 5.573 0 7.686L88.13 75.6c2.112 2.11 2.112 5.572 0 7.687l-4.842 4.84z"
              />
            </svg>
          </label>
          <!-- <ul class="menu-sidebar">
            <li v-if="!$store.getters.isAuthenticated">
              <router-link to="/login" exact-active-class="mal">
                Sign In</router-link
              >
            </li>
            <li>
              <router-link to="/" exact-active-class="mal">Home</router-link>
            </li>
            <li>
              <router-link to="/faq" exact-active-class="mal">FAQ</router-link>
            </li>
            <li>
              <router-link to="/instructors" exact-active-class="mal"
                >Instructors</router-link
              >
            </li>
            <li>
              <router-link to="/lessons" exact-active-class="mal"
                >Lessons</router-link
              >
            </li>
            <li>
              <router-link to="/bde" exact-active-class="mal">BDE</router-link>
            </li>
            <li>
              <router-link to="/about-us" exact-active-class="mal"
                >About Us</router-link
              >
            </li>
            <li>
              <router-link to="/terms-conditions" exact-active-class="mal"
                >Terms & Conditions</router-link
              >
            </li>
            <li>
              <router-link to="/privacy-policy" exact-active-class="mal"
                >Privacy Policy</router-link
              >
            </li>
          </ul> -->
        </div>
        <div class="nav_item">
          <router-link to="/">
            <img
              src="/assets/media/logos/drivisa-logo200_80.svg"
              class="mobile_logo"
            />
          </router-link>
        </div>
        <div class="nav_item">
          <div v-if="$store.getters.isAuthenticated" class="user_profile_menu">
            <v-menu style="z-index: 11" offset-y bottom>
              <template v-slot:activator="{ attrs, on }">
                <span v-bind="attrs" v-on="on">
                  <img :src="$store.state.user.user.avatar" alt="User Image" />
                </span>
              </template>
              <v-list>
                <router-link :to="profileLink">
                  <v-list-item> My Profile </v-list-item>
                </router-link>
                <v-list-item @click="$store.dispatch('logout')">
                  Logout
                </v-list-item>
              </v-list>
            </v-menu>
          </div>
          <router-link v-else class="login_btn" to="/Login">
            <i class="mdi mdi-login text-body"></i>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Header",
  data() {
    return {
      al: false,
      hasError: true,
    };
  },
  computed: {
    profileLink() {
      let profile_link = "/admin/dashboard";
      let user = this.$store.state.user;

      if (user && user.user.userType === 0) {
        profile_link = "/admin/dashboard";
      }
      return profile_link;
    },
  },
};
</script>

<style scoped lang="scss">
.header {
  background-color: #f3f7ff;
  padding-top: 6px;
  padding-bottom: 6px;

  .d-container-lg {
    .header_item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-left: 10px;
      padding-right: 10px;

      .web_nav {
        text-align: center;
        padding: 15px;
        margin-inline: 10px;
        text-decoration: none !important;
        color: #000;
        font: 500 20px/32px Roboto, Helvetica Neue, sans-serif;
      }

      .login_btn {
        cursor: pointer;
        outline: none;
        display: inline-block;
        white-space: nowrap;
        text-decoration: none;
        text-align: center;
        margin-right: 12px;
        min-width: 64px;
        line-height: 36px;
        padding: 0 16px;
        height: 36px;
        border-radius: 4px;
        overflow: visible;
        color: rgba(0, 0, 0, 0.87);
        background-color: #fff;
        font-size: 14px;
        font-weight: 500;
        align-items: center;

        .mat-button-wrapper {
          vertical-align: middle !important;
        }

        i {
          font-size: 24px;
          vertical-align: middle !important;
          padding-bottom: 4px;
          padding-left: 4px;
        }
      }

      .user_profile_box {
        box-sizing: border-box;
        position: relative;
        user-select: none;
        cursor: pointer;
        outline: none;
        border: none;
        display: inline-block;
        white-space: nowrap;
        text-decoration: none;
        vertical-align: baseline;
        text-align: center;
        margin: 0;
        min-width: 64px;
        line-height: 36px;
        padding: 4px 8px;
        border-radius: 4px;
        overflow: visible;
        padding-left: 15px !important;
        padding-right: 15px !important;

        img {
          margin-left: 3px;
          border-radius: 50%;
          width: 60px;
          height: 60px;
          object-fit: cover;
        }
      }

      .user_profile_box:hover {
        background-color: hsl(0, 0%, 90%);
      }
    }
  }

  @media (max-width: 960px) {
    .d-container-lg {
      display: none;
    }
  }

  .mobile_menu {
    display: none;
  }

  @media (max-width: 960px) {
    .mobile_menu {
      display: block;

      .nav-mobile {
        flex-direction: row;
        box-sizing: border-box;
        display: flex !important;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;

        .nav_item {
          flex-direction: column;
          box-sizing: border-box;
          display: flex;
          flex: 1 1 100%;
          max-width: 20%;
          text-align: center;
        }

        svg {
          height: 45px;
          width: 65px;
          padding: 9px;
          position: absolute;

          path {
            fill: black;
          }

          &.icon-close {
            display: none;
            padding: 15px;
          }
        }

        li {
          line-height: 55px;
          text-align: center;

          a {
            display: block;
            color: #aaa;
            width: 100%;
            height: 100%;
            text-decoration: none;
          }
        }

        .menu-button {
          position: absolute;
          top: -22px;
          left: -18px;
          width: 100%;
          height: 100%;
          margin: 0;
          cursor: pointer;
          display: block;

          &:after {
            opacity: 0;
            top: 45px;
            content: "";
            width: 100vw;
            display: block;
            position: fixed;
            height: 100vh;
            pointer-events: none;
            transition: opacity 0.2s cubic-bezier(0, 0, 0.3, 1);
            transition-delay: 0.1s;
          }
        }

        #menu-toggle {
          display: none;
          position: relative;

          &.active ~ .menu-button,
          &:checked ~ .menu-button {
            .icon-close {
              display: block;
              top: 0;
              left: 48%;
            }

            .icon-open {
              display: none;
            }

            &:after {
              opacity: 1;
              pointer-events: auto;
              transition: opacity 0.3s cubic-bezier(0, 0, 0.3, 1);
            }
          }

          &.active ~ .menu-sidebar,
          &:checked ~ .menu-sidebar {
            transform: translateX(0);
            transition: transform 0.3s cubic-bezier(0, 0, 0.3, 1);
          }
        }

        .menu-container {
          position: relative;

          .menu-sidebar {
            box-shadow: 5px 0 5px -5px #333;
            display: block;
            bottom: 0;
            background: #14151e;
            position: fixed;
            transform: translateX(-405px);
            transition: transform 0.3s cubic-bezier(0, 0, 0.3, 1);
            top: 61px;
            z-index: 1000;
            list-style-type: none;
            padding: 0;
            width: 300px;
            max-width: 400px;

            .arrow {
              position: absolute;
              line-height: 50px;
              font-size: 32px;
              color: #555;
              top: 0;
              z-index: 0;

              &.left {
                left: 25px;
              }

              &.right {
                right: 25px;
              }
            }

            li {
              height: 48px;
              line-height: 55px;
              font-size: 16px;
              text-align: left;
              position: relative;
              border-bottom: 1px solid rgba(0, 0, 0, 0.1);
              padding-left: 20px;

              .menu-sub {
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                width: 0;
                overflow: hidden;
                background: white;
                visibility: hidden;
                transition: all 0.3s cubic-bezier(0, 0, 0.3, 1);
                border-left: 1px solid #ccc;
                list-style-type: none;
                padding: 0;
                margin: 0;
                z-index: 2;
                max-width: 400px;

                li {
                  overflow: hidden;
                }

                .menu-sub-title {
                  padding-left: 50px;
                }
              }

              .submenu-label {
                width: 100%;
                height: 100%;
                display: block;
              }

              .submenu-toggle {
                display: none;

                &.active ~ .menu-sub,
                &:checked ~ .menu-sub {
                  width: 65vw;
                  visibility: visible;
                  z-index: 1;
                  transition: width 0.35s cubic-bezier(0, 0, 0.3, 1);
                }
              }
            }
          }
        }

        .login_btn {
          font-size: 25px;
          margin-right: 45px;
        }

        .mobile_logo {
          width: 125px;
          height: 50px;
        }

        @media (max-width: 600px) {
          .mobile_logo {
            width: 120px;
            height: 48px;
          }
        }
      }
    }
  }
}

@media (max-width: 960px) {
  .header {
    background-color: #fff !important;
    border-bottom: 1px solid #eee;
  }
}

.logo {
  img {
    display: flex;
    align-items: center;
    width: 150px;
  }
}

.user_profile_menu {
  img {
    margin-right: 12px !important;
    margin-left: 10px;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    object-fit: cover;
  }
}

@media (max-width: 600px) {
  .user_profile_menu {
    text-align: start;

    img {
      margin-right: 12px !important;
      margin-left: 10px;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      object-fit: cover;
    }
  }
}

.mal {
  border-bottom: 2px solid #b2d5f1 !important;
}

@media print {
  .header{
    display: none !important;
  }
}
</style>