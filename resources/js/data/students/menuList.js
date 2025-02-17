let MenuList = [
  {
    heading: "Profile",
    lists: [
      {
        icon: "mdi-account-circle",
        title: "Profile",
        link: "/trainee/profile",
        activated: true,
      },
    ],
  },
  {
    heading: "Courses",
    lists: [
      {
        icon: "mdi-book",
        title: "All Course",
        link: "/trainee/courses",
        activated: true,
      },
    ],
  },
  {
    heading: "History",
    lists: [
      {
        icon: "mdi-flag",
        title: "Training",
        link: "/trainee/training-history",
        activated: true,
      },
      {
        icon: "mdi-cart",
        title: "Purchase",
        link: "/trainee/purchase-history",
        activated: true,
      },
      {
        icon: "mdi-cash",
        title: "Transactions",
        link: "/trainee/transaction-history",
        activated: true,
      },
    ],
  },
  {
    heading: "Car Rental Request",
    lists: [
      {
        icon: "mdi-newspaper-plus",
        title: "Requests",
        link: "/trainee/car-rental-requests",
        activated: true,
      },
    ],
  },
  {
    heading: "Account",
    lists: [
      {
        icon: "mdi-tooltip-account",
        title: "Edit Profile",
        link: "/trainee/edit-profile",
        activated: true,
      },
      // {
      //     icon: "mdi-settings",
      //     title: "Settings",
      //     link: "/trainee/settings",
      //     activated: true
      // },
      {
        icon: "mdi-security",
        title: "Security",
        link: "/trainee/security",
        activated: true,
      },
      {
        icon: "mdi-file-document",
        title: "Documents",
        link: "/trainee/documents",
        activated: true,
      },
    ],
  },
  {
    heading: "Complaint",
    lists: [
      {
        icon: "mdi-message-alert-outline",
        title: "Raise Complaint",
        link: "/student/raise-complaint",
        activated: true,
      },
      {
        icon: "mdi-account-check-outline",
        title: "Complaints",
        link: "/student/complaint",
        activated: true,
      },
    ],
  },
];

export default MenuList;
