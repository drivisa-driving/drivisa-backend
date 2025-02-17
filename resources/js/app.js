/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import axios from "axios";

require("./bootstrap");

window.Vue = require("vue").default;

import vuetify from "./vuetify";
import Vue from "vue";
import VueRouter from "vue-router";
import Vuex from "vuex";
import appMixin from "./Mixins/app";
import Toasted from "vue-toasted";
import NProgress from "vue-nprogress";
import * as VueGoogleMaps from "vue2-google-maps";
import ApiServicePlugin from "./Plugins/ApiServicePlugin";
import { ValidationProvider,ValidationObserver, extend } from "vee-validate";
import VueSignaturePad from "vue-signature-pad";

// ==== prime vue
import PrimeVue from "primevue/config";
import "primevue/resources/themes/saga-blue/theme.css";
import "primevue/resources/primevue.min.css";
import "primeicons/primeicons.css";

// ==== prime vue components
import Dialog from "primevue/dialog";
import DataTable from "primevue/datatable/DataTable";
import Column from "primevue/column/Column";
import ColumnGroup from "primevue/columngroup/ColumnGroup";
import InputText from "primevue/inputtext/InputText";
import Calendar from "primevue/calendar/Calendar";
import AutoComplete from "primevue/autocomplete";
import { FilterService, FilterMatchMode } from "primevue/api";
import MultiSelect from "primevue/multiselect";
import TabView from "primevue/tabview";
import TabPanel from "primevue/tabpanel";
import Dropdown from "primevue/dropdown";
import filters from "./Filters";
import router from "./Router/index";
import App from "./App.vue";
import example from "./components/ExampleComponent";
import Toolbar from "primevue/toolbar";
import ProgressSpinner from "primevue/progressspinner";

// options

// ==== vue toasted options
const toastOpt = {
  position: "bottom-center",
  duration: 2000,
  action: {
    text: "Dismiss",
    class: "text-dark",
    onClick: (e, toastObject) => {
      toastObject.goAway(0);
    },
  },
};

// ==== vue NProgress options
const NProgressOptions = {
  latencyThreshold: 200,
  router: true,
};

// ==== prime vue components
Vue.use(Vuex);
Vue.use(VueRouter);
Vue.use(Toasted, toastOpt);
Vue.use(NProgress, NProgressOptions);
Vue.use(VueGoogleMaps, {
  load: {
    key: process.env.GOOGLE_MAP_KEY,
    libraries: "places",
  },
});
Vue.use(ApiServicePlugin);
Vue.use(PrimeVue);
Vue.use(require("vue-moment"));
Vue.use(VueSignaturePad);

Vue.component("DataTable", DataTable);
Vue.component("Column", Column);
Vue.component("ColumnGroup", ColumnGroup);
Vue.component("InputText", InputText);
Vue.component("Calendar", Calendar);
Vue.component("AutoComplete", AutoComplete);
Vue.component("Dialog", Dialog);
Vue.component("MultiSelect", MultiSelect);
Vue.component("TabView", TabView);
Vue.component("TabPanel", TabPanel);
Vue.component("Dropdown", Dropdown);
Vue.component("Toolbar", Toolbar);
Vue.component(
  "example-component",
  require("./components/ExampleComponent.vue").default
);
Vue.component("ValidationObserver", ValidationObserver);
Vue.component("ValidationProvider", ValidationProvider);
Vue.component("ProgressSpinner", ProgressSpinner);

//====================//
//======= Store ======//
//===================//

import storeData from "./Store/index";

import * as url from "url";

//instructor store data

import instructorProfile from "./Store/Instructor/profile";

//student store data

import traineeProfile from "./Store/trainee/profile";

