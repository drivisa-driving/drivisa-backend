import VueRouter from "vue-router";
import routes from "./routes";
import middlewarePipeline from "./middlewarePipeline";

const router = new VueRouter({
  mode: "history",
  routes,
});

router.beforeEach((to, from, next) => {
  if (!to.meta && !to.meta.middleware) {
    return next();
  }

  if (to.meta.middleware.length === 0) {
    return next();
  }

  const middleware = to.meta.middleware;

  const context = {
    to,
    from,
    next,
    router,
  };

  return middleware[0]({
    ...context,
    next: middlewarePipeline(context, middleware, 1),
  });
});

export default router;
