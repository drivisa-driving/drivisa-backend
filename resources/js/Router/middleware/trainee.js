export default function trainee({to, from, next, router}) {
    let user = router.app.$store.state.user;
    if (user.user) {
        if (user.user.userType != 2) {
            router.app.$toasted.error("You are not a trainee");
            if (from.name) {
                // if user (!trainee) from any previous history
                return next(from)
            } else {
                // if user (!trainee) not comes from any previous history
                return router.push("/");
            }
        } else {
            // if user is trainee
            return next();
        }

    }

    return next();
}
