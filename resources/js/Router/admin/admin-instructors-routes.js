import auth from "../middleware/auth";

const adminInstructorsRoutes = [
  {
    path: "/admin/instructors",
    component: () => import("../../Pages/Admin/Instructors/InstructorsView"),
    name: "admin-instructors-page",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/instructors/details/:id",
    component: () => import("../../Pages/Admin/Instructors/Details"),
    name: "admin-instructor-details",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/instructors/transactions/:id",
    component: () => import("../../Pages/Admin/Instructors/Transactions"),
    name: "admin-instructor-transactions",
    meta: {
      middleware: [auth],
    },
  },
];

export default adminInstructorsRoutes;
