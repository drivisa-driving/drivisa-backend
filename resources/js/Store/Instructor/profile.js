export default {
    namespaced: true,
    state: {
        profile: null
    },

    getters: {},

    actions: {
        setProfile({commit}, profile) {
            commit("SET_PROFILE", profile)
        },

        getProfile({commit}) {
            axios.get('/v1/drivisa/instructors/get-profile-info').then((res) => {
                commit("SET_PROFILE", res.data.data)
            })
        },
    },

    mutations: {
        SET_PROFILE(state, profile) {
            state.profile = profile;
        }
    }
}
