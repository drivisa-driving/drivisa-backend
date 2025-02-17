export default function guest({ next, router }) {
    const isAuth = router.app.$store.getters['isAuthenticated'];
    if (isAuth) {
        return next({
            name: 'admin-dashboard'
        })
    }
    return next()
}
