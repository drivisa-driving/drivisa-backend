import guest from "../middleware/guest";
import auth from "../middleware/auth";
import trainee from "../middleware/trainee";

const studentRoutes = [
  {
    path: "/trainee/profile",
    component: () => import("../../Pages/Student/Profile/StudentProfile"),
    name: "trainee-profile-page",
    meta: {
      middleware: [auth, trainee],
    },
  },
  {
    path: "/trainee/training-history",
    component: () => import("../../Pages/Student/Training/TrainingHistory"),
    name: "trainee-training-history-page",
    meta: {
      middleware: [auth, trainee],
    },
  },
  {
    path: "/trainee/lesson/:lesson",
    component: () => import("../../Pages/Student/Training/TrainingDetail"),
    name: "trainee-lesson-page",
    meta: {
      middleware: [auth, trainee],
    },
  },
  {
    path: "/trainee/edit-profile",
    component: () => import("../../Pages/Student/Profile/EditProfile"),
    name: "trainee-edit-profile-page",
    meta: {
      middleware: [auth, trainee],
    },
  },
  {
    path: "/trainee/settings",
    component: () => import("../../Pages/Student/Settings/Settings"),
    name: "trainee-settings-page",
    meta: {
      middleware: [auth, trainee],
    },
  },
  {
    path: "/trainee/security",
    component: () => import("../../Pages/Student/Security/Security"),
    name: "trainee-security-page",
    meta: {
      middleware: [auth, trainee],
    },
  },
  {
    path: "/trainee/documents",
    component: () => import("../../Pages/Student/Documents/Documents"),
    name: "trainee-documents-page",
    meta: {
      middleware: [auth, trainee],
    },
  },
  {
    path: "/trainee/purchase-history",
    component: () => import("../../Pages/Student/Transactions/PurchaseHistory"),
    name: "trainee-purchase-history",
    meta: {
      middleware: [auth, trainee],
    },
  },
  {
    path: "/trainee/transaction-history",
    component: () =>
      import("../../Pages/Student/Transactions/TransactionHistory"),
    name: "trainee-transaction-history",
    meta: {
      middleware: [auth, trainee],
    },
  },
  {
    path: "/trainee/courses",
    component: () => import("../../Pages/Student/Courses/Course"),
    name: "trainee-courses",
    meta: {
      middleware: [auth, trainee],
    },
  },
  {
    path: "/trainee/car-rental-requests",
    component: () => import("../../Pages/Student/CarRentalRequests/Requests"),
    name: "trainee-car-rental-requests",
    meta: {
      middleware: [auth, trainee],
    },
  },
  {
    path: "/student/raise-complaint",
    component: () => import("../../Pages/Student/Complaint/RaiseComplaint"),
    name: "raise-complaint",
    meta: {
      middleware: [auth, trainee],
    },
  },
  {
    path: "/student/complaint",
    component: () => import("../../Pages/Student/Complaint/Complaint"),
    name: "complaint",
    meta: {
      middleware: [auth, trainee],
    },
  },
];

export default studentRoutes;
