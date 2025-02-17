import front from "./front/front-routes";
import extra from "./front/extra-routes";
import auth from "./front/auth-routes";
import adminProfileRoutes from "./admin/admin-profile-routes";
import adminTraineesRoutes from "./admin/admin-trainees-routes";
import adminInstructorsRoutes from "./admin/admin-instructors-routes";
import crudRoutes from "./admin/crud-routes";
import History from "./admin/history";
import Logs from "./admin/logs";
import adminDiscountsRoutes from "./admin/admin-discounts-routes";

const routes = [
    ...extra,
    ...front,
    ...auth,
    ...adminProfileRoutes,
    ...adminInstructorsRoutes,
    ...adminTraineesRoutes,
    ...crudRoutes,
    ...History,
    ...Logs,
    ...adminDiscountsRoutes
];

export default routes;
