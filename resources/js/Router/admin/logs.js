import auth from "../middleware/auth";

const Logs = [
    {
        path: "/admin/logs",
        component: () => import("../../Pages/Admin/Logs/logs.vue"),
        name: "logs",
        meta: {
            middleware: [auth],
        },
    },

];

export default Logs;
