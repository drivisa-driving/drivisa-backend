import auth from "../middleware/auth";

const History = [
  {
    path: "/admin/transaction",
    component: () => import("../../Pages/Admin/Transaction/Transaction"),
    name: "transaction",
    meta: {
      middleware: [auth],
    },
  },
  {
    path: "/admin/refund",
    component: () => import("../../Pages/Admin/Transaction/Refund"),
    name: "Refund",
    meta: {
      middleware: [auth],
    },
  },
];

export default History;
