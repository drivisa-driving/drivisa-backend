export default function instructor({to, from, next, router}) {
    let user = router.app.$store.state.user;
    if (user.user) {
        if (user.user.userType != 1) {
            router.app.$toasted.error("You are not a instructor");
            if (from.name) {
                // if user (!instructor) from any previous history
                return next(from)
            } else {
                // if user (!instructor) not comes from any previous history
                return router.push("/");
            }
        } else {
            // if user is instructor
            return next();
        }

    } else {
        // if user is not exist
        return router.push("/login");
    }
}
