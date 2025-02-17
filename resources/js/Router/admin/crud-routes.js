import auth from "../middleware/auth";

const crudRoutes = [
  {
    path: "/admin/admin-crud",
    component: () => import("../../Pages/Admin/CRUD/Admin"),
    name: "admin-crud-page",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/core-settings",
    component: () => import("../../Pages/Admin/CRUD/Settings"),
    name: "admin-core_settings",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/package-types",
    component: () => import("../../Pages/Admin/CRUD/PackageTypes"),
    name: "admin-package-types",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/packages",
    component: () => import("../../Pages/Admin/CRUD/Packages"),
    name: "admin-packages",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/complaint",
    component: () => import("../../Pages/Admin/Complaint/Complaint"),
    name: "admin-complaint",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/training-location",
    component: () =>
      import("../../Pages/Admin/TrainingLocation/TrainingLocation"),
    name: "admin-training-location",
    meta: {
      middleware: [auth],
    },
  },
];

export default crudRoutes;
