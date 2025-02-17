<template>
  <div>
    <Header />
    <div class="d-container-lg mt-5">
      <div class="d-container profile_menu_page">
        <div class="row">
          <div class="profile_left_bar pt-0" style="height: 85vh">
            <v-card
              class="menu-list-wrapper mt-0 m-2 mr-1"
              style="padding: 10px"
            >
              <v-list dense v-for="(list, j) in items" :key="j">
                <v-subheader>{{ list.heading }}</v-subheader>

                <router-link
                  v-for="(item, i) in list.lists"
                  :key="i"
                  :to="item.link"
                  v-ripple
                  class="d-menu-item"
                  exact-active-class="d-menu-active btn-outline"
                >
                  <v-list-item>
                    <div>
                      <i class="d-menu-item-icon mdi" :class="item.icon"></i>
                    </div>
                    {{ item.title }}
                  </v-list-item>
                </router-link>
                <hr v-if="j === 0" />
              </v-list>
            </v-card>
          </div>
          <div class="profile_right_bar">
            <div class="m-2 mt-lg-0">
              <div class="text-right menu_btn mr-2">
                <v-menu bottom right>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on">
                      <v-app-bar-nav-icon></v-app-bar-nav-icon>
                    </v-btn>
                  </template>
                  <v-list
                    dense
                    style="width: 225px"
                    class="px-2"
                    v-for="(list, j) in items"
                    :key="j"
                  >
                    <v-subheader class="list_heading">{{
                      list.heading
                    }}</v-subheader>

                    <router-link
                      v-for="(item, i) in list.lists"
                      :key="i"
                      :to="item.link"
                      class="d-menu-item d-mini-link"
                      exact-active-class="d-menu-active btn-outline"
                    >
                      <v-list-item>
                        <div>
                          <i
                            class="d-menu-item-icon mdi d-mini-menu-icon"
                            :class="item.icon"
                          ></i>
                        </div>
                        {{ item.title }}
                      </v-list-item>
                    </router-link>
                    <hr class="m-0" v-if="j === 0" />
                  </v-list>
                </v-menu>
              </div>
              <slot></slot>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Header from "../../../components/Front/Header";
import MenuList from "../../../data/admin/menuList";

export default {
  name: "MasterAdminLayout",
  components: { Header },
  data() {
    return {
      selectedItem: 1,
      items: MenuList,
    };
  },
};
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
  top: -30px !important;
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

.v-menu__content {
  position: absolute !important;
  top: 132px !important;
}

.profile_left_bar {
  /* width */
  ::-webkit-scrollbar {
    width: 10px;
  }

  /* Track */
  ::-webkit-scrollbar-track {
    display: none;
  }

  /* Handle */
  ::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 5px;
  }
}
</style>