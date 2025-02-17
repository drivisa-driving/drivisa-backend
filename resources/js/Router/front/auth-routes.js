import guest from "../middleware/guest";

const authRoutes = [
  {
    path: "/login",
    component: () => import("../../Pages/Front/Login.vue"),
    name: "login-page",
    meta: {
      middleware: [guest],
    },
  },
];

export default authRoutes;
