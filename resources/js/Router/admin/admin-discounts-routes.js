import auth from "../middleware/auth";

const adminDiscountsRoutes = [
    {
        path: "/admin/user-discounts",
        component: () => import("../../components/Admin/Discounts/DiscountUsers.vue"),
        name: "admin-user-discount-page",
        meta: {
            middleware: [auth],
        },
    },
    {
        path: "/admin/discounts",
        component: () => import("../../components/Admin/Discounts/List.vue"),
        name: "admin-discount-page",
        meta: {
            middleware: [auth],
        },
    },

];
export default adminDiscountsRoutes;