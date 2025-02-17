import guest from "../middleware/guest";
import auth from "../middleware/auth";

const frontRoutes = [
    {
        path: '/faq',
        component: () => import('../../Pages/Front/extra/Faq.vue'),
        name: 'faq-page',
        meta: {
            middleware: []
        }
    },
    // {
    //     path: '/instructors',
    //     component: () => import('../../Pages/Front/extra/Instructors'),
    //     name: 'instructors',
    //     meta: {
    //         middleware: []
    //     }
    // },
    // {
    //     path: '/package/lessons',
    //     component: () => import('../../Pages/Front/extra/Lessons'),
    //     name: 'lessons',
    //     meta: {
    //         middleware: []
    //     }
    // },
    // {
    //     path: '/road-test/rental',
    //     component: () => import('../../Pages/Front/extra/Rentals'),
    //     name: 'rentals',
    //     meta: {
    //         middleware: []
    //     }
    // },
    // {
    //     path: '/bde',
    //     component: () => import('../../Pages/Front/extra/Bde'),
    //     name: 'bde',
    //     meta: {
    //         middleware: []
    //     }
    // },
    // {
    //     path: '/instructors/:username',
    //     component: () => import('../../Pages/Front/extra/SingleInstructor'),
    //     name: 'single-instructor',
    //     meta: {
    //         middleware: []
    //     }
    // },
    {
        path: '/privacy-policy',
        component: () => import('../../Pages/Front/extra/PrivacyPolicy.vue'),
        name: 'privacy-policy-page',
        meta: {
            middleware: []
        }
    },
    {
        path: '/about-us',
        component: () => import('../../Pages/Front/extra/AboutUs.vue'),
        name: 'about-us-page',
        meta: {
            middleware: []
        }
    },

    {
        path: '/contact-us',
        component: () => import('../../Pages/Front/extra/ContactUs.vue'),
        name: 'contact-us-page',
        meta: {
            middleware: []
        }
    },
    // {
    //     path: '/instructor-booking',
    //     component: () => import('../../Pages/Front/RoadTest/InstructorBooking.vue'),
    //     name: 'instructor',
    //     meta: {
    //         middleware: []
    //     }
    // },
    // {
    //     path: '/payment',
    //     component: () => import('../../Pages/Front/Payments/PackagePayment'),
    //     name: 'payment',
    //     meta: {
    //         middleware: []
    //     }
    // },

    // {
    //     path: '/pricing',
    //     component: () => import('../../Pages/Front/extra/Pricing.vue'),
    //     name: 'pricing-page',
    //     meta: {
    //         middleware: []
    //     }
    // },
    //
    // {
    //     path: '/refund',
    //     component: () => import('../../Pages/Front/extra/Refund'),
    //     name: 'refund-page',
    //     meta: {
    //         middleware: []
    //     }
    // },

    {
        path: '/terms-conditions',
        component: () => import('../../Pages/Front/extra/TermCondition.vue'),
        name: 'terms-page',
        meta: {
            middleware: []
        }
    },
    // {
    //     path: '/inst-search-map',
    //     component: () => import('../../Pages/Front/extra/MapPage.vue'),
    //     name: 'inst-search-map',
    //     meta: {
    //         middleware: []
    //     }
    // },

]

export default frontRoutes;
