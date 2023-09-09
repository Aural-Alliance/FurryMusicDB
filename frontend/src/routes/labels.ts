import {RouteRecordRaw} from "vue-router";

export default [
    {
        path: '/artists/create',
        name: 'label:artist:create',
        component: () => import('~/components/Artists/EditArtist.vue'),
        meta: {
            title: 'Create New Artist in Label',
        },
    },
    {
        path: '/artist/:artist_id/edit',
        name: 'label:artist:edit',
        component: () => import('~/components/Artists/EditArtist.vue'),
        meta: {
            title: 'Edit Artist in Label',
        },
    }
] as RouteRecordRaw[];