const store = new Vuex.Store({
  modules: {
    user: storeData,
    instructorProfile: instructorProfile,
    traineeProfile: traineeProfile,
  },
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

Vue.config.productionTip = false;

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// rules

import {
  alpha_dash,
  alpha_num,
  alpha_spaces,
  alpha,
  between,
  confirmed,
  digits,
  dimensions,
  email,
  ext,
  image,
  oneOf,
  integer,
  length,
  is_not,
  is,
  max,
  max_value,
  mimes,
  min,
  min_value,
  excluded,
  numeric,
  regex,
  required,
  required_if,
  size,
  double,
} from "vee-validate/dist/rules";

extend("required", {
  ...required,
  message: "{_field_} is required",
});
extend("email", {
  ...email,
  message: "You entered a wrong email",
});
extend("integer", integer);
extend("between", between);
extend("numeric", {
  ...numeric,
  message: "You entered a wrong {_field_}",
});
extend("min", min);
extend("max", max);
extend("alpha", {
  ...alpha,
  message: "You entered a wrong {_field_}",
});
extend("double", {
  ...double,
  message: "You entered a wrong {_field_}",
});
extend("confirmed", {
  ...confirmed,
  message: "You entered a wrong {_field_}",
});

// global filters

Vue.use(filters);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const uri = window.location.href.split("/");

const nprogress = new NProgress();

// Add a request interceptor
axios.interceptors.request.use(
  function (config) {
    nprogress.start();
    return config;
  },
  function (error) {
    nprogress.done();
    return Promise.reject(error);
  }
);

// Add a response interceptor
axios.interceptors.response.use(
  function (response) {
    nprogress.done();
    return response;
  },
  function (error) {
    nprogress.done();
    return Promise.reject(error);
  }
);

// stripe axios instance

window.stripeAPI = axios.create({
  baseURL: "https://api.stripe.com",
});

const app = new Vue({
  el: "#app",
  data: {
    loading: false,
  },
  store,
  router,
  vuetify,
  nprogress,
  components: { App, example },
  mounted() {
    this.getLoggedInUser();
  },
  methods: {
    getSettingEnv(name) {
      let settings = JSON.parse(process.env.SETTINGS);
      return settings[name] ? settings[name] : null;
    },
    async getLoggedInUser() {
      var token = localStorage.getItem("access_token");
      if (!token) return;

      try {
        this.loading = true;
        const { data } = await axios.get("/v1/auth/me", {
          headers: {
            Authorization: "Bearer " + token,
          },
        });
        localStorage.setItem("access_token", data.user.token.token);
        axios.defaults.headers.common["Authorization"] =
          "Bearer " + data.user.token.token;
        await this.$store.dispatch("setUser", data.user);
      } catch (e) {
        localStorage.removeItem("access_token");
        this.loading = false;
        await this.$router.push("/login").catch(() => {});
      } finally {
        if (this.$store.getters.isAuthenticated) {
          this.loading = false;
          if (uri.length === 5) {
            await this.$router
              .push("/" + uri[3] + "/" + uri[4])
              .catch(() => {});
          } else if (uri.length === 6) {
            await this.$router
              .push("/" + uri[3] + "/" + uri[4] + "/" + uri[5])
              .catch(() => {});
          } else if (uri.length === 7) {
            await this.$router
              .push("/" + uri[3] + "/" + uri[4] + "/" + uri[5] + "/" + uri[6])
              .catch(() => {});
          }
        }
      }
    },
  },
  mixins: [appMixin],
});

// custom functions

window.getCurrentMonthFirstAndLastDay = function (
  actionDate = null,
  action = null
) {
  let date;
  if (actionDate) {
    date = new Date(Date.parse(actionDate));
  } else {
    date = new Date();
  }

  let y = date.getFullYear();
  let m = date.getMonth();

  if (action === "increment") {
    date.setMonth(m + 1);
  } else if (action == "decrement") {
    date.setMonth(m - 1);
  }

  y = date.getFullYear();
  m = date.getMonth();

  var firstDay = new Date(y, m, 1);
  var lastDay = new Date(y, m + 1, 0);

  return {
    start: formatDate(firstDay),
    end: formatDate(lastDay),
  };
};

window.formatDate = function (date) {
  let year = date.getFullYear();
  let month = date.getMonth() + 1;
  let today = date.getDate();

  if (month < 10) {
    month = "0" + month;
  }

  if (today < 10) {
    today = "0" + today;
  }

  return year + "-" + month + "-" + today;
};

window.isBookingAvailable = function (date, time) {
  date = date + " " + time;
  var userdt = Date.parse(date);
  var currdt = new Date();
  return userdt > currdt;
};

window.hasWorkHours = function (
  schedules,
  date,
  by_status = null,
  by_type = "workingHour"
) {
  let schedule = schedules.find((sch) => sch.date === formatDate(date));
  if (schedule !== undefined) {
    if (by_status === null) {
      return schedule.workingHours.length;
    } else {
      if (by_type === "workingHour") {
        return schedule.workingHours.filter((wh) => wh.status === by_status)
          .length;
      } else {
        if (schedule.lessons.length > 0) {
          return schedule.lessons.filter((wh) => wh.status === by_status)
            .length;
        } else {
          return 0;
        }
      }
    }
  } else {
    return 0;
  }
};

window.formatDateFromString = function (date) {
  var d = new Date(date),
    month = "" + (d.getMonth() + 1),
    day = "" + d.getDate(),
    year = d.getFullYear();

  if (month.length < 2) month = "0" + month;
  if (day.length < 2) day = "0" + day;

  return [year, month, day].join("-");
};
