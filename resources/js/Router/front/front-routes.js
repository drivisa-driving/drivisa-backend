const frontRoutes = [
    {
        path: '/',
        component: () => import('../../Pages/Front/Home.vue'),
        name: 'front-page',
        meta: {
            middleware: []
        }
    },
]

export default frontRoutes;
