import {$api} from "../Services/api";

export default {
    install(Vue, options) {
        Vue.prototype.$api = $api;
    }
}