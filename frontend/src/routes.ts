import {authGuard} from "@auth0/auth0-vue";

export default [
    {
        path: '/',
        name: 'home',
        component: () => import('./components/Index.vue')
    },
    {
        path: '/about',
        name: 'about',
        component: () => import('./components/About.vue')
    },
    {
        path: '/help',
        name: 'help',
        component: () => import('./components/Help.vue')
    },
    {
        path: '/donate',
        name: 'donate',
        component: () => import('./components/Donate.vue')
    },
    {
        path: '',
        beforeEnter: authGuard,
        children: [
            {
                path: '/profile',
                name: 'profile',
                component: () => import('./components/Profile.vue')
            },
            {
                path: '/label/create',
                name: 'label:create',
                component: () => import('./components/Labels/EditLabel.vue'),
            },
            {
                path: '/label/edit/:id',
                menu: 'label:edit',
                component: () => import('./components/Labels/EditLabel.vue'),
            }
        ]
    }
];
