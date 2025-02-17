import dateAgo from "./date_ago";
import time from "./time";
import status from "./StatusCodeFilter";
import date from "./date";
import dateTime from "./dateTime";
import setting from "./setting";
import uppercase from "./uppercase";

export default {
    install(Vue) {
        Vue.filter('dateAgo', dateAgo);
        Vue.filter('time', time);
        Vue.filter('status', status);
        Vue.filter('date', date);
        Vue.filter('dateTime', dateTime);
        Vue.filter('setting', setting);
        Vue.filter('uppercase', uppercase);
    }
}