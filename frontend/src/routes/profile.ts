import {RouteRecordRaw} from "vue-router";
import labelRoutes from './labels';

export default [
    {
        path: '/labels/create',
        name: 'label:create',
        component: () => import('~/components/Labels/EditLabel.vue'),
        meta: {
            title: 'Create New Label'
        }
    },
    {
        path: '/label/:label_id/edit',
        name: 'label:edit',
        component: () => import('~/components/Labels/EditLabel.vue'),
        meta: {
            title: 'Edit Label',
        }
    },
    {
        path: '/label/:label_id/manage',
        name: 'label:manage',
        component: () => import('~/components/Labels/ManageLabel.vue'),
        meta: {
            title: 'Manage Label',
        },
    },
    {
        path: '/label/:label_id',
        meta: {
            breadcrumb: (route) => ([])
        },
        children: labelRoutes
    },
    {
        path: '/artists/create',
        name: 'artist:create',
        component: () => import('~/components/Artists/EditArtist.vue'),
        meta: {
            title: 'Create New Artist'
        }
    },
    {
        path: '/artist/:artist_id/edit',
        name: 'artist:edit',
        component: () => import('~/components/Artists/EditArtist.vue'),
        meta: {
            title: 'Edit Artist'
        }
    }
] as RouteRecordRaw[];
