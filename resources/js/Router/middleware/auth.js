export default function auth({next, router}) {
    if (!router.app.$store.getters.isAuthenticated) {
        return next({
            name: 'login-page'
        })
    }

    return next()
}
