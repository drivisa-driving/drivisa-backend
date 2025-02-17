let MenuList = [
  {
    heading: "Dashboard",
    lists: [
      {
        icon: "mdi-bank",
        title: "Dashboard",
        link: "/admin/dashboard",
        activated: true,
      },
      {
        icon: "mdi-chart-line",
        title: "Sales Report",
        link: "/admin/sales-report",
        activated: true,
      },
    ],
  },

  {
    heading: "Users",
    lists: [
      {
        icon: "mdi-shield-account",
        title: "Admins",
        link: "/admin/admin-crud",
        activated: true,
      },
      {
        icon: "mdi-account-details",
        title: "All Instructors",
        link: "/admin/instructors",
        activated: true,
      },
      {
        icon: "mdi-human-child",
        title: "All Trainee",
        link: "/admin/trainees",
        activated: true,
      },
    ],
  },

  {
    heading: "BDE",
    lists: [
      {
        icon: "mdi-book-open-page-variant",
        title: "BDE course",
        link: "/admin/bde-course",
        activated: true,
      },
    ],
  },

  {
    heading: "Complaint",
    lists: [
      {
        icon: "mdi-message-alert-outline",
        title: "Complaint",
        link: "/admin/complaint",
        activated: true,
      },
    ],
  },
  {
    heading: "Core Settings",
    lists: [
      {
        icon: "mdi-hammer-wrench",
        title: "Core Settings",
        link: "/admin/core-settings",
        activated: true,
      },
    ],
  },
  {
    heading: "Lessons",
    lists: [
      {
        icon: "mdi-plus-circle",
        title: "Create Lesson",
        link: "/admin/create-lesson",
        activated: true,
      },
    ],
  },
  {
    heading: "Credit",
    lists: [
      {
        icon: "mdi-credit-card ",
        title: "Credit",
        link: "/admin/credit",
        activated: true,
      },
    ],
  },

  {
    heading: "Profile",
    lists: [
      {
        icon: "mdi-account",
        title: "Edit Profile",
        link: "/admin/edit-profile",
        activated: true,
      },
      {
        icon: "mdi-account-circle",
        title: "My Profile",
        link: "/admin/profile",
        activated: true,
      },
    ],
  },
  {
    heading: "Packages",
    lists: [
      {
        icon: "mdi-package-variant",
        title: "Package Types",
        link: "/admin/package-types",
        activated: true,
      },
      {
        icon: "mdi-package",
        title: "Packages",
        link: "/admin/packages",
        activated: true,
      },
    ],
  },
  {
    heading: "Security",
    lists: [
      {
        icon: "mdi-security",
        title: "Security",
        link: "/admin/security",
        activated: true,
      },
    ],
  },
  {
    heading: "Notification",
    lists: [
      {
        icon: "mdi-bell",
        title: "Send Notification",
        link: "/admin/notification",
        activated: true,
      },
    ],
  },
  {
    heading: "Discounts",
    lists: [
      {
        icon: "mdi-percent",
        title: "Discounts",
        link: "/admin/discounts",
        activated: true,
      },
      {
        icon: "mdi-account-details-outline",
        title: "User Discounts",
        link: "/admin/user-discounts",
        activated: true,
      },
    ],
  },
  {
    heading: "Training",
    lists: [
      {
        icon: "mdi-calendar-clock",
        title: "Today Lessons",
        link: "/admin/training/today-lessons",
        activated: true,
      },
      {
        icon: "mdi-map-marker-radius-outline",
        title: "Training Location",
        link: "/admin/training-location",
        activated: true,
      },
    ],
  }
];

export default MenuList;
