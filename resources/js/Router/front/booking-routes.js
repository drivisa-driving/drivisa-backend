import guest from "../middleware/guest";
import auth from "../middleware/auth";
import trainee from "../middleware/trainee";
import instructor from "../middleware/instructor";

const bookingRoutes = [
    {
        path: '/booking',
        component: () => import('../../Pages/Front/Booking/AvailableBooking.vue'),
        name: 'booking-page',
        meta: {
            middleware: []
        }
    },
    {
        path: '/lesson-booking',
        component: () => import('../../Pages/Front/Booking/LessonBookingPage.vue'),
        name: 'lesson-page',
        meta: {
            middleware: [auth, trainee]
        }
    },
     {
        path: '/instructor-booking',
        component: () => import('../../Pages/Front/RoadTest/InstructorBooking'),
        name: 'instructor-booking',
        meta: {
            middleware: []
        }
    },
]

export default bookingRoutes;
