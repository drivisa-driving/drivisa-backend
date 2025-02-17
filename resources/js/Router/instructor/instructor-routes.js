import guest from "../middleware/guest";
import auth from "../middleware/auth";
import instructor from "../middleware/instructor";
import trainee from "../middleware/trainee";

const instructorRoutes = [
  {
    path: "/instructor/dashboard",
    component: () => import("../../Pages/Instructor/Dashboard/Dashboard"),
    name: "instructor-dashboard",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/profile",
    component: () => import("../../Pages/Instructor/Profile/InstructorProfile"),
    name: "instructor-profile-page",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/schedules",
    component: () => import("../../Pages/Instructor/Calendar/Calendar"),
    name: "instructor-calendar-page",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/timeline",
    component: () => import("../../Pages/Instructor/Calendar/Timeline"),
    name: "instructor-timeline-page",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/earnings",
    component: () => import("../../Pages/Instructor/Earning/Earning"),
    name: "instructor-earning-page",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/training-history",
    component: () => import("../../Pages/Instructor/Training/TrainingHistory"),
    name: "instructor-training-history-page",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/trainee-profile/:trainee_id",
    component: () => import("../../Pages/Instructor/Training/TraineeProfile"),
    name: "instructor-trainee-profile-page",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/training-history/:id",
    component: () =>
      import("../../Pages/Instructor/Training/SingleTrainingHistory"),
    name: "instructor-single-training-history-page",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/training-locations",
    component: () =>
      import("../../Pages/Instructor/Training/TrainingLocations"),
    name: "instructor-training-locations-page",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/training-locations/add",
    component: () =>
      import("../../Pages/Instructor/Training/TrainingLocationAdd"),
    name: "instructor-location-add-page",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/training-locations/:id",
    component: () =>
      import("../../Pages/Instructor/Training/TrainingLocationEdit"),
    name: "instructor-location-edit-page",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/cars",
    component: () => import("../../Pages/Instructor/Cars/Cars"),
    name: "instructor-cars-page",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/edit-profile",
    component: () => import("../../Pages/Instructor/Profile/EditProfile"),
    name: "instructor-edit-profile-page",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/settings",
    component: () => import("../../Pages/Instructor/Settings/Settings"),
    name: "instructor-settings-page",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/security",
    component: () => import("../../Pages/Instructor/Security/Security"),
    name: "instructor-security-page",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/documents",
    component: () => import("../../Pages/Instructor/Documents/Documents"),
    name: "instructor-documents-page",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/bank-account",
    component: () => import("../../Pages/Instructor/BankAccount/BankAccount"),
    name: "instructor-bank-account-page",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/car-rental-requests",
    component: () =>
      import("../../Pages/Instructor/CarRentalRequests/Requests"),
    name: "instructor-car-rental-requests",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/car-rental-requests-history",
    component: () => import("../../Pages/Instructor/CarRentalRequests/History"),
    name: "instructor-car-rental-request-history",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/raise-complaint",
    component: () => import("../../Pages/Instructor/Complaint/RaiseComplaint"),
    name: "raise-complaint",
    meta: {
      middleware: [auth, instructor],
    },
  },
  {
    path: "/instructor/complaint",
    component: () => import("../../Pages/Instructor/Complaint/Complaint"),
    name: "complaint",
    meta: {
      middleware: [auth, instructor],
    },
  },
];

export default instructorRoutes;
