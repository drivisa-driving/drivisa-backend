import auth from "../middleware/auth";

const adminTraineesRoutes = [
  {
    path: "/admin/trainees",
    component: () => import("../../Pages/Admin/Trainees/TraineesView"),
    name: "admin-trainees-page",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/trainees-details/:id",
    component: () => import("../../Pages/Admin/Trainees/Details"),
    name: "admin-trainees-details",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/trainees/transactions/:id",
    component: () => import("../../Pages/Admin/Trainees/Transactions"),
    name: "admin-trainees-transactions",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/bde-course",
    component: () => import("../../Pages/Admin/Trainees/BdeCourse"),
    name: "admin-bde-course",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/bde-course/bde-log/:username",
    component: () => import("../../Pages/Admin/Trainees/BDELog"),
    name: "bde-log",
    meta: {
      middleware: [auth],
    },
  },
];

export default adminTraineesRoutes;
