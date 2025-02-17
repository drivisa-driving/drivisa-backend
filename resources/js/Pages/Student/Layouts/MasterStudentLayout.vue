<template>
  <div>
    <Header/>
    <div class="d-container-lg mt-3">
      <NonVerifiedBanner
          class="mt-5 mb-1"
          v-if="$store.state.user.user.verified === 0"
          link="/trainee/documents"
      />
      <div class="text-center">
        <v-dialog
            v-model="dialog"
            max-width="1200"
            persistent
        >
          <v-card>
            <v-stepper v-model="e1">
              <v-stepper-header>
                <v-stepper-step
                    :complete="e1 > 1"
                    step="1"
                >
                  Profile
                </v-stepper-step>

                <v-divider></v-divider>

                <v-stepper-step
                    :complete="e1 > 2"
                    step="2"
                >
                  Documents
                </v-stepper-step>
              </v-stepper-header>

              <v-stepper-items>
                <v-stepper-content step="1">
                  <v-card
                      class="overflow-y-auto mb-2"
                      height="600px"
                  >
                    <EditProfileComponent
                        :showDPDeleteBtn="false"
                        @studentContinue="() => this.studentContinueForm = true"/>
                  </v-card>

                  <v-btn
                      :disabled="studentContinueForm == false"
                      color="primary"
                      @click="e1 = 2"
                  >
                    Continue
                  </v-btn>
                </v-stepper-content>

                <v-stepper-content step="2">
                  <v-card
                      class="overflow-y-auto mb-2"
                      height="600px"
                  >
                    <DocumentComponent />
                  </v-card>

                  <v-btn
                      color="primary"
                      @click="finishKyc"
                  >
                    Finish
                  </v-btn>
                  <v-btn text @click="e1 = 1">Back</v-btn>
                </v-stepper-content>
              </v-stepper-items>
            </v-stepper>
          </v-card>
        </v-dialog>
      </div>
      <div class="d-container profile_menu_page">
        <div class="row">
          <div class="profile_left_bar">
            <v-card
                class="menu-list-wrapper mt-0 m-2"
            >
              <v-list
                  dense
                  v-for="(list, j) in items"
                  :key="j"
              >
                <v-subheader>{{ list.heading }}</v-subheader>

                <router-link
                    v-for="(item, i) in list.lists"
                    :key="i"
                    :to="item.link"
                    class="d-menu-item"
                    exact-active-class="d-menu-active"
                >
                  <v-list-item>
                    <div>
                      <i class="d-menu-item-icon mdi" :class="item.icon"></i>
                    </div>
                    {{ item.title }}
                  </v-list-item>
                </router-link>
                <hr v-if="j===0">
              </v-list>
            </v-card>
          </div>
          <div class="profile_right_bar pt-1">
            <div class="m-2  mt-lg-0">
              <div class="text-right menu_btn mr-2">
                <v-menu
                    offset-y
                    bottom
                    right>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn class="bg-white" icon v-bind="attrs" v-on="on">
                      <v-app-bar-nav-icon></v-app-bar-nav-icon>
                    </v-btn>
                  </template>
                  <v-list
                      dense
                      style="width: 215px"
                      class="px-2"
                      v-for="(list, j) in items"
                      :key="j"
                  >
                    <v-subheader class="list_heading">{{ list.heading }}</v-subheader>

                    <router-link
                        v-for="(item, i) in list.lists"
                        :key="i"
                        :to="item.link"
                        class="d-menu-item d-mini-link"
                        exact-active-class="d-menu-active"
                    >
                      <v-list-item>
                        <div>
                          <i class="d-menu-item-icon mdi d-mini-menu-icon" :class="item.icon"></i>
                        </div>
                        {{ item.title }}
                      </v-list-item>
                    </router-link>
                    <hr class="m-0" v-if="j===0">
                  </v-list>
                </v-menu>
              </div>
              <slot></slot>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Footer/>
  </div>
</template>

<script>
import Footer from "../../../components/Front/Footer";
import Header from "../../../components/Front/Header";
import MenuList from "../../../data/students/menuList";
import NonVerifiedBanner from "../../../components/NonVerifiedBanner";
import EditProfileComponent from "../../../components/Student/Profile/EditProfileComponent";
import DocumentComponent from "../../../components/Student/Document/DocumentComponent";

export default {
  name: "MasterStudentLayout",
  components: {DocumentComponent, EditProfileComponent, NonVerifiedBanner, Header, Footer},
  data() {
    return {
      kycVerified: {},
      studentContinueForm: false,
      studentDocumentContinueForm: false,
      selectedItem: 1,
      e1: 1,
      items: MenuList,
    }
  },
  computed: {
    dialog() {
      return this.$store.state.user.user.kycVerification === "Pending";
    }
  },
  methods: {
    async finishKyc() {
      try {
        const url = "/v1/drivisa/trainees/kyc";
        await axios.post(url);
        await this.$store.dispatch('updateKycVerification');
        this.$toasted.success("Kyc in progress");
      } catch (e) {
        this.$root.handleErrorToast(e, "Please Check Your Details")
      }
    }
  }
}
</script>

<style scoped lang="scss">

.d-menu-item {
  display: block;
  height: 56px;
  line-height: 50px;
  -webkit-tap-highlight-color: transparent;
  width: 100%;
  padding: 0;
  font-size: 16px;
}

.d-menu-item-icon {
  display: inline-block;
  font-size: 24px;
  width: 24px;
  height: 24px;
  line-height: 24px;
  margin-top: 12px;
  margin-right: 5px;
  box-sizing: content-box;
  border-radius: 50%;
  padding: 4px;
}

.d-menu-item-text {
  cursor: pointer;
  font-family: Montserrat, sans-serif !important;
}

.d-menu-active {
  background-color: #3266cc;
  color: #fff;
  border-radius: 5px;
}

.d-menu-active * {
  color: white !important;
}

.v-list-item {
  padding: 0 0 0 8px !important;
}

.menu_btn {
  display: none;
}

.d-mini-link {
  height: 44px;
  line-height: 28px;
}

.d-mini-menu-icon {
  margin-top: 2px !important;
}

.list_heading {
  height: 0 !important;
  margin-top: 10px;
  margin-bottom: 10px;
}
</style>