window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
    require('datatables.net-bs4');
    require('datatables.net-buttons-bs4');
} catch (e) {
}

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


window.Vue = require('vue').default;

import vuetify from './vuetify';
import VueCountdownTimer from 'vuejs-countdown-timer';
import Toasted from 'vue-toasted';

// options

// ==== vue toasted options

const toastOpt = {
    position: "top-right",
    duration: 2000
}


Vue.use(Toasted, toastOpt)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */


Vue.config.productionTip = false;

const admin = new Vue({
    el: '#admin',
    vuetify,
});
