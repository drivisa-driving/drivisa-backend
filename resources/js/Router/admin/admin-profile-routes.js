import auth from "../middleware/auth";

const adminProfileRoutes = [
  {
    path: "/admin/profile",
    component: () => import("../../Pages/Admin/Profile/AdminProfile"),
    name: "admin-profile-page",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/edit-profile",
    component: () => import("../../Pages/Admin/Profile/EditProfile"),
    name: "admin-edit-profile-page",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/settings",
    component: () => import("../../Pages/Admin/Settings/Settings"),
    name: "admin-settings-page",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/security",
    component: () => import("../../Pages/Admin/Security/Security"),
    name: "admin-security-page",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/dashboard",
    component: () => import("../../Pages/Admin/Dashboard/Dashboard"),
    name: "admin-dashboard",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/sales-report",
    component: () => import("../../Pages/Admin/Dashboard/SalesReport"),
    name: "sales-report",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/report/:type",
    component: () => import("../../Pages/Admin/Dashboard/DetailReport"),
    name: "admin-sales-report",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/credit",
    component: () => import("../../Pages/Admin/Credit/Index"),
    name: "admin-credit",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/notification",
    component: () => import("../../Pages/Admin/Notification/Notification"),
    name: "admin-notification",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/training/today-lessons",
    component: () => import("../../Pages/Admin/Training/TodayLessons"),
    name: "admin-today-lessons",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/create-lesson",
    component: () => import("../../Pages/Admin/Lesson/CreateLesson"),
    name: "admin-create-lesson",
    meta: {
      middleware: [auth],
    },
  },
];

export default adminProfileRoutes;
