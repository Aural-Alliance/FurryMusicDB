export default [
    {
        path: '/', component: () => import('./components/Index.vue')
    },
    {
        path: '/about', component: () => import('./components/About.vue')
    },
    {
        path: '/help', component: () => import('./components/Help.vue')
    },
    {
        path: '/donate', component: () => import('./components/Donate.vue')
    }
];
