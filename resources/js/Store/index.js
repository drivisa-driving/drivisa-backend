import router from '../Router/index'
import axios from "axios";

export default {

    state: {
        user: null
    },

    getters: {
        isAuthenticated: state => {
            return state.user !== null;
        }
    },

    actions: {
        setUser({commit}, user) {
            commit("SET_USER", user)
        },

        getUser({commit}) {
            axios.get('/v1/auth/me', [], {
                headers: {
                    "Authorization": "Bearer " + localStorage.getItem("access_token")
                }
            }).then((res) => {
                localStorage.setItem("access_token", res.data.data.token.token);
                axios.defaults.headers.common['Authorization'] = "Bearer " + res.data.data.token.token;
                commit("SET_USER", res.data.data)
            })
        },
        deleteUser({commit, state}) {
            axios.delete('/v1/user/delete-account/' + state.user.username, {
                headers: {
                    "Authorization": "Bearer " + localStorage.getItem("access_token")
                }
            }).then((res) => {
                localStorage.removeItem("access_token");
                axios.defaults.headers.common['Authorization'] = null;
                commit("DELETE_USER")
                router.push("/login")
            })
        },
        logout({commit}) {
            localStorage.removeItem('access_token')
            commit("SET_USER", null)
            router.push("/login")
        },
        updateKycVerification({commit}){
            commit("SET_VERIFICATION");
        }
    },

    mutations: {
        SET_USER(state, user) {
            state.user = user;
        },
        DELETE_USER(state) {
            state.user = null;
        },
        SET_VERIFICATION(state){
            state.user.kycVerification = "InProgress";
        }
    }
}
