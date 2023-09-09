import {authGuard} from "@auth0/auth0-vue";
import {RouteRecordRaw} from "vue-router";
import profileRoutes from './routes/profile';

export default [
    {
        path: '/',
        name: 'home',
        component: () => import('~/components/Index.vue'),
        meta: {
            title: 'Home'
        }
    },
    {
        path: '/about',
        name: 'about',
        component: () => import('~/components/About.vue'),
        meta: {
            title: 'About FurryMusicDB',
        }
    },
    {
        path: '/help',
        name: 'help',
        component: () => import('~/components/Help.vue'),
        meta: {
            title: 'Help'
        }
    },
    {
        path: '/donate',
        name: 'donate',
        component: () => import('~/components/Donate.vue'),
        meta: {
            title: 'Donate'
        }
    },
    {
        path: '',
        beforeEnter: authGuard,
        children: [
            {
                path: '/profile',
                name: 'profile',
                component: () => import('~/components/Profile.vue'),
                meta: {
                    title: 'My Profile',
                }
            },
            {
                path: '',
                meta: {
                    breadcrumb: [
                        {
                            label: 'My Profile',
                            url: {
                                name: 'profile'
                            }
                        }
                    ]
                },
                children: profileRoutes,
            }
        ]
    }
] as RouteRecordRaw[];
